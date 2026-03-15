<?php

use Core\Auth;

$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '/';
$base = defined('APP_BASE_PATH') ? APP_BASE_PATH : '';

if ($base !== '' && strpos($requestPath, $base) === 0) {
    $requestPath = substr($requestPath, strlen($base));
}

if (!isset($app) || !is_callable($app)) {
    $app = static function (string $path) use ($base): string {
        $normalized = '/' . ltrim($path, '/');
        if ($base === '') {
            return $normalized;
        }

        return $base . $normalized;
    };
}

$activeHome = ($requestPath === '/home' || $requestPath === '/');
$activeOrders = (strpos($requestPath, '/user/my-orders') === 0 || strpos($requestPath, '/user/orders') === 0);

$currentUser = $currentUser ?? Auth::user() ?? [];
$userName = trim((string) ($currentUser['name'] ?? 'User'));
$userInitial = strtoupper(substr($userName, 0, 1));
?>

<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold text-warning fs-5" href="<?= htmlspecialchars($app('/home')) ?>">
      <i class="bi bi-cup-hot-fill me-2"></i>Cafeteria
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navUser">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navUser">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link <?= $activeHome ? 'active fw-semibold' : '' ?>" href="<?= htmlspecialchars($app('/home')) ?>">
            <i class="bi bi-house me-1"></i>Home
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $activeOrders ? 'active fw-semibold' : '' ?>" href="<?= htmlspecialchars($app('/user/my-orders')) ?>">
            <i class="bi bi-receipt me-1"></i>My Orders
          </a>
        </li>
      </ul>
      <div class="d-flex align-items-center gap-2">
        <span class="rounded-circle bg-warning text-white d-inline-flex align-items-center justify-content-center" style="width:32px;height:32px;">
          <?= htmlspecialchars($userInitial) ?>
        </span>
        <span class="fw-semibold text-dark"><?= htmlspecialchars($userName) ?></span>
        <a href="<?= htmlspecialchars($app('/logout')) ?>" class="btn btn-sm btn-outline-secondary ms-2">Logout</a>
      </div>
    </div>
  </div>
</nav>
