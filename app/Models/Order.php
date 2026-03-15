<?php

namespace App\Models;

use Core\Model;

class Order extends Model
{
    protected $table = 'orders';

    private const DATE_EXPR = 'COALESCE(orders.created_at, orders.updated_at)';

    private function applyDateRangeFilters($qb, string $dateFrom = '', string $dateTo = ''): void
    {
        if ($dateFrom !== '') {
            $qb->whereRaw(self::DATE_EXPR . ' >= ?', [$dateFrom . ' 00:00:00']);
        }

        if ($dateTo !== '') {
            $qb->whereRaw(self::DATE_EXPR . ' <= ?', [$dateTo . ' 23:59:59']);
        }
    }

    private function applyStatusFilter($qb, string $status = ''): void
    {
        if ($status !== '') {
            $qb->where('orders.status', $status);
        }
    }

    public function getLatestByUserId(int $userId): ?array
    {
        return $this->query()
            ->select('orders.id, orders.notes, orders.status, orders.total_price, orders.room_id, rooms.no AS room_no, rooms.name AS room_name')
            ->join('rooms', 'rooms.id', '=', 'orders.room_id')
            ->where('orders.user_id', $userId)
            ->orderBy('orders.id', 'DESC')
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

    public function countByUserAndDateRange(int $userId, string $dateFrom = '', string $dateTo = '', string $status = ''): int
    {
        $qb = $this->query()
            ->where('orders.user_id', $userId);

        $this->applyDateRangeFilters($qb, $dateFrom, $dateTo);
        $this->applyStatusFilter($qb, $status);

        return (int) $qb->count();
    }

    public function getByUserAndDateRange(int $userId, string $dateFrom, string $dateTo, int $limit, int $offset, string $status = ''): array
    {
        $qb = $this->query()
            ->select('orders.id, orders.notes, orders.status, orders.total_price, ' . self::DATE_EXPR . ' AS created_at, orders.room_id, rooms.no AS room_no, rooms.name AS room_name')
            ->join('rooms', 'rooms.id', '=', 'orders.room_id')
            ->where('orders.user_id', $userId);

        $this->applyDateRangeFilters($qb, $dateFrom, $dateTo);
        $this->applyStatusFilter($qb, $status);

        return $qb->orderBy('created_at', 'DESC')
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

    public function createWithItems(int $userId, int $roomId, string $notes, array $items): int
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
