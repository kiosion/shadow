<?php

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
