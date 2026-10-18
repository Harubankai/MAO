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
  ['coming-soon.php?m=User+Management','User Management','fa-user-gear','user','manage_users'],
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
    <link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
</head>
<body>
<aside class="sidebar" id="sidebar">
    <div class="brand"><i class="fa-solid fa-wheat-awn"></i><div><strong>Moncada Agri</strong><small>Farmer Records System</small></div></div>
    <nav>
    <?php foreach ($nav as [$href, $label, $icon, $key, $perm]): if ($perm && !can($perm)) continue; ?>
        <a href="<?= url($href) ?>" class="<?= ($active ?? '') === $key ? 'active' : '' ?>">
            <i class="fa-solid <?= $icon ?>"></i><span><?= e($label) ?></span>
            <?php if ($key === 'user'): ?><em class="soon">Soon</em><?php endif; ?>
        </a>
    <?php endforeach; ?>
    </nav>
</aside>
<div class="main">
    <header class="topbar">
        <button class="menu-btn" onclick="document.getElementById('sidebar').classList.toggle('open')" aria-label="Toggle menu"><i class="fa-solid fa-bars"></i></button>
        <div class="topbar-title">Municipal Agriculture Office &middot; Moncada, Tarlac</div>
        <div class="user-box">
            <span><i class="fa-solid fa-circle-user"></i> <?= e($user['full_name'] ?? '') ?> <small class="muted">(<?= e($user['role'] ?? '') ?>)</small></span>
            <a href="<?= url('logout.php') ?>" class="btn btn-light btn-sm"><i class="fa-solid fa-right-from-bracket"></i> Log out</a>
        </div>
    </header>
    <main class="content">
    <?php if ($flash): ?><div class="alert alert-<?= e($flash[0]) ?>"><?= e($flash[1]) ?></div><?php endif; ?>
