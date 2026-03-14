<?php

use Core\Router;
use App\Controllers\AuthController;
use App\Controllers\Admin\UserController;
use App\Controllers\Admin\ProductController;
use App\Controllers\Admin\CategoryController;
use App\Controllers\Admin\OrderController;

Router::get('/', function () {
    echo "Welcome to the Cafeteria Management System!";
});

Router::get('/login', [AuthController::class, 'loginForm']);
Router::post('/login', [AuthController::class, 'login']);
Router::get('/logout', [AuthController::class, 'logout']);

//*- Users Routes
Router::get('/admin/users', [UserController::class, 'index'], [
    'AuthMiddleware',
    'AdminMiddleware'
]);
Router::post('/admin/users/delete', [UserController::class, 'delete'], [
    'AuthMiddleware',
    'AdminMiddleware'
]);
Router::get('/admin/users/create', [UserController::class, 'create'], [
    'AuthMiddleware',
    'AdminMiddleware'
]);
Router::post('/admin/users', [UserController::class, 'store'], [
    'AuthMiddleware',
    'AdminMiddleware'
]);
Router::get('/admin/users/edit', [UserController::class, 'edit'], [
    'AuthMiddleware',
    'AdminMiddleware'
]);
Router::post('/admin/users/update', [UserController::class, 'update'], [
    'AuthMiddleware',
    'AdminMiddleware'
]);
//*- Products Routes
Router::get('/admin/products', [ProductController::class, 'index'], ['AuthMiddleware', 'AdminMiddleware']);
Router::get('/admin/products/create', [ProductController::class, 'create'], ['AuthMiddleware', 'AdminMiddleware']);
Router::post('/admin/products/store', [ProductController::class, 'store'], ['AuthMiddleware', 'AdminMiddleware']);
Router::get('/admin/products/edit/{id}', [ProductController::class, 'edit'], ['AuthMiddleware', 'AdminMiddleware']);
Router::post('/admin/products/update/{id}', [ProductController::class, 'update'], ['AuthMiddleware', 'AdminMiddleware']);
Router::post('/admin/products/delete/{id}', [ProductController::class, 'delete'], ['AuthMiddleware', 'AdminMiddleware']);
Router::post('/admin/products/toggle/{id}', [ProductController::class, 'toggle'], ['AuthMiddleware', 'AdminMiddleware']);

//*- Categories Routes  
Router::get('/admin/categories', [CategoryController::class, 'index'], ['AuthMiddleware', 'AdminMiddleware']);
Router::post('/admin/categories/store', [CategoryController::class, 'store'], ['AuthMiddleware', 'AdminMiddleware']);
Router::post('/admin/categories/delete/{id}', [CategoryController::class, 'delete'], ['AuthMiddleware', 'AdminMiddleware']);

//*- Dashboard Route
Router::get('/admin/dashboard',      [OrderController::class, 'dashboard'],  ['AuthMiddleware', 'AdminMiddleware']);

//*- Orders Routes
Router::get('/admin/orders',         [OrderController::class, 'index'],       ['AuthMiddleware', 'AdminMiddleware']);
Router::post('/admin/mark-delivered',[OrderController::class, 'deliver'],     ['AuthMiddleware', 'AdminMiddleware']);

//*- Manual Order Routes
Router::get('/admin/manual-order',   [OrderController::class, 'manualForm'],  ['AuthMiddleware', 'AdminMiddleware']);
Router::post('/admin/place-order',   [OrderController::class, 'manualStore'], ['AuthMiddleware', 'AdminMiddleware']);

//*- Checks Route
Router::get('/admin/checks',         [OrderController::class, 'checks'],      ['AuthMiddleware', 'AdminMiddleware']);