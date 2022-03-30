<?php

// Prevent direct access
if (!isset($include)) {
	echo Res::fail(403, 'Forbidden');
	exit();
}

switch ($path_arr[1]) {
	case 'get-uid':
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
	case 'get-info':
		if (!isset($_POST['filename']) && !isset($_POST['file_id'])) {
			echo Res::fail(401, 'Filename or ID not provided');
			break;
		}
		$filename = $_POST['filename'];
		// Check for file extension
		$ext = pathinfo($_POST['filename'], PATHINFO_EXTENSION);
		if (!empty($ext)) {
			// Remove file extension
			$filename = substr($filename, 0, -strlen($ext) - 1);
		}
		echo File::get_info($filename);
		break;
	case 'get-file':
		// TODO: Handle serving files, images, audio, video, text, and add param for resource stream response
		if (empty ($path_arr[2])) {
			echo Res::fail(401, 'Filename not provided');
			break;
		}
		$filename = $path_arr[2];
		$res = File::get_info($filename);
		$res_decoded = json_decode($res);
		if ($res_decoded->status != 'success') {
			echo Res::fail(404, 'File "'.$filename.'" not found');
			exit();
		}
		$ext = $res_decoded->data->ext;
		$uid = $res_decoded->data->uid;
		$filepath = '../uploads/users/'.$uid.'/'.$filename.'.'.$ext;
		$mimetype = File::get_mimetype($ext);
		// Set token from auth header
		$token = $_SERVER['HTTP_AUTHORIZATION'];
		
		// Auth
		switch ($res_decoded->data->vis) {
			// Public
			case '0':
				break;
			// Hidden (need any login token)
			case '1':
				if (!isset($token)) {
					header('Content-Type: application/json');
					echo Res::fail(401, 'Token not provided');
					exit();
				}
				if (!Auth::check_token($token, 'login')) {
					header('Content-Type: application/json');
					echo Res::fail(401, 'Invalid token');
					exit();
				}
				break;
			// Private (owner only)
			case '2':
				if (empty($token)) {
					header('Content-Type: application/json');
					echo Res::fail(401, 'Token not provided');
					exit();
				}
				if (!Auth::check_token($token, 'login')) {
					header('Content-Type: application/json');
					echo Res::fail(401, 'Invalid token');
					exit();
				}
				// Get token uid
				$token_uid = json_decode(Auth::get_uid($token));
				if ($token_uid->status != 'success') {
					header('Content-Type: application/json');
					echo Res::fail(500, 'Internal server error');
					exit();
				}
				if ($token_uid->data != $uid) {
					header('Content-Type: application/json');
					echo Res::fail(401, 'Invalid token');
					exit();
				}
				break;
		}

		// Serve file
		header('Content-Type: '.$mimetype);
		header('Content-Length: '.filesize($filepath));
		$fopen = fopen($filepath, 'r');
		fpassthru($fopen);

		break;
	case 'set-visibility':
		if (!isset($_POST['fileID']) || !isset($_POST['token']) || !isset($_POST['vis'])) {
			echo Res::fail(401, 'FileID, token, or visibility not provided');
			break;
		}
		// Call set_visibility with fileID and token
		echo File::set_visibility($_POST['fileID'], $_POST['token'], $_POST['vis']);
		break;
	// Not a valid action
	default:
		echo Res::fail(400, 'Invalid action '.$path_arr[4].' provided');
		break;
}
