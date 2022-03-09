<?php

// TODO: Import config from .json file, split out of index.php
// $config_array = json_decode(file_get_contents('config/app.json'), true);
// if ($config_array['ssl']) $app_url = 'https://'.$config_array['host'];
// else $app_url = 'http://'.$config_array['host'];
// $app_name = $config_array['name'];
//$app_route = 'login';
//$page_title = 'Shadow - Login';
$include = true;

// Include files
require_once 'includes/utils/post.php';
require_once 'includes/utils/functions.php';

// Handle URL paths
$res = handle_url_paths(parse_url($_SERVER['REQUEST_URI']));
$app_route = $res['app_route'];
$page_title = 'Shadow - '.$res['title'];
if ($app_route == 'file' || $app_route == 'raw' || $app_route == 'download') {
	$content_type = get_mimetype($res['ext']);
	$filename = $res['filename'];
	$og_name = $res['og_name'];
	$uid = $res['uid'];
}

// Verify login state only if not viewing public page
if ($app_route != 'raw' && $app_route != 'file' && $app_route != 'download' && $app_route != '404' && $app_route != '403') {
	$res = verify_login_token($app_route);
	if ($res['status'] == 'valid') {
		if ($app_route == 'login') header('Location: /home');
		$user_auth_token = $res['token'];
		$user_auth_role = $res['role'];
		$user_auth_username = $res['username'];
	}
	else if ($app_route != 'login') {
		header('Location: /login'.$res['redir'].'');
		exit();
	}
}

// Show HTML content
switch ($app_route) {
	case 'raw':
		// Set HTTP headers
		header('Content-Type: '.$content_type);
		header('Content-Length: '.filesize('uploads/users/'.$uid.'/'.$filename));
		// Display file using fpassthru
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
