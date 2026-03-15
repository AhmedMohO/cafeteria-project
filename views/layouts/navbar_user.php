<?php $current = basename($_SERVER['PHP_SELF']); ?>
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold text-warning fs-5" href="index.php">
      <i class="bi bi-cup-hot-fill me-2"></i>Cafeteria
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navUser">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navUser">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link <?= $current === '/cafeteria' ? 'active fw-semibold' : '' ?>" href="/cafeteria">
            <i class="bi bi-house me-1"></i>Home
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $current === 'my_orders.php' ? 'active fw-semibold' : '' ?>" href="my_orders.php">
            <i class="bi bi-receipt me-1"></i>My Orders
          </a>
        </li>
      </ul>
      <div class="d-flex align-items-center gap-3">
        <div class="d-flex align-items-center gap-2">
          <span class="rounded-circle text-white">IA</span>
          <span class="fw-semibold text-dark">{{Core\Auth::user()['name']}}</span>
        </div>

        <a href="/logout" class="btn btn-outline-danger btn-sm">
          <i class="bi bi-box-arrow-right me-1"></i> Logout
        </a>
      </div>
    </div>
  </div>
</nav>