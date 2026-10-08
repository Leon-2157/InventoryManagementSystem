<div class="login-wrapper">
    <div class="login-card">
        <div class="login-brand">
            <div class="login-brand-icon">
                <i class="bi bi-box-seam"></i>
            </div>
            <h3>Inventory System</h3>
            <p>Masuk ke akun Anda</p>
        </div>

        <?php if ($msg = getFlash('error')): ?>
            <div class="alert alert-danger mb-3"><?= e($msg) ?></div>
        <?php endif; ?>
        <?php if ($msg = getFlash('success')): ?>
            <div class="alert alert-success mb-3"><?= e($msg) ?></div>
        <?php endif; ?>

        <form method="POST" action="/login">
            <?= csrfField() ?>

            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username"
                       placeholder="Masukkan username" required autofocus>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password"
                       placeholder="Masukkan password" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
            </button>
        </form>
    </div>
</div>
