<?php
// Require files
require_once 'jwt.php';
require_once 'res.php';
require_once 'db.php';

// Set HTTP headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

class Auth {
	// Function to generate token from username and password
	public static function generate_token($conn, $username, $password) {
		// If username or password is not set, return false
		if (!isset($username) || !isset($password)) echo Res::fail(401, 'Username or password not provided');
		// Check if username and password are valid
		$sql = "SELECT * FROM user WHERE username = '".mysqli_real_escape_string($conn, $_POST['username'])."' AND password = '".mysqli_real_escape_string($conn, $_POST['password'])."' LIMIT 1";
		// Run query
		$result = runQuery($sql);
		if (numRows($result) < 1) {
			echo Res::fail(403, 'Username or password is incorrect');
		}
		else {
			$row = fetchAssoc($result);
			$username = $row['username'];
			$headers = array('alg' => 'HS256', 'typ' => 'JWT');
			$payload = array('username' => $username, 'exp' => (time() + 120)); // Valid for 2 minutes, 120 seconds
			$jwt = JWT::generate_jwt($headers, $payload);
			echo Res::success(200, 'Success', $jwt);
		}
	}
	// Function to check if token is valid
	public static function check_token($token) {
		if (!isset($token)) return false;
		if (JWT::is_jwt_valid($token)) return true;
		return false;
	}
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if ($_POST['action'] == 'generate_token') {
		// Use function
		Auth::generate_token($conn, $_POST['username'], $_POST['password']);
	}
	else if ($_POST['action'] == 'check_token') {
		// Use function
		if (Auth::check_token($_POST['token'])) {
			echo Res::success(200, 'Token valid', null);
		}
		else {
			echo Res::fail(200, 'Token invalid');
		}
	}
	else {
		Res::fail(400, 'Method not supported');
	}
}
