<?php use Core\Auth; $pageTitle = "Users – Admin"; ?>
<?php include __DIR__ . '/../layouts/head.php'; ?>
<?php include __DIR__ . '/../layouts/navbar_admin.php'; ?>

<div class="container py-4">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-people me-2 text-warning"></i>All Users</h4>
    <a href="<?= BASE_URL ?>/admin/users/create" class="btn btn-warning fw-semibold"><i class="bi bi-person-plus me-1"></i>Add User</a>
  </div>

  <?php if (!empty($error)): ?>
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle me-1"></i><?= htmlspecialchars($error) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php endif; ?>

  <form method="GET" action="<?= BASE_URL ?>/admin/users" class="mb-3">
    <div class="row g-2 align-items-center justify-content-between mb-3">
      <div class="col-auto">
        <div class="input-group" style="max-width:400px;">
          <input type="text" name="search" class="form-control" placeholder="Search by name or email…" value="<?= htmlspecialchars($search) ?>">
          <button class="btn btn-warning" type="submit"><i class="bi bi-search"></i></button>
          <?php if ($search !== ''): ?>
          <a href="<?= BASE_URL ?>/admin/users" class="btn btn-outline-secondary">Clear</a>
          <?php endif; ?>
        </div>
      </div>
      <div class="col-auto d-flex gap-2">
        <select name="status" class="form-select" onchange="this.form.submit()">
          <option value="all" <?= $status === 'all' ? 'selected' : '' ?>>All Status</option>
          <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
          <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
        </select>
        <select name="room_id" class="form-select" onchange="this.form.submit()">
          <option value="all" <?= $room_id === null ? 'selected' : '' ?>>All Rooms</option>
          <?php foreach ($rooms as $r): ?>
            <option value="<?= $r['id'] ?>" <?= (int)$room_id === (int)$r['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($r['no'] . ' – ' . $r['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
  </form>

  <div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0 table-responsive rounded-4">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light text-secondary small text-uppercase">
          <tr>
            <th class="ps-4 py-3 border-0 text-white" style="background-color: #212529 !important;">Name</th>
            <th class="py-3 border-0 text-white" style="background-color: #212529 !important;">Email</th>
            <th class="py-3 border-0 text-white" style="background-color: #212529 !important;">Room</th>
            <th class="py-3 border-0 text-white" style="background-color: #212529 !important;">Ext.</th>
            <th class="pe-4 py-3 border-0 text-end text-white" style="background-color: #212529 !important;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($users)): ?>
          <tr>
            <td colspan="5" class="text-center text-muted py-5">
              <i class="bi bi-people fs-1 d-block mb-2 opacity-50"></i>
              No users found.
            </td>
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
            <td class="ps-4 py-3">
              <div class="d-flex align-items-center gap-3">
                <img src="<?= htmlspecialchars($avatar) ?>"
                     class="rounded-circle border"
                     width="34" height="34"
                     style="object-fit:cover;"
                     alt=""
                     onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($u['name']) ?>&background=f59e0b&color=fff&size=64&bold=true';"
                     >
                <div class="d-flex flex-column">
                  <span class="fw-bold text-dark"><?= htmlspecialchars($u['name']) ?></span>
                  <?php if (!$u['is_active']): ?>
                    <small class="text-danger" style="font-size: 0.65rem;"><i class="bi bi-circle-fill me-1"></i>Inactive</small>
                  <?php endif; ?>
                </div>
              </div>
            </td>
            <td class="text-muted small py-3"><?= htmlspecialchars($u['email']) ?></td>
            <td class="py-3"><?= htmlspecialchars($roomLabel) ?></td>
            <td class="py-3"><?= htmlspecialchars($u['ext'] ?? '—') ?></td>
            <td class="pe-4 py-3">
              <div class="d-flex gap-2 justify-content-end">
                <a href="<?= BASE_URL ?>/admin/users/edit/<?= $u['id'] ?>" class="btn btn-sm btn-outline-primary rounded-3" title="Edit User">
                  <i class="bi bi-pencil-square"></i>
                </a>
                
                <?php if ($u['is_active']): ?>
                  <?php if ((int)$u['id'] === (int)\Core\Auth::user()['id']): ?>
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-3" disabled title="Cannot delete your own account">
                      <i class="bi bi-trash"></i>
                    </button>
                  <?php else: ?>
                    <button type="button" class="btn btn-sm btn-outline-danger rounded-3" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $u['id'] ?>" title="Delete User">
                      <i class="bi bi-trash"></i>
                    </button>
                  <?php endif; ?>
                <?php else: ?>
                  <form method="POST" action="<?= BASE_URL ?>/admin/users/activate/<?= $u['id'] ?>" class="d-inline">
                    <button type="submit" class="btn btn-sm btn-outline-success rounded-3" title="Activate User">
                      <i class="bi bi-person-check-fill"></i>
                    </button>
                  </form>
                <?php endif; ?>

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
                    
                    <form method="POST" action="<?= BASE_URL ?>/admin/users/delete/<?= $u['id'] ?>" class="d-inline">
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
          <a class="page-link" href="?page=<?= $page - 1 ?><?= $search !== '' ? '&search=' . urlencode($search) : '' ?><?= $status !== 'all' ? '&status=' . urlencode($status) : '' ?><?= $room_id !== null ? '&room_id=' . $room_id : '' ?>">&laquo;</a>
        </li>

        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
          <a class="page-link" href="?page=<?= $i ?><?= $search !== '' ? '&search=' . urlencode($search) : '' ?><?= $status !== 'all' ? '&status=' . urlencode($status) : '' ?><?= $room_id !== null ? '&room_id=' . $room_id : '' ?>"><?= $i ?></a>
        </li>
        <?php endfor; ?>

        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
          <a class="page-link" href="?page=<?= $page + 1 ?><?= $search !== '' ? '&search=' . urlencode($search) : '' ?><?= $status !== 'all' ? '&status=' . urlencode($status) : '' ?><?= $room_id !== null ? '&room_id=' . $room_id : '' ?>">&raquo;</a>
        </li>

      </ul></nav>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
