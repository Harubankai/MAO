<?php
require_once __DIR__ . '/../includes/functions.php';
require_login(); require_perm('manage_records');
$id = (int)($_GET['id'] ?? 0);
$f = ['farm_id' => 0, 'crop_type' => '', 'variety' => '', 'season' => '', 'planting_date' => date('Y-m-d'), 'expected_harvest' => '', 'area_planted_ha' => '', 'status' => 'Planted'];
if ($id) {
    $st = db()->prepare('SELECT * FROM crops WHERE id = ?'); $st->execute([$id]); $f = $st->fetch();
    if (!$f) { flash('danger', 'Crop record not found.'); redirect('crops/index.php'); }
}
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    foreach (['crop_type','variety','season','planting_date','expected_harvest','area_planted_ha','status'] as $k) $f[$k] = trim($_POST[$k] ?? '');
    $f['farm_id'] = (int)($_POST['farm_id'] ?? 0);
    $st = db()->prepare('SELECT COUNT(*) FROM farms WHERE id = ?'); $st->execute([$f['farm_id']]);
    if (!$st->fetchColumn()) $errors['farm_id'] = 'Select a farm.';
    if ($f['crop_type'] === '' || mb_strlen($f['crop_type']) > 80) $errors['crop_type'] = 'Enter the crop type.';
    if (mb_strlen($f['variety']) > 80) $errors['variety'] = 'Variety is too long.';
    if (!in_array($f['season'], ['Wet','Dry'], true)) $errors['season'] = 'Select a season.';
    if (!valid_date($f['planting_date'])) $errors['planting_date'] = 'Enter a valid planting date.';
    if ($f['expected_harvest'] !== '' && (!valid_date($f['expected_harvest']) || $f['expected_harvest'] < $f['planting_date'])) $errors['expected_harvest'] = 'Enter a valid date on or after planting.';
    if (!is_numeric($f['area_planted_ha']) || $f['area_planted_ha'] <= 0 || $f['area_planted_ha'] > 99999) $errors['area_planted_ha'] = 'Enter an area greater than 0.';
    if (!in_array($f['status'], ['Planted','Harvested','Failed'], true)) $errors['status'] = 'Select a status.';
    if (!$errors) {
        $v = [$f['farm_id'], $f['crop_type'], $f['variety'] ?: null, $f['season'], $f['planting_date'], $f['expected_harvest'] ?: null, $f['area_planted_ha'], $f['status']];
        if ($id) { db()->prepare('UPDATE crops SET farm_id=?, crop_type=?, variety=?, season=?, planting_date=?, expected_harvest=?, area_planted_ha=?, status=? WHERE id=?')->execute([...$v, $id]); log_activity('Updated crop', 'crop', $id); }
        else { db()->prepare('INSERT INTO crops (farm_id, crop_type, variety, season, planting_date, expected_harvest, area_planted_ha, status) VALUES (?,?,?,?,?,?,?,?)')->execute($v); log_activity('Created crop', 'crop', (int)db()->lastInsertId()); }
        flash('success', $id ? 'Crop record updated.' : 'Crop record added.'); redirect('crops/index.php');
    }
}
$farms = db()->query('SELECT f.id, f.farm_name, fr.full_name FROM farms f JOIN farmers fr ON fr.id = f.farmer_id ORDER BY fr.full_name, f.farm_name')->fetchAll();
$pageTitle = $id ? 'Edit crop record' : 'Add crop record'; $active = 'crops';
require __DIR__ . '/../includes/header.php';
?>
<div class="page-head"><h1><?= e($pageTitle) ?></h1></div>
<?php if (!$farms): ?><div class="alert alert-danger">Add a farm first. Every crop record must be linked to a farm.</div><?php endif; ?>
<section class="panel"><form method="post" novalidate class="form-grid"><?= csrf_field() ?>
<div class="full"><label for="farm_id">Farm</label><select id="farm_id" name="farm_id" required><option value="">Select a farm</option>
<?php foreach ($farms as $r): ?><option value="<?= $r['id'] ?>" <?= (int)$f['farm_id'] === (int)$r['id'] ? 'selected' : '' ?>><?= e($r['full_name']) ?> - <?= e($r['farm_name']) ?></option><?php endforeach; ?></select><?= field_err($errors, 'farm_id') ?></div>
<div><label for="crop_type">Crop type</label><input id="crop_type" name="crop_type" list="crops" value="<?= e($f['crop_type']) ?>" maxlength="80" required><datalist id="crops"><option>Rice</option><option>Corn</option><option>Vegetables</option><option>Sugarcane</option></datalist><?= field_err($errors, 'crop_type') ?></div>
<div><label for="variety">Variety</label><input id="variety" name="variety" value="<?= e($f['variety']) ?>" maxlength="80"><?= field_err($errors, 'variety') ?></div>
<div><label for="season">Season</label><select id="season" name="season" required><option value="">Select</option><?php foreach (['Wet','Dry'] as $o): ?><option <?= $f['season'] === $o ? 'selected' : '' ?>><?= $o ?></option><?php endforeach; ?></select><?= field_err($errors, 'season') ?></div>
<div><label for="status">Crop status</label><select id="status" name="status"><?php foreach (['Planted','Harvested','Failed'] as $o): ?><option <?= $f['status'] === $o ? 'selected' : '' ?>><?= $o ?></option><?php endforeach; ?></select><?= field_err($errors, 'status') ?></div>
<div><label for="planting_date">Planting date</label><input type="date" id="planting_date" name="planting_date" value="<?= e($f['planting_date']) ?>" required><?= field_err($errors, 'planting_date') ?></div>
<div><label for="expected_harvest">Expected harvest</label><input type="date" id="expected_harvest" name="expected_harvest" value="<?= e($f['expected_harvest']) ?>"><?= field_err($errors, 'expected_harvest') ?></div>
<div><label for="area_planted_ha">Area planted (hectares)</label><input type="number" step="0.01" min="0.01" id="area_planted_ha" name="area_planted_ha" value="<?= e($f['area_planted_ha']) ?>" required><?= field_err($errors, 'area_planted_ha') ?></div>
<div class="full form-actions"><button class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> <?= $id ? 'Save changes' : 'Add crop record' ?></button><a class="btn btn-light" href="index.php">Cancel</a></div>
</form></section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
