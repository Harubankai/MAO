<?php
$names = ['Farms','Crops','Insurance Applications','Reports','User Management','Settings'];
$m = in_array($_GET['m'] ?? '', $names, true) ? $_GET['m'] : 'This module';
$pageTitle = $m; $active = strtolower(strtok($m, ' '));
require __DIR__ . '/includes/header.php';
?>
<div class="page-head"><h1><?= e($m) ?></h1></div>
<section class="panel soon-panel">
    <i class="fa-solid fa-person-digging"></i>
    <h2>Coming Soon &mdash; Under Development</h2>
    <p class="muted">This module is planned for a later development phase and is not available yet.</p>
    <a class="btn btn-primary" href="<?= url('index.php') ?>">Back to dashboard</a>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
