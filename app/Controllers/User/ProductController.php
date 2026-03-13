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
		$roomModel = new Room();

		$rooms = $roomModel->allRooms();
		$currentUser = Auth::user();

		$this->view('index', [
			'rooms' => $rooms,
			'currentUser' => $currentUser,
		]);
	}

	public function searchProducts(): void
	{
		header('Content-Type: application/json; charset=utf-8');

		try {
			$search = trim((string) ($_GET['q'] ?? ''));
			$productModel = new Product();
			$rows = $productModel->searchAvailable($search);

			$products = array_map(static function (array $row): array {
				return [
					'id' => (int) ($row['id'] ?? 0),
					'name' => (string) ($row['name'] ?? ''),
					'price' => (float) ($row['price'] ?? 0),
					'icon' => (string) (($row['image'] ?? '') !== '' ? $row['image'] : ($row['icon'] ?? '')),
				];
			}, $rows);

			echo json_encode([
				'success' => true,
				'products' => $products,
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
