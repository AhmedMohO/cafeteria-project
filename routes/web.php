<?php

use Core\Router;
use App\Controllers\AuthController;
use App\Controllers\Admin\UserController;
use App\Controllers\Admin\ProductController;
use App\Controllers\Admin\CategoryController;

Router::get('/', function () {
    echo "Welcome to the Cafeteria Management System!";
});

Router::get('/login', [AuthController::class, 'loginForm']);
Router::post('/login', [AuthController::class, 'login']);
Router::get('/logout', [AuthController::class, 'logout']);


Router::get('/admin/users', [UserController::class, 'index'], [
    'AuthMiddleware',
    'AdminMiddleware'
]);
Router::post('/admin/users/delete', [UserController::class, 'delete'], [
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