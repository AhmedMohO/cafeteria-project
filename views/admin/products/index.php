<?php require __DIR__ . '/../../layouts/head.php'; ?>
<?php require __DIR__ . '/../../layouts/navbar_admin.php'; ?>

<div class="container py-4">

  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h2 class="fw-bold mb-0">All Products</h2>
      <p class="text-muted small mb-0">Manage your cafeteria products</p>
    </div>
    <a href="<?= BASE_URL ?>/admin/products/create" class="btn btn-warning fw-bold rounded-3 px-4">
      <i class="bi bi-plus-lg me-1"></i> Add Product
    </a>
  </div>

  <!-- Table Card -->
  <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
    <div class="card-body p-0 table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th class="text-white fw-semibold py-3 px-4" style="background-color: #212529 !important;">Product</th>
            <th class="text-white fw-semibold py-3 px-4" style="background-color: #212529 !important;">Category</th>
            <th class="text-white fw-semibold py-3 px-4" style="background-color: #212529 !important;">Price</th>
            <th class="text-white fw-semibold py-3 px-4" style="background-color: #212529 !important;">Image</th>
            <th class="text-white fw-semibold py-3 px-4" style="background-color: #212529 !important;">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($products)): ?>
            <tr>
              <td colspan="5" class="text-center text-muted py-5">
                <i class="bi bi-box-seam fs-1 d-block mb-2"></i>
                No products found
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($products as $product): ?>
            <tr class="border-bottom">
              <td class="px-4 py-3">
                <span class="fw-semibold"><?= htmlspecialchars($product['name']) ?></span>
              </td>
              <td class="px-4 py-3">
                <span class="badge bg-warning text-dark rounded-pill px-3">
                  <?= htmlspecialchars($product['category_name'] ?? 'N/A') ?>
                </span>
              </td>
              <td class="px-4 py-3 fw-semibold text-success">
                <?= number_format($product['price'], 2) ?> EGP
              </td>
              <td class="px-4 py-3">
                <?php if (!empty($product['image'])): ?>
                  <img src="/uploads/<?= htmlspecialchars($product['image']) ?>"
                       width="55" height="55"
                       class="rounded-3 object-fit-cover border shadow-sm">
                <?php else: ?>
                  <div class="rounded-3 border bg-light d-flex align-items-center justify-content-center"
                       style="width:55px;height:55px;">
                    <i class="bi bi-image text-muted"></i>
                  </div>
                <?php endif; ?>
              </td>
              <td class="px-4 py-3">
                <div class="d-flex gap-2 align-items-center flex-wrap">
                  <!-- Toggle -->
                  <form method="POST" action="<?= BASE_URL ?>/admin/products/toggle/<?= $product['id'] ?>">
                    <button type="submit" class="btn btn-sm rounded-pill px-3
                      <?= $product['status'] === 'available' ? 'btn-success' : 'btn-outline-secondary' ?>">
                      <i class="bi <?= $product['status'] === 'available' ? 'bi-check-circle' : 'bi-slash-circle' ?> me-1"></i>
                      <?= $product['status'] === 'available' ? 'Available' : 'Unavailable' ?>
                    </button>
                  </form>
                  <!-- Edit -->
                  <a href="<?= BASE_URL ?>/admin/products/edit/<?= $product['id'] ?>"
                     class="btn btn-sm btn-outline-primary rounded-pill px-3">
                    <i class="bi bi-pencil me-1"></i>Edit
                  </a>
                  <!-- Delete -->
                  <form method="POST" action="<?= BASE_URL ?>/admin/products/delete/<?= $product['id'] ?>"
                        onsubmit="return confirm('Are you sure you want to delete this product?')">
                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
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

    <!-- Pagination inside card -->
    <?php if ($pages > 1): ?>
    <div class="card-footer bg-white border-0 d-flex justify-content-center py-3">
      <nav>
        <ul class="pagination pagination-sm mb-0">
          <?php for ($i = 1; $i <= $pages; $i++): ?>
            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
              <a class="page-link rounded-3 mx-1" href="?page=<?= $i ?>"><?= $i ?></a>
            </li>
          <?php endfor; ?>
        </ul>
      </nav>
    </div>
    <?php endif; ?>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>