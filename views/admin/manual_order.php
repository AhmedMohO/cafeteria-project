<?php $pageTitle = "Manual Order – Admin"; ?>
<?php include __DIR__ . '/../layouts/head.php'; ?>
<?php include __DIR__ . '/../layouts/navbar_admin.php'; ?>

<div class="container py-4">
  <h4 class="fw-bold mb-4"><i class="bi bi-pencil-square me-2 text-warning"></i>Manual Order</h4>
  <?php
  if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">' . $_SESSION['success'] . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    unset($_SESSION['success']);
  }
  if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">' . $_SESSION['error'] . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    unset($_SESSION['error']);
  }
  ?>
  <div class="row g-4">

    <!-- Left: Cart -->
    <div class="col-lg-4">
      <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top:20px;">
        <div class="card-header bg-white border-0 pt-4 px-4">
          <h5 class="fw-bold mb-0"><i class="bi bi-cart3 me-2 text-warning"></i>Order Details</h5>
        </div>
        <div class="card-body px-4">
          <form method="POST" action="<?= BASE_URL ?>/admin/place-order" id="manualOrderForm">
            <div class="mb-3">
              <label class="form-label fw-semibold small">Assign to User</label>
              <select name="user_id" class="form-select" required>
                <option value="">Select User</option>
                <?php foreach($users as $user): ?>
                <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold small">Selected Items</label>
              <div id="selectedItems" class="mb-2">
                <p class="text-muted small text-center mt-2">No items selected yet.</p>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold small">Notes</label>
              <textarea name="notes" class="form-control" rows="3" placeholder="Special instructions..."></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold small">Room</label>
              <select name="room_id" class="form-select" required>
                <option value="">Select Room</option>
                <?php foreach($rooms as $room): ?>
                <option value="<?= $room['id'] ?>">Room <?= htmlspecialchars($room['no']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <hr>
            <div class="d-flex justify-content-between align-items-center mb-3">
              <span class="fw-bold">Total</span>
              <span class="fw-bold fs-5 text-warning" id="totalPrice">EGP 0.00</span>
            </div>
            <button type="submit" class="btn btn-warning w-100 fw-bold py-2 rounded-3">
              <i class="bi bi-check2-circle me-2"></i>Confirm Order
            </button>
          </form>
        </div>
      </div>
    </div>

    <!-- Right: Products -->
    <div class="col-lg-8">

      <!-- Search (JS) -->
      <div class="mb-3">
        <div class="input-group">
          <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
          <input type="text" id="searchInput" class="form-control border-start-0"
                 placeholder="Search products...">
          <button type="button" class="btn btn-outline-secondary" id="clearSearch"
                  style="display:none;" onclick="clearSearch()">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
      </div>

      <h6 class="fw-bold text-muted text-uppercase mb-3 small">Click to add products</h6>

      <!-- Products Grid -->
      <div class="row g-3" id="productsGrid">
        <?php if (empty($products)): ?>
          <div class="col-12 text-center text-muted py-5">
            <i class="bi bi-box fs-1 d-block mb-2"></i>
            No products available.
          </div>
        <?php else: ?>
          <?php foreach($products as $product): ?>
          <div class="col-6 col-md-4 col-xl-3 product-col">
            <div class="card border-2 border-transparent shadow-sm text-center rounded-3 h-100 product-card"
                 data-product-id="<?= $product['id'] ?>"
                 data-product-name="<?= htmlspecialchars($product['name']) ?>"
                 data-product-price="<?= $product['price'] ?>"
                 onclick="toggleProduct(this)">
              <div class="card-body p-3">
                <div class="fs-2 mb-1">☕</div>
                <div class="fw-semibold"><?= htmlspecialchars($product['name']) ?></div>
                <span class="badge bg-warning bg-opacity-25 text-warning-emphasis">
                  <?= number_format($product['price'], 2) ?> EGP
                </span>
                <?php if ($product['category_name']): ?>
                  <div class="text-muted small mt-1"><?= htmlspecialchars($product['category_name']) ?></div>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <?php endforeach; ?>

          <!-- JS no-results message (hidden by default) -->
          <div class="col-12 text-center text-muted py-5" id="noResults" style="display:none;">
            <i class="bi bi-search fs-1 d-block mb-2"></i>
            No products found.
          </div>

        <?php endif; ?>
      </div>

      <!-- JS Pagination -->
      <div class="d-flex justify-content-center mt-4" id="paginationContainer"></div>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ─── Config 
const ITEMS_PER_PAGE = 12;

// ─── State 
const selectedItems = {};
let currentPage  = 1;
let filteredCols = [];
let allCols      = [];

// ─── Search 
document.getElementById('searchInput').addEventListener('input', function () {
  const query = this.value.trim().toLowerCase();
  document.getElementById('clearSearch').style.display = query ? '' : 'none';

  filteredCols = allCols.filter(col => {
    const name = col.querySelector('.product-card').dataset.productName.toLowerCase();
    return name.includes(query);
  });

  document.getElementById('noResults').style.display = filteredCols.length === 0 ? '' : 'none';

  currentPage = 1;
  renderPage();
});

function clearSearch() {
  document.getElementById('searchInput').value = '';
  document.getElementById('clearSearch').style.display = 'none';
  document.getElementById('noResults').style.display = 'none';
  filteredCols = [...allCols];
  currentPage  = 1;
  renderPage();
}

// ─── Pagination 
function initPagination() {
  allCols      = Array.from(document.querySelectorAll('.product-col'));
  filteredCols = [...allCols];
  currentPage  = 1;
  renderPage();
}

function renderPage() {
  const totalPages = Math.ceil(filteredCols.length / ITEMS_PER_PAGE);
  const start = (currentPage - 1) * ITEMS_PER_PAGE;
  const end   = start + ITEMS_PER_PAGE;

  allCols.forEach(col => col.style.display = 'none');
  filteredCols.forEach((col, i) => {
    col.style.display = (i >= start && i < end) ? '' : 'none';
  });

  renderPagination(totalPages);
}

function renderPagination(totalPages) {
  const container = document.getElementById('paginationContainer');
  container.innerHTML = '';
  if (totalPages <= 1) return;

  const ul = document.createElement('ul');
  ul.className = 'pagination pagination-sm mb-0';

  function makeLi(label, page, isActive, isDisabled) {
    const li = document.createElement('li');
    li.className = `page-item${isActive ? ' active' : ''}${isDisabled ? ' disabled' : ''}`;
    const a = document.createElement('a');
    a.className = 'page-link' + (isActive ? ' bg-warning border-warning text-dark fw-bold' : ' text-dark');
    a.innerHTML = label;
    a.href = '#';
    if (!isDisabled && !isActive) {
      a.addEventListener('click', (e) => {
        e.preventDefault();
        currentPage = page;
        renderPage();
        document.getElementById('productsGrid').scrollIntoView({ behavior: 'smooth', block: 'start' });
      });
    }
    li.appendChild(a);
    return li;
  }

  // Prev
  ul.appendChild(makeLi('<i class="bi bi-chevron-left"></i>', currentPage - 1, false, currentPage === 1));

  // Page numbers with ellipsis
  let prev = null;
  for (let i = 1; i <= totalPages; i++) {
    const show = i === 1 || i === totalPages || Math.abs(i - currentPage) <= 1;
    if (!show) {
      if (prev !== null && Math.abs(i - currentPage) === 2) {
        const li = document.createElement('li');
        li.className = 'page-item disabled';
        li.innerHTML = '<span class="page-link text-dark">…</span>';
        ul.appendChild(li);
      }
      prev = i;
      continue;
    }
    ul.appendChild(makeLi(i, i, i === currentPage, false));
    prev = i;
  }

  // Next
  ul.appendChild(makeLi('<i class="bi bi-chevron-right"></i>', currentPage + 1, false, currentPage === totalPages));

  container.appendChild(ul);
}

// ─── Toggle product 
function toggleProduct(element) {
  const productId    = element.dataset.productId;
  const productName  = element.dataset.productName;
  const productPrice = parseFloat(element.dataset.productPrice);

  if (selectedItems[productId]) {
    delete selectedItems[productId];
    element.classList.remove('border-warning', 'bg-warning', 'bg-opacity-10');
  } else {
    selectedItems[productId] = { name: productName, price: productPrice, quantity: 1 };
    element.classList.add('border-warning', 'bg-warning', 'bg-opacity-10');
  }

  updateSelectedItems();
}

// ─── Render cart  
function updateSelectedItems() {
  const container = document.getElementById('selectedItems');
  container.innerHTML = '';
  let total = 0;

  const entries = Object.entries(selectedItems);

  if (entries.length === 0) {
    container.innerHTML = '<p class="text-muted small text-center mt-2">No items selected yet.</p>';
    document.getElementById('totalPrice').textContent = 'EGP 0.00';
    return;
  }

  for (const [productId, item] of entries) {
    const itemTotal = item.price * item.quantity;
    total += itemTotal;

    const div = document.createElement('div');
    div.className = 'd-flex align-items-center justify-content-between mb-2 p-2 bg-light rounded-3';
    div.innerHTML = `
      <span class="fw-semibold small">☕ ${item.name}</span>
      <div class="d-flex align-items-center gap-2">
        <input type="number" class="form-control form-control-sm text-center"
               value="${item.quantity}" min="1" style="width:55px;"
               onchange="updateQuantity('${productId}', this.value)">
        <span class="text-muted small text-nowrap">EGP ${itemTotal.toFixed(2)}</span>
        <button type="button" class="btn btn-sm btn-outline-danger px-1 py-0"
                onclick="removeItem('${productId}')">
          <i class="bi bi-x"></i>
        </button>
      </div>
    `;
    container.appendChild(div);
  }

  document.getElementById('totalPrice').textContent = `EGP ${total.toFixed(2)}`;
}

function updateQuantity(productId, quantity) {
  quantity = Math.max(1, parseInt(quantity) || 1);
  selectedItems[productId].quantity = quantity;
  updateSelectedItems();
}

function removeItem(productId) {
  delete selectedItems[productId];
  const card = document.querySelector(`[data-product-id="${productId}"]`);
  if (card) card.classList.remove('border-warning', 'bg-warning', 'bg-opacity-10');
  updateSelectedItems();
}

// ─── Form submit 
document.getElementById('manualOrderForm').addEventListener('submit', function (e) {
  if (Object.keys(selectedItems).length === 0) {
    e.preventDefault();
    alert('Please select at least one product.');
    return;
  }
  for (const [productId, item] of Object.entries(selectedItems)) {
    const input = document.createElement('input');
    input.type  = 'hidden';
    input.name  = `items[${productId}]`;
    input.value = item.quantity;
    this.appendChild(input);
  }
});

// ─── Init 
document.addEventListener('DOMContentLoaded', () => {
  initPagination();
});
</script>
</body>
</html>