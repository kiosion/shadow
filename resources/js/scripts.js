// On DOM loaded
$(document).ready(() => {
	// Init tooltips
	$("html").tooltip({ selector: '[data-bs-toggle=tooltip]' });
	// Init context menu
	$("html").contextmenu((e) => {
		e.preventDefault();
	});
	$("html").mouseleave(function(){
		$(".contextMenu_obj").css({
			"display": "none"
		});
		$(".contextMenu_link").css({
			"display": "none"
		});
		$("div.tooltip").remove();
	});
	$(".file-card-file").contextmenu((e) => {
		e.preventDefault();
		$(".contextMenu_obj").css({
			position: "absolute", 
			top: e.pageY, 
			left: e.pageX, 
			display: "block",
		});
		$(".contextMenu_link").css({
			display: "none",
		});
	});
	$("a").contextmenu((e) => {
		e.preventDefault();
		let clicked = $(e.delegateTarget);
		let link = clicked.attr('href');
		// If link doesn't contain http or https, add it
		if (link.indexOf('http') == -1) {
			window.location.protocol + '//' + window.location.host + link;
		}
		$(".contextMenu_obj").css({
			display: "none",
		});
		$(".contextMenu_link").css({
			position: "absolute", 
			top: e.pageY, 
			left: e.pageX, 
			display: "block",
		});
		$(".contextMenu_link").find("a.open-link").attr("data-link", link);
		$(".contextMenu_link").find("a.copy-link").attr("data-link", link);
	});
	$("html").click(() => {
		$(".contextMenu_obj,.contextMenu_link").css({
			display: "none" 
		});
	});
	$("button.data-link").contextmenu((e) => {
		e.preventDefault();
		let clicked = $(e.delegateTarget);
		let link = clicked.attr('data-link');
		// If link doesn't contain http or https, add it
		if (link.indexOf('http') == -1) {
			window.location.protocol + '//' + window.location.host + link;
		}
		$(".contextMenu_obj").css({
			display: "none",
		});
		$(".contextMenu_link").css({
			position: "absolute", 
			top: e.pageY, 
			left: e.pageX, 
			display: "block",
		});
		$(".contextMenu_link").find("a.open-link").attr("data-link", link);
		$(".contextMenu_link").find("a.copy-link").attr("data-link", link);
	});
	$("html").click(() => {
		$(".contextMenu_obj,.contextMenu_link").css({
			display: "none" 
		});
	});

	// Context menu actions
	$(".dropdown-item.copy-link").click((e) => {
		copyLink(e);
		$(".contextMenu_obj,.contextMenu_link").css({
			display: "none"
		});
	});
	$(".dropdown-item.open-link").click((e) => {
		openLink(e, true);
		$(".contextMenu_obj,.contextMenu_link").css({
			display: "none"
		});
	});
	$(".dropdown-item.view-raw").click((e) => {
		viewRaw(e);
		$(".contextMenu_obj,.contextMenu_link").css({
			display: "none"
		});
	});
	$(".dropdown-item.download").click((e) => {
		download(e);
		$(".contextMenu_obj,.contextMenu_link").css({
			display: "none"
		});
	});

	// Handle login form update on submit
	$("#loginForm").submit((e) => {
		// Prevent default submit
		e.preventDefault();
		console.log("Login form submitted...");
		let un = $('#username');
		let pw = $('#password');
		// Send data using post
		console.log("Checking credentials...");
		let post = $.ajax({
			type: 'POST',
			url: '/api/v2/auth/check-creds/',
			data: { 
				username: un.val(),
				password: pw.val(),
			}
		});
		// Check response when done
		post.done((authRes) => {
			//authRes = JSON.parse(m);
			if (authRes.status == 'success' && authRes.msg == 'Credentials valid') {
				// Request token if creds are valid
				console.log("Credentials valid, requesting token...");
				let token = $.ajax({
					type: 'POST',
					url: '/api/v2/auth/request-token/',
					data: {
						username: un.val(),
						password: pw.val(),
						type: 'login',
					}
				});
				// Check response when done
				token.done((tokenRes) => {
					//tokenRes = JSON.parse(tokenRes);
					// If token was generated and returned
					if (tokenRes.status == 'success' && tokenRes.msg == 'Token generated') {	
						console.log("Token generated, setting cookie...");
						// Set login_token cookie with expiry of 6 hours
						Cookies.set('shadow_login_token', tokenRes.data, { expires: .25 });
						console.log("Cookie set, redirecting...");
						window.location.href = '/home/';
					}
					// If not generated or returned, log error and redirect to index.php
					else {
						console.warn("Failed to get token, server responded with error: " + tokenRes.msg);
						window.location.href = '/login/?error=token';
					}
				});
				// On fail, log error and redirect to index.php with error
				token.fail(() => {
					console.error("Error requesting token, server failed to respond");
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
				console.error("Failed to auth, server responded with error: " + authRes.msg);
				window.location.href = '/login/?error=server';
			}
			else {
				console.error("Failed to auth, server responded with unknown response: " + authRes.msg + " (status: " + authRes.status + ")");
				window.location.href = '/login/?error=server';
			}
		});
		// On fail
		post.fail(() => {
			console.error('Failed to auth, server failed to respond');
			window.location.href = '/login/?error=server';
		});
	});

	// Logout button
	$('#logoutButton').click((e) => {
		e.preventDefault();
		// Remove shadow_login_token cookie
		Cookies.remove('shadow_login_token');
		// Redirect to index
		window.location.href = '/login/';
	});

	// Header button actions
	$("#header-loginButton").click((e) => {
		e.preventDefault();
		window.location.href = '/login';
	});
	$('#header-logoutButton').click((e) => {
		e.preventDefault();
		Cookies.remove('shadow_login_token');
		window.location.href = '/login';
	});
	$('#header-settingsButton,#header-accountButton,#header-uploadButton').click((e) => {
		openLink(e, false);
	});
	$('#header-backButton').click(() => {
		window.history.back();
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
	$(".fileButtonOpen,.fileButtonDownload").click((e) => {
		openLink(e, true);
	});
	$(".fileButtonCopy").click((e) => {
		copyLink(e);
	});
	$(".fileButtonVis").click((e) => {
		let button = $(e.delegateTarget);
		let i = button.find('i').eq(0);
		let fileID = button.attr('data-id');
		let vis = -1;
		// Get current visibility based on icon
		if (i.hasClass('fa-eye')) { 
			vis = 1; 
		}
		else if (i.hasClass('fa-eye-slash')) {
			vis = 2;
		}
		else {
			vis = 0;
		}
		// Request to toggle visibility
		let post = $.ajax({
			url: '/api/v2/file/set-visibility/',
			type: 'POST',
			headers: {
				"Authorization": "Bearer " + Cookies.get('shadow_login_token'),
			},
			data: {
				fileID: fileID,
				vis: vis,
			}
		});
		// On success
		post.done((visRes) => {
			console.warn(visRes.msg + " (" + visRes.data + ")");
			// If 'status' is 'success', then toggle visibility
			if (visRes.status == 'success') {
				// Toggle visibility
				if (visRes.data == 1) {
					i.removeClass('fa-eye');
					i.addClass('fa-eye-slash');
					button.attr('title', 'Change visibility to private');
					button.attr('data-bs-original-title', 'Change visibility to private');
				}
				else if (visRes.data == 2) {
					i.removeClass('fa-eye-slash');
					i.addClass('fa-low-vision');
					button.attr('title', 'Change visibility to public');
					button.attr('data-bs-original-title', 'Change visibility to public');
				}
				else if (visRes.data == 0) {
					i.removeClass('fa-low-vision');
					i.addClass('fa-eye');
					button.attr('title', 'Make file hidden');
					button.attr('data-bs-original-title', 'Change visibility to hidden');
				}
			}
			// If 'status' is 'error', then display error message
			else if (visRes.status == 'error') {
				// Display error message from 'msg' field
				console.warn("Failed to toggle visibility, server responded with error: " + visRes.msg);
			}
			else {
				console.error("Failed to set file visibility, server responded with unknown response: " + authRes.msg + " (status: " + authRes.status + ")");
			}
		});
	});
	$(".fileButtonDelete").click((e) => {
		let button = $(e.delegateTarget);
		let fileID = button.attr('data-id');
	});
	$(".sortName,.sortSize,.sortDate,.buttonPrevPage,.buttonNextPage").click((e) => {
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
	window.location.href = (linkText);
}
