<?php

if (!isset($include)) {
	header("Location: ../../index.php");
}

$arr = array("action"=>"print_payload","token"=>"$_COOKIE[shadow_login_token]");
$res = post('http://localhost/api/v1/auth.php', $arr);
$username = json_decode($res)->data->username;
?>
<div class="row justify-content-around">
	<div class="col-12 col-sm-10 col-md-8 col-lg-6">
		<h3 class="h3 display-4 fw-bold text-light">Shadow</h3>
		<p class="lead text-light">Welcome, <?php echo htmlspecialchars($username); ?></p>
		<div class="row my-5 justify-content-around">
			<button id="launchDashButton" class="col-12 col-lg-5 btn btn-lg btn-light mb-4">Launch dashboard</button>
			<button id="logoutButton" class="col-12 col-lg-5 btn btn-lg btn-light mb-4">Logout</button>
		</div>
	</div>
</div>
