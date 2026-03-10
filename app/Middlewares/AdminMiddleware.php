<?php

namespace App\Middlewares;

use Core\Auth;

class AdminMiddleware
{
    public function handle()
    {
        if (!Auth::check() || Auth::role() !== 'admin') {

            echo "Access Denied";
            exit;
        }
    }
}
