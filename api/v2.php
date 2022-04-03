<?php

// Set HTTP headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET');

// Prevent direct access
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'GET') {
	//require_once 'utils/res.php';
	echo 'Shoo, nothing for you here!';
	exit();
}

// Load sys config
$include = true;
$_SHADOW_SYS_CONFIG = include('../app/config/system.php');
if (!$_SHADOW_SYS_CONFIG) {
	die('Error: Could not load system config.');
}
$DB_PREFIX = $_SHADOW_SYS_CONFIG['APP_DB_PREFIX'];
	
// Include files
require_once 'utils/res.php';
require_once 'utils/jwt.php';
require_once 'utils/db.php';

// Get request URL
$url = parse_url($_SERVER['REQUEST_URI']);
// Explode path into array, delimiter is /
$path_arr = explode('/', $url['path']);
// Search array, remove keys before key with value 'v1.php'
$key = array_search('v2', $path_arr);
$path_arr = array_slice($path_arr, $key + 1);
// Check if object is empty
if (!isset($path_arr[0]) || empty($path_arr[0])) { 
	echo Res::fail(400, 'Object not provided');
	exit();
}
// Check if action is empty
if (!isset($path_arr[1]) || empty($path_arr[1])) {
	echo Res::fail(400, 'Action not provided');
	exit();
}

// Get bearer token from authorization header
$bearer_token = JWT::get_bearer_token();

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
switch ($path_arr[0]) {
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
		echo Res::fail(400, 'Invalid object \''.$path_arr[0].'\' provided');
		break;
}
