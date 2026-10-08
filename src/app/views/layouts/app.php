<?php
// Main layout: sidebar + content area
// Required variables: $pageTitle, $content (via output buffering in controllers)
$user = currentUser();
$userInitial = strtoupper(substr($user['full_name'] ?? $user['username'] ?? 'U', 0, 1));
?>
<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Dashboard') ?> - Inventory System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/assets/css/app.css">
    <script src="/assets/js/app.js"></script>
</head>
<body>
    <div class="app-wrapper">
        <!-- Sidebar Overlay (mobile) -->
        <div class="sidebar-overlay" id="sidebar-overlay"></div>

        <!-- Sidebar -->
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Bar -->
            <div class="topbar">
                <div class="topbar-left">
                    <button class="btn-sidebar-toggle" id="sidebar-toggle" type="button" aria-label="Toggle menu">
                        <i class="bi bi-list"></i>
                    </button>
                    <div class="breadcrumb-nav">
                        <a href="/">Dashboard</a>
                        <?php if (!empty($breadcrumb)): ?>
                            <?php foreach ($breadcrumb as $item): ?>
                                <span class="separator">/</span>
                                <?php if (!empty($item['url'])): ?>
                                    <a href="<?= e($item['url']) ?>"><?= e($item['label']) ?></a>
                                <?php else: ?>
                                    <span><?= e($item['label']) ?></span>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="topbar-right">
                    <button class="theme-toggle" id="theme-toggle" type="button" aria-label="Toggle theme">
                        <i class="bi bi-moon"></i>
                    </button>
                    <a href="/logout" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </div>
            </div>

            <!-- Flash Messages -->
            <?php include __DIR__ . '/../partials/alert.php'; ?>

            <!-- Page Content -->
            <div class="page-content">
                <?= $content ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
