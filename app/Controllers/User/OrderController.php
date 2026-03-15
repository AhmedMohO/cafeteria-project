<?php

namespace App\Controllers\User;

use Core\Controller;
use Core\Auth;
use App\Models\Order;
use App\Models\Product;

class OrderController extends Controller
{
	private const PER_PAGE = 5;

	private function resolveOrderItemImage(array $productsById, int $productId, string $storedImage): string
	{
		$image = Product::resolveStoredImage($storedImage);
		if ($image !== '') {
			return $image;
		}

		if ($productId > 0 && isset($productsById[$productId])) {
			return Product::resolveStoredImage((string) ($productsById[$productId]['image'] ?? ''));
		}

		return '';
	}

	private function currentUserId(): int
	{
		$user = Auth::user();
		return (int) ($user['id'] ?? 0);
	}

	private function currentUserRoomId(): int
	{
		$user = Auth::user();
		return (int) ($user['room_id'] ?? 0);
	}

	private function resolveRoomIdFromPayload(array $data): int
	{
		$roomId = (int) ($data['room_id'] ?? 0);
		if ($roomId > 0) {
			return $roomId;
		}

		return $this->currentUserRoomId();
	}

	private function redirectQuery(
		int $page,
		string $dateFrom,
		string $dateTo,
		string $statusFilter = '',
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
		if ($statusFilter !== '') {
			$query['status'] = $statusFilter;
		}
		if ($alertType !== '') {
			$query['alert_type'] = $alertType;
		}
		if ($alertMessage !== '') {
			$query['alert_message'] = $alertMessage;
		}

		return BASE_URL . '/user/my-orders?' . http_build_query($query);
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

			$productsById = $productModel->getAvailableIndexedById();
			$productsByName = [];
			foreach ($productsById as $product) {
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
					'image' => $this->resolveOrderItemImage($productsById, $productId, (string) ($row['image'] ?? '')),
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
		$roomId = $this->resolveRoomIdFromPayload($data);

		if (!is_array($items) || count($items) === 0) {
			http_response_code(400);
			echo json_encode(['success' => false, 'message' => 'Order must contain at least one item']);
			return;
		}

		if ($roomId < 1) {
			http_response_code(400);
			echo json_encode(['success' => false, 'message' => 'Please select a room']);
			return;
		}

		$productModel = new Product();
		$orderModel = new Order();

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
				$productImage = Product::resolveStoredImage((string) ($product['image'] ?? ''));
				if ($price <= 0) {
					http_response_code(400);
					echo json_encode(['success' => false, 'message' => 'Invalid product price']);
					return;
				}

				$productName = (string) ($product['name'] ?? '');

				$normalizedItems[] = [
					'product_id' => $productId,
					'name' => $productName,
					'image' => $productImage,
					'price' => $price,
					'quantity' => $quantity,
				];
			}

			$orderId = $orderModel->createWithItems(
				$this->currentUserId(),
				$roomId,
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
			echo json_encode(['success' => false, 'message' => 'Failed to save order. Please try again.']);
		}
	}

	public function myOrders(): void
	{
		$dateFrom = trim((string) ($_REQUEST['date_from'] ?? ''));
		$dateTo = trim((string) ($_REQUEST['date_to'] ?? ''));
		$statusFilter = strtolower(trim((string) ($_REQUEST['status'] ?? '')));
		$statusFilter = str_replace(' ', '_', $statusFilter);
		$allowedStatuses = ['processing', 'out_for_delivery', 'done'];
		if (!in_array($statusFilter, $allowedStatuses, true)) {
			$statusFilter = '';
		}
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
				'statusFilter' => $statusFilter,
				'statusOptions' => [
					'' => 'All Statuses',
					'processing' => 'Processing',
					'out_for_delivery' => 'Out For Delivery',
					'done' => 'Done',
				],
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
				'appBase' => defined('BASE_URL') ? BASE_URL : '',
				'currentUser' => Auth::user(),
			]);
			return;
		}

		$orderModel = new Order();
		$productModel = new Product();
		$productsById = $productModel->getAvailableIndexedById();
		$totalOrders = $orderModel->countByUserAndDateRange($this->currentUserId(), $dateFrom, $dateTo, $statusFilter);
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
			$offset,
			$statusFilter
		);

		$orderIds = array_map(static fn(array $order): int => (int) ($order['id'] ?? 0), $orders);
		$itemsByOrderId = $orderModel->getItemsByOrderIds($orderIds);

		$totalSpent = 0.0;
		foreach ($orders as &$order) {
			$orderId = (int) ($order['id'] ?? 0);
			$order['items'] = array_map(function (array $item) use ($productsById): array {
				$productId = (int) ($item['product_id'] ?? 0);
				$item['image'] = $this->resolveOrderItemImage($productsById, $productId, (string) ($item['image'] ?? ''));
				return $item;
			}, $itemsByOrderId[$orderId] ?? []);
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
			'statusFilter' => $statusFilter,
			'statusOptions' => [
				'' => 'All Statuses',
				'processing' => 'Processing',
				'out_for_delivery' => 'Out For Delivery',
				'done' => 'Done',
			],
			'page' => $page,
			'perPage' => $perPage,
			'totalPages' => $totalPages,
			'totalSpent' => $totalSpent,
			'statusClasses' => $statusClasses,
			'visiblePages' => $visiblePages,
			'alertType' => $alertType,
			'alertMessage' => $alertMessage,
			'appBase' => defined('BASE_URL') ? BASE_URL : '',
			'currentUser' => Auth::user(),
		]);
	}

	public function cancelOrder(): void
	{
		if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
			header('Location: ' . BASE_URL . '/user/my-orders');
			exit;
		}

		$orderId = (int) ($_POST['order_id'] ?? 0);
		$redirectPage = isset($_POST['page']) ? max(1, (int) $_POST['page']) : 1;
		$redirectDateFrom = trim((string) ($_POST['date_from'] ?? ''));
		$redirectDateTo = trim((string) ($_POST['date_to'] ?? ''));
		$redirectStatus = strtolower(trim((string) ($_POST['status'] ?? '')));
		$redirectStatus = str_replace(' ', '_', $redirectStatus);
		if (!in_array($redirectStatus, ['processing', 'out_for_delivery', 'done'], true)) {
			$redirectStatus = '';
		}

		if ($orderId < 1) {
			header('Location: ' . $this->redirectQuery($redirectPage, $redirectDateFrom, $redirectDateTo, $redirectStatus, 'danger', 'Invalid order selected.'));
			exit;
		}

		try {
			$orderModel = new Order();
			$orderModel->cancelProcessingOrder($orderId, $this->currentUserId());

			header('Location: ' . $this->redirectQuery($redirectPage, $redirectDateFrom, $redirectDateTo, $redirectStatus, 'success', 'Order canceled successfully.'));
			exit;
		} catch (\Throwable $e) {
			header('Location: ' . $this->redirectQuery($redirectPage, $redirectDateFrom, $redirectDateTo, $redirectStatus, 'danger', $e->getMessage()));
			exit;
		}
	}

	public function ordersAlias(): void
	{
		header('Location: ' . BASE_URL . '/user/my-orders');
		exit;
	}
}
