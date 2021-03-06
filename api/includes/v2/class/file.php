<?php

// Prevent direct access
if (!isset($include)) {
	echo Res::fail(403, 'Forbidden');
	exit();
}

class File {
	// Function to get info about given file
	public static function get_info($filename, $token) {
		GLOBAL $DB_PREFIX;
		if (!isset($filename)) return Res::fail(401, 'Filename not provided');
		// Query database for user id given username
		$table = $DB_PREFIX . 'files';
		$sql = "SELECT * FROM $table WHERE BINARY ul_name = '$filename';";
		$result = runQuery($sql);
		$row = fetchAssoc($result);
		if ($row) { 
			if (empty($token) || !Auth::check_token($token, 'login')) {
				if ($row['vis'] != 0) {
					return Res::fail(401, 'File is private');
					exit();
				}
			}
			else if ($row['uid'] != json_decode(Auth::get_uid($token))->data) {
				if ($row['vis'] == 2) {
					return Res::fail(401, 'File is not owned by user');
					exit();
				}
			}

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
	public static function get_uid($filename, $token) {
		GLOBAL $DB_PREFIX;
		// Query database for user id given username
		$table = $DB_PREFIX . 'files';
		$sql = "SELECT * FROM $table WHERE BINARY ul_name = '$filename';";
		$result = runQuery($sql);
		$row = fetchAssoc($result);
		if ($row) {
			if (empty($token) || !Auth::check_token($token, 'login')) {
				if ($row['vis'] != 0) {
					return Res::fail(401, 'File is private');
					exit();
				}
			}
			else if ($row['uid'] != json_decode(Auth::get_uid($token))->data) {
				if ($row['vis'] == 2) {
					return Res::fail(401, 'File is not owned by user');
					exit();
				}
			}
			
			return Res::success(
				200, 
				'UID retrieved', 
				array(
					"uid" => $row['uid'],
					"ul_name" => $row['ul_name'],
				)
			); 
		}
		else {
			return Res::fail(404, 'File "'.$filename.'" not found');
		}
	}
	// Function to get content-type from file extension
	public static function get_mimetype($ext) {
		$ext = strtolower($ext);
		// Set content type
		switch ($ext) {
			case 'txt':
			case 'asc':
				return 'text/plain';
				break;
			case 'html':
				return 'text/html';
				break;
			case 'css':
				return 'text/css';
				break;
			case 'js':
				return 'application/js';
				break;
			case 'php':
				return 'application/php';
				break;
			case 'jpg':
				return 'image/jpeg';
				break;
			case 'png':
				return 'image/png';
				break;
			case 'gif':
				return 'image/gif';
				break;
			case 'pdf':
				return 'application/pdf';
				break;
			case 'zip':
				return 'application/zip';
				break;
			case 'rar':
				return 'application/rar';
				break;
			case '7z':
				return 'application/7z';
				break;
			case 'gz':
				return 'application/gzip';
				break;
			case 'mp3':
				return 'audio/mpeg';
				break;
			case 'wav':
				return 'audio/wav';
				break;
			case 'mp4':
				return 'video/mp4';
				break;
			case 'webm':
				return 'video/webm';
				break;
			case 'mkv':
				return 'video/mkv';
				break;
			case 'mov':
				return 'video/mov';
				break;
			case 'flac':
				return 'audio/flac';
				break;
			default:
				return 'application/octet-stream';
				break;
		}
	}
	// Function to toggle file visibility
	public static function set_visibility($fileID, $token, $vis) {
		GLOBAL $DB_PREFIX;
		if (!isset($token) || empty($token)) {
			return Res::fail(401, 'Token not provided');
			exit();
		}
		if (!Auth::check_token($token, 'login')) {
			return Res::fail(401, 'Invalid token');
			exit();
		}
		if (!isset($fileID) || !isset($vis)) {
			return Res::fail(401, 'File ID or visibility not provided');
			exit();
		}
		// Get UID from token
		require_once 'auth.php';
		$uid = json_decode(Auth::get_uid($token))->data;
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
		$table = $DB_PREFIX . 'files';
		$sql = "SELECT * FROM $table WHERE id = '$fileID';";
		$result = runQuery($sql);
		$row = fetchAssoc($result);
		// Also query database to check if user is administrator
		$table = $DB_PREFIX . 'users';
		$sql = "SELECT * FROM $table WHERE id = '$uid';";
		$result = runQuery($sql);
		$row2 = fetchAssoc($result);
		if ($row && $row2) {
			if ($row['uid'] == $uid || $row2['role'] == 1) {
				if ($row['vis'] == $vis) {
					return Res::fail(400, 'File visbility already set to '.$vis);
				}
				else {
					// Query database to set file to public
					$table = $DB_PREFIX . 'files';
					$sql = "UPDATE $table SET vis = '$vis' WHERE id = '$fileID';";
					runQuery($sql);
					return Res::success(200, 'File visibility set', $vis);
				}
			}
			else return Res::fail(403, 'You are not the owner of this file');
		}
		else return Res::fail(404, 'File or user not found');
	}
}
