<?php
$pageTitle = 'Farmers'; $active = 'farmers';
require __DIR__ . '/../includes/header.php';
$q = trim($_GET['q'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$per = 10;
$where = ''; $params = [];
if ($q !== '') { $where = 'WHERE full_name LIKE ? OR farmer_id LIKE ?'; $params = ["%$q%", "%$q%"]; }
$st = db()->prepare("SELECT COUNT(*) FROM farmers $where"); $st->execute($params);
$total = (int)$st->fetchColumn();
$pages = max(1, (int)ceil($total / $per));
$page = min($page, $pages);
$st = db()->prepare("SELECT * FROM farmers $where ORDER BY full_name LIMIT $per OFFSET " . (($page - 1) * $per));
$st->execute($params);
$rows = $st->fetchAll();
?>
<div class="page-head row">
    <div><h1>Farmers</h1><p class="muted"><?= $total ?> record<?= $total === 1 ? '' : 's' ?><?= $q !== '' ? ' matching "' . e($q) . '"' : '' ?></p></div>
    <a class="btn btn-primary" href="<?= url('farmers/form.php') ?>"><i class="fa-solid fa-plus"></i> Add farmer</a>
</div>
<section class="panel">
    <form method="get" class="search">
        <input type="search" name="q" value="<?= e($q) ?>" placeholder="Search by name or farmer ID">
        <button class="btn btn-primary" type="submit"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
        <?php if ($q !== ''): ?><a class="btn btn-light" href="index.php">Clear</a><?php endif; ?>
    </form>
    <?php if (!$rows): ?>
        <p class="empty"><?= $q !== '' ? 'No farmers match your search. Try a different name or ID.' : 'No farmers registered yet.' ?></p>
    <?php else: ?>
    <div class="table-wrap"><table>
        <thead><tr><th>Farmer ID</th><th>Full name</th><th>Address</th><th>Contact</th><th>Gender</th><th>Registered</th><th class="right">Actions</th></tr></thead>
        <tbody><?php foreach ($rows as $r): ?>
            <tr>
                <td><?= e($r['farmer_id']) ?></td><td><?= e($r['full_name']) ?></td><td><?= e($r['address']) ?></td>
                <td><?= e($r['contact_number']) ?></td><td><?= e($r['gender']) ?></td>
                <td><?= e(date('M d, Y', strtotime($r['date_registered']))) ?></td>
                <td class="right actions">
                    <a class="icon-btn" title="View" href="view.php?id=<?= $r['id'] ?>"><i class="fa-solid fa-eye"></i></a>
                    <a class="icon-btn" title="Edit" href="form.php?id=<?= $r['id'] ?>"><i class="fa-solid fa-pen"></i></a>
<?php if (can('delete')): ?>
                    <button class="icon-btn danger" title="Delete" type="button" data-delete="<?= $r['id'] ?>" data-name="<?= e($r['full_name']) ?>"><i class="fa-solid fa-trash"></i></button>
<?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?></tbody>
    </table></div>
    <?php if ($pages > 1): ?><div class="pager">
        <?php for ($i = 1; $i <= $pages; $i++): ?>
            <a class="<?= $i === $page ? 'cur' : '' ?>" href="?q=<?= urlencode($q) ?>&page=<?= $i ?>"><?= $i ?></a>
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
