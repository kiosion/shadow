<?php

// Prevent direct file access
if (!isset($include)) {
	require_once '../includes/utils/res.php';
	header('Content-Type: application/json');
	echo Res::fail(403, 'Forbidden');
	exit();
}

// Include functions
require_once('install/functions.php');

// Parse URL for current step
$step = handle_url_paths(parse_url($_SERVER['REQUEST_URI']));

// Get current URL minus anything after install/
$url = explode('/', parse_url($_SERVER['REQUEST_URI'])['path']);
$url = array_slice($url, 0, 2);
$url = implode('/', $url);
$hostname = $_SERVER['HTTP_HOST'];
// Get http protocol
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
echo $protocol.$hostname.$url.'/'.$step;

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Shadow - Install</title>
	<link rel="stylesheet" href="/install/resources/styles.css">
</head>
<body>
	<?php
		$header = "<h1>Shadow - Install</h1><p>Welcome to the Shadow installation process. Please follow the instructions below to setup your Shadow installation.</p><hr>";
		switch ($step) {
			// Homepage
			default:
			case 0:
				echo $header;
				echo '
				<div class="next-step">
					<a href="'.$protocol.$hostname.$url.'/1'.'" class="btn btn-primary">Step 1</a>
				</div>
				';
				break;
			// Step 1: Databse credentials
			case 1:
				// Check if form has been submitted
				if (isset($_POST['_submit'])) {
					// Call function to validate credentials and test connection
					$valid = validate_db_creds($_POST['db-host'], $_POST['db-user'], $_POST['db-pass'], $_POST['db-name'], $_POST['db-port']);
					// If credentials are valid, redirect to next step
					// If response doesn't contain 'Error', redirect to next step
					if (strpos($valid, 'Error') === false) {
						// Show button for next step
						echo $header;
						echo '
						<h2>Step 1: Database credentials</h2>
						<p>Success! If the following info looks correct, click continue. Otherwise, click edit.</p>
						<form>
							<div class="form-group">
								<label for="db-host">Host:</label>
								<input type="text" name="db-host" id="db-host" class="form-control" value="'.htmlspecialchars($_POST['db-host']).'" disabled>
							</div>
							<div class="form-group">
								<label for="db-user">Username:</label>
								<input type="text" name="db-user" id="db-user" class="form-control" value="'.htmlspecialchars($_POST['db-user']).'" disabled>
							</div>
							<div class="form-group">
								<label for="db-pass">Password:</label>
								<input type="password" name="db-pass" id="db-pass" class="form-control" value="'.htmlspecialchars($_POST['db-pass']).'" disabled>
							</div>
							<div class="form-group">
								<label for="db-name">Database:</label>
								<input type="text" name="db-name" id="db-name" class="form-control" value="'.htmlspecialchars($_POST['db-name']).'" disabled>
							</div>
							<div class="form-group">
								<label for="db-port">Port:</label>
								<input type="text" name="db-port" id="db-port" class="form-control" value="'.htmlspecialchars($_POST['db-port']).'" disabled>
							</div>
						</form>
						<div class="prev-step">
							<a href="'.$protocol.$hostname.$url.'/1'.'" class="btn btn-primary">Edit</a>
						</div>
						<div class="next-step">
							<a href="'.$protocol.$hostname.$url.'/2'.'" class="btn btn-primary">Continue</a>
						</div>
						';
						// On click on 'continue', use AJAX to call API function to create initial .ENV file
						exit();
					}
					// Else, display error message
					else {
						echo $header;
						echo '
						<div class="error-message">
							<p>'.$valid.'</p>
						</div>
						<div class="next-step">
							<a href="'.$protocol.$hostname.$url.'/1'.'" class="btn btn-primary">Retry</a>
						</div>
						';
					}
				}
				else {
					echo $header;
					echo '
					<h2>Step 1: Database credentials</h2>
					<p>Please enter your database credentials below.</p>
					<form action="" method="post" id="setup-DB-form">
						<div class="form-group">
							<label for="db-host">Host:</label>
							<input type="text" name="db-host" id="db-host" class="form-control" required autocomplete>
						</div>
						<div class="form-group">
							<label for="db-user">Username:</label>
							<input type="text" name="db-user" id="db-user" class="form-control" required autocomplete>
						</div>
						<div class="form-group">
							<label for="db-pass">Password:</label>
							<input type="password" name="db-pass" id="db-pass" class="form-control" required autocomplete>
						</div>
						<div class="form-group">
							<label for="db-name">Database:</label>
							<input type="text" name="db-name" id="db-name" class="form-control" required autocomplete>
						</div>
						<div class="form-group">
							<label for="db-port">Port:</label>
							<input type="text" name="db-port" id="db-port" class="form-control" required autocomplete>
						</div>
						<input name="_submit" type="hidden" value="_submit" />
						<div class="form-group">
							<input type="submit" value="Submit" id="db-creds-submit" class="btn btn-primary">
						</div>
					</form>
					';
				}
				break;
			// Step 2: Database table creation
			case 2:
				echo $header;
				echo '
				<h2>Step 2: Database table creation</h2>
				<p>Follow the steps below to create required database tables.</p>
				<h4>Login table</h4>
				<button type="button" class="btn btn-primary">Create</button>
				<br>
				<h4>User table</h4>
				<button type="button" class="btn btn-primary">Create</button>
				<br>
				<h4>Upload table</h4>
				<button type="button" class="btn btn-primary">Create</button>
				<br>
				<br>
				<div class="next-step">
					<a href="'.$protocol.$hostname.$url.'/3'.'" class="btn btn-primary">Next Step</a>
				</div>
				';
				// For each button, call function in this file to try creating table

				break;
			// Step 3: Admin user creation
			case 3:
				echo $header;
				echo '
				<h2>Step 3: Config and .ENV file creation</h2>
				
				';
				// For each button, call function in API to create file
				break;
			// Step 4: Various settings options
			// Step 5: Finalize setup, button to turn off installer and redireect to login
		}
	?>
</body>
<script src="/install/resources/scripts.js"></script>
</html>
