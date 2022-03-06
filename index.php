<?php

// TODO: Import config

// Set vars
$app_route = 'index';
$page_title = 'Shadow - Index'; // TODO: Name of app
$include = true;

// Include files
require_once 'includes/utils/post.php';
require_once 'includes/utils/functions.php';

// Handle URL paths
$res = handle_url_paths(parse_url($_SERVER['REQUEST_URI']));
switch ($res['app_route']) {
	case 'file':
		$app_route = 'file';
		$page_title = $res['title'];
		$og_name = $res['og_name'];
		$uid = $res['uid'];
		$filename = $res['filename'];
		$content_type = get_contenttype($res['ext']);
		break;
	case 'raw':
		$app_route = 'raw';
		$uid = $res['uid'];
		$filename = $res['filename'];
		$content_type = get_contenttype($res['ext']);
		break;
	case 'download':
		$app_route = 'download';
		$uid = $res['uid'];
		$og_name = $res['og_name'];
		$filename = $res['filename'];
		$content_type = get_contenttype($res['ext']);
		break;
	case 'admin':
		$app_route = 'admin';
		$page_title = $res['title'];
		break;
	case '404':
		$app_route = '404';
		break;
	case '403':
		$app_route = '403';
		break;
}

// Verify login state only if not viewing file
if ($app_route != 'raw' && $app_route != 'file') {
	$res = verify_login_token($app_route);
	if (!($res['status'] == 'valid')) {
		$app_route = 'login';
		$page_title = 'Shadow - Login';
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
			// Index / account page
			case 'index':
				$includeHeader = 'includes/components/header.php';
				$includeBody = 'includes/pages/index.php';
				$includeFooter = 'includes/components/footer.php';
				break;
			// File view page
			case 'file':
				$includeHeader  = 'includes/components/file-header.php';
				$includeBody = 'includes/pages/file.php';
				$includeFooter = 'includes/components/file-footer.php';
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
