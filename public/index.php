<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);



require_once __DIR__ . '/../vendor/autoload.php';

use Core\Router;

require_once __DIR__ . '/../routes/web.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$base = '/Cafeteria_Project';

$uri = str_replace($base, '', $uri);

$method = $_SERVER['REQUEST_METHOD'];

Router::resolve($uri, $method);