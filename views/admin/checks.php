<?php $pageTitle = "Checks – Admin"; ?>
<?php include __DIR__ . '/../layouts/head.php'; ?>
<?php include __DIR__ . '/../layouts/navbar_admin.php'; ?>

<div class="container py-4">
  <h4 class="fw-bold mb-4"><i class="bi bi-clipboard-check me-2 text-warning"></i>Checks</h4>

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
  ?>

  <div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
      <form method="GET">
        <div class="row g-2 align-items-end">
          <div class="col-md-3"><label class="form-label small fw-semibold">Date From</label><input type="date" name="date_from" class="form-control" value="<?= $dateFrom ?>"></div>
          <div class="col-md-3"><label class="form-label small fw-semibold">Date To</label><input type="date" name="date_to" class="form-control" value="<?= $dateTo ?>"></div>
          <div class="col-md-3">
            <label class="form-label small fw-semibold">User</label>
            <select name="user_id" class="form-select">
              <option value="">All Users</option>
              <?php foreach($usersSummary as $user): ?>
              <option value="<?= $user['id'] ?>" <?= $userId == $user['id'] ? 'selected' : '' ?>><?= $user['name'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-3">
            <button type="submit" class="btn btn-warning fw-semibold w-100"><i class="bi bi-funnel me-1"></i>Apply Filter</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-0 pt-3 px-4"><h6 class="fw-bold mb-0">Users Summary</h6></div>
    <div class="card-body p-0">
      <table class="table table-hover align-middle mb-0">
        <thead><tr><th class="ps-4">Name</th><th>Total Amount</th><th>Orders</th><th></th></tr></thead>
        <tbody>
          <?php foreach($usersSummary as $user): ?>
          <tr class="<?= $selectedUser && $selectedUser['id'] == $user['id'] ? 'table-warning' : '' ?>">
            <td class="ps-4">
              <div class="d-flex align-items-center gap-2">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($user['name']) ?>&background=ffc107&color=fff&size=28" class="rounded-circle" width="28" height="28">
                <span class="fw-semibold"><?= $user['name'] ?></span>
              </div>
            </td>
            <td class="fw-bold text-warning"><?= number_format($user['total_spent'], 2) ?> EGP</td>
            <td><span class="badge bg-secondary"><?= $user['order_count'] ?> orders</span></td>
            <td>
              <a href="?user_id=<?= $user['id'] ?>&date_from=<?= $dateFrom ?>&date_to=<?= $dateTo ?>" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-eye me-1"></i>View Orders
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <?php if ($selectedUser): ?>
  <div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 pt-3 px-4 d-flex justify-content-between align-items-center">
      <h6 class="fw-bold mb-0">Orders — <span class="text-warning"><?= $selectedUser['name'] ?></span></h6>
      <span class="fw-bold">Total: <span class="text-warning"><?= $grandTotal ?> EGP</span></span>
    </div>
    <div class="card-body p-0">
      <table class="table table-hover align-middle mb-0">
        <thead><tr><th class="ps-4">Order Date</th><th>Items</th><th>Amount</th></tr></thead>
        <tbody>
          <?php foreach($orders as $i => $order): ?>
          <tr>
            <td class="ps-4">
              <span class="fw-semibold"><?= date('Y/m/d h:i A', strtotime($order['created_at'])) ?></span>
              <button class="btn btn-link btn-sm text-muted p-0 ms-1" data-bs-toggle="collapse" data-bs-target="#chk-<?= $i ?>">
                <i class="bi bi-chevron-down"></i>
              </button>
            </td>
            <td class="text-muted small">
              <?php 
              $itemsList = [];
              foreach($order['items'] as $item) {
                $itemsList[] = $item['product_name'] . '×' . $item['quantity'];
              }
              echo implode(', ', $itemsList);
              ?>
            </td>
            <td class="fw-bold"><?= number_format($order['total_price'], 2) ?> EGP</td>
          </tr>
          <tr><td colspan="3" class="p-0">
            <div class="collapse" id="chk-<?= $i ?>">
              <div class="bg-light px-4 py-3 d-flex gap-4 flex-wrap">
                <?php foreach($order['items'] as $item): ?>
                <div class="text-center">
                  <div class="fs-4">☕</div>
                  <div class="small"><?= $item['product_name'] ?></div>
                  <span class="badge bg-warning text-dark"><?= number_format($item['price'], 2) ?> EGP</span>
                  <div class="small text-muted">×<?= $item['quantity'] ?></div>
                </div>
                <?php endforeach; ?>
              </div>
            </div>
          </td></tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php if ($totalPages > 1): ?>
    <div class="card-footer bg-white border-0 d-flex justify-content-center py-3">
      <nav><ul class="pagination pagination-sm mb-0">
        <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
          <a class="page-link" href="?user_id=<?= $userId ?>&date_from=<?= $dateFrom ?>&date_to=<?= $dateTo ?>&page=<?= $currentPage - 1 ?>">&laquo;</a>
        </li>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
          <a class="page-link" href="?user_id=<?= $userId ?>&date_from=<?= $dateFrom ?>&date_to=<?= $dateTo ?>&page=<?= $i ?>"><?= $i ?></a>
        </li>
        <?php endfor; ?>
        <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
          <a class="page-link" href="?user_id=<?= $userId ?>&date_from=<?= $dateFrom ?>&date_to=<?= $dateTo ?>&page=<?= $currentPage + 1 ?>">&raquo;</a>
        </li>
      </ul></nav>
    </div>
    <?php endif; ?>
  </div>
  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
