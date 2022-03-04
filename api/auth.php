<?php

// Require files
$cwd = dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR;
require_once $cwd.'utils/res.php';
require_once $cwd.'utils/jwt.php';
require_once $cwd.'utils/db.php';

// Set HTTP headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

// To be turned POST-based eventually
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
			$issuedAt = new DateTimeImmutable();
			$expiration = $issuedAt->modify('+2 minutes')->getTimestamp();
			$serverName = "cdn.kio.dev";
			$payload = array(
				'iat' => $issuedAt->getTimestamp(), // Issued at: time
				'iss' => $serverName, // Issuer
				'nbf' => $issuedAt->getTimestamp(), // Not before
				'exp' => $expiration, // Expire after: (2 minutes)
				'username' => $username // Username
			);
			$headers = array(
				'alg' => 'HS256', 
				'typ' => 'JWT'
			);
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
	if (isset($_POST['action'])) {
		$action = $_POST['action'];
		switch ($action) {
			case 'generate_token':
				Auth::generate_token($conn, $_POST['username'], $_POST['password']);
				break;
			case 'check_token':
				if (Auth::check_token($_POST['token'])) {
					echo Res::success(200, 'Token valid', null);
				}
				else {
					echo Res::success(200, 'Token invalid', null);
				}
				break;
			case 'check_credentials':
				break;
			default:
				echo Res::fail(400, 'Invalid action');
				break;
		}
	}
	else {
		echo Res::fail(400, 'No action provided');
	}
}
