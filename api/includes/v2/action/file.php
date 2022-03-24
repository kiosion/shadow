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
		if ((!isset($_POST['filename']) && !isset($_POST['file_id'])) || !isset($_POST['token'])) {
			echo Res::fail(401, 'Filename, ID, or token not provided');
			break;
		}
		// Check for file extension
		$ext = pathinfo($_POST['filename'], PATHINFO_EXTENSION);
		if ($ext != '') {
			// Remove file extension
			$filename = substr($_POST['filename'], 0, -strlen($ext) - 1);
		}
		echo File::get_info($filename, $token);
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
