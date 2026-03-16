<?php

namespace App\Middlewares;

use Core\Auth;

class AuthMiddleware
{
    public function handle()
    {
        if (!Auth::check()) {
            header("Location: " . BASE_URL . "/login"); 
            exit;
        }
    }
}