<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();
$id = (int)($_GET['id'] ?? 0);
$st = db()->prepare('SELECT a.*, fr.full_name, fr.farmer_id AS fno, f.farm_name, c.crop_type, c.season FROM applications a JOIN farmers fr ON fr.id = a.farmer_id JOIN farms f ON f.id = a.farm_id JOIN crops c ON c.id = a.crop_id WHERE a.id = ?');
$st->execute([$id]); $a = $st->fetch();
if (!$a) { flash('danger', 'Application not found.'); redirect('applications/index.php'); }
$uid = $_SESSION['user']['id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check(); $act = $_POST['action'] ?? ''; $rem = mb_substr(trim($_POST['remarks'] ?? ''), 0, 255);
    if ($act === 'add_doc') {
        require_perm('manage_records');
        $t = trim($_POST['doc_type'] ?? ''); $d = trim($_POST['submitted_date'] ?? '');
        if ($t === '' || mb_strlen($t) > 100 || !valid_date($d) || $d > date('Y-m-d')) flash('danger', 'Enter a document type and a valid submission date.');
        else { db()->prepare('INSERT INTO application_documents (application_id, doc_type, submitted_date, remarks, uploaded_by) VALUES (?,?,?,?,?)')->execute([$id, $t, $d, $rem ?: null, $uid]); log_activity('Recorded document', 'application', $id, $t); flash('success', 'Document recorded.'); }
    } elseif ($act === 'doc_status') {
        require_perm('update_status');
        $s = $_POST['doc_status'] ?? '';
        if (in_array($s, ['Submitted', 'Verified', 'Rejected'], true)) {
            db()->prepare('UPDATE application_documents SET status = ?, remarks = ? WHERE id = ? AND application_id = ?')->execute([$s, $rem ?: null, (int)($_POST['doc_id'] ?? 0), $id]);
            log_activity('Document ' . strtolower($s), 'application', $id); flash('success', 'Document status updated.');
        }
    } elseif ($act === 'set_status') {
        require_perm('update_status');
        $s = $_POST['status'] ?? '';
        if (!in_array($s, APP_STATUSES, true) || $s === $a['status']) flash('danger', 'Choose a different status.');
        elseif ($s === 'Returned' && $rem === '') flash('danger', 'Add a remark explaining why the application is returned.');
        else {
            $pdo = db(); $pdo->beginTransaction();
            $pdo->prepare('UPDATE applications SET status = ? WHERE id = ?')->execute([$s, $id]);
            $pdo->prepare('INSERT INTO verification_records (application_id, old_status, new_status, remarks, changed_by) VALUES (?,?,?,?,?)')->execute([$id, $a['status'], $s, $rem ?: null, $uid]);
            $pdo->commit(); log_activity('Status changed', 'application', $id, $a['status'] . ' to ' . $s); flash('success', 'Status updated to ' . $s . '.');
        }
    }
    redirect('applications/view.php?id=' . $id);
}
$docs = db()->prepare('SELECT d.*, u.full_name AS uploader FROM application_documents d LEFT JOIN users u ON u.id = d.uploaded_by WHERE application_id = ? ORDER BY d.id'); $docs->execute([$id]); $docs = $docs->fetchAll();
$hist = db()->prepare('SELECT v.*, u.full_name AS who FROM verification_records v LEFT JOIN users u ON u.id = v.changed_by WHERE application_id = ? ORDER BY v.id DESC'); $hist->execute([$id]); $hist = $hist->fetchAll();
$have = array_column(array_filter($docs, fn($d) => $d['status'] !== 'Rejected'), 'doc_type');
$missing = array_values(array_diff(REQUIRED_DOCS, $have));
$pageTitle = $a['app_no']; $active = 'insurance';
require __DIR__ . '/../includes/header.php';
?>
<div class="page-head row"><div><h1><?= e($a['app_no']) ?> <?= badge($a['status']) ?></h1><p class="muted"><?= e($a['full_name']) ?> (<?= e($a['fno']) ?>) &middot; <?= e($a['farm_name']) ?> &middot; <?= e($a['crop_type']) ?>, <?= e($a['season']) ?> season &middot; applied <?= e($a['date_applied']) ?></p></div>
<a class="btn btn-light" href="index.php">Back to list</a></div>

<section class="panel"><h2>Requirements checklist</h2>
<p class="muted">This tracks documents received. It does not replace the office's physical-document procedure. The required list is configurable in <code>includes/functions.php</code> and should be confirmed with the MAO.</p>
<?php if ($missing): ?><div class="alert alert-danger"><strong>Missing:</strong> <?= e(implode(', ', $missing)) ?></div><?php else: ?><div class="alert alert-success">All listed requirements have been recorded.</div><?php endif; ?>
<?php if ($docs): ?><div class="table-wrap"><table><thead><tr><th>Document</th><th>Submitted</th><th>Recorded by</th><th>Status</th><th>Remarks</th><th>Update</th></tr></thead><tbody>
<?php foreach ($docs as $d): ?><tr><td><?= e($d['doc_type']) ?></td><td><?= e($d['submitted_date']) ?></td><td><?= e($d['uploader'] ?? '-') ?></td><td><?= badge($d['status']) ?></td><td><?= e($d['remarks'] ?? '') ?></td>
<td><form method="post" class="inline"><?= csrf_field() ?><input type="hidden" name="action" value="doc_status"><input type="hidden" name="doc_id" value="<?= $d['id'] ?>">
<select name="doc_status" aria-label="Document status"><?php foreach (['Submitted','Verified','Rejected'] as $x): ?><option <?= $d['status'] === $x ? 'selected' : '' ?>><?= $x ?></option><?php endforeach; ?></select>
<input name="remarks" placeholder="Remarks" value="<?= e($d['remarks'] ?? '') ?>" maxlength="255"><button class="btn btn-light btn-sm">Save</button></form></td></tr>
<?php endforeach; ?></tbody></table></div><?php endif; ?>
<form method="post" class="inline top-gap"><?= csrf_field() ?><input type="hidden" name="action" value="add_doc">
<input name="doc_type" list="reqdocs" placeholder="Document type" required maxlength="100"><datalist id="reqdocs"><?php foreach (REQUIRED_DOCS as $x): ?><option value="<?= e($x) ?>"><?php endforeach; ?></datalist>
<input type="date" name="submitted_date" value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>" required><input name="remarks" placeholder="Remarks (optional)" maxlength="255">
<button class="btn btn-primary btn-sm"><i class="fa-solid fa-plus"></i> Record document</button></form></section>

<section class="panel"><h2>Update status</h2>
<form method="post" class="inline top-gap"><?= csrf_field() ?><input type="hidden" name="action" value="set_status">
<select name="status" aria-label="New status"><?php foreach (APP_STATUSES as $x): ?><option <?= $a['status'] === $x ? 'selected' : '' ?>><?= $x ?></option><?php endforeach; ?></select>
<input name="remarks" placeholder="Verification remarks (required when returning)" maxlength="255" style="min-width:280px"><button class="btn btn-primary btn-sm">Update status</button></form></section>

<section class="panel"><h2>Status history</h2><div class="table-wrap"><table><thead><tr><th>When</th><th>Change</th><th>By</th><th>Remarks</th></tr></thead><tbody>
<?php foreach ($hist as $h): ?><tr><td><?= e($h['changed_at']) ?></td><td><?= $h['old_status'] ? e($h['old_status']) . ' &rarr; ' : '' ?><?= badge($h['new_status']) ?></td><td><?= e($h['who'] ?? '-') ?></td><td><?= e($h['remarks'] ?? '') ?></td></tr><?php endforeach; ?></tbody></table></div></section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
