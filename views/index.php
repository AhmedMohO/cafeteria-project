<?php
$pageTitle = 'Home - Cafeteria';
$appBase = defined('APP_BASE_PATH') ? APP_BASE_PATH : '';
$selectedRoomId = (int) ($currentUser['room_id'] ?? 0);
$selectedRoomText = trim((string) ($currentUser['room'] ?? ''));

$app = static function (string $path) use ($appBase): string {
    $normalized = '/' . ltrim($path, '/');
    if ($appBase === '') {
        return $normalized;
    }
    return $appBase . $normalized;
};
?>
<?php include __DIR__ . '/layouts/head.php'; ?>
<?php include __DIR__ . '/layouts/navbar_user.php'; ?>

<div class="container py-4">
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm cart-panel" style="border-radius:16px;">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-cart3 me-2 text-warning"></i>Your Order</h5>
                </div>
                <div class="card-body px-4">
                    <div class="mb-3" id="cart-items">
                        <div class="text-muted small">No items selected yet.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Notes</label>
                        <textarea id="order-notes" class="form-control" rows="3" placeholder="e.g. 1 Tea Extra Sugar"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Deliver to Room</label>
                        <select id="order-room" class="form-select">
                            <option value="" hidden>Select Room</option>
                            <?php foreach (($rooms ?? []) as $room): ?>
                                <?php
                                $roomValue = (string) ($room['value'] ?? (($room['id'] ?? 0) > 0 ? (string) ((int) $room['id']) : ''));
                                $roomLabel = (string) ($room['label'] ?? trim(((string) ($room['no'] ?? '')) . ((string) ($room['name'] ?? '') !== '' ? ' - ' . (string) ($room['name'] ?? '') : '')));
                                $isSelected = false;

                                if ($selectedRoomId > 0 && ctype_digit($roomValue)) {
                                    $isSelected = ((int) $roomValue) === $selectedRoomId;
                                }

                                if (!$isSelected && $selectedRoomText !== '') {
                                    $isSelected = (strcasecmp($roomLabel, $selectedRoomText) === 0 || strcasecmp($roomValue, $selectedRoomText) === 0);
                                }
                                ?>
                                <option value="<?= htmlspecialchars($roomValue) ?>" <?= $isSelected ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($roomLabel !== '' ? $roomLabel : 'Room') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div id="order-message" class="mb-3"></div>

                    <hr>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-bold">Total</span>
                        <span id="total" class="fw-800 fs-5 text-warning">EGP 0</span>
                    </div>

                    <button id="confirm-order-btn" class="btn btn-warning w-100 fw-bold py-2" style="border-radius:10px;">
                        <i class="bi bi-check2-circle me-2"></i>Confirm Order
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                    <input id="product-search" type="text" class="form-control border-start-0" placeholder="Search products..." autocomplete="off">
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4" style="border-radius:16px;">
                <div class="card-body p-3">
                    <p class="fw-bold text-muted small text-uppercase mb-2">
                        <i class="bi bi-clock-history me-1"></i>Latest Order
                    </p>
                    <div id="latest-order-container" class="small text-muted">
                        No previous orders yet.
                    </div>
                </div>
            </div>

            <h6 class="fw-bold text-muted text-uppercase mb-3 small">All Products</h6>
            <div id="products" class="row g-3"></div>
        </div>
    </div>
</div>

<script>
window.CAFETERIA_ENDPOINTS = {
    searchProducts: <?= json_encode($app('/user/search-products')) ?>,
    latestOrder: <?= json_encode($app('/user/latest-order')) ?>,
    placeOrder: <?= json_encode($app('/user/place-order')) ?>
};
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?= htmlspecialchars($app('/js/main.js')) ?>"></script>
</body>
</html>
