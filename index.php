<?php
$pageTitle = 'Dashboard'; $active = 'dashboard';
require __DIR__ . '/includes/header.php';
$n = fn($sql) => (int)db()->query($sql)->fetchColumn();
$byStatus = db()->query('SELECT status, COUNT(*) c FROM applications GROUP BY status')->fetchAll(PDO::FETCH_KEY_PAIR);
$pending = ($byStatus['Submitted'] ?? 0) + ($byStatus['Under Review'] ?? 0);
$cards = [['Registered farmers', $n('SELECT COUNT(*) FROM farmers'), 'fa-users'], ['Farm records', $n('SELECT COUNT(*) FROM farms'), 'fa-tractor'], ['Crop records', $n('SELECT COUNT(*) FROM crops'), 'fa-seedling'],
 ['Insurance applications', $n('SELECT COUNT(*) FROM applications'), 'fa-shield-halved'], ['Pending verification', $pending, 'fa-hourglass-half'], ['Approved', $byStatus['Approved'] ?? 0, 'fa-circle-check'], ['Returned', $byStatus['Returned'] ?? 0, 'fa-rotate-left']];
$crops = db()->query('SELECT crop_type, COUNT(*) c FROM crops GROUP BY crop_type ORDER BY c DESC LIMIT 6')->fetchAll(PDO::FETCH_KEY_PAIR);
if (can('view_logs')) $act = db()->query('SELECT l.*, u.username FROM activity_logs l LEFT JOIN users u ON u.id = l.user_id ORDER BY l.id DESC LIMIT 8')->fetchAll();
else { $st = db()->prepare('SELECT l.*, u.username FROM activity_logs l LEFT JOIN users u ON u.id = l.user_id WHERE l.user_id = ? ORDER BY l.id DESC LIMIT 8'); $st->execute([current_user()['id']]); $act = $st->fetchAll(); }
function bars(array $d): void { $m = max(1, ...array_values($d) ?: [1]); foreach ($d as $k => $v): ?>
<div class="bar-row"><span><?= e($k) ?></span><div class="bar"><i style="width:<?= round($v / $m * 100) ?>%"></i></div><b><?= $v ?></b></div><?php endforeach; if (!$d): ?><p class="empty">No data yet.</p><?php endif; }
$stat = []; foreach (APP_STATUSES as $s) $stat[$s] = $byStatus[$s] ?? 0;
// Farmer approval workflow: Admin sees the review queue, Staff sees records to correct and what is waiting.
$fc = db()->query('SELECT status, COUNT(*) FROM farmers GROUP BY status')->fetchAll(PDO::FETCH_KEY_PAIR);
$queueStatus = can('review_farmers') ? 'pending' : 'rejected';
$queue = db()->prepare('SELECT f.id, f.farmer_id, f.full_name, ' . farmer_farm_sql('f')['barangay'] . ' AS farm_barangay, f.status, f.rejection_reason, f.submitted_at, f.reviewed_at, u.full_name AS encoder
    FROM farmers f LEFT JOIN users u ON u.id = f.encoded_by WHERE f.status IN (?, \'pending\') ORDER BY FIELD(f.status, ?) DESC, f.submitted_at LIMIT 8');
$queue->execute([$queueStatus, $queueStatus]); $queue = $queue->fetchAll();
?>
<div class="page-head"><h1>Dashboard</h1><p class="muted">Welcome, <?= e(current_user()['full_name']) ?>.</p></div>
<div class="stats">
    <a class="stat stat-warn" href="<?= url('farmers/index.php?status_filter=pending') ?>"><i class="fa-solid fa-hourglass-half"></i><div><div class="stat-n"><?= $fc['pending'] ?? 0 ?></div><div class="stat-l">Farmers pending approval</div></div></a>
    <a class="stat" href="<?= url('farmers/index.php?status_filter=active') ?>"><i class="fa-solid fa-user-check"></i><div><div class="stat-n"><?= $fc['active'] ?? 0 ?></div><div class="stat-l">Approved farmers</div></div></a>
    <a class="stat stat-bad" href="<?= url('farmers/index.php?status_filter=rejected') ?>"><i class="fa-solid fa-user-xmark"></i><div><div class="stat-n"><?= $fc['rejected'] ?? 0 ?></div><div class="stat-l">Rejected farmers</div></div></a>
</div>
<section class="panel"><div class="panel-head"><h2><?= can('review_farmers') ? 'Farmers awaiting your approval' : 'Farmer records: needs correction and pending' ?></h2>
<?php if (can('encode_farmers')): ?><a class="btn btn-primary btn-sm" href="<?= url('farmers/form.php') ?>"><i class="fa-solid fa-plus"></i> Register farmer</a><?php endif; ?></div>
<?php if (!$queue): ?><p class="empty"><?= can('review_farmers') ? 'No farmer records waiting for review.' : 'Nothing to correct and nothing pending.' ?></p><?php else: ?><div class="table-wrap"><table>
<thead><tr><th>Farmer</th><th>Barangay</th><th>Encoded by</th><th>Submitted</th><th>Status</th><th class="right">Action</th></tr></thead><tbody>
<?php foreach ($queue as $r): ?><tr><td><?= e($r['full_name']) ?> <small class="muted"><?= e($r['farmer_id']) ?></small></td><td><?= e($r['farm_barangay'] ?? '—') ?></td><td><?= e($r['encoder'] ?? '—') ?></td>
<td><?= $r['submitted_at'] ? e(date('M d, Y g:i A', strtotime($r['submitted_at']))) : '—' ?></td>
<td><?= farmer_badge($r['status']) ?><?php if ($r['status'] === 'rejected'): ?><small class="reject-note"><?= e($r['rejection_reason']) ?></small><?php endif; ?></td>
<td class="right"><?php if ($r['status'] === 'pending' && can('review_farmers')): ?><a class="btn btn-sm btn-primary" href="<?= url('farmers/view.php?id=' . $r['id'] . '#review') ?>">Review</a>
<?php elseif ($r['status'] === 'rejected' && can('encode_farmers')): ?><a class="btn btn-sm btn-primary" href="<?= url('farmers/form.php?id=' . $r['id']) ?>">Correct</a>
<?php else: ?><a class="btn btn-sm btn-light" href="<?= url('farmers/view.php?id=' . $r['id']) ?>">View</a><?php endif; ?></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?></section>
<div class="stats"><?php foreach ($cards as [$l, $v, $i]): ?><div class="stat"><i class="fa-solid <?= $i ?>"></i><div><div class="stat-n"><?= $v ?></div><div class="stat-l"><?= e($l) ?></div></div></div><?php endforeach; ?></div>
<div class="two-col">
<section class="panel"><h2>Applications by status</h2><?php bars($stat); ?></section>
<section class="panel"><h2>Crop records by type</h2><?php bars($crops); ?></section></div>
<section class="panel"><h2><?= can('view_logs') ? 'Recent system activity' : 'Your recent activity' ?></h2>
<?php if (!$act): ?><p class="empty">No activity recorded yet.</p><?php else: ?><div class="table-wrap"><table><thead><tr><th>When</th><th>User</th><th>Action</th><th>Record</th></tr></thead><tbody>
<?php foreach ($act as $l): ?><tr><td><?= e($l['created_at']) ?></td><td><?= e($l['username'] ?? '-') ?></td><td><?= e($l['action']) ?></td><td><?= e(trim(($l['entity'] ?? '') . ' ' . ($l['entity_id'] ?? ''))) ?></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
