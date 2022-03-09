<?php

// Prevent direct access
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header('Content-Type: application/json; charset=utf-8');
	include_once 'utils/res.php';
	echo Res::fail(403, 'Forbidden');
	exit();
}

$include = true;

// Include files
require_once 'utils/res.php';
require_once 'utils/db.php';
require_once 'auth.php';

// Set HTTP headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

// Funcs:
// - get uploads
// - get account info
// - set account info

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (isset($_POST['token'])) {
		$token = $_POST['token'];
		$auth = Auth::check_token($token, 'login'); // For now, only check for login token
		// Get UID from token
		$uid = json_decode(Auth::get_uid($token))->data;
		if (empty($uid)) {
			echo Res::fail(500, 'Failed to get UID from token');
		}
		if ($auth) {
			// Continue to action
			if (isset($_POST['action'])) {
				$action = $_POST['action'];
				switch ($action) {
					// Get uploader uid from filename, this func is public as it is used to view files
					case 'get_uploads':
						if (!isset($_POST['start'])) {
							echo Res::fail(401, 'Start index not provided');
							break;
						}
						$start = $_POST['start'];
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
						$arr = User::get_uploads($uid, $start, $sort, $order);
						if ($arr != false) {
							echo Res::success(200, 'Uploads retrieved', $arr);
						}
						else {
							echo Res::fail(404, 'No uploads found');
						}
						break;
					case 'get_upload_count':
						$count = User::get_upload_count($uid);
						if ($count != false) {
							echo Res::success(200, 'Upload count retrieved', $count);
						}
						else {
							echo Res::fail(404, 'No uploads found');
						}
						break;
					case 'get_role':
						$role = User::get_role($uid);
						if ($role != false) {
							echo Res::success(200, 'Role retrieved', $role);
						}
						else {
							echo Res::fail(404, 'No role or user found');
						}
						break;
				}
			}
			else {
				echo Res::fail(400, 'Action not provided');
			}
		}
		// If token is invalid
		else {
			echo Res::fail(401, 'Invalid token');
		}
	}
	// If token is not provided
	else {
		echo Res::fail(401, 'Token not provided');
	}
}

class User {
	public static function get_uploads($uid, $start, $sort, $order) {
		if (!isset($uid) || !isset($start)) return false;
		// Set query
		$sql = "SELECT * FROM files WHERE uid = $uid ORDER BY $sort $order LIMIT $start, 10";
		// Get results
		$res = runQuery($sql);
		$rows = array();
		// If results are found
		if ($res) {
			// Loop through results
			while ($row = $res->fetch_assoc()) {
				// Add to array
				$rows[] = $row;
			}
			// Return array
			return $rows;
		}
		// If no results are found
		else {
			return false;
		}
	}
	public static function get_upload_count($uid) {
		if (!isset($uid)) return false;
		// Set query
		$sql = "SELECT COUNT('id') AS count FROM files WHERE uid = '$uid'";
		// Get results
		$res = runQuery($sql);
		// If results are found
		if ($res) {
			// Return count
			return $res->fetch_assoc()['count'];
		}
		// If no results are found
		else {
			return false;
		}
	}
	public static function get_role($uid) {
		if (!isset($uid)) return false;
		// Run query
		$sql = "SELECT role FROM users WHERE id = '$uid'";
		$res = runQuery($sql);
		if ($res) {
			return $res->fetch_assoc()['role'];
		}
		// If no results are found
		else {
			return false;
		}
	}
}
