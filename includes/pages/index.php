<?php

if (!isset($include)) {
	header("Location: ../../");
}

// $arr = array("action"=>"print_payload","token"=>"$_COOKIE[shadow_login_token]");
// $res = post('http://localhost/api/v1/auth.php', $arr);
// $username = json_decode($res)->data->username;
$username = $user_auth_username;
?>
<main id="page-index" class="container-fluid pb-5">
	<div id="indexContent" class="row-fluid justify-content-around mx-auto">
		<div class="col-fluid mx-3">
			<h3 class="h3 display-4 fw-bold text-light nosel">Shadow</h3>
			<p class="lead text-light nosel">Welcome, <?php echo htmlspecialchars($username); ?>.</p>
			<div class="row my-5 mx-4 justify-content-around">
				<!-- <button id="launchDashButton" class="col-12 col-md-3 btn btn-lg btn-light mb-4">Dashboard</button>
				<button id="logoutButton" class="col-12 col-md-3 btn btn-lg btn-light mb-4">Logout</button> -->
			</div>
		</div>
	</div>
	<?php include 'includes/components/uploads.php'; ?>
</main>
