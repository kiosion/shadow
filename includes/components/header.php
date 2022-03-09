<?php

// Prevent direct access
if (!isset($include)) {
	header("Location: ../../");
}
// Set page link
$requestURI = explode('/', $_SERVER['REQUEST_URI']);
$requestURI = '/'.$requestURI[1].'/'.$requestURI[2];
$currentLink = 'http://'.$_SERVER['HTTP_HOST'].$requestURI;
$currentHost = 'http://'.$_SERVER['HTTP_HOST'];
?>
<header class="p-3 bg-black text-white fixed-top nosel">
	<div class="container-fluid">
		<div class="d-flex flex-wrap align-items-center justify-content-center justify-content-sm-start">
			<div class="col-12 col-sm-auto me-sm-auto mb-2 justify-content-center mb-sm-0">
				<a href="/home" class="h4 text-white text-decoration-none">
					Shadow
				</a>
			</div>

			<div class="text-end">
				<?php
					echo '<div class="text-end">';
					switch ($app_route) {
						case 'login':
							echo '
								<button id="header-loginButton" type="button" class="btn btn-light">Login</button>
							';
							break;
						case 'upload':
						case 'index':
							echo '
								<button id="header-uploadButton" data-link="'.$currentHost.'/upload" type="button" class="btn btn-light me-3" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Upload" style="width:60px;"><i class="fas fa-upload"></i></button>
								<button id="header-accountButton" data-link="'.$currentHost.'/account" type="button" class="btn btn-light me-3" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Account" style="width:60px;"><i class="fas fa-user"></i></button>
							';
							// If user has admin privileges, show system settings button
							if ($user_auth_role == 1) {
								echo '
								<button id="header-settingsButton" data-link="'.$currentHost.'/settings" type="button" class="btn btn-light me-3" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Settings" style="width:60px;"><i class="fas fa-cog"></i></button>
								';
							}
							echo '
								<button id="header-logoutButton" type="button" class="btn btn-light-danger" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Logout" style="width:60px;"><i class="fas fa-sign-out-alt"></i></button>
							';
							break;
						case 'admin':
							echo '
								<button id="header-logoutButton" type="button" class="btn btn-light-danger">Logout</button>
							';
							break;
						case 'file':
							echo '
								<button type="button" id="menuBar-copyLink" data-link="'.$currentLink.'" class="btn btn-light me-3" style="width:60px;" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Copy link"><i class="fas fa-link p-1"></i></button>
								<button type="button" id="menuBar-viewRaw" data-link="'.$currentLink.'/raw" class="btn btn-light me-3" style="width:60px;" data-bs-toggle="tooltip" data-bs-placement="bottom" title="View raw"><i class="fas fa-external-link-square p-1"></i></button>
								<button type="button" id="menuBar-download" data-link="'.$currentLink.'/download" class="btn btn-light-cyan" style="width:60px;" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Download file"><i class="fas fa-cloud-download p-1"></i></button>
							';
							break;
						default:
						echo '
							<button id="header-backButton" type="button" class="btn btn-light">Go back</button>
						';
						// TODO: jQuery pushhistorystate to go back a page
						break;
							
					}
				?>
			</div>
		</div>
	</div>
</header>
