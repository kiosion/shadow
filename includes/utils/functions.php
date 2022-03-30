<?php

// Prevent direct access
if (!isset($include)) {
	header('Content-Type: application/json; charset=utf-8');
	include_once 'res.php';
	echo Res::fail(403, 'Unauthorized');
	exit();
}

// Verify login token
function verify_login_token($app_route, $token) {
	if (empty($token)) {
		return array(
			'status' => 'invalid',
			'route' => 'login',
			'redir' => $app_route
		);
	}
	// Check if the token is valid via POST req to API auth endpoint
	$arr = array("token"=>"$token");
	$res = post('api/v2/user/get-payload/', $arr);
	if (json_decode($res)->data->expired == false && json_decode($res)->data->type == 'login') {
		// TODO: Check that the token is valid for the given app route
		$res2 = post('api/v2/user/get-role/', $arr);
		$res3 = post('api/v2/user/get-uid/', $arr);
		return array(
			'status' => 'valid',
			'token' => $token,
			'role' => json_decode($res2)->data,
			'username' => json_decode($res)->data->username,
			'uid' => json_decode($res3)->data,
		);
	}
	else {
		return array(
			'status' => 'invalid',
			'route' => 'login',
			'redir' => $app_route
		);
	}
}

// Verify user access to file
function verify_access($file, $token) {
	if (empty($token)) {
		return array(
			'status' => 'invalid',
			'route' => 'login'
		);
	}
	// Check if the token is valid via POST req to API auth endpoint
	$arr = array("token"=>"$token");
	$res = post('api/v2/user/get-payload/', $arr);
	if (json_decode($res)->data->expired == false && json_decode($res)->data->type == 'login') {
		$arr2 = array("token"=>"$token");
		$res2 = post('api/v2/user/get-uid/', $arr2);
		$uid = json_decode($res2)->data;
		// Check if uid is owner of file
		$arr3 = array("token"=>"$token","filename"=>"$file");
		$res3 = post('api/v2/file/get-info/', $arr3);
		if (json_decode($res3)->data->uid == $uid) {
			return array(
				'status' => 'valid',
				'file' => $file,
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
	// Explode path into array, delimiter is /
	$path_arr = explode('/', $url['path']);
	// Check if path is empty
	if (empty($path_arr[1])) return header('Location: /home');
	// Else, switch statement to check if path is valid
	switch ($path_arr[1]) {
		case 'login':
			return array(
				'app_route' => 'login',
				'title' => 'Login',
			);
			break;
		case 'home':
			return array(
				'app_route' => 'index',
				'title' => 'Home',
			);
			break;
		case 'upload':
			return array(
				'app_route' => 'upload',
				'title' => 'Upload',
			);
			break;
		case 'file':
			// Check if file is present after '/file/', if not, return error
			$filename = $path_arr[2];
			if (empty($filename)) return array('app_route' => '404');
			// Check for extension at end of filename
			$ext = pathinfo($filename, PATHINFO_EXTENSION);
			// Remove extension from end of filename
			if (!empty($ext)) $filename = substr($filename, 0, - strlen($ext) - 1);
			$arr = array("filename"=>"$filename");
			$res = post('api/v2/file/get-uid/', $arr);
			$res_decoded = json_decode($res);
			if ($res_decoded->status == 'success') {
				$uid = $res_decoded->data->uid;
				$ul_name = $filename;
				$og_name = $res_decoded->data->og_name;
				$ext = $res_decoded->data->ext;
				// Switch statement for raw/download/view
				switch ($path_arr[3]) {
					// Check if URL contains trailing '/raw'
					case 'raw':
						return array(
							'app_route' => 'raw',
							'title' => '',
							'filename' => $ul_name.'.'.$ext,
							'ext' => $ext,
							'uid' => $uid,
						);
						break;
					// Check if URL contains trailing '/download'
					case 'download':
						return array(
							'app_route' => 'download',
							'title' => '',
							'filename' => $ul_name.'.'.$ext,
							'og_name' => $og_name,
							'ext' => $ext,
							'uid' => $uid,
						);
						break;
					// Else if viewing file
					default:
						return array(
							'app_route' => 'file',
							'title' => $ul_name,
							'filename' => $ul_name.'.'.$ext,
							'og_name' => $og_name,
							'ext' => $ext,
							'uid' => $uid,
						);
						break;
						
				}
			}
			else return array(
				'app_route' => '404',
				'title' => 'Error 404',
			);
			break;
		case 'settings':
			return array(
				'app_route' => 'settings',
				'title' => 'Settings',
			);
			break;
		case 'account':
			return array(
				'app_route' => 'account',
				'title' => 'Account',
			);
			break;
		case 'admin':
			return array(
				'app_route' => 'admin',
				'title' => 'Admin',
			);
			break;
		default:
			return array(
				'app_route' => '404',
				'title' => 'Error 404',
			);
			break;
	}
}

// Get image from api using cURL, encode, and return
function get_file($filename) {
	GLOBAL $_SHADOW_API_URL;

	// Get image extension using file get-info
	$res = post('api/v2/file/get-info/', array('filename'=>$filename));
	$res_decoded = json_decode($res);
	if ($res_decoded->status == 'success') {
		$ext = $res_decoded->data->ext;
	}
	else {
		return false;
	}

	// Get mime type
	$mimetype = get_mimetype($ext);

	// cURL request to get file
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $_SHADOW_API_URL.'/api/v2/file/get-file/'.$filename);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	// Set authorization header
	$headers = array(
		'Authorization: '.$_COOKIE['shadow_login_token'],
	);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	// Get result and close cURL
	$data = curl_exec($ch);
	curl_close($ch);
	return 'data:image/'.$ext.';base64,'.base64_encode($data);

	// switch($mimetype) {
	// 	// Case for contains 'image'
	// 	case stristr($mimetype, 'image'):
	// 		// Encode and return result as b64 encoded string
	// 		return 'data:image/'.$ext.';base64,'.base64_encode($data);
	// 		break;
	// 	// Case for contains 'video'
	// 	case stristr($mimetype, 'video'):
	// 		break;
	// 	// Case for contains 'audio'
	// 	case stristr($mimetype, 'audio'):
	// 		break;
	// }
}

// Get audio from api using cURL, encode, and return
function get_audio($filename) {
	// TODO:
}

// Get text-file from api using cURL, encode, and return
function get_text($filename) {

}

// Get other file from api using cURL, encode, and return
function get_other($filename) {

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
