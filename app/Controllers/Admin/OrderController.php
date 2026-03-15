<?php

namespace App\Controllers\Admin;

use Core\Controller;
use Core\Database;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use PDO;

class OrderController extends Controller
{
    private const PER_PAGE = 10;

    private function db(): PDO
    {
        return Database::getInstance()->getConnection();
    }

    private function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // =========================================================================
    // GET /admin/dashboard
    // =========================================================================
    public function dashboard(): void
    {
        $this->startSession();
        $pdo = $this->db();

        $row = $pdo->query("
            SELECT COUNT(*) AS cnt, COALESCE(SUM(total_price), 0) AS rev
            FROM orders
            WHERE DATE(created_at) = CURDATE()
        ")->fetch(PDO::FETCH_ASSOC);

        $todayOrders  = (int) $row['cnt'];
        $todayRevenue = number_format((float) $row['rev'], 2);

        $pendingDeliveries = (int) $pdo->query("
            SELECT COUNT(*) FROM orders
            WHERE status IN ('processing', 'out_for_delivery')
        ")->fetchColumn();

        $totalUsers = (new User())->where('role', 'user')->count();

        $this->view('admin/dashboard', compact(
            'todayOrders', 'todayRevenue', 'totalUsers', 'pendingDeliveries'
        ));
    }

    // =========================================================================
    // GET /admin/orders
    // =========================================================================
    public function index(): void
    {
        $this->startSession();
        $pdo = $this->db();

        $currentPage = max(1, (int) ($_GET['page'] ?? 1));
        $offset      = ($currentPage - 1) * self::PER_PAGE;

        $total = (int) $pdo->query("
            SELECT COUNT(*) FROM orders
            WHERE status IN ('processing', 'out_for_delivery')
        ")->fetchColumn();

        $totalPages = (int) ceil($total / self::PER_PAGE);

        $stmt = $pdo->prepare("
            SELECT  o.id,
                    o.total_price,
                    o.status,
                    o.notes,
                    o.created_at,
                    u.name,
                    u.ext,
                    r.no AS room_no
            FROM    orders o
            JOIN    users u    ON u.id = o.user_id
            LEFT JOIN rooms r  ON r.id = o.room_id
            WHERE   o.status IN ('processing', 'out_for_delivery')
            ORDER BY o.created_at ASC
            LIMIT   :limit OFFSET :offset
        ");
        $stmt->bindValue(':limit',  self::PER_PAGE, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset,        PDO::PARAM_INT);
        $stmt->execute();
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($orders)) {
            $ids          = array_column($orders, 'id');
            $placeholders = implode(',', array_fill(0, count($ids), '?'));

            $itemStmt = $pdo->prepare("
                SELECT oi.order_id,
                       oi.name     AS product_name,
                       oi.price,
                       oi.quantity
                FROM   order_items oi
                WHERE  oi.order_id IN ($placeholders)
                ORDER BY oi.id ASC
            ");
            $itemStmt->execute($ids);

            $itemMap = [];
            foreach ($itemStmt->fetchAll(PDO::FETCH_ASSOC) as $item) {
                $itemMap[$item['order_id']][] = $item;
            }
            foreach ($orders as &$order) {
                $order['items'] = $itemMap[$order['id']] ?? [];
            }
            unset($order);
        }

        $this->view('admin/orders', compact('orders', 'totalPages', 'currentPage'));
    }

    // =========================================================================
    // POST /admin/mark-delivered
    // =========================================================================
    public function deliver(): void
    {
        $this->startSession();
        $orderId = (int) ($_POST['order_id'] ?? 0);

        if ($orderId > 0) {
            (new Order())
                ->where('id', $orderId)
                ->update(['status' => 'done', 'updated_at' => date('Y-m-d H:i:s')]);

            $_SESSION['success'] = "Order #$orderId marked as delivered.";
        } else {
            $_SESSION['error'] = 'Invalid order ID.';
        }

        header('Location: ' . BASE_URL . '/admin/orders');
        exit;
    }

    // =========================================================================
    // GET /admin/manual-order
    // =========================================================================
    public function manualForm(): void
    {
        $this->startSession();
        $pdo = $this->db();

        $users = (new User())
            ->where('role', 'user')
            ->orderBy('name', 'ASC')
            ->get();

        $products = $pdo->query("
            SELECT  p.id, p.name, p.price, p.image,
                    c.name AS category_name
            FROM    products p
            LEFT JOIN categories c ON c.id = p.category_id
            WHERE   p.status = 'available'
            ORDER BY c.name ASC, p.name ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        $rooms = $pdo->query("
            SELECT id, no, name FROM rooms ORDER BY no ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        $this->view('admin/manual_order', compact('users', 'products', 'rooms')); // ✅ underscore
    }

    // =========================================================================
    // POST /admin/place-order
    // =========================================================================
    public function manualStore(): void
    {
        $this->startSession();
        $pdo = $this->db();

        $targetUserId = (int) ($_POST['user_id'] ?? 0);
        $roomId       = (int) ($_POST['room_id'] ?? 0) ?: null;
        $notes        = trim($_POST['notes'] ?? '');
        $items        = $_POST['items'] ?? [];

        if ($targetUserId <= 0) {
            $_SESSION['error'] = 'Please select a user.';
            header('Location: ' . BASE_URL . '/admin/manual-order'); // ✅ hyphen
            exit;
        }

        $selectedItems = [];
        foreach ($items as $productId => $qty) {
            $qty = (int) $qty;
            if ($qty > 0) {
                $selectedItems[(int) $productId] = $qty;
            }
        }

        if (empty($selectedItems)) {
            $_SESSION['error'] = 'Please select at least one product.';
            header('Location: ' . BASE_URL . '/admin/manual-order'); // ✅ hyphen
            exit;
        }

        $placeholders = implode(',', array_fill(0, count($selectedItems), '?'));
        $prodStmt = $pdo->prepare("
            SELECT id, name, price FROM products
            WHERE  id IN ($placeholders) AND status = 'available'
        ");
        $prodStmt->execute(array_keys($selectedItems));
        $products = $prodStmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($products)) {
            $_SESSION['error'] = 'No valid products selected.';
            header('Location: ' . BASE_URL . '/admin/manual-order'); // ✅ hyphen
            exit;
        }

        if (!$roomId) {
            $userModel = (new User())->where('id', $targetUserId)->first();
            $roomId    = $userModel['room_id'] ?? null;
        }

        $totalPrice = 0.0;
        foreach ($products as $p) {
            $totalPrice += (float) $p['price'] * $selectedItems[$p['id']];
        }

        $pdo->beginTransaction();
        try {
            (new Order())->create([
                'user_id'     => $targetUserId,
                'room_id'     => $roomId,
                'total_price' => $totalPrice,
                'status'      => 'processing',
                'notes'       => $notes ?: null,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ]);

            $orderId = (int) $pdo->lastInsertId();

            $itemStmt = $pdo->prepare("
                INSERT INTO order_items
                    (order_id, product_id, name, price, quantity, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, NOW(), NOW())
            ");
            foreach ($products as $p) {
                $itemStmt->execute([
                    $orderId,
                    $p['id'],
                    $p['name'],
                    $p['price'],
                    $selectedItems[$p['id']],
                ]);
            }

            $pdo->commit();
            $_SESSION['success'] = "Order #$orderId placed successfully!";

        } catch (\Exception $e) {
            $pdo->rollBack();
            $_SESSION['error'] = 'Failed to place order. Please try again.';
        }

        header('Location: ' . BASE_URL . '/admin/manual-order'); // ✅ hyphen
        exit;
    }

    // =========================================================================
    // GET /admin/checks
    // =========================================================================
    public function checks(): void
    {
        $this->startSession();
        $pdo = $this->db();

        $dateFrom = $_GET['date_from'] ?? '';
        $dateTo   = $_GET['date_to']   ?? '';
        $userId   = (int) ($_GET['user_id'] ?? 0);

        $where  = "WHERE o.status = 'done'";
        $params = [];

        if ($dateFrom !== '') { $where .= " AND DATE(o.created_at) >= ?"; $params[] = $dateFrom; }
        if ($dateTo   !== '') { $where .= " AND DATE(o.created_at) <= ?"; $params[] = $dateTo; }

        $summaryStmt = $pdo->prepare("
            SELECT  u.id,
                    u.name,
                    COUNT(o.id)        AS order_count,
                    SUM(o.total_price) AS total_spent
            FROM    orders o
            JOIN    users u ON u.id = o.user_id
            $where
            GROUP BY o.user_id, u.id, u.name
            ORDER BY total_spent DESC
        ");
        $summaryStmt->execute($params);
        $usersSummary = $summaryStmt->fetchAll(PDO::FETCH_ASSOC);

        $selectedUser = null;
        $orders       = [];
        $grandTotal   = '0.00';
        $currentPage  = 1;
        $totalPages   = 1;

        if ($userId > 0) {
            $selectedUser = (new User())->where('id', $userId)->first() ?: null;

            if ($selectedUser) {
                $userWhere  = $where . " AND o.user_id = ?";
                $userParams = array_merge($params, [$userId]);

                $gtStmt = $pdo->prepare("
                    SELECT COALESCE(SUM(o.total_price), 0)
                    FROM   orders o $userWhere
                ");
                $gtStmt->execute($userParams);
                $grandTotal = number_format((float) $gtStmt->fetchColumn(), 2);

                $currentPage = max(1, (int) ($_GET['page'] ?? 1));
                $offset      = ($currentPage - 1) * self::PER_PAGE;

                $countStmt = $pdo->prepare("SELECT COUNT(*) FROM orders o $userWhere");
                $countStmt->execute($userParams);
                $totalPages = (int) ceil((int) $countStmt->fetchColumn() / self::PER_PAGE);

                $limit = self::PER_PAGE;
                $ordStmt = $pdo->prepare("
                    SELECT o.id, o.total_price, o.created_at, o.notes
                    FROM   orders o
                    $userWhere
                    ORDER BY o.created_at DESC
                    LIMIT $limit OFFSET $offset
                ");
                $ordStmt->execute($userParams);
                $orders = $ordStmt->fetchAll(PDO::FETCH_ASSOC);

                if (!empty($orders)) {
                    $ids          = array_column($orders, 'id');
                    $placeholders = implode(',', array_fill(0, count($ids), '?'));

                    $itStmt = $pdo->prepare("
                        SELECT oi.order_id,
                               oi.name     AS product_name,
                               oi.price,
                               oi.quantity
                        FROM   order_items oi
                        WHERE  oi.order_id IN ($placeholders)
                    ");
                    $itStmt->execute($ids);

                    $itemMap = [];
                    foreach ($itStmt->fetchAll(PDO::FETCH_ASSOC) as $item) {
                        $itemMap[$item['order_id']][] = $item;
                    }
                    foreach ($orders as &$ord) {
                        $ord['items'] = $itemMap[$ord['id']] ?? [];
                    }
                    unset($ord);
                }
            }
        }

        $this->view('admin/checks', compact(
            'usersSummary', 'dateFrom', 'dateTo', 'userId',
            'selectedUser', 'orders', 'grandTotal',
            'totalPages', 'currentPage'
        ));
    }
}