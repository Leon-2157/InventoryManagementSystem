<div class="page-header">
    <h1>Tambah Supplier</h1>
</div>

<div class="card" style="max-width: 700px;">
    <div class="card-body">
        <form method="POST" action="/suppliers/store">
            <?= csrfField() ?>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">Nama Supplier</label>
                    <input type="text" class="form-control" id="name" name="name"
                           value="<?= old('name') ?>" required autofocus maxlength="100">
                </div>
                <div class="col-md-6">
                    <label for="contact" class="form-label">Kontak / Telepon</label>
                    <input type="text" class="form-control" id="contact" name="contact"
                           value="<?= old('contact') ?>" maxlength="100">
                </div>
            </div>

            <div class="mb-4">
                <label for="address" class="form-label">Alamat Lengkap</label>
                <textarea class="form-control" id="address" name="address" rows="3"><?= old('address') ?></textarea>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> Simpan
                </button>
                <a href="/suppliers" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
