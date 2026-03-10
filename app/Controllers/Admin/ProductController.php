<?php
namespace App\Controllers\Admin;

use Core\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $p = new Product();
        $products = $p->all();
        $this->view('admin/products', ['products'=>$products]);
    }

    /*
        index
        show
        create
        store
        edit
        update
        delete
    */ 
}
