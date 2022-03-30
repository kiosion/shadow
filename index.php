<?php

$include = true;

// Load sys config
$_SHADOW_SYS_CONFIG = include('config/system.php');
if (!$_SHADOW_SYS_CONFIG) {
	die('Error: Could not load system config.');
}

// Check install status
if ($_SHADOW_SYS_CONFIG['installed'] == false) {
	header('Location: /install');
	exit();
}

// Set app and api urls
if ($_SHADOW_SYS_CONFIG['ssl'] == true) {
	$_SHADOW_APP_URL = 'https://'.$_SHADOW_SYS_CONFIG['webroot'];
	$_SHADOW_API_URL = 'https://'.$_SHADOW_SYS_CONFIG['apiroot'];
}
else {
	$_SHADOW_APP_URL = 'http://'.$_SHADOW_SYS_CONFIG['webroot'];
	$_SHADOW_API_URL = 'http://'.$_SHADOW_SYS_CONFIG['apiroot'];
}

// Include files
require_once 'includes/utils/post.php';
require_once 'includes/utils/functions.php';

// Set token if user is logged in
if (isset($_COOKIE['shadow_login_token'])) {
	$_SHADOW_USER_TOKEN = $_COOKIE['shadow_login_token'];
	// Verify token
	$res = verify_login_token('any', $_SHADOW_USER_TOKEN, $_SHADOW_API_URL);
	if ($res['status'] == 'valid') {
		$_SHADOW_USER_UID = $res['uid'];
		// Load user config
		$_SHADOW_USER_CONFIG = include('config/user/'.$_SHADOW_USER_UID.'.php');
	}
}
else {
	$_SHADOW_USER_TOKEN = '';
}

// Handle URL paths
$res = handle_url_paths(parse_url($_SERVER['REQUEST_URI']));
$app_route = $res['app_route'];
$page_title = $_SHADOW_SYS_CONFIG['name'].' - '.$res['title'];
if ($app_route == 'file' || $app_route == 'raw' || $app_route == 'download') {
	$content_type = get_mimetype($res['ext']);
	$filename = $res['filename'];
	$og_name = $res['og_name'];
	$uid = $res['uid'];
	// Check if requested file is private
	$arr = array("filename"=>"$filename");
	$res = post('api/v2/file/get-info/', $arr);
	$res_decoded = json_decode($res);
	$priv_file = '';
	// If file is hidden
	if ($res_decoded->data->vis == 1) {
		//Check if user is logged in
		if (!isset($_COOKIE['shadow_login_token'])) {
			// Display 403 error
			$app_route = '403';
		}
		else {
			// Verify login token
			if (verify_login_token($app_route, $_SHADOW_USER_TOKEN)['status'] != 'valid') $app_route = '403';
			else $priv_file = 'hidden';
		}
	}
	// If file is private
	if ($res_decoded->data->vis == 2) {
		//Check if user is logged in
		if (!isset($_COOKIE['shadow_login_token'])) $app_route = '403';
		else {
			// Verify login token
			if (verify_login_token($app_route, $_SHADOW_USER_TOKEN)['status'] != 'valid') $app_route = '403';
			else {
				if (verify_access($filename, $_SHADOW_USER_TOKEN)['status'] != 'valid') {
					var_dump(verify_access($filename, $_SHADOW_USER_TOKEN));
					$app_route = '403';
				}
				else $priv_file = 'private';
			}
		}
	}
}

// Verify login state only if not viewing public page
if ($app_route != 'raw' && $app_route != 'file' && $app_route != 'download' && $app_route != '404' && $app_route != '403') {
	$res = verify_login_token($app_route, $_SHADOW_USER_TOKEN);
	if ($res['status'] == 'valid') {
		if ($app_route == 'login') header('Location: /home');
		$user_auth_token = $res['token'];
		$user_auth_role = $res['role'];
		$user_auth_username = $res['username'];
	}
	else if ($app_route != 'login') {
		header('Location: /login');
		exit();
	}
}

// Show HTML content
switch ($app_route) {
	case 'raw':
		// Set HTTP headers
		header('Content-Type: '.$content_type);
		header('Content-Length: '.filesize('uploads/users/'.$uid.'/'.$filename));
		//header('Content-Length: '.filesize(get_file($filename)));
		//Display file using fpassthru
		fpassthru(fopen('uploads/users/'.$uid.'/'.$filename, 'r'));
		break;
	case 'download':
		// Set HTTP headers
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.$og_name);
		// Serve file using readfile
		readfile('uploads/users/'.$uid.'/'.$filename);
		break;
	default:
		require_once 'includes/components/head.php';
		// Display image
		//echo '<img src="'.get_file('uxowVt').'" alt="img" class="img-fluid">';

		switch ($app_route) {
			// Login page
			case 'login':
				$includeBody = 'includes/pages/login.php';
				$includeFooter = 'includes/components/footer.php';
				break;
			// Home page
			case 'index':
				$includeHeader = 'includes/components/header.php';
				$includeBody = 'includes/pages/index.php';
				$includeFooter = 'includes/components/footer.php';
				break;
			// Upload page
			case 'upload':
				$includeHeader = 'includes/components/header.php';
				$includeBody = 'includes/pages/upload.php';
				$includeFooter = 'includes/components/footer.php';
				break;
			// File view page
			case 'file':
				$includeHeader  = 'includes/components/header.php';
				$includeBody = 'includes/pages/file.php';
				$includeFooter = 'includes/components/footer.php';
				break;
			// Admin UI
			case 'admin':
				$includeHeader = 'includes/components/header.php';
				$includeBody = 'includes/pages/admin.php';
				$includeFooter = 'includes/components/footer.php';
				break;
			// App settings
			case 'settings':
				$includeHeader = 'includes/components/header.php';
				$includeBody = 'includes/pages/settings.php';
				$includeFooter = 'includes/components/footer.php';
				break;
			// Account page
			case 'account':
				$includeHeader = 'includes/components/header.php';
				$includeBody = 'includes/pages/account.php';
				$includeFooter = 'includes/components/footer.php';
				break;
			// Error pages
			default:
				$includeHeader = 'includes/components/header.php';
				$includeBody = 'includes/pages/error/'.$app_route.'.php';
				$includeFooter = 'includes/components/footer.php';
				break;
		}
		require_once 'includes/components/body.php';
		break;
}
