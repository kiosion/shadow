<?php
// Prevent direct access
if (!isset($include)) {
	header("Location: ../../");
}

$username = $user_auth_username;
?>
<main id="page-index" class="container-fluid pb-5">
	<div id="indexContent" class="row-fluid justify-content-around mx-auto">
		<div class="col-fluid mx-3">
			<span class="h2 fw-bold text-light nosel">Uploads</span>
			<div class="row my-5 mx-4 justify-content-around">
			</div>
		</div>
	</div>
	<?php include 'includes/components/uploads.php'; ?>
</main>
