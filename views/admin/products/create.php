<?php require __DIR__ . '/../../layouts/head.php'; ?>
<?php require __DIR__ . '/../../layouts/navbar_admin.php'; ?>

<div class="container py-4" style="max-width: 700px;">

  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">
      <i class="bi bi-plus-circle me-2 text-warning"></i>Add Product
    </h2>
    <a href="<?= BASE_URL ?>/admin/products" class="btn btn-outline-secondary rounded-3">
      <i class="bi bi-arrow-left me-1"></i> Back
    </a>
  </div>

  <!-- Form Card -->
  <div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
      <form method="POST" action="<?= BASE_PATH ?>/admin/products/store" enctype="multipart/form-data">

        <!-- Product Name -->
        <div class="mb-3">
          <label class="form-label fw-semibold">Product Name</label>
          <input type="text" name="name" class="form-control rounded-3"
                 placeholder="Enter product name" required>
        </div>

        <!-- Price -->
        <div class="mb-3">
          <label class="form-label fw-semibold">Price</label>
          <div class="input-group">
            <input type="number" step="0.01" min="0" name="price"
                   class="form-control rounded-start-3" placeholder="0.00" required>
            <span class="input-group-text bg-light rounded-end-3">EGP</span>
          </div>
        </div>

        <!-- Category -->
        <div class="mb-3">
          <label class="form-label fw-semibold">Category</label>
          <div class="d-flex gap-2">
            <select name="category_id" class="form-select rounded-3">
              <option value="">-- Select Category --</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
              <?php endforeach; ?>
            </select>
            <a href="<?= BASE_URL ?>/admin/categories"
               class="btn btn-outline-warning rounded-3 text-nowrap fw-semibold">
              <i class="bi bi-tags me-1"></i> Manage
            </a>
          </div>
        </div>

        <!-- Image -->
        <div class="mb-4">
          <label class="form-label fw-semibold">Product Image</label>
          <input type="file" name="image" accept="image/*" class="form-control rounded-3">
        </div>

        <!-- Buttons -->
        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-warning fw-bold px-4 rounded-3">
            <i class="bi bi-check-lg me-1"></i> Save Product
          </button>
          <button type="reset" class="btn btn-outline-secondary rounded-3 px-4">
            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
          </button>
        </div>

      </form>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>