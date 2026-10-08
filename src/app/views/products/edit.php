<div class="page-header">
    <h1>Edit Produk</h1>
</div>

<form method="POST" action="/products/update/<?= $product['id'] ?>">
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
                                   value="<?= e($product['sku']) ?>" required maxlength="50">
                        </div>
                        <div class="col-md-6">
                            <label for="category_id" class="form-label">Kategori</label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                <option value="">-- Pilih Kategori --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= $product['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                                        <?= e($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Produk</label>
                        <input type="text" class="form-control" id="name" name="name"
                               value="<?= e($product['name']) ?>" required maxlength="150">
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="price" class="form-label">Harga Jual (Rp)</label>
                            <input type="number" class="form-control" id="price" name="price"
                                   value="<?= (float)$product['price'] ?>" min="0" step="100" required>
                        </div>
                        <div class="col-md-4">
                            <label for="stock_readonly" class="form-label">Stok Saat Ini</label>
                            <input type="text" class="form-control" id="stock_readonly" value="<?= $product['stock'] ?>" disabled>
                            <div class="form-text">Ubah stok via transaksi.</div>
                        </div>
                        <div class="col-md-4">
                            <label for="minimum_stock" class="form-label">Batas Stok Minimum</label>
                            <input type="number" class="form-control" id="minimum_stock" name="minimum_stock"
                                   value="<?= $product['minimum_stock'] ?>" min="0" required>
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
                                    <?php foreach ($suppliers as $s): 
                                        $isChecked = isset($assignedSuppliers[$s['id']]);
                                        $price = $isChecked ? $assignedSuppliers[$s['id']] : 0;
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
                                                   name="supplier_prices[<?= $s['id'] ?>]" value="<?= (float)$price ?>" 
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
                        <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
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
