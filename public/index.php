<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (PHP_SAPI === 'cli-server') {
	$requestedPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
	$staticFile = __DIR__ . $requestedPath;

	if (is_file($staticFile)) {
		return false;
	}
}

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Router;

require_once __DIR__ . '/../routes/web.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$base = str_replace('/public/index.php', '', $scriptName);

if ($base === '/index.php') {
	$base = '';
}

$base = rtrim($base, '/');

if (!defined('APP_BASE_PATH')) {
	define('APP_BASE_PATH', $base);
}

if ($base !== '' && str_starts_with($uri, $base)) {
	$uri = substr($uri, strlen($base));
}

if ($uri === '' || $uri === false) {
	$uri = '/';
}

$method = $_SERVER['REQUEST_METHOD'];

Router::resolve($uri, $method);