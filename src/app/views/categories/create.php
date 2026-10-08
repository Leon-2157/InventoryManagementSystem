<div class="page-header">
    <h1>Tambah Kategori</h1>
</div>

<div class="card" style="max-width: 600px;">
    <div class="card-body">
        <form method="POST" action="/categories/store">
            <?= csrfField() ?>

            <div class="mb-3">
                <label for="name" class="form-label">Nama Kategori</label>
                <input type="text" class="form-control" id="name" name="name"
                       value="<?= old('name') ?>" required autofocus maxlength="100"
                       placeholder="Contoh: Elektronik">
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> Simpan
                </button>
                <a href="/categories" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
