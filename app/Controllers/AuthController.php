<?php

namespace App\Controllers;

use Core\Controller;
use Core\Auth;

class AuthController extends Controller
{
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
                // header('Location: ' . $this->appUrl('/admin'));
                header('Location: /admin');
                exit;
            } else {
                // header('Location: ' . $this->appUrl('/home'));
                header('Location: /home');
                exit;
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
