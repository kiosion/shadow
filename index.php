<?php

require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'includes/utils/jwt.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'includes/utils/auth.php';
// Check if the user is logged in via cookie with JWT token
if (isset($_COOKIE['login_token'])) {
	if (Auth::check_login_token($_COOKIE['login_token'])) {
		// If logged in, redirect to admin dash
		//header('Location: admin/index.php');
		// console log
		echo 'Cookie set';
	}
}
else echo "Cookie not set."
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Shadow - Index</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>
<body class="text-center bg-black">
	<div class="d-flex flex-column min-vh-100">
		<main class="container my-auto">
			<?php
				include 'includes/pages/login.php';
			?>
		</main>
	</div>
</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/js-cookie@3.0.1/dist/js.cookie.min.js"></script>
<script src="js/scripts.js"></script>
</html>
