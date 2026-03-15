<?php require __DIR__ . '/../../layouts/head.php'; ?>
<?php require __DIR__ . '/../../layouts/navbar_admin.php'; ?>

<div class="container py-4" style="max-width: 700px;">

  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">
      <i class="bi bi-pencil-square me-2 text-warning"></i>Edit Product
    </h2>
    <a href="<?= BASE_URL ?>/admin/products" class="btn btn-outline-secondary rounded-3">
      <i class="bi bi-arrow-left me-1"></i> Back
    </a>
  </div>

  <!-- Form Card -->
  <div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
      <form method="POST" action="<?= BASE_PATH ?>/admin/products/update/<?= $product['id'] ?>" enctype="multipart/form-data">

        <!-- Product Name -->
        <div class="mb-3">
          <label class="form-label fw-semibold">Product Name</label>
          <input type="text" name="name" class="form-control rounded-3"
                 value="<?= htmlspecialchars($product['name']) ?>" required>
        </div>

        <!-- Price -->
        <div class="mb-3">
          <label class="form-label fw-semibold">Price</label>
          <div class="input-group">
            <input type="number" step="0.01" name="price"
                   class="form-control rounded-start-3"
                   value="<?= $product['price'] ?>" required>
            <span class="input-group-text bg-light rounded-end-3">EGP</span>
          </div>
        </div>

        <!-- Category -->
        <div class="mb-3">
          <label class="form-label fw-semibold">Category</label>
          <select name="category_id" class="form-select rounded-3">
            <option value="">-- Select Category --</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat['id'] ?>"
                <?= $cat['id'] == $product['category_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Current Image -->
        <?php if (!empty($product['image'])): ?>
        <div class="mb-3">
          <label class="form-label fw-semibold">Product picture</label>
          <div class="d-flex align-items-center gap-3">
            <img src="<?= BASE_URL ?>/public/uploads/<?= htmlspecialchars($product['image']) ?>"
                 width="100" height="100"
                 class="rounded-3 object-fit-cover border"> <!-- ✅ fixed -->
            <span class="text-muted small">Current image</span>
          </div>
        </div>
        <?php endif; ?>

        <!-- New Image -->
        <div class="mb-4">
          <label class="form-label fw-semibold">
            <?= !empty($product['image']) ? 'Change Image' : 'Product Image' ?>
          </label>
          <input type="file" name="image" accept="image/*" class="form-control rounded-3">
          <small class="text-muted">Leave empty to keep current image</small>
        </div>

        <!-- Buttons -->
        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-warning fw-bold px-4 rounded-3">
            <i class="bi bi-check-lg me-1"></i> Save Changes
          </button>
          <a href="<?= BASE_URL ?>/admin/products" class="btn btn-outline-secondary rounded-3 px-4">
            <i class="bi bi-x-lg me-1"></i> Cancel
          </a>
        </div>

      </form>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>