<?php

namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\Category;

class CategoryController extends Controller
{
    private $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new Category();
    }

    public function store()
    {
        $name = trim($_POST['name']);
        if ($name) {
            $this->categoryModel->create(['name' => $name]);
        }
        header('Location:' . BASE_URL . '/admin/categories');
        exit;
    }
    
    public function delete($id)
    {
        $this->categoryModel->deleteWhere('id', $id);
        header('Location: ' . BASE_URL . '/admin/categories');
        exit;
    }

    public function index()
{
    $categories = $this->categoryModel->all();
    $this->view('admin/categories/index', compact('categories'));
}
}