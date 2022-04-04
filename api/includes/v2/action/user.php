<?php

// Prevent direct access
if (!isset($include)) {
	echo Res::fail(403, 'Forbidden');
	exit();
}

// Check provided token
if (!empty($bearer_token)) {
	$token = $bearer_token;
	$auth = Auth::check_token($token, 'login'); // For now, only check for login token
	if (!$auth) {
		echo Res::fail(401, 'Invalid token');
		exit();
	}
	// Get UID from token
	$uid = json_decode(Auth::get_uid($token))->data;
	if (empty($uid)) {
		echo Res::fail(500, 'Failed to get UID from token');
	}
}
else if (isset($_POST['token'])) {
	$token = $_POST['token'];
	$auth = Auth::check_token($token, 'login'); // For now, only check for login token
	if (!$auth) {
		echo Res::fail(401, 'Invalid token');
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

switch ($path_arr[1]) {
	// Get payload from token
	case 'get-payload':
		if (!isset($_POST['token'])) {
			echo Res::fail(401, 'Token not provided');
			break;
		}
		echo Auth::print_token($_POST['token']);
		break;
	// Get UID from token
	case 'get-uid':
		if (!isset($_POST['token'])) {
			echo Res::fail(401, 'Token not provided');
			break;
		}
		echo Auth::get_uid($_POST['token']);
		break;
	// Get user role
	case 'get-role':
		$role = User::get_role($uid);
		if ($role != false) {
			echo Res::success(200, 'Role retrieved', $role);
		}
		else {
			echo Res::fail(404, 'No user found');
		}
		break;
	// Get all uploads from user
	case 'get-uploads':
		if (!isset($_POST['start']) || !isset($_POST['limit'])) {
			echo Res::fail(401, 'Start or limit index not provided');
			break;
		}
		$start = $_POST['start'];
		$limit = $_POST['limit'];
		if ($start < 0) $start = 0;
		switch ($_POST['sort']) {
			case 'n':
				$sort = 'og_name';
				break;
			case 't':
				$sort = 'time';
				break;
			case 's':
				$sort = 'size';
				break;
			default:
				$sort = 'time';
				break;
		}
		switch ($_POST['order']) {
			case 'a':
				$order = 'ASC';
				break;
			case 'd':
				$order = 'DESC';
				break;
			default:
				$order = 'DESC';
				break;
		}
		if (isset($_POST['filter'])) $filter = $_POST['filter'];
		else $filter = '';
		$arr = User::get_uploads($uid, $start, $limit, $sort, $order, $filter);
		if ($arr != false) {
			echo Res::success(200, 'Uploads retrieved', $arr);
		}
		else {
			echo Res::fail(404, 'No uploads found');
		}
		break;
	// Get number of uploads from user
	case 'get-upload-count':
		if (isset($_POST['filter'])) $filter = $_POST['filter'];
		else $filter = '';
		$count = User::get_upload_count($uid, $filter);
		if ($count != false) {
			echo Res::success(200, 'Upload count retrieved', $count);
		}
		else {
			echo Res::fail(404, 'No uploads found');
		}
		break;
	default:
		echo Res::fail(400, 'Invalid action provided');
		break;
}
