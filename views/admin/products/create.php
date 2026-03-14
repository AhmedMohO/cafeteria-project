<?php require __DIR__ . '/../../layouts/head.php'; ?>
<?php require __DIR__ . '/../../layouts/navbar_admin.php'; ?>

<div class="container mt-4" style="max-width:600px">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Add Product</h1>
        <a href="<?= BASE_URL ?>/admin/products" class="btn btn-outline-secondary">← Back</a>
    </div>

    <form method="POST" action="<?= BASE_URL ?>/admin/products/store" enctype="multipart/form-data">

        <div class="mb-3">
            <label class="form-label fw-semibold">Product</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Price</label>
            <div class="input-group">
                <input type="number" step="0.01" name="price" class="form-control" required>
                <span class="input-group-text">EGP</span>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Category</label>
            <div class="d-flex gap-2 align-items-center">
                <select name="category_id" class="form-select">
                    <option value="">-- Select Category --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <a href="<?= BASE_URL ?>/admin/categories" class="btn btn-outline-warning text-nowrap">+ Manage</a>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold">Product picture</label>
            <input type="file" name="image" class="form-control" accept="image/*">
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-warning fw-bold px-4">Save</button>
            <button type="reset" class="btn btn-outline-secondary px-4">Reset</button>
        </div>

    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>