<?php

namespace App\Models;

use Core\Model;

class Product extends Model
{
    protected $table = "products";

    private array $columnsCache = [];

    private function hasColumn(string $column): bool
    {
        if (array_key_exists($column, $this->columnsCache)) {
            return $this->columnsCache[$column];
        }

        try {
            $stmt = $this->db->prepare('SHOW COLUMNS FROM products LIKE ?');
            $stmt->execute([$column]);
            $exists = (bool) $stmt->fetch();
            $this->columnsCache[$column] = $exists;
            return $exists;
        } catch (\Throwable $e) {
            $this->columnsCache[$column] = false;
            return false;
        }
    }

    private function defaultProducts(): array
    {
        return [
            ['name' => 'Tea', 'price' => 5.00, 'icon' => '☕'],
            ['name' => 'Coffee', 'price' => 6.50, 'icon' => '☕'],
            ['name' => 'Nescafe', 'price' => 8.00, 'icon' => '☕'],
            ['name' => 'Cola', 'price' => 10.00, 'icon' => '🥤'],
            ['name' => 'Juice', 'price' => 12.00, 'icon' => '🧃'],
            ['name' => 'Water', 'price' => 3.00, 'icon' => '💧'],
            ['name' => 'Milk', 'price' => 7.00, 'icon' => '🥛'],
            ['name' => 'Cappuccino', 'price' => 15.00, 'icon' => '☕'],
        ];
    }

    private function fallbackIconByName(string $name): string
    {
        $key = strtolower(trim($name));

        $map = [
            'tea' => '☕',
            'coffee' => '☕',
            'nescafe' => '☕',
            'cappuccino' => '☕',
            'cola' => '🥤',
            'juice' => '🧃',
            'water' => '💧',
            'milk' => '🥛',
        ];

        return $map[$key] ?? '☕';
    }

    private function sanitizeIconForDb(string $icon): string
    {
        $clean = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '?', $icon);
        $clean = trim((string) $clean);

        return $clean !== '' ? $clean : '?';
    }

    private function normalizeIcons(array $rows): array
    {
        foreach ($rows as &$row) {
            $icon = trim((string) ($row['image'] ?? ''));
            if ($icon === '' || $icon === '?' || $icon === '??' || $icon === '???' || $icon === '�') {
                $row['image'] = $this->fallbackIconByName((string) ($row['name'] ?? ''));
            }
        }
        unset($row);

        return $rows;
    }

    private function seedCurrentSchemaIfEmpty(): void
    {
        $count = (int) $this->db->query('SELECT COUNT(*) FROM products')->fetchColumn();
        if ($count > 0) {
            return;
        }

        $insert = $this->db->prepare(
            'INSERT INTO products (category_id, name, price, icon, status)
             VALUES (NULL, :name, :price, :icon, :status)'
        );

        foreach ($this->defaultProducts() as $product) {
            $dbIcon = $this->sanitizeIconForDb((string) $product['icon']);

            $insert->execute([
                ':name' => $product['name'],
                ':price' => $product['price'],
                ':icon' => $dbIcon,
                ':status' => 'available',
            ]);
        }
    }

    private function seedLegacySchemaIfEmpty(): void
    {
        $count = (int) $this->db->query('SELECT COUNT(*) FROM products')->fetchColumn();
        if ($count > 0) {
            return;
        }

        $insert = $this->db->prepare(
            'INSERT INTO products (name, price, icon)
             VALUES (:name, :price, :icon)'
        );

        foreach ($this->defaultProducts() as $product) {
            $dbIcon = $this->sanitizeIconForDb((string) $product['icon']);

            $insert->execute([
                ':name' => $product['name'],
                ':price' => $product['price'],
                ':icon' => $dbIcon,
            ]);
        }
    }

    private function queryCurrentSchema(string $search): array
    {
        $hasIcon = $this->hasColumn('icon');
        $hasImage = $this->hasColumn('image');
        $hasStatus = $this->hasColumn('status');

        if ($hasIcon && $hasImage) {
            $iconExpr = 'COALESCE(NULLIF(icon, ""), NULLIF(image, ""), "")';
        } elseif ($hasIcon) {
            $iconExpr = 'COALESCE(icon, "")';
        } elseif ($hasImage) {
            $iconExpr = 'COALESCE(image, "")';
        } else {
            $iconExpr = '""';
        }

        $sql = 'SELECT id, name, price, ' . $iconExpr . ' AS image
                FROM products
                WHERE 1=1';

        if ($hasStatus) {
            $sql .= ' AND status = "available"';
        }

        $bindings = [];

        if ($search !== '') {
            $sql .= ' AND name LIKE ?';
            $bindings[] = '%' . $search . '%';
        }

        $sql .= ' ORDER BY name ASC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($bindings);

        return $stmt->fetchAll();
    }

    private function queryLegacySchema(string $search): array
    {
        $sql = 'SELECT id, name, price, COALESCE(icon, "") AS image
                FROM products';

        $bindings = [];

        if ($search !== '') {
            $sql .= ' WHERE name LIKE ?';
            $bindings[] = '%' . $search . '%';
        }

        $sql .= ' ORDER BY name ASC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($bindings);

        return $stmt->fetchAll();
    }

    public function searchAvailable(string $search = ''): array
    {
        try {
            $rows = $this->queryCurrentSchema($search);
            if (!empty($rows)) {
                return $this->normalizeIcons($rows);
            }

            $this->seedCurrentSchemaIfEmpty();
            return $this->normalizeIcons($this->queryCurrentSchema($search));
        } catch (\Throwable $e) {
            try {
                $rows = $this->queryLegacySchema($search);
                if (!empty($rows)) {
                    return $this->normalizeIcons($rows);
                }

                $this->seedLegacySchemaIfEmpty();
                return $this->normalizeIcons($this->queryLegacySchema($search));
            } catch (\Throwable $inner) {
                return [];
            }
        }
    }
}
