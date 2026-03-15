<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Handle static files in development
if (PHP_SAPI === 'cli-server') {
	$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
	if ($path && is_file(__DIR__ . $path)) {
		return false;
	}
}

$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$basePath = rtrim(str_replace('/index.php', '', $scriptName), '/');
if ($basePath === '/' || $basePath === '.') {
	$basePath = '';
}

if (!defined('APP_BASE_PATH')) {
	define('APP_BASE_PATH', $basePath);
}

if (!defined('BASE_URL')) {
	define('BASE_URL', APP_BASE_PATH);
}

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../routes/web.php';

use Core\Router;

// Route request
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';

if (APP_BASE_PATH !== '' && strpos($uri, APP_BASE_PATH) === 0) {
	$uri = substr($uri, strlen(APP_BASE_PATH));
}

$uri = preg_replace('#^/index\.php#', '', $uri);

if ($uri === '' || $uri === null) {
    $uri = '/';
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

Router::resolve($uri, $method);
