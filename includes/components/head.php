<?php

// Prevent direct access
if (!isset($include)) {
	header("Location: ../../");
}
?>
<!DOCTYPE html>
	<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title><?php echo $page_title; ?></title>
		<?php require_once 'includes/styles.php'; ?>
	</head>
