<?php

namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    private $productModel;
    private $categoryModel;

    public function __construct()
    {
        $this->productModel = new Product();
        $this->categoryModel = new Category();
    }

    public function index()
    {
        $page    = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 10;

        $products = $this->productModel->allWithCategory($page, $perPage);
        $total    = $this->productModel->countAll();
        $pages    = ceil($total / $perPage);

        $this->view('admin/products/index', compact('products', 'page', 'pages'));
    }

    public function create()
    {
        $categories = $this->categoryModel->all();
        $this->view('admin/products/create', compact('categories'));
    }

    public function store()
    {
        $name        = trim($_POST['name']);
        $price       = (float) $_POST['price'];
        $category_id = !empty($_POST['category_id']) ? (int) $_POST['category_id'] : null;

        $image = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $image = $this->uploadImage($_FILES['image']);
        }

        $this->productModel->create([
            'name'        => $name,
            'price'       => $price,
            'category_id' => $category_id,
            'image'       => $image,
            'available'   => 1,
        ]);

        header('Location: /admin/products');
        exit;
    }

    public function edit($id)
    {
        $product    = $this->productModel->find($id);
        $categories = $this->categoryModel->all();

        if (!$product) {
            http_response_code(404);
            echo "Product not found";
            return;
        }

        $this->view('admin/products/edit', compact('product', 'categories'));
    }

    public function update($id)
    {
        $data = [
            'name'        => trim($_POST['name']),
            'price'       => (float) $_POST['price'],
            'category_id' => !empty($_POST['category_id']) ? (int) $_POST['category_id'] : null,
        ];

        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $data['image'] = $this->uploadImage($_FILES['image']);
        }

        $this->productModel->updateWhere('id', $id, $data);

        header('Location: /admin/products');
        exit;
    }

    public function delete($id)
    {
        $this->productModel->deleteWhere('id', $id);

        header('Location: /admin/products');
        exit;
    }

    public function toggle($id)
    {
        $this->productModel->toggleAvailable($id);

        header('Location: /admin/products');
        exit;
    }

    private function uploadImage($file)
    {
        $uploadDir = __DIR__ . '/../../../public/uploads/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($ext, $allowed)) {
            return null;
        }

        $filename = uniqid('product_') . '.' . $ext;
        move_uploaded_file($file['tmp_name'], $uploadDir . $filename);

        return $filename;
    }
}