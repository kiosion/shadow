<?php

// Prevent direct access
if (!isset($include)) {
	header('Content-Type: application/json; charset=utf-8');
	include_once 'res.php';
	echo Res::fail(403, 'Forbidden');
	exit();
}

$include = true;

// Include files
require_once 'dotenv.php';

class JWT {
	// Get JWT secret from .env file
	private static function get_secret() {
		$env = new DotEnv('../.env');
		$env->load();
		return getenv('JWT_SECRET');
	}
	// B64 encode a string
	private static function base64url_encode($data) {
		return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
	}
	// Generate a JWT
	public static function generate_jwt($headers, $payload) {
		$headers_encoded = self::base64url_encode(json_encode($headers));
		$payload_encoded = self::base64url_encode(json_encode($payload));
		$signature = hash_hmac('SHA256', "$headers_encoded.$payload_encoded", self::get_secret(), true);
		$sig_encoded = self::base64url_encode($signature);
		$jwt = "$headers_encoded.$payload_encoded.$sig_encoded";
		return $jwt;
	}
	// Check if JWT is valid
	public static function is_jwt_valid($jwt, $type) {
		// Split into parts
		$tokenParts = explode('.', $jwt);
		$header = base64_decode($tokenParts[0]);
		$payload = base64_decode($tokenParts[1]);
		$sig_provided = $tokenParts[2];
		
		// Check the provided type against the type in the payload
		if (json_decode($payload)->type != $type) return false;

		// Check the expiration time
		$expiration = json_decode($payload)->exp;
		// Current time with America/Halifax timezone
		$curr_time = (new DateTime('now', new DateTimeZone('America/Halifax')))->setTimeZone(new DateTimeZone('America/Halifax'))->getTimeStamp();
		$is_token_expired = ($expiration - $curr_time) < 0;

		// Build a signature based on the header and payload using the secret
		$base64_url_header = self::base64url_encode($header);
		$base64_url_payload = self::base64url_encode($payload);
		$signature = hash_hmac('SHA256', $base64_url_header . "." . $base64_url_payload, self::get_secret(), true);
		$base64_url_sig = self::base64url_encode($signature);

		// Verify it matches the signature provided in the jwt
		$is_sig_valid = ($base64_url_sig === $sig_provided);
		
		if (!$is_sig_valid) {
			return false;
		} 
		else if ($is_token_expired) {
			return false;
		}
		else {
			return true;
		}
	}
	// Get info from JWT
	public static function get_info($jwt) {
		// Split the token
		$tokenParts = explode('.', $jwt);
		$payload = base64_decode($tokenParts[1]);

		//Check all the payload info is present
		if (!isset(json_decode($payload)->nbf) || !isset(json_decode($payload)->iat) || !isset(json_decode($payload)->exp) || !isset(json_decode($payload)->iss) || !isset(json_decode($payload)->username)) return false;

		// Decode elements
		$expiration = json_decode($payload)->exp;
		$is_token_expired = ($expiration - time()) < 0;

		// Convert the timestamps to DateTimeImmutable objects, using the America/Halifax timezone
		$tz = new DateTimeZone('America/Halifax');
		$nbf_date = (new DateTimeImmutable('@'.json_decode($payload)->nbf, $tz))->setTimeZone($tz);
		$iat_date = (new DateTimeImmutable('@'.json_decode($payload)->iat, $tz))->setTimeZone($tz);
		$exp_date = (new DateTimeImmutable('@'.$expiration, $tz))->setTimeZone($tz);

		// Return info as an array
		return array(
			'nbf' => $nbf_date->format('d.m.Y, H:i:s').' UTC-4',
			'iat' => $iat_date->format('d.m.Y, H:i:s').' UTC-4',
			'exp' => $exp_date->format('d.m.Y, H:i:s').' UTC-4',
			'iss' => json_decode($payload)->iss,
			'type' => json_decode($payload)->type,
			'username' => json_decode($payload)->username,
			'expired' => $is_token_expired
		);
	}
	// Get payload from header
	private static function get_authorization_header(){
		$headers = null;
		if (isset($_SERVER['Authorization'])) {
			$headers = trim($_SERVER["Authorization"]);
		} else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
			$headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
		} else if (function_exists('apache_request_headers')) {
			$requestHeaders = apache_request_headers();
			// Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
			$requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
			//print_r($requestHeaders);
			if (isset($requestHeaders['Authorization'])) {
				$headers = trim($requestHeaders['Authorization']);
			}
		}
		return $headers;
	}
	// Get JWT from header
	public static function get_bearer_token() {
		$headers = self::get_authorization_header();
		// HEADER: Get the access token from the header
		if (!empty($headers)) {
			if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
				return $matches[1];
			}
		}
		return null;
	}
}
