<div class="page-header">
    <h1>Barang Masuk</h1>
</div>

<div class="card" style="max-width: 700px;">
    <div class="card-header bg-transparent d-flex align-items-center gap-2">
        <div class="stat-card-icon accent" style="width: 32px; height: 32px; font-size: 1rem;">
            <i class="bi bi-box-arrow-in-down"></i>
        </div>
        <span>Form Penerimaan Barang</span>
    </div>
    <div class="card-body">
        <form method="POST" action="/transactions/stock-in">
            <?= csrfField() ?>

            <div class="mb-3">
                <label for="product_id" class="form-label">Pilih Produk</label>
                <select class="form-select" id="product_id" name="product_id" required>
                    <option value="">-- Cari dan Pilih Produk --</option>
                    <?php foreach ($products as $p): 
                        $isSelected = (old('product_id') == $p['id']) || ($selectedProductId === $p['id']);
                    ?>
                        <option value="<?= $p['id'] ?>" <?= $isSelected ? 'selected' : '' ?>>
                            <?= e($p['sku']) ?> - <?= e($p['name']) ?> (Stok: <?= $p['stock'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3" style="max-width: 200px;">
                <label for="quantity" class="form-label">Kuantitas Masuk</label>
                <input type="number" class="form-control" id="quantity" name="quantity"
                       value="<?= old('quantity') ?>" min="1" required>
            </div>

            <div class="mb-4">
                <label for="note" class="form-label">Catatan (Opsional)</label>
                <textarea class="form-control" id="note" name="note" rows="3"
                          placeholder="Nomor PO, Surat Jalan, dll..."><?= old('note') ?></textarea>
            </div>

            <div class="d-flex gap-2 border-top pt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> Simpan Transaksi
                </button>
                <a href="/transactions" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
