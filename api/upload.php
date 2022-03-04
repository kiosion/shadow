<?php

// Include files
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'utils/res.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'utils/db.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'utils/upload.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'utils/auth.php';

// Set HTTP headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

// Get token from header and check if it is valid
if (!Auth::check_api_auth(JWT::get_bearer_token())) {
	echo Res::fail(401, 'Not authorized');
	exit();
}

// Set some vars
$res = array();
$upload_dir = $cwd.'../imgs/';
if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);

// If POST is used
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (isset($_FILES['file'])) {
		// Fetch user id from token
		$uid = Auth::get_user_id($conn, JWT::get_bearer_token());
		// Create new upload object
		$upload = new Upload($upload_dir, $_FILES['file'], $uid);
		// Upload file
		if ($res = $upload->upload_file()) {
			// Return response
			echo $res;
		}
		else {
			// Return error
			echo Res::fail(500, 'File upload failed');
		}
		exit();
	}
	// If no file provided
	else {
		echo Res::fail(405, 'No file provided');
		exit();
	}
}
// If GET is used
else {
	echo Res::fail(405, 'Method not allowed');
	exit();
}
