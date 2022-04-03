<?php
// Prevent direct access
if (!isset($include)) {
	header('Content-Type: application/json; charset=utf-8');
	include_once '../../app/utils/res.php';
	echo Res::fail(403, 'Unauthorized');
	exit();
}
return array(
	"USER_UN" => "",
	"USER_UID" => "",
);
