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
