<?php $pageTitle = "Manual Order – Admin"; ?>
<?php include __DIR__ . '/../layouts/head.php'; ?>
<?php include __DIR__ . '/../layouts/navbar_admin.php'; ?>

<div class="container py-4">
  <h4 class="fw-bold mb-4"><i class="bi bi-pencil-square me-2 text-warning"></i>Manual Order</h4>
  <?php
  // session_start();
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
                <option value="<?= $user['id'] ?>"><?= $user['name'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold small">Selected Items</label>
              <div id="selectedItems" class="mb-2">
                <!-- Items will be added here dynamically -->
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
                <option value="<?= $room['id'] ?>">Room <?= $room['no'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <hr>
            <div class="d-flex justify-content-between align-items-center mb-3">
              <span class="fw-bold">Total</span>
              <span class="fw-bold fs-5 text-warning" id="totalPrice">EGP 0</span>
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
      <div class="mb-3">
        <div class="input-group">
          <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
          <input type="text" class="form-control border-start-0" placeholder="Search products...">
        </div>
      </div>
      <h6 class="fw-bold text-muted text-uppercase mb-3 small">Click to add products</h6>
      <div class="row g-3">
        <?php foreach($products as $product): ?>
        <div class="col-6 col-md-4 col-xl-3">
          <div class="card border-2 border-transparent shadow-sm text-center rounded-3 h-100 product-card"
               data-product-id="<?= $product['id'] ?>"
               data-product-name="<?= $product['name'] ?>"
               data-product-price="<?= $product['price'] ?>"
               onclick="toggleProduct(this)">
            <div class="card-body p-3">
              <div class="fs-2 mb-1">☕</div>
              <div class="fw-semibold"><?= $product['name'] ?></div>
              <span class="badge bg-warning bg-opacity-25 text-warning-emphasis"><?= number_format($product['price'], 2) ?> EGP</span>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const selectedItems = {};

function toggleProduct(element) {
  const productId = element.dataset.productId;
  const productName = element.dataset.productName;
  const productPrice = parseFloat(element.dataset.productPrice);
  
  if (selectedItems[productId]) {
    delete selectedItems[productId];
    element.classList.remove('border-warning', 'bg-warning', 'bg-opacity-10');
  } else {
    selectedItems[productId] = {
      name: productName,
      price: productPrice,
      quantity: 1
    };
    element.classList.add('border-warning', 'bg-warning', 'bg-opacity-10');
  }
  
  updateSelectedItems();
}

function updateSelectedItems() {
  const container = document.getElementById('selectedItems');
  container.innerHTML = '';
  let total = 0;
  
  for (const [productId, item] of Object.entries(selectedItems)) {
    const itemTotal = item.price * item.quantity;
    total += itemTotal;
    
    const itemDiv = document.createElement('div');
    itemDiv.className = 'd-flex align-items-center justify-content-between mb-2 p-2 bg-light rounded-3';
    itemDiv.innerHTML = `
      <span class="fw-semibold">☕ ${item.name}</span>
      <div class="d-flex align-items-center gap-2">
        <input type="number" class="form-control form-control-sm text-center" value="${item.quantity}" min="1" 
               style="width:55px;" onchange="updateQuantity('${productId}', this.value)">
        <span class="text-muted small">EGP ${itemTotal.toFixed(2)}</span>
        <button class="btn btn-sm btn-outline-danger px-1 py-0" onclick="removeItem('${productId}')">
          <i class="bi bi-x"></i>
        </button>
      </div>
    `;
    container.appendChild(itemDiv);
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
  document.querySelector(`[data-product-id="${productId}"]`).classList.remove('border-warning', 'bg-warning', 'bg-opacity-10');
  updateSelectedItems();
}

// Add hidden inputs for form submission
document.getElementById('manualOrderForm').addEventListener('submit', function(e) {
  for (const [productId, item] of Object.entries(selectedItems)) {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = `items[${productId}]`;
    input.value = item.quantity;
    this.appendChild(input);
  }
});
</script>
</body>
</html>
