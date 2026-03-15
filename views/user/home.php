<?php $pageTitle = "Home – Cafeteria"; ?>
<?php include __DIR__ . '/../layouts/head.php'; ?>
<?php include __DIR__ . '/../layouts/navbar_user.php'; ?>

<div class="container py-4">
  <div class="row g-4">

    <!-- Left: Cart Panel -->
    <div class="col-lg-4">
      <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top:20px;">
        <div class="card-header bg-white border-0 pt-4 px-4">
          <h5 class="fw-bold mb-0"><i class="bi bi-cart3 me-2 text-warning"></i>Your Order</h5>
        </div>
        <div class="card-body px-4">

          <div class="mb-3">
            <div class="d-flex align-items-center justify-content-between mb-2 p-2 bg-light rounded-3">
              <span class="fw-semibold">☕ Tea</span>
              <div class="d-flex align-items-center gap-2">
                <input type="number" class="form-control form-control-sm text-center" value="5" min="1" style="width:55px;">
                <span class="text-muted small">EGP 25</span>
                <button class="btn btn-sm btn-outline-danger px-1 py-0"><i class="bi bi-x"></i></button>
              </div>
            </div>
            <div class="d-flex align-items-center justify-content-between p-2 bg-light rounded-3">
              <span class="fw-semibold">🥤 Cola</span>
              <div class="d-flex align-items-center gap-2">
                <input type="number" class="form-control form-control-sm text-center" value="3" min="1" style="width:55px;">
                <span class="text-muted small">EGP 30</span>
                <button class="btn btn-sm btn-outline-danger px-1 py-0"><i class="bi bi-x"></i></button>
              </div>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold small">Notes</label>
            <textarea class="form-control" rows="3" placeholder="e.g. 1 Tea Extra Sugar">1 Tea Extra Sugar</textarea>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold small">Deliver to Room</label>
            <select class="form-select">
              <option>Select Room</option>
              <option selected>Room 2010</option>
              <option>Room 2006</option>
              <option>Room 2008</option>
            </select>
          </div>

          <hr>
          <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="fw-bold">Total</span>
            <span class="fw-bold fs-5 text-warning">EGP 55</span>
          </div>

          <button class="btn btn-warning w-100 fw-bold py-2 rounded-3">
            <i class="bi bi-check2-circle me-2"></i>Confirm Order
          </button>
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

      <!-- Latest Order -->
      <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
          <p class="fw-bold text-muted small text-uppercase mb-2">
            <i class="bi bi-clock-history me-1"></i>Latest Order
          </p>
          <div class="d-flex gap-3">
            <div class="text-center">
              <div class="fs-2">☕</div>
              <div class="small fw-semibold">Tea</div>
            </div>
            <div class="text-center">
              <div class="fs-2">☕</div>
              <div class="small fw-semibold">Coffee</div>
            </div>
          </div>
        </div>
      </div>

      <h6 class="fw-bold text-muted text-uppercase mb-3 small">All Products</h6>
      <div class="row g-3">
        <?php
        $products = [
          ['name'=>'Tea',       'price'=>5,  'icon'=>'☕'],
          ['name'=>'Coffee',    'price'=>6,  'icon'=>'☕'],
          ['name'=>'Nescafe',   'price'=>8,  'icon'=>'☕'],
          ['name'=>'Cola',      'price'=>10, 'icon'=>'🥤'],
          ['name'=>'Juice',     'price'=>12, 'icon'=>'🧃'],
          ['name'=>'Water',     'price'=>3,  'icon'=>'💧'],
          ['name'=>'Milk',      'price'=>7,  'icon'=>'🥛'],
          ['name'=>'Cappuccino','price'=>15, 'icon'=>'☕'],
        ];
        foreach($products as $p): ?>
        <div class="col-6 col-md-4 col-xl-3">
          <div class="card border-2 border-transparent shadow-sm text-center rounded-3 h-100"
               onclick="this.classList.toggle('border-warning'); this.classList.toggle('bg-warning'); this.classList.toggle('bg-opacity-10');">
            <div class="card-body p-3">
              <div class="fs-2 mb-1"><?= $p['icon'] ?></div>
              <div class="fw-semibold"><?= $p['name'] ?></div>
              <span class="badge bg-warning bg-opacity-25 text-warning-emphasis"><?= $p['price'] ?> EGP</span>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
