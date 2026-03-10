<?php

use Core\Router;
use App\Controllers\AuthController;

Router::get('/', function () {
    echo "Welcome to the Cafeteria Management System!";
});

Router::get('/login', [AuthController::class, 'loginForm']);
Router::post('/login', [AuthController::class, 'login']);
Router::get('/logout', [AuthController::class, 'logout']);