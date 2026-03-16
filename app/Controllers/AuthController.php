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
                header("Location: /admin");
            } else {
                header("Location: /home");
            }

            exit;
        }

    }

    public function logout()
    {
        Auth::logout();
        $this->view('auth/login');
    }
}
