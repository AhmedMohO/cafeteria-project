<?php require __DIR__ . '/../../layouts/head.php'; ?>
<?php require __DIR__ . '/../../layouts/navbar_admin.php'; ?>

<div class="container mt-4" style="max-width:600px">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Edit Product</h1>
        <a href="<?= BASE_URL ?>/admin/products" class="btn btn-outline-secondary">← Back</a>
    </div>

    <form method="POST" action="<?= BASE_URL ?>/admin/products/update/<?= $product['id'] ?>" enctype="multipart/form-data">

        <div class="mb-3">
            <label class="form-label fw-semibold">Product</label>
            <input type="text" name="name" class="form-control"
                   value="<?= htmlspecialchars($product['name']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Price</label>
            <div class="input-group">
                <input type="number" step="0.01" name="price" class="form-control"
                       value="<?= $product['price'] ?>" required>
                <span class="input-group-text">EGP</span>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Category</label>
            <select name="category_id" class="form-select">
                <option value="">-- Select Category --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>"
                        <?= $cat['id'] == $product['category_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold">Product picture</label>
            <?php if (!empty($product['image'])): ?>
                <div class="mb-2">
                    <img src="/uploads/<?= htmlspecialchars($product['image']) ?>"
                         width="100" height="100"
                         class="rounded object-fit-cover border">
                    <small class="text-muted ms-2">Current image</small>
                </div>
            <?php endif; ?>
            <input type="file" name="image" class="form-control" accept="image/*">
            <small class="text-muted">Leave empty to keep current image</small>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-warning fw-bold px-4">Save</button>
            <a href="<?= BASE_URL ?>/admin/products" class="btn btn-outline-secondary px-4">Cancel</a>
        </div>

    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>