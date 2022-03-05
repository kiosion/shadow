<?php

// Prevent direct access
if (!isset($include)) {
	header('Content-Type: application/json; charset=utf-8');
	include_once 'res.php';
	echo Res::fail(403, 'Unauthorized');
	exit();
}

class DotEnv {
	protected $path;
	public function __construct(string $path) {
		$this->path = $path;
	}
	// Load function, loads .env file into $_ENV var
	public function load() :void {
		$lines = file($this->path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		foreach ($lines as $line) {
			if (strpos($line, '#') === 0) continue;
			list($name, $value) = explode('=', $line, 2);
			$name = trim($name);
			$value = trim($value);
			if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
				putenv(sprintf('%s=%s', $name, $value));
				$_ENV[$name] = $value;
			}
		}
	}
}
