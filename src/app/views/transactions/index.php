<div class="page-header">
    <h1>Riwayat Transaksi</h1>
    <div class="d-flex gap-2">
        <a href="/transactions/stock-in" class="btn btn-outline-primary bg-white">
            <i class="bi bi-box-arrow-in-down me-1"></i> Barang Masuk
        </a>
        <a href="/transactions/stock-out" class="btn btn-primary">
            <i class="bi bi-box-arrow-up me-1"></i> Barang Keluar
        </a>
    </div>
</div>

<!-- Filter Toolbar -->
<form method="GET" action="/transactions" class="filter-toolbar">
    <div class="form-group" style="max-width: 300px;">
        <label for="search">Cari Produk</label>
        <div class="input-group input-group-sm">
            <input type="text" class="form-control" id="search" name="search"
                   value="<?= e($search) ?>" placeholder="Nama produk atau SKU...">
        </div>
    </div>
    
    <div class="form-group" style="max-width: 200px;">
        <label for="type">Tipe Transaksi</label>
        <select class="form-select form-select-sm" id="type" name="type">
            <option value="">Semua Tipe</option>
            <option value="IN" <?= $type === 'IN' ? 'selected' : '' ?>>Masuk</option>
            <option value="OUT" <?= $type === 'OUT' ? 'selected' : '' ?>>Keluar</option>
        </select>
    </div>

    <div class="d-flex gap-2 align-items-end">
        <button class="btn btn-primary btn-sm" type="submit">Filter</button>
        <?php if ($search !== '' || $type !== ''): ?>
            <a href="/transactions" class="btn btn-outline-secondary btn-sm">Reset</a>
        <?php endif; ?>
    </div>
</form>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($movements)): ?>
            <div class="empty-state">
                <div class="empty-state-icon"><i class="bi bi-arrow-left-right"></i></div>
                <?php if ($search !== '' || $type !== ''): ?>
                    <h5>Tidak ada hasil</h5>
                    <p>Coba sesuaikan kata kunci atau filter tipe transaksi Anda.</p>
                    <a href="/transactions" class="btn btn-outline-secondary btn-sm">Reset Filter</a>
                <?php else: ?>
                    <h5>Belum ada transaksi</h5>
                    <p>Catat transaksi barang masuk atau keluar untuk mulai melacak inventori.</p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th class="text-center">Tipe</th>
                            <th>Produk</th>
                            <th class="text-center">Qty</th>
                            <th>User</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($movements as $m): ?>
                        <tr>
                            <td style="font-size: 0.8rem;"><?= formatDateTime($m['created_at']) ?></td>
                            <td class="text-center">
                                <span class="badge-status <?= $m['type'] === 'IN' ? 'badge-in' : 'badge-out' ?>">
                                    <?= $m['type'] === 'IN' ? 'Masuk' : 'Keluar' ?>
                                </span>
                            </td>
                            <td>
                                <div class="fw-medium">
                                    <a href="/products/show/<?= $m['product_id'] ?>"><?= e($m['product_name']) ?></a>
                                </div>
                                <div class="text-muted" style="font-size: 0.75rem;"><code><?= e($m['sku']) ?></code></div>
                            </td>
                            <td class="text-center fw-bold <?= $m['type'] === 'IN' ? 'text-success' : 'text-danger' ?>">
                                <?= $m['type'] === 'IN' ? '+' : '-' ?><?= $m['quantity'] ?>
                            </td>
                            <td style="font-size: 0.8rem;"><?= e($m['username']) ?></td>
                            <td class="text-muted" style="font-size: 0.8rem; max-width: 250px;">
                                <div class="text-truncate" title="<?= e($m['note']) ?>">
                                    <?= e($m['note']) ?: '-' ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="p-3 border-top">
                <?php include __DIR__ . '/../partials/pagination.php'; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
