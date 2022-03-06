<?php

if (!isset($include)) {
	header("Location: ../../");
}

$arr = array("action"=>"print_payload","token"=>"$_COOKIE[shadow_login_token]");
$res = post('http://localhost/api/v1/auth.php', $arr);
$username = json_decode($res)->data->username;
?>
<div id="indexContent" class="row-fluid justify-content-around mx-auto">
	<div class="col-fluid mx-3">
		<h3 class="h3 display-4 fw-bold text-light">Shadow</h3>
		<p class="lead text-light">Welcome, <?php echo htmlspecialchars($username); ?></p>
		<div class="row my-5 justify-content-around">
			<button id="launchDashButton" class="col-12 col-lg-5 btn btn-lg btn-light mb-4">Launch dashboard</button>
			<button id="logoutButton" class="col-12 col-lg-5 btn btn-lg btn-light mb-4">Logout</button>
		</div>
	</div>
</div>
