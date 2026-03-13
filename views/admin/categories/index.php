<?php require __DIR__ . '/../../layouts/head.php'; ?>
<?php require __DIR__ . '/../../layouts/navbar_admin.php'; ?>

<div class="container mt-4" style="max-width:600px">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Categories</h1>
        <a href="/admin/products" class="btn btn-outline-secondary">← Back to Products</a>
    </div>

    <!-- Add Category Form -->
    <form method="POST" action="/admin/categories/store" class="mb-4">
        <label class="form-label fw-semibold">New Category</label>
        <div class="input-group">
            <input type="text" name="name" class="form-control" placeholder="e.g. Hot Drinks" required>
            <button type="submit" class="btn btn-warning fw-bold">Save</button>
        </div>
    </form>

    <!-- Categories Table -->
    <table class="table table-bordered align-middle">
        <thead class="table-warning">
            <tr>
                <th>#</th>
                <th>Category Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($categories)): ?>
                <tr>
                    <td colspan="3" class="text-center text-muted">No categories yet</td>
                </tr>
            <?php else: ?>
                <?php foreach ($categories as $cat): ?>
                <tr>
                    <td><?= $cat['id'] ?></td>
                    <td><?= htmlspecialchars($cat['name']) ?></td>
                    <td>
                        <form method="POST" action="/admin/categories/delete/<?= $cat['id'] ?>"
                              onsubmit="return confirm('Delete this category?')">
                            <button class="btn btn-sm btn-danger">delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>