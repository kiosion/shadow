<?php

// Prevent direct access
if (!isset($include)) {
	header('Content-Type: application/json; charset=utf-8');
	include_once 'res.php';
	echo Res::fail(403, 'Unauthorized');
	exit();
}

// Function to handle POST requests and responses
function post($url, $data) {
	GLOBAL $_SHADOW_API_URL;
	// Set authorization as an appended field to the data array with a bearer token
	$data['authorization'] = 'token-blank-for-now';
	$url = $_SHADOW_API_URL.'/'.$url; // Set url to current server address, plus the given url. TODO: Read from config file
	// Set curl POST data
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
};
