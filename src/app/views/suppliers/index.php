<div class="page-header">
    <h1>Supplier</h1>
    <a href="/suppliers/create" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Tambah Supplier
    </a>
</div>

<!-- Search -->
<form method="GET" action="/suppliers" class="filter-toolbar">
    <div class="form-group" style="max-width: 320px;">
        <label for="search">Cari Supplier</label>
        <div class="input-group input-group-sm">
            <input type="text" class="form-control" id="search" name="search"
                   value="<?= e($search) ?>" placeholder="Nama atau kontak...">
            <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
        </div>
    </div>
    <?php if ($search !== ''): ?>
        <a href="/suppliers" class="btn btn-outline-secondary btn-sm align-self-end">Reset</a>
    <?php endif; ?>
</form>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($suppliers)): ?>
            <div class="empty-state">
                <div class="empty-state-icon"><i class="bi bi-truck"></i></div>
                <?php if ($search !== ''): ?>
                    <h5>Tidak ada hasil</h5>
                    <p>Tidak ditemukan supplier dengan kata kunci "<?= e($search) ?>".</p>
                    <a href="/suppliers" class="btn btn-outline-secondary btn-sm">Reset Pencarian</a>
                <?php else: ?>
                    <h5>Belum ada supplier</h5>
                    <p>Tambahkan supplier pertama untuk memulai.</p>
                    <a href="/suppliers/create" class="btn btn-primary btn-sm">Tambah Supplier</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Nama Supplier</th>
                            <th>Kontak</th>
                            <th>Alamat</th>
                            <th class="text-center">Total Produk</th>
                            <th style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($suppliers as $s): ?>
                        <tr>
                            <td class="fw-medium"><?= e($s['name']) ?></td>
                            <td><?= e($s['contact']) ?: '<span class="text-muted">-</span>' ?></td>
                            <td>
                                <div class="text-truncate" style="max-width: 250px;" title="<?= e($s['address']) ?>">
                                    <?= e($s['address']) ?: '<span class="text-muted">-</span>' ?>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge-status badge-admin"><?= $s['product_count'] ?></span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="border:none; background:transparent;">
                                        <i class="bi bi-three-dots-vertical text-muted"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-2" href="/suppliers/edit/<?= $s['id'] ?>">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form method="POST" action="/suppliers/delete/<?= $s['id'] ?>" class="m-0">
                                                <?= csrfField() ?>
                                                <button type="submit" class="dropdown-item text-danger d-flex align-items-center gap-2 btn-delete">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
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
