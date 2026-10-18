<?php
$pageTitle = 'Farms'; $active = 'farms';
require __DIR__ . '/../includes/header.php';
$q = trim($_GET['q'] ?? ''); $w = ''; $p = [];
if ($q !== '') { $w = 'WHERE fr.full_name LIKE ? OR fr.farmer_id LIKE ? OR f.barangay LIKE ? OR f.farm_name LIKE ?'; $p = array_fill(0, 4, "%$q%"); }
$st = db()->prepare("SELECT f.*, fr.full_name, fr.farmer_id AS fno FROM farms f JOIN farmers fr ON fr.id = f.farmer_id $w ORDER BY f.id DESC LIMIT 200");
$st->execute($p); $rows = $st->fetchAll();
?>
<div class="page-head row"><div><h1>Farms</h1><p class="muted"><?= count($rows) ?> shown (max 200)</p></div>
<a class="btn btn-primary" href="form.php"><i class="fa-solid fa-plus"></i> Add farm</a></div>
<section class="panel">
<form method="get" class="search"><input type="search" name="q" value="<?= e($q) ?>" placeholder="Search farmer, ID, barangay, or farm name">
<button class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i> Search</button><?php if ($q !== ''): ?><a class="btn btn-light" href="index.php">Clear</a><?php endif; ?></form>
<?php if (!$rows): ?><p class="empty">No farms found. Add a farm to link it to a registered farmer.</p><?php else: ?>
<div class="table-wrap"><table><thead><tr><th>Farmer</th><th>Farm</th><th>Barangay</th><th>Area (ha)</th><th>Ownership</th><th class="right">Actions</th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td><?= e($r['full_name']) ?> <small class="muted"><?= e($r['fno']) ?></small></td><td><?= e($r['farm_name']) ?></td><td><?= e($r['barangay']) ?></td><td><?= e($r['area_ha']) ?></td><td><?= e($r['ownership']) ?></td>
<td class="right actions"><a class="icon-btn" title="Edit" href="form.php?id=<?= $r['id'] ?>"><i class="fa-solid fa-pen"></i></a>
<?php if (can('delete')): ?><form method="post" action="delete.php" data-confirm="Delete this farm record? This cannot be undone."><?= csrf_field() ?><input type="hidden" name="id" value="<?= $r['id'] ?>"><button class="icon-btn danger" title="Delete"><i class="fa-solid fa-trash"></i></button></form><?php endif; ?></td></tr>
<?php endforeach; ?></tbody></table></div><?php endif; ?></section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
