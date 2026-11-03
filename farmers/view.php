<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();
$st = db()->prepare('SELECT f.*, eu.full_name AS encoder, ru.full_name AS reviewer FROM farmers f
    LEFT JOIN users eu ON eu.id = f.encoded_by LEFT JOIN users ru ON ru.id = f.reviewed_by WHERE f.id = ?');
$st->execute([(int)($_GET['id'] ?? 0)]);
$f = $st->fetch();
if (!$f) { flash('danger', 'Farmer not found.'); redirect('farmers/index.php'); }
$pageTitle = 'Farmer details'; $active = 'farmers';
require __DIR__ . '/../includes/header.php';
$dash = '—';
$ha = fn($v) => $v !== null ? number_format((float)$v, 2) . ' ha' : $dash;
$rows = ['Farmer ID' => $f['farmer_id'], 'Full name' => $f['full_name'],
         'Birthday' => $f['birthday'] ? date('F d, Y', strtotime($f['birthday'])) : $dash,
         'Contact number' => $f['contact_number'] ?? $dash, 'RSBSA ID number' => $f['id_number'] ?? $dash, 'Gender' => $f['gender'] ?? $dash];
if (!empty($f['address'])) $rows['Address (old record)'] = $f['address'];
// Farm details now live in the Farms section below; these only exist on old records.
foreach (['Total farm area' => $f['total_farm_area_ha'] !== null ? $ha($f['total_farm_area_ha']) : null, 'Insured area' => $f['insured_area_ha'] !== null ? $ha($f['insured_area_ha']) : null,
          'Crop type' => $f['crop_type'], 'Farm location' => $f['farm_location'], 'Barangay' => $f['barangay'], 'Farm ownership' => $f['farm_ownership']] as $k => $v)
    if (!empty($v)) $rows["$k (old record)"] = $v;
$rows += ['Encoded by' => $f['encoder'] ?? $dash, 'Date registered' => date('F d, Y', strtotime($f['date_registered'])),
          'Last updated' => date('M d, Y g:i A', strtotime($f['updated_at']))];
$hs = db()->prepare('SELECT a.*, u.full_name AS who, u.role FROM farmer_approvals a LEFT JOIN users u ON u.id = a.acted_by WHERE a.farmer_id = ? ORDER BY a.id DESC');
$hs->execute([$f['id']]); $hist = $hs->fetchAll();
?>
<div class="page-head row"><div><h1><?= e($f['full_name']) ?> <?= farmer_badge($f['status']) ?></h1><p class="muted"><?= e($f['farmer_id']) ?></p></div>
    <div><a class="btn btn-light" href="index.php">Back to list</a>
    <?php if (can('encode_farmers')): ?><a class="btn btn-primary" href="form.php?id=<?= $f['id'] ?>"><i class="fa-solid fa-pen"></i> <?= $f['status'] === 'rejected' ? 'Correct and resubmit' : 'Edit' ?></a><?php endif; ?></div></div>

<?php if ($f['status'] === 'rejected'): ?>
<div class="alert alert-danger"><strong><i class="fa-solid fa-circle-xmark"></i> Rejected</strong> by <?= e($f['reviewer'] ?? 'Admin') ?> on <?= e(date('M d, Y g:i A', strtotime($f['reviewed_at']))) ?>.<br><strong>Reason:</strong> <?= e($f['rejection_reason']) ?></div>
<?php elseif ($f['status'] === 'active' && $f['reviewed_at']): ?>
<div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> <strong>Approved</strong> by <?= e($f['reviewer'] ?? 'Admin') ?> on <?= e(date('M d, Y g:i A', strtotime($f['reviewed_at']))) ?>.</div>
<?php elseif ($f['status'] === 'pending'): ?>
<div class="alert alert-warn"><i class="fa-solid fa-hourglass-half"></i> <strong>Pending Approval</strong> &middot; submitted <?= $f['submitted_at'] ? e(date('M d, Y g:i A', strtotime($f['submitted_at']))) : '' ?>. <?= can('review_farmers') ? 'Review the information below, then approve or reject.' : 'Waiting for the Admin to review this record.' ?></div>
<?php endif; ?>

<section class="panel"><h2>Farmer information</h2><dl class="details">
<?php foreach ($rows as $k => $v): ?><dt><?= e($k) ?></dt><dd><?= e($v) ?></dd><?php endforeach; ?>
</dl></section>

<?php if (can('review_farmers') && $f['status'] === 'pending'): ?>
<section class="panel" id="review"><h2>Admin decision</h2>
<p class="muted">Review the information above, then approve or reject this record.</p>
<div class="modal-actions" style="justify-content:flex-start;">
    <button type="button" class="btn btn-primary" data-modal-open="approveModal"><i class="fa-solid fa-circle-check"></i> Approve</button>
    <button type="button" class="btn btn-danger" data-modal-open="rejectModal"><i class="fa-solid fa-circle-xmark"></i> Reject</button>
</div></section>

<div class="modal" id="approveModal" hidden>
    <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="am-title">
        <h3 id="am-title">Approve this farmer?</h3>
        <p>You are about to approve <strong><?= e($f['full_name']) ?></strong> (<?= e($f['farmer_id']) ?>). Confirm that all information is complete and correct.</p>
        <form method="post" action="approve.php"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $f['id'] ?>"><input type="hidden" name="decision" value="approve">
            <div class="modal-actions">
                <button type="button" class="btn btn-light" data-modal-close>Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-circle-check"></i> Approve</button>
            </div>
        </form>
    </div>
</div>

<div class="modal" id="rejectModal" hidden>
    <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="rm-title">
        <h3 id="rm-title">Reject this farmer?</h3>
        <p>Tell the Staff what to correct in <strong><?= e($f['full_name']) ?></strong>'s record.</p>
        <form method="post" action="approve.php"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $f['id'] ?>"><input type="hidden" name="decision" value="reject">
            <label for="reason">Reason for rejection <span class="req">*</span></label>
            <textarea id="reason" name="reason" maxlength="500" required placeholder="Tell the Staff what to correct"></textarea>
            <div class="modal-actions">
                <button type="button" class="btn btn-light" data-modal-close>Cancel</button>
                <button type="submit" class="btn btn-danger"><i class="fa-solid fa-circle-xmark"></i> Reject</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<section class="panel"><h2>Approval history</h2>
<?php if (!$hist): ?><p class="empty">No approval actions recorded<?= $f['status'] === 'active' ? ' (approved before the approval workflow was introduced)' : '' ?>.</p><?php else: ?><div class="table-wrap"><table>
<thead><tr><th>Date and time</th><th>Action</th><th>By</th><th>Remarks</th></tr></thead>
<tbody><?php foreach ($hist as $h): ?><tr><td><?= e(date('M d, Y g:i A', strtotime($h['acted_at']))) ?></td>
<td><?= badge($h['action'] === 'Resubmitted' ? 'Submitted' : $h['action']) ?><?= $h['action'] === 'Resubmitted' ? ' <small class="muted">(resubmitted)</small>' : '' ?></td>
<td><?= e($h['who'] ?? '-') ?> <small class="muted"><?= e($h['role'] ?? '') ?></small></td><td><?= e($h['remarks'] ?? '') ?></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?></section>
<?php
$fs = db()->prepare('SELECT * FROM farms WHERE farmer_id = ? ORDER BY id DESC'); $fs->execute([$f['id']]); $farms = $fs->fetchAll();
$as = db()->prepare('SELECT a.*, c.crop_type FROM applications a JOIN crops c ON c.id = a.crop_id WHERE a.farmer_id = ? ORDER BY a.id DESC'); $as->execute([$f['id']]); $apps = $as->fetchAll();
?>
<section class="panel"><div class="panel-head"><h2>Farms</h2><a class="btn btn-light btn-sm" href="<?= url('farms/form.php?farmer=' . $f['id']) ?>"><i class="fa-solid fa-plus"></i> Add farm</a></div>
<?php if (!$farms): ?><p class="empty">No farms recorded for this farmer.</p><?php else: ?><div class="table-wrap"><table>
<thead><tr><th>Farm</th><th>Barangay</th><th>Location of farm and land</th><th>Area (ha)</th><th>Insured Area (ha)</th><th>Ownership</th></tr></thead>
<tbody><?php foreach ($farms as $r): ?><tr><td><a href="<?= url('farms/form.php?id=' . $r['id']) ?>"><?= e($r['farm_name']) ?></a></td><td><?= e($r['barangay']) ?></td><td><?= !empty($r['location']) ? e($r['location']) : '<span class="muted">—</span>' ?></td><td><?= e($r['area_ha']) ?></td><td><?= !empty($r['insured_area_ha']) ? e($r['insured_area_ha']) : '<span class="muted">—</span>' ?></td><td><?= e($r['ownership'] ?? '—') ?></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?></section>
<section class="panel"><div class="panel-head"><h2>Insurance application history</h2></div>
<?php if (!$apps): ?><p class="empty">No applications yet.</p><?php else: ?><div class="table-wrap"><table>
<thead><tr><th>Application no.</th><th>Crop</th><th>Date applied</th><th>Status</th></tr></thead>
<tbody><?php foreach ($apps as $r): ?><tr><td><a href="<?= url('applications/view.php?id=' . $r['id']) ?>"><?= e($r['app_no']) ?></a></td><td><?= e($r['crop_type']) ?></td><td><?= e($r['date_applied']) ?></td><td><?= badge($r['status']) ?></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?></section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
