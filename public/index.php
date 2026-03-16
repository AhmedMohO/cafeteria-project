<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

define('BASE_URL', '/public');

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Router;

require_once __DIR__ . '/../routes/web.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$uri = preg_replace('#^.*?/public(?:/index\.php)?#', '', $uri);

if ($uri === '' || $uri === null || $uri === '/') {
    header('Location: ' . BASE_URL . '/login');
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

Router::resolve($uri, $method);