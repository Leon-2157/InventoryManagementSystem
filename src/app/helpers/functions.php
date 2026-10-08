<?php
// Global helper functions

// XSS-safe output encoding
function e(?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

// Redirect helper
function redirect(string $path): void {
    header('Location: ' . $path);
    exit;
}

// Flash message helpers
function setFlash(string $type, string $message): void {
    startSession();
    $_SESSION['flash'][$type] = $message;
}

function getFlash(string $type): ?string {
    startSession();
    $message = $_SESSION['flash'][$type] ?? null;
    unset($_SESSION['flash'][$type]);
    return $message;
}

function hasFlash(string $type): bool {
    startSession();
    return isset($_SESSION['flash'][$type]);
}

// Format currency IDR
function formatRupiah(float $amount): string {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

// Format date
function formatDate(string $date): string {
    return date('d M Y', strtotime($date));
}

function formatDateTime(string $date): string {
    return date('d M Y H:i', strtotime($date));
}

// Base URL helper
function url(string $path = ''): string {
    return '/' . ltrim($path, '/');
}

// Active menu helper
function isActive(string $path): string {
    $currentUrl = trim($_GET['url'] ?? '', '/');
    $checkPath = trim($path, '/');

    if ($checkPath === '' && $currentUrl === '') {
        return 'active';
    }

    if ($checkPath !== '' && str_starts_with($currentUrl, $checkPath)) {
        return 'active';
    }

    return '';
}

// Pagination helper
function paginate(int $totalItems, int $perPage = 10, int $currentPage = 1): array {
    $totalPages = max(1, (int) ceil($totalItems / $perPage));
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $perPage;

    return [
        'total_items'  => $totalItems,
        'per_page'     => $perPage,
        'current_page' => $currentPage,
        'total_pages'  => $totalPages,
        'offset'       => $offset,
    ];
}

// Build query string preserving existing params
function buildQueryString(array $overrides = []): string {
    $params = $_GET;
    unset($params['url']); // internal routing param
    $params = array_merge($params, $overrides);
    return http_build_query($params);
}

// Sorting allowlist validator
function validateSort(string $column, array $allowed, string $default = 'created_at'): string {
    return in_array($column, $allowed, true) ? $column : $default;
}

function validateDirection(string $dir): string {
    return strtoupper($dir) === 'ASC' ? 'ASC' : 'DESC';
}

// Input sanitization
function inputString(string $key, string $default = ''): string {
    return trim($_POST[$key] ?? $_GET[$key] ?? $default);
}

function inputInt(string $key, int $default = 0): int {
    $val = filter_input(INPUT_POST, $key, FILTER_VALIDATE_INT)
        ?? filter_input(INPUT_GET, $key, FILTER_VALIDATE_INT);
    return $val !== false && $val !== null ? $val : $default;
}

function inputFloat(string $key, float $default = 0.0): float {
    $val = filter_input(INPUT_POST, $key, FILTER_VALIDATE_FLOAT)
        ?? filter_input(INPUT_GET, $key, FILTER_VALIDATE_FLOAT);
    return $val !== false && $val !== null ? $val : $default;
}

// Old input (for repopulating forms after validation failure)
function old(string $key, string $default = ''): string {
    startSession();
    return e($_SESSION['old_input'][$key] ?? $default);
}

function setOldInput(array $data): void {
    startSession();
    $_SESSION['old_input'] = $data;
}

function clearOldInput(): void {
    startSession();
    unset($_SESSION['old_input']);
}
