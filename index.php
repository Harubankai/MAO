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
?>
<div class="page-head"><h1>Dashboard</h1><p class="muted">Welcome, <?= e(current_user()['full_name']) ?>.</p></div>
<div class="stats"><?php foreach ($cards as [$l, $v, $i]): ?><div class="stat"><i class="fa-solid <?= $i ?>"></i><div><div class="stat-n"><?= $v ?></div><div class="stat-l"><?= e($l) ?></div></div></div><?php endforeach; ?></div>
<div class="two-col">
<section class="panel"><h2>Applications by status</h2><?php bars($stat); ?></section>
<section class="panel"><h2>Crop records by type</h2><?php bars($crops); ?></section></div>
<section class="panel"><h2><?= can('view_logs') ? 'Recent system activity' : 'Your recent activity' ?></h2>
<?php if (!$act): ?><p class="empty">No activity recorded yet.</p><?php else: ?><div class="table-wrap"><table><thead><tr><th>When</th><th>User</th><th>Action</th><th>Record</th></tr></thead><tbody>
<?php foreach ($act as $l): ?><tr><td><?= e($l['created_at']) ?></td><td><?= e($l['username'] ?? '-') ?></td><td><?= e($l['action']) ?></td><td><?= e(trim(($l['entity'] ?? '') . ' ' . ($l['entity_id'] ?? ''))) ?></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
