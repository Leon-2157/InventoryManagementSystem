-- Inventory Management System - Seed Data

-- Users (password: admin123 dan staff123)
INSERT INTO users (username, password_hash, role) VALUES
('admin', '$2y$12$Xs3qCzUGrHQsYHBhV6OWROQJKre8aI.6lEe611T0K0EeW4Ut/jJea', 'admin'),
('staff', '$2y$12$9KWEEfYfnc.j02NoZWD9CuUioVHtP5IFklx3Tw3k8DjC8vrrgbXSi', 'staff');

-- User Profiles
INSERT INTO user_profiles (user_id, full_name) VALUES
(1, 'Administrator Sistem'),
(2, 'Staff Gudang');

-- Categories
INSERT INTO categories (name) VALUES
('Elektronik'),
('Peralatan Kantor'),
('Bahan Baku'),
('Suku Cadang'),
('Perlengkapan Gudang');

-- Products
INSERT INTO products (category_id, sku, name, price, stock, minimum_stock) VALUES
(1, 'ELK-001', 'Keyboard Mechanical RGB', 750000.00, 25, 5),
(1, 'ELK-002', 'Mouse Wireless Ergonomic', 350000.00, 40, 10),
(1, 'ELK-003', 'Monitor LED 24 Inch', 2500000.00, 8, 3),
(1, 'ELK-004', 'USB Hub 7 Port', 180000.00, 15, 5),
(2, 'KNT-001', 'Kertas HVS A4 80gsm', 55000.00, 200, 50),
(2, 'KNT-002', 'Tinta Printer Hitam', 125000.00, 30, 10),
(2, 'KNT-003', 'Stapler Heavy Duty', 85000.00, 12, 5),
(3, 'BHN-001', 'Kabel UTP Cat6 (1 Roll)', 450000.00, 10, 3),
(3, 'BHN-002', 'Connector RJ45 (100pcs)', 75000.00, 50, 15),
(3, 'BHN-003', 'Pipa Conduit 20mm', 35000.00, 80, 20),
(4, 'SKC-001', 'Bearing 6205', 45000.00, 100, 25),
(4, 'SKC-002', 'V-Belt A68', 120000.00, 20, 5),
(4, 'SKC-003', 'Filter Oli Mesin', 65000.00, 35, 10),
(5, 'GDG-001', 'Palet Kayu 120x100cm', 250000.00, 15, 5),
(5, 'GDG-002', 'Stretch Film 50cm', 95000.00, 4, 10);

-- Suppliers
INSERT INTO suppliers (name, contact, address) VALUES
('PT Sinar Elektronik', '021-5551234', 'Jl. Mangga Dua Raya No. 45, Jakarta Utara'),
('CV Maju Bersama', '031-8887654', 'Jl. Rungkut Industri III No. 12, Surabaya'),
('UD Teknik Jaya', '022-7654321', 'Jl. Soekarno-Hatta No. 88, Bandung'),
('PT Global Supply', '024-3456789', 'Jl. Kaligawe KM 5, Semarang'),
('CV Perkasa Material', '061-4567890', 'Jl. Gatot Subroto No. 100, Medan');

-- Product-Supplier Relationships
INSERT INTO product_suppliers (product_id, supplier_id, supplier_price) VALUES
(1, 1, 650000.00),
(2, 1, 280000.00),
(3, 1, 2200000.00),
(4, 1, 150000.00),
(5, 2, 48000.00),
(6, 2, 105000.00),
(7, 2, 70000.00),
(8, 3, 380000.00),
(9, 3, 60000.00),
(10, 3, 28000.00),
(11, 4, 35000.00),
(12, 4, 95000.00),
(13, 4, 50000.00),
(14, 5, 200000.00),
(15, 5, 78000.00),
(1, 4, 670000.00),
(8, 4, 400000.00),
(11, 3, 38000.00);

-- Stock Movements
INSERT INTO stock_movements (product_id, user_id, type, quantity, note, created_at) VALUES
(1, 1, 'IN', 30, 'Pembelian awal keyboard', '2026-09-01 08:00:00'),
(2, 1, 'IN', 50, 'Pembelian awal mouse', '2026-09-01 08:30:00'),
(3, 1, 'IN', 10, 'Pembelian awal monitor', '2026-09-02 09:00:00'),
(5, 1, 'IN', 250, 'Restok kertas HVS', '2026-09-03 10:00:00'),
(11, 1, 'IN', 120, 'Pembelian bearing batch 1', '2026-09-05 08:00:00'),
(1, 2, 'OUT', 5, 'Permintaan divisi IT', '2026-09-10 13:00:00'),
(2, 2, 'OUT', 10, 'Permintaan divisi Marketing', '2026-09-10 14:00:00'),
(3, 2, 'OUT', 2, 'Setup ruang meeting baru', '2026-09-12 10:00:00'),
(5, 2, 'OUT', 50, 'Distribusi ke lantai 2', '2026-09-15 09:00:00'),
(11, 2, 'OUT', 20, 'Maintenance mesin produksi', '2026-09-18 11:00:00'),
(6, 1, 'IN', 40, 'Restok tinta printer', '2026-09-20 08:00:00'),
(6, 2, 'OUT', 10, 'Distribusi ke bagian admin', '2026-09-22 14:00:00'),
(14, 1, 'IN', 20, 'Pembelian palet baru', '2026-09-25 09:00:00'),
(14, 2, 'OUT', 5, 'Pengiriman ke gudang cabang', '2026-09-26 10:00:00'),
(15, 1, 'IN', 10, 'Restok stretch film', '2026-09-28 08:00:00'),
(15, 2, 'OUT', 6, 'Packing pengiriman batch 9', '2026-09-29 14:00:00'),
(9, 1, 'IN', 60, 'Pembelian connector RJ45', '2026-10-01 08:00:00'),
(9, 2, 'OUT', 10, 'Setup jaringan gedung B', '2026-10-02 10:00:00'),
(7, 1, 'IN', 15, 'Pembelian stapler', '2026-10-03 09:00:00'),
(7, 2, 'OUT', 3, 'Distribusi ke divisi HRD', '2026-10-04 11:00:00');
