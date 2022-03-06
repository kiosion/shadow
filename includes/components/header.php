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
					if ($view == 'login') {
						echo '<button id="loginButton-header" type="button" class="btn btn-light">Login</button>';
					}
					else if ($view == 'index') {
						echo '<button id="logoutButton-header" type="button" class="btn btn-light-danger">Logout</button>';
					}
					else if ($view == '404' || $view == '403' || $view == '500') {
						echo '<button id="homeButton-header" type="button" class="btn btn-light">Go back</button>'; // TODO: jQuery pushhistorystate to go back a page
					}
				?>
			</div>
		</div>
	</div>
</header>
