<?php

namespace App\Models;

use Core\Model;

class Order extends Model
{
    protected $table = "orders";

    public function getLatestByUserId(int $userId): ?array
    {
        return $this->query()
            ->from('orders o')
            ->select('o.id, o.notes, o.status, o.total_price, o.room_id, r.no AS room_no, r.name AS room_name')
            ->join('LEFT', 'rooms r', 'r.id = o.room_id')
            ->where('o.user_id', $userId)
            ->orderBy('o.id', 'DESC')
            ->first() ?: null;
    }

    public function getItemsByOrderId(int $orderId): array
    {
        return $this->queryTable('order_items')
            ->select('product_id, name, image, price, quantity')
            ->where('order_id', $orderId)
            ->orderBy('id', 'ASC')
            ->get();
    }

    public function countByUserAndDateRange(int $userId, string $dateFrom = '', string $dateTo = ''): int
    {
        $dateExpr = 'COALESCE(o.created_at, o.updated_at)';
        $qb = $this->query()
            ->from('orders o')
            ->where('o.user_id', $userId);

        if ($dateFrom !== '') {
            $qb->whereRaw("{$dateExpr} >= ?", [$dateFrom . ' 00:00:00']);
        }
        if ($dateTo !== '') {
            $qb->whereRaw("{$dateExpr} <= ?", [$dateTo . ' 23:59:59']);
        }

        return (int) $qb->count();
    }

    public function getByUserAndDateRange(int $userId, string $dateFrom, string $dateTo, int $limit, int $offset): array
    {
        $dateExpr = 'COALESCE(o.created_at, o.updated_at)';
        $qb = $this->query()
            ->from('orders o')
            ->select("o.id, o.notes, o.status, o.total_price, {$dateExpr} AS created_at, o.room_id, r.no AS room_no, r.name AS room_name")
            ->join('LEFT', 'rooms r', 'r.id = o.room_id')
            ->where('o.user_id', $userId);

        if ($dateFrom !== '') {
            $qb->whereRaw("{$dateExpr} >= ?", [$dateFrom . ' 00:00:00']);
        }
        if ($dateTo !== '') {
            $qb->whereRaw("{$dateExpr} <= ?", [$dateTo . ' 23:59:59']);
        }

        return $qb->orderBy("{$dateExpr} DESC, o.id", 'DESC')
            ->limit($limit)
            ->offset($offset)
            ->get();
    }

    public function getItemsByOrderIds(array $orderIds): array
    {
        if (empty($orderIds)) {
            return [];
        }

        $itemsByOrder = [];
        foreach ($this->queryTable('order_items')
            ->select('order_id, product_id, name, image, price, quantity')
            ->whereIn('order_id', $orderIds)
            ->orderBy('id', 'ASC')
            ->get() as $item) {
            $itemsByOrder[(int) $item['order_id']][] = $item;
        }

        return $itemsByOrder;
    }

    public function createWithItems(int $userId, int $roomId, string $roomLabel, string $notes, array $items): int
    {
        $totalPrice = array_sum(array_map(
            fn($item) => (float) $item['price'] * (int) $item['quantity'],
            $items
        ));
        $now = date('Y-m-d H:i:s');

        $this->db->beginTransaction();
        try {
            $this->query()->insert([
                'user_id'     => $userId,
                'room_id'     => $roomId > 0 ? $roomId : null,
                'notes'       => $notes,
                'status'      => 'processing',
                'total_price' => $totalPrice,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);

            $orderId = (int) $this->db->lastInsertId();

            foreach ($items as $item) {
                $this->queryTable('order_items')->insert([
                    'order_id'   => $orderId,
                    'product_id' => (int) $item['product_id'],
                    'name'       => (string) $item['name'],
                    'image'      => (string) ($item['image'] ?? ''),
                    'price'      => (float) $item['price'],
                    'quantity'   => (int) $item['quantity'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            $this->db->commit();
            return $orderId;
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function cancelProcessingOrder(int $orderId, int $userId): bool
    {
        $this->db->beginTransaction();
        try {
            $order = $this->query()
                ->select('id')
                ->where('id', $orderId)
                ->where('user_id', $userId)
                ->where('status', 'processing')
                ->first();

            if (!$order) {
                throw new \RuntimeException('Only processing orders can be cancelled.');
            }

            $this->queryTable('order_items')
                ->where('order_id', $orderId)
                ->delete();

            $deleted = $this->query()
                ->where('id', $orderId)
                ->where('user_id', $userId)
                ->delete();

            if ($deleted < 1) {
                throw new \RuntimeException('Failed to cancel the order.');
            }

            $this->db->commit();
            return true;
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }
}
