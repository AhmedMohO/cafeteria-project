<?php $pageTitle = "Users – Admin"; ?>
<?php include __DIR__ . '/../layouts/head.php'; ?>
<?php include __DIR__ . '/../layouts/navbar_admin.php'; ?>

<div class="container py-4">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-people me-2 text-warning"></i>All Users</h4>
    <a href="/admin/users/create" class="btn btn-warning fw-semibold"><i class="bi bi-person-plus me-1"></i>Add User</a>
  </div>

  <?php if (!empty($error)): ?>
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle me-1"></i><?= htmlspecialchars($error) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php endif; ?>

  <form method="GET" action="/admin/users" class="mb-3">
    <div class="input-group" style="max-width:400px;">
      <input type="text" name="search" class="form-control" placeholder="Search by name or email…" value="<?= htmlspecialchars($search) ?>">
      <button class="btn btn-warning" type="submit"><i class="bi bi-search"></i></button>
      <?php if ($search !== ''): ?>
      <a href="/admin/users" class="btn btn-outline-secondary">Clear</a>
      <?php endif; ?>
    </div>
  </form>

  <div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th class="ps-4">Name</th>
            <th>Email</th>
            <th>Room</th>
            <th>Ext.</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($users)): ?>
          <tr>
            <td colspan="5" class="text-center text-muted py-4">No users found.</td>
          </tr>
          <?php endif; ?>
          <?php foreach ($users as $u): ?>
          <?php
            if (!empty($u['pic'])) {
                $avatar = '/' . ltrim($u['pic'], '/');
            } else {
                $avatar = 'https://ui-avatars.com/api/?name=' . urlencode($u['name']) . '&background=f59e0b&color=fff&size=64&bold=true';
            }
            $roomLabel = $u['room_name'] ? ($u['room_no'] . ' – ' . $u['room_name']) : '—';
          ?>
          <tr>
            <td class="ps-4">
              <div class="d-flex align-items-center gap-2">
                <img src="<?= htmlspecialchars($avatar) ?>"
                     class="rounded-circle"
                     width="32" height="32"
                     style="object-fit:cover;"
                     alt=""
                     onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($u['name']) ?>&background=f59e0b&color=fff&size=64&bold=true';"
                     >
                <span class="fw-semibold"><?= htmlspecialchars($u['name']) ?></span>
              </div>
            </td>
            <td class="text-muted small"><?= htmlspecialchars($u['email']) ?></td>
            <td><?= htmlspecialchars($roomLabel) ?></td>
            <td><?= htmlspecialchars($u['ext'] ?? '—') ?></td>
            <td>
              <div class="d-flex gap-1">
                <a href="/admin/users/edit?id=<?= $u['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $u['id'] ?>">
              <i class="bi bi-trash"></i>
            </button>

            <div class="modal fade" id="deleteModal<?= $u['id'] ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?= $u['id'] ?>" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  
                  <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel<?= $u['id'] ?>">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  
                  <div class="modal-body">
                    Are you sure you want to delete <strong><?= htmlspecialchars($u['name']) ?></strong>?
                  </div>
                  
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    
                    <form method="POST" action="/admin/users/delete" class="d-inline">
                      <input type="hidden" name="id" value="<?= $u['id'] ?>">
                      <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                  </div>
                  
                </div>
              </div>
            </div>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="card-footer bg-white border-0 d-flex justify-content-center py-3">
      <nav><ul class="pagination pagination-sm mb-0">

        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
          <a class="page-link" href="?page=<?= $page - 1 ?><?= $search !== '' ? '&search=' . urlencode($search) : '' ?>">&laquo;</a>
        </li>

        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
          <a class="page-link" href="?page=<?= $i ?><?= $search !== '' ? '&search=' . urlencode($search) : '' ?>"><?= $i ?></a>
        </li>
        <?php endfor; ?>

        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
          <a class="page-link" href="?page=<?= $page + 1 ?><?= $search !== '' ? '&search=' . urlencode($search) : '' ?>">&raquo;</a>
        </li>

      </ul></nav>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
