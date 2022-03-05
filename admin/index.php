<?php

// Set vars
$login = false;
$include = true;

// Include files
require_once '../includes/utils/post.php';

// Verify login token
if (isset($_COOKIE['shadow_login_token'])) {
	$token = $_COOKIE['shadow_login_token'];
	// Check if the token is valid via POST req to API auth endpoint
	$arr = array("action"=>"check_token","token"=>"$token","type"=>"login");
	$res = post('http://localhost/shadow/api/v1/auth.php', $arr);
	if (json_decode($res)->msg == 'Token valid') {
		$login = true;
	}
}

if (!$login) {
	// Redirect to login page
	header('Location: ../index.php');
	exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Shadow - Admin</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>
<body class="text-center bg-black">
	<div class="d-flex flex-column min-vh-100">
		<main class="container my-auto">
			<?php
				include 'includes/pages/index.php';
			?>
		</main>
	</div>
</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</html>
