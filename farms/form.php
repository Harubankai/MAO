<?php
require_once __DIR__ . '/../includes/functions.php';
require_login(); require_perm('manage_records');
$id = (int)($_GET['id'] ?? 0);
$f = ['farmer_id' => (int)($_GET['farmer'] ?? 0), 'farm_name' => '', 'barangay' => '', 'location' => '', 'area_ha' => '', 'ownership' => ''];
if ($id) {
    $st = db()->prepare('SELECT * FROM farms WHERE id = ?'); $st->execute([$id]); $f = $st->fetch();
    if (!$f) { flash('danger', 'Farm not found.'); redirect('farms/index.php'); }
}
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    foreach (['farm_name','barangay','location','area_ha','ownership'] as $k) $f[$k] = trim($_POST[$k] ?? '');
    $f['farmer_id'] = (int)($_POST['farmer_id'] ?? 0);
    $st = db()->prepare('SELECT COUNT(*) FROM farmers WHERE id = ?'); $st->execute([$f['farmer_id']]);
    if (!$st->fetchColumn()) $errors['farmer_id'] = 'Select a registered farmer.';
    if ($f['farm_name'] === '' || mb_strlen($f['farm_name']) > 120) $errors['farm_name'] = 'Enter a farm name (up to 120 characters).';
    if ($f['barangay'] === '' || mb_strlen($f['barangay']) > 80) $errors['barangay'] = 'Enter the barangay.';
    if (mb_strlen($f['location']) > 255) $errors['location'] = 'Location is too long.';
    if (!is_numeric($f['area_ha']) || $f['area_ha'] <= 0 || $f['area_ha'] > 99999) $errors['area_ha'] = 'Enter an area in hectares greater than 0.';
    if (!in_array($f['ownership'], ['Owner','Tenant','Lessee','Caretaker'], true)) $errors['ownership'] = 'Select the ownership type.';
    if (!$errors) {
        $v = [$f['farmer_id'], $f['farm_name'], $f['barangay'], $f['location'] ?: null, $f['area_ha'], $f['ownership']];
        if ($id) { db()->prepare('UPDATE farms SET farmer_id=?, farm_name=?, barangay=?, location=?, area_ha=?, ownership=? WHERE id=?')->execute([...$v, $id]); log_activity('Updated farm', 'farm', $id); }
        else { db()->prepare('INSERT INTO farms (farmer_id, farm_name, barangay, location, area_ha, ownership) VALUES (?,?,?,?,?,?)')->execute($v); log_activity('Created farm', 'farm', (int)db()->lastInsertId()); }
        flash('success', $id ? 'Farm updated.' : 'Farm added.'); redirect('farms/index.php');
    }
}
$farmers = db()->query('SELECT id, farmer_id, full_name FROM farmers ORDER BY full_name')->fetchAll();
$pageTitle = $id ? 'Edit farm' : 'Add farm'; $active = 'farms';
require __DIR__ . '/../includes/header.php';
?>
<div class="page-head"><h1><?= e($pageTitle) ?></h1></div>
<section class="panel"><form method="post" novalidate class="form-grid"><?= csrf_field() ?>
<div class="full"><label for="farmer_id">Farmer</label><select id="farmer_id" name="farmer_id" required><option value="">Select a farmer</option>
<?php foreach ($farmers as $r): ?><option value="<?= $r['id'] ?>" <?= (int)$f['farmer_id'] === (int)$r['id'] ? 'selected' : '' ?>><?= e($r['full_name']) ?> (<?= e($r['farmer_id']) ?>)</option><?php endforeach; ?></select><?= field_err($errors, 'farmer_id') ?></div>
<div><label for="farm_name">Farm name</label><input id="farm_name" name="farm_name" value="<?= e($f['farm_name']) ?>" maxlength="120" required><?= field_err($errors, 'farm_name') ?></div>
<div><label for="barangay">Barangay</label><input id="barangay" name="barangay" value="<?= e($f['barangay']) ?>" maxlength="80" required><?= field_err($errors, 'barangay') ?></div>
<div class="full"><label for="location">Location details</label><input id="location" name="location" value="<?= e($f['location']) ?>" maxlength="255" placeholder="Landmark or sitio (optional)"><?= field_err($errors, 'location') ?></div>
<div><label for="area_ha">Area (hectares)</label><input id="area_ha" name="area_ha" type="number" step="0.01" min="0.01" value="<?= e($f['area_ha']) ?>" required><?= field_err($errors, 'area_ha') ?></div>
<div><label for="ownership">Ownership</label><select id="ownership" name="ownership" required><option value="">Select</option>
<?php foreach (['Owner','Tenant','Lessee','Caretaker'] as $o): ?><option <?= $f['ownership'] === $o ? 'selected' : '' ?>><?= $o ?></option><?php endforeach; ?></select><?= field_err($errors, 'ownership') ?></div>
<div class="full form-actions"><button class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> <?= $id ? 'Save changes' : 'Add farm' ?></button><a class="btn btn-light" href="index.php">Cancel</a></div>
</form></section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
