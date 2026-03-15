<?php require __DIR__ . '/../../layouts/head.php'; ?>
<?php require __DIR__ . '/../../layouts/navbar_admin.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>All Products</h1>
        <a href="<?= BASE_URL ?>/admin/products/create" class="btn btn-warning fw-bold">+ Add product</a>
    </div>

    <table class="table table-bordered align-middle">
        <thead class="table-warning">
            <tr>
                <th>Product</th>
                <th>Category</th>
                <th>Price</th>
                <th>Image</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($products)): ?>
                <tr>
                    <td colspan="5" class="text-center text-muted">No products found</td>
                </tr>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td><?= htmlspecialchars($product['name']) ?></td>
                    <td><?= htmlspecialchars($product['category_name'] ?? 'N/A') ?></td>
                    <td><?= number_format($product['price'], 2) ?> EGP</td>
                    <td>
                        <?php if (!empty($product['image'])): ?>
                            <img src="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($product['image']) ?>"
                                 width="60" height="60"
                                 class="rounded object-fit-cover border">
                        <?php else: ?>
                            <span class="text-muted">N/A</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="d-flex gap-1 flex-wrap">
                            <form method="POST" action="<?= BASE_URL ?>/admin/products/toggle/<?= $product['id'] ?>">
                                <button class="btn btn-sm <?= $product['status'] === 'available' ? 'btn-success' : 'btn-secondary' ?>">
                                    <?= $product['status'] === 'available' ? 'available' : 'unavailable' ?>
                                </button>
                            </form>
                            <a href="<?= BASE_URL ?>/admin/products/edit/<?= $product['id'] ?>"
                               class="btn btn-sm btn-primary">edit</a>
                            <form method="POST" action="<?= BASE_URL ?>/admin/products/delete/<?= $product['id'] ?>"
                                  onsubmit="return confirm('Are you sure you want to delete this product?')">
                                <button class="btn btn-sm btn-danger">delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if ($pages > 1): ?>
    <nav>
        <ul class="pagination">
            <?php for ($i = 1; $i <= $pages; $i++): ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>