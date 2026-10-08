<?php
// Flash message alerts
$types = ['success', 'error', 'warning'];
foreach ($types as $type):
    $msg = getFlash($type);
    if ($msg):
        $bsClass = $type === 'error' ? 'danger' : $type;
?>
<div class="px-4 pt-3">
    <div class="alert alert-<?= $bsClass ?> alert-dismissible fade show" role="alert">
        <?= e($msg) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>
<?php
    endif;
endforeach;
?>
