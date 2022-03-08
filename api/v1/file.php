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
//require_once 'auth.php';

// Set HTTP headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

// Get token from header and check if it is valid
//if (!Auth::check_token(JWT::get_bearer_token(), 'api')) {
// 	echo Res::fail(401, 'Unauthorized');
// 	exit();
// }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (isset($_POST['action'])) {
		$action = $_POST['action'];
		switch ($action) {
			// Get uploader uid from filename, this func is public as it is used to view files
			case 'get_uid':
				if (!isset($_POST['filename'])) {
					echo Res::fail(401, 'Filename not provided');
					break;
				}
				// Check for file extension
				$ext = pathinfo($_POST['filename'], PATHINFO_EXTENSION);
				if ($ext == '') {
					$res = File::get_uid($_POST['filename']);
				}
				else {
					// Remove file extension
					$filename = substr($_POST['filename'], 0, -strlen($ext) - 1);
					$res = File::get_uid($filename);
				}
				echo $res;
				break;
			case 'get_info':
				break;
			case 'set_visibility':
				break;
			// Not a valid action
			default:
				echo Res::fail(400, 'Invalid action');
				break;
		}
	}
}

class File {
	// Function to get user id from token
	public static function get_uid($filename) {
		if (!isset($filename)) return false;
		else {
			// Query database for user id given username
			$sql = "SELECT * FROM files WHERE BINARY ul_name = '$filename';";
			$result = runQuery($sql);
			$row = fetchAssoc($result);
			if ($row) return Res::success(200, 'File info retrieved', array("uid" => $row['uid'], "og_name" => $row['og_name'], "ul_name" => $row['ul_name'], "ext" => $row['ext']));
			else return Res::fail(404, 'File "'.$filename.'" not found');
		}
	}
}
