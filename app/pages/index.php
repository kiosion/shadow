<?php
if (!isset($include)) {
	header('Content-Type: application/json; charset=utf-8');
	include_once '../utils/res.php';
	echo Res::fail(403, 'Unauthorized');
	exit();
}
?>
<main id="page-index" class="container-fluid pb-5">
	<div id="indexContent" class="row-fluid justify-content-around mx-auto">
		<div class="col-fluid mx-3">
			<span class="h2 fw-bold text-light nosel">Uploads</span>
		</div>
	</div>
	<?php 
		include 'app/components/search-filter.php';
		include 'app/components/uploads.php'; 
	?>
	<div class="modal fade" id="file-modal" tabindex="-1" aria-labelledby="">
		<div class="modal-dialog modal-dialog-centered">
			<?php include 'app/components/file-modal.php'; ?>
		</div>
	</div>
</main>
