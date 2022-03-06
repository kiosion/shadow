<?php

// Set vars
$view = 'login';
$title = 'Shadow - Login';
$include = true;

// Include files
require_once 'includes/utils/post.php';
require_once 'includes/utils/functions.php';

// Handle URL paths
$res = handle_url_paths(parse_url($_SERVER['REQUEST_URI']));
switch ($res['view']) {
	case 'raw':
		$view = 'raw';
		$filename = $res['filename'];
		$uid = $res['uid'];
		// Get filetype from extension
		$ext = pathinfo($filename, PATHINFO_EXTENSION);
		$content_type = get_contenttype($ext);
		break;
	case 'file':
		$view = 'file';
		$title = $res['title'];
		$filename = $res['filename'];
		$uid = $res['uid'];
		// Get filetype from extension
		$ext = pathinfo($filename, PATHINFO_EXTENSION);
		$content_type = get_contenttype($ext);
		break;
	case 'admin':
		$view = 'admin';
		$title = 'Shadow - Admin';
		break;
	case '404':
		$view = '404';
		break;
	case '403':
		$view = '403';
		break;
}

// Verify login state
if (!($view == 'raw' || $view == 'file')) {
	$res = verify_login_token();
	if ($res['view'] == 'index') {
		$view = 'index';
		$title = 'Shadow - Index';
	}
}

// Page content
if ($view == 'raw') {
	// Set HTTP headers
	header('Content-Type: '.$content_type);
	header('Content-Length: '.filesize('uploads/users/'.$uid.'/'.$filename));
	// Display file using fpassthru
	fpassthru(fopen('uploads/users/'.$uid.'/'.$filename, 'r'));
	exit();
}
else {
	echo '
		<!DOCTYPE html>
		<html lang="en">
		<head>
			<meta charset="UTF-8">
			<meta http-equiv="X-UA-Compatible" content="IE=edge">
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<title>'.$title.'</title>
			'; require_once 'includes/styles.php'; echo '
		</head>
	';
	switch ($view) {
		// Login page
		case 'login':
			echo '
				<body class="text-center bg-black">
					<div class="d-flex flex-column min-vh-100 mx-2">
						'; require 'includes/components/header.php'; echo '
						<main class="container-fluid my-auto">
							'; include 'includes/pages/login.php'; echo '
						</main>
						'; require 'includes/components/footer.php'; echo '
					</div>
			';
			break;
		// Index page
		case 'index':
			echo '
				<body class="text-center bg-black">
					<div class="d-flex flex-column min-vh-100 mx-2">
						'; require 'includes/components/header.php'; echo '
						<main class="container-fluid my-auto">
							'; include 'includes/pages/index.php'; echo '
						</main>
						'; require 'includes/components/footer.php'; echo '
					</div>
			';
			break;
		// File view page
		case 'file':
			echo '
				<body class="text-center bg-black">
					<div class="d-flex flex-column min-vh-100 mx-2">
						'; require 'includes/components/file-header.php'; echo'
						<main class="container-fluid my-auto">
							'; include 'includes/pages/file.php'; echo '
						</main>
						'; require 'includes/components/file-footer.php'; echo '
					</div>
			';
			break;
		// default:
		// 	echo '
		// 		<body class="text-center bg-black">
		// 			<div class="d-flex flex-column min-vh-100">
		// 				'; require 'includes/components/header.php'; echo'
		// 				<main class="container my-auto">
		// 					'; include 'includes/pages/'.$view.'.php'; echo '
		// 				</main>
		// 				'; require 'includes/components/footer.php'; echo '
		// 			</div>
		// 	';
		// 	break;
	}
	echo '
		</body>
		'; require_once "includes/scripts.php"; echo '
		</html>
	';
}
