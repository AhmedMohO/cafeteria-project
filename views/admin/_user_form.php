<?php

$user   = $user   ?? [];
$errors = $errors ?? [];
$old    = $old    ?? [];

function fieldVal(string $key, array $old, array $user): string {
    return htmlspecialchars($old[$key] ?? $user[$key] ?? '');
}
?>

<form method="POST" action="<?= $formAction ?>" enctype="multipart/form-data">

  <?php if ($isEdit): ?>
    <input type="hidden" name="id" value="<?= (int)($user['id'] ?? 0) ?>">
  <?php endif; ?>

  <div class="row g-3">

    <div class="col-md-6">
      <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
      <input type="text" name="name" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
             value="<?= fieldVal('name', $old, $user) ?>" required>
      <?php if (isset($errors['name'])): ?>
        <div class="invalid-feedback"><?= htmlspecialchars($errors['name']) ?></div>
      <?php endif; ?>
    </div>

    <div class="col-md-6">
      <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
      <input type="email" name="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
             value="<?= fieldVal('email', $old, $user) ?>" required>
      <?php if (isset($errors['email'])): ?>
        <div class="invalid-feedback"><?= htmlspecialchars($errors['email']) ?></div>
      <?php endif; ?>
    </div>

    <div class="col-md-6">
      <label class="form-label fw-semibold">
        Password <?= $isEdit ? '<span class="text-muted small">(leave blank to keep current)</span>' : '<span class="text-danger">*</span>' ?>
      </label>
      <input type="password" name="password"
             class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
             <?= $isEdit ? '' : 'required' ?>>
      <?php if (isset($errors['password'])): ?>
        <div class="invalid-feedback"><?= htmlspecialchars($errors['password']) ?></div>
      <?php endif; ?>
    </div>

    <div class="col-md-6">
      <label class="form-label fw-semibold">
        Confirm Password <?= $isEdit ? '' : '<span class="text-danger">*</span>' ?>
      </label>
      <input type="password" name="password_confirm"
             class="form-control <?= isset($errors['password_confirm']) ? 'is-invalid' : '' ?>"
             <?= $isEdit ? '' : 'required' ?>>
      <?php if (isset($errors['password_confirm'])): ?>
        <div class="invalid-feedback"><?= htmlspecialchars($errors['password_confirm']) ?></div>
      <?php endif; ?>
    </div>

    <div class="col-md-6">
      <label class="form-label fw-semibold">Room</label>
      <select name="room_id" class="form-select">
        <option value="">— No Room —</option>
        <?php foreach ($rooms as $room): ?>
          <option value="<?= $room['id'] ?>"
            <?= ($old['room_id'] ?? $user['room_id'] ?? '') == $room['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($room['no'] . ' – ' . $room['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-6">
      <label class="form-label fw-semibold">Extension</label>
      <input type="text" name="ext" class="form-control"
             value="<?= fieldVal('ext', $old, $user) ?>">
    </div>

    <div class="col-12">
      <label class="form-label fw-semibold">Profile Picture</label>
      <?php if ($isEdit && !empty($user['pic'])): ?>
        <div class="mb-2">
          <img src="<?= BASE_URL ?>/<?= htmlspecialchars(ltrim($user['pic'], '/')) ?>"
               width="60" height="60"
               class="rounded-circle border"
               style="object-fit:cover;"
               alt="Current photo">
          <small class="text-muted ms-2">Current photo. Upload a new one to replace it.</small>
        </div>
      <?php endif; ?>
      <input type="file" name="pic" class="form-control <?= isset($errors['pic']) ? 'is-invalid' : '' ?>"
             accept="image/*">
      <?php if (isset($errors['pic'])): ?>
        <div class="invalid-feedback"><?= htmlspecialchars($errors['pic']) ?></div>
      <?php endif; ?>
    </div>

  </div>

  <div class="d-flex gap-2 mt-4">
    <button type="submit" class="btn btn-warning fw-semibold">
      <i class="bi bi-<?= $isEdit ? 'check-lg' : 'person-plus' ?> me-1"></i>
      <?= $isEdit ? 'Save Changes' : 'Add User' ?>
    </button>
    <a href="<?= BASE_URL ?>/admin/users" class="btn btn-outline-secondary">Cancel</a>
  </div>

</form>
