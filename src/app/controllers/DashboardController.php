<?php

class DashboardController
{
    public function index(): void
    {
        $db = getDB();

        // Total products
        $totalProducts = $db->query('SELECT COUNT(id) FROM products')->fetchColumn();

        // Inventory value
        $inventoryValue = $db->query('SELECT COALESCE(SUM(stock * price), 0) FROM products')->fetchColumn();

        // Low stock count
        $lowStockCount = $db->query('SELECT COUNT(*) FROM products WHERE stock <= minimum_stock')->fetchColumn();

        // Stock in this month
        $stockIn = $db->query("
            SELECT COALESCE(SUM(quantity), 0) FROM stock_movements
            WHERE type = 'IN' AND MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())
        ")->fetchColumn();

        // Stock out this month
        $stockOut = $db->query("
            SELECT COALESCE(SUM(quantity), 0) FROM stock_movements
            WHERE type = 'OUT' AND MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())
        ")->fetchColumn();

        // Low stock products (top 10)
        $lowStockProducts = $db->query('
            SELECT p.id, p.sku, p.name, p.stock, p.minimum_stock, c.name AS category_name
            FROM products p
            JOIN categories c ON c.id = p.category_id
            WHERE p.stock <= p.minimum_stock
            ORDER BY (p.stock - p.minimum_stock) ASC
            LIMIT 10
        ')->fetchAll();

        // Recent transactions
        $recentTransactions = $db->query('
            SELECT sm.id, sm.type, sm.quantity, sm.note, sm.created_at,
                   p.name AS product_name, p.sku,
                   u.username
            FROM stock_movements sm
            JOIN products p ON p.id = sm.product_id
            JOIN users u ON u.id = sm.user_id
            ORDER BY sm.created_at DESC
            LIMIT 5
        ')->fetchAll();

        // Total categories
        $totalCategories = $db->query('SELECT COUNT(id) FROM categories')->fetchColumn();

        // Total suppliers
        $totalSuppliers = $db->query('SELECT COUNT(id) FROM suppliers')->fetchColumn();

        $pageTitle = 'Dashboard';
        $breadcrumb = [];

        ob_start();
        include __DIR__ . '/../views/dashboard/index.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layouts/app.php';
    }
}
