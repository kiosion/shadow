<?php

// Set HTTP headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

// Prevent direct access
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	require_once 'utils/res.php';
	echo Res::fail(403, 'Forbidden');
	exit();
}
	
// Include files
$include = true;
require_once 'utils/res.php';
require_once 'utils/jwt.php';
require_once 'utils/db.php';

// Get request URL
$url = parse_url($_SERVER['REQUEST_URI']);
// Explode path into array, delimiter is /
$path_arr = explode('/', $url['path']);
// Check if object is empty
if (!isset($path_arr[3]) || empty($path_arr[2])) { 
	echo Res::fail(400, 'Object not provided');
	exit();
}
// Check if action is empty
if (!isset($path_arr[4]) || empty($path_arr[3])) {
	echo Res::fail(400, 'Action not provided');
	exit();
}

// App class
// require_once 'includes/v2/action/app.php';

// Auth class
require_once 'includes/v2/class/auth.php';

// File class
require_once 'includes/v2/class/file.php';

// Upload class
require_once 'includes/v2/class/upload.php';

// User class
require_once 'includes/v2/class/user.php';

// Switch for object
switch ($path_arr[3]) {
	case 'app':
		//require_once 'includes/v2/action/app.php';
		break;
	case 'auth':
		require_once 'includes/v2/action/auth.php';
		break;
	case 'file':
		require_once 'includes/v2/action/file.php';
		break;
	case 'upload':
		require_once 'includes/v2/action/upload.php';
		break;
	case 'user':
		require_once 'includes/v2/action/user.php';
		break;
	default:
		echo Res::fail(400, 'Invalid object \''.$path_arr[3].'\' provided');
		break;
}
