<?php require __DIR__ . '/../../layouts/head.php'; ?>
<?php require __DIR__ . '/../../layouts/navbar_admin.php'; ?>

<div class="container py-4" style="max-width: 700px;">

  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0"><i class="bi bi-tags me-2 text-warning"></i>Manage Categories</h2>
    <a href="<?= BASE_URL ?>/admin/products" class="btn btn-outline-secondary rounded-3">
      <i class="bi bi-arrow-left me-1"></i> Back to Products
    </a>
  </div>

  <!-- Add Category Form -->
  <div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
      <h5 class="fw-bold mb-3">Add New Category</h5>
      <form method="POST" action="<?= BASE_URL ?>/admin/categories/store">
        <div class="input-group">
          <input type="text" name="name" class="form-control rounded-start-3"
                 placeholder="Category name" required>
          <button type="submit" class="btn btn-warning fw-bold rounded-end-3">
            <i class="bi bi-plus-lg me-1"></i> Add
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Categories Table -->
  <div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
      <table class="table table-hover align-middle mb-0 rounded-4 overflow-hidden">
        <thead class="table-dark">
          <tr>
            <th style="width:60px">#</th>
            <th>Category Name</th>
            <th style="width:120px">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($categories)): ?>
            <tr>
              <td colspan="3" class="text-center text-muted py-4">No categories yet</td>
            </tr>
          <?php else: ?>
            <?php $i = 1; foreach ($categories as $cat): ?>
            <tr>
              <td class="text-muted"><?= $i++ ?></td>
              <td class="fw-semibold"><?= htmlspecialchars($cat['name']) ?></td>
              <td>
                <form method="POST" action="<?= BASE_URL ?>/admin/categories/delete/<?= $cat['id'] ?>"
                      onsubmit="return confirm('Delete this category?')">
                  <button class="btn btn-sm btn-outline-danger rounded-3">
                    <i class="bi bi-trash me-1"></i> Delete
                  </button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>