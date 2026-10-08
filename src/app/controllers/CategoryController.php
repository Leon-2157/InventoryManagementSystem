<?php

class CategoryController
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
            $where = 'WHERE c.name LIKE :search';
            $params['search'] = '%' . $search . '%';
        }

        $countSql = "SELECT COUNT(*) FROM categories c {$where}";
        $countStmt = $db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $pagination = paginate($total, $perPage, $page);

        $sql = "
            SELECT c.*, COUNT(p.id) AS product_count
            FROM categories c
            LEFT JOIN products p ON p.category_id = c.id
            {$where}
            GROUP BY c.id
            ORDER BY c.name ASC
            LIMIT :limit OFFSET :offset
        ";
        $stmt = $db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', $pagination['per_page'], PDO::PARAM_INT);
        $stmt->bindValue(':offset', $pagination['offset'], PDO::PARAM_INT);
        $stmt->execute();
        $categories = $stmt->fetchAll();

        $pageTitle = 'Kategori';
        $breadcrumb = [['label' => 'Kategori', 'url' => '']];

        ob_start();
        include __DIR__ . '/../views/categories/index.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layouts/app.php';
    }

    public function create(): void
    {
        $pageTitle = 'Tambah Kategori';
        $breadcrumb = [
            ['label' => 'Kategori', 'url' => '/categories'],
            ['label' => 'Tambah', 'url' => ''],
        ];

        ob_start();
        include __DIR__ . '/../views/categories/create.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layouts/app.php';
    }

    public function store(): void
    {
        if (!validateCsrfToken()) {
            setFlash('error', 'Token keamanan tidak valid.');
            redirect('/categories');
        }

        $name = inputString('name');
        $errors = [];

        if (empty($name)) {
            $errors[] = 'Nama kategori wajib diisi.';
        } elseif (strlen($name) > 100) {
            $errors[] = 'Nama kategori maksimal 100 karakter.';
        }

        if (!empty($errors)) {
            setFlash('error', implode(' ', $errors));
            setOldInput($_POST);
            redirect('/categories/create');
        }

        $db = getDB();
        $stmt = $db->prepare('INSERT INTO categories (name) VALUES (:name)');
        $stmt->execute(['name' => $name]);

        clearOldInput();
        setFlash('success', 'Kategori berhasil ditambahkan.');
        redirect('/categories');
    }

    public function edit(int $id): void
    {
        $db = getDB();
        $stmt = $db->prepare('SELECT * FROM categories WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $category = $stmt->fetch();

        if (!$category) {
            setFlash('error', 'Kategori tidak ditemukan.');
            redirect('/categories');
        }

        $pageTitle = 'Edit Kategori';
        $breadcrumb = [
            ['label' => 'Kategori', 'url' => '/categories'],
            ['label' => 'Edit', 'url' => ''],
        ];

        ob_start();
        include __DIR__ . '/../views/categories/edit.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layouts/app.php';
    }

    public function update(int $id): void
    {
        if (!validateCsrfToken()) {
            setFlash('error', 'Token keamanan tidak valid.');
            redirect('/categories');
        }

        $name = inputString('name');
        $errors = [];

        if (empty($name)) {
            $errors[] = 'Nama kategori wajib diisi.';
        } elseif (strlen($name) > 100) {
            $errors[] = 'Nama kategori maksimal 100 karakter.';
        }

        if (!empty($errors)) {
            setFlash('error', implode(' ', $errors));
            redirect("/categories/edit/{$id}");
        }

        $db = getDB();
        $stmt = $db->prepare('UPDATE categories SET name = :name WHERE id = :id');
        $stmt->execute(['name' => $name, 'id' => $id]);

        setFlash('success', 'Kategori berhasil diperbarui.');
        redirect('/categories');
    }

    public function delete(int $id): void
    {
        if (!validateCsrfToken()) {
            setFlash('error', 'Token keamanan tidak valid.');
            redirect('/categories');
        }

        $db = getDB();

        // Check if category has products
        $stmt = $db->prepare('SELECT COUNT(*) FROM products WHERE category_id = :id');
        $stmt->execute(['id' => $id]);
        if ((int) $stmt->fetchColumn() > 0) {
            setFlash('error', 'Kategori tidak dapat dihapus karena masih memiliki produk terkait.');
            redirect('/categories');
        }

        $stmt = $db->prepare('DELETE FROM categories WHERE id = :id');
        $stmt->execute(['id' => $id]);

        setFlash('success', 'Kategori berhasil dihapus.');
        redirect('/categories');
    }
}
