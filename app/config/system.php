<?php
// Prevent direct access
if (!isset($include)) {
	header('Content-Type: application/json; charset=utf-8');
	include_once '../app/utils/res.php';
	echo Res::fail(403, 'Unauthorized');
	exit();
}

return array(
	"name" => "Shadow",
	"desc" => "A simple, lightweight, and secure web-based file manager.",
	"ssl" => false,
	"webroot" => "localhost",
	"apiroot" => "localhost",
	"version" => "0.0.1",
	"defaults" => array(
		"theme" => "shadow",
		"lang" => "en",
		"timezone" => "UTC",
		"dateformat" => "Y-m-d",
		"itemsperpage" => "20",
	),
	"installed" => false,
	"post_install" => false,
	// Following values are pulled from .env on initial install and are not editable through the UI
	"db_host" => "localhost",
	"db_name" => "shadow",
	"db_prefix" => "",
	"db_user" => "root",
);
