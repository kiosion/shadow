<?php
if (!isset($include)) {
	header('Content-Type: application/json; charset=utf-8');
	include_once '../utils/res.php';
	echo Res::fail(403, 'Unauthorized');
	exit();
}
?>
<main class="container-fluid my-auto pb-5">
	<p class="text-light">TODO</p>
</main>
