<?php

// Include files
require_once dirname(__FILE__).'/utils/res.php';
require_once dirname(__FILE__).'/utils/db.php';
require_once dirname(__FILE__).'/auth.php';

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

class Upload {
	public function __construct($dir, $file, $uid, $conn) {
		$this->dir = $dir; // Upload directory
		$this->file = $file; // File object to uplaod
		$this->uid = $uid; // User ID initiating upload
		$this->conn = $conn; // DB connection
	}
	// Generate random string
	private static function random_string($len) {
		if (!isset($len)) $len = 10;
		$chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$str = '';
		for ($i = 0; $i < $len; $i++) $str .= $chars[mt_rand(0, strlen($chars)-1)];
		return $str;
	}
	// Upload file, add to db if successful
	public function upload_file() {
		$file = $this->file;
		$dir = $this->dir;
		$file_name = $file['name'];
		$file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
		$file_tmp_name = $file['tmp_name'];
		$file_error = $file['error'];
		$file_size = $file['size'];
		// If file is over 90MB, return error
		if ($file_size > 90000000) {
			return $this->upload_fail(405, 'Error uploading, filesize limit is 90MB');
		}
		// If there is an error with the file
		if ($file_error > 0) {
			return $this->upload_fail(500, 'Error uploading, file error');
		}
		// Move the file to the uploads directory
		$rand_name = $this->random_string(5).'.'.$file_ext;
		$upload_name = $dir.$rand_name;
		if (move_uploaded_file($file_tmp_name, $upload_name)) {
			// Add file to db
			$uid = $this->uid;
			$conn = $this->conn;
			$tz = new DateTimeZone('America/Halifax');
			$time = ((new DateTimeImmutable("now", $tz))->setTimezone($tz))->getTimestamp();
			// Format: UID, filename, timestamp
			$sql = "INSERT INTO files (uid, name, time) VALUES($uid, $upload_name, $time)";
			// Run query
			if(runQuery($conn, $sql)) {
				return $this->upload_success($dir.$rand_name);
			}
			else return $this->upload_fail(500, 'Error uploading, database error'); // TODO: Also delete file so we don't have 'ghost' files left over on fail
		}
		else {
			return $this->upload_fail(500, 'Error uploading, failed on move_uploaded_file()');
		}
	}
	private static function upload_success($path) {
		return Res::success(200, 'Uploaded successfully', $path);
	}
	private static function upload_fail($code, $msg) {
		return Res::fail($code, $msg);
	}
}
