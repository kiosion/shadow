<?php

// Prevent direct access
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header('Content-Type: application/json; charset=utf-8');
	include_once 'utils/res.php';
	echo Res::fail(403, 'Forbidden');
	exit();
}

header('Content-Type: application/json; charset=utf-8');
include_once 'utils/res.php';
echo Res::fail(403, 'Forbidden');
exit();

// TODO: Implement app-related config changes or other actions
