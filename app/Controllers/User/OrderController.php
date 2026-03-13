<?php

namespace App\Controllers\User;

use Core\Controller;
use Core\Auth;
use App\Models\Order;
use App\Models\Product;
use App\Models\Room;

class OrderController extends Controller
{
	private const PER_PAGE = 5;

	private function currentUserId(): int
	{
		$user = Auth::user();
		return (int) ($user['id'] ?? 1);
	}

	private function appUrl(string $path): string
	{
		$base = defined('APP_BASE_PATH') ? APP_BASE_PATH : '';
		$normalizedPath = '/' . ltrim($path, '/');

		if ($base === '') {
			return $normalizedPath;
		}

		return $base . $normalizedPath;
	}

	private function redirectQuery(
		int $page,
		string $dateFrom,
		string $dateTo,
		string $alertType = '',
		string $alertMessage = ''
	): string {
		$query = ['page' => $page];

		if ($dateFrom !== '') {
			$query['date_from'] = $dateFrom;
		}
		if ($dateTo !== '') {
			$query['date_to'] = $dateTo;
		}
		if ($alertType !== '') {
			$query['alert_type'] = $alertType;
		}
		if ($alertMessage !== '') {
			$query['alert_message'] = $alertMessage;
		}

		return $this->appUrl('/user/my-orders') . '?' . http_build_query($query);
	}

	public function latestOrder(): void
	{
		header('Content-Type: application/json; charset=utf-8');

		try {
			$orderModel = new Order();
			$productModel = new Product();
			$latestOrder = $orderModel->getLatestByUserId($this->currentUserId());

			if (!$latestOrder) {
				echo json_encode(['success' => true, 'order' => null]);
				return;
			}

			$productsByName = [];
			foreach ($productModel->searchAvailable('') as $product) {
				$nameKey = strtolower(trim((string) ($product['name'] ?? '')));
				if ($nameKey !== '') {
					$productsByName[$nameKey] = (int) ($product['id'] ?? 0);
				}
			}

			$items = [];
			foreach ($orderModel->getItemsByOrderId((int) $latestOrder['id']) as $row) {
				$name = (string) ($row['name'] ?? '');
				$productId = (int) ($row['product_id'] ?? 0);

				if ($productId < 1) {
					$nameKey = strtolower(trim($name));
					$productId = (int) ($productsByName[$nameKey] ?? 0);
				}

				$items[] = [
					'id' => $productId,
					'name' => $name,
					'icon' => (string) ($row['image'] ?? ''),
					'price' => (float) ($row['price'] ?? 0),
					'quantity' => (int) ($row['quantity'] ?? 0),
				];
			}

			echo json_encode([
				'success' => true,
				'order' => [
					'id' => (int) $latestOrder['id'],
					'room' => trim(((string) ($latestOrder['room_no'] ?? '')) . (isset($latestOrder['room_name']) && $latestOrder['room_name'] !== '' ? ' - ' . (string) $latestOrder['room_name'] : '')),
					'room_id' => (int) ($latestOrder['room_id'] ?? 0),
					'notes' => (string) ($latestOrder['notes'] ?? ''),
					'total' => (float) ($latestOrder['total_price'] ?? 0),
					'status' => (string) ($latestOrder['status'] ?? ''),
					'items' => $items,
				],
			]);
		} catch (\Throwable $e) {
			http_response_code(500);
			echo json_encode([
				'success' => false,
				'message' => 'Failed to load latest order',
			]);
		}
	}

	public function placeOrder(): void
	{
		header('Content-Type: application/json; charset=utf-8');

		if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
			http_response_code(405);
			echo json_encode(['success' => false, 'message' => 'Method not allowed']);
			return;
		}

		$rawBody = file_get_contents('php://input');
		$data = json_decode($rawBody, true);

		if (!is_array($data)) {
			http_response_code(400);
			echo json_encode(['success' => false, 'message' => 'Invalid JSON payload']);
			return;
		}

		$items = $data['items'] ?? [];
		$notes = trim((string) ($data['notes'] ?? ''));
		$roomLabel = trim((string) ($data['room'] ?? ''));
		$roomId = (int) ($data['room_id'] ?? 0);

		if (!is_array($items) || count($items) === 0) {
			http_response_code(400);
			echo json_encode(['success' => false, 'message' => 'Order must contain at least one item']);
			return;
		}

		if ($roomId < 1 && $roomLabel === '') {
			http_response_code(400);
			echo json_encode(['success' => false, 'message' => 'Please select a room']);
			return;
		}

		$productModel = new Product();
		$roomModel = new Room();
		$orderModel = new Order();
		$rooms = $roomModel->allRooms();

		$roomsById = [];
		foreach ($rooms as $room) {
			$value = (string) ($room['value'] ?? '');
			if (ctype_digit($value)) {
				$roomsById[(int) $value] = $room;
			}
		}

		if ($roomLabel === '' && $roomId > 0) {
			if (isset($roomsById[$roomId])) {
				$roomLabel = (string) ($roomsById[$roomId]['label'] ?? $roomId);
			}
		}

		if ($roomId < 1 && $roomLabel !== '') {
			$roomLabelLower = strtolower($roomLabel);
			foreach ($roomsById as $id => $room) {
				$label = strtolower((string) ($room['label'] ?? ''));
				$no = strtolower((string) ($room['no'] ?? ''));

				if ($label === $roomLabelLower || $no === $roomLabelLower || ($no !== '' && str_contains($roomLabelLower, $no))) {
					$roomId = (int) $id;
					if ($roomLabel === '') {
						$roomLabel = (string) ($room['label'] ?? $id);
					}
					break;
				}
			}
		}

		try {
			$availableProducts = $productModel->searchAvailable('');
			$productsById = [];
			foreach ($availableProducts as $product) {
				$productsById[(int) $product['id']] = $product;
			}

			$normalizedItems = [];
			foreach ($items as $item) {
				$productId = (int) ($item['id'] ?? 0);
				$quantity = (int) ($item['quantity'] ?? 0);

				if ($productId < 1 || $quantity < 1 || !isset($productsById[$productId])) {
					http_response_code(400);
					echo json_encode(['success' => false, 'message' => 'Invalid item data']);
					return;
				}

				$product = $productsById[$productId];
				$price = (float) ($product['price'] ?? 0);
				if ($price <= 0) {
					http_response_code(400);
					echo json_encode(['success' => false, 'message' => 'Invalid product price']);
					return;
				}

				$normalizedItems[] = [
					'product_id' => $productId,
					'name' => (string) ($product['name'] ?? ''),
					'image' => (string) ($product['image'] ?? ''),
					'price' => $price,
					'quantity' => $quantity,
				];
			}

			$orderId = $orderModel->createWithItems(
				$this->currentUserId(),
				$roomId,
				$roomLabel,
				$notes,
				$normalizedItems
			);

			echo json_encode([
				'success' => true,
				'message' => 'Order placed successfully',
				'order_id' => $orderId,
			]);
		} catch (\Throwable $e) {
			error_log('placeOrder failed: ' . $e->getMessage());
			http_response_code(500);
			echo json_encode(['success' => false, 'message' => 'Failed to save order: ' . $e->getMessage()]);
		}
	}

	public function myOrders(): void
	{
		$dateFrom = trim((string) ($_REQUEST['date_from'] ?? ''));
		$dateTo = trim((string) ($_REQUEST['date_to'] ?? ''));
		$page = isset($_REQUEST['page']) ? max(1, (int) $_REQUEST['page']) : 1;
		$perPage = self::PER_PAGE;

		$alertType = trim((string) ($_GET['alert_type'] ?? ''));
		$alertMessage = trim((string) ($_GET['alert_message'] ?? ''));

		if ($dateFrom !== '' && $dateTo !== '' && strtotime($dateFrom) > strtotime($dateTo)) {
			$alertType = 'danger';
			$alertMessage = 'Date From cannot be after Date To.';

			$this->view('user/my_orders', [
				'orders' => [],
				'dateFrom' => $dateFrom,
				'dateTo' => $dateTo,
				'page' => 1,
				'perPage' => $perPage,
				'totalPages' => 0,
				'totalSpent' => 0,
				'statusClasses' => [
					'processing' => 'status-processing',
					'out_for_delivery' => 'status-delivery',
					'out for delivery' => 'status-delivery',
					'done' => 'status-done',
				],
				'visiblePages' => [],
				'alertType' => $alertType,
				'alertMessage' => $alertMessage,
				'appBase' => defined('APP_BASE_PATH') ? APP_BASE_PATH : '',
				'currentUser' => Auth::user(),
			]);
			return;
		}

		$orderModel = new Order();
		$totalOrders = $orderModel->countByUserAndDateRange($this->currentUserId(), $dateFrom, $dateTo);
		$totalPages = (int) ceil($totalOrders / $perPage);

		if ($totalPages > 0 && $page > $totalPages) {
			$page = $totalPages;
		}

		$offset = ($page - 1) * $perPage;
		$orders = $orderModel->getByUserAndDateRange(
			$this->currentUserId(),
			$dateFrom,
			$dateTo,
			$perPage,
			$offset
		);

		$orderIds = array_map(static fn(array $order): int => (int) ($order['id'] ?? 0), $orders);
		$itemsByOrderId = $orderModel->getItemsByOrderIds($orderIds);

		$totalSpent = 0.0;
		foreach ($orders as &$order) {
			$orderId = (int) ($order['id'] ?? 0);
			$order['items'] = $itemsByOrderId[$orderId] ?? [];
			$order['room'] = trim(((string) ($order['room_no'] ?? '')) . (isset($order['room_name']) && $order['room_name'] !== '' ? ' - ' . (string) $order['room_name'] : ''));
			$totalSpent += (float) ($order['total_price'] ?? 0);
		}
		unset($order);

		$statusClasses = [
			'processing' => 'status-processing',
			'out_for_delivery' => 'status-delivery',
			'out for delivery' => 'status-delivery',
			'done' => 'status-done',
		];

		$visiblePages = [];
		if ($totalPages > 0) {
			$visiblePages = [1, $totalPages];

			for ($visiblePage = $page - 1; $visiblePage <= $page + 1; $visiblePage++) {
				if ($visiblePage >= 1 && $visiblePage <= $totalPages) {
					$visiblePages[] = $visiblePage;
				}
			}

			if ($page <= 3) {
				$visiblePages[] = 2;
				$visiblePages[] = 3;
				$visiblePages[] = 4;
			}

			if ($page >= $totalPages - 2) {
				$visiblePages[] = $totalPages - 1;
				$visiblePages[] = $totalPages - 2;
				$visiblePages[] = $totalPages - 3;
			}

			$visiblePages = array_values(array_unique(array_filter(
				$visiblePages,
				static fn(int $visiblePage): bool => $visiblePage >= 1 && $visiblePage <= $totalPages
			)));
			sort($visiblePages);
		}

		$this->view('user/my_orders', [
			'orders' => $orders,
			'dateFrom' => $dateFrom,
			'dateTo' => $dateTo,
			'page' => $page,
			'perPage' => $perPage,
			'totalPages' => $totalPages,
			'totalSpent' => $totalSpent,
			'statusClasses' => $statusClasses,
			'visiblePages' => $visiblePages,
			'alertType' => $alertType,
			'alertMessage' => $alertMessage,
			'appBase' => defined('APP_BASE_PATH') ? APP_BASE_PATH : '',
			'currentUser' => Auth::user(),
		]);
	}

	public function cancelOrder(): void
	{
		if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
			header('Location: ' . $this->appUrl('/user/my-orders'));
			exit;
		}

		$orderId = (int) ($_POST['order_id'] ?? 0);
		$redirectPage = isset($_POST['page']) ? max(1, (int) $_POST['page']) : 1;
		$redirectDateFrom = trim((string) ($_POST['date_from'] ?? ''));
		$redirectDateTo = trim((string) ($_POST['date_to'] ?? ''));

		if ($orderId < 1) {
			header('Location: ' . $this->redirectQuery($redirectPage, $redirectDateFrom, $redirectDateTo, 'danger', 'Invalid order selected.'));
			exit;
		}

		try {
			$orderModel = new Order();
			$orderModel->cancelProcessingOrder($orderId, $this->currentUserId());

			header('Location: ' . $this->redirectQuery($redirectPage, $redirectDateFrom, $redirectDateTo, 'success', 'Order canceled successfully.'));
			exit;
		} catch (\Throwable $e) {
			header('Location: ' . $this->redirectQuery($redirectPage, $redirectDateFrom, $redirectDateTo, 'danger', $e->getMessage()));
			exit;
		}
	}

	public function ordersAlias(): void
	{
		// Kept as a legacy-compatible alias for the old orders.php entry.
		header('Location: ' . $this->appUrl('/user/my-orders'));
		exit;
	}
}
