<?php

// Prevent direct access
if (!isset($include)) {
	header("Location: ../../");
}
// Set page link
$currentLink = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; // TODO: HTTP for now since HTTPS isn't implemented in my local apache test setup
// If trailing '/' in link, remove it
if (substr($currentLink, -1) == '/') {
	$currentLink = substr($currentLink, 0, -1);
}
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
				<?php
					echo '<div class="text-end">';
					switch ($app_route) {
						case 'login':
							echo '
								<button id="loginButton-header" type="button" class="btn btn-light">Login</button>
							';
							break;
						case 'index':
							echo '
								<button id="accountButton-header" type="button" class="btn btn-light me-3" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Account" style="width:40px;"><i class="fas fa-user"></i></button>
								<button id="logoutButton-header" type="button" class="btn btn-light-danger" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Logout" style="width:40px;"><i class="fas fa-sign-out-alt"></i></button>
							';
							break;
						case 'admin':
							echo '
								<button id="logoutButton-header" type="button" class="btn btn-light-danger">Logout</button>
							';
							break;
						case 'file':
							echo '
								<button type="button" id="menuBar-copyLink" data-link="'.$currentLink.'" class="btn btn-light me-3" style="width:60px;" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Copy link"><i class="fas fa-link p-1"></i></button>
								<button type="button" id="menuBar-viewRaw" data-link="'.$currentLink.'/raw" class="btn btn-light me-3" style="width:60px;" data-bs-toggle="tooltip" data-bs-placement="bottom" title="View raw"><i class="fas fa-external-link-square p-1"></i></button>
								<button type="button" id="menuBar-download" data-link="'.$currentLink.'/download" class="btn btn-primary" style="width:60px;" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Download file"><i class="fas fa-cloud-download p-1"></i></button>
							';
							break;
						default:
						echo '
							<button id="homeButton-header" type="button" class="btn btn-light">Go back</button>
						';
						// TODO: jQuery pushhistorystate to go back a page
						break;
							
					}
				?>
			</div>
		</div>
	</div>
</header>
