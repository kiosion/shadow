// Imports

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
			url: 'api/auth.php',
			data: { 
				action: 'check_credentials',
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
					url: 'api/auth.php',
					data: {
						action: 'generate_token',
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
						window.location.href = 'index.php';
					}
					// If not generated or returned, log error and redirect to index.php
					else {
						console.log(tokenRes.msg);
						//window.location.href = 'index.php?error=2';
					}
				});
				// On fail, log error and redirect to index.php with error
				token.fail(() => {
					//window.location.href = 'index.php?error=3';
					console.log("Error generating token");
				});
			}
			else if (authRes.status == 'success' && authRes.msg == 'Credentials invalid') {
				// Log error and redirect to index.php with error
				console.log("Invalid credentials");
				//window.location.href = 'index.php?error=1';
			}
			// If 'status' is 'error', then display error message
			else if (authRes.status == 'error') {
				// Display error message from 'msg' field
				console.log("Failed to auth, server responded with error: " + post.responseJSON.msg);
			}
			else {
				console.log("Failed to auth, server responded with unknown error");
			}
			// Redirect to index
			//window.location.href = 'index.php';
		});
		// On fail
		post.fail((xhr) => {
			if (xhr.status != 200 && xhr.status != null) {
				console.log('Server responded: ' + xhr.status);
			}
			console.log('Failed to auth!');
			// Redirect to login with error
			//window.location.href = 'index.php?error=1';
		});
	});
	// Handle button clicks
	// Logout button
	$('#logoutButton').click((e) => {
		e.preventDefault();
		// Remove shadow_login_token cookie
		Cookies.remove('shadow_login_token');
		// Redirect to index
		window.location.href = 'index.php';
	});
});
