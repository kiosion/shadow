<?php

// Prevent direct access
if (!isset($include)) {
	header('Content-Type: application/json; charset=utf-8');
	include_once 'res.php';
	echo Res::fail(403, 'Unauthorized');
	exit();
}

// Verify login token
function verify_login_token($app_route) {
	if (isset($_COOKIE['shadow_login_token'])) {
		$token = $_COOKIE['shadow_login_token'];
		if (empty($token)) {
			return array(
				'status' => 'invalid',
				'route' => 'login'
			);
		}
		// Check if the token is valid via POST req to API auth endpoint
		$arr = array("action"=>"check_token","token"=>"$token","type"=>"login");
		$res = post('http://localhost/api/v1/auth.php', $arr);
		if (json_decode($res)->msg == 'Token valid') {
			// TODO: Check that the token is valid for the given app route
			return array(
				'status' => 'valid',
				'route' => $app_route
			);
		}
		else {
			return array(
				'status' => 'invalid',
				'route' => 'login'
			);
		}
	}
	else {
		return array(
			'status' => 'invalid',
			'route' => 'login'
		);
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
		// Check if path ends with '/raw' or '/download'
		if (strstr($path, '/raw')) {
			$filename = substr($filename, 0, - 4);
		}
		if (strstr($path, '/download')) {
			$filename = substr($filename, 0, - 9);
		}
		// Check for anything else after filename
		if (substr($filename, -1) == '/') {
			$filename = substr($filename, 0, - 1);
		}
		if (strstr($filename, '/')) { 
			$filename = substr($filename, 0, strpos($filename, '/'));
		}
		// Check for extension at end of filename
		$ext = pathinfo($filename, PATHINFO_EXTENSION);
		if ($ext != '') {
			// Remove extension from end of filename
			$filename = substr($filename, 0, - strlen($ext) - 1);
		}
		$arr = array("action"=>"get_uid","filename"=>"$filename");
		$res = post('http://localhost/api/v1/file.php', $arr);
		$res_decoded = json_decode($res);
		if ($res_decoded->status == 'success') {
			$uid = $res_decoded->data->uid;
			$ul_name = $res_decoded->data->ul_name;
			$og_name = $res_decoded->data->og_name;
			$ext = $res_decoded->data->ext;
			// Check if URL contains trailing '/raw'
			if (strstr($path, '/raw')) {
				return array(
					'app_route' => 'raw',
					'ext' => $ext,
					'filename' => $ul_name.'.'.$ext,
					'uid' => $uid,
				);
			}
			// Check if URL contains trailing '/download'
			else if (strstr($path, '/download')) {
				return array(
					'app_route' => 'download',
					'ext' => $ext,
					'og_name' => $og_name,
					'filename' => $ul_name.'.'.$ext,
					'uid' => $uid,
				);
			}
			// Else if app_route is normal
			else {
				return array(
					'app_route' => 'file',
					'title' => 'Shadow - '.$ul_name,
					'og_name' => $og_name,
					'ext' => $ext,
					'filename' => $ul_name.'.'.$ext,
					'uid' => $uid,
				);
			}
		}
		else return array('app_route' => '404');
	}
	else if (strstr($path, '/admin')) {
		return array(
			'app_route' => 'admin',
			'title' => 'Shadow - Admin',
		);
	}
	// // Check if URL contains anything after '/'
	// else if (substr($path, 1) != '') {
	// 	header('Location: /');
	// }
}

// Get filetype from ext
function get_mimetype($ext) {
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
