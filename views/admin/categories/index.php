<?php require __DIR__ . '/../../layouts/head.php'; ?>
<?php require __DIR__ . '/../../layouts/navbar_admin.php'; ?>

<div class="container py-5" style="max-width: 800px;">

  <div class="row align-items-center mb-4">
    <div class="col">
    
      <h2 class="h4 fw-bold mb-0 text-dark">Category Management</h2>
    </div>
    <div class="col-auto">
      <a href="<?= BASE_URL ?>/admin/products" class="btn btn-light border btn-sm rounded-pill px-3">
        <i class="bi bi-arrow-left me-1"></i> Back
      </a>
    </div>
  </div>

  <!-- Add Category -->
  <div class="card border-light shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
      <label class="form-label small fw-bold text-uppercase text-secondary mb-3">Add New Category</label>
      <form method="POST" action="<?= BASE_URL ?>/admin/categories/store">
        <div class="input-group">
          <input type="text" name="name" class="form-control bg-light border-0 py-2 ps-3"
                 placeholder="Enter category name..." required>
          <button type="submit" class="btn btn-warning fw-bold px-4">
            <i class="bi bi-plus-lg"></i>
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Categories Table -->
  <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="bg-light">
          <tr>
            <th class="border-0 text-secondary small fw-bold px-4 py-3" style="width: 80px;">ID</th>
            <th class="border-0 text-secondary small fw-bold py-3">NAME</th>
            <th class="border-0 text-secondary small fw-bold text-end px-4 py-3">ACTION</th>
          </tr>
        </thead>
        <tbody class="border-top-0">
          <?php if (empty($categories)): ?>
            <tr>
              <td colspan="3" class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
                No categories found
              </td>
            </tr>
          <?php else: ?>
            <?php $i = 1; foreach ($categories as $cat): ?>
            <tr>
              <td class="px-4 text-secondary small"><?= sprintf("%02d", $i++) ?></td>
              <td>
                <div class="d-flex align-items-center">
                  <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 me-2">#</span>
                  <span class="fw-semibold text-dark"><?= htmlspecialchars($cat['name']) ?></span>
                </div>
              </td>
              <td class="text-end px-4">
                <form method="POST" action="<?= BASE_URL ?>/admin/categories/delete/<?= $cat['id'] ?>"
                      onsubmit="return confirm('Delete this category?')">
                  <button class="btn btn-link text-danger p-0 text-decoration-none small fw-bold">
                    <i class="bi bi-trash3 me-1"></i> Delete
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