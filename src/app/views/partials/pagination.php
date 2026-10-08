<?php
// Pagination partial
// Required: $pagination (from paginate() helper), current query params preserved
if ($pagination['total_pages'] > 1):
?>
<nav aria-label="Navigasi halaman">
    <ul class="pagination pagination-sm justify-content-center mb-0">
        <li class="page-item <?= $pagination['current_page'] <= 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="?<?= buildQueryString(['page' => $pagination['current_page'] - 1]) ?>">
                <i class="bi bi-chevron-left"></i>
            </a>
        </li>
        <?php
        $start = max(1, $pagination['current_page'] - 2);
        $end = min($pagination['total_pages'], $pagination['current_page'] + 2);
        if ($start > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?<?= buildQueryString(['page' => 1]) ?>">1</a>
            </li>
            <?php if ($start > 2): ?>
                <li class="page-item disabled"><span class="page-link">...</span></li>
            <?php endif; ?>
        <?php endif; ?>
        <?php for ($i = $start; $i <= $end; $i++): ?>
            <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                <a class="page-link" href="?<?= buildQueryString(['page' => $i]) ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
        <?php if ($end < $pagination['total_pages']): ?>
            <?php if ($end < $pagination['total_pages'] - 1): ?>
                <li class="page-item disabled"><span class="page-link">...</span></li>
            <?php endif; ?>
            <li class="page-item">
                <a class="page-link" href="?<?= buildQueryString(['page' => $pagination['total_pages']]) ?>"><?= $pagination['total_pages'] ?></a>
            </li>
        <?php endif; ?>
        <li class="page-item <?= $pagination['current_page'] >= $pagination['total_pages'] ? 'disabled' : '' ?>">
            <a class="page-link" href="?<?= buildQueryString(['page' => $pagination['current_page'] + 1]) ?>">
                <i class="bi bi-chevron-right"></i>
            </a>
        </li>
    </ul>
</nav>
<?php endif; ?>
