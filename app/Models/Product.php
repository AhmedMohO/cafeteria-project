<?php

namespace App\Models;

use Core\Model;

class Product extends Model
{
    protected $table = "products";

    public static function mapNameToIcon(string $name): string
    {
        $normalized = self::normalizeProductName($name);

        $iconMap = [
            'tea' => '🍵',
            'coffee' => '☕',
            'espresso' => '☕',
            'cappuccino' => '☕',
            'latte' => '☕',
            'mocha' => '☕',
            'chocolate' => '🍫',
            'milk' => '🥛',
            'water' => '💧',
            'juice' => '🧃',
            'cola' => '🥤',
            'soda' => '🥤',
            'sandwich' => '🥪',
            'burger' => '🍔',
            'pizza' => '🍕',
            'fries' => '🍟',
            'chip' => '🍟',
            'cake' => '🍰',
            'donut' => '🍩',
            'cookie' => '🍪',
            'muffin' => '🧁',
            'croissant' => '🥐',
            'salad' => '🥗',
        ];

        foreach ($iconMap as $keyword => $icon) {
            if (str_contains($normalized, $keyword)) {
                return $icon;
            }
        }

        return '☕';
    }

    public static function resolveIcon(string $name, string $icon = ''): string
    {
        $trimmedIcon = trim($icon);
        if (self::isValidIcon($trimmedIcon)) {
            return $trimmedIcon;
        }

        return self::mapNameToIcon($name);
    }

    private static function isValidIcon(string $icon): bool
    {
        if ($icon === '') {
            return false;
        }

        return !in_array($icon, ['?', '??', '???', '�'], true);
    }

    private static function normalizeProductName(string $name): string
    {
        $normalized = strtolower(trim($name));
        if ($normalized === '') {
            return '';
        }

        return (string) preg_replace('/\s+/', ' ', $normalized);
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
}