<?php

use Core\Auth;

 $current = basename($_SERVER['PHP_SELF']); ?>
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold text-warning fs-5" href="<?= BASE_URL ?>/user/home">
      <i class="bi bi-cup-hot-fill me-2"></i>Cafeteria
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navUser">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navUser">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link <?= $current==='/user/home'?'active fw-semibold':'' ?>" href="<?= BASE_URL ?>/user/home">
            <i class="bi bi-house me-1"></i>Home
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $current==='/user/my_orders'?'active fw-semibold':'' ?>" href="<?= BASE_URL ?>/user/my_orders">
            <i class="bi bi-receipt me-1"></i>My Orders
          </a>
        </li>
      </ul>
      <div class="d-flex align-items-center gap-2">
        <?php 
          $user = Auth::user();
        ?>
        <img class="rounded-circle" src="https://ui-avatars.com/api/?name=<?= urlencode($user['name']) ?>&background=f59e0b&color=fff&size=32&bold=true" alt="">
        <span class="fw-semibold text-dark"><?= htmlspecialchars($user['name'] ?? 'User') ?></span>
      </div>
    </div>
  </div>
</nav>
