<?php

namespace App\Models;

use Core\Model;
use Core\Database;

class Product extends Model
{
    protected $table = 'products';

    public function allWithCategory($page = 1, $perPage = 10)
    {
        $offset = ($page - 1) * $perPage;

        return $this->query()
            ->select(['products.*', 'categories.name as category_name'])
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->limit($perPage)
            ->offset($offset)
            ->get();
    }

    public function countAll()
    {
        return $this->query()->count();
    }

    //*- Toggle Available us update
    public function toggleAvailable($id)
    {
    $product = $this->find($id);
    $newValue = $product['available'] ? 0 : 1;
    
    return $this->updateWhere('id', $id, ['available' => $newValue]);
    }
}