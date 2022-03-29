<?php
// Prevent direct access
if (!isset($include)) {
	header('Content-Type: application/json; charset=utf-8');
	include_once '../../includes/utils/res.php';
	echo Res::fail(403, 'Unauthorized');
	exit();
}

return array(
	"defaults" => array(
		"theme" => "shadow",
		"lang" => "en",
		"timezone" => "UTC",
		"dateformat" => "Y-m-d",
		"itemsperpage" => "20",
	),
	"username" => "Kio",
	"userid" => "1",
);
