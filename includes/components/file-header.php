<?php

$currentLink = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
?>
<header class="p-3 bg-black text-white">
	<div class="container-fluid">
		<div class="d-flex flex-wrap align-items-center justify-content-center justify-content-sm-start">
			<div class="col-12 col-sm-auto me-sm-auto mb-2 justify-content-center mb-sm-0">
				<a href="/" class="h4 text-white text-decoration-none">
					Shadow
				</a>
			</div>

			<div class="text-end">
				<button type="button" id="menuBar-copyLink" data-link="<?php echo $currentLink; ?>" class="btn btn-light me-3" style="width:60px;" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Copy link"><i class="fas fa-link p-1"></i></button>
				<button type="button" id="menuBar-viewRaw" data-link="<?php echo $currentLink; ?>/raw" class="btn btn-light me-3" style="width:60px;" data-bs-toggle="tooltip" data-bs-placement="bottom" title="View raw"><i class="fas fa-external-link-square p-1"></i></button>
				<button type="button" id="menuBar-download" data-link="<?php echo $currentLink; ?>/download" class="btn btn-primary" style="width:60px;" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Download file"><i class="fas fa-cloud-download p-1"></i></button>
			</div>
		</div>
	</div>
</header>
