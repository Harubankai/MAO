<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();
require_perm('manage_users');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('users/index.php');
}

csrf_check();

$id = (int)($_POST['id'] ?? 0);
$currentId = (int)current_user()['id'];

if ($id <= 0) {
    flash('danger', 'Invalid user account.');
    redirect('users/index.php');
}

if ($id === $currentId) {
    flash('danger', 'You cannot delete your own account.');
    redirect('users/index.php');
}

$st = db()->prepare('SELECT id, username, role FROM users WHERE id = ?');
$st->execute([$id]);
$u = $st->fetch();

if (!$u) {
    flash('danger', 'User account not found.');
    redirect('users/index.php');
}

// Check if this is the only remaining admin
if ($u['role'] === 'admin') {
    $adminCount = (int)db()->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
    if ($adminCount <= 1) {
        flash('danger', 'Cannot delete the only remaining Administrator account.');
        redirect('users/index.php');
    }
}

try {
    $del = db()->prepare('DELETE FROM users WHERE id = ?');
    $del->execute([$id]);
    if ($del->rowCount()) {
        log_activity('Deleted user', 'user', $id, $u['username']);
        flash('success', 'User account "' . e($u['username']) . '" has been deleted.');
    } else {
        flash('danger', 'User could not be deleted.');
    }
} catch (PDOException $e) {
    flash('danger', 'This user could not be deleted due to associated database activity records.');
}

redirect('users/index.php');
