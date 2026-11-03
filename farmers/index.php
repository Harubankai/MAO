<?php
$pageTitle = 'Farmers'; $active = 'farmers';
require __DIR__ . '/../includes/header.php';
$q = trim($_GET['q'] ?? '');
$statusFilter = $_GET['status_filter'] ?? '';
if (!isset(FARMER_STATUSES[$statusFilter])) $statusFilter = '';
$page = max(1, (int)($_GET['page'] ?? 1));
$per = 10;
$conditions = [];
$params = [];
$farm = farmer_farm_sql('f');
if ($q !== '') { $conditions[] = "(f.full_name LIKE ? OR f.farmer_id LIKE ? OR f.id_number LIKE ? OR {$farm['barangay']} LIKE ?)"; $params = ["%$q%", "%$q%", "%$q%", "%$q%"]; }
if ($statusFilter !== '') { $conditions[] = 'f.status = ?'; $params[] = $statusFilter; }
$where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
$st = db()->prepare("SELECT COUNT(*) FROM farmers f $where"); $st->execute($params);
$total = (int)$st->fetchColumn();
$pages = max(1, (int)ceil($total / $per));
$page = min($page, $pages);
// Pending first so the review queue is always on top.
$st = db()->prepare("SELECT f.*, u.full_name AS encoder, {$farm['barangay']} AS farm_barangay, {$farm['crop']} AS farm_crop, {$farm['area']} AS farm_area FROM farmers f LEFT JOIN users u ON u.id = f.encoded_by $where
    ORDER BY FIELD(f.status, 'pending', 'rejected', 'active'), f.full_name LIMIT $per OFFSET " . (($page - 1) * $per));
$st->execute($params);
$rows = $st->fetchAll();
$counts = db()->query('SELECT status, COUNT(*) FROM farmers GROUP BY status')->fetchAll(PDO::FETCH_KEY_PAIR);
$tab = fn($s) => '?' . http_build_query(['q' => $q, 'status_filter' => $s]);
?>
<div class="page-head row">
    <div><h1>Farmers</h1><p class="muted"><?= $total ?> record<?= $total === 1 ? '' : 's' ?><?= $q !== '' ? ' matching "' . e($q) . '"' : '' ?></p></div>
    <?php if (can('encode_farmers')): ?><a class="btn btn-primary" href="<?= url('farmers/form.php') ?>"><i class="fa-solid fa-plus"></i> Register farmer</a><?php endif; ?>
</div>

<?php if (can('review_farmers') && ($counts['pending'] ?? 0) > 0 && $statusFilter !== 'pending'): ?>
<div class="alert alert-warn"><i class="fa-solid fa-clock"></i> <strong><?= $counts['pending'] ?> farmer<?= $counts['pending'] > 1 ? 's' : '' ?> awaiting your approval.</strong> <a href="<?= e($tab('pending')) ?>">View pending</a></div>
<?php elseif (can('encode_farmers') && ($counts['rejected'] ?? 0) > 0 && $statusFilter !== 'rejected'): ?>
<div class="alert alert-danger"><i class="fa-solid fa-rotate-left"></i> <strong><?= $counts['rejected'] ?> rejected record<?= $counts['rejected'] > 1 ? 's' : '' ?> need correction.</strong> <a href="<?= e($tab('rejected')) ?>">View rejected</a></div>
<?php endif; ?>

<section class="panel">
    <nav class="status-tabs" aria-label="Filter by approval status">
        <a href="<?= e($tab('')) ?>" class="<?= $statusFilter === '' ? 'cur' : '' ?>">All<b><?= array_sum($counts) ?></b></a>
        <?php foreach (FARMER_STATUSES as $k => $label): ?><a href="<?= e($tab($k)) ?>" class="<?= $statusFilter === $k ? 'cur' : '' ?>"><?= e($label) ?><b><?= $counts[$k] ?? 0 ?></b></a><?php endforeach; ?>
    </nav>
    <form method="get" class="search">
        <input type="hidden" name="status_filter" value="<?= e($statusFilter) ?>">
        <input type="search" name="q" value="<?= e($q) ?>" placeholder="Search by name, farmer ID, ID number or barangay">
        <button class="btn btn-primary" type="submit"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
        <?php if ($q !== '' || $statusFilter !== ''): ?><a class="btn btn-light" href="index.php">Clear</a><?php endif; ?>
    </form>
    <?php if (!$rows): ?>
        <p class="empty"><?= $q !== '' || $statusFilter !== '' ? 'No farmers match this filter.' : 'No farmers registered yet.' ?></p>
    <?php else: ?>
    <div class="table-wrap"><table>
        <thead><tr><th>Farmer ID</th><th>Full name</th><th>Barangay</th><th>Crop</th><th>Area (ha)</th><th>Encoded by</th><th>Status</th><th class="right">Actions</th></tr></thead>
        <tbody><?php foreach ($rows as $r): ?>
            <tr>
                <td><?= e($r['farmer_id']) ?></td><td><?= e($r['full_name']) ?></td>
                <td><?= e($r['farm_barangay'] ?? '—') ?></td><td><?= e($r['farm_crop'] ?? '—') ?></td>
                <td><?= $r['farm_area'] !== null ? e($r['farm_area']) : '—' ?></td>
                <td><?= e($r['encoder'] ?? '—') ?></td>
                <td><?= farmer_badge($r['status']) ?><?php if ($r['status'] === 'rejected' && $r['rejection_reason']): ?><small class="reject-note"><?= e($r['rejection_reason']) ?></small><?php endif; ?></td>
                <td class="right actions">
                    <a class="icon-btn" title="View" href="view.php?id=<?= $r['id'] ?>"><i class="fa-solid fa-eye"></i></a>
<?php if (can('encode_farmers')): ?>
                    <a class="icon-btn" title="<?= $r['status'] === 'rejected' ? 'Correct and resubmit' : 'Edit' ?>" href="form.php?id=<?= $r['id'] ?>"><i class="fa-solid fa-pen"></i></a>
<?php endif; ?>
<?php if (can('review_farmers') && $r['status'] === 'pending'): ?>
                    <a class="icon-btn" title="Review" href="view.php?id=<?= $r['id'] ?>#review" style="color:var(--amber);border-color:var(--amber);"><i class="fa-solid fa-clipboard-check"></i></a>
<?php endif; ?>
<?php if (can('delete')): ?>
                    <button class="icon-btn danger" title="Delete" type="button" data-delete="<?= $r['id'] ?>" data-name="<?= e($r['full_name']) ?>"><i class="fa-solid fa-trash"></i></button>
<?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?></tbody>
    </table></div>
    <?php if ($pages > 1): ?><div class="pager">
        <?php for ($i = 1; $i <= $pages; $i++): ?>
            <a class="<?= $i === $page ? 'cur' : '' ?>" href="?q=<?= urlencode($q) ?>&status_filter=<?= urlencode($statusFilter) ?>&page=<?= $i ?>"><?= $i ?></a>
        <?php endfor; ?></div><?php endif; ?>
    <?php endif; ?>
</section>

<div class="modal" id="deleteModal" hidden>
    <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="dm-title">
        <h3 id="dm-title">Delete this farmer?</h3>
        <p>You are about to delete <strong id="dm-name"></strong>. This cannot be undone.</p>
        <form method="post" action="delete.php">
            <?= csrf_field() ?><input type="hidden" name="id" id="dm-id">
            <div class="modal-actions">
                <button type="button" class="btn btn-light" id="dm-cancel">Cancel</button>
                <button type="submit" class="btn btn-danger">Delete farmer</button>
            </div>
        </form>
    </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
