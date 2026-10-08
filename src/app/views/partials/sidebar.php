<?php
// Sidebar navigation
$user = currentUser();
$userInitial = strtoupper(substr($user['full_name'] ?? $user['username'] ?? 'U', 0, 1));
?>
<nav class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="sidebar-brand-icon">
            <i class="bi bi-box-seam"></i>
        </div>
        <div>
            <h5>Inventory</h5>
            <small>Management System</small>
        </div>
    </div>

    <div class="sidebar-nav">
        <div class="sidebar-section">
            <div class="sidebar-section-title">Menu Utama</div>
            <a href="/" class="sidebar-link <?= isActive('') === 'active' && !isset($_GET['url']) ? 'active' : (isActive('dashboard')) ?>">
                <i class="bi bi-grid-1x2"></i>
                <span>Dashboard</span>
            </a>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-section-title">Inventori</div>
            <a href="/products" class="sidebar-link <?= isActive('products') ?>">
                <i class="bi bi-archive"></i>
                <span>Produk</span>
            </a>
            <?php if (isAdmin()): ?>
            <a href="/categories" class="sidebar-link <?= isActive('categories') ?>">
                <i class="bi bi-tag"></i>
                <span>Kategori</span>
            </a>
            <a href="/suppliers" class="sidebar-link <?= isActive('suppliers') ?>">
                <i class="bi bi-truck"></i>
                <span>Supplier</span>
            </a>
            <?php endif; ?>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-section-title">Transaksi</div>
            <a href="/transactions" class="sidebar-link <?= isActive('transactions') ?>">
                <i class="bi bi-arrow-left-right"></i>
                <span>Riwayat Transaksi</span>
            </a>
            <a href="/transactions/stock-in" class="sidebar-link <?= isActive('transactions/stock-in') ?>">
                <i class="bi bi-box-arrow-in-down"></i>
                <span>Barang Masuk</span>
            </a>
            <a href="/transactions/stock-out" class="sidebar-link <?= isActive('transactions/stock-out') ?>">
                <i class="bi bi-box-arrow-up"></i>
                <span>Barang Keluar</span>
            </a>
        </div>

        <?php if (isAdmin()): ?>
        <div class="sidebar-section">
            <div class="sidebar-section-title">Administrasi</div>
            <a href="/users" class="sidebar-link <?= isActive('users') ?>">
                <i class="bi bi-people"></i>
                <span>Manajemen User</span>
            </a>
        </div>
        <?php endif; ?>
    </div>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="sidebar-user-avatar"><?= e($userInitial) ?></div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name"><?= e($user['full_name'] ?? $user['username']) ?></div>
                <div class="sidebar-user-role"><?= e($user['role']) ?></div>
            </div>
        </div>
    </div>
</nav>
