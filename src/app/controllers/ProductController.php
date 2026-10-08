<?php

class ProductController
{
    public function index(): void
    {
        $db = getDB();

        $search = inputString('search');
        $categoryId = inputInt('category_id', 0);
        $sort = validateSort(inputString('sort'), ['name', 'price', 'stock', 'sku', 'created_at']);
        $dir = validateDirection(inputString('dir', 'ASC'));
        $page = max(1, inputInt('page', 1));
        $perPage = 10;

        $where = [];
        $params = [];

        if ($search !== '') {
            $where[] = '(p.name LIKE :search OR p.sku LIKE :search2)';
            $params['search'] = '%' . $search . '%';
            $params['search2'] = '%' . $search . '%';
        }

        if ($categoryId > 0) {
            $where[] = 'p.category_id = :cat_id';
            $params['cat_id'] = $categoryId;
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        // Count
        $countStmt = $db->prepare("SELECT COUNT(*) FROM products p {$whereClause}");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();
        $pagination = paginate($total, $perPage, $page);

        // Fetch
        $sql = "
            SELECT p.*, c.name AS category_name
            FROM products p
            JOIN categories c ON c.id = p.category_id
            {$whereClause}
            ORDER BY p.{$sort} {$dir}
            LIMIT :limit OFFSET :offset
        ";
        $stmt = $db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', $pagination['per_page'], PDO::PARAM_INT);
        $stmt->bindValue(':offset', $pagination['offset'], PDO::PARAM_INT);
        $stmt->execute();
        $products = $stmt->fetchAll();

        // Categories for filter dropdown
        $categories = $db->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();

        $pageTitle = 'Produk';
        $breadcrumb = [['label' => 'Produk', 'url' => '']];

        ob_start();
        include __DIR__ . '/../views/products/index.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layouts/app.php';
    }

    public function show(int $id): void
    {
        $db = getDB();

        $stmt = $db->prepare('
            SELECT p.*, c.name AS category_name
            FROM products p
            JOIN categories c ON c.id = p.category_id
            WHERE p.id = :id
        ');
        $stmt->execute(['id' => $id]);
        $product = $stmt->fetch();

        if (!$product) {
            setFlash('error', 'Produk tidak ditemukan.');
            redirect('/products');
        }

        // Many-to-many: get suppliers
        $supplierStmt = $db->prepare('
            SELECT s.*, ps.supplier_price
            FROM suppliers s
            JOIN product_suppliers ps ON ps.supplier_id = s.id
            WHERE ps.product_id = :pid
            ORDER BY s.name
        ');
        $supplierStmt->execute(['pid' => $id]);
        $suppliers = $supplierStmt->fetchAll();

        // Stock mutation summary
        $mutationStmt = $db->prepare("
            SELECT
                COALESCE(SUM(CASE WHEN type='IN' THEN quantity ELSE 0 END), 0) AS total_in,
                COALESCE(SUM(CASE WHEN type='OUT' THEN quantity ELSE 0 END), 0) AS total_out
            FROM stock_movements
            WHERE product_id = :pid
        ");
        $mutationStmt->execute(['pid' => $id]);
        $mutation = $mutationStmt->fetch();

        // Recent movements
        $movementStmt = $db->prepare('
            SELECT sm.*, u.username
            FROM stock_movements sm
            JOIN users u ON u.id = sm.user_id
            WHERE sm.product_id = :pid
            ORDER BY sm.created_at DESC
            LIMIT 10
        ');
        $movementStmt->execute(['pid' => $id]);
        $movements = $movementStmt->fetchAll();

        $pageTitle = $product['name'];
        $breadcrumb = [
            ['label' => 'Produk', 'url' => '/products'],
            ['label' => $product['name'], 'url' => ''],
        ];

        ob_start();
        include __DIR__ . '/../views/products/show.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layouts/app.php';
    }

    public function create(): void
    {
        $db = getDB();
        $categories = $db->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();
        $suppliers = $db->query('SELECT id, name FROM suppliers ORDER BY name')->fetchAll();

        $pageTitle = 'Tambah Produk';
        $breadcrumb = [
            ['label' => 'Produk', 'url' => '/products'],
            ['label' => 'Tambah', 'url' => ''],
        ];

        ob_start();
        include __DIR__ . '/../views/products/create.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layouts/app.php';
    }

    public function store(): void
    {
        if (!validateCsrfToken()) {
            setFlash('error', 'Token keamanan tidak valid.');
            redirect('/products');
        }

        $data = [
            'category_id'  => inputInt('category_id'),
            'sku'           => inputString('sku'),
            'name'          => inputString('name'),
            'price'         => inputFloat('price'),
            'stock'         => inputInt('stock'),
            'minimum_stock' => inputInt('minimum_stock', 5),
        ];

        $errors = $this->validate($data);

        if (!empty($errors)) {
            setFlash('error', implode(' ', $errors));
            setOldInput($_POST);
            redirect('/products/create');
        }

        $db = getDB();

        // Check SKU uniqueness
        $stmt = $db->prepare('SELECT COUNT(*) FROM products WHERE sku = :sku');
        $stmt->execute(['sku' => $data['sku']]);
        if ((int) $stmt->fetchColumn() > 0) {
            setFlash('error', 'SKU sudah digunakan oleh produk lain.');
            setOldInput($_POST);
            redirect('/products/create');
        }

        $db->beginTransaction();
        try {
            $stmt = $db->prepare('
                INSERT INTO products (category_id, sku, name, price, stock, minimum_stock)
                VALUES (:category_id, :sku, :name, :price, :stock, :minimum_stock)
            ');
            $stmt->execute($data);
            $productId = (int) $db->lastInsertId();

            // Many-to-many: save suppliers
            $this->syncSuppliers($db, $productId);

            $db->commit();
        } catch (\Throwable $e) {
            $db->rollBack();
            setFlash('error', 'Gagal menyimpan produk.');
            setOldInput($_POST);
            redirect('/products/create');
        }

        clearOldInput();
        setFlash('success', 'Produk berhasil ditambahkan.');
        redirect('/products');
    }

    public function edit(int $id): void
    {
        $db = getDB();

        $stmt = $db->prepare('SELECT * FROM products WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $product = $stmt->fetch();

        if (!$product) {
            setFlash('error', 'Produk tidak ditemukan.');
            redirect('/products');
        }

        $categories = $db->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();
        $suppliers = $db->query('SELECT id, name FROM suppliers ORDER BY name')->fetchAll();

        // Current supplier assignments
        $currentSuppliers = $db->prepare('SELECT supplier_id, supplier_price FROM product_suppliers WHERE product_id = :pid');
        $currentSuppliers->execute(['pid' => $id]);
        $assignedSuppliers = [];
        foreach ($currentSuppliers->fetchAll() as $row) {
            $assignedSuppliers[$row['supplier_id']] = $row['supplier_price'];
        }

        $pageTitle = 'Edit Produk';
        $breadcrumb = [
            ['label' => 'Produk', 'url' => '/products'],
            ['label' => 'Edit', 'url' => ''],
        ];

        ob_start();
        include __DIR__ . '/../views/products/edit.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layouts/app.php';
    }

    public function update(int $id): void
    {
        if (!validateCsrfToken()) {
            setFlash('error', 'Token keamanan tidak valid.');
            redirect('/products');
        }

        $data = [
            'category_id'  => inputInt('category_id'),
            'sku'           => inputString('sku'),
            'name'          => inputString('name'),
            'price'         => inputFloat('price'),
            'minimum_stock' => inputInt('minimum_stock', 5),
        ];

        $errors = $this->validate($data, false);

        if (!empty($errors)) {
            setFlash('error', implode(' ', $errors));
            redirect("/products/edit/{$id}");
        }

        $db = getDB();

        // Check SKU uniqueness (exclude current)
        $stmt = $db->prepare('SELECT COUNT(*) FROM products WHERE sku = :sku AND id != :id');
        $stmt->execute(['sku' => $data['sku'], 'id' => $id]);
        if ((int) $stmt->fetchColumn() > 0) {
            setFlash('error', 'SKU sudah digunakan oleh produk lain.');
            redirect("/products/edit/{$id}");
        }

        $db->beginTransaction();
        try {
            $stmt = $db->prepare('
                UPDATE products SET category_id = :category_id, sku = :sku, name = :name,
                price = :price, minimum_stock = :minimum_stock
                WHERE id = :id
            ');
            $data['id'] = $id;
            $stmt->execute($data);

            $this->syncSuppliers($db, $id);

            $db->commit();
        } catch (\Throwable $e) {
            $db->rollBack();
            setFlash('error', 'Gagal memperbarui produk.');
            redirect("/products/edit/{$id}");
        }

        setFlash('success', 'Produk berhasil diperbarui.');
        redirect('/products');
    }

    public function delete(int $id): void
    {
        if (!validateCsrfToken()) {
            setFlash('error', 'Token keamanan tidak valid.');
            redirect('/products');
        }

        $db = getDB();

        // Check if product has movements
        $stmt = $db->prepare('SELECT COUNT(*) FROM stock_movements WHERE product_id = :id');
        $stmt->execute(['id' => $id]);
        if ((int) $stmt->fetchColumn() > 0) {
            setFlash('error', 'Produk tidak dapat dihapus karena memiliki histori transaksi.');
            redirect('/products');
        }

        $db->beginTransaction();
        try {
            // Delete supplier relationships first
            $db->prepare('DELETE FROM product_suppliers WHERE product_id = :id')->execute(['id' => $id]);
            $db->prepare('DELETE FROM products WHERE id = :id')->execute(['id' => $id]);
            $db->commit();
        } catch (\Throwable $e) {
            $db->rollBack();
            setFlash('error', 'Gagal menghapus produk.');
            redirect('/products');
        }

        setFlash('success', 'Produk berhasil dihapus.');
        redirect('/products');
    }

    private function validate(array $data, bool $includeStock = true): array
    {
        $errors = [];

        if (empty($data['name'])) $errors[] = 'Nama produk wajib diisi.';
        if (empty($data['sku'])) $errors[] = 'SKU wajib diisi.';
        if ($data['category_id'] <= 0) $errors[] = 'Kategori wajib dipilih.';
        if ($data['price'] < 0) $errors[] = 'Harga tidak boleh negatif.';
        if ($includeStock && $data['stock'] < 0) $errors[] = 'Stok tidak boleh negatif.';
        if ($data['minimum_stock'] < 0) $errors[] = 'Minimum stok tidak boleh negatif.';

        return $errors;
    }

    private function syncSuppliers(PDO $db, int $productId): void
    {
        // Remove existing
        $db->prepare('DELETE FROM product_suppliers WHERE product_id = :pid')->execute(['pid' => $productId]);

        $supplierIds = $_POST['supplier_ids'] ?? [];
        $supplierPrices = $_POST['supplier_prices'] ?? [];

        if (is_array($supplierIds)) {
            $stmt = $db->prepare('
                INSERT INTO product_suppliers (product_id, supplier_id, supplier_price)
                VALUES (:pid, :sid, :price)
            ');
            foreach ($supplierIds as $sid) {
                $sid = (int) $sid;
                if ($sid > 0) {
                    $price = (float) ($supplierPrices[$sid] ?? 0);
                    $stmt->execute(['pid' => $productId, 'sid' => $sid, 'price' => $price]);
                }
            }
        }
    }
}
