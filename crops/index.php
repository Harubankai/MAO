<?php
$pageTitle = 'Crops'; $active = 'crops';
require __DIR__ . '/../includes/header.php';
$q = trim($_GET['q'] ?? ''); $w = ''; $p = [];
if ($q !== '') { $w = 'WHERE c.crop_type LIKE ? OR fr.full_name LIKE ? OR fr.farmer_id LIKE ? OR f.farm_name LIKE ?'; $p = array_fill(0, 4, "%$q%"); }
$page = max(1, (int)($_GET['page'] ?? 1));
$per = 10;
$st = db()->prepare("SELECT COUNT(*) FROM crops c JOIN farms f ON f.id = c.farm_id JOIN farmers fr ON fr.id = f.farmer_id $w");
$st->execute($p);
$total = (int)$st->fetchColumn();
$pages = max(1, (int)ceil($total / $per));
$page = min($page, $pages);
$offset = ($page - 1) * $per;

$st = db()->prepare("SELECT c.*, f.farm_name, f.barangay, f.location, fr.full_name, fr.farmer_id AS fno FROM crops c JOIN farms f ON f.id = c.farm_id JOIN farmers fr ON fr.id = f.farmer_id $w ORDER BY c.planting_date DESC LIMIT $per OFFSET $offset");
$st->execute($p);
$rows = $st->fetchAll();
?>
<div class="page-head row"><div><h1>Crops</h1><p class="muted"><?= $total ?> record<?= $total === 1 ? '' : 's' ?><?= $q !== '' ? ' matching "' . e($q) . '"' : '' ?></p></div>
<a class="btn btn-primary" href="form.php"><i class="fa-solid fa-plus"></i> Add crop record</a></div>
<section class="panel">
<form method="get" class="search"><input type="search" name="q" value="<?= e($q) ?>" placeholder="Search crop, farmer, ID, or farm">
<button class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i> Search</button><?php if ($q !== ''): ?><a class="btn btn-light" href="index.php">Clear</a><?php endif; ?></form>
<?php if (!$rows): ?><p class="empty">No crop records found.</p><?php else: ?>
<div class="table-wrap"><table><thead><tr><th>Farmer</th><th>Farm & Location</th><th>Crop</th><th>Season</th><th>Planted</th><th>Area (ha)</th><th>Status</th><th class="right">Actions</th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td><?= e($r['full_name']) ?> <small class="muted"><?= e($r['fno']) ?></small></td><td><strong><?= e($r['farm_name']) ?></strong><?php if (!empty($r['location']) || !empty($r['barangay'])): ?><br><small class="muted"><?= e($r['barangay']) ?><?= !empty($r['location']) ? ' &middot; ' . e($r['location']) : '' ?></small><?php endif; ?></td><td><?= e($r['crop_type']) ?><?= $r['variety'] ? ' <small class="muted">' . e($r['variety']) . '</small>' : '' ?></td><td><?= e($r['season']) ?></td><td><?= e($r['planting_date']) ?></td><td><?= e($r['area_planted_ha']) ?></td><td><?= badge($r['status']) ?></td>
<td class="right actions"><a class="icon-btn" title="Edit" href="form.php?id=<?= $r['id'] ?>"><i class="fa-solid fa-pen"></i></a>
<?php if (can('delete')): ?><form method="post" action="delete.php" data-confirm="Delete this crop record? This cannot be undone."><?= csrf_field() ?><input type="hidden" name="id" value="<?= $r['id'] ?>"><button class="icon-btn danger" title="Delete"><i class="fa-solid fa-trash"></i></button></form><?php endif; ?></td></tr>
<?php endforeach; ?></tbody></table></div>
<?php if ($pages > 1): ?>
<div class="pager">
    <?php for ($i = 1; $i <= $pages; $i++): ?>
        <a class="<?= $i === $page ? 'cur' : '' ?>" href="?q=<?= urlencode($q) ?>&page=<?= $i ?>"><?= $i ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>
<?php endif; ?></section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
