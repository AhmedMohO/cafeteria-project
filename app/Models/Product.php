<?php

namespace App\Models;

use Core\Model;

class Product extends Model
{
    protected $table = 'products';

    private const INVALID_IMAGE_VALUES = ['', '?', '??', '???', '�'];
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    public static function resolveStoredImage(string $image = ''): string
    {
        $trimmedImage = trim($image);

        if (!self::isValidStoredImage($trimmedImage)) {
            return '';
        }

        return $trimmedImage;
    }

    public function getAvailableIndexedById(): array
    {
        $productsById = [];

        foreach ($this->searchAvailable('') as $product) {
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
            ->select('id, name, price, COALESCE(NULLIF(image, \'?\'), NULLIF(image, \'\'), \'\') AS image')
            ->where('status', 'available')
            ->orderBy('name', 'ASC');

        if ($search !== '') {
            $qb->whereLike('name', $search);
        }

        return $qb->get();
    }

    private static function isValidStoredImage(string $image): bool
    {
        if (in_array($image, self::INVALID_IMAGE_VALUES, true)) {
            return false;
        }

        if (basename($image) !== $image) {
            return false;
        }

        $extension = strtolower((string) pathinfo($image, PATHINFO_EXTENSION));

        return in_array($extension, self::ALLOWED_EXTENSIONS, true);
    }

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
        return (int) $this->query()->count();
    }

    public function toggleAvailable($id)
    {
        $product = $this->find($id);
        if (!$product) {
            return false;
        }

        $newStatus = ($product['status'] === 'available') ? 'unavailable' : 'available';

        return $this->updateWhere('id', $id, ['status' => $newStatus]);
    }
}