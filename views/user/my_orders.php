<?php
$pageTitle = 'My Orders - Cafeteria';
$appBase = '';
if (PHP_SAPI !== 'cli-server') {
	$appBase = $appBase ?? (defined('BASE_URL') ? (string) (parse_url(BASE_URL, PHP_URL_PATH) ?: '') : '');
}

$app = static function (string $path) use ($appBase): string {
	$normalized = '/' . ltrim($path, '/');
	if ($appBase === '') {
		return $normalized;
	}
	return $appBase . $normalized;
};

$buildPageLink = static function (int $targetPage) use ($dateFrom, $dateTo, $statusFilter, $app): string {
	$query = ['page' => $targetPage];
	if ($dateFrom !== '') {
		$query['date_from'] = $dateFrom;
	}
	if ($dateTo !== '') {
		$query['date_to'] = $dateTo;
	}
	if (($statusFilter ?? '') !== '') {
		$query['status'] = $statusFilter;
	}
	return $app('/user/my-orders') . '?' . http_build_query($query);
};

$productImageUrl = static function (string $image) use ($app): string {
	$image = trim($image);
	if ($image === '') {
		return '';
	}

    return BASE_URL . '/uploads/' . rawurlencode($image); 
};
?>

<?php include __DIR__ . '/../layouts/head.php'; ?>
<?php include __DIR__ . '/../layouts/navbar_user.php'; ?>

<div class="container-fluid py-4 px-3 px-lg-4">
	<h4 class="section-title mb-4"><i class="bi bi-receipt me-2 text-warning"></i>My Orders</h4>

	<div
		id="my-orders-alert"
		data-alert-type="<?= htmlspecialchars($alertType ?? '') ?>"
		data-alert-message="<?= htmlspecialchars($alertMessage ?? '') ?>"
		hidden
	></div>

	<div class="card border-0 shadow-sm mb-4 rounded-4">
		<div class="card-body p-3">
			<form id="my-orders-filter-form" method="GET" action="<?= htmlspecialchars($app('/user/my-orders')) ?>" class="row g-2 g-md-3 align-items-end">
				<div class="col-12 col-md-3">
					<label for="date_from" class="form-label small fw-semibold">Date From</label>
					<input id="date_from" name="date_from" type="date" class="form-control rounded-3" value="<?= htmlspecialchars($dateFrom ?? '') ?>" max="<?= htmlspecialchars($dateTo ?? '') ?>">
				</div>
				<div class="col-12 col-md-3">
					<label for="date_to" class="form-label small fw-semibold">Date To</label>
					<input id="date_to" name="date_to" type="date" class="form-control rounded-3" value="<?= htmlspecialchars($dateTo ?? '') ?>" min="<?= htmlspecialchars($dateFrom ?? '') ?>">
				</div>
				<div class="col-12 col-md-2">
					<label for="status" class="form-label small fw-semibold">Status</label>
					<select id="status" name="status" class="form-select rounded-3">
						<?php foreach (($statusOptions ?? ['' => 'All Statuses']) as $optionValue => $optionLabel): ?>
							<option value="<?= htmlspecialchars((string) $optionValue) ?>" <?= ((string) ($statusFilter ?? '') === (string) $optionValue) ? 'selected' : '' ?>>
								<?= htmlspecialchars((string) $optionLabel) ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="col-6 col-md-2">
					<button class="btn btn-warning w-100 fw-semibold rounded-3" type="submit"><i class="bi bi-funnel me-1"></i>Filter</button>
				</div>
				<div class="col-6 col-md-2">
					<a class="btn btn-outline-secondary w-100 rounded-3" href="<?= htmlspecialchars($app('/user/my-orders')) ?>">Reset</a>
				</div>
			</form>
		</div>
	</div>

	<div class="card border-0 shadow-sm rounded-4">
		<div class="card-body p-0">
			<div class="table-responsive">
				<table class="table table-hover mb-0 align-middle">
					<thead>
						<tr>
							<th class="ps-4 py-2">#</th>
							<th class="py-2">Order Date</th>
							<th class="py-2">Status</th>
							<th class="py-2">Amount</th>
							<th class="py-2">Action</th>
						</tr>
					</thead>
					<tbody>
						<?php if (empty($orders ?? [])): ?>
							<tr>
								<td colspan="5" class="text-center py-4 text-muted">No orders found for the selected filter.</td>
							</tr>
						<?php else: ?>
							<?php foreach (($orders ?? []) as $index => $o): ?>
								<?php
								$rowIndex = ($index + 1) + (($page ?? 1) - 1) * ($perPage ?? 5);
								$orderId = (int) ($o['id'] ?? 0);
								$collapseId = 'order-' . $orderId;
								$statusKey = strtolower(trim((string) ($o['status'] ?? '')));
								$statusClass = ($statusClasses[$statusKey] ?? 'bg-secondary text-white');
								$statusLabel = str_replace('_', ' ', ucfirst($statusKey));
								$canCancel = ($statusKey === 'processing');
								$formattedDate = !empty($o['created_at']) ? date('Y/m/d h:i A', strtotime((string) $o['created_at'])) : '-';
								?>
								<tr>
									<td class="ps-4 py-2 text-muted"><?= $rowIndex ?></td>
									<td>
										<span><?= htmlspecialchars($formattedDate) ?></span>
										<button class="btn btn-link text-muted p-0 ms-2 align-baseline" type="button" data-bs-toggle="collapse" data-bs-target="#<?= htmlspecialchars($collapseId) ?>">
											<i class="bi bi-chevron-down fs-6"></i>
										</button>
									</td>
									<td><span class="badge rounded-pill <?= htmlspecialchars($statusClass) ?> px-3 py-1"><?= htmlspecialchars($statusLabel) ?></span></td>
									<td class="fw-semibold"><?= number_format((float) ($o['total_price'] ?? 0), 2) ?> EGP</td>
									<td>
										<?php if ($canCancel): ?>
											<form method="POST" action="<?= htmlspecialchars($app('/user/my-orders/cancel')) ?>" class="d-inline cancel-order-form" data-order-label="#<?= $orderId ?>">
												<input type="hidden" name="order_id" value="<?= $orderId ?>">
												<input type="hidden" name="page" value="<?= (int) ($page ?? 1) ?>">
												<input type="hidden" name="date_from" value="<?= htmlspecialchars($dateFrom ?? '') ?>">
												<input type="hidden" name="date_to" value="<?= htmlspecialchars($dateTo ?? '') ?>">
																		<input type="hidden" name="status" value="<?= htmlspecialchars((string) ($statusFilter ?? '')) ?>">
												<button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-x-circle me-1"></i>Cancel</button>
											</form>
										<?php else: ?>
											<span class="text-muted">-</span>
										<?php endif; ?>
									</td>
								</tr>
								<tr><td colspan="5" class="p-0">
									<div class="collapse" id="<?= htmlspecialchars($collapseId) ?>">
										<div class="bg-light px-4 py-3">
											<div class="d-flex flex-wrap gap-4 mb-3 small">
												<div><span class="fw-semibold text-muted">Order:</span> <span class="fw-semibold">#<?= $orderId ?></span></div>
												<div><span class="fw-semibold text-muted">Room:</span> <span class="fw-semibold"><?= htmlspecialchars((string) ($o['room'] ?? '')) ?></span></div>
											</div>
											<div class="d-flex flex-wrap gap-4">
												<?php if (empty($o['items'] ?? [])): ?>
													<div class="text-muted small">No items found for this order.</div>
												<?php else: ?>
													<?php foreach (($o['items'] ?? []) as $item): ?>
														<?php
													$image = trim((string) ($item['image'] ?? ''));
													$imageUrl = $productImageUrl($image);
													$name = (string) ($item['name'] ?? 'Product');
													$unitPrice = (float) ($item['price'] ?? 0);
													$qty = (int) ($item['quantity'] ?? 0);
													?>
													<div class="text-center">
														<?php if ($imageUrl !== ''): ?>
															<img src="<?= htmlspecialchars($imageUrl) ?>" alt="<?= htmlspecialchars($name) ?>" class="order-item-thumb">
														<?php else: ?>
															<div class="order-item-thumb product-image-fallback d-inline-flex align-items-center justify-content-center">No image</div>
														<?php endif; ?>
															<div class="small"><?= htmlspecialchars($name) ?></div>
															<span class="badge bg-warning text-dark"><?= number_format($unitPrice, 2) ?> EGP</span>
															<div class="small text-muted">x<?= $qty ?></div>
														</div>
													<?php endforeach; ?>
												<?php endif; ?>
											</div>
											<div class="mt-3 small">
												<span class="fw-semibold text-muted">Note:</span>
												<span class="text-dark"><?= trim((string) ($o['notes'] ?? '')) !== '' ? htmlspecialchars((string) ($o['notes'] ?? '')) : 'No notes' ?></span>
											</div>
										</div>
									</div>
								</td></tr>
							<?php endforeach; ?>
						<?php endif; ?>
					</tbody>
					<tfoot>
						<tr class="table-light">
							<td colspan="4" class="text-end fw-semibold ps-4">Total Spent:</td>
							<td class="fw-bold text-warning"><?= number_format((float) ($totalSpent ?? 0), 2) ?> EGP</td>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
		<?php if (($totalPages ?? 0) > 1): ?>
			<div class="card-footer bg-white border-0 d-flex justify-content-center py-3">
				<nav>
					<ul class="pagination mb-0">
						<?php $hasPrev = ($page ?? 1) > 1; ?>
						<li class="page-item <?= $hasPrev ? '' : 'disabled' ?>">
							<a class="page-link" href="<?= $hasPrev ? htmlspecialchars($buildPageLink(($page ?? 1) - 1)) : '#' ?>">&laquo;</a>
						</li>

						<?php $previousVisiblePage = null; ?>
						<?php foreach (($visiblePages ?? []) as $visiblePage): ?>
							<?php if ($previousVisiblePage !== null && $visiblePage - $previousVisiblePage > 1): ?>
								<li class="page-item disabled">
									<span class="page-link">...</span>
								</li>
							<?php endif; ?>

							<li class="page-item <?= $visiblePage === ($page ?? 1) ? 'active' : '' ?>">
								<a class="page-link" href="<?= htmlspecialchars($buildPageLink($visiblePage)) ?>"><?= $visiblePage ?></a>
							</li>
							<?php $previousVisiblePage = $visiblePage; ?>
						<?php endforeach; ?>

						<?php $hasNext = ($page ?? 1) < ($totalPages ?? 0); ?>
						<li class="page-item <?= $hasNext ? '' : 'disabled' ?>">
							<a class="page-link" href="<?= $hasNext ? htmlspecialchars($buildPageLink(($page ?? 1) + 1)) : '#' ?>">&raquo;</a>
						</li>
					</ul>
				</nav>
			</div>
		<?php endif; ?>
	</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?= htmlspecialchars($app('/js/myOrders.js')) ?>"></script>
</body>
</html>
