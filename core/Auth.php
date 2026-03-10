<?php

namespace Core;

use App\Models\User;

class Auth
{
    public static function attempt($email, $password)
    {
        $user = new User();
        $user = $user->where('email', $email)->first();

        if (!$user) {
            return false;
        }

        if (!password_verify($password, $user['password'])) {
            return false;
        }

        self::login($user);

        return true;
    }

    public static function login($user)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['user'] = $user;
    }

    public static function user()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return $_SESSION['user'] ?? null;
    }

    public static function check()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return isset($_SESSION['user']);
    }

    public static function role()
    {
        return self::user()['role'] ?? null;
    }

    public static function isAdmin()
    {
        return self::role() === 'admin';
    }

    public static function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_destroy();
    }
}