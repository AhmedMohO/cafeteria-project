<?php

use Core\Router;
use Core\Auth;
use App\Controllers\AuthController;
use App\Controllers\Admin\UserController;
use App\Controllers\User\ProductController;
use App\Controllers\User\OrderController;

Router::get('/', function () {
    $base = defined('APP_BASE_PATH') ? APP_BASE_PATH : '';
    $target = Auth::check() ? '/home' : '/login';
    header('Location: ' . ($base === '' ? $target : $base . $target));
    exit;
});

Router::get('/login', [AuthController::class, 'loginForm']);
Router::post('/login', [AuthController::class, 'login']);
Router::get('/logout', [AuthController::class, 'logout']);

Router::get('/home', [ProductController::class, 'index'], [
    'AuthMiddleware'
]);

Router::get('/user/my-orders', [OrderController::class, 'myOrders'], [
    'AuthMiddleware'
]);
Router::post('/user/my-orders/cancel', [OrderController::class, 'cancelOrder'], [
    'AuthMiddleware'
]);

Router::get('/user/my_orders.php', [OrderController::class, 'myOrders'], [
    'AuthMiddleware'
]);
Router::post('/user/my_orders.php', [OrderController::class, 'cancelOrder'], [
    'AuthMiddleware'
]);

Router::get('/user/orders', [OrderController::class, 'ordersAlias'], [
    'AuthMiddleware'
]);

Router::get('/user/orders.php', [OrderController::class, 'ordersAlias'], [
    'AuthMiddleware'
]);

Router::get('/user/search-products', [ProductController::class, 'searchProducts'], [
    'AuthMiddleware'
]);
Router::get('/user/latest-order', [OrderController::class, 'latestOrder'], [
    'AuthMiddleware'
]);
Router::post('/user/place-order', [OrderController::class, 'placeOrder'], [
    'AuthMiddleware'
]);

Router::get('/user/search_products.php', [ProductController::class, 'searchProducts'], [
    'AuthMiddleware'
]);
Router::get('/user/latest_order.php', [OrderController::class, 'latestOrder'], [
    'AuthMiddleware'
]);
Router::post('/user/place_order.php', [OrderController::class, 'placeOrder'], [
    'AuthMiddleware'
]);

Router::get('/admin/users', [UserController::class, 'index'], [
    'AuthMiddleware',
    'AdminMiddleware'
]);
Router::post('/admin/users/delete', [UserController::class, 'delete'], [
    'AuthMiddleware',
    'AdminMiddleware'
]);