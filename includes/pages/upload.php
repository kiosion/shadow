<?php

if (!isset($include)) {
	header("Location: ../../");
}
?>
<main id="page-index" class="container-fluid pb-5">
	<div id="indexContent" class="row-fluid justify-content-around mx-auto">
		<div class="col-fluid mx-3">
			<h3 class="h3 display-4 fw-bold text-light nosel">Shadow</h3>
			<p class="lead text-light nosel">Drop files or choose files to upload.</p>
			<div class="row my-5 mx-4 justify-content-around">
				<form enctype="multipart/form-data" id="fileUploadForm" class="dropzone">
					<input type="file" id="fileUpload" class="text-light bg-dark p-3 rounded" multiple>
				</form>
			</div>
		</div>
	</div>

</main>
