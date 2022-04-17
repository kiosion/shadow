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
		<?php
		
		if (!is_readable($FILE_PATH)) echo '<p class="text-light pt-4">Error previewing file.</p>';
		else {
			// Switch for displaying various content types
			switch ($FILE_MIMETYPE) {
				// Plain text files
				case 'text/plain':
				case 'text/js':
				case 'text/html':
					echo '<pre class="text-light">'.htmlspecialchars(file_get_contents($FILE_PATH)).'</pre>';
					break;
				// Images
				case 'image/avif':
				case 'image/bmp':
				case 'image/gif':
				case 'image/vdn.microsoft.icon':
				case 'image/jpeg':
				case 'image/png':
				case 'image/svg':
				case 'image/tiff':
				case 'image/webp':
					echo '<img src="'.$_SHADOW_APP_URL.'/file/'.$FILE_NAME.'/raw" class="img-fluid nosel" alt="'.$FILE_NAME.'">';
					break;
				// Audio
				case 'audio/aac':
				case 'audio/flac':
				case 'audio/mpeg':
				case 'audio/ogg':
				case 'audio/opus':
				case 'audio/wav':
				case 'audio/webm':
					echo '<audio controls src="'.$_SHADOW_APP_URL.'/file/'.$FILE_NAME.'/raw" preload="metadata" class="nosel">This browser doesn\'t support the Audio embed.</audio>';
					break;
				// Application-type
				case 'application/pdf':
					echo '<embed id="embed-pdf" src="'.$_SHADOW_APP_URL.'/file/'.$FILE_NAME.'/raw" type="application/pdf" width="100%" height="100%">';
					break;
				case 'application/php':
				case 'application/json':
					$FILE_NAME = '/uploads/users/'.$FILE_UID.'/'.$FILE_NAME;
					echo '<pre class="text-light">'.htmlspecialchars(file_get_contents($FILE_NAME)).'</pre>';
					break;
				// Other
				default:
					$str_content_type = explode('/', $FILE_MIMETYPE)[1];
					echo '<p class="text-light pt-4 nosel">\'.'.$str_content_type.'\' files cannot be previewed.</p>';
					break;
				}
			}
		?>
	</div>
	<div class="file-card-info pb-3">
		<pre class="fs-6 fw-bold text-light my-0"><?php 
			if (!empty($FILE_VIS)) { 
				echo '<i class="fas fa-lock ps-2 pe-3" data-bs-toggle="tooltip" data-bs-placement="left" title="You\'re viewing a '.$FILE_VIS.' file"></i>'; 
			} 
			echo htmlspecialchars($FILE_OGNAME);
		?></pre>
	</div>
</div>
