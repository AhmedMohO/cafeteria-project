<?php require __DIR__ . '/../../layouts/head.php'; ?>
<?php require __DIR__ . '/../../layouts/navbar_admin.php'; ?>

<div class="container py-4">

  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">All Products</h2>
    <a href="<?= BASE_URL ?>/admin/products/create" class="btn btn-warning fw-bold rounded-3">
      <i class="bi bi-plus-lg me-1"></i> Add product
    </a>
  </div>

  <!-- Table Card -->
  <div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
      <table class="table table-hover align-middle mb-0 rounded-4 overflow-hidden">
        <thead class="table-dark">
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
              <td colspan="5" class="text-center text-muted py-4">No products found</td>
            </tr>
          <?php else: ?>
            <?php foreach ($products as $product): ?>
            <tr>
              <td class="fw-semibold"><?= htmlspecialchars($product['name']) ?></td>
              <td><?= htmlspecialchars($product['category_name'] ?? 'N/A') ?></td>
              <td><?= number_format($product['price'], 2) ?> EGP</td>
              <td>
                <?php if (!empty($product['image'])): ?>
                  <img src="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($product['image']) ?>"
                       width="60" height="60"
                       class="rounded-3 object-fit-cover border">
                <?php else: ?>
                  <span class="text-muted">N/A</span>
                <?php endif; ?>
              </td>
              <td>
                <div class="d-flex gap-1 flex-wrap">
                  <!-- Toggle -->
                  <form method="POST" action="<?= BASE_URL ?>/admin/products/toggle/<?= $product['id'] ?>">
                        <button class="btn btn-sm rounded-3 <?= $product['status'] === 'available' ? 'btn-success' : 'btn-secondary' ?>">
                            <?= $product['status'] === 'available' ? 'Available' : 'Unavailable' ?>
                        </button>
                 </form>
                  <!-- Edit -->
                  <a href="<?= BASE_URL ?>/admin/products/edit/<?= $product['id'] ?>"
                     class="btn btn-sm btn-primary rounded-3">
                    <i class="bi bi-pencil me-1"></i>Edit
                  </a>
                  <!-- Delete -->
                  <form method="POST" action="<?= BASE_URL ?>/admin/products/delete/<?= $product['id'] ?>"
                        onsubmit="return confirm('Are you sure you want to delete this product?')">
                    <button class="btn btn-sm btn-danger rounded-3">
                      <i class="bi bi-trash me-1"></i>Delete
                    </button>
                  </form>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Pagination -->
  <?php if ($pages > 1): ?>
  <nav class="mt-4">
    <ul class="pagination justify-content-center">
      <?php for ($i = 1; $i <= $pages; $i++): ?>
        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
          <a class="page-link rounded-3 mx-1" href="?page=<?= $i ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>
    </ul>
  </nav>
  <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>