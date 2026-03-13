<?php

namespace App\Models;

use Core\Model;

class Order extends Model
{
    protected $table = "orders";

    private function sanitizeIconForDb(string $icon): string
    {
        // MySQL utf8 (3-byte) cannot store 4-byte emoji chars.
        $clean = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '?', $icon);
        $clean = trim((string) $clean);

        return $clean !== '' ? $clean : '?';
    }

    private function filterDateExpression(): string
    {
        return 'COALESCE(o.created_at, o.updated_at, NOW())';
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

    private function normalizeItems(array $items): array
    {
        foreach ($items as &$item) {
            $icon = trim((string) ($item['image'] ?? ''));
            if ($icon === '' || $icon === '?' || $icon === '??' || $icon === '???' || $icon === '�') {
                $item['image'] = $this->fallbackIconByName((string) ($item['name'] ?? ''));
            }
        }
        unset($item);

        return $items;
    }

    private function backfillOrderCreatedAt(int $orderId): void
    {
        try {
            $stmt = $this->db->prepare('UPDATE orders SET created_at = NOW() WHERE id = ? AND created_at IS NULL');
            $stmt->execute([$orderId]);
        } catch (\Throwable $e) {
            // Ignore when schema doesn't have created_at.
        }
    }

    public function getLatestByUserId(int $userId): ?array
    {
        try {
            $stmt = $this->db->prepare(
                'SELECT o.id, o.notes, o.status, o.total_price, o.room_id, r.no AS room_no, r.name AS room_name
                 FROM orders o
                 LEFT JOIN rooms r ON r.id = o.room_id
                 WHERE o.user_id = ?
                 ORDER BY o.id DESC
                 LIMIT 1'
            );
            $stmt->execute([$userId]);

            $row = $stmt->fetch();
            return $row ?: null;
        } catch (\Throwable $e) {
            $stmt = $this->db->prepare(
                'SELECT id, notes, status, total AS total_price, room AS room_no
                 FROM orders
                 WHERE user_id = ?
                 ORDER BY id DESC
                 LIMIT 1'
            );
            $stmt->execute([$userId]);
            $row = $stmt->fetch();

            if (!$row) {
                return null;
            }

            $row['room_id'] = 0;
            $row['room_name'] = '';

            return $row;
        }
    }

    public function getItemsByOrderId(int $orderId): array
    {
        try {
            $stmt = $this->db->prepare(
                'SELECT product_id, name, image, price, quantity
                 FROM order_items
                 WHERE order_id = ?
                 ORDER BY id ASC'
            );
            $stmt->execute([$orderId]);
            return $this->normalizeItems($stmt->fetchAll());
        } catch (\Throwable $e) {
            $stmt = $this->db->prepare(
                'SELECT 0 AS product_id, product_name AS name, product_icon AS image, unit_price AS price, quantity
                 FROM order_items
                 WHERE order_id = ?
                 ORDER BY id ASC'
            );
            $stmt->execute([$orderId]);
            return $this->normalizeItems($stmt->fetchAll());
        }
    }

    public function countByUserAndDateRange(int $userId, string $dateFrom = '', string $dateTo = ''): int
    {
        $dateExpr = $this->filterDateExpression();
        $whereParts = ['o.user_id = :user_id'];
        $params = [':user_id' => $userId];

        if ($dateFrom !== '') {
            $whereParts[] = $dateExpr . ' >= :date_from';
            $params[':date_from'] = $dateFrom . ' 00:00:00';
        }

        if ($dateTo !== '') {
            $whereParts[] = $dateExpr . ' <= :date_to';
            $params[':date_to'] = $dateTo . ' 23:59:59';
        }

        $sql = 'SELECT COUNT(*) FROM orders o WHERE ' . implode(' AND ', $whereParts);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    public function getByUserAndDateRange(int $userId, string $dateFrom, string $dateTo, int $limit, int $offset): array
    {
        $dateExpr = $this->filterDateExpression();
        $whereParts = ['o.user_id = :user_id'];
        $params = [':user_id' => $userId];

        if ($dateFrom !== '') {
            $whereParts[] = $dateExpr . ' >= :date_from';
            $params[':date_from'] = $dateFrom . ' 00:00:00';
        }

        if ($dateTo !== '') {
            $whereParts[] = $dateExpr . ' <= :date_to';
            $params[':date_to'] = $dateTo . ' 23:59:59';
        }

        try {
                    $sql = 'SELECT o.id, o.notes, o.status, o.total_price, ' . $dateExpr . ' AS created_at, o.room_id, r.no AS room_no, r.name AS room_name
                    FROM orders o
                    LEFT JOIN rooms r ON r.id = o.room_id
                    WHERE ' . implode(' AND ', $whereParts) . '
                        ORDER BY ' . $dateExpr . ' DESC
                    LIMIT :limit OFFSET :offset';

            $stmt = $this->db->prepare($sql);

            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch (\Throwable $e) {
                    $sql = 'SELECT o.id, o.notes, o.status, o.total AS total_price, COALESCE(o.created_at, NOW()) AS created_at, o.room AS room_no
                    FROM orders o
                    WHERE ' . implode(' AND ', $whereParts) . '
                        ORDER BY COALESCE(o.created_at, NOW()) DESC
                    LIMIT :limit OFFSET :offset';

            $stmt = $this->db->prepare($sql);

            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
            $stmt->execute();

            $rows = $stmt->fetchAll();
            foreach ($rows as &$row) {
                $row['room_id'] = 0;
                $row['room_name'] = '';
            }
            unset($row);

            return $rows;
        }
    }

    public function getItemsByOrderIds(array $orderIds): array
    {
        if (empty($orderIds)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($orderIds), '?'));
        try {
            $stmt = $this->db->prepare(
                'SELECT order_id, product_id, name, image, price, quantity
                 FROM order_items
                 WHERE order_id IN (' . $placeholders . ')
                 ORDER BY id ASC'
            );
            $stmt->execute($orderIds);
        } catch (\Throwable $e) {
            $stmt = $this->db->prepare(
                'SELECT order_id, 0 AS product_id, product_name AS name, product_icon AS image, unit_price AS price, quantity
                 FROM order_items
                 WHERE order_id IN (' . $placeholders . ')
                 ORDER BY id ASC'
            );
            $stmt->execute($orderIds);
        }

        $itemsByOrder = [];
        foreach ($stmt->fetchAll() as $item) {
            $orderId = (int) $item['order_id'];
            if (!isset($itemsByOrder[$orderId])) {
                $itemsByOrder[$orderId] = [];
            }
            $normalized = $this->normalizeItems([$item]);
            $itemsByOrder[$orderId][] = $normalized[0];
        }

        return $itemsByOrder;
    }

    public function createWithItems(int $userId, int $roomId, string $roomLabel, string $notes, array $items): int
    {
        $totalPrice = 0.0;
        foreach ($items as $item) {
            $totalPrice += ((float) $item['price']) * ((int) $item['quantity']);
        }

        try {
            $this->db->beginTransaction();

            $insertOrder = $this->db->prepare(
                'INSERT INTO orders (user_id, room_id, notes, status, total_price)
                 VALUES (:user_id, :room_id, :notes, :status, :total_price)'
            );

            $insertOrder->execute([
                ':user_id' => $userId,
                ':room_id' => $roomId > 0 ? $roomId : null,
                ':notes' => $notes,
                ':status' => 'processing',
                ':total_price' => $totalPrice,
            ]);

            $orderId = (int) $this->db->lastInsertId();

            $insertItem = $this->db->prepare(
                'INSERT INTO order_items (order_id, product_id, name, image, price, quantity)
                 VALUES (:order_id, :product_id, :name, :image, :price, :quantity)'
            );

            foreach ($items as $item) {
                $dbIcon = $this->sanitizeIconForDb((string) ($item['image'] ?? ''));

                $insertItem->execute([
                    ':order_id' => $orderId,
                    ':product_id' => (int) $item['product_id'],
                    ':name' => (string) $item['name'],
                    ':image' => $dbIcon,
                    ':price' => (float) $item['price'],
                    ':quantity' => (int) $item['quantity'],
                ]);
            }

            $this->db->commit();
            $this->backfillOrderCreatedAt($orderId);
            return $orderId;
        } catch (\Throwable $primaryError) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            try {
                $this->db->beginTransaction();

                $insertOrder = $this->db->prepare(
                    'INSERT INTO orders (user_id, room, notes, status, total)
                     VALUES (:user_id, :room, :notes, :status, :total)'
                );

                $insertOrder->execute([
                    ':user_id' => $userId,
                    ':room' => $roomLabel,
                    ':notes' => $notes,
                    ':status' => 'Processing',
                    ':total' => $totalPrice,
                ]);

                $orderId = (int) $this->db->lastInsertId();

                $insertItem = $this->db->prepare(
                    'INSERT INTO order_items (order_id, product_name, product_icon, unit_price, quantity, line_total)
                     VALUES (:order_id, :product_name, :product_icon, :unit_price, :quantity, :line_total)'
                );

                foreach ($items as $item) {
                    $qty = (int) $item['quantity'];
                    $price = (float) $item['price'];
                    $lineTotal = $price * $qty;
                    $dbIcon = $this->sanitizeIconForDb((string) ($item['image'] ?? ''));

                    $insertItem->execute([
                        ':order_id' => $orderId,
                        ':product_name' => (string) $item['name'],
                        ':product_icon' => $dbIcon,
                        ':unit_price' => $price,
                        ':quantity' => $qty,
                        ':line_total' => $lineTotal,
                    ]);
                }

                $this->db->commit();
                $this->backfillOrderCreatedAt($orderId);
                return $orderId;
            } catch (\Throwable $fallbackError) {
                if ($this->db->inTransaction()) {
                    $this->db->rollBack();
                }
                throw $fallbackError;
            }
        }
    }

    public function cancelProcessingOrder(int $orderId, int $userId): bool
    {
        $this->db->beginTransaction();

        try {
                        $orderCheck = $this->db->prepare(
                                'SELECT id
                                 FROM orders
                                 WHERE id = :order_id
                                     AND user_id = :user_id
                                     AND LOWER(status) = "processing"
                                 LIMIT 1'
                        );
                        $orderCheck->execute([
                                ':order_id' => $orderId,
                                ':user_id' => $userId,
                        ]);

            if (!$orderCheck->fetchColumn()) {
                throw new \RuntimeException('Only processing orders can be canceled.');
            }

            $deleteItems = $this->db->prepare('DELETE FROM order_items WHERE order_id = :order_id');
            $deleteItems->execute([':order_id' => $orderId]);

            $deleteOrder = $this->db->prepare(
                'DELETE FROM orders WHERE id = :order_id AND user_id = :user_id'
            );
            $deleteOrder->execute([
                ':order_id' => $orderId,
                ':user_id' => $userId,
            ]);

            if ($deleteOrder->rowCount() !== 1) {
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
