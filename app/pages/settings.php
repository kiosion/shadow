<?php
if (!isset($include)) {
	header('Content-Type: application/json; charset=utf-8');
	include_once '../utils/res.php';
	echo Res::fail(403, 'Unauthorized');
	exit();
}
?>
<main class="container-fluid my-auto pb-5">
	<div class="row d-flex justify-content-evenly">
		<!-- Left col -->
		<div class="col-11 col-md-7 px-4 px-lg-5 bg-dark text-light container-rounded shadow pb-3 mb-4 nosel">
			<div class="row pt-4 pb-4 pb-lg-5">
				<div class="col">
					<h2 class="h2 fw-bold">App settings</h2>
				</div>
			</div>
			<div class="row text-start py-2 d-flex flex-nowrap justify-content-between">
				<div class="col fit-col my-auto">
					<p class="fs-5 mb-0">Image embeds</p>
				</div>
				<div class="col fit-col">
					<label class="switch">
						<input type="checkbox" checked>
						<span class="slider round"></span>
					</label>
				</div>
			</div>
			<div class="row text-start py-2 d-flex flex-nowrap justify-content-between">
				<div class="col fit-col my-auto">
					<p class="fs-5 mb-0">Upload quota</p>
				</div>
				<div class="col fit-col">
					<label class="switch">
						<input type="checkbox">
						<span class="slider round"></span>
					</label>
				</div>
			</div>
			<div class="row text-start py-2 d-flex flex-nowrap justify-content-between">
				<div class="col fit-col my-auto">
					<p class="fs-5 mb-0">Quota amount</p>
				</div>
				<div class="col fit-col">
					<label class="switch">
						<input type="checkbox">
						<span class="slider round"></span>
					</label>
				</div>
			</div>
			<div class="row text-start py-2 d-flex flex-nowrap justify-content-between">
				<div class="col fit-col my-auto">
					<p class="fs-5 mb-0">Colour theme</p>
				</div>
				<div class="col fit-col">
					<div class="dropdown">
						<a class="btn btn-dark dropdown-toggle" role="button" id="dropdownSortBy" data-bs-toggle="dropdown" aria-expanded="false">Dracula</a>
						<ul class="dropdown-menu" aria-labelledby="dropdownSortBy">
							<li class="dropdown-item active"><a class="dropdown-item">Dracula</a></li>
							<li class="dropdown-item"><a class="dropdown-item">Hacker</a></li>
							<li class="dropdown-item"><a class="dropdown-item">Pure Black</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<!-- Right col group -->
		<div class="col-11 col-md-4">
			<div class="row mb-4">
				<div class="col px-4 px-lg-5 bg-dark text-light container-rounded shadow pb-3">
					<div class="row my-3 pb-3">
						<div class="col">
							<h2 class="h2 fw-bold">Info</h2>
						</div>
					</div>
					<div class="row text-start py-2 d-flex flex-nowrap justify-content-between">
						<div class="col fit-col my-auto">
							<p class="fs-5 mb-0">Registered users:</p>
						</div>
						<div class="col fit-col">
							<p class="fs-5 mb-0">2</p>
						</div>
					</div>
					<div class="row text-start py-2 d-flex flex-nowrap justify-content-between">
						<div class="col fit-col my-auto">
							<p class="fs-5 mb-0">Space used:</p>
						</div>
						<div class="col fit-col">
							<p class="fs-5 mb-0">456 MB</p>
						</div>
					</div>
					<div class="row text-start py-2 d-flex flex-nowrap justify-content-between">
						<div class="col fit-col my-auto">
							<p class="fs-5 mb-0">PHP version:</p>
						</div>
						<div class="col fit-col">
							<p class="fs-5 mb-0"><?php echo phpversion();?></p>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col px-4 px-lg-5 bg-dark text-light container-rounded shadow pb-3">
					<div class="row my-3 pb-3">
						<div class="col">
							<h2 class="h2 fw-bold">Operations</h2>
						</div>
					</div>
					<div class="row text-start py-2 d-flex flex-nowrap justify-content-between">
						<div class="col">
							<button class="btn btn-md btn-dark-danger fs-5 mb-0 w-100">Remove orphaned files</button>
						</div>
					</div>
					<div class="row text-start py-2 d-flex flex-nowrap justify-content-between">
						<div class="col">
							<button class="btn btn-md btn-dark fs-5 mb-0 w-100">Recalculate stats</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		
	</div>
</main>
