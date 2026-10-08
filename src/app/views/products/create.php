<div class="page-header">
    <h1>Tambah Produk</h1>
</div>

<form method="POST" action="/products/store">
    <?= csrfField() ?>

    <div class="row g-4">
        <!-- Main Form -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">Informasi Dasar</div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="sku" class="form-label">SKU (Stock Keeping Unit)</label>
                            <input type="text" class="form-control text-uppercase" id="sku" name="sku"
                                   value="<?= old('sku') ?>" required maxlength="50"
                                   placeholder="Contoh: ELK-001">
                            <div class="form-text">Kode unik untuk produk.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="category_id" class="form-label">Kategori</label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                <option value="">-- Pilih Kategori --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= old('category_id') == $cat['id'] ? 'selected' : '' ?>>
                                        <?= e($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Produk</label>
                        <input type="text" class="form-control" id="name" name="name"
                               value="<?= old('name') ?>" required maxlength="150">
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="price" class="form-label">Harga Jual (Rp)</label>
                            <input type="number" class="form-control" id="price" name="price"
                                   value="<?= old('price', '0') ?>" min="0" step="100" required>
                        </div>
                        <div class="col-md-4">
                            <label for="stock" class="form-label">Stok Awal</label>
                            <input type="number" class="form-control" id="stock" name="stock"
                                   value="<?= old('stock', '0') ?>" min="0" required>
                        </div>
                        <div class="col-md-4">
                            <label for="minimum_stock" class="form-label">Batas Stok Minimum</label>
                            <input type="number" class="form-control" id="minimum_stock" name="minimum_stock"
                                   value="<?= old('minimum_stock', '5') ?>" min="0" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">Supplier & Harga Beli</div>
                <div class="card-body p-0">
                    <?php if (empty($suppliers)): ?>
                        <div class="p-3 text-muted text-center">
                            Belum ada supplier yang terdaftar. <a href="/suppliers/create">Tambah Supplier</a>.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table mb-0 align-middle">
                                <thead>
                                    <tr>
                                        <th style="width: 40px;"></th>
                                        <th>Nama Supplier</th>
                                        <th>Harga Beli (Rp)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $oldIds = old('supplier_ids', []);
                                    if (!is_array($oldIds)) $oldIds = [];
                                    $oldPrices = old('supplier_prices', []);
                                    if (!is_array($oldPrices)) $oldPrices = [];
                                    
                                    foreach ($suppliers as $s): 
                                        $isChecked = in_array($s['id'], $oldIds);
                                        $oldPrice = $oldPrices[$s['id']] ?? '0';
                                    ?>
                                    <tr>
                                        <td class="text-center">
                                            <div class="form-check d-flex justify-content-center">
                                                <input class="form-check-input supplier-checkbox" type="checkbox" 
                                                       name="supplier_ids[]" value="<?= $s['id'] ?>" 
                                                       id="sup_<?= $s['id'] ?>" <?= $isChecked ? 'checked' : '' ?>>
                                            </div>
                                        </td>
                                        <td>
                                            <label class="form-check-label d-block cursor-pointer" for="sup_<?= $s['id'] ?>">
                                                <?= e($s['name']) ?>
                                            </label>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm supplier-price-input" 
                                                   name="supplier_prices[<?= $s['id'] ?>]" value="<?= e($oldPrice) ?>" 
                                                   min="0" step="100" <?= $isChecked ? '' : 'disabled' ?>>
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

        <!-- Sidebar Actions -->
        <div class="col-lg-4">
            <div class="card sticky-top" style="top: 80px;">
                <div class="card-body">
                    <h5 class="card-title mb-3" style="font-size: 1rem;">Aksi</h5>
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <i class="bi bi-check-lg me-1"></i> Simpan Produk
                    </button>
                    <a href="/products" class="btn btn-outline-secondary w-100">Batal</a>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-enable/disable price input based on checkbox
    const checkboxes = document.querySelectorAll('.supplier-checkbox');
    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            const row = this.closest('tr');
            const priceInput = row.querySelector('.supplier-price-input');
            priceInput.disabled = !this.checked;
            if (this.checked && priceInput.value === '0') {
                priceInput.focus();
                priceInput.select();
            }
        });
    });
});
</script>
