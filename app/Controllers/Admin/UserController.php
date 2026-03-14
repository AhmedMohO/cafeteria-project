<?php

namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\User;

class UserController extends Controller
{
    private const PER_PAGE = 10;

    private function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index()
    {
        $this->startSession();

        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);

        $userModel = new User();

        $search = trim($_GET['search'] ?? '');
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = self::PER_PAGE;

        $total = $userModel->countUsers($search);
        $totalPages = (int) ceil($total / $perPage);
        $users = $userModel->getUsers($search, $page, $perPage);

        $this->view('admin/users', [
            'users' => $users,
            'search' => $search,
            'page' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
            'error' => $error,
        ]);
    }

    public function delete()
    {
        $this->startSession();
        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            $this->redirectWithError('/admin/users', 'Invalid user ID.');
            return;
        }

        $userModel = new User();
        $user = $userModel->find($id);

        if (!$user) {
            $this->redirectWithError('/admin/users', 'User not found.');
            return;
        }

        if ($userModel->hasOrders($id)) {
            $userModel->softDelete($id);
        } else {
            $userModel->hardDelete($id);
        }

        // Delete profile picture if it's a local file
        if (!empty($user['pic'])) {
            $picPath = __DIR__ . '/../../../../public/' . ltrim($user['pic'], '/');
            if (file_exists($picPath)) {
                @unlink($picPath);
            }
        }

        header('Location: ' . base_url('/admin/users'));
        exit;
    }

    private function redirectWithError(string $location, string $message): void
    {
        $this->startSession();
        $_SESSION['error'] = $message;
        header("Location: $location");
        exit;
    }
}
