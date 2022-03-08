// On DOM loaded
$(document).ready(() => {
	// Handle login form update on submit
	$("#loginForm").submit((e) => {
		// Prevent default submit
		e.preventDefault();
		console.log("Login form submitted...");
		let un = $('#username');
		let pw = $('#password');
		// Send data using post
		console.log("Checking creds...");
		let post = $.ajax({
			type: 'POST',
			url: '/api/v1/auth.php',
			data: { 
				action: 'check_creds',
				username: un.val(),
				password: pw.val(),
			}
		});
		// Check response when done
		post.done((authRes) => {
			if (authRes.status == 'success' && authRes.msg == 'Credentials valid') {
				// Request token if creds are valid
				console.log("Credentials valid, requesting token...");
				let token = $.ajax({
					type: 'POST',
					url: '/api/v1/auth.php',
					data: {
						action: 'request_token',
						username: un.val(),
						password: pw.val(),
						type: 'login',
					}
				});
				// Check response when done
				token.done((tokenRes) => {
					// If token was generated and returned
					if (tokenRes.status == 'success' && tokenRes.msg == 'Token generated') {	
						console.log("Token generated, setting cookie...");
						// Set login_token cookie with expiry of 6 hours
						Cookies.set('shadow_login_token', tokenRes.data, { expires: .25 });
						console.log("Cookie set, redirecting...");
						window.location.href = '/home';
					}
					// If not generated or returned, log error and redirect to index.php
					else {
						console.log(tokenRes.msg);
						window.location.href = '/login/?error=token';
					}
				});
				// On fail, log error and redirect to index.php with error
				token.fail(() => {
					console.log("Error requesting token");
					window.location.href = '/login/?error=token';
				});
			}
			else if (authRes.status == 'success' && authRes.msg == 'Credentials invalid') {
				// Log error and redirect to index.php with error
				window.location.href = '/login/?error=creds';
			}
			// If 'status' is 'error', then display error message
			else if (authRes.status == 'error') {
				// Display error message from 'msg' field
				console.log("Failed to auth, server responded with error: " + authRes.msg);
				window.location.href = '/login/?error=server';
			}
			else {
				console.log("Failed to auth, server responded with unknown error");
				window.location.href = '/login/?error=server';
			}
		});
		// On fail
		post.fail(() => {
			console.log('Failed to auth, server responded with unknown error');
			window.location.href = '/login/?error=server';
		});
	});
	// Logout button
	$('#logoutButton').click((e) => {
		e.preventDefault();
		// Remove shadow_login_token cookie
		Cookies.remove('shadow_login_token');
		// Redirect to index
		window.location.href = '/login';
	});
	// Dashboard button
	$('#launchDashButton').click((e) => {
		e.preventDefault();
		// Redirect to dashboard
		window.location.href = '/admin';
	});
	// Init tooltips
	$("html").tooltip({ selector: '[data-bs-toggle=tooltip]' });

	// Init right click menu
	$("html").contextmenu((e) => {
		e.preventDefault();
	});
	$(".file-card-file").contextmenu((e) => {
		e.preventDefault();
		$("#contextMenu").css({
			position: "absolute", 
			top: e.pageY, 
			left: e.pageX, 
			display: "block",
		});
	});
	$("html").click(() => {
		$("#contextMenu").css({
			display: "none" 
		});
	});
	// Context menu actions
	$(".dropdown-item.copy-link").click((e) => {
		copyLink(e);
		$("#contextMenu").css({
			display: "none"
		});
	});
	$(".dropdown-item.view-raw").click((e) => {
		viewRaw(e);
		$("#contextMenu").css({
			display: "none"
		});
	});
	$(".dropdown-item.download").click((e) => {
		download(e);
		$("#contextMenu").css({
			display: "none"
		});
	});
	// Header button actions
	$('#header-logoutButton').click((e) => {
		e.preventDefault();
		// Remove shadow_login_token cookie
		Cookies.remove('shadow_login_token');
		// Redirect to index
		window.location.href = '/login';
	});
	$('#header-settingsButton').click((e) => {
		openLink(e, false);
	});
	$('#header-accountButton').click((e) => {
		openLink(e, false);
	});
	$('#header-uploadButton').click((e) => {
		openLink(e, false);
	});
	// Menu bar button actions
	$("#menuBar-copyLink").click((e) => {
		copyLink(e);
	});
	$("#menuBar-viewRaw").click((e) => {
		viewRaw(e);
	});
	$("#menuBar-download").click((e) => {
		download(e);
	});
	// Upload table button actions
	$(".fileButtonOpen").click((e) => {
		openLink(e, true);
	});
	$(".fileButtonCopy").click((e) => {
		copyLink(e);
	});
	$(".fileButtonDownload").click((e) => {
		openLink(e, true);
	});
	$("#sortName").click((e) => {
		openLink(e, false);
	});
	$("#sortSize").click((e) => {
		openLink(e, false);
	});
	$("#sortDate").click((e) => {
		openLink(e, false);
	});
	// Pagination button actions
	$(".buttonPrevPage").click((e) => {
		openLink(e, false);
	});
	$(".buttonNextPage").click((e) => {
		openLink(e, false);
	});
});

function openLink(e, newPage) {
	e.preventDefault();
	let link = $(e.delegateTarget).attr('data-link');
	if (newPage) {
		window.open(link, '_blank');
	}
	else {
		window.location.href = link;
	}
}

function copyLink(e) {
	e.preventDefault();
	let linkText = $(e.delegateTarget).attr('data-link');
	let $temp = $("<input>");
	$("body").append($temp);
	$temp.val(linkText).select();
    document.execCommand("copy");
    $temp.remove();
}

function viewRaw(e) {
	e.preventDefault();
	let linkText = $(e.delegateTarget).attr('data-link');
	window.open(linkText, '_blank');
}

function download(e) {
	e.preventDefault();
	let linkText = $(e.delegateTarget).attr('data-link');
	window.location.href = ("File/"+linkText);
}
