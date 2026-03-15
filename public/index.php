<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Handle static files in development
if (PHP_SAPI === 'cli-server') {
	$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
	if (is_file(__DIR__ . $path)) {
		return false;
	}
}

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../routes/web.php';

use Core\Router;

// Define base path
if (!defined('APP_BASE_PATH')) {
	define('APP_BASE_PATH', '');
}

// Route request
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

Router::resolve($uri, $method);
