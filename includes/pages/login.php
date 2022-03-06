<?php

if (!isset($include)) {
	header("Location: ../../index.php");
}

?>
<div class="row d-flex justify-content-around">
	<div class="col-12 col-sm-10 col-md-6 col-lg-4">
		<form id="loginForm">
			<h1 class="display-3 fw-bold text-light pb-3">Shadow</h1>
			<h3 class="h3 text-light pb-3">Please sign in</h3>
				<div class="form-group mb-3">
					<div class="form-floating">
						<input id="username" type="text" name="username" class="form-control bg-black text-light" placeholder="Username" autocomplete>
						<label for="username" class="text-light">Username</label>
					</div>
					<div class="form-floating">
						<input id="password" type="password" name="password" class="form-control bg-black text-light" placeholder="Password" autocomplete="current-password">
						<label for="password" class="text-light">Password</label>
					</div>
				</div>
			<button id="submit" type="submit" class="w-100 btn btn-lg btn-light">Login</button>
			<p class="mt-5 mb-3 text-muted">&copy; 2022 Kiosion</p>
		</form>
	</div>
</div>
