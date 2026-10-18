<?php
$pageTitle = 'Activity Logs'; $active = 'logs';
require __DIR__ . '/includes/header.php';
require_perm('view_logs');
$q = trim($_GET['q'] ?? ''); $w = ''; $p = [];
if ($q !== '') { $w = 'WHERE l.action LIKE ? OR u.username LIKE ? OR l.entity LIKE ?'; $p = array_fill(0, 3, "%$q%"); }
$st = db()->prepare("SELECT l.*, u.username FROM activity_logs l LEFT JOIN users u ON u.id = l.user_id $w ORDER BY l.id DESC LIMIT 300"); $st->execute($p); $rows = $st->fetchAll();
?>
<div class="page-head"><h1>Activity logs</h1><p class="muted">Read-only. Latest 300 entries. Logs record IDs and reference numbers, not personal details.</p></div>
<section class="panel"><form method="get" class="search"><input type="search" name="q" value="<?= e($q) ?>" placeholder="Search action, user, or record type"><button class="btn btn-primary">Search</button></form>
<?php if (!$rows): ?><p class="empty">No log entries.</p><?php else: ?><div class="table-wrap"><table><thead><tr><th>When</th><th>User</th><th>Action</th><th>Record</th><th>Details</th><th>IP</th></tr></thead><tbody>
<?php foreach ($rows as $l): ?><tr><td><?= e($l['created_at']) ?></td><td><?= e($l['username'] ?? '-') ?></td><td><?= e($l['action']) ?></td><td><?= e(trim(($l['entity'] ?? '') . ' ' . ($l['entity_id'] ?? ''))) ?></td><td><?= e($l['details'] ?? '') ?></td><td><?= e($l['ip_address'] ?? '') ?></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
