# Inventory Management System

A native PHP MVC application for tracking products, categories, suppliers, and stock transactions.

## Tech Stack
* **Runtime**: PHP 8.2 (Apache)
* **Database**: MySQL 8.0
* **Frontend**: Vanilla HTML/CSS/JS + Bootstrap 5
* **Infrastructure**: Podman & Podman Compose

## Requirements
* Podman
* Podman Compose

## Local Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com/Leon-2157/InventoryManagementSystem.git
   cd tugas
   ```

2. **Start the containers**
   ```bash
   podman-compose up -d --build
   ```
   *Note: This command automatically initializes the database schema and seeds initial data.*

3. **Access the application**
   * Web UI: `http://localhost:8080`
   * Database: `localhost:3306`

4. **Stop the containers**
   ```bash
   podman-compose down
   ```
   *Note: Add `-v` flag if you want to wipe the database volume (`podman-compose down -v`).*

## Environment Configuration
The application relies on Podman Compose environment variables for database connections. The default local setup is pre-configured in `docker-compose.yml`:
* `DB_HOST`: db
* `DB_NAME`: inventory
* `DB_USER`: inventory_user
* `DB_PASS`: inventory_pass

## Project Structure
```text
.
├── database/          # SQL schema and seed files (auto-executed on DB init)
├── docker/            # Dockerfiles (PHP + Apache config)
├── src/               # Application source code
│   ├── app/           # Controllers, Models, and Views (MVC)
│   ├── core/          # Base MVC classes and Router
│   └── public/        # Web root (index.php, CSS, JS)
└── docker-compose.yml # Container orchestration
```

## Default Credentials
The `database/seed.sql` provides default accounts for testing:
* **Admin**: `admin` / `admin123`
* **Staff**: `staff` / `staff123`

## Aturan Bisnis (Business Rules)

Sistem inventaris ini menerapkan serangkaian aturan bisnis dan batasan integritas data pada lapisan aplikasi dan basis data:

### 1. Autentikasi & Kontrol Akses (RBAC)
* **Role Admin**: Memiliki hak akses penuh ke seluruh modul sistem (Manajemen Kategori, Produk, Supplier, Mutasi Stok, dan Manajemen User).
* **Role Staff**: Memiliki hak akses operasional terbatas untuk melihat Dashboard, melihat daftar dan detail Produk, mencatat Transaksi Barang Masuk (`IN`), mencatat Transaksi Barang Keluar (`OUT`), serta meninjau Riwayat Transaksi. Akses ke modul pengelolaan Kategori, Supplier, User, dan manipulasi Produk diblokir (HTTP 403).
* **Keamanan Transaksi**: Setiap permintaan mutasi data berbasis `POST` diwajibkan menyertakan validasi token CSRF (`csrf_token`) dan sesi login aktif.

### 2. Manajemen Pengguna & Integritas Akun
* **Identitas Unik**: Setiap akun wajib memiliki `username` unik di dalam sistem.
* **Profil Pengguna**: Setiap entitas `users` terhubung secara relasional 1:1 dengan `user_profiles` (`ON UPDATE CASCADE`, `ON DELETE CASCADE`).
* **Proteksi Akun Sendiri**: Pengguna dilarang mengubah perannya sendiri menjadi non-admin dan dilarang menghapus akun yang sedang digunakan saat aktif.
* **Preservasi Audit Trail**: Pengguna yang sudah pernah mencatat transaksi mutasi stok (`stock_movements`) tidak dapat dihapus dari sistem guna menjaga keabsahan histori logistik.

### 3. Katalog Produk & Kategori
* **Identifikasi Unik (SKU)**: Setiap produk wajib memiliki kode SKU unik yang tidak boleh terduplikasi.
* **Validasi Nilai Fisik**: Nilai harga (`price`), kuantitas stok (`stock`), dan batas stok minimum (`minimum_stock`) wajib bernilai non-negatif (`CHECK >= 0`).
* **Relasi Kategori**: Setiap produk wajib terasosiasi dengan satu kategori (`category_id NOT NULL`). Kategori yang masih menaungi produk aktif tidak dapat dihapus (`ON DELETE RESTRICT`).
* **Proteksi Riwayat Transaksi**: Produk yang telah memiliki histori transaksi mutasi stok tidak dapat dihapus dari katalog.

### 4. Manajemen Supplier & Multi-Sourcing
* **Relasi Many-to-Many**: Satu produk dapat dipasok oleh lebih dari satu supplier, dan satu supplier dapat memasok banyak produk melalui tabel perantara `product_suppliers`.
* **Pencatatan Harga Beli**: Setiap relasi produk-supplier dapat menyimpan harga beli spesifik (`supplier_price`).
* **Restriksi Penghapusan**: Supplier yang masih terikat dalam relasi katalog produk tidak dapat dihapus sebelum relasinya dilepaskan.

### 5. Mutasi & Kontrol Stok (Inventory Movements)
* **Kuantitas Positif**: Setiap pergerakan stok wajib bernilai kuantitas lebih dari nol (`quantity > 0`).
* **Barang Masuk (`IN`)**: Menambah jumlah stok fisik produk secara atomik dalam transaksi basis data (`ACID transaction`).
* **Barang Keluar (`OUT`)**:
  * Menggunakan *pessimistic locking* (`SELECT ... FOR UPDATE`) saat memeriksa stok untuk mencegah inkonsistensi data akibat *race condition*.
  * Kuantitas pengeluaran barang tidak boleh melebihi sisa stok fisik yang tersedia. Transaksi otomatis dibatalkan (*rollback*) jika stok tidak mencukupi.
* **Immutabilitas Transaksi**: Seluruh catatan pergerakan stok bersifat permanen dan tidak dapat diedit atau dihapus guna menjamin integritas pembukuan barang.
