<div class="page-header">
    <h1>Tambah User</h1>
</div>

<div class="card" style="max-width: 600px;">
    <div class="card-body">
        <form method="POST" action="/users/store">
            <?= csrfField() ?>

            <div class="mb-3">
                <label for="full_name" class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control" id="full_name" name="full_name"
                       value="<?= old('full_name') ?>" required autofocus maxlength="100">
            </div>

            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username"
                       value="<?= old('username') ?>" required maxlength="50">
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="col-md-6">
                    <label for="role" class="form-label">Role</label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="staff" <?= old('role') === 'staff' ? 'selected' : '' ?>>Staff</option>
                        <option value="admin" <?= old('role') === 'admin' ? 'selected' : '' ?>>Administrator</option>
                    </select>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> Simpan
                </button>
                <a href="/users" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
