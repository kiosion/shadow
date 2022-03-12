<?php

// Prevent direct access
if (!isset($include)) {
	header('Content-Type: application/json; charset=utf-8');
	include_once 'res.php';
	echo Res::fail(403, 'Unauthorized');
	exit();
}

// Include files
require_once 'dotenv.php';

// Load .env file
$env = new DotEnv('/utils/.env');
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
