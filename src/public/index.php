<?php
// Front controller and router

require_once __DIR__ . '/../app/helpers/functions.php';
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/config/auth.php';

startSession();

$url = trim($_GET['url'] ?? '', '/');
$segments = explode('/', $url);
$method = $_SERVER['REQUEST_METHOD'];

// Route definitions: [pattern => [controller, action, auth_required, admin_only]]
$routes = [
    // Auth
    'GET:login'             => ['AuthController', 'loginForm', false, false],
    'POST:login'            => ['AuthController', 'login', false, false],
    'GET:logout'            => ['AuthController', 'logout', true, false],

    // Dashboard
    'GET:'                  => ['DashboardController', 'index', true, false],
    'GET:dashboard'         => ['DashboardController', 'index', true, false],

    // Categories
    'GET:categories'            => ['CategoryController', 'index', true, true],
    'GET:categories/create'     => ['CategoryController', 'create', true, true],
    'POST:categories/store'     => ['CategoryController', 'store', true, true],
    'GET:categories/edit'       => ['CategoryController', 'edit', true, true],
    'POST:categories/update'    => ['CategoryController', 'update', true, true],
    'POST:categories/delete'    => ['CategoryController', 'delete', true, true],

    // Products
    'GET:products'              => ['ProductController', 'index', true, false],
    'GET:products/create'       => ['ProductController', 'create', true, true],
    'POST:products/store'       => ['ProductController', 'store', true, true],
    'GET:products/show'         => ['ProductController', 'show', true, false],
    'GET:products/edit'         => ['ProductController', 'edit', true, true],
    'POST:products/update'      => ['ProductController', 'update', true, true],
    'POST:products/delete'      => ['ProductController', 'delete', true, true],

    // Suppliers
    'GET:suppliers'             => ['SupplierController', 'index', true, true],
    'GET:suppliers/create'      => ['SupplierController', 'create', true, true],
    'POST:suppliers/store'      => ['SupplierController', 'store', true, true],
    'GET:suppliers/edit'        => ['SupplierController', 'edit', true, true],
    'POST:suppliers/update'     => ['SupplierController', 'update', true, true],
    'POST:suppliers/delete'     => ['SupplierController', 'delete', true, true],

    // Transactions
    'GET:transactions'              => ['TransactionController', 'index', true, false],
    'GET:transactions/stock-in'     => ['TransactionController', 'stockInForm', true, false],
    'POST:transactions/stock-in'    => ['TransactionController', 'stockIn', true, false],
    'GET:transactions/stock-out'    => ['TransactionController', 'stockOutForm', true, false],
    'POST:transactions/stock-out'   => ['TransactionController', 'stockOut', true, false],

    // Users
    'GET:users'             => ['UserController', 'index', true, true],
    'GET:users/create'      => ['UserController', 'create', true, true],
    'POST:users/store'      => ['UserController', 'store', true, true],
    'GET:users/edit'        => ['UserController', 'edit', true, true],
    'POST:users/update'     => ['UserController', 'update', true, true],
    'POST:users/delete'     => ['UserController', 'delete', true, true],
];

// Extract route key: strip numeric ID segments for matching
$routeUrl = $url;
$id = null;

// Match patterns like categories/edit/5 -> categories/edit with id=5
if (count($segments) >= 3 && is_numeric(end($segments))) {
    $id = (int) array_pop($segments);
    $routeUrl = implode('/', $segments);
}

$routeKey = $method . ':' . $routeUrl;

if (isset($routes[$routeKey])) {
    [$controllerName, $action, $authRequired, $adminOnly] = $routes[$routeKey];

    if ($authRequired) {
        requireLogin();
    }

    if ($adminOnly) {
        requireAdmin();
    }

    $controllerFile = __DIR__ . '/../app/controllers/' . $controllerName . '.php';

    if (!file_exists($controllerFile)) {
        http_response_code(500);
        exit('Controller not found.');
    }

    require_once $controllerFile;
    $controller = new $controllerName();

    if ($id !== null) {
        $controller->$action($id);
    } else {
        $controller->$action();
    }
} else {
    http_response_code(404);
    $pageTitle = '404 - Halaman Tidak Ditemukan';
    include __DIR__ . '/../app/views/errors/404.php';
}
