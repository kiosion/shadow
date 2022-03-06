<?php

// Verify login token
function verify_login_token() {
	if (isset($_COOKIE['shadow_login_token'])) {
		$token = $_COOKIE['shadow_login_token'];
		// Check if the token is valid via POST req to API auth endpoint
		$arr = array("action"=>"check_token","token"=>"$token","type"=>"login");
		$res = post('http://localhost/api/v1/auth.php', $arr);
		if (json_decode($res)->msg == 'Token valid') {
			return array(
				'view' => 'index',
				'title' => 'Shadow - Index',
			);
		}
	}
}

// Handle URL paths
function handle_url_paths($url) {
	// Check if URL contains file
	$path = $url['path'];
	// Check if path contains '/file/'
	if (strstr($path, '/file/')) {
		// Check if file is present after '/file/'
		$filename = substr($path, strrpos($path, 'file/') + 5);
		if ($filename == '') header('Location: /');
		// Check if path ends with '/raw'
		if (strstr($path, '/raw')) {
			$filename = substr($filename, 0, - 4);
		}
		// Check for anything else after filename
		if (substr($filename, -1) == '/') {
			$filename = substr($filename, 0, - 1);
		}
		if (strstr($filename, '/')) { 
			$filename = substr($filename, 0, strpos($filename, '/'));
		}
		// Get uid from filename using function
		$arr = array("action"=>"get_uid","filename"=>"$filename");
		$res = post('http://localhost/api/v1/file.php', $arr);
		$res_decoded = json_decode($res);
		if ($res_decoded->status == 'success') {
			$uid = $res_decoded->data;
			// Check if URL contains trailing '/raw'
			if (strstr($path, '/raw')) {
				return array(
					'view' => 'raw',
					'filename' => $filename,
					'uid' => $uid,
				);
			}
			else {
				return array(
					'view' => 'file',
					'title' => 'Shadow - '.$filename,
					'filename' => $filename,
					'uid' => $uid,
				);
			}
		}
		else return array('view' => '404');
	}
	// Check if URL contains anything after '/'
	else if (substr($path, 1) != '') {
		header('Location: /');
	}
}

// Get filetype from ext
function get_contenttype($ext) {
	$ext = strtolower($ext);
	// Set content type
	switch ($ext) {
		case 'txt':
			return 'text/plain';
			break;
		case 'html':
			return 'text/plain';
			break;
		case 'css':
			return 'text/plain';
			break;
		case 'js':
			return 'text/plain';
			break;
		case 'php':
			return 'text/plain';
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
