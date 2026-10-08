<div class="page-header">
    <h1>Produk</h1>
    <?php if (isAdmin()): ?>
    <a href="/products/create" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Tambah Produk
    </a>
    <?php endif; ?>
</div>

<!-- Filter Toolbar -->
<form method="GET" action="/products" class="filter-toolbar">
    <div class="form-group" style="max-width: 300px;">
        <label for="search">Cari</label>
        <div class="input-group input-group-sm">
            <input type="text" class="form-control" id="search" name="search"
                   value="<?= e($search) ?>" placeholder="Nama atau SKU...">
        </div>
    </div>
    
    <div class="form-group" style="max-width: 200px;">
        <label for="category_id">Kategori</label>
        <select class="form-select form-select-sm" id="category_id" name="category_id">
            <option value="0">Semua Kategori</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= $categoryId === $cat['id'] ? 'selected' : '' ?>>
                    <?= e($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Hidden sorts to preserve them -->
    <input type="hidden" name="sort" value="<?= e($sort) ?>">
    <input type="hidden" name="dir" value="<?= e($dir) ?>">

    <div class="d-flex gap-2 align-items-end">
        <button class="btn btn-primary btn-sm" type="submit">Filter</button>
        <?php if ($search !== '' || $categoryId > 0): ?>
            <a href="/products" class="btn btn-outline-secondary btn-sm">Reset</a>
        <?php endif; ?>
    </div>
</form>

<?php
// Helper for table headers sort links
function sortLink($column, $label, $currentSort, $currentDir) {
    $newDir = ($currentSort === $column && $currentDir === 'ASC') ? 'DESC' : 'ASC';
    $icon = '';
    $class = 'sort-link';
    if ($currentSort === $column) {
        $icon = $currentDir === 'ASC' ? '<i class="bi bi-sort-alpha-down"></i>' : '<i class="bi bi-sort-alpha-down-alt"></i>';
        if ($column === 'price' || $column === 'stock') {
            $icon = $currentDir === 'ASC' ? '<i class="bi bi-sort-numeric-down"></i>' : '<i class="bi bi-sort-numeric-down-alt"></i>';
        }
        $class .= ' active-sort';
    } else {
        $icon = '<i class="bi bi-arrow-down-up text-muted" style="font-size:0.7rem; opacity:0.5"></i>';
    }
    
    $url = '?' . buildQueryString(['sort' => $column, 'dir' => $newDir, 'page' => 1]);
    return "<a href=\"{$url}\" class=\"{$class}\">{$label} {$icon}</a>";
}
?>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($products)): ?>
            <div class="empty-state">
                <div class="empty-state-icon"><i class="bi bi-archive"></i></div>
                <?php if ($search !== '' || $categoryId > 0): ?>
                    <h5>Tidak ada hasil</h5>
                    <p>Coba sesuaikan kata kunci atau filter kategori Anda.</p>
                    <a href="/products" class="btn btn-outline-secondary btn-sm">Reset Filter</a>
                <?php else: ?>
                    <h5>Belum ada produk</h5>
                    <p>Tambahkan produk ke dalam sistem untuk memulai.</p>
                    <?php if (isAdmin()): ?>
                    <a href="/products/create" class="btn btn-primary btn-sm">Tambah Produk</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead>
                        <tr>
                            <th><?= sortLink('sku', 'SKU', $sort, $dir) ?></th>
                            <th><?= sortLink('name', 'Produk', $sort, $dir) ?></th>
                            <th class="text-end"><?= sortLink('price', 'Harga', $sort, $dir) ?></th>
                            <th class="text-center"><?= sortLink('stock', 'Stok', $sort, $dir) ?></th>
                            <th class="text-center" style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $p): 
                            $isLowStock = $p['stock'] <= $p['minimum_stock'];
                        ?>
                        <tr>
                            <td><code><?= e($p['sku']) ?></code></td>
                            <td>
                                <div class="fw-medium"><?= e($p['name']) ?></div>
                                <div class="text-muted" style="font-size: 0.75rem;"><?= e($p['category_name']) ?></div>
                            </td>
                            <td class="text-end"><?= formatRupiah($p['price']) ?></td>
                            <td class="text-center">
                                <span class="badge-status <?= $isLowStock ? 'badge-low-stock' : 'badge-in' ?>">
                                    <?= $p['stock'] ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="border:none; background:transparent;">
                                        <i class="bi bi-three-dots-vertical text-muted"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-2" href="/products/show/<?= $p['id'] ?>">
                                                <i class="bi bi-eye"></i> Detail
                                            </a>
                                        </li>
                                        <?php if (isAdmin()): ?>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-2" href="/products/edit/<?= $p['id'] ?>">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form method="POST" action="/products/delete/<?= $p['id'] ?>" class="m-0">
                                                <?= csrfField() ?>
                                                <button type="submit" class="dropdown-item text-danger d-flex align-items-center gap-2 btn-delete">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </button>
                                            </form>
                                        </li>
                                        <?php endif; ?>
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
