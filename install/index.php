<?php
// Prevent direct file access
if (!isset($include) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
	require_once 'app/utils/res.php';
	header('Content-Type: application/json');
	echo Res::fail(403, 'Forbidden');
	exit();
}

// Include functions
require_once 'install/functions.php';
require_once 'app/utils/res.php';

// Vars
$env_path = realpath(dirname(__FILE__))."/../api/utils/demo.env"; // To be changed to '/api/utils/.env' in release
$req_exts_path = "./install/resources/reqs/exts.txt";
$req_dirs_path = "./install/resources/reqs/dirs.txt";
$req_files_path = "./install/resources/reqs/files.txt";
$d_htf = './install/resources/files/deny.htaccess';
$a_htf = './install/resources/files/allow.htaccess';
$htf = './install/resources/files/root.htaccess';
$app_config = './app/config/system.php';
$temp_file = './install/temp.txt';

// Check if temp file exists
if (file_exists($temp_file) && filesize($temp_file) > 8) {
	if (!isset($_COOKIE['sh-setup-token'])) {
		echo Res::fail(403, 'Forbidden, invalid token. If you believe this is an error, clear the temp file and restart the setup proccess.');
		exit();
	}
	if (get_temp($temp_file, 'sh-setup-token') !== $_COOKIE['sh-setup-token']) {
		echo Res::fail(403, 'Forbidden, invalid token. If you believe this is an error, clear the temp file and restart the setup proccess.');
		exit();
	}
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// Parse URL for current req
	$url = handle_url_paths(parse_url($_SERVER['REQUEST_URI']));
	if (empty($url)) {
		echo Res::fail(400, 'Bad Request');
		exit();
	}
	switch ($url[0]) {
		case 'create':
			switch ($url[1]) {
				case 'env':
					$arr = array(
						"DB_HOST" => $_POST['db_host'], 
						"DB_USER" => $_POST['db_user'], 
						"DB_PASS" => $_POST['db_pass'], 
						"DB_PORT" => $_POST['db_port'],
					);
					$res = create_env(
						$env_path,
						$arr
					);
					if (stripos($res, "success") !== false) {
						echo Res::success(200, '.ENV created and populated', $res);
						exit();
					}
					else {
						echo Res::fail(500, $res);
						exit();
					}
					break;
				case 'db':
					$res = create_db($env_path, $_POST['db_name']);
					if (stripos($res, "success") !== false) {
						echo Res::success(200, 'Database created', $res);
						exit();
					}
					else {
						echo Res::fail(500, $res);
						exit();
					}
					break;
				case 'tables':
					$res = create_tables($env_path, $_POST['db_prefix']);
					if (stripos($res, "success") !== false) {
						echo Res::success(200, 'Tables created', $res);
						exit();
					}
					else {
						echo Res::fail(500, $res);
						exit();
					}
					break;
				case 'admin':
					$res = create_admin($env_path, $_POST['acc_un'], $_POST['acc_pass']);
					if (stripos($res, "success") !== false) {
						echo Res::success(200, 'Account created', $res);
						exit();
					}
					else {
						echo Res::fail(500, $res);
						exit();
					}
				case 'app-config':
					$arr = array();
					if (isset($_POST['app_name'])) $arr['APP_NAME'] = $_POST['app_name'];
					if (isset($_POST['app_desc'])) $arr['APP_DESC'] = $_POST['app_desc'];
					if (isset($_POST['app_url'])) {
						$arr['APP_URL'] = $_POST['app_url'];
						$arr['APP_API_URL'] = $_POST['app_url'];
					}
					$res = update_app_config($app_config, $arr);
					if (stripos($res, "success") !== false) {
						echo Res::success(200, 'App config created', $res);
						exit();
					}
					else {
						echo Res::fail(500, $res);
						exit();
					}
					break;
			}
			break;
		case 'check':
			switch ($url[1]) {
				case 'creds':
					if (
						!isset($_POST['db_host']) || empty($_POST['db_host']) ||
						!isset($_POST['db_user']) || empty($_POST['db_user']) ||
						!isset($_POST['db_pass']) || empty($_POST['db_pass']) ||
						!isset($_POST['db_port']) || empty($_POST['db_port'])) {
						echo Res::fail(400, 'Bad Request');
						exit();
					}
					$res = validate_db_creds($_POST['db_host'], $_POST['db_user'], $_POST['db_pass'], $_POST['db_port']);
					if (stripos($res, "success") !== false) {
						echo Res::success(200, 'Success', $res);
						exit();
					}
					else {
						echo Res::fail(500, $res);
						exit();
					}
					break;
				case 'env':
					$res = env_exists($env_path);
					if ($res[0]) {
						if (stripos($res[1][0], 'error') !== false) {
							echo Res::fail(500, $res[1]);
							exit();
						}
						echo Res::success(200, 'ENV exists', $res[1]);
						exit();
					}
					else {
						echo Res::success(200, 'ENV does not exist', null);
						exit();
					}
					break;
				case 'db':
					$res = db_exists($env_path);
					if (stripos($res, "success") !== false) {
						if (stripos($res, "does not exist") !== false) {
							echo Res::success(200, $res, false);
							exit();
						}
						echo Res::success(200, $res, true);
						exit();
					}
					else {
						echo Res::fail(500, $res);
						exit();
					}
					break;
				case 'tables':
					$res = tables_exists($env_path);
					if (stripos($res, "success") !== false) {
						if (stripos($res, "do not exist") !== false) {
							echo Res::success(200, $res, false);
							exit();
						}
						echo Res::success(200, $res, true);
						exit();
					}
					else {
						echo Res::fail(500, $res);
						exit();
					}
					break;
				case 'admin':
					$res = admin_exists($env_path);
					if (stripos($res, "success") !== false) {
						if (stripos($res, "does not exist") !== false) {
							echo Res::success(200, $res, false);
							exit();
						}
						echo Res::success(200, $res, true);
						exit();
					}
					else {
						echo Res::fail(500, $res);
						exit();
					}
					break;
			}
			break;
		case 'finish-setup':
			$res = finish_setup($env_path, $temp_file, $app_config);
			if (stripos($res, "success") !== false) {
				echo Res::success(200, 'Setup script disabled', $res);
				exit();
			}
			else {
				echo Res::fail(500, $res);
				exit();
			}
	}
	exit();
}

// Parse URL for current step
$step = handle_url_paths(parse_url($_SERVER['REQUEST_URI']), 'step');
$url = implode('/', array_slice(explode('/', parse_url($_SERVER['REQUEST_URI'])['path']), 0, 2));
$hostname = $_SERVER['HTTP_HOST'];
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
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
<body class="col-fluid me-auto p-5 ms-auto me-auto bg-dark text-light">
	<?php
		function print_header(int $prog) : void {
			GLOBAL $step;
			$val = ($prog / 7) * 100;
			if ($step > 0) echo '<p class="mb-2">Step '.$step.'</p>';
			else echo '<p class="mb-2">Start</p>';
			echo '<h1>Shadow - Install</h1>
			<p class="lead">Welcome, follow the instructions below to get setup.</p>';
			if ($prog > 0) echo '
			<div class="mb-3">
				<div class="progress">
					<div class="progress-bar" role="progressbar" style="width:'.$val.'%" aria-valuenow="'.$val.'" aria-valuemin="0" aria-valuemax="100"></div>
				</div>
			</div>
			';
			echo '<hr>';
		}
		switch ($step) {
			case 0:
				print_header($step);
				echo '
				<p>Select an option to continue.</p>
				<div class="col d-flex nowrap">
					<a href="/setup/1" class="btn btn-primary btn-block me-3">Install</a>
					<a href="/setup/" class="btn btn-secondary btn-block disabled me-3">Upgrade</a>
				</div>
				';
				break;
			// 1: Dependencies
			case 1:
				print_header($step);
				echo '<div id="dep-checks-setup">
					<h2>Dependancies</h2>
					<p>If the following checks look good, click continue. Otherwise, resolve any issues.</p>';
				$ready = true;
				if (version_compare(PHP_VERSION, '7.0.0', '<')) {
					echo '<div class="alert alert-danger" role="alert">PHP version is <strong>'.PHP_VERSION.'</strong>. You need PHP 7.0 or higher to run Shadow.</div>';
					$ready = false;
				}
				else {
					echo '<div class="alert alert-success" role="alert">Required PHP version is 8.0 or higher, you are running <strong>'.PHP_VERSION.'</strong>.</div>';
				}
				$required_ext = read_arr($req_exts_path);
				$missing_ext = '';
				foreach ($required_ext as $ext) if (!extension_loaded($ext)) $missing_ext .= $ext.', ';
				if ($missing_ext != '') {
					echo '<div class="alert alert-danger" role="alert">PHP extension(s) <strong>'.$missing_ext.'</strong> are not loaded. You need these extensions to run Shadow.</div>';
					$ready = false;
				}
				else {
					$required_ext_str = implode(', ', $required_ext);
					echo '<div class="alert alert-success" role="alert">All required PHP extensions are loaded ('.$required_ext_str.').</div>';
				}
				echo '</div>
				<hr>
				<a href="/setup/'.($step-1).'" class="btn btn-outline-primary btn-block me-3">Back</a>';
				if ($ready) echo '<a href="/setup/'.($step+1).'" class="btn btn-primary btn-block me-3">Continue</a>';
				else echo '<a href="/setup/'.$step.'" class="btn btn-primary btn-block me-3">Retry</a>';
				break;
			// 2: Required dirs/files
			case 2:
				print_header($step);
				echo '<div class="file-checks-setup">
					<h2>Required files</h2>
					<p>If the following checks look good, click continue. Otherwise, resolve any issues.</p>';
				$ready = true;
				$required_dirs = read_arr($req_dirs_path);
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
				$required_files = read_arr($req_files_path);
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
					echo '<div class="alert alert-success" role="alert">All required app files are present.</div>';
				}
				$res = write_file('./app/.htaccess', $d_htf);
				$ready = $res[0];
				echo $res[1];
				$res = write_file('./app/resources/.htaccess', $a_htf);
				$ready = $res[0];
				echo $res[1];
				$res = write_file('./install/.htaccess', $d_htf);
				$ready = $res[0];
				echo $res[1];
				$res = write_file('./install/resources/.htaccess', $a_htf);
				$ready = $res[0];
				echo $res[1];
				$res = write_file('./uploads/.htaccess', $d_htf);
				$ready = $res[0];
				echo $res[1];
				$res = write_file('./api/utils/.htaccess', $d_htf);
				$ready = $res[0];
				echo $res[1];
				$res = write_file('.htaccess', $htf);
				$ready = $res[0];
				echo $res[1];
				echo '
				</div>
				<hr>
				<a href="/setup/'.($step-1).'" class="btn btn-outline-primary btn-block me-3">Back</a>
				';
				if ($ready) echo '<a href="/setup/'.($step+1).'" class="btn btn-primary btn-block me-3">Continue</a>';
				else echo '<a href="/setup/'.$step.'" class="btn btn-primary btn-block me-3">Retry</a>';
				break;
			// 3: Databse credentials
			case 3:
				if (!file_exists($temp_file)) {
					$res = create_temp($temp_file);
					if (!$res) {
						echo Res::fail(500, 'Could not create temp file');
						exit();
					}
					else {
						$token = bin2hex(random_bytes(32));
						$res = append_temp($temp_file, array('sh-setup-token' => $token));
						if (!$res) {
							echo Res::fail(500, 'Could not write to temp file');
							exit();
						}
						setcookie('sh-setup-token', $token, time() + (86400 * 30), "/");
					}
				}
				print_header($step);
				echo '<div id="db-creds-setup">
					<h2>Database credentials</h2>
					<p>Enter your database credentials below.</p>
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
				</div>
				<div class="alert alert-danger d-none" role="alert" id="db-creds-error"></div>
				<div class="alert alert-success d-none" role="alert" id="db-creds-success"></div>
				<hr>
				<a href="/setup/'.($step-1).'" class="btn btn-outline-primary btn-block me-3">Back</a>
				<button class="btn btn-secondary btn-block disabled me-3" id="db-creds-check">Check</button>';
				break;
			// Step 4: Database table creation
			case 4:
				print_header($step);
				echo '<div id="db-table-setup">
					<h2>Database setup</h2>
					<p>Follow the steps below to create required database tables.</p>
					<div class="mb-4">
						<h4>Name</h4>
						<p>The database name must not contain spaces or non-alphanumeric characters.</p>
						<div class="form-floating mb-3">
							<input type="text" name="db-name" id="db-name" class="form-control" placeholder="Database name" required autocomplete>
							<label for="db-name">Database name</label>
						</div>
						<button type="button" class="btn btn-secondary disabled" id="db-name-create">Create</button>
					</div>
					<div class="mb-4">
						<h4>Tables</h4>
						<p id="db-prefix-demo">(sh_files, sh_users)</p>
						<p>The table prefix must not contain spaces, non-alphanumeric characters, or exceed 12 characters in length.</p>
						<div class="form-floating mb-3">
							<input type="text" name="db-prefix" id="db-prefix" class="form-control" placeholder="Table prefix" value="sh_" required autocomplete>
							<label for="db-prefix">Table prefix</label>
						</div>
						<button type="button" class="btn btn-secondary disabled" id="db-tables-create">Create</button>
					</div>
				</div>
				<div class="alert alert-danger d-none" role="alert" id="db-setup-error"></div>
				<div class="alert alert-success d-none" role="alert" id="db-setup-success"></div>
				<hr>
				<a href="/setup/'.($step-1).'" class="btn btn-outline-primary btn-block me-3">Back</a>
				<button class="btn btn-secondary btn-block me-3 disabled" id="continue-btn">Continue</button>';
				break;
			// Step 5: Admin user creation
			case 5:
				print_header($step);
				echo '<div id="admin-setup">
					<h2>Account creation</h2>
					<p>Enter credentials for your administrator account.</p>
					<div class="form-floating mb-3">
						<input type="text" name="acc-un" id="acc-un" class="form-control" placeholder="Username" required autocomplete>
						<label for="acc-un">Username</label>
					</div>
					<div class="form-floating mb-3">
						<input type="password" name="acc-pass" id="acc-pass" class="form-control" placeholder="Password" required autocomplete>
						<label for="acc-pass">Password</label>
					</div>
					<div class="form-floating mb-3">
						<input type="password" name="acc-pass-c" id="acc-pass-c" class="form-control" placeholder="Confirm password" required autocomplete>
						<label for="acc-pass-c">Confirm password</label>
					</div>
				</div>
				<div class="alert alert-danger d-none" role="alert" id="admin-setup-error"></div>
				<div class="alert alert-success d-none" role="alert" id="admin-setup-success"></div>
				<hr>
				<a href="/setup/'.($step-1).'" class="btn btn-outline-primary btn-block me-3">Back</a>
				<button class="btn btn-secondary btn-block me-3 disabled" id="continue-btn">Create</button>';
				break;
			// Step 6: App config and file creation
			case 6:
				print_header($step);
				echo '<div id="app-config-setup">
					<h2>App config</h2>
					<p>These values can either be configured or left at their default values.</p>
					<div class="mb-4" id="app-name-container">
						<h4>Name</h4>
						<p>The app name appears in the header and page titles.</p>
						<div class="form-floating mb-3">
							<input type="text" name="app-name" id="app-name" class="form-control" placeholder="App name" value="Shadow" required autocomplete>
							<label for="app-name">App Name</label>
						</div>
					</div>
					<div class="mb-4" id="webroot-container">
						<h4>App URL</h4>
						<p><i><strong>Note:</strong></i> Don\'t change this value unless you know what you\'re doing!</p>
						<div class="form-floating mb-3">
							<input type="text" class="form-select" name="app-webroot" id="app-webroot" placeholder="App URL" value="'.$_SERVER['HTTP_HOST'].'">
							<label for="app-webroot">App URL</label>
						</div>
					</div>
					<div class="mb-4" id="lang-container">
						<h4>Language*</h4>
						<p>Choose a default language for all users.</p>
						<div class="form-floating mb-3">
							<select class="form-select" name="app-lang" id="app-lang" disabled>
								<option selected>English</option>
							</select>
							<label for="app-lang">Language</label>
						</div>
					</div>
					<p>* Not functional yet.</p>
				</div>
				<div class="alert alert-danger d-none" role="alert" id="app-config-setup-error"></div>
				<div class="alert alert-success d-none" role="alert" id="app-config-setup-success"></div>
				<hr>
				<a href="/setup/'.($step-1).'" class="btn btn-outline-primary btn-block me-3">Back</a>
				<button class="btn btn-primary btn-block me-3" id="continue-btn">Create app config</button>';
				break;
			// TODO: Step 7: Finalize setup, button to turn off installer and redireect to login
			case 7:
				print_header($step);
				echo '<div id="app-finalize-setup">
					<h2>Finalize setup</h2>
					<p>Shadow is now set up and ready to use!</p>
				</div>
				<hr>
				<button class="btn btn-primary btn-block me-3" id="continue-btn">Continue</button>';
				break;
			// If no step is specified, redirect to step 0
			default:
				header('Location: /setup');
				break;
		}
	?>
</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="/install/resources/scripts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</html>
