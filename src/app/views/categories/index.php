<div class="page-header">
    <h1>Kategori</h1>
    <a href="/categories/create" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Tambah Kategori
    </a>
</div>

<!-- Search -->
<form method="GET" action="/categories" class="filter-toolbar">
    <div class="form-group" style="max-width: 320px;">
        <label for="search">Cari Kategori</label>
        <div class="input-group input-group-sm">
            <input type="text" class="form-control" id="search" name="search"
                   value="<?= e($search) ?>" placeholder="Nama kategori...">
            <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
        </div>
    </div>
    <?php if ($search !== ''): ?>
        <a href="/categories" class="btn btn-outline-secondary btn-sm align-self-end">Reset</a>
    <?php endif; ?>
</form>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($categories)): ?>
            <div class="empty-state">
                <div class="empty-state-icon"><i class="bi bi-tag"></i></div>
                <?php if ($search !== ''): ?>
                    <h5>Tidak ada hasil</h5>
                    <p>Tidak ditemukan kategori dengan kata kunci "<?= e($search) ?>".</p>
                    <a href="/categories" class="btn btn-outline-secondary btn-sm">Reset Pencarian</a>
                <?php else: ?>
                    <h5>Belum ada kategori</h5>
                    <p>Tambahkan kategori pertama untuk memulai.</p>
                    <a href="/categories/create" class="btn btn-primary btn-sm">Tambah Kategori</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th style="width: 60px;">#</th>
                            <th>Nama Kategori</th>
                            <th class="text-center">Jumlah Produk</th>
                            <th>Dibuat</th>
                            <th style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $i => $cat): ?>
                        <tr>
                            <td class="text-muted"><?= $pagination['offset'] + $i + 1 ?></td>
                            <td><strong><?= e($cat['name']) ?></strong></td>
                            <td class="text-center"><?= $cat['product_count'] ?></td>
                            <td><?= formatDate($cat['created_at']) ?></td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="border:none; background:transparent;">
                                        <i class="bi bi-three-dots-vertical text-muted"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-2" href="/categories/edit/<?= $cat['id'] ?>">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form method="POST" action="/categories/delete/<?= $cat['id'] ?>" class="m-0">
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

            <div class="p-3">
                <?php include __DIR__ . '/../partials/pagination.php'; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
