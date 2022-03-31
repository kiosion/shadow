<?php

// Prevent direct file access
if (!isset($include)) {
	require_once '../includes/utils/res.php';
	header('Content-Type: application/json');
	echo Res::fail(403, 'Forbidden');
	exit();
}

// Handle URL paths
function handle_url_paths($url) {
	// Get request URL
	// Explode path into array, delimiter is /
	$path_arr = explode('/', $url['path']);
	$key = array_search('install', $path_arr);
	$path_arr = array_slice($path_arr, $key + 1);
	// Check if object is empty
	if (!isset($path_arr[0]) || empty($path_arr[0])) { 
		return 0;
		exit();
	}
	else return $path_arr[0];
}

// Validate db credentials
function validate_db_creds($db_host, $db_user, $db_pass, $db_name, $db_port) {
	// TODO
	if (empty($db_host) || empty($db_user) || empty($db_pass) || empty($db_name) || empty($db_port)) {
		return 'Error: Please fill in all the fields.';
		exit();
	}
	// Test connection
	try {
		$conn = new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);
		if ($conn->connect_error) {
			// Return error
			return 'Error: ' . $conn->connect_error;
			exit();
		}
		else return 'Connection successful.';
	} catch (Exception $e) {
		return 'Error: ' . $e->getMessage();
	}
}

// Create tables
function create_table() {
	
}
