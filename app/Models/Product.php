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
    $newStatus = $product['status'] === 'available' ? 'unavailable' : 'available';
    
    return $this->updateWhere('id', $id, ['status' => $newStatus]);
}
//start

    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    public static function resolveStoredImage(string $image = ''): string
    {
        $image = trim($image);

        return self::isValidStoredImage($image) ? $image : '';
    }

    public function getAvailableIndexedById(): array
    {
        $productsById = [];

        foreach ($this->searchAvailable() as $product) {
            $id = (int) ($product['id'] ?? 0);
            if ($id > 0) {
                $productsById[$id] = $product;
            }
        }

        return $productsById;
    }

    public function searchAvailable(string $search = ''): array
    {
        $qb = $this->query()
            ->select('id, name, price, image')
            ->where('status', 'available')
            ->orderBy('name', 'ASC');

        if ($search !== '') {
            $qb->whereLike('name', $search);
        }

        return $qb->get();
    }

    public function countAvailable(string $search = ''): int
    {
        $qb = $this->query()
            ->where('status', 'available');

        if ($search !== '') {
            $qb->whereLike('name', $search);
        }

        return (int) $qb->count();
    }

    public function searchAvailablePaginated(string $search = '', int $page = 1, int $perPage = 10): array
    {
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $offset = ($page - 1) * $perPage;

        $qb = $this->query()
            ->select('id, name, price, image')
            ->where('status', 'available')
            ->orderBy('name', 'ASC');

        if ($search !== '') {
            $qb->whereLike('name', $search);
        }

        return $qb->limit($perPage)
            ->offset($offset)
            ->get();
    }

    private static function isValidStoredImage(string $image): bool
    {
        if ($image === '' || preg_match('/^\?+$/', $image) === 1) {
            return false;
        }

        if (basename($image) !== $image) {
            return false;
        }

        $extension = strtolower((string) pathinfo($image, PATHINFO_EXTENSION));
        if (!in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            return false;
        }

        $uploadsPath = dirname(__DIR__, 2) . '/public/uploads/' . $image;

        return is_file($uploadsPath);
    }
}