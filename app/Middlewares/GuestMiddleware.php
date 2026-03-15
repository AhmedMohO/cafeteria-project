<?php

namespace App\Middlewares;

use Core\Auth;

class GuestMiddleware
{
    public function handle()
    {
        if (Auth::check()) {
            if (Auth::role() === 'admin') {
                header("Location: " . BASE_URL . "/admin/dashboard");
            } else {
                header("Location: " . BASE_URL . "/user/home");
            }
            exit;
        }
    }
}
