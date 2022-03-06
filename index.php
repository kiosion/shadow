<?php

// Set vars
$view = 'index';
$title = 'Shadow - Index';
$include = true;

// Include files
require_once 'includes/utils/post.php';
require_once 'includes/utils/functions.php';

// Handle URL paths
$res = handle_url_paths(parse_url($_SERVER['REQUEST_URI']));
switch ($res['view']) {
	case 'file':
		$view = 'file';
		$title = $res['title'];
		$og_name = $res['og_name'];
		$ext = $res['ext'];
		$uid = $res['uid'];
		$filename = $res['filename'];
		$content_type = get_contenttype($ext);
		break;
	case 'raw':
		$view = 'raw';
		$ext = $res['ext'];
		$uid = $res['uid'];
		$filename = $res['filename'];
		$content_type = get_contenttype($ext);
		break;
	case 'download':
		$view = 'download';
		$ext = $res['ext'];
		$uid = $res['uid'];
		$og_name = $res['og_name'];
		$filename = $res['filename'];
		$content_type = get_contenttype($ext);
		break;
	case 'admin':
		$view = 'admin';
		$title = $res['title'];
		break;
	case '404':
		$view = '404';
		break;
	case '403':
		$view = '403';
		break;
}

// Verify login state only if not viewing file
if ($view != 'raw' && $view != 'file') {
	$res = verify_login_token($view);
	if (!($res['status'] == 'valid')) {
		$view = 'login';
		$title = 'Shadow - Login';
	}
}

// Show HTML content
if ($view == 'raw') {
	// Set HTTP headers
	header('Content-Type: '.$content_type);
	header('Content-Length: '.filesize('uploads/users/'.$uid.'/'.$filename));
	// Display file using fpassthru
	fpassthru(fopen('uploads/users/'.$uid.'/'.$filename, 'r'));
	exit();
}
else if ($view == 'download') {
	// Serve file using readfile
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename='.$og_name);
	readfile('uploads/users/'.$uid.'/'.$filename);
	exit();
}
else {
	require_once 'includes/components/head.php';
	switch ($view) {
		// Login page
		case 'login':
			$includeBody = 'includes/pages/login.php';
			$includeFooter = 'includes/components/footer.php';
			break;
		// Index page
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
		case 'admin':
			$includeHeader = 'includes/components/header.php';
			$includeBody = 'includes/pages/admin.php';
			$includeFooter = 'includes/components/footer.php';
			break;
		default:
			$includeHeader = 'includes/components/header.php';
			$includeBody = 'includes/pages/error/'.$view.'.php';
			$includeFooter = 'includes/components/footer.php';
			break;
	}
	require_once 'includes/components/body.php';
	include_once 'includes/components/context-menu.php';
	echo '
		</body>
		'; require_once 'includes/scripts.php'; echo '
		</html>
	';
}
