<div class="page-header">
    <h1>Edit User</h1>
</div>

<?php $isSelf = $userEdit['id'] === currentUserId(); ?>

<div class="card" style="max-width: 600px;">
    <div class="card-body">
        <form method="POST" action="/users/update/<?= $userEdit['id'] ?>">
            <?= csrfField() ?>

            <div class="mb-3">
                <label for="full_name" class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control" id="full_name" name="full_name"
                       value="<?= e($userEdit['full_name']) ?>" required autofocus maxlength="100">
            </div>

            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username"
                       value="<?= e($userEdit['username']) ?>" required maxlength="50">
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="password" class="form-label">Password (Kosongkan jika tidak diubah)</label>
                    <input type="password" class="form-control" id="password" name="password">
                </div>
                <div class="col-md-6">
                    <label for="role" class="form-label">Role</label>
                    <select class="form-select" id="role" name="role" <?= $isSelf ? 'disabled' : 'required' ?>>
                        <option value="staff" <?= $userEdit['role'] === 'staff' ? 'selected' : '' ?>>Staff</option>
                        <option value="admin" <?= $userEdit['role'] === 'admin' ? 'selected' : '' ?>>Administrator</option>
                    </select>
                    <?php if ($isSelf): ?>
                        <input type="hidden" name="role" value="<?= e($userEdit['role']) ?>">
                        <div class="form-text text-warning">Anda tidak bisa mengubah role sendiri.</div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> Perbarui
                </button>
                <a href="/users" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
