<?php $pageTitle = "Orders – Admin"; ?>
<?php include __DIR__ . '/../layouts/head.php'; ?>
<?php include __DIR__ . '/../layouts/navbar_admin.php'; ?>

<div class="container py-4">
  <h4 class="fw-bold mb-4"><i class="bi bi-list-check me-2 text-warning"></i>Orders</h4>

  <?php
  // session_start();
  if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">' . $_SESSION['success'] . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    unset($_SESSION['success']);
  }
  if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">' . $_SESSION['error'] . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    unset($_SESSION['error']);
  }
  
  foreach($orders as $order): ?>
  <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
    <div class="card-header bg-white py-0">
      <div class="row g-0 align-items-center">
        <div class="col px-4 py-3">
          <div class="text-muted fw-semibold text-uppercase" style="font-size:0.72rem;">ORDER DATE</div>
          <div class="fw-bold"><?= date('Y/m/d h:i A', strtotime($order['created_at'])) ?></div>
        </div>
        <div class="col px-4 py-3">
          <div class="text-muted fw-semibold text-uppercase" style="font-size:0.72rem;">CUSTOMER</div>
          <div class="fw-bold"><?= $order['name'] ?></div>
        </div>
        <div class="col px-4 py-3 text-center">
          <div class="text-muted fw-semibold text-uppercase" style="font-size:0.72rem;">ROOM</div>
          <div class="fw-bold"><?= $order['room_no'] ?></div>
        </div>
        <div class="col px-4 py-3 text-center">
          <div class="text-muted fw-semibold text-uppercase" style="font-size:0.72rem;">EXT.</div>
          <div class="fw-bold"><?= $order['ext'] ?></div>
        </div>
        <div class="col px-4 py-3 text-end">
          <form method="POST" action="<?= BASE_URL ?>/admin/mark-delivered" style="display:inline;">
            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
            <button type="submit" class="btn btn-success fw-semibold" onclick="return confirm('Mark this order as delivered?')">
              <i class="bi bi-truck me-2"></i>Mark as Delivered
            </button>
          </form>
        </div>
      </div>
    </div>
    <div class="card-body p-4">
      <div class="d-flex gap-4 flex-wrap">
        <?php foreach($order['items'] as $item): ?>
        <div class="text-center">
          <div class="fs-3">☕</div>
          <div class="fw-semibold small"><?= $item['product_name'] ?></div>
          <span class="badge bg-warning text-dark"><?= number_format($item['price'], 2) ?> EGP</span>
          <div class="fw-bold text-muted mt-1"><?= $item['quantity'] ?></div>
        </div>
        <?php endforeach; ?>
      </div>
      <div class="text-end mt-3">
        <span class="text-muted fw-semibold">Total: </span>
        <span class="fw-bold fs-5 text-warning">EGP <?= number_format($order['total_price'], 2) ?></span>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
  
  <?php if ($totalPages > 1): ?>
  <div class="card-footer bg-white border-0 d-flex justify-content-center py-3">
    <nav><ul class="pagination pagination-sm mb-0">
      <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
        <a class="page-link" href="?page=<?= $currentPage - 1 ?>">&laquo;</a>
      </li>
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
      <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
      </li>
      <?php endfor; ?>
      <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
        <a class="page-link" href="?page=<?= $currentPage + 1 ?>">&raquo;</a>
      </li>
    </ul></nav>
  </div>
  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Auto-refresh page every 60 seconds
setTimeout(function(){
  window.location.reload();
}, 60000);
</script>
</body>
</html>
