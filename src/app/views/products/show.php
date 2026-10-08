<div class="page-header">
    <h1>Detail Produk</h1>
    <div class="d-flex gap-2">
        <?php if (isAdmin()): ?>
        <a href="/products/edit/<?= $product['id'] ?>" class="btn btn-primary">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <?php endif; ?>
        <a href="/transactions/stock-in?product_id=<?= $product['id'] ?>" class="btn btn-outline-secondary">
            <i class="bi bi-box-arrow-in-down me-1"></i> Restok
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Kolom Kiri: Detail -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">Informasi Produk</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted" style="font-size: 0.75rem; font-weight: 500; text-transform: uppercase;">SKU</label>
                    <div class="fs-5"><code><?= e($product['sku']) ?></code></div>
                </div>
                <div class="mb-3">
                    <label class="text-muted" style="font-size: 0.75rem; font-weight: 500; text-transform: uppercase;">Nama Produk</label>
                    <div class="fs-5 fw-medium"><?= e($product['name']) ?></div>
                </div>
                <div class="mb-3">
                    <label class="text-muted" style="font-size: 0.75rem; font-weight: 500; text-transform: uppercase;">Kategori</label>
                    <div><span class="badge-status badge-admin"><?= e($product['category_name']) ?></span></div>
                </div>
                <div class="mb-3">
                    <label class="text-muted" style="font-size: 0.75rem; font-weight: 500; text-transform: uppercase;">Harga Jual</label>
                    <div class="fs-5 text-accent fw-bold"><?= formatRupiah($product['price']) ?></div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">Status Stok</div>
            <div class="card-body">
                <div class="row text-center mb-3">
                    <div class="col-6 border-end">
                        <div class="text-muted" style="font-size: 0.75rem;">Stok Saat Ini</div>
                        <div class="fs-3 fw-bold <?= $product['stock'] <= $product['minimum_stock'] ? 'text-danger' : 'text-success' ?>">
                            <?= $product['stock'] ?>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted" style="font-size: 0.75rem;">Batas Minimum</div>
                        <div class="fs-4 mt-1"><?= $product['minimum_stock'] ?></div>
                    </div>
                </div>
                
                <hr>
                
                <div class="d-flex justify-content-between mb-2" style="font-size: 0.85rem;">
                    <span class="text-muted">Total Barang Masuk</span>
                    <span class="fw-medium text-success"><i class="bi bi-arrow-down-right"></i> <?= number_format($mutation['total_in']) ?></span>
                </div>
                <div class="d-flex justify-content-between" style="font-size: 0.85rem;">
                    <span class="text-muted">Total Barang Keluar</span>
                    <span class="fw-medium text-danger"><i class="bi bi-arrow-up-right"></i> <?= number_format($mutation['total_out']) ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Supplier & Histori -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">Daftar Supplier</div>
            <div class="card-body p-0">
                <?php if (empty($suppliers)): ?>
                    <div class="p-4 text-center text-muted">
                        <p class="mb-0">Belum ada supplier yang dikaitkan dengan produk ini.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Supplier</th>
                                    <th>Kontak</th>
                                    <th class="text-end">Harga Beli</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($suppliers as $s): ?>
                                <tr>
                                    <td class="fw-medium"><?= e($s['name']) ?></td>
                                    <td><?= e($s['contact']) ?: '-' ?></td>
                                    <td class="text-end text-accent fw-medium"><?= formatRupiah($s['supplier_price']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>10 Transaksi Terakhir</span>
                <a href="/transactions?search=<?= urlencode($product['sku']) ?>" class="btn btn-sm btn-outline-secondary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($movements)): ?>
                    <div class="p-4 text-center text-muted">
                        <p class="mb-0">Belum ada transaksi untuk produk ini.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Tipe</th>
                                    <th class="text-end">Qty</th>
                                    <th>User</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($movements as $m): ?>
                                <tr>
                                    <td style="font-size: 0.8rem;"><?= formatDateTime($m['created_at']) ?></td>
                                    <td>
                                        <span class="badge-status <?= $m['type'] === 'IN' ? 'badge-in' : 'badge-out' ?>">
                                            <?= $m['type'] === 'IN' ? 'Masuk' : 'Keluar' ?>
                                        </span>
                                    </td>
                                    <td class="text-end fw-medium"><?= $m['quantity'] ?></td>
                                    <td style="font-size: 0.8rem;"><?= e($m['username']) ?></td>
                                    <td class="text-muted" style="font-size: 0.8rem; max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        <?= e($m['note']) ?>
                                    </td>
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
