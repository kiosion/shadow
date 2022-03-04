<?php

// Include files
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'utils/res.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'utils/jwt.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'utils/db.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'api/auth.php';

// Set HTTP headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

// Get token from header
$token = JWT::get_bearer_token();
if (!isset($token)) {
	echo Res::fail(401, 'Not authenticated');
	exit();
};

// If token is valid
if (!Auth::check_token($token)) {
	echo Res::fail(403, 'Invalid token');
	exit();
}

// Set some vars
$res = array();
$upload_dir = $cwd.'../imgs/';
if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);

// Generate random string
function random_string($len) {
	// If length is not set, set it to 10
	if (!isset($len)) $len = 10;
	$chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$str = '';
	for ($i = 0; $i < $len; $i++) $str .= $chars[mt_rand(0, strlen($chars)-1)];
	return $str;
}

class Upload {
	public function __construct($dir, $file) {
		// Set variables
		$this->dir = $dir;
		$this->file = $file;
	}
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
			return $this->upload_fail(405, 'Filesize limit is 90MB');
		}
		// If there is an error with the file
		if ($file_error > 0) {
			return $this->upload_fail(500, 'Error uploading, file may be corrupt');
		}
		// Move the file to the uploads directory
		$rand_name = random_string(5).'.'.$file_ext;
		$upload_name = $dir.$rand_name;
		if (move_uploaded_file($file_tmp_name, $upload_name)) {
			return $this->upload_success($dir.$rand_name);
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

// If POST is used
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (isset($_FILES['file'])) {
		// Create new upload object
		$upload = new Upload($upload_dir, $_FILES['file']);
		// Upload file
		$res = $upload->upload_file();
		// Return response
		echo $res;
		exit();
	}
	else {
		echo Res::fail(405, 'No file provided');
		exit();
	}
}
else {
	echo Res::fail(405, 'Method not allowed');
	exit();
}
