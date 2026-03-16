<?php

namespace App\Controllers;

use Core\Auth;
use Core\Controller;
use Core\Validator;

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
        $errors = Validator::validate($_POST, [
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6']
        ]);
        if (!empty($errors)) {
            $this->view('auth/login', ['errors' => $errors]);
            return;
        }
        if (Auth::attempt($email, $password)) {
            if(Auth::user()['is_active'] == 0) {
                Auth::logout();
                $this->view('auth/login', ['errors' => ['Your account is inactive. Please contact support.']]);
                return;
            }
            if (Auth::role() === 'admin') {
                $this->view("/admin/dashboard");
            } else {
                $this->view("/user/home");
            }
            exit;
        }
        $this->view('auth/login', ['errors' => ['Invalid email or password']]);
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
