<?php

// Require files
$cwd = dirname(__FILE__).DIRECTORY_SEPARATOR;
require_once $cwd.'dotenv.php';
// Load .env file
$env = new Dotenv(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'.env');
$env->load();
// Set vars for db
$db_host = getenv('DB_HOST');
$db_name = getenv('DB_NAME');
$db_user = getenv('DB_USER');
$db_pass = getenv('DB_PASS');

// New mysql connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}

// Functions
function runQuery($sql) {
	global $conn;
	$result = $conn->query($sql);
	if (!$result) {
		die("Error: " . $conn->error);
	}
	return $result;
}
function fetchAssoc($result) {
	return $result->fetch_assoc();
}
function numRows($result) {
	return $result->num_rows;
}
function closeConn() {
	global $conn;
	$conn->close();
}
