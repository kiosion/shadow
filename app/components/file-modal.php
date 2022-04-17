<?php
if (!isset($include)) {
	header('Content-Type: application/json; charset=utf-8');
	include_once '../utils/res.php';
	echo Res::fail(403, 'Unauthorized');
	exit();
}
?>
<div class="file-card shadow">
	<div class="file-card-file pb-3">
		<img src="" class="img-fluid nosel" alt="" id="modal-content">
		<?php
		// TODO: Implement jquery to add img/audio/embed depending on filetype clicked. for now only images will open in modals.
		// Switch for displaying various content types
		// switch ($FILE_MIMETYPE) {
		// 	// Images
		// 	case 'image/avif':
		// 	case 'image/bmp':
		// 	case 'image/gif':
		// 	case 'image/vdn.microsoft.icon':
		// 	case 'image/jpeg':
		// 	case 'image/png':
		// 	case 'image/svg':
		// 	case 'image/tiff':
		// 	case 'image/webp':
		// 		echo '';
		// 		break;
		// 	// Audio
		// 	case 'audio/aac':
		// 	case 'audio/flac':
		// 	case 'audio/mpeg':
		// 	case 'audio/ogg':
		// 	case 'audio/opus':
		// 	case 'audio/wav':
		// 	case 'audio/webm':
		// 		echo '<audio controls src="" preload="metadata" class="nosel" id="modal-content">This browser doesn\'t support the Audio embed.</audio>';
		// 		break;
		// 	// Application-type
		// 	case 'application/pdf':
		// 		echo '<embed id="modal-content" src="" type="application/pdf" width="100%" height="100%">';
		// 		break;
		// 	}
		?>
	</div>
	<div class="file-card-info pb-3">
		<pre class="fs-6 fw-bold text-light my-0" id="modal-fn"></pre>
	</div>
</div>
