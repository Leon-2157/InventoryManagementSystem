<?php

class TransactionController
{
    public function index(): void
    {
        $db = getDB();

        $search = inputString('search');
        $type = inputString('type'); // IN / OUT
        $page = max(1, inputInt('page', 1));
        $perPage = 15;

        $where = [];
        $params = [];

        if ($search !== '') {
            $where[] = '(p.name LIKE :search OR p.sku LIKE :search2)';
            $params['search'] = '%' . $search . '%';
            $params['search2'] = '%' . $search . '%';
        }

        if ($type === 'IN' || $type === 'OUT') {
            $where[] = 'sm.type = :type';
            $params['type'] = $type;
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $countStmt = $db->prepare("
            SELECT COUNT(*) 
            FROM stock_movements sm
            JOIN products p ON p.id = sm.product_id
            {$whereClause}
        ");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $pagination = paginate($total, $perPage, $page);

        $sql = "
            SELECT sm.*, p.name AS product_name, p.sku, u.username
            FROM stock_movements sm
            JOIN products p ON p.id = sm.product_id
            JOIN users u ON u.id = sm.user_id
            {$whereClause}
            ORDER BY sm.created_at DESC
            LIMIT :limit OFFSET :offset
        ";
        $stmt = $db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', $pagination['per_page'], PDO::PARAM_INT);
        $stmt->bindValue(':offset', $pagination['offset'], PDO::PARAM_INT);
        $stmt->execute();
        $movements = $stmt->fetchAll();

        $pageTitle = 'Riwayat Transaksi';
        $breadcrumb = [['label' => 'Transaksi', 'url' => '']];

        ob_start();
        include __DIR__ . '/../views/transactions/index.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layouts/app.php';
    }

    public function stockInForm(): void
    {
        $db = getDB();
        $products = $db->query('SELECT id, sku, name, stock FROM products ORDER BY name')->fetchAll();
        $selectedProductId = inputInt('product_id', 0);

        $pageTitle = 'Barang Masuk';
        $breadcrumb = [
            ['label' => 'Transaksi', 'url' => '/transactions'],
            ['label' => 'Barang Masuk', 'url' => ''],
        ];

        ob_start();
        include __DIR__ . '/../views/transactions/stock-in.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layouts/app.php';
    }

    public function stockIn(): void
    {
        if (!validateCsrfToken()) {
            setFlash('error', 'Token keamanan tidak valid.');
            redirect('/transactions/stock-in');
        }

        $productId = inputInt('product_id');
        $quantity = inputInt('quantity');
        $note = inputString('note');
        $userId = currentUserId();

        if ($productId <= 0 || $quantity <= 0) {
            setFlash('error', 'Produk dan kuantitas wajib diisi dengan benar.');
            setOldInput($_POST);
            redirect('/transactions/stock-in');
        }

        $db = getDB();
        $db->beginTransaction();

        try {
            // Update stock
            $updateStmt = $db->prepare('UPDATE products SET stock = stock + :qty WHERE id = :pid');
            $updateStmt->execute(['qty' => $quantity, 'pid' => $productId]);

            // Record movement
            $moveStmt = $db->prepare('
                INSERT INTO stock_movements (product_id, user_id, type, quantity, note)
                VALUES (:pid, :uid, "IN", :qty, :note)
            ');
            $moveStmt->execute([
                'pid'  => $productId,
                'uid'  => $userId,
                'qty'  => $quantity,
                'note' => $note
            ]);

            $db->commit();
            clearOldInput();
            setFlash('success', 'Transaksi barang masuk berhasil dicatat.');
            redirect('/transactions');

        } catch (\Throwable $e) {
            $db->rollBack();
            setFlash('error', 'Terjadi kesalahan sistem saat memproses transaksi.');
            setOldInput($_POST);
            redirect('/transactions/stock-in');
        }
    }

    public function stockOutForm(): void
    {
        $db = getDB();
        // Only fetch products with stock > 0
        $products = $db->query('SELECT id, sku, name, stock FROM products WHERE stock > 0 ORDER BY name')->fetchAll();
        $selectedProductId = inputInt('product_id', 0);

        $pageTitle = 'Barang Keluar';
        $breadcrumb = [
            ['label' => 'Transaksi', 'url' => '/transactions'],
            ['label' => 'Barang Keluar', 'url' => ''],
        ];

        ob_start();
        include __DIR__ . '/../views/transactions/stock-out.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layouts/app.php';
    }

    public function stockOut(): void
    {
        if (!validateCsrfToken()) {
            setFlash('error', 'Token keamanan tidak valid.');
            redirect('/transactions/stock-out');
        }

        $productId = inputInt('product_id');
        $quantity = inputInt('quantity');
        $note = inputString('note');
        $userId = currentUserId();

        if ($productId <= 0 || $quantity <= 0) {
            setFlash('error', 'Produk dan kuantitas wajib diisi dengan benar.');
            setOldInput($_POST);
            redirect('/transactions/stock-out');
        }

        $db = getDB();
        $db->beginTransaction();

        try {
            // Check current stock with FOR UPDATE to prevent race conditions
            $stockStmt = $db->prepare('SELECT stock FROM products WHERE id = :pid FOR UPDATE');
            $stockStmt->execute(['pid' => $productId]);
            $currentStock = (int) $stockStmt->fetchColumn();

            if ($quantity > $currentStock) {
                $db->rollBack();
                setFlash('error', "Kuantitas barang keluar melebihi stok yang tersedia (Sisa: {$currentStock}).");
                setOldInput($_POST);
                redirect('/transactions/stock-out');
                return;
            }

            // Update stock
            $updateStmt = $db->prepare('UPDATE products SET stock = stock - :qty WHERE id = :pid');
            $updateStmt->execute(['qty' => $quantity, 'pid' => $productId]);

            // Record movement
            $moveStmt = $db->prepare('
                INSERT INTO stock_movements (product_id, user_id, type, quantity, note)
                VALUES (:pid, :uid, "OUT", :qty, :note)
            ');
            $moveStmt->execute([
                'pid'  => $productId,
                'uid'  => $userId,
                'qty'  => $quantity,
                'note' => $note
            ]);

            $db->commit();
            clearOldInput();
            setFlash('success', 'Transaksi barang keluar berhasil dicatat.');
            redirect('/transactions');

        } catch (\Throwable $e) {
            $db->rollBack();
            setFlash('error', 'Terjadi kesalahan sistem saat memproses transaksi.');
            setOldInput($_POST);
            redirect('/transactions/stock-out');
        }
    }
}
