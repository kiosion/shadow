<?php
if (!isset($include)) {
	header('Content-Type: application/json; charset=utf-8');
	include_once '../utils/res.php';
	echo Res::fail(403, 'Unauthorized');
	exit();
}
?>
<!DOCTYPE html>
	<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title><?php echo $page_title; ?></title>
		<?php require_once 'app/styles.php'; ?>
	</head>
