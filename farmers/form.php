<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();
require_perm('encode_farmers'); // staff encode; admin only reviews
$id = (int)($_GET['id'] ?? 0);
$fields = ['full_name','birthday','contact_number','id_number','gender'];
$f = array_fill_keys($fields, '') + ['status' => 'pending', 'rejection_reason' => null];
if ($id) {
    $st = db()->prepare('SELECT * FROM farmers WHERE id = ?'); $st->execute([$id]);
    $f = $st->fetch();
    if (!$f) { flash('danger', 'Farmer not found.'); redirect('farmers/index.php'); }
    foreach ($fields as $k) $f[$k] = (string)($f[$k] ?? '');
}
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    foreach ($fields as $k) $f[$k] = trim($_POST[$k] ?? '');
    $f['full_name'] = preg_replace('/\s+/', ' ', $f['full_name']);

    if ($f['full_name'] === '' || mb_strlen($f['full_name']) > 150) $errors['full_name'] = 'Enter the farmer\'s full name (up to 150 characters).';
    if (!valid_date($f['birthday']) || $f['birthday'] > date('Y-m-d') || $f['birthday'] < date('Y-m-d', strtotime('-120 years'))) $errors['birthday'] = 'Enter a valid birthday that is not in the future.';
    if (!preg_match('/^09\d{9}$/', $f['contact_number'])) $errors['contact_number'] = 'Enter a valid 11-digit mobile number starting with 09 (e.g., 09171234567).';
    // The RSBSA number doubles as the Farmer ID, so it is limited to the farmer_id column length (30).
    if (!preg_match('/^[A-Za-z0-9][A-Za-z0-9 \/-]{2,29}$/', $f['id_number'])) $errors['id_number'] = 'Enter a valid RSBSA ID number (3-30 letters, numbers, spaces, hyphens or slashes).';
    if (!in_array($f['gender'], ['Male','Female','Other'], true)) $errors['gender'] = 'Select a gender.';
    if (!isset($errors['id_number'])) {
        $st = db()->prepare('SELECT id FROM farmers WHERE (id_number = ? OR farmer_id = ?) AND id <> ?'); $st->execute([$f['id_number'], $f['id_number'], $id]);
        if ($st->fetch()) $errors['id_number'] = 'This ID number is already registered to another farmer.';
    }

    if (!$errors) {
        $vals = array_map(fn($k) => $f[$k], $fields);
        $pdo = db(); $pdo->beginTransaction();
        if ($id) {
            // Any edit to a reviewed record sends it back to the Admin; pending records stay pending.
            $resubmit = $f['status'] !== 'pending';
            $pdo->prepare('UPDATE farmers SET full_name=?, birthday=?, contact_number=?, id_number=?, gender=?, farmer_id=?, address=NULL,
                status=\'pending\', rejection_reason=NULL' . ($resubmit ? ', submitted_at=NOW()' : '') . ' WHERE id=?')->execute([...$vals, $f['id_number'], $id]);
            if ($resubmit) log_farmer_approval($id, 'Resubmitted');
            $pdo->commit();
            log_activity($resubmit ? 'Resubmitted farmer' : 'Updated farmer', 'farmer', $id);
            flash('success', $resubmit ? 'Farmer record updated and resubmitted for admin approval.' : 'Farmer record updated. It is still pending admin approval.');
        } else {
            $farmerId = $f['id_number'];
            $pdo->prepare('INSERT INTO farmers (farmer_id, full_name, birthday, contact_number, id_number, gender,
                date_registered, status, encoded_by, submitted_at) VALUES (?,?,?,?,?,?,CURDATE(),\'pending\',?,NOW())')->execute([$farmerId, ...$vals, current_user()['id']]);
            $id = (int)$pdo->lastInsertId();
            log_farmer_approval($id, 'Submitted');
            $pdo->commit();
            log_activity('Submitted farmer', 'farmer', $id, $farmerId);
            flash('success', 'Farmer registered and submitted for admin approval.');
        }
        redirect('farmers/view.php?id=' . $id);
    }
}
$editing = (bool)$id;
$pageTitle = $editing ? 'Edit farmer' : 'Register farmer'; $active = 'farmers';
require __DIR__ . '/../includes/header.php';
?>
<div class="page-head"><h1><?= e($pageTitle) ?></h1>
<?php if ($editing): ?><p class="muted"><?= e($f['farmer_id']) ?> &middot; Current status: <?= farmer_badge($f['status']) ?></p><?php endif; ?></div>

<?php if ($editing && $f['status'] === 'rejected'): ?>
<div class="alert alert-danger"><strong><i class="fa-solid fa-circle-xmark"></i> Rejected by Admin:</strong> <?= e($f['rejection_reason'] ?? 'No reason given.') ?><br>Correct the information below and save to resubmit.</div>
<?php elseif ($editing && $f['status'] === 'active'): ?>
<div class="alert alert-warn"><i class="fa-solid fa-triangle-exclamation"></i> This record is already approved. Saving changes will send it back to the Admin for re-approval.</div>
<?php endif; ?>
<?php if ($errors): ?><div class="alert alert-danger">Please fix the <?= count($errors) ?> highlighted field<?= count($errors) > 1 ? 's' : '' ?> below.</div><?php endif; ?>

<section class="panel">
<form method="post" novalidate class="form-grid">
    <?= csrf_field() ?>
    <h2 class="full form-section">Personal information</h2>
    <div class="full"><label for="full_name">Full name <span class="req">*</span></label>
        <input id="full_name" name="full_name" value="<?= e($f['full_name']) ?>" maxlength="150" placeholder="First name, middle name, last name" required><?= field_err($errors,'full_name') ?></div>
    <div><label for="birthday">Birthday <span class="req">*</span></label>
        <input type="date" id="birthday" name="birthday" value="<?= e($f['birthday']) ?>" max="<?= date('Y-m-d') ?>" required><?= field_err($errors,'birthday') ?></div>
    <div><label for="gender">Gender <span class="req">*</span></label>
        <select id="gender" name="gender" required><option value="">Select</option>
        <?php foreach (['Male','Female','Other'] as $g): ?><option <?= $f['gender'] === $g ? 'selected' : '' ?>><?= $g ?></option><?php endforeach; ?></select><?= field_err($errors,'gender') ?></div>
    <div><label for="contact_number">Contact number <span class="req">*</span></label>
        <input type="tel" id="contact_number" name="contact_number" value="<?= e($f['contact_number']) ?>" placeholder="09171234567" maxlength="11" pattern="09[0-9]{9}" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11)" required><?= field_err($errors,'contact_number') ?></div>
    <div><label for="id_number">RSBSA ID number <span class="req">*</span></label>
        <input id="id_number" name="id_number" value="<?= e($f['id_number']) ?>" maxlength="30" placeholder="Used as the Farmer ID" required><?= field_err($errors,'id_number') ?></div>

    <div class="full alert alert-info top-gap"><i class="fa-solid fa-circle-info"></i>
        Saving submits this record to the Admin. Its status will be <strong>Pending Approval</strong> until the Admin approves or rejects it.</div>
    <div class="full form-actions">
        <button class="btn btn-primary" type="submit"><i class="fa-solid fa-paper-plane"></i> <?= $editing && $f['status'] !== 'pending' ? 'Save and resubmit' : ($editing ? 'Save changes' : 'Submit for approval') ?></button>
        <a class="btn btn-light" href="<?= url($editing ? 'farmers/view.php?id=' . $id : 'farmers/index.php') ?>">Cancel</a>
    </div>
</form>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
