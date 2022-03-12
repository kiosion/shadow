<?php

// Prevent direct access
if (!isset($include)) {
	header('Content-Type: application/json; charset=utf-8');
	include_once '../../../utils/res.php';
	echo Res::fail(403, 'Forbidden');
	exit();
}

class Auth {
	// Function to generate token from username and password
	public static function generate_token($conn, $username, $password, $type) {
		// If username or password is not set, return invalid res
		if (!isset($username) || !isset($password) || !isset($type) || !($type == 'login' || $type == 'api')) return Res::fail(401, 'Username, password, or type not provided');
		// Check if username and password are valid
		$auth = self::check_credentials($conn, $username, $password);
		if (!$auth) {
			return Res::fail(403, 'Username or password is incorrect');
		}
		else {
			$tz = new DateTimeZone('America/Halifax');
			$issuedAt = (new DateTimeImmutable("now", $tz))->setTimeZone($tz);
			if ($type == 'login') $expiration = $issuedAt->modify('+6 hours')->getTimestamp(); // Login token expiry is 6 hours
			else $expiration = $issuedAt->modify('+7 days')->getTimestamp(); // API token expiry is 7 days
			$serverName = "cdn.kio.dev";
			$payload = array(
				'nbf' => $issuedAt->getTimestamp(), // Not before
				'iat' => $issuedAt->getTimestamp(), // Issued at: time
				'exp' => $expiration, // Expire after
				'iss' => $serverName, // Issuer
				'type' => $type, // Type, either 'login' or 'api'
				'username' => $username, // Username
				'password' => $password // Password
			);
			$headers = array(
				'alg' => 'HS256', 
				'typ' => 'JWT'
			);
			$jwt = JWT::generate_jwt($headers, $payload);
			return Res::success(200, 'Token generated', $jwt);
		}
	}
	// Function to check if token is valid
	public static function check_token($token, $type) {
		if (!isset($token) || !isset($type)) return false;
		if (JWT::is_jwt_valid($token, $type)) return true;
		return false;
	}
	// Function to print payload of token
	public static function print_token($token) {
		if (!isset($token)) return false;
		$payload = JWT::get_info($token);
		if (!$payload) echo Res::fail(401, 'Invalid token');
		else return Res::success(200, 'Token payload decoded', $payload);
	}
	// Function to get user id from token
	public static function get_uid($token) {
		if (!isset($token)) return false;
		$payload = JWT::get_info($token);
		if (!$payload) echo Res::fail(401, 'Invalid token');
		else {
			// Query database for user id given username
			$sql = "SELECT * FROM users WHERE username = '$payload[username]'";
			$result = runQuery($sql);
			$row = fetchAssoc($result);
			return Res::success(200, 'User ID retrieved', $row['id']);
		}
	}
	// Function to check user credentials on login
	public static function check_credentials($conn, $username, $password) {
		// If username or password is not set, return false
		if (!isset($username) || !isset($password)) return false;
		// Check if username and password are valid
		$sql = "SELECT * FROM users WHERE (username = '".mysqli_real_escape_string($conn, $_POST['username'])."') AND (password = '".mysqli_real_escape_string($conn, $_POST['password'])."') LIMIT 1;";
		// Run query
		$result = runQuery($sql);
		if (numRows($result) < 1) {
			return false;
		}
		else {
			return true;
		}
	}
}
