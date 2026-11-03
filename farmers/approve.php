<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();
require_perm('review_farmers'); // admin only
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('farmers/index.php');
csrf_check();
$id = (int)($_POST['id'] ?? 0);
$decision = $_POST['decision'] ?? '';
$reason = trim($_POST['reason'] ?? '');
$back = 'farmers/view.php?id=' . $id;

if (!in_array($decision, ['approve', 'reject'], true)) { flash('danger', 'Choose approve or reject.'); redirect($back); }
if ($decision === 'reject' && ($reason === '' || mb_strlen($reason) > 500)) {
    flash('danger', 'Enter a reason for rejection (up to 500 characters) so the Staff can correct the record.');
    redirect($back);
}
$pdo = db(); $pdo->beginTransaction();
// Only pending records can be decided; the status guard also stops double submissions.
$st = $pdo->prepare("UPDATE farmers SET status = ?, rejection_reason = ?, reviewed_by = ?, reviewed_at = NOW() WHERE id = ? AND status = 'pending'");
$st->execute([$decision === 'approve' ? 'active' : 'rejected', $decision === 'reject' ? $reason : null, current_user()['id'], $id]);
if (!$st->rowCount()) {
    $pdo->rollBack();
    flash('danger', 'This farmer is not pending approval. It may have already been reviewed.');
    redirect($back);
}
log_farmer_approval($id, $decision === 'approve' ? 'Approved' : 'Rejected', $decision === 'reject' ? $reason : null);
$pdo->commit();
log_activity($decision === 'approve' ? 'Approved farmer' : 'Rejected farmer', 'farmer', $id);
flash('success', $decision === 'approve' ? 'Farmer record approved.' : 'Farmer record rejected. The Staff can now correct and resubmit it.');
redirect($back);
