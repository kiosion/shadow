<?php

// Prevent direct access
if (!isset($include)) {
	echo Res::fail(403, 'Forbidden');
	exit();
}

// Get token from header and check if it is valid
// if (!Auth::check_token(JWT::get_bearer_token(), 'login')) {
// 	echo Res::fail(401, 'Unauthorized');
// 	exit();
// }

// Check provided token
if (isset($_POST['token'])) {
	$token = $_POST['token'];
	$auth = Auth::check_token($token, 'login'); // For now, only check for login token
	if (!$auth) {
		echo Res::fail(401, 'Unauthorized');
		exit();
	}
	// Get UID from token
	$uid = json_decode(Auth::get_uid($token))->data;
	if (empty($uid)) {
		echo Res::fail(500, 'Failed to get UID from token');
	}
}
else {
	echo Res::fail(401, 'Token not provided');
	exit();
}

if (isset($_FILES['file'])) {
	// Fetch user id from token
	$uid = json_decode(Auth::get_uid(JWT::get_bearer_token()), true);
	if ($uid['status'] == 'success') {
		$uid = $uid['data'];
	}
	else {
		echo Res::fail(500, 'File upload failed');
		exit();
	}
	// Create new upload object
	$upload = new Upload($_FILES['file'], $uid);
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
else {
	echo Res::fail(405, 'No file provided');
	exit();
}
