// Imports

// On DOM loaded
$(document).ready(() => {
	// Handle form submission for login form
	$("#1").submit((e) => {
		e.preventDefault();
		console.log("Submitted!");
	});
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
		// On success, check response
		post.done((authRes) => {
			if (authRes.status == 'success' && authRes.msg == 'Credentials valid') {
				// Request token
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
				// On success, check response
				token.done((tokenRes) => {
					//console.log(token_response);
					if (tokenRes.status == 'success' && tokenRes.msg == 'Token generated') {	
						console.log("Token generated, setting cookie...");
						// Set cookie with expiry of 6 hours
						Cookies.set('login_token', tokenRes.data, { expires: 6 });
						console.log("Cookie set, redirecting...");
						//window.location.href = 'index.php';
					}
					// Else, log error and redirect to index.php with error
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
});
