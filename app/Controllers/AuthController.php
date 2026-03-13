<?php

namespace App\Controllers;

use Core\Controller;
use Core\Auth;

class AuthController extends Controller
{
    private function appUrl(string $path): string
    {
        $base = defined('APP_BASE_PATH') ? APP_BASE_PATH : '';
        $normalizedPath = '/' . ltrim($path, '/');

        if ($base === '') {
            return $normalizedPath;
        }

        return $base . $normalizedPath;
    }

    public function loginForm()
    {
        $this->view('auth/login');
    }

    public function login()
    {
        $email = $_POST['email'];
        $password = $_POST['password'];

        if (Auth::attempt($email, $password)) {

            if (Auth::role() === 'admin') {
                header('Location: ' . $this->appUrl('/admin'));
            } else {
                header('Location: ' . $this->appUrl('/home'));
            }

            exit;
        }

        echo "Invalid credentials";
    }

    public function logout()
    {
        Auth::logout();
        $this->view('auth/login');
    }
}
