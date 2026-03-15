<?php $pageTitle = "Dashboard – Admin"; ?>
<?php include __DIR__ . '/../layouts/head.php'; ?>
<?php include __DIR__ . '/../layouts/navbar_admin.php'; ?>

<div class="container py-4">
  <h4 class="fw-bold mb-4"><i class="bi bi-speedometer2 me-2 text-warning"></i>Dashboard</h4>

  <div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
      <div class="card border-0 shadow-sm rounded-4 border-start border-warning border-4">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="rounded-circle bg-warning bg-opacity-10 p-3"><i class="bi bi-receipt fs-4 text-warning"></i></div>
          <div>
            <div class="text-muted small fw-semibold">Today's Orders</div>
            <div class="fw-bold fs-4"><?= $todayOrders ?></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3">
      <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="rounded-circle bg-success bg-opacity-10 p-3"><i class="bi bi-cash-stack fs-4 text-success"></i></div>
          <div>
            <div class="text-muted small fw-semibold">Today's Revenue</div>
            <div class="fw-bold fs-4"><?= $todayRevenue ?> EGP</div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3">
      <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="rounded-circle bg-info bg-opacity-10 p-3"><i class="bi bi-people fs-4 text-info"></i></div>
          <div>
            <div class="text-muted small fw-semibold">Total Users</div>
            <div class="fw-bold fs-4"><?= $totalUsers ?></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3">
      <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="rounded-circle bg-danger bg-opacity-10 p-3"><i class="bi bi-truck fs-4 text-danger"></i></div>
          <div>
            <div class="text-muted small fw-semibold">Pending Delivery</div>
            <div class="fw-bold fs-4"><?= $pendingDeliveries ?></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-md-4">
      <a href="<?= BASE_URL ?>/admin/orders" class="card border-0 shadow-sm rounded-4 text-decoration-none">
        <div class="card-body p-4 text-center">
          <i class="bi bi-list-check fs-1 text-warning mb-2 d-block"></i>
          <div class="fw-bold">View All Orders</div>
          <div class="small text-muted">Manage and deliver orders</div>
        </div>
      </a>
    </div>
    <div class="col-md-4">
      <a href="<?= BASE_URL ?>/admin/manual-order" class="card border-0 shadow-sm rounded-4 text-decoration-none">
        <div class="card-body p-4 text-center">
          <i class="bi bi-pencil-square fs-1 text-primary mb-2 d-block"></i>
          <div class="fw-bold">Manual Order</div>
          <div class="small text-muted">Place order on behalf of user</div>
        </div>
      </a>
    </div>
    <div class="col-md-4">
      <a href="<?= BASE_URL ?>/admin/checks" class="card border-0 shadow-sm rounded-4 text-decoration-none">
        <div class="card-body p-4 text-center">
          <i class="bi bi-clipboard-check fs-1 text-success mb-2 d-block"></i>
          <div class="fw-bold">Checks</div>
          <div class="small text-muted">View spending by user</div>
        </div>
      </a>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
