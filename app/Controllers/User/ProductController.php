<?php

namespace App\Controllers\User;

use Core\Controller;
use Core\Auth;
use App\Models\Product;
use App\Models\Room;

class ProductController extends Controller
{
	public function index(): void
	{
		$currentUser = Auth::user() ?? [];
		$rooms = (new Room())->allRooms();

		$this->view('user/home', [
			'rooms' => $rooms,
			'currentUser' => $currentUser,
		]);
	}

	public function searchProducts(): void
	{
		header('Content-Type: application/json; charset=utf-8');

		try {
			$search = trim((string) ($_GET['q'] ?? ''));
			$page = max(1, (int) ($_GET['page'] ?? 1));
			$perPage = max(1, min(50, (int) ($_GET['per_page'] ?? 10)));

			$productModel = new Product();
			$total = $productModel->countAvailable($search);
			$totalPages = $total > 0 ? (int) ceil($total / $perPage) : 0;

			if ($totalPages > 0 && $page > $totalPages) {
				$page = $totalPages;
			}

			$rows = $total > 0
				? $productModel->searchAvailablePaginated($search, $page, $perPage)
				: [];

			$products = array_map(static function (array $row): array {
				return [
					'id' => (int) ($row['id'] ?? 0),
					'name' => (string) ($row['name'] ?? ''),
					'price' => (float) ($row['price'] ?? 0),
					'image' => Product::resolveStoredImage((string) ($row['image'] ?? '')),
				];
			}, $rows);

			echo json_encode([
				'success' => true,
				'products' => $products,
				'pagination' => [
					'page' => $page,
					'per_page' => $perPage,
					'total' => $total,
					'total_pages' => $totalPages,
				],
			]);
		} catch (\Throwable $e) {
			http_response_code(500);
			echo json_encode([
				'success' => false,
				'message' => 'Failed to load products',
			]);
		}
	}
}
