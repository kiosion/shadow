<?php

// Prevent direct access
if (!isset($include)) {
	echo Res::fail(403, 'Forbidden');
	exit();
}

class Upload {
	public function __construct($file, $uid) {
		$this->dir = '../uploads/users/'.$uid.'/'; // Upload directory
		$this->file = $file; // File object to uplaod
		$this->uid = $uid; // User ID initiating upload
	}
	// Generate random string
	private static function random_string($len) {
		GLOBAL $DB_PREFIX;
		if (!isset($len)) $len = 10;
		$chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$str = '';
		for ($i = 0; $i < $len; $i++) $str .= $chars[mt_rand(0, strlen($chars)-1)];
		// Check name isn't in use
		$table = $DB_PREFIX . 'files';
		$sql = "SELECT * FROM $table WHERE BINARY ul_name = '$str'";
		if (numRows(runQuery($sql)) > 0) {
			$str = self::random_string($len);
		}
		return $str;
	}
	// Upload file, add to db if successful
	public function upload_file() {
		GLOBAL $DB_PREFIX;
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
		// If directory doesn't exist, create it
		if (!file_exists($dir)) mkdir($dir, 0777, true);
		// Move the file to the uploads directory
		$ul_name = $this->random_string(6);
		$og_name = $file_name;
		$dir_name = $dir.$ul_name.'.'.$file_ext;
		if (move_uploaded_file($file_tmp_name, $dir_name)) {
			// Add file to db
			$uid = $this->uid;
			$tz = new DateTimeZone('America/Halifax');
			$time = ((new DateTimeImmutable("now", $tz))->setTimezone($tz))->getTimestamp();
			// Format: UID, filename, timestamp
			$table = $DB_PREFIX . 'files';
			$sql = "INSERT INTO $table (uid, og_name, ul_name, ext, time, size, vis) VALUES ('$uid', '$og_name', '$ul_name', '$file_ext', '$time', '$file_size', 0)";
			// Run query
			if(runQuery($sql)) {
				return $this->upload_success($ul_name.'.'.$file_ext);
			}
			else return $this->upload_fail(500, 'Error uploading, database error'); // TODO: Also delete file so we don't have 'ghost' files left over on fail
		}
		else {
			return $this->upload_fail(500, 'Error uploading, failed on move_uploaded_file()');
		}
	}
	private static function upload_success($path) {
		return Res::success(200, 'Upload successful', $path);
	}
	private static function upload_fail($code, $msg) {
		return Res::fail($code, $msg);
	}
}
