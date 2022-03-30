<?php

// Prevent direct access
if (!isset($include)) {
	echo Res::fail(403, 'Forbidden');
	exit();
}

class File {
	// Function to get info about given file
	public static function get_info($filename) {
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
