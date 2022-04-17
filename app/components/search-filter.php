<?php
if (!isset($include)) {
	header('Content-Type: application/json; charset=utf-8');
	include_once '../utils/res.php';
	echo Res::fail(403, 'Unauthorized');
	exit();
}
?>
<div id="searchFilterContainer" class="row-fluid justify-content-around py-5">
	<div class="col-fluid">
		<form class="" id="searchFilter">
			<div class="form-floating">
				<input type="text" class="form-control" id="searchFilterInput" placeholder="Search...">
				<label for="searchFilterInput">Search</label>
			</div>
		</form>
	</div>
</div>
