<?php

// Include files
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'utils/res.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'utils/auth.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'utils/jwt.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'utils/db.php';

// Set HTTP headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (isset($_POST['action'])) {
		$action = $_POST['action'];
		switch ($action) {
			// Generate token
			case 'generate_token':
				echo Auth::generate_token($conn, $_POST['username'], $_POST['password']);
				break;
			// Check token
			case 'check_token':
				if (!isset($_POST['token'])) {
					echo Res::fail(401, 'Token not provided');
					break;
				}
				if (Auth::check_token($_POST['token'])) {
					echo Res::success(200, 'Token valid', null);
				}
				else {
					echo Res::success(200, 'Token invalid', null);
				}
				break;
			// Print token payload
			case 'print_token':
				if (!isset($_POST['token'])) {
					echo Res::fail(401, 'Token not provided');
					break;
				}
				echo Auth::print_token($_POST['token']);
				break;
			// Get user id from token
			case 'get_user_id':
				if (!isset($_POST['token'])) {
					echo Res::fail(401, 'Token not provided');
					break;
				}
				echo Auth::get_user_id($conn, $_POST['token']);
				break;
			// Check user credentials on login
			case 'check_credentials':
				break;
			// Not a valid action
			default:
				echo Res::fail(400, 'Invalid action');
				break;
		}
	}
}
