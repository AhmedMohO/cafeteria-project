<?php

use Core\Router;
use Core\Auth;
use App\Controllers\AuthController;
use App\Controllers\User\ProductController as UserProductController;
use App\Controllers\User\OrderController as UserOrderController;
use App\Controllers\Admin\UserController;
use App\Controllers\Admin\ProductController as AdminProductController;
use App\Controllers\Admin\CategoryController;
use App\Controllers\Admin\OrderController as AdminOrderController;

Router::get('/', function () {
    $base = defined('APP_BASE_PATH') ? APP_BASE_PATH : '';

    if (!Auth::check()) {
        $target = '/login';
    } elseif (Auth::role() === 'admin') {
        $target = '/admin/dashboard';
    } else {
        $target = '/home';
    }

    header('Location: ' . ($base === '' ? $target : $base . $target));
    exit;
});

Router::get('/login', [AuthController::class, 'loginForm']);
Router::post('/login', [AuthController::class, 'login']);
Router::get('/logout', [AuthController::class, 'logout']);

Router::get('/home', [UserProductController::class, 'index'], [
    'AuthMiddleware'
]);

Router::get('/user/my-orders', [UserOrderController::class, 'myOrders'], [
    'AuthMiddleware'
]);
Router::post('/user/my-orders/cancel', [UserOrderController::class, 'cancelOrder'], [
    'AuthMiddleware'
]);

Router::get('/user/my_orders.php', [UserOrderController::class, 'myOrders'], [
    'AuthMiddleware'
]);
Router::post('/user/my_orders.php', [UserOrderController::class, 'cancelOrder'], [
    'AuthMiddleware'
]);

Router::get('/user/orders', [UserOrderController::class, 'ordersAlias'], [
    'AuthMiddleware'
]);
Router::get('/user/orders.php', [UserOrderController::class, 'ordersAlias'], [
    'AuthMiddleware'
]);

Router::get('/user/search-products', [UserProductController::class, 'searchProducts'], [
    'AuthMiddleware'
]);
Router::get('/user/latest-order', [UserOrderController::class, 'latestOrder'], [
    'AuthMiddleware'
]);
Router::post('/user/place-order', [UserOrderController::class, 'placeOrder'], [
    'AuthMiddleware'
]);

Router::get('/user/search_products.php', [UserProductController::class, 'searchProducts'], [
    'AuthMiddleware'
]);
Router::get('/user/latest_order.php', [UserOrderController::class, 'latestOrder'], [
    'AuthMiddleware'
]);
Router::post('/user/place_order.php', [UserOrderController::class, 'placeOrder'], [
    'AuthMiddleware'
]);

Router::get('/admin', [AdminOrderController::class, 'dashboard'], ['AuthMiddleware', 'AdminMiddleware']);

Router::get('/admin/users', [UserController::class, 'index'], ['AuthMiddleware', 'AdminMiddleware']);
Router::post('/admin/users/delete', [UserController::class, 'delete'], ['AuthMiddleware', 'AdminMiddleware']);
Router::get('/admin/users/create', [UserController::class, 'create'], ['AuthMiddleware', 'AdminMiddleware']);
Router::post('/admin/users', [UserController::class, 'store'], ['AuthMiddleware', 'AdminMiddleware']);
Router::get('/admin/users/edit', [UserController::class, 'edit'], ['AuthMiddleware', 'AdminMiddleware']);
Router::post('/admin/users/update', [UserController::class, 'update'], ['AuthMiddleware', 'AdminMiddleware']);

Router::get('/admin/products', [AdminProductController::class, 'index'], ['AuthMiddleware', 'AdminMiddleware']);
Router::get('/admin/products/create', [AdminProductController::class, 'create'], ['AuthMiddleware', 'AdminMiddleware']);
Router::post('/admin/products/store', [AdminProductController::class, 'store'], ['AuthMiddleware', 'AdminMiddleware']);
Router::get('/admin/products/edit/{id}', [AdminProductController::class, 'edit'], ['AuthMiddleware', 'AdminMiddleware']);
Router::post('/admin/products/update/{id}', [AdminProductController::class, 'update'], ['AuthMiddleware', 'AdminMiddleware']);
Router::post('/admin/products/delete/{id}', [AdminProductController::class, 'delete'], ['AuthMiddleware', 'AdminMiddleware']);
Router::post('/admin/products/toggle/{id}', [AdminProductController::class, 'toggle'], ['AuthMiddleware', 'AdminMiddleware']);

Router::get('/admin/categories', [CategoryController::class, 'index'], ['AuthMiddleware', 'AdminMiddleware']);
Router::post('/admin/categories/store', [CategoryController::class, 'store'], ['AuthMiddleware', 'AdminMiddleware']);
Router::post('/admin/categories/delete/{id}', [CategoryController::class, 'delete'], ['AuthMiddleware', 'AdminMiddleware']);

Router::get('/admin/dashboard', [AdminOrderController::class, 'dashboard'], ['AuthMiddleware', 'AdminMiddleware']);
Router::get('/admin/orders', [AdminOrderController::class, 'index'], ['AuthMiddleware', 'AdminMiddleware']);
Router::post('/admin/mark-delivered', [AdminOrderController::class, 'deliver'], ['AuthMiddleware', 'AdminMiddleware']);

Router::get('/admin/manual-order', [AdminOrderController::class, 'manualForm'], ['AuthMiddleware', 'AdminMiddleware']);
Router::post('/admin/place-order', [AdminOrderController::class, 'manualStore'], ['AuthMiddleware', 'AdminMiddleware']);

// Backward-compatible aliases for old underscore style URLs.
Router::get('/admin/manual_order', [AdminOrderController::class, 'manualForm'], ['AuthMiddleware', 'AdminMiddleware']);
Router::post('/admin/place_order', [AdminOrderController::class, 'manualStore'], ['AuthMiddleware', 'AdminMiddleware']);

Router::get('/admin/checks', [AdminOrderController::class, 'checks'], ['AuthMiddleware', 'AdminMiddleware']);
