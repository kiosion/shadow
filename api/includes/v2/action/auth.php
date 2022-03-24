<?php

// Prevent direct access
if (!isset($include)) {
	echo Res::fail(403, 'Forbidden');
	exit();
}

switch ($path_arr[1]) {
	// Generate token
	case 'request-token':
		if (!isset($_POST['username']) || !isset($_POST['password']) || !isset($_POST['type'])) {
			echo Res::fail(401, 'Username, password, or type not provided');
			break;
		}
		echo Auth::generate_token($conn, $_POST['username'], $_POST['password'], $_POST['type']);
		break;
	// Check token
	case 'check-token':
		if (!isset($_POST['token']) || !isset($_POST['type'])) {
			echo Res::fail(401, 'Token or type not provided');
			break;
		}
		if (!($_POST['type'] == 'api' || $_POST['type'] == 'login')) {
			echo Res::fail(401, 'Invalid token type');
			break;
		}
		if (Auth::check_token($_POST['token'], $_POST['type'])) {
			echo Res::success(200, 'Token valid', null);
		}
		else {
			echo Res::success(200, 'Token invalid', null);
		}
		break;
	// Check user credentials on login
	case 'check-creds':
		if (!isset($_POST['username']) || !isset($_POST['password'])) {
			echo Res::fail(401, 'Username or password not provided');
			break;
		}
		if (Auth::check_credentials($conn, $_POST['username'], $_POST['password'])) {
			echo Res::success(200, 'Credentials valid', null);
		}
		else {
			echo Res::success(200, 'Credentials invalid', null);
		}
		break;
	default:
		echo Res::fail(400, 'Invalid action '.$path_arr[4].' provided');
		break;
}
