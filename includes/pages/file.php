<div class="file-card shadow">
	<div class="file-card-file pb-3">
		<?php

		// Switch for displaying various content types
		switch ($content_type) {
			case 'text/plain':
				$filepath = '/uploads/users/'.$uid.'/'.$filename;
				echo '<pre class="text-light">'.htmlspecialchars(file_get_contents($filepath)).'</pre>';
				break;
			case 'image/jpeg':
				echo '<img src="/file/'.$filename.'/raw" class="img-fluid" alt="'.$filename.'">';
				break;
			case 'image/png':
				echo '<img src="/file/'.$filename.'/raw" class="img-fluid" alt="'.$filename.'">';
				break;
			case 'image/gif':
				echo '<img src="/file/'.$filename.'/raw" class="img-fluid" alt="'.$filename.'">';
				break;
			case 'application/pdf':
				echo '<embed src="/file/'.$filename.'/raw" type="application/pdf" width="100%" height="100%">';
				break;
			case 'application/zip':

		}
		?>
	</div>
	<div class="file-card-info pb-3">
		<pre class="fs-6 fw-bold text-light my-0"><?php echo $filename; ?></pre>
	</div>
</div>
