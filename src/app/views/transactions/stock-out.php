<div class="page-header">
    <h1>Barang Keluar</h1>
</div>

<div class="card" style="max-width: 700px;">
    <div class="card-header bg-transparent d-flex align-items-center gap-2">
        <div class="stat-card-icon destructive" style="width: 32px; height: 32px; font-size: 1rem;">
            <i class="bi bi-box-arrow-up"></i>
        </div>
        <span>Form Pengeluaran Barang</span>
    </div>
    <div class="card-body">
        <?php if (empty($products)): ?>
            <div class="alert alert-warning">
                Tidak ada produk dengan stok tersedia saat ini.
            </div>
            <a href="/transactions" class="btn btn-outline-secondary">Kembali</a>
        <?php else: ?>
            <form method="POST" action="/transactions/stock-out">
                <?= csrfField() ?>

                <div class="mb-3">
                    <label for="product_id" class="form-label">Pilih Produk (Stok > 0)</label>
                    <select class="form-select" id="product_id" name="product_id" required>
                        <option value="">-- Cari dan Pilih Produk --</option>
                        <?php foreach ($products as $p): 
                            $isSelected = (old('product_id') == $p['id']);
                        ?>
                            <option value="<?= $p['id'] ?>" data-stock="<?= $p['stock'] ?>" <?= $isSelected ? 'selected' : '' ?>>
                                <?= e($p['sku']) ?> - <?= e($p['name']) ?> (Tersedia: <?= $p['stock'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3" style="max-width: 200px;">
                    <label for="quantity" class="form-label">Kuantitas Keluar</label>
                    <input type="number" class="form-control" id="quantity" name="quantity"
                           value="<?= old('quantity') ?>" min="1" required>
                    <div class="form-text text-danger d-none" id="qty-warning">Kuantitas melebihi stok!</div>
                </div>

                <div class="mb-4">
                    <label for="note" class="form-label">Catatan (Opsional)</label>
                    <textarea class="form-control" id="note" name="note" rows="3"
                              placeholder="Tujuan divisi, referensi SO, dll..."><?= old('note') ?></textarea>
                </div>

                <div class="d-flex gap-2 border-top pt-3">
                    <button type="submit" class="btn btn-danger" id="btn-submit">
                        <i class="bi bi-check-lg me-1"></i> Simpan Transaksi
                    </button>
                    <a href="/transactions" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const productSelect = document.getElementById('product_id');
    const qtyInput = document.getElementById('quantity');
    const warningText = document.getElementById('qty-warning');
    const btnSubmit = document.getElementById('btn-submit');

    if (!productSelect || !qtyInput) return;

    function checkQty() {
        const option = productSelect.options[productSelect.selectedIndex];
        if (option && option.value !== '') {
            const maxStock = parseInt(option.getAttribute('data-stock'), 10);
            const qty = parseInt(qtyInput.value, 10) || 0;
            
            // Allow setting max HTML attribute dynamically
            qtyInput.setAttribute('max', maxStock);

            if (qty > maxStock) {
                qtyInput.classList.add('is-invalid');
                warningText.classList.remove('d-none');
                btnSubmit.disabled = true;
            } else {
                qtyInput.classList.remove('is-invalid');
                warningText.classList.add('d-none');
                btnSubmit.disabled = false;
            }
        } else {
            qtyInput.removeAttribute('max');
            qtyInput.classList.remove('is-invalid');
            warningText.classList.add('d-none');
            btnSubmit.disabled = false;
        }
    }

    productSelect.addEventListener('change', checkQty);
    qtyInput.addEventListener('input', checkQty);
});
</script>
