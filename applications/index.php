<?php
$pageTitle = 'Insurance Applications'; $active = 'insurance';
require __DIR__ . '/../includes/header.php';
$q = trim($_GET['q'] ?? ''); $s = $_GET['status'] ?? ''; $from = $_GET['from'] ?? ''; $to = $_GET['to'] ?? '';
$w = ['1=1']; $p = [];
if ($q !== '') { $w[] = '(a.app_no LIKE ? OR fr.full_name LIKE ? OR fr.farmer_id LIKE ?)'; array_push($p, "%$q%", "%$q%", "%$q%"); }
if (in_array($s, APP_STATUSES, true)) { $w[] = 'a.status = ?'; $p[] = $s; } else $s = '';
if (valid_date($from)) { $w[] = 'a.date_applied >= ?'; $p[] = $from; } else $from = '';
if (valid_date($to)) { $w[] = 'a.date_applied <= ?'; $p[] = $to; } else $to = '';
$st = db()->prepare('SELECT a.*, fr.full_name, fr.farmer_id AS fno, c.crop_type FROM applications a JOIN farmers fr ON fr.id = a.farmer_id JOIN crops c ON c.id = a.crop_id WHERE ' . implode(' AND ', $w) . ' ORDER BY a.id DESC LIMIT 200');
$st->execute($p); $rows = $st->fetchAll();
?>
<div class="page-head row"><div><h1>Insurance applications</h1><p class="muted"><?= count($rows) ?> shown (max 200)</p></div>
<a class="btn btn-primary" href="form.php"><i class="fa-solid fa-plus"></i> New application</a></div>
<section class="panel">
<form method="get" class="search"><input type="search" name="q" value="<?= e($q) ?>" placeholder="Application no., farmer name, or farmer ID">
<select name="status" style="width:auto"><option value="">All statuses</option><?php foreach (APP_STATUSES as $x): ?><option <?= $s === $x ? 'selected' : '' ?>><?= $x ?></option><?php endforeach; ?></select>
<input type="date" name="from" value="<?= e($from) ?>" style="width:auto" aria-label="From date"><input type="date" name="to" value="<?= e($to) ?>" style="width:auto" aria-label="To date">
<button class="btn btn-primary"><i class="fa-solid fa-filter"></i> Filter</button><a class="btn btn-light" href="index.php">Clear</a></form>
<?php if (!$rows): ?><p class="empty">No applications match. Change the filters or create a new application.</p><?php else: ?>
<div class="table-wrap"><table><thead><tr><th>Application no.</th><th>Farmer</th><th>Crop</th><th>Date applied</th><th>Status</th><th class="right">Actions</th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td><?= e($r['app_no']) ?></td><td><?= e($r['full_name']) ?> <small class="muted"><?= e($r['fno']) ?></small></td><td><?= e($r['crop_type']) ?></td><td><?= e($r['date_applied']) ?></td><td><?= badge($r['status']) ?></td>
<td class="right actions"><a class="icon-btn" title="Open" href="view.php?id=<?= $r['id'] ?>"><i class="fa-solid fa-eye"></i></a></td></tr>
<?php endforeach; ?></tbody></table></div><?php endif; ?></section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
