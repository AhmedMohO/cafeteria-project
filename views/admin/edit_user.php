<?php
$pageTitle = "Edit User – Admin";
include __DIR__ . '/../layouts/head.php';
include __DIR__ . '/../layouts/navbar_admin.php';

$formAction = BASE_URL . '/admin/users/update/' . (int)($user['id'] ?? 0);
$isEdit = true;
?>

<div class="container py-4">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-pencil me-2 text-warning"></i>Edit User</h4>
    <a href="<?= BASE_URL ?>/admin/users" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
  </div>

  <div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
      <?php include __DIR__ . '/_user_form.php'; ?>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
