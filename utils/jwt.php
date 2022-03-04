<?php

// Include files
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'dotenv.php';

class JWT {
	// Get JWT secret from .env file
	private static function get_secret() :string {
		$env = new DotEnv(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'.env');
		$env->load();
		return getenv('JWT_SECRET');
	}
	// Generate a JWT, TOOD: change secret
	public static function generate_jwt($headers, $payload) {
		$headers_encoded = self::base64url_encode(json_encode($headers));
		
		$payload_encoded = self::base64url_encode(json_encode($payload));
		
		$signature = hash_hmac('SHA256', "$headers_encoded.$payload_encoded", self::get_secret(), true);
		$signature_encoded = self::base64url_encode($signature);
		
		$jwt = "$headers_encoded.$payload_encoded.$signature_encoded";
		
		return $jwt;
	}
	// Check if JWT is valid
	public static function is_jwt_valid($jwt) {
		// split the jwt
		$tokenParts = explode('.', $jwt);
		$header = base64_decode($tokenParts[0]);
		$payload = base64_decode($tokenParts[1]);
		$signature_provided = $tokenParts[2];

		// check the expiration time - note this will cause an error if there is no 'exp' claim in the jwt
		$expiration = json_decode($payload)->exp;
		$is_token_expired = ($expiration - time()) < 0;

		// build a signature based on the header and payload using the secret
		$base64_url_header = self::base64url_encode($header);
		$base64_url_payload = self::base64url_encode($payload);
		$signature = hash_hmac('SHA256', $base64_url_header . "." . $base64_url_payload, self::get_secret(), true);
		$base64_url_signature = self::base64url_encode($signature);

		// verify it matches the signature provided in the jwt
		$is_signature_valid = ($base64_url_signature === $signature_provided);
		
		if ($is_token_expired || !$is_signature_valid) {
			return FALSE;
		} else {
			return TRUE;
		}
	}
	// B64 encode a string
	private static function base64url_encode($data) {
		return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
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
