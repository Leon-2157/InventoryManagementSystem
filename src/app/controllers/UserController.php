<?php

class UserController
{
    public function index(): void
    {
        $db = getDB();

        $search = inputString('search');
        $page = max(1, inputInt('page', 1));
        $perPage = 10;

        $where = '';
        $params = [];

        if ($search !== '') {
            $where = 'WHERE u.username LIKE :search OR up.full_name LIKE :search2';
            $params['search'] = '%' . $search . '%';
            $params['search2'] = '%' . $search . '%';
        }

        $countStmt = $db->prepare("
            SELECT COUNT(*) 
            FROM users u 
            LEFT JOIN user_profiles up ON up.user_id = u.id 
            {$where}
        ");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $pagination = paginate($total, $perPage, $page);

        $sql = "
            SELECT u.id, u.username, u.role, u.created_at, up.full_name
            FROM users u
            LEFT JOIN user_profiles up ON up.user_id = u.id
            {$where}
            ORDER BY u.created_at DESC
            LIMIT :limit OFFSET :offset
        ";
        $stmt = $db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', $pagination['per_page'], PDO::PARAM_INT);
        $stmt->bindValue(':offset', $pagination['offset'], PDO::PARAM_INT);
        $stmt->execute();
        $usersList = $stmt->fetchAll();

        $pageTitle = 'Manajemen User';
        $breadcrumb = [['label' => 'Manajemen User', 'url' => '']];

        ob_start();
        include __DIR__ . '/../views/users/index.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layouts/app.php';
    }

    public function create(): void
    {
        $pageTitle = 'Tambah User';
        $breadcrumb = [
            ['label' => 'Manajemen User', 'url' => '/users'],
            ['label' => 'Tambah', 'url' => ''],
        ];

        ob_start();
        include __DIR__ . '/../views/users/create.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layouts/app.php';
    }

    public function store(): void
    {
        if (!validateCsrfToken()) {
            setFlash('error', 'Token keamanan tidak valid.');
            redirect('/users');
        }

        $username = trim(inputString('username'));
        $password = $_POST['password'] ?? '';
        $role = inputString('role');
        $fullName = trim(inputString('full_name'));

        $errors = [];
        if (empty($username)) $errors[] = 'Username wajib diisi.';
        if (empty($password)) $errors[] = 'Password wajib diisi.';
        if (empty($fullName)) $errors[] = 'Nama lengkap wajib diisi.';
        if (!in_array($role, ['admin', 'staff'])) $errors[] = 'Role tidak valid.';

        if (!empty($errors)) {
            setFlash('error', implode(' ', $errors));
            setOldInput($_POST);
            redirect('/users/create');
        }

        $db = getDB();

        // Check username uniqueness
        $stmt = $db->prepare('SELECT COUNT(*) FROM users WHERE username = :usr');
        $stmt->execute(['usr' => $username]);
        if ((int) $stmt->fetchColumn() > 0) {
            setFlash('error', 'Username sudah digunakan.');
            setOldInput($_POST);
            redirect('/users/create');
        }

        $db->beginTransaction();
        try {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare('INSERT INTO users (username, password_hash, role) VALUES (:u, :p, :r)');
            $stmt->execute(['u' => $username, 'p' => $hash, 'r' => $role]);
            $userId = (int) $db->lastInsertId();

            $stmtProfile = $db->prepare('INSERT INTO user_profiles (user_id, full_name) VALUES (:uid, :fname)');
            $stmtProfile->execute(['uid' => $userId, 'fname' => $fullName]);

            $db->commit();
            clearOldInput();
            setFlash('success', 'User berhasil ditambahkan.');
            redirect('/users');
        } catch (\Throwable $e) {
            $db->rollBack();
            setFlash('error', 'Gagal menambahkan user.');
            setOldInput($_POST);
            redirect('/users/create');
        }
    }

    public function edit(int $id): void
    {
        // Prevent editing self role (handled in view, but prevent access too just in case)
        $db = getDB();
        $stmt = $db->prepare('
            SELECT u.id, u.username, u.role, up.full_name
            FROM users u
            LEFT JOIN user_profiles up ON up.user_id = u.id
            WHERE u.id = :id
        ');
        $stmt->execute(['id' => $id]);
        $userEdit = $stmt->fetch();

        if (!$userEdit) {
            setFlash('error', 'User tidak ditemukan.');
            redirect('/users');
        }

        $pageTitle = 'Edit User';
        $breadcrumb = [
            ['label' => 'Manajemen User', 'url' => '/users'],
            ['label' => 'Edit', 'url' => ''],
        ];

        ob_start();
        include __DIR__ . '/../views/users/edit.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layouts/app.php';
    }

    public function update(int $id): void
    {
        if (!validateCsrfToken()) {
            setFlash('error', 'Token keamanan tidak valid.');
            redirect('/users');
        }

        $username = trim(inputString('username'));
        $password = $_POST['password'] ?? '';
        $role = inputString('role');
        $fullName = trim(inputString('full_name'));

        $errors = [];
        if (empty($username)) $errors[] = 'Username wajib diisi.';
        if (empty($fullName)) $errors[] = 'Nama lengkap wajib diisi.';
        if (!in_array($role, ['admin', 'staff'])) $errors[] = 'Role tidak valid.';

        if (!empty($errors)) {
            setFlash('error', implode(' ', $errors));
            redirect("/users/edit/{$id}");
        }

        $db = getDB();

        // Check username uniqueness (excluding current user)
        $stmt = $db->prepare('SELECT COUNT(*) FROM users WHERE username = :usr AND id != :id');
        $stmt->execute(['usr' => $username, 'id' => $id]);
        if ((int) $stmt->fetchColumn() > 0) {
            setFlash('error', 'Username sudah digunakan.');
            redirect("/users/edit/{$id}");
        }

        // Prevent changing own role
        if ($id === currentUserId() && $role !== 'admin') {
            setFlash('error', 'Anda tidak dapat mengubah role akun Anda sendiri.');
            redirect("/users/edit/{$id}");
        }

        $db->beginTransaction();
        try {
            if (!empty($password)) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare('UPDATE users SET username = :u, password_hash = :p, role = :r WHERE id = :id');
                $stmt->execute(['u' => $username, 'p' => $hash, 'r' => $role, 'id' => $id]);
            } else {
                $stmt = $db->prepare('UPDATE users SET username = :u, role = :r WHERE id = :id');
                $stmt->execute(['u' => $username, 'r' => $role, 'id' => $id]);
            }

            $stmtProfile = $db->prepare('UPDATE user_profiles SET full_name = :fname WHERE user_id = :uid');
            $stmtProfile->execute(['fname' => $fullName, 'uid' => $id]);

            $db->commit();
            setFlash('success', 'User berhasil diperbarui.');
            
            // If editing self, update session
            if ($id === currentUserId()) {
                $_SESSION['user']['username'] = $username;
                $_SESSION['user']['full_name'] = $fullName;
            }

            redirect('/users');
        } catch (\Throwable $e) {
            $db->rollBack();
            setFlash('error', 'Gagal memperbarui user.');
            redirect("/users/edit/{$id}");
        }
    }

    public function delete(int $id): void
    {
        if (!validateCsrfToken()) {
            setFlash('error', 'Token keamanan tidak valid.');
            redirect('/users');
        }

        if ($id === currentUserId()) {
            setFlash('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
            redirect('/users');
        }

        $db = getDB();

        // Check if user has stock movements
        $stmt = $db->prepare('SELECT COUNT(*) FROM stock_movements WHERE user_id = :id');
        $stmt->execute(['id' => $id]);
        if ((int) $stmt->fetchColumn() > 0) {
            setFlash('error', 'User tidak dapat dihapus karena memiliki histori transaksi.');
            redirect('/users');
        }

        $db->beginTransaction();
        try {
            $db->prepare('DELETE FROM user_profiles WHERE user_id = :id')->execute(['id' => $id]);
            $db->prepare('DELETE FROM users WHERE id = :id')->execute(['id' => $id]);
            $db->commit();
            setFlash('success', 'User berhasil dihapus.');
        } catch (\Throwable $e) {
            $db->rollBack();
            setFlash('error', 'Gagal menghapus user.');
        }

        redirect('/users');
    }
}
