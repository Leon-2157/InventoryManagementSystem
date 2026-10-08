<div class="page-header">
    <h1>Dashboard</h1>
    <span class="text-muted" style="font-size: 0.85rem;"><?= date('l, d F Y') ?></span>
</div>

<!-- Stat Cards Row -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-card-label">Total Produk</div>
                    <div class="stat-card-value"><?= number_format($totalProducts) ?></div>
                </div>
                <div class="stat-card-icon accent">
                    <i class="bi bi-archive"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-card-label">Nilai Inventory</div>
                    <div class="stat-card-value"><?= formatRupiah($inventoryValue) ?></div>
                </div>
                <div class="stat-card-icon info">
                    <i class="bi bi-cash-stack"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-card-label">Barang Masuk (Bulan Ini)</div>
                    <div class="stat-card-value"><?= number_format($stockIn) ?></div>
                </div>
                <div class="stat-card-icon accent">
                    <i class="bi bi-box-arrow-in-down"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-card-label">Barang Keluar (Bulan Ini)</div>
                    <div class="stat-card-value"><?= number_format($stockOut) ?></div>
                </div>
                <div class="stat-card-icon destructive">
                    <i class="bi bi-box-arrow-up"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Secondary Stats -->
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-card-label">Stok Rendah</div>
                    <div class="stat-card-value"><?= number_format($lowStockCount) ?></div>
                </div>
                <div class="stat-card-icon warning">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-card-label">Kategori</div>
                    <div class="stat-card-value"><?= number_format($totalCategories) ?></div>
                </div>
                <div class="stat-card-icon info">
                    <i class="bi bi-tag"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-card-label">Supplier</div>
                    <div class="stat-card-value"><?= number_format($totalSuppliers) ?></div>
                </div>
                <div class="stat-card-icon accent">
                    <i class="bi bi-truck"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tables Row -->
<div class="row g-3">
    <!-- Low Stock Products -->
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Produk Stok Rendah</span>
                <?php if ($lowStockCount > 0): ?>
                    <span class="badge-status badge-low-stock"><?= $lowStockCount ?> produk</span>
                <?php endif; ?>
            </div>
            <div class="card-body p-0">
                <?php if (empty($lowStockProducts)): ?>
                    <div class="empty-state">
                        <div class="empty-state-icon"><i class="bi bi-check-circle"></i></div>
                        <h5>Semua stok aman</h5>
                        <p>Tidak ada produk dengan stok di bawah minimum.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>SKU</th>
                                    <th class="text-center">Stok</th>
                                    <th class="text-center">Minimum</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lowStockProducts as $p): ?>
                                <tr>
                                    <td>
                                        <a href="/products/show/<?= $p['id'] ?>"><?= e($p['name']) ?></a>
                                        <div class="text-muted" style="font-size: 0.75rem;"><?= e($p['category_name']) ?></div>
                                    </td>
                                    <td><code><?= e($p['sku']) ?></code></td>
                                    <td class="text-center">
                                        <span class="badge-status badge-low-stock"><?= $p['stock'] ?></span>
                                    </td>
                                    <td class="text-center"><?= $p['minimum_stock'] ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Transaksi Terbaru</span>
                <a href="/transactions" class="btn btn-sm btn-outline-secondary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentTransactions)): ?>
                    <div class="empty-state">
                        <div class="empty-state-icon"><i class="bi bi-arrow-left-right"></i></div>
                        <h5>Belum ada transaksi</h5>
                        <p>Catat barang masuk atau keluar untuk memulai.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th class="text-center">Tipe</th>
                                    <th class="text-center">Qty</th>
                                    <th>Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentTransactions as $t): ?>
                                <tr>
                                    <td>
                                        <?= e($t['product_name']) ?>
                                        <div class="text-muted" style="font-size: 0.7rem;"><?= e($t['username']) ?></div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge-status <?= $t['type'] === 'IN' ? 'badge-in' : 'badge-out' ?>">
                                            <?= $t['type'] === 'IN' ? 'Masuk' : 'Keluar' ?>
                                        </span>
                                    </td>
                                    <td class="text-center"><?= $t['quantity'] ?></td>
                                    <td style="font-size: 0.75rem;"><?= formatDateTime($t['created_at']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
