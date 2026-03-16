<?php

use Core\Auth;

$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

$runtimeBase = '';
if (PHP_SAPI !== 'cli-server' && defined('BASE_URL')) {
  $runtimeBase = (string) (parse_url(BASE_URL, PHP_URL_PATH) ?: '');
  if ($runtimeBase === '/') {
    $runtimeBase = '';
  }
}

$pathNoBase = $currentPath;
if ($runtimeBase !== '' && strpos($currentPath, $runtimeBase) === 0) {
  $pathNoBase = substr($currentPath, strlen($runtimeBase));
  if ($pathNoBase === '' || $pathNoBase === false) {
    $pathNoBase = '/';
  }
}

$href = static function (string $path) use ($runtimeBase): string {
  return $runtimeBase . '/' . ltrim($path, '/');
};

$isHome = preg_match('#/user/home$#', rtrim($pathNoBase, '/')) === 1;
$isOrders = preg_match('#/user/(my-orders|my_orders\.php|orders(?:\.php)?)$#', rtrim($pathNoBase, '/')) === 1;
?>
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold text-warning fs-5" href="<?= htmlspecialchars($href('/user/home')) ?>">
      <i class="bi bi-cup-hot-fill me-2"></i>Cafeteria
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navUser">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navUser">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link <?= $isHome ? 'active fw-semibold' : '' ?>" href="<?= htmlspecialchars($href('/user/home')) ?>">
            <i class="bi bi-house me-1"></i>Home
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $isOrders ? 'active fw-semibold' : '' ?>" href="<?= htmlspecialchars($href('/user/my-orders')) ?>">
            <i class="bi bi-receipt me-1"></i>My Orders
          </a>
        </li>
      </ul>
      <div class="d-flex align-items-center gap-2">
        <a href="<?= BASE_URL ?>/logout" class="btn btn-outline-danger btn-sm">
          <i class="bi bi-box-arrow-right me-1"></i> Logout
        </a>
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
        <img class="rounded-circle" width="32" height="32" src="<?= htmlspecialchars($avatar) ?>" alt="" onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($authUser['name']) ?>&background=f59e0b&color=fff&size=32&bold=true';">
        <span class="fw-semibold text-dark"><?= htmlspecialchars($authUser['name'] ?? 'User') ?></span>
      </div>
    </div>
  </div>
</nav>
