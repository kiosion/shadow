<?php

// Prevent direct access
if (!isset($include)) {
	header("Location: ../../");
}
// Set filepath
$filepath = 'uploads/users/'.$uid.'/'.$filename;
// Check if file exists and is readable
if (!file_exists($filepath) || !is_readable($filepath)) {
	// File does not exist or is not readable
	// Redirect to 404 page
	header("Location: /404");
	exit();
}
// Check if file is private
$arr = array("action"=>"get_info","filename"=>"$filename", "token"=>"$token");
$res = post('http://localhost/api/v1/file.php', $arr);
$res_decoded = json_decode($res);
echo "Visibility: ".$res_decoded->data->vis;
$priv_file = false;
// If file is private
if ($res_decoded->data->vis == 1) {
	// Check if user is logged in
	if (!isset($_COOKIE['shadow_login_token'])) {
		// Redirect to login page
		header("Location: /login");
		exit();
	}
	else {
		$priv_file = true;
	}
}
?>
<main class="container-fluid my-auto pb-5">
	<div class="file-card shadow">
		<div class="file-card-file pb-3">
			<?php
			
			if (!is_readable($filepath)) {
				echo '<p class="text-light pt-4">Error previewing file.</p>';
			}
			else {
				// Switch for displaying various content types
				switch ($content_type) {
					// Plain text files
					case 'text/plain':
					case 'text/js':
					case 'text/html':
						echo '<pre class="text-light">'.htmlspecialchars(file_get_contents($filepath)).'</pre>';
						break;
					// Image files
					case 'image/avif':
					case 'image/bmp':
					case 'image/gif':
					case 'image/vdn.microsoft.icon':
					case 'image/jpeg':
					case 'image/png':
					case 'image/svg':
					case 'image/tiff':
					case 'image/webp':
						echo '<img src="/file/'.$filename.'/raw" class="img-fluid nosel" alt="'.$filename.'">';
						break;
					// Audio files
					case 'audio/aac':
					case 'audio/flac':
					case 'audio/mpeg':
					case 'audio/ogg':
					case 'audio/opus':
					case 'audio/wav':
					case 'audio/webm':
						echo '<audio controls src="/file/'.$filename.'/raw" preload="metadata" class="nosel">This browser doesn\'t support the Audio embed.</audio>';
						break;
					// Application-type files
					case 'application/pdf':
						echo '<embed id="embed-pdf" src="/file/'.$filename.'/raw" type="application/pdf" width="100%" height="100%">';
						break;
					case 'application/php':
					case 'application/json':
						$filepath = '/uploads/users/'.$uid.'/'.$filename;
						echo '<pre class="text-light">'.htmlspecialchars(file_get_contents($filepath)).'</pre>';
						break;
					// Other file types
					default:
						// Remove string content before first slash
						$str_content_type = explode('/', $content_type);
						$str_content_type = $str_content_type[1];
						echo '<p class="text-light pt-4 nosel">\'.'.$str_content_type.'\' files cannot be previewed.</p>';
						break;
					}
				}
			?>
		</div>
		<div class="file-card-info pb-3">
			<pre class="fs-6 fw-bold text-light my-0"><?php 
				if ($priv_file) { 
					echo '<i class="fas fa-lock pe-3"></i>'; 
				} 
				echo htmlspecialchars($og_name);
			?></pre>
		</div>
	</div>
</main>
