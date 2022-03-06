<header class="p-3 bg-dark text-white">
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
						echo '<button id="loginButton-header" type="button" class="btn btn-dark">Login</button>';
					}
					else {
						echo '<button id="logoutButton-header" type="button" class="btn btn-dark-danger">Logout</button>';
					}
				?>
			</div>
		</div>
	</div>
</header>
