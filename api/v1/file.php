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
				if ((!isset($_POST['filename']) && !isset($_POST['file_id'])) || !isset($_POST['token'])) {
					echo Res::fail(401, 'Filename, ID, or token not provided');
					break;
				}
				// Check for file extension
				$ext = pathinfo($_POST['filename'], PATHINFO_EXTENSION);
				if ($ext != '') {
					// Remove file extension
					$filename = substr($_POST['filename'], 0, -strlen($ext) - 1);
				}
				echo File::get_info($filename, $token);
				break;
			case 'set_visibility':
				if (!isset($_POST['fileID']) || !isset($_POST['token']) || !isset($_POST['vis'])) {
					echo Res::fail(401, 'FileID, token, or visibility not provided');
					break;
				}
				// Call set_visibility with fileID and token
				echo File::set_visibility($_POST['fileID'], $_POST['token'], $_POST['vis']);
				break;
			// Not a valid action
			default:
				echo Res::fail(400, 'Invalid action');
				break;
		}
	}
}

class File {
	// Function to get info about given file
	public static function get_info($filename, $token) {
		if (!isset($filename)) return Res::fail(401, 'Filename not provided');
		// Query database for user id given username
		$sql = "SELECT * FROM files WHERE BINARY ul_name = '$filename';";
		$result = runQuery($sql);
		$row = fetchAssoc($result);
		if ($row) { 
			return Res::success(
				200, 
				'File info retrieved', 
				array(
					"id" => $row['id'],
					"uid" => $row['uid'], 
					"og_name" => $row['og_name'], 
					"ul_name" => $row['ul_name'], 
					"ext" => $row['ext'],
					"time" => $row['time'],
					"size" => $row['size'],
					"vis" => $row['vis']
				)
			); 
		}
	}
	// Function to get user id from token
	public static function get_uid($filename) {
		//if (!isset($filename)) return Res::fail(401, 'Filename not provided');
		// Query database for user id given username
		$sql = "SELECT * FROM files WHERE BINARY ul_name = '$filename';";
		$result = runQuery($sql);
		$row = fetchAssoc($result);
		if ($row) {
			return Res::success(
				200, 
				'File info retrieved', 
				array(
					"uid" => $row['uid'], 
					"og_name" => $row['og_name'], 
					"ext" => $row['ext']
				)
			); 
		}
		else {
			return Res::fail(404, 'File "'.$filename.'" not found');
		}
	}
	// Function to toggle file visibility
	public static function set_visibility($fileID, $token, $vis) {
		if (!isset($fileID) || !isset($token) || !isset($vis)) return Res::fail(401, 'FileID, token, or visibility not provided');
		// Get UID from token
		require_once 'auth.php';
		$uid = json_decode(Auth::get_uid($_POST['token']))->data;
		// $arr = array("action"=>"get_uid","token"=>"$token");
		// $res = post('http://localhost/api/v1/auth.php', $arr);
		// $res_decoded = json_decode($res);
		// $uid = $res_decoded->data;
		if (empty($uid)) {
			return Res::fail(500, 'Failed to get UID from token');
			exit();
		}
		switch ($vis) {
			case '2':
				$vis = 2;
				break;
			case '1':
				$vis = 1;
				break;
			case '0':
				$vis = 0;
				break;
			default:
				return Res::fail(400, 'Invalid visibility');
				break;
		}
		// Query database to check if user is owner of file
		$sql = "SELECT * FROM files WHERE id = '$fileID';";
		$result = runQuery($sql);
		$row = fetchAssoc($result);
		// Also query database to check if user is administrator
		$sql = "SELECT * FROM users WHERE id = '$uid';";
		$result = runQuery($sql);
		$row2 = fetchAssoc($result);
		if ($row && $row2) {
			if ($row['uid'] == $uid || $row2['role'] == 1) {
				if ($row['vis'] == $vis) {
					return Res::fail(400, 'File visbility already set to '.$vis);
				}
				else {
					// Query database to set file to public
					$sql = "UPDATE files SET vis = '$vis' WHERE id = '$fileID';";
					runQuery($sql);
					return Res::success(200, 'File visibility set', $vis);
				}
			}
			else return Res::fail(403, 'You are not the owner of this file');
		}
		else return Res::fail(404, 'File or user not found');
	}
}
