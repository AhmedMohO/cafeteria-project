<?php

use Core\Router;
use App\Controllers\AuthController;
use App\Controllers\Admin\UserController;

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