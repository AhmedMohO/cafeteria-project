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
                header("Location: " . BASE_URL . "/admin/dashboard");
            } else {
                header("Location: " . BASE_URL . "/index");
            }

            exit;
        }

        echo "Invalid credentials";
    }


    //for test
//     public function login()
// {
//     $email    = $_POST['email'];
//     $password = $_POST['password'];

//     $pdo  = \Core\Database::getInstance()->getConnection();
//     $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
//     $stmt->execute([$email]);
//     $user = $stmt->fetch(\PDO::FETCH_ASSOC);

//     var_dump($user);
//     var_dump(password_verify($password, $user['password'] ?? ''));
//     die();
// }

    public function logout()
    {
        Auth::logout();
        $this->view('auth/login');
    }
    public function index()
    {
        $this->view('user/home');
    }
}
