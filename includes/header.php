<?php
require_once __DIR__ . '/functions.php';
require_login();
$user = current_user();
$nav = [
  ['index.php','Dashboard','fa-gauge-high','dashboard',''],
  ['farmers/index.php','Farmers','fa-users','farmers',''],
  ['farms/index.php','Farms','fa-tractor','farms',''],
  ['crops/index.php','Crops','fa-seedling','crops',''],
  ['applications/index.php','Insurance Applications','fa-shield-halved','insurance',''],
  ['reports.php','Reports','fa-chart-column','reports',''],
  ['logs.php','Activity Logs','fa-clipboard-list','logs','view_logs'],
  ['backup.php','Backup','fa-database','backup','backup'],
  ['users/index.php','User Management','fa-user-gear','user','manage_users'],
];
$flash = take_flash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle ?? 'Dashboard') ?> | Moncada Agriculture Office</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= url('assets/css/style.css?v=' . filemtime(__DIR__ . '/../assets/css/style.css')) ?>">
</head>
<body>
<aside class="sidebar" id="sidebar">
    <div class="brand"><img src="<?= url('assets/images/logo.jpg') ?>" alt="Moncada Agri Logo" class="brand-logo" width="38" height="38" style="width:38px;height:38px;border-radius:50%;object-fit:cover;background:#fff;border:1.5px solid rgba(255,255,255,.3);flex-shrink:0;"><div><strong>Moncada Agri</strong><small>Farmer Records System</small></div></div>
    <nav>
    <?php foreach ($nav as [$href, $label, $icon, $key, $perm]): if ($perm && !can($perm)) continue; ?>
        <a href="<?= url($href) ?>" class="<?= ($active ?? '') === $key ? 'active' : '' ?>">
            <i class="fa-solid <?= $icon ?>"></i><span><?= e($label) ?></span>
        </a>
    <?php endforeach; ?>
    </nav>
    <div class="sidebar-footer">
        <a href="<?= url('logout.php') ?>" class="sidebar-logout">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span>Log out</span>
        </a>
    </div>
</aside>
<div class="sidebar-backdrop" id="sidebarBackdrop" hidden></div>
<div class="main">
    <header class="topbar">
        <button type="button" class="menu-btn" id="menuBtn" aria-label="Toggle menu"><i class="fa-solid fa-bars"></i></button>
        <div class="topbar-title">Municipal Agriculture Office &middot; Moncada, Tarlac</div>
        <div class="user-box">
            <span><i class="fa-solid fa-circle-user"></i> <?= e($user['full_name'] ?? '') ?> <small class="muted">(<?= e($user['role'] ?? '') ?>)</small></span>
        </div>
    </header>
    <main class="content">
    <?php if ($flash): ?><div class="alert alert-<?= e($flash[0]) ?>"><?= e($flash[1]) ?></div><?php endif; ?>
