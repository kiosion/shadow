<?php

if (!isset($include)) {
	header("Location: ../../");
}
?>
<main class="container-fluid my-auto pb-5">
	<div class="file-card shadow">
		<div class="file-card-file pb-3">
			<?php
			$filepath = 'uploads/users/'.$uid.'/'.$filename;
			if (!is_readable($filepath)) {
				echo '<p class="text-light pt-4">Error previewing file.</p>';
			}
			else {
				// Switch for displaying various content types
				switch ($content_type) {
					// Plain text files
					case 'text/plain':
						echo '<pre class="text-light">'.htmlspecialchars(file_get_contents($filepath)).'</pre>';
						break;
					case 'text/js':
						echo '<pre class="text-light">'.htmlspecialchars(file_get_contents($filepath)).'</pre>';
						break;
					case 'text/html':
						echo '<pre class="text-light">'.htmlspecialchars(file_get_contents($filepath)).'</pre>';
						break;
					// Image files
					case 'image/avif':
						echo '<img src="/file/'.$filename.'/raw" class="img-fluid" alt="'.$filename.'">';
						break;
					case 'image/bmp':
						echo '<img src="/file/'.$filename.'/raw" class="img-fluid" alt="'.$filename.'">';
						break;
					case 'image/gif':
						echo '<img src="/file/'.$filename.'/raw" class="img-fluid" alt="'.$filename.'">';
						break;
					case 'image/vdn.microsoft.icon':
						echo '<img src="/file/'.$filename.'/raw" class="img-fluid" alt="'.$filename.'">';
						break;
					case 'image/jpeg':
						echo '<img src="/file/'.$filename.'/raw" class="img-fluid" alt="'.$filename.'">';
						break;
					case 'image/png':
						echo '<img src="/file/'.$filename.'/raw" class="img-fluid" alt="'.$filename.'">';
						break;
					case 'image/svg':
						//TODO
						break;
					case 'image/tiff':
						echo '<img src="/file/'.$filename.'/raw" class="img-fluid" alt="'.$filename.'">';
						break;
					case 'image/webp':
						echo '<img src="/file/'.$filename.'/raw" class="img-fluid" alt="'.$filename.'">';
						break;
					// Audio files
					case 'audio/aac':
						echo '<audio controls src="/file/'.$filename.'/raw" preload="metadata">This browser doesn\'t support the Audio embed.</audio>';
						break;
					case 'audio/flac':
						echo '<audio controls src="/file/'.$filename.'/raw" preload="metadata">This browser doesn\'t support the Audio embed.</audio>';
						break;
					case 'audio/mpeg':
						echo '<audio controls src="/file/'.$filename.'/raw" preload="metadata">This browser doesn\'t support the Audio embed.</audio>';
						break;
					case 'audio/ogg':
						echo '<audio controls src="/file/'.$filename.'/raw" preload="metadata">This browser doesn\'t support the Audio embed.</audio>';
						break;
					case 'audio/opus':
						echo '<audio controls src="/file/'.$filename.'/raw" preload="metadata">This browser doesn\'t support the Audio embed.</audio>';
						break;
					case 'audio/wav':
						echo '<audio controls src="/file/'.$filename.'/raw" preload="metadata">This browser doesn\'t support the Audio embed.</audio>';
						break;
					case 'audio/webm':
						echo '<audio controls src="/file/'.$filename.'/raw" preload="metadata">This browser doesn\'t support the Audio embed.</audio>';
						break;
					// Application-type files
					case 'application/pdf':
						echo '<embed id="embed-pdf" src="/file/'.$filename.'/raw" type="application/pdf" width="100%" height="100%">';
						break;
					case 'application/php':
						$filepath = '/uploads/users/'.$uid.'/'.$filename;
						echo '<pre class="text-light">'.htmlspecialchars(file_get_contents($filepath)).'</pre>';
						break;
					case 'application/json':
						$filepath = '/uploads/users/'.$uid.'/'.$filename;
						echo '<pre class="text-light">'.htmlspecialchars(file_get_contents($filepath)).'</pre>';
						break;
					
					// Other file types
					default:
						echo '<p class="text-light pt-4">This file cannot be previewed.</p>';
						break;
					}
				}
			?>
		</div>
		<div class="file-card-info pb-3">
			<pre class="fs-6 fw-bold text-light my-0"><?php echo $og_name; ?></pre>
		</div>
	</div>
</main>
