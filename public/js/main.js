const productsContainer = document.querySelector('#products');
const cartItemsContainer = document.querySelector('#cart-items');
const productSearchInput = document.querySelector('#product-search');
const confirmOrderBtn = document.querySelector('#confirm-order-btn');
const orderRoomSelect = document.querySelector('#order-room');
const orderNotesTextarea = document.querySelector('#order-notes');
const orderMessageBox = document.querySelector('#order-message');
const latestOrderContainer = document.querySelector('#latest-order-container');
const endpointConfig = (typeof window !== 'undefined' && window.CAFETERIA_ENDPOINTS)
  ? window.CAFETERIA_ENDPOINTS
  : {};

const endpoints = {
  searchProducts: endpointConfig.searchProducts || '/user/search-products',
  latestOrder: endpointConfig.latestOrder || '/user/latest-order',
  placeOrder: endpointConfig.placeOrder || '/user/place-order',
  uploadsBase: endpointConfig.uploadsBase || '/uploads',
};

let cart = [];
let latestOrder = null;
let productSearchDebounceTimer = null;
let latestProductsRequestId = 0;

function escapeHtml(value) {
  return String(value)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

function formatPrice(value) {
  const numericValue = Number(value);
  if (!Number.isFinite(numericValue)) return '0.00';
  return numericValue.toFixed(2);
}

function buildUploadUrl(imageName) {
  const cleanImageName = String(imageName || '').trim();
  if (!cleanImageName) return '';

  const cleanBase = String(endpoints.uploadsBase || '/uploads').replace(/\/+$/, '');
  return `${cleanBase}/${encodeURIComponent(cleanImageName)}`;
}

function renderProductImage(imageName, altText, className = 'product-thumb', fallbackText = 'No image') {
  const imageUrl = buildUploadUrl(imageName);

  if (!imageUrl) {
    return `<div class="${className} product-image-fallback">${escapeHtml(fallbackText)}</div>`;
  }

  return `<img src="${imageUrl}" alt="${escapeHtml(altText || 'Product image')}" class="${className}" loading="lazy">`;
}

function parseQuantity(value) {
  const qty = parseInt(value, 10);
  if (Number.isNaN(qty) || qty < 1) return 1;
  if (qty > 999) return 999;
  return qty;
}

function getProductDataFromCard(card) {
  const idNode = card.querySelector('[data-id]');
  const nameNode = card.querySelector('[data-name]');
  const imageNode = card.querySelector('[data-image]');
  const priceNode = card.querySelector('[data-price]');

  const idRaw = idNode ? (idNode.getAttribute('data-id') || '0') : '0';
  const name = nameNode ? (nameNode.getAttribute('data-name') || '') : '';
  const image = imageNode ? (imageNode.getAttribute('data-image') || '') : '';
  const rawPrice = priceNode ? (priceNode.getAttribute('data-price') || '0') : '0';

  const id = Number.parseInt(idRaw, 10);
  const price = Number.parseFloat(rawPrice);

  if (!Number.isInteger(id) || id < 1 || !name || !Number.isFinite(price) || price <= 0) {
    return null;
  }

  return { id, name, image, price };
}

function syncSelectedProductCards() {
  if (!productsContainer) return;

  const selectedIds = new Set(cart.map((item) => item.id));
  productsContainer.querySelectorAll('.product-card').forEach((card) => {
    const idNode = card.querySelector('[data-id]');
    const idRaw = idNode ? (idNode.getAttribute('data-id') || '0') : '0';
    const id = Number.parseInt(idRaw, 10);
    card.classList.toggle('selected', selectedIds.has(id));
  });
}

function updateTotal() {
  const total = cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
  const totalElement = document.querySelector('#total');
  if (totalElement) {
    totalElement.textContent = `${formatPrice(total)} EGP`;
  }
}

function renderCart() {
  if (!cartItemsContainer) return;

  if (cart.length === 0) {
    cartItemsContainer.innerHTML = '<div class="text-muted small">No items selected yet.</div>';
    updateTotal();
    syncSelectedProductCards();
    return;
  }

  cartItemsContainer.innerHTML = cart.map((item, index) => `
    <div class="mb-2" data-index="${index}">
      <div class="d-flex align-items-center justify-content-between p-2 bg-light rounded-3">
        <div class="d-flex align-items-center gap-2">
          ${renderProductImage(item.image, item.name, 'cart-item-thumb')}
          <span class="fw-semibold">${escapeHtml(item.name)}</span>
        </div>
        <div class="d-flex align-items-center gap-2">
          <input
            type="number"
            class="form-control form-control-sm text-center quantity-input"
            value="${item.quantity}"
            min="1"
            max="999"
            style="width:65px;"
          >
          <span class="text-muted small">${formatPrice(item.price * item.quantity)} EGP</span>
          <button class="btn btn-sm btn-outline-danger px-1 py-0 remove-btn" type="button">
            <i class="bi bi-x"></i>
          </button>
        </div>
      </div>
    </div>
  `).join('');

  updateTotal();
  syncSelectedProductCards();
}

function renderProductsList(productList) {
  if (!productsContainer) return;

  if (!Array.isArray(productList) || productList.length === 0) {
    productsContainer.innerHTML = `
      <div class="col-12">
        <div class="alert alert-light border mb-0">No products found.</div>
      </div>
    `;
    return;
  }

  productsContainer.innerHTML = productList.map((product) => {
    const id = Number.parseInt(product.id, 10) || 0;
    const image = product.image || '';
    const name = product.name || '';
    const price = formatPrice(product.price);
    const imageHtml = renderProductImage(image, name, 'product-thumb');

    return `
      <div class="col-6 col-md-4 col-xl-3">
        <div class="card border-0 shadow-sm text-center product-card h-100" style="border-radius:14px;">
          <div class="card-body p-3">
            <div class="product-icon mb-2" data-image="${escapeHtml(image)}">${imageHtml}</div>
            <div class="fw-semibold" data-id="${id}" data-name="${escapeHtml(name)}">${escapeHtml(name)}</div>
            <span class="badge-price"><span data-price="${price}">${price}</span> EGP</span>
          </div>
        </div>
      </div>
    `;
  }).join('');

  syncSelectedProductCards();
}

async function loadProducts(searchTerm = '') {
  if (!productsContainer) return;

  const requestId = ++latestProductsRequestId;

  try {
    const response = await fetch(`${endpoints.searchProducts}?q=${encodeURIComponent(searchTerm)}`, {
      method: 'GET',
      cache: 'no-store',
    });

    const result = await response.json();
    if (requestId !== latestProductsRequestId) return;

    if (!response.ok || !result.success) {
      throw new Error(result.message || 'Failed to load products');
    }

    renderProductsList(result.products || []);
  } catch (error) {
    if (requestId !== latestProductsRequestId) return;

    productsContainer.innerHTML = `
      <div class="col-12">
        <div class="alert alert-danger mb-0">Unable to load products from database.</div>
      </div>
    `;
  }
}

function showOrderMessage(type, message, showPopup = true) {
  if (orderMessageBox) {
    const cssClass = type === 'success' ? 'alert-success' : 'alert-danger';
    orderMessageBox.innerHTML = `<div class="alert ${cssClass} py-2 mb-0" role="alert">${escapeHtml(message)}</div>`;
  }

  if (!showPopup) return;

  if (typeof Swal !== 'undefined') {
    const isSuccess = type === 'success';
    Swal.fire({
      icon: isSuccess ? 'success' : 'error',
      title: isSuccess ? 'Order Confirmed' : 'Order Error',
      text: message,
      confirmButtonColor: '#f0ad4e',
    });
  } else {
    alert(message);
  }
}

function buildNotesPreview(notes, maxLength = 55) {
  const cleanNotes = String(notes || '').trim();
  if (cleanNotes === '') return '';

  const escapedFull = escapeHtml(cleanNotes);
  if (cleanNotes.length <= maxLength) {
    return `<div class="small text-muted mt-2">Notes: ${escapedFull}</div>`;
  }

  const shortened = cleanNotes.slice(0, maxLength).trimEnd();
  return `
    <div class="small text-muted mt-2" title="${escapedFull}">
      Notes: ${escapeHtml(shortened)}... <span class="fw-semibold text-secondary">more</span>
    </div>
  `;
}

function ensureRoomOption(roomValue) {
  if (!orderRoomSelect || !roomValue) return;

  const hasOption = Array.from(orderRoomSelect.options).some(
    (opt) => opt.value === roomValue || opt.text.trim() === roomValue
  );

  if (!hasOption) {
    const option = document.createElement('option');
    option.value = roomValue;
    option.textContent = roomValue;
    orderRoomSelect.appendChild(option);
  }
}

function applyLatestOrderToCart() {
  if (!latestOrder || !Array.isArray(latestOrder.items) || latestOrder.items.length === 0) {
    return;
  }

  cart = latestOrder.items.map((item) => ({
    id: Number.parseInt(item.id, 10) || 0,
    name: String(item.name || '').trim(),
    image: String(item.image || '').trim(),
    price: Number(item.price) > 0 ? Number(item.price) : 0,
    quantity: parseQuantity(item.quantity),
  })).filter((item) => item.id > 0 && item.name !== '' && item.price > 0);

  if (orderRoomSelect) {
    const latestRoomId = Number.parseInt(latestOrder.room_id, 10);
    if (latestRoomId > 0) {
      orderRoomSelect.value = String(latestRoomId);
    } else if (latestOrder.room) {
      ensureRoomOption(String(latestOrder.room));
      orderRoomSelect.value = String(latestOrder.room);
    }
  }

  if (orderNotesTextarea) {
    orderNotesTextarea.value = latestOrder.notes || '';
  }

  renderCart();
  showOrderMessage('success', 'Latest order loaded. Click Confirm Order to place it again.', false);
}

function renderLatestOrder() {
  if (!latestOrderContainer) return;

  if (!latestOrder || !Array.isArray(latestOrder.items) || latestOrder.items.length === 0) {
    latestOrderContainer.innerHTML = '<span class="text-muted">No previous orders yet.</span>';
    return;
  }

  const previewItems = latestOrder.items.slice(0, 4).map((item) => {
    const quantity = parseQuantity(item.quantity);
    return `
      <span class="badge rounded-pill text-bg-light border fw-normal px-2 py-1 d-inline-flex align-items-center gap-2" style="font-size:0.8rem;">
        ${renderProductImage(item.image, item.name, 'latest-order-thumb')}
        <span>${escapeHtml(item.name || '')} x${quantity}</span>
      </span>
    `;
  }).join('');

  const extraItemsCount = Math.max(0, latestOrder.items.length - 4);
  const notesLine = buildNotesPreview(latestOrder.notes, 55);

  latestOrderContainer.innerHTML = `
    <button id="repeat-latest-order-btn" type="button" class="btn w-100 text-start p-0 border-0 bg-transparent">
      <div class="border rounded-4 shadow-sm px-3 py-3 bg-light-subtle">
        <div class="d-flex justify-content-between align-items-center gap-2 mb-2">
          <div>
            <div class="fw-semibold" style="font-size:1.05rem;">#${escapeHtml(latestOrder.id)} • ${escapeHtml(latestOrder.room || '-')}</div>
          </div>
          <span class="badge rounded-pill bg-warning text-dark px-3" style="font-size:0.85rem;">${formatPrice(latestOrder.total)} EGP</span>
        </div>
        <div class="d-flex flex-wrap gap-2 mt-1">
          ${previewItems}
          ${extraItemsCount > 0 ? `<span class="badge rounded-pill text-bg-secondary px-2 py-1" style="font-size:0.8rem;">+${extraItemsCount} more</span>` : ''}
        </div>
        ${notesLine}
        <div class="small text-warning fw-semibold mt-2 text-center" style="font-size:1rem;">Click to reorder</div>
      </div>
    </button>
  `;

  const repeatButton = document.querySelector('#repeat-latest-order-btn');
  if (repeatButton) {
    repeatButton.addEventListener('click', applyLatestOrderToCart);
  }
}

async function loadLatestOrder() {
  if (!latestOrderContainer) return;

  try {
    const response = await fetch(endpoints.latestOrder, {
      method: 'GET',
      cache: 'no-store',
    });

    const result = await response.json();
    if (!response.ok || !result.success) {
      throw new Error(result.message || 'Failed to fetch latest order');
    }

    latestOrder = result.order;
    renderLatestOrder();
  } catch (error) {
    latestOrder = null;
    latestOrderContainer.innerHTML = '<span class="text-muted">Unable to load latest order.</span>';
  }
}

async function askBeforeSubmit() {
  if (typeof Swal !== 'undefined') {
    const result = await Swal.fire({
      title: 'Confirm this order?',
      text: 'Your selected items will be sent and saved.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Yes, confirm',
      cancelButtonText: 'Cancel',
      confirmButtonColor: '#f0ad4e',
    });
    return result.isConfirmed;
  }

  return confirm('Confirm this order?');
}

async function submitOrder() {
  if (!confirmOrderBtn) return;

  if (cart.length === 0) {
    showOrderMessage('error', 'Please select at least one product before confirming.');
    return;
  }

  const selectedRoomValue = orderRoomSelect ? String(orderRoomSelect.value || '').trim() : '';
  const selectedRoomOption = orderRoomSelect && orderRoomSelect.selectedIndex >= 0
    ? orderRoomSelect.options[orderRoomSelect.selectedIndex]
    : null;

  const roomLabel = selectedRoomOption ? String(selectedRoomOption.text || '').trim() : selectedRoomValue;
  const roomId = /^\d+$/.test(selectedRoomValue)
    ? Number.parseInt(selectedRoomValue, 10)
    : 0;
  const notes = orderNotesTextarea ? orderNotesTextarea.value.trim() : '';

  if (!selectedRoomValue || selectedRoomValue.toLowerCase() === 'select room') {
    showOrderMessage('error', 'Please select a room.');
    return;
  }

  const shouldSubmit = await askBeforeSubmit();
  if (!shouldSubmit) return;

  const payload = {
    room_id: roomId,
    room: roomLabel,
    notes,
    items: cart.map((item) => ({
      id: item.id,
      quantity: item.quantity,
    })),
  };

  const originalText = confirmOrderBtn.innerHTML;
  confirmOrderBtn.disabled = true;
  confirmOrderBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving...';

  try {
    const response = await fetch(endpoints.placeOrder, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(payload),
    });

    let result;
    try {
      result = await response.json();
    } catch (jsonError) {
      throw new Error('Invalid response from server.');
    }

    if (!response.ok || !result.success) {
      throw new Error(result.message || 'Failed to place order');
    }

    showOrderMessage('success', `Order #${result.order_id} has been saved successfully.`);

    cart = [];
    if (orderNotesTextarea) {
      orderNotesTextarea.value = '';
    }
    renderCart();
    loadLatestOrder();
  } catch (error) {
    showOrderMessage('error', error.message || 'Unexpected error while placing order.');
  } finally {
    confirmOrderBtn.disabled = false;
    confirmOrderBtn.innerHTML = originalText;
  }
}

if (productsContainer) {
  productsContainer.addEventListener('click', (event) => {
    const card = event.target.closest('.product-card');
    if (!card || !productsContainer.contains(card)) return;

    const productData = getProductDataFromCard(card);
    if (!productData) return;

    const existingIndex = cart.findIndex((item) => item.id === productData.id);
    if (existingIndex !== -1) {
      cart.splice(existingIndex, 1);
    } else {
      cart.push({ ...productData, quantity: 1 });
    }

    renderCart();
  });
}

if (cartItemsContainer) {
  cartItemsContainer.addEventListener('change', (event) => {
    const input = event.target.closest('.quantity-input');
    if (!input) return;

    const itemRow = input.closest('[data-index]');
    if (!itemRow) return;

    const index = parseInt(itemRow.dataset.index, 10);
    if (Number.isNaN(index) || !cart[index]) return;

    const quantity = parseQuantity(input.value);
    input.value = String(quantity);
    cart[index].quantity = quantity;
    renderCart();
  });

  cartItemsContainer.addEventListener('click', (event) => {
    const removeBtn = event.target.closest('.remove-btn');
    if (!removeBtn) return;

    const itemRow = removeBtn.closest('[data-index]');
    if (!itemRow) return;

    const index = parseInt(itemRow.dataset.index, 10);
    if (Number.isNaN(index) || !cart[index]) return;

    cart.splice(index, 1);
    renderCart();
  });
}

if (productSearchInput) {
  productSearchInput.addEventListener('input', (event) => {
    const searchValue = event.target.value.trim();
    clearTimeout(productSearchDebounceTimer);
    productSearchDebounceTimer = setTimeout(() => {
      loadProducts(searchValue);
    }, 250);
  });
}

if (confirmOrderBtn) {
  confirmOrderBtn.addEventListener('click', submitOrder);
}

renderCart();
loadLatestOrder();
loadProducts();
