<?php

namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\User;
use App\Models\Room;

class UserController extends Controller
{
    protected $userOP;
    public function __construct()
    {
        $this->userOP = new User();
    }
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);

        $users = new User();
        $search = trim($_GET['search'] ?? '');
        $page = max(1, (int)($_GET['page'] ?? 1));
        $total = $users->countUsers($search);
        $totalPages = (int)ceil($total / 10);
        $users = $users->getUsers($search, $page, 10);

        $this->view('admin/users', compact('users', 'search', 'page', 'totalPages', 'total', 'error'));
    }

    public function create()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $errors = $_SESSION['errors'] ?? [];
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['errors'], $_SESSION['old']);

        $rooms = (new Room())->all();
        $this->view('admin/add_user', compact('rooms', 'errors', 'old'));
    }

    public function store()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $confirm = trim($_POST['password_confirm'] ?? '');
        $room_id = trim($_POST['room_id'] ?? '');
        $ext = trim($_POST['ext'] ?? '');
        $errors = [];

        if ($name === '') $errors['name'] = 'Name is required.';
        if ($email === '') $errors['email'] = 'Email is required.';
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Invalid email address.';
        elseif ($this->userOP->emailExists($email)) $errors['email'] = 'Email already in use.';
        if ($password === '') $errors['password'] = 'Password is required.';
        elseif (strlen($password) < 6) $errors['password'] = 'Minimum 6 characters.';
        elseif ($password !== $confirm) $errors['password_confirm'] = 'Passwords do not match.';

        if ($errors) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = compact('name', 'email', 'room_id', 'ext');
            header('Location: ' . BASE_URL . '/admin/users/create');
            exit;
        }
        $pic = '';
        if (!empty($_FILES['pic']['name'])) {
            $pic = $this->uploadPic($_FILES['pic']);
        }

        $this->userOP->create([
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'role' => 'user',
            'is_active' => 1,
            'room_id' => $room_id ?: null,
            'ext' => $ext,
            'pic' => $pic,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        header('Location: ' . BASE_URL . '/admin/users');
        exit;
    }

    public function edit()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $id   = (int)($_GET['id'] ?? 0);
        $user = $this->userOP->find($id);

        if (!$user) {
            $_SESSION['error'] = 'User not found.';
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }

        $errors = $_SESSION['errors'] ?? [];
        $old    = $_SESSION['old']    ?? [];
        unset($_SESSION['errors'], $_SESSION['old']);

        $rooms = (new Room())->all();
        $this->view('admin/edit_user', compact('user', 'rooms', 'errors', 'old'));
    }

    public function update()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $confirm = trim($_POST['password_confirm'] ?? '');
        $room_id = trim($_POST['room_id'] ?? '');
        $ext = trim($_POST['ext'] ?? '');
        $errors = [];

        $user = $this->userOP->find($id);
        if (!$user) {
            $_SESSION['error'] = 'User not found.';
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }

        if ($name === '') $errors['name'] = 'Name is required.';
        if ($email === '') $errors['email'] = 'Email is required.';
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Invalid email address.';
        elseif ($this->userOP->emailExists($email, $id)) $errors['email'] = 'Email already in use.';
        if ($password !== '' && strlen($password) < 6) $errors['password'] = 'Minimum 6 characters.';
        elseif ($password !== '' && $password !== $confirm) $errors['password_confirm'] = 'Passwords do not match.';

        if ($errors) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = compact('name', 'email', 'room_id', 'ext');
            header("Location: " . BASE_URL . "/admin/users/edit?id=$id");
            exit;
        }

        $data = [
            'name' => $name,
            'email' => $email,
            'room_id' => $room_id ?: null,
            'ext' => $ext,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($password !== '') {
            $data['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        if (!empty($_FILES['pic']['name'])) {
            if (!empty($user['pic'])) {
                $old = __DIR__ . '/../../../public/' . $user['pic'];
                if (file_exists($old)) @unlink($old);
            }
            $data['pic'] = $this->uploadPic($_FILES['pic']);
        }

        $this->userOP->updateWhere('id', $id, $data);

        header('Location: ' . BASE_URL . '/admin/users');
        exit;
    }

    public function delete()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $id = (int)($_POST['id'] ?? 0);
        $user = $this->userOP->find($id);

        if (!$user) {
            $_SESSION['error'] = 'User not found.';
            header('Location: ' . BASE_URL . '/admin/users');
            exit;
        }

        if ($this->userOP->hasOrders($id)) {
            $this->userOP->softDelete($id);
        } else {
            if (!empty($user['pic'])) {
                $path = __DIR__ . '/../../../public/' . $user['pic'];
                if (file_exists($path)) @unlink($path);
            }
            $this->userOP->hardDelete($id);
        }

        header('Location: ' . BASE_URL . '/admin/users');
        exit;
    }

    private function uploadPic(array $file): string
    {
        $uploadDir = __DIR__.'/../../../public/uploads/profiles/';
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = uniqid('user_').'.'.$ext;
        move_uploaded_file($file['tmp_name'], $uploadDir.$filename);

        return 'uploads/profiles/'.$filename;
    }
}
