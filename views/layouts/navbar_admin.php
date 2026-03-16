<?php

use Core\Auth;

 $current = basename($_SERVER['PHP_SELF']); ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark border-bottom shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold text-warning fs-5" href="<?= BASE_URL ?>/admin/dashboard">
      <i class="bi bi-cup-hot-fill me-2"></i>Cafeteria
      <span class="badge bg-warning text-dark ms-1 align-middle" style="font-size:0.6rem!important;">ADMIN</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navAdmin">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navAdmin">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link <?= $current==='dashboard.php'?'active':'' ?>" href="<?= BASE_URL ?>/admin/dashboard">
            <i class="bi bi-house me-1"></i>Home
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= in_array($current,['products.php','add_product.php'])?'active':'' ?>" href="<?= BASE_URL ?>/admin/products">
            <i class="bi bi-box-seam me-1"></i>Products
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= in_array($current,['users.php','add_user.php'])?'active':'' ?>" href="<?= BASE_URL ?>/admin/users">
            <i class="bi bi-people me-1"></i>Users
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $current==='manual_order.php'?'active':'' ?>" href="<?= BASE_URL ?>/admin/manual-order">
            <i class="bi bi-pencil-square me-1"></i>Manual Order
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $current==='checks.php'?'active':'' ?>" href="<?= BASE_URL ?>/admin/checks">
            <i class="bi bi-clipboard-check me-1"></i>Checks
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $current==='orders.php'?'active':'' ?>" href="<?= BASE_URL ?>/admin/orders">
            <i class="bi bi-list-check me-1"></i>Orders
          </a>
        </li>
      </ul>
      <div class="d-flex align-items-center gap-2">
        <?php 
          $authUser = Auth::user();
        ?>
         <?php
            if (!empty($authUser['pic'])) {
                $avatar = '/' . ltrim($authUser['pic'], '/');
            } else {
                $avatar = 'https://ui-avatars.com/api/?name=' . urlencode($authUser['name']) . '&background=f59e0b&color=fff&size=32&bold=true';
            }
        ?>

        <img width="32" height="32" class="rounded-circle" src="<?= htmlspecialchars($avatar) ?>" alt="" onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($authUser['name']) ?>&background=f59e0b&color=fff&size=32&bold=true';">
        <span class="text-white fw-semibold"><?= htmlspecialchars($authUser['name'] ?? 'Admin') ?></span>
      </div>
    </div>
  </div>
</nav>
