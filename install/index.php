<?php

// Prevent direct file access
if (!isset($include) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
	require_once 'includes/utils/res.php';
	header('Content-Type: application/json');
	echo Res::fail(403, 'Forbidden');
	exit();
}

// Include functions
require_once 'install/functions.php';
require_once 'includes/utils/res.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// Parse URL for current req
	$url = handle_url_paths(parse_url($_SERVER['REQUEST_URI']));
	// Check if req is valid
	if (empty($url)) {
		echo Res::fail(400, 'Bad Request');
		exit();
	}
	switch ($url[0]) {
		case 'create-file':
			switch ($url[1]) {
				case 'env':
					// Create .env file, populate with db credentials and generate random phrase for JWT
					if (
						!isset($_POST['db_host']) || 
						!isset($_POST['db_user']) || 
						!isset($_POST['db_pass']) || 
						!isset($_POST['db_name']) || 
						!isset($_POST['db_port'])) {
						echo Res::fail(400, 'Bad Request');
						exit();
					}
					$res = create_env(
						$_POST['db_host'], 
						$_POST['db_user'], 
						$_POST['db_pass'], 
						$_POST['db_name'], 
						$_POST['db_port']
					);
					// If res contains 'Success:'
					if (strpos($res, 'Success:') !== false) {
						// Return success
						echo Res::success(200, '.ENV created and populated', $res);
						exit();
					}
					else {
						echo Res::fail(500, $res);
						exit();
					}
					break;
				case 'sys-config':
					break;
				case 'user-config':
					break;
			}
			break;
		default:
			echo Res::fail(400, 'Bad Request');
			break;
	}
	exit();
}

// Parse URL for current step
$step = handle_url_paths(parse_url($_SERVER['REQUEST_URI']), 'step');
// Get current URL minus anything after install/
$url = explode('/', parse_url($_SERVER['REQUEST_URI'])['path']);
$url = array_slice($url, 0, 2);
$url = implode('/', $url);
$hostname = $_SERVER['HTTP_HOST'];
// Get http protocol
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
//echo $protocol.$hostname.$url.'/'.$step;

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Shadow - Install</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
	<link rel="stylesheet" href="/install/resources/styles.css">
</head>
<body class="col col-md-6 me-auto p-5 bg-dark text-light">
	<?php
		function print_header(int $prog) : void {
			GLOBAL $step;
			$val = ($prog / 6) * 100;
			if ($step > 0) echo '<p class="mb-2">Step '.$step.'</p>';
			else echo '<p class="mb-2">Start</p>';
			echo '
			<h1>Shadow - Install</h1>
			<p class="lead">Welcome, follow the instructions below to setup your Shadow installation.</p>
			';
			if ($prog > 0) echo '
			<div class="mb-3">
				<div class="progress">
					<div class="progress-bar" role="progressbar" style="width: '.$val.'%" aria-valuenow="'.$val.'" aria-valuemin="0" aria-valuemax="100"></div>
				</div>
			</div>
			';
			echo '<hr>';
		}
		switch ($step) {
			// Homepage
			default:
			case 0:
				print_header(0);
				echo '<p>Select an option below to continue.</p>';
				echo '<div class="col d-flex nowrap">';
				echo '<a href="/install/1" class="btn btn-primary btn-block me-3">Install</a>';
				echo '<a href="/install/2" class="btn btn-secondary btn-block disabled me-3">Upgrade</a>';
				echo '</div>';
				break;
			case 1:
				print_header(1);
				echo '
				<h2>Dependancies check</h2>
				<p>If the following checks look good, click continue. Otherwise, resolve any issues.</p>';
				$ready = true;
				$missing_ext = '';
				$nowrite_dirs = array();
				// Check if PHP version is >= 7.0
				if (version_compare(PHP_VERSION, '7.0.0', '<')) {
					echo '<div class="alert alert-danger" role="alert">PHP version is <strong>'.PHP_VERSION.'</strong>. You need PHP 7.0 or higher to run Shadow.</div>';
					$ready = false;
				}
				else {
					echo '<div class="alert alert-success" role="alert">Required PHP version is 8.0 or higher, you are running <strong>'.PHP_VERSION.'</strong>.</div>';
				}
				// Check if PHP extensions are loaded
				$required_ext = array (
					'curl',
					'gd',
					'json',
					'mbstring',
					'mysqli',
					'openssl',
					'pdo',
					'pdo_mysql',
					'session',
					'simplexml',
					'xml',
					'zip'
				);
				foreach ($required_ext as $ext) {
					if (!extension_loaded($ext)) $missing_ext .= $ext.' ';
				}

				if ($missing_ext != '') {
					echo '<div class="alert alert-danger" role="alert">PHP extension(s) <strong>'.$missing_ext.'</strong> are not loaded. You need these extensions to run Shadow.</div>';
					$ready = false;
				}
				else {
					echo '<div class="alert alert-success" role="alert">All required PHP extensions are loaded.</div>';
				}

				// Check if required directories are writable
				$required_dirs = array (
					'./api',
					'./api/includes',
					'./api/utils',
					'./config',
					'./config/user',
					'./uploads',
					'./uploads/users',
					'./includes',
					'./includes/components',
					'./includes/pages',
					'./includes/utils',
					'./resources',
					'./resources/css',
					'./resources/js',
					'./resources/imgs',
				);
				$nowritedirs = array();
				foreach ($required_dirs as $dir) {
					if (!is_writable($dir)) { $nowritedirs[] = $dir; }
				}
				if (count($nowritedirs) > 0) {
					foreach ($nowritedirs as $dir) {
						echo '<div class="alert alert-danger" role="alert">Directory <strong>'.$dir.'</strong> is not writable. You need to make this directory writable to continue.</div>';
					}
					$ready = false;
				}
				else {
					echo '<div class="alert alert-success" role="alert">All required directories are writable.</div>';
				}

				// Create required files
				$required_files = array (
					'./api/utils/demo.env',
					'./config/user/1.php',
				);
				$created_files = array();
				foreach ($required_files as $file) {
					if (!file_exists($file)) {
						try {
							file_put_contents($file, '');
							$created_files[] = $file;
						} catch (Exception $e) {
							echo '<div class="alert alert-danger" role="alert">File <strong>'.$file.'</strong> could not be created. You need to create this file manually with the proper permissions.</div>';
						}
					}
				}
				if (count($created_files) > 0) {
					foreach ($created_files as $file) {
						echo '<div class="alert alert-success" role="alert">Required file <strong>'.$file.'</strong> was created.</div>';
					}
				}
				else {
					echo '<div class="alert alert-success" role="alert">All required files are present.</div>';
				}


				if ($ready) {
					echo '<a href="/install" class="btn btn-outline-primary btn-block me-3">Back</a>';
					echo '<a href="/install/2" class="btn btn-primary btn-block me-3">Continue</a>';
				}
				else {
					echo '<a href="/install" class="btn btn-outline-primary btn-block me-3">Back</a>';
					echo '<a class="btn btn-secondary btn-block disabled me-3">Continue</a>';
				}
				break;
			// Step 2: Databse credentials
			case 2:
				// Check if form has been submitted TODO: move this section to a function to be used in jQuery instead. check at the step of creating tables, in order to let the user choose a db name and prefix then.
				if (isset($_POST['_submit'])) {
					$valid = validate_db_creds($_POST['db-host'], $_POST['db-user'], $_POST['db-pass'], $_POST['db-name'], $_POST['db-port']);
					// If credentials are valid, redirect to next step
					if (strpos($valid, 'Error') === false) {
						// Show button for next step
						print_header(2);
						echo '
						<h2>Step 2: Database credentials</h2>
						<p>Success! If the following info looks correct, click continue. Otherwise, click edit.</p>
						<form class="mb-2">
							<div class="form-floating mb-3">
								<input type="text" name="db-host" id="db-host" class="form-control" placeholder="Hostname" value="'.htmlspecialchars($_POST['db-host']).'" disabled>
								<label for="db-host">Hostname</label>
							</div>
							<div class="form-floating mb-3">
								<input type="text" name="db-user" id="db-user" class="form-control" placeholder="User" value="'.htmlspecialchars($_POST['db-user']).'" disabled>
								<label for="db-user">User</label>
							</div>
							<div class="form-floating mb-3">
								<input type="password" name="db-pass" id="db-pass" class="form-control" placeholder="Password" value="'.htmlspecialchars($_POST['db-pass']).'" disabled>
								<label for="db-pass">Password</label>
							</div>
							<div class="form-floating mb-3">
								<input type="text" name="db-name" id="db-name" class="form-control" placeholder="Database" value="'.htmlspecialchars($_POST['db-name']).'" disabled>
								<label for="db-name">Database</label>
							</div>
							<div class="form-floating mb-3">
								<input type="text" name="db-port" id="db-port" class="form-control" placeholder="Port" value="'.htmlspecialchars($_POST['db-port']).'" disabled>
								<label for="db-port">Port</label>
							</div>
						</form>
						<a href="/install/2" class="btn btn-outline-primary btn-block me-3">Edit</a>
						<hr>
						';
						echo '<a href="/install/1" class="btn btn-outline-primary btn-block me-3">Back</a>';
						echo '<button type="button" class="btn btn-primary btn-block me-3">Continue</button>';
						exit();
					}
					// Else, display error message
					else {
						print_header(2);
						echo '<div class="alert alert-danger" role="alert">Error connecting to database: <strong>'.$valid.'</strong>.</div>';
						echo '<a href="/install/1" class="btn btn-outline-primary btn-block me-3">Back</a>';
						echo '<a href="/install/2" class="btn btn-primary btn-block me-3">Retry</a>';
					}
				}
				else {
					print_header(2);
					echo '
					<h2>Database credentials</h2>
					<p>Enter your database credentials below.</p>
					<form class="mb-2" id="setup-db-creds-form">
						<div class="form-floating mb-3">
							<input type="text" name="db-host" id="db-host" class="form-control" placeholder="Hostname" required autocomplete>
							<label for="db-host">Hostname</label>
						</div>
						<div class="form-floating mb-3">
							<input type="text" name="db-user" id="db-user" class="form-control" placeholder="Username" required autocomplete>
							<label for="db-user">Username</label>
						</div>
						<div class="form-floating mb-3">
							<input type="password" name="db-pass" id="db-pass" class="form-control" placeholder="Password" required autocomplete>
							<label for="db-pass">Password</label>
						</div>
						<div class="form-floating mb-3">
							<input type="text" name="db-port" id="db-port" class="form-control" placeholder="Port" required autocomplete>
							<label for="db-port">Port</label>
						</div>
						<!--div class="form-group">
							<button type="submit" class="btn btn-primary">Submit</button>
						</div-->
					</form>
					<hr>
					';
					// Use jQuery to allow clicking 'continue' when all fields filled in
					// On click on 'continue', use AJAX to call API function to create initial .ENV file
					// This won't have the table name, that will be created and added in step 3
					echo '<a href="/install/1" class="btn btn-primary btn-block me-3">Back</a>';
					echo '<a class="btn btn-secondary btn-block disabled me-3">Continue</a>';
				}
				break;
			// Step 3: Database table creation
			case 3:
				print_header(3);
				echo '
				<h2>Database setup</h2>
				<p>Follow the steps below to create required database tables.</p>
				<div class="mb-4">
				<h4>Database</h4>
				<p>Name must not contain underscores, spaces, or special characters.</p>
				<div class="form-floating mb-3">
					<input type="text" name="db-name" id="db-name" class="form-control" placeholder="Database name" required autocomplete>
					<label for="db-name">Database name</label>
				</div>
				<button type="button" class="btn btn-secondary disabled">Create</button>
				</div>
				<div class="mb-4">
				<h4>Tables</h4>
				<p>(sh_files, sh_users)</p>
				<p>Prefix must not contain underscores, spaces, special characters, or be longer than 12 characters.</p>
				<div class="form-floating mb-3">
					<input type="text" name="db-name" id="db-name" class="form-control" placeholder="Table prefix" required autocomplete>
					<label for="db-name">Table prefix</label>
				</div>
				<button type="button" class="btn btn-secondary disabled">Create</button>
				</div>
				<hr>
				';
				// TODO: Implement error if database creation fails, specifically if due to credentials. Provide 'back' or edit opiton.
				echo '<a href="/install/2" class="btn btn-outline-primary btn-block me-3">Back</a>';
				echo '<a class="btn btn-secondary btn-block me-3 disabled">Continue</a>';
				// For each button, call function in this file to try creating table

				break;
			// Step 4: Admin user creation
			case 4:
				echo $header;
				echo '
				<h2>Config and .ENV file creation</h2>
				
				';
				// For each button, call function in API to create file
				break;
			// Step 5: Various settings options
			// Step 6: Finalize setup, button to turn off installer and redireect to login
		}
	?>
</body>
<script src="/install/resources/scripts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</html>
