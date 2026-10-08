<div class="page-header">
    <h1>Manajemen User</h1>
    <a href="/users/create" class="btn btn-primary">
        <i class="bi bi-person-plus me-1"></i> Tambah User
    </a>
</div>

<!-- Search -->
<form method="GET" action="/users" class="filter-toolbar">
    <div class="form-group" style="max-width: 320px;">
        <label for="search">Cari User</label>
        <div class="input-group input-group-sm">
            <input type="text" class="form-control" id="search" name="search"
                   value="<?= e($search) ?>" placeholder="Username atau nama...">
            <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
        </div>
    </div>
    <?php if ($search !== ''): ?>
        <a href="/users" class="btn btn-outline-secondary btn-sm align-self-end">Reset</a>
    <?php endif; ?>
</form>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($usersList)): ?>
            <div class="empty-state">
                <div class="empty-state-icon"><i class="bi bi-people"></i></div>
                <h5>Tidak ada hasil</h5>
                <p>Tidak ditemukan user dengan kata kunci tersebut.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Role</th>
                            <th>Terdaftar</th>
                            <th style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usersList as $u): 
                            $isSelf = $u['id'] === currentUserId();
                        ?>
                        <tr>
                            <td>
                                <div class="fw-medium"><?= e($u['full_name']) ?> <?= $isSelf ? '<span class="text-muted">(Anda)</span>' : '' ?></div>
                                <div class="text-muted" style="font-size: 0.75rem;">@<?= e($u['username']) ?></div>
                            </td>
                            <td>
                                <span class="badge-status <?= $u['role'] === 'admin' ? 'badge-admin' : 'badge-staff' ?>">
                                    <?= e($u['role']) ?>
                                </span>
                            </td>
                            <td style="font-size: 0.85rem;"><?= formatDate($u['created_at']) ?></td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="border:none; background:transparent;">
                                        <i class="bi bi-three-dots-vertical text-muted"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-2" href="/users/edit/<?= $u['id'] ?>">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                        </li>
                                        <?php if (!$isSelf): ?>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form method="POST" action="/users/delete/<?= $u['id'] ?>" class="m-0">
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
