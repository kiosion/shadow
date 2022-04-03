<?php
if (!isset($include)) {
	header('Content-Type: application/json; charset=utf-8');
	include_once '../utils/res.php';
	echo Res::fail(403, 'Unauthorized');
	exit();
}
// Check if file exists
if (!file_exists($FILE_PATH)) $app_route = '404';
?>
<main id="page-file" class="container-fluid my-auto pb-5">
	<?php include 'app/components/file-card.php'; ?>
</main>
