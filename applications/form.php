<?php
require_once __DIR__ . '/../includes/functions.php';
require_login(); require_perm('manage_records');
$f = ['farmer_id' => 0, 'farm_id' => 0, 'crop_id' => 0, 'date_applied' => date('Y-m-d'), 'remarks' => ''];
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    foreach (['farmer_id','farm_id','crop_id'] as $k) $f[$k] = (int)($_POST[$k] ?? 0);
    $f['date_applied'] = trim($_POST['date_applied'] ?? ''); $f['remarks'] = trim($_POST['remarks'] ?? '');
    $st = db()->prepare('SELECT COUNT(*) FROM farms WHERE id = ? AND farmer_id = ?'); $st->execute([$f['farm_id'], $f['farmer_id']]);
    if (!$st->fetchColumn()) $errors['farm_id'] = 'Select a farm that belongs to this farmer.';
    $st = db()->prepare('SELECT COUNT(*) FROM crops WHERE id = ? AND farm_id = ?'); $st->execute([$f['crop_id'], $f['farm_id']]);
    if (!$st->fetchColumn()) $errors['crop_id'] = 'Select a crop record from this farm.';
    if (!valid_date($f['date_applied']) || $f['date_applied'] > date('Y-m-d')) $errors['date_applied'] = 'Enter a valid date that is not in the future.';
    if (mb_strlen($f['remarks']) > 500) $errors['remarks'] = 'Remarks are too long.';
    if (!$errors) {
        $pdo = db(); $pdo->beginTransaction();
        $last = $pdo->query("SELECT app_no FROM applications WHERE app_no LIKE 'APP-" . date('Y') . "-%' ORDER BY app_no DESC LIMIT 1 FOR UPDATE")->fetchColumn();
        $no = 'APP-' . date('Y') . '-' . str_pad((string)($last ? (int)substr($last, -4) + 1 : 1), 4, '0', STR_PAD_LEFT);
        $pdo->prepare('INSERT INTO applications (app_no, farmer_id, farm_id, crop_id, date_applied, remarks, created_by) VALUES (?,?,?,?,?,?,?)')
            ->execute([$no, $f['farmer_id'], $f['farm_id'], $f['crop_id'], $f['date_applied'], $f['remarks'] ?: null, $_SESSION['user']['id']]);
        $id = (int)$pdo->lastInsertId();
        $pdo->prepare('INSERT INTO verification_records (application_id, old_status, new_status, remarks, changed_by) VALUES (?,NULL,?,?,?)')->execute([$id, 'Submitted', 'Application recorded', $_SESSION['user']['id']]);
        $pdo->commit();
        log_activity('Created application', 'application', $id, $no);
        flash('success', "Application $no recorded."); redirect('applications/view.php?id=' . $id);
    }
}
$farmers = db()->query('SELECT id, farmer_id, full_name FROM farmers ORDER BY full_name')->fetchAll();
$farms = db()->query('SELECT id, farmer_id, farm_name FROM farms')->fetchAll();
$crops = db()->query('SELECT id, farm_id, crop_type, season, planting_date FROM crops')->fetchAll();
$pageTitle = 'New application'; $active = 'insurance';
require __DIR__ . '/../includes/header.php';
?>
<div class="page-head"><h1>New insurance application</h1></div>
<section class="panel"><form method="post" novalidate class="form-grid"><?= csrf_field() ?>
<div class="full"><label for="farmer_id">Farmer</label><select id="farmer_id" name="farmer_id" required><option value="">Select a farmer</option>
<?php foreach ($farmers as $r): ?><option value="<?= $r['id'] ?>" <?= $f['farmer_id'] === (int)$r['id'] ? 'selected' : '' ?>><?= e($r['full_name']) ?> (<?= e($r['farmer_id']) ?>)</option><?php endforeach; ?></select></div>
<div><label for="farm_id">Farm</label><select id="farm_id" name="farm_id" required></select><?= field_err($errors, 'farm_id') ?></div>
<div><label for="crop_id">Crop record</label><select id="crop_id" name="crop_id" required></select><?= field_err($errors, 'crop_id') ?></div>
<div><label for="date_applied">Date applied</label><input type="date" id="date_applied" name="date_applied" value="<?= e($f['date_applied']) ?>" max="<?= date('Y-m-d') ?>" required><?= field_err($errors, 'date_applied') ?></div>
<div class="full"><label for="remarks">Remarks (optional)</label><input id="remarks" name="remarks" value="<?= e($f['remarks']) ?>" maxlength="500"><?= field_err($errors, 'remarks') ?></div>
<div class="full form-actions"><button class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Record application</button><a class="btn btn-light" href="index.php">Cancel</a></div>
</form></section>
<script>
const FARMS = <?= json_encode($farms) ?>, CROPS = <?= json_encode($crops) ?>, SEL = {farm: <?= (int)$f['farm_id'] ?>, crop: <?= (int)$f['crop_id'] ?>};
const fe = document.getElementById('farmer_id'), fa = document.getElementById('farm_id'), cr = document.getElementById('crop_id');
function fill(sel, rows, label, keep, empty) {
  sel.innerHTML = '<option value="">' + empty + '</option>';
  rows.forEach(r => { const o = document.createElement('option'); o.value = r.id; o.textContent = label(r); if (+r.id === keep) o.selected = true; sel.appendChild(o); });
}
function farms() { fill(fa, FARMS.filter(x => x.farmer_id == fe.value), r => r.farm_name, SEL.farm, fe.value ? 'Select a farm' : 'Select a farmer first'); crops(); }
function crops() { fill(cr, CROPS.filter(x => x.farm_id == fa.value), r => r.crop_type + ' (' + r.season + ', planted ' + r.planting_date + ')', SEL.crop, fa.value ? 'Select a crop record' : 'Select a farm first'); }
fe.addEventListener('change', () => { SEL.farm = 0; SEL.crop = 0; farms(); }); fa.addEventListener('change', () => { SEL.crop = 0; crops(); }); farms();
</script>
<?php require __DIR__ . '/../includes/footer.php'; ?>
