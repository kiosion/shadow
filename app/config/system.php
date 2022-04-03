<?php
// Prevent direct access
if (!isset($include)) {
	header('Content-Type: application/json; charset=utf-8');
	include_once '../app/utils/res.php';
	echo Res::fail(403, 'Unauthorized');
	exit();
}
return array(
	"APP_NAME" => "Shadow",
	"APP_DESC" => "A simple, lightweight, and secure web-based file manager.",
	"APP_SSL" => false,
	"APP_URL" => "localhost",
	"APP_API_URL" => "localhost",
	"APP_VERSION" => "0.0.1",
	"APP_THEME" => "shadow",
	"APP_LANG" => "en",
	"APP_TIMEZONE" => "UTC",
	"APP_D_FORMAT" => "Y-m-d",
	"APP_I_PP" => "20",
	"APP_IS_INSTALLED" => false,
	"APP_IS_POST_INSTALL" => false,
	"APP_DB_NAME" => "shadow",
	"APP_DB_PREFIX" => "",
	"APP_DB_HOST" => "localhost",
	"APP_DB_USER" => "root",
);
