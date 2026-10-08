<?php

class SupplierController
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
            $where = 'WHERE s.name LIKE :search OR s.contact LIKE :search2';
            $params['search'] = '%' . $search . '%';
            $params['search2'] = '%' . $search . '%';
        }

        $countStmt = $db->prepare("SELECT COUNT(*) FROM suppliers s {$where}");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $pagination = paginate($total, $perPage, $page);

        // Fetch suppliers with count of products supplied
        $sql = "
            SELECT s.*, COUNT(ps.product_id) AS product_count
            FROM suppliers s
            LEFT JOIN product_suppliers ps ON ps.supplier_id = s.id
            {$where}
            GROUP BY s.id
            ORDER BY s.name ASC
            LIMIT :limit OFFSET :offset
        ";
        $stmt = $db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', $pagination['per_page'], PDO::PARAM_INT);
        $stmt->bindValue(':offset', $pagination['offset'], PDO::PARAM_INT);
        $stmt->execute();
        $suppliers = $stmt->fetchAll();

        $pageTitle = 'Supplier';
        $breadcrumb = [['label' => 'Supplier', 'url' => '']];

        ob_start();
        include __DIR__ . '/../views/suppliers/index.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layouts/app.php';
    }

    public function create(): void
    {
        $pageTitle = 'Tambah Supplier';
        $breadcrumb = [
            ['label' => 'Supplier', 'url' => '/suppliers'],
            ['label' => 'Tambah', 'url' => ''],
        ];

        ob_start();
        include __DIR__ . '/../views/suppliers/create.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layouts/app.php';
    }

    public function store(): void
    {
        if (!validateCsrfToken()) {
            setFlash('error', 'Token keamanan tidak valid.');
            redirect('/suppliers');
        }

        $data = [
            'name'    => inputString('name'),
            'contact' => inputString('contact'),
            'address' => inputString('address'),
        ];

        if (empty($data['name'])) {
            setFlash('error', 'Nama supplier wajib diisi.');
            setOldInput($_POST);
            redirect('/suppliers/create');
        }

        $db = getDB();
        $stmt = $db->prepare('
            INSERT INTO suppliers (name, contact, address)
            VALUES (:name, :contact, :address)
        ');
        $stmt->execute($data);

        clearOldInput();
        setFlash('success', 'Supplier berhasil ditambahkan.');
        redirect('/suppliers');
    }

    public function edit(int $id): void
    {
        $db = getDB();
        $stmt = $db->prepare('SELECT * FROM suppliers WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $supplier = $stmt->fetch();

        if (!$supplier) {
            setFlash('error', 'Supplier tidak ditemukan.');
            redirect('/suppliers');
        }

        $pageTitle = 'Edit Supplier';
        $breadcrumb = [
            ['label' => 'Supplier', 'url' => '/suppliers'],
            ['label' => 'Edit', 'url' => ''],
        ];

        ob_start();
        include __DIR__ . '/../views/suppliers/edit.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layouts/app.php';
    }

    public function update(int $id): void
    {
        if (!validateCsrfToken()) {
            setFlash('error', 'Token keamanan tidak valid.');
            redirect('/suppliers');
        }

        $data = [
            'id'      => $id,
            'name'    => inputString('name'),
            'contact' => inputString('contact'),
            'address' => inputString('address'),
        ];

        if (empty($data['name'])) {
            setFlash('error', 'Nama supplier wajib diisi.');
            redirect("/suppliers/edit/{$id}");
        }

        $db = getDB();
        $stmt = $db->prepare('
            UPDATE suppliers 
            SET name = :name, contact = :contact, address = :address 
            WHERE id = :id
        ');
        $stmt->execute($data);

        setFlash('success', 'Supplier berhasil diperbarui.');
        redirect('/suppliers');
    }

    public function delete(int $id): void
    {
        if (!validateCsrfToken()) {
            setFlash('error', 'Token keamanan tidak valid.');
            redirect('/suppliers');
        }

        $db = getDB();

        // Check product assignments
        $stmt = $db->prepare('SELECT COUNT(*) FROM product_suppliers WHERE supplier_id = :id');
        $stmt->execute(['id' => $id]);
        if ((int) $stmt->fetchColumn() > 0) {
            setFlash('error', 'Supplier tidak dapat dihapus karena masih terkait dengan produk.');
            redirect('/suppliers');
        }

        $stmt = $db->prepare('DELETE FROM suppliers WHERE id = :id');
        $stmt->execute(['id' => $id]);

        setFlash('success', 'Supplier berhasil dihapus.');
        redirect('/suppliers');
    }
}
