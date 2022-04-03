<?php

$include = true;

// Load sys config
$_SHADOW_SYS_CONFIG = include('app/config/system.php');
if (!$_SHADOW_SYS_CONFIG) {
	die('Error: Could not load system config.');
}

// Check install status
if ($_SHADOW_SYS_CONFIG['APP_IS_INSTALLED'] == false) {
	// If first part of URL is not 'install', redirect to install
	if (explode('/', parse_url($_SERVER['REQUEST_URI'])['path'])[1] != 'setup') {
		header('Location: /setup/');
		exit();
	}
	require 'install/index.php';
	exit();
}
// TODO: Check if post install is complete
// if ($_SHADOW_SYS_CONFIG['APP_IS_POST_INSTALL'] == true) {
// 	require 'install/post.php';
// 	exit();
// }

// Set app and api urls
if ($_SHADOW_SYS_CONFIG['APP_SSL'] == true) {
	$_SHADOW_APP_URL = 'https://'.$_SHADOW_SYS_CONFIG['APP_URL'];
	$_SHADOW_API_URL = 'https://'.$_SHADOW_SYS_CONFIG['APP_API_URL'];
}
else {
	$_SHADOW_APP_URL = 'http://'.$_SHADOW_SYS_CONFIG['APP_URL'];
	$_SHADOW_API_URL = 'http://'.$_SHADOW_SYS_CONFIG['APP_API_URL'];
}

// Include files
require_once 'app/utils/post.php';
require_once 'app/utils/functions.php';

// Set token if user is logged in
if (isset($_COOKIE['shadow_login_token'])) {
	$_SHADOW_USER_TOKEN = $_COOKIE['shadow_login_token'];
	// Verify token
	$res = verify_login_token('any', $_SHADOW_USER_TOKEN, $_SHADOW_API_URL);
	if ($res['status'] == 'valid') {
		$_SHADOW_USER_UID = $res['uid'];
		// Load user config
		try {
			$_SHADOW_USER_CONFIG = include('app/config/user/'.$_SHADOW_USER_UID.'.php');
		}
		catch (Exception $e) {
			die('Error: Could not load user config.');
		}
	}
}
else {
	$_SHADOW_USER_TOKEN = '';
}

// Handle URL paths
$res = handle_url_paths(parse_url($_SERVER['REQUEST_URI']));
$app_route = $res['app_route'];
$page_title = $_SHADOW_SYS_CONFIG['APP_NAME'].' - '.$res['title'];
if ($app_route == 'file' || $app_route == 'raw' || $app_route == 'download') {
	$FILE_MIMETYPE = get_mimetype($res['ext']);
	$FILE_NAME = $res['filename'];
	$FILE_EXT = $res['ext'];
	$FILE_OGNAME = $res['og_name'];
	$FILE_UID = $res['uid'];
	$FILE_PATH = 'uploads/users/'.$FILE_UID.'/'.$FILE_NAME.'.'.$FILE_EXT;
	// Check if requested file is private
	$arr = array("filename"=>"$FILE_NAME");
	$res = post('api/v2/file/get-info/', $arr);
	$res_decoded = json_decode($res);
	$FILE_VIS = '';
	// If file is hidden
	if ($res_decoded->data->vis == 1) {
		if (!isset($_COOKIE['shadow_login_token'])) $app_route = '403';
		else {
			if (verify_login_token($app_route, $_SHADOW_USER_TOKEN)['status'] != 'valid') $app_route = '403';
			else $FILE_VIS = 'hidden';
		}
	}
	// If file is private
	if ($res_decoded->data->vis == 2) {
		if (!isset($_COOKIE['shadow_login_token'])) $app_route = '403';
		else {
			if (verify_login_token($app_route, $_SHADOW_USER_TOKEN)['status'] != 'valid') $app_route = '403';
			else {
				if (verify_access($FILE_NAME, $_SHADOW_USER_TOKEN)['status'] != 'valid') {
					//var_dump(verify_access($FILE_NAME, $_SHADOW_USER_TOKEN));
					$app_route = '403';
				}
				else $FILE_VIS = 'private';
			}
		}
	}
}

// Verify login state if not viewing public page TODO: Make this more efficient, array of allowed pages
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

switch ($app_route) {
	case 'raw':
		header('Content-Type: '.$FILE_MIMETYPE);
		header('Content-Length: '.filesize($FILE_PATH));
		fpassthru(fopen($FILE_PATH, 'r'));
		break;
	case 'download':
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.$FILE_OGNAME);
		readfile($FILE_PATH);
		break;
	default:
		require_once 'app/components/head.php';
		switch ($app_route) {
			case 'login':
				$includeBody = 'app/pages/login.php';
				$includeFooter = 'app/components/footer.php';
				break;
			case 'index':
				$includeHeader = 'app/components/header.php';
				$includeBody = 'app/pages/index.php';
				$includeFooter = 'app/components/footer.php';
				break;
			case 'upload':
				$includeHeader = 'app/components/header.php';
				$includeBody = 'app/pages/upload.php';
				$includeFooter = 'app/components/footer.php';
				break;
			case 'file':
				$includeHeader  = 'app/components/header.php';
				$includeBody = 'app/pages/file.php';
				$includeFooter = 'app/components/footer.php';
				break;
			case 'admin':
				$includeHeader = 'app/components/header.php';
				$includeBody = 'app/pages/admin.php';
				$includeFooter = 'app/components/footer.php';
				break;
			case 'settings':
				$includeHeader = 'app/components/header.php';
				$includeBody = 'app/pages/settings.php';
				$includeFooter = 'app/components/footer.php';
				break;
			case 'account':
				$includeHeader = 'app/components/header.php';
				$includeBody = 'app/pages/account.php';
				$includeFooter = 'app/components/footer.php';
				break;
			default:
				$includeHeader = 'app/components/header.php';
				$includeBody = 'app/pages/error/'.$app_route.'.php';
				$includeFooter = 'app/components/footer.php';
				break;
		}
		require_once 'app/components/body.php';
		break;
}
