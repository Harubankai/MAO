<?php
$pageTitle = 'Farmer details'; $active = 'farmers';
require __DIR__ . '/../includes/header.php';
$st = db()->prepare('SELECT * FROM farmers WHERE id = ?'); $st->execute([(int)($_GET['id'] ?? 0)]);
$f = $st->fetch();
if (!$f) { flash('danger', 'Farmer not found.'); redirect('farmers/index.php'); }
$rows = ['Farmer ID' => $f['farmer_id'], 'Full name' => $f['full_name'], 'Address' => $f['address'], 'Contact number' => $f['contact_number'],
         'Gender' => $f['gender'], 'Date registered' => date('F d, Y', strtotime($f['date_registered'])), 'Last updated' => date('M d, Y g:i A', strtotime($f['updated_at']))];
?>
<div class="page-head row"><h1><?= e($f['full_name']) ?></h1>
    <div><a class="btn btn-light" href="index.php">Back to list</a>
    <a class="btn btn-primary" href="form.php?id=<?= $f['id'] ?>"><i class="fa-solid fa-pen"></i> Edit</a></div></div>
<section class="panel"><dl class="details">
<?php foreach ($rows as $k => $v): ?><dt><?= e($k) ?></dt><dd><?= e($v) ?></dd><?php endforeach; ?>
</dl></section>
<?php
$fs = db()->prepare('SELECT * FROM farms WHERE farmer_id = ? ORDER BY id DESC'); $fs->execute([$f['id']]); $farms = $fs->fetchAll();
$as = db()->prepare('SELECT a.*, c.crop_type FROM applications a JOIN crops c ON c.id = a.crop_id WHERE a.farmer_id = ? ORDER BY a.id DESC'); $as->execute([$f['id']]); $apps = $as->fetchAll();
?>
<section class="panel"><div class="panel-head"><h2>Farms</h2><a class="btn btn-light btn-sm" href="<?= url('farms/form.php?farmer=' . $f['id']) ?>"><i class="fa-solid fa-plus"></i> Add farm</a></div>
<?php if (!$farms): ?><p class="empty">No farms recorded for this farmer.</p><?php else: ?><div class="table-wrap"><table>
<thead><tr><th>Farm</th><th>Barangay</th><th>Area (ha)</th><th>Ownership</th></tr></thead>
<tbody><?php foreach ($farms as $r): ?><tr><td><a href="<?= url('farms/form.php?id=' . $r['id']) ?>"><?= e($r['farm_name']) ?></a></td><td><?= e($r['barangay']) ?></td><td><?= e($r['area_ha']) ?></td><td><?= e($r['ownership']) ?></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?></section>
<section class="panel"><div class="panel-head"><h2>Insurance application history</h2></div>
<?php if (!$apps): ?><p class="empty">No applications yet.</p><?php else: ?><div class="table-wrap"><table>
<thead><tr><th>Application no.</th><th>Crop</th><th>Date applied</th><th>Status</th></tr></thead>
<tbody><?php foreach ($apps as $r): ?><tr><td><a href="<?= url('applications/view.php?id=' . $r['id']) ?>"><?= e($r['app_no']) ?></a></td><td><?= e($r['crop_type']) ?></td><td><?= e($r['date_applied']) ?></td><td><?= badge($r['status']) ?></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?></section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
