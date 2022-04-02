<?php

// Prevent direct file access
if (!isset($include)) {
	require_once '../app/utils/res.php';
	header('Content-Type: application/json');
	echo Res::fail(403, 'Forbidden');
	exit();
}

// Function to read from ENV file
function readEnv(string $loc) : array {
	$lines = file(realpath(dirname(__FILE__)).'/..'.$loc, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	$res = array();
	foreach ($lines as $line) {
		if (strpos($line, '#') === 0) continue;
		list($name, $value) = explode('=', $line, 2);
		$name = trim($name);
		$value = trim($value);
		if (!array_key_exists($name, $res)) {
			$res[$name] = $value;
		}
	}
	return $res;
}

// Handle URL paths
function handle_url_paths($url, $return = 'path') {
	// Explode path into array, delimiter is /
	$path_arr = explode('/', $url['path']);
	$key = array_search('install', $path_arr);
	$path_arr = array_slice($path_arr, $key + 1);
	// Check if object is empty
	if (!isset($path_arr[0]) || empty($path_arr[0])) { 
		return 0;
		exit();
	}
	switch ($return) {
		case 'step':
			return $path_arr[0];
			break;
		case 'path':
			return $path_arr;
			break;
	}
}

// Validate db credentials
function validate_db_creds($db_host, $db_user, $db_pass, $db_port) : string {
	if (empty($db_host) || empty($db_user) || empty($db_pass) || empty($db_port)) {
		return 'Error: Empty field provided.';
	}
	// Test connection
	try {
		$conn = new mysqli($db_host, $db_user, $db_pass, null, $db_port);
		if ($conn->connect_error) {
			// Return error
			return 'Error: ' . $conn->connect_error;
		}
		else {
			$test = $conn->query('SHOW DATABASES');
			if ($test) {
				return 'Success: Connection established.';
			}
			else {
				return 'Error: ' . $conn->error;
			}
		}
	} catch (Exception $e) {
		return 'Error: ' . $e->getMessage();
	}
}

// Create .env file
function create_env(string $loc, array $env_arr) : string {
	if (empty($env_arr)) {
		return 'Error: Empty array provided.';
	}
	$loc =  realpath(dirname(__FILE__)).'/..'.$loc;
	try {
		// If file exists
		if (file_exists($loc)) {
			// Delete file
			file_put_contents($loc, '');
		}
		// Create .env file
		$env_file = fopen($loc, 'w');
		// Write to file
		foreach ($env_arr as $key => $value) {
			fwrite($env_file, $key . '=' . $value . PHP_EOL);
		}
		// Generate random phrase for JWT
		fwrite($env_file, 'JWT_SECRET=' . bin2hex(random_bytes(32)) . PHP_EOL);
		fclose($env_file);
		// Return success
		return 'Success: '.$loc.' created.';
	} catch (Exception $e) {
		return 'Error: ' . $e->getMessage();
	}
}

// Append to .env file
function append_env(string $loc, array $env_arr) : string {
	if (empty($env_arr)) {
		return 'Error: Empty array provided.';
	}
	$loc =  realpath(dirname(__FILE__)).'/..'.$loc;
	try {
		// Open .env file
		$env_file = fopen($loc, 'a');
		// Write to file
		foreach ($env_arr as $key => $value) {
			fwrite($env_file, $key . '=' . $value . PHP_EOL);
		}
		fclose($env_file);
		// Return success
		return 'Success: '.$loc.' updated.';
	} catch (Exception $e) {
		return 'Error: ' . $e->getMessage();
	}
}

// Create .htaccess files
function write_file(string $loc, string $src) : array {
	//$src = realpath(dirname(__FILE__)).'/..'.$src;
	$contents = "";
	if ($file = fopen($src, 'r')) {
		while (!feof($file)) {
			$line = fgets($file);
			if (!empty($line)) $contents .= trim($line)."\r\n";
		}
		fclose($file);
	}
	else {
		return array(false, '<div class="alert alert-danger" role="alert">Resource file \''.$src.'\' could not be read.</div>');
	}
	if (file_exists($loc)) {
		if (file_get_contents($loc) != $contents) {
			try {
				file_put_contents($loc, $contents);
				if (file_get_contents($loc) != $contents) {
					return array(false, '<div class="alert alert-danger" role="alert"><strong>'.$loc.'</strong> could not be updated.</div>');
				}
				return array(true, '<div class="alert alert-success" role="alert"><strong>'.$loc.'</strong> was updated.</div>');
			} catch (Exception $e) {
				return array(false, '<div class="alert alert-danger" role="alert"><strong>'.$loc.'</strong> could not be updated.</div>');
			}
		}
		else {
			return array(true, '<div class="alert alert-success" role="alert"><strong>'.$loc.'</strong> is present.</div>');
		}
	}
	else {
		try {
			file_put_contents($loc, $contents);
			return array(true, '<div class="alert alert-success" role="alert"><strong>'.$loc.'</strong> was created and updated.</div>');
		} catch (Exception $e) {
			return array(false, '<div class="alert alert-danger" role="alert"><strong>'.$loc.'</strong> could not be created.</div>');
		}
	}
}

function read_arr(string $loc) : array {
	$loc = realpath(dirname(__FILE__)).'/..'.$loc;
	$arr = array();
	if ($file = fopen($loc, 'r')) {
		while (!feof($file)) {
			$line = fgets($file);
			if (!empty($line)) $arr[] = trim("$line");
		}
		fclose($file);
	}
	return $arr;
}

// Create database
function create_db(string $loc, string $db_name) : string {
	// Read values from env file
	$ENV = readEnv($loc);
	// If array is empty, return false
	if (empty($ENV)) {
		return 'Error: Could not read .env file.';
	}
	// Else, try create database
	try {
		$conn = new mysqli($ENV['DB_HOST'], $ENV['DB_USER'], $ENV['DB_PASS'], null, $ENV['DB_PORT']);
		if ($conn->connect_error) {
			return 'Error: ' . $conn->connect_error;
		}
		else {
			$sql = "CREATE DATABASE IF NOT EXISTS ".$db_name." CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci";
			if ($conn->query($sql) === TRUE) {
				append_env($loc, array('DB_NAME' => $db_name));
				return 'Success: Database "'.$db_name.'" created.';
			}
			else {
				return 'Error: ' . $conn->error;
			}
		}
	} catch (Exception $e) {
		return 'Error: ' . $e->getMessage();
	}
}

// Create tables
function create_tables(string $loc, string $db_prefix) : string {
	// Read values from env file
	$ENV = readEnv($loc);
	// If array is empty, return false
	if (empty($ENV)) {
		return 'Error: Could not read .env file.';
	}
	// Else, try create tables
	try {
		$conn = new mysqli($ENV['DB_HOST'], $ENV['DB_USER'], $ENV['DB_PASS'], $ENV['DB_NAME'], $ENV['DB_PORT']);
		if ($conn->connect_error) {
			return 'Error: ' . $conn->connect_error;
		}
		else {
			$sql = "CREATE TABLE IF NOT EXISTS ".$db_prefix."users (
				id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
				role INT(1) NOT NULL DEFAULT 0,
				email VARCHAR(50) NOT NULL,
				username VARCHAR(30) NOT NULL,
				password VARCHAR(255) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
			if ($conn->query($sql) === TRUE) {
				$sql = "CREATE TABLE IF NOT EXISTS ".$db_prefix."files (
					id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
					uid INT(6) UNSIGNED NOT NULL,
					FOREIGN KEY (uid) REFERENCES ".$db_prefix."users(id),
					og_name VARCHAR(255) NOT NULL,
					ul_name VARCHAR(255) NOT NULL,
					ext VARCHAR(30) NOT NULL,
					size INT(255) NOT NULL,
					time VARCHAR(255) NOT NULL,
					vis INT(1) NOT NULL DEFAULT 0
				) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
				if ($conn->query($sql) === TRUE) {
					append_env($loc, array('DB_PREFIX' => $db_prefix));
					return 'Success: Tables created.';
				}
				else {
					return 'Error: ' . $conn->error;
				}
			}
			else {
				return 'Error: ' . $conn->error;
			}
		}
	} catch (Exception $e) {
		return 'Error: ' . $e->getMessage();
	}
}

// Create admin user
function create_admin(string $loc, string $username, string $password) : string {
	// Read values from env file
	$ENV = readEnv($loc);
	// If array is empty, return false
	if (empty($ENV)) {
		return 'Error: Could not read .env file.';
	}
	// Else, try create admin user
	try {
		$conn = new mysqli($ENV['DB_HOST'], $ENV['DB_USER'], $ENV['DB_PASS'], $ENV['DB_NAME'], $ENV['DB_PORT']);
		if ($conn->connect_error) {
			return 'Error: ' . $conn->connect_error;
		}
		else {
			$password = password_hash($password, PASSWORD_BCRYPT);
			$sql = "INSERT INTO ".$ENV['DB_PREFIX']."users VALUES (null, 1, '', '".$username."', '".$password."')";
			if ($conn->query($sql) === TRUE) {
				return 'Success: Admin user created.';
			}
			else {
				return 'Error: ' . $conn->error;
			}
		}
	} catch (Exception $e) {
		return 'Error: ' . $e->getMessage();
	}
}

// Check if env exists
function env_exists(string $loc) : array {
	if (file_exists(realpath(dirname(__FILE__)).'/..'.$loc)) {
		// Get values from env file
		$ENV = readEnv($loc);
		// If array is empty, return false
		if (empty($ENV)) {
			return array(true, array('Error: Could not read .env file.'));
		}
		// Else, return true with values
		else {
			return array(
				true, 
				array(
					'DB_HOST' => $ENV['DB_HOST'],
					'DB_USER' => $ENV['DB_USER'],
					'DB_PASS' => str_repeat('x', strlen($ENV['DB_PASS'])),
					'DB_PORT' => $ENV['DB_PORT'],
				),
			);
		}
	}
	else {
		return array(false, null);
	}
}

// Check if database exists
function db_exists(string $loc) : string {
	// Get values from env file
	$ENV = readEnv($loc);
	// If array is empty
	if (empty($ENV)) {
		return 'Error: Could not read .env file.';
	}
	// If array doesn't contain 'DB_NAME'
	if (!array_key_exists('DB_NAME', $ENV)) {
		return 'Success: Database does not exist.';
	}
	// Else, test connection
	try {
		$conn = new mysqli($ENV['DB_HOST'], $ENV['DB_USER'], $ENV['DB_PASS'], $ENV['DB_NAME'], $ENV['DB_PORT']);
		if ($conn->connect_error) {
			return 'Error: ' . $conn->connect_error;
		}
		else {
			return 'Success: Database "'.$ENV['DB_NAME'].'" exists.';
		}
	} catch (Exception $e) {
		return 'Error: ' . $e->getMessage();
	}
}

// Check if tables exist
function tables_exists(string $loc) : string {
	// Get values from env file
	$ENV = readEnv($loc);
	// If array is empty
	if (empty($ENV)) {
		return 'Error: Could not read .env file.';
	}
	// If array doesn't contain 'DB_PREFIX'
	if (!array_key_exists('DB_PREFIX', $ENV)) {
		return 'Success: Tables do not exist.';
	}
	// Else, test connection
	try {
		$conn = new mysqli($ENV['DB_HOST'], $ENV['DB_USER'], $ENV['DB_PASS'], $ENV['DB_NAME'], $ENV['DB_PORT']);
		if ($conn->connect_error) {
			return 'Error: ' . $conn->connect_error;
		}
		else {
			$sql = "SHOW TABLES LIKE '".$ENV['DB_PREFIX']."%'";
			$result = $conn->query($sql);
			if ($result->num_rows == 2) {
				return 'Success: Tables with prefix "'.$ENV['DB_PREFIX'].'" exist.';
			}
			else {
				return 'Success: Tables with prefix "'.$ENV['DB_PREFIX'].'" do not exist.';
			}
		}
	} catch (Exception $e) {
		return 'Error: ' . $e->getMessage();
	}
}

// Check if admin user exists
function admin_exists(string $loc) : string {
	// Get values from env file
	$ENV = readEnv($loc);
	// If array is empty
	if (empty($ENV)) {
		return 'Error: Could not read .env file.';
	}
	// If array doesn't contain 'DB_PREFIX'
	if (!array_key_exists('DB_PREFIX', $ENV)) {
		return 'Error: Tables do not exist.';
	}
	// Else, run query
	try {
		$conn = new mysqli($ENV['DB_HOST'], $ENV['DB_USER'], $ENV['DB_PASS'], $ENV['DB_NAME'], $ENV['DB_PORT']);
		if ($conn->connect_error) {
			return 'Error: ' . $conn->connect_error;
		}
		else {
			$sql = "SELECT * FROM ".$ENV['DB_PREFIX']."users WHERE role = 1 LIMIT 1";
			$result = $conn->query($sql);
			if ($result->num_rows == 1) {
				$row = $result->fetch_assoc();
				return 'Success: Admin user "'.$row['username'].'" exists.';
			}
			else {
				return 'Success: Admin user does not exist.';
			}
		}
	} catch (Exception $e) {
		return 'Error: ' . $e->getMessage();
	}
}
