<?php

if (!isset($include)) {
	header("Location: ../../index.php");
}

$arr = array("action"=>"print_token","token"=>"$token");
$res = post('http://localhost/shadow/api/auth.php', $arr);
$username = json_decode($res)->data->username;
?>
<div class="row justify-content-around">
	<div class="col-12 col-sm-10 col-md-8 col-lg-6">
		<h3 class="h3 display-4 fw-bold text-light">Shadow</h3>
		<p class="lead text-light">Welcome, <?php echo htmlspecialchars($username); ?></p>
		<div class="row my-5 justify-content-around">
			<a id="launchDashButton" class="col-12 col-lg-5 btn btn-lg btn-outline-light mb-4">Launch dashboard</a>
			<a id="logoutButton" class="col-12 col-lg-5 btn btn-lg btn-outline-light mb-4">Logout</a>
		</div>
	</div>
</div>
