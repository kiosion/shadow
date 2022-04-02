<?php

// Prevent direct file access
if (!isset($include)) {
	require_once '../app/utils/res.php';
	header('Content-Type: application/json');
	echo Res::fail(403, 'Forbidden');
	exit();
}
