<?php
require_once __DIR__ . '/../includes/functions.php';
require_login(); require_perm('delete');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('farmers/index.php');
csrf_check();
$id = (int)($_POST['id'] ?? 0);
try {
    $st = db()->prepare('DELETE FROM farmers WHERE id = ?'); $st->execute([$id]);
    if ($st->rowCount()) { log_activity('Deleted farmer', 'farmer', $id); flash('success', 'Farmer deleted.'); }
    else flash('danger', 'Farmer not found.');
} catch (PDOException $e) {
    flash('danger', 'This farmer still has farms or applications. Remove those first.');
}
redirect('farmers/index.php');
