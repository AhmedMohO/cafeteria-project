<?php $pageTitle = "Cafeteria – Login"; ?>
<?php include __DIR__ . '/../layouts/head.php'; ?>

<div class="min-vh-100 d-flex align-items-center justify-content-center bg-warning bg-opacity-10">
  <div class="card shadow-lg border-0 rounded-4 w-100 mx-3" style="max-width:420px;">
    <div class="card-body p-5">

      <div class="text-center mb-4">
        <div class="mb-2 fs-1">☕</div>
        <h2 class="fw-bold">Cafeteria</h2>
        <p class="text-muted small">Sign in to your account</p>
      </div>

<form method="POST" action="/cafeteria-project/public/login">
        <div class="mb-3">
          <label class="form-label fw-semibold">Email address</label>
          <div class="input-group">
            <span class="input-group-text bg-light border-end-0">
              <i class="bi bi-envelope text-muted"></i>
            </span>

            <input
              type="email"
              name="email"
              class="form-control border-start-0 ps-0"
              placeholder="you@example.com"
              required>

          </div>
        </div>

        <div class="mb-4">
          <label class="form-label fw-semibold">Password</label>

          <div class="input-group">
            <span class="input-group-text bg-light border-end-0">
              <i class="bi bi-lock text-muted"></i>
            </span>

            <input
              type="password"
              name="password"
              class="form-control border-start-0 ps-0"
              placeholder="••••••••"
              required>

          </div>
        </div>

        <button type="submit" class="btn btn-warning w-100 fw-bold py-2 mb-3 rounded-3">
          <i class="bi bi-box-arrow-in-right me-2"></i>Login
        </button>

      </form>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>