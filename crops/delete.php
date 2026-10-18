<?php
require_once __DIR__ . '/../includes/functions.php';
require_login(); require_perm('delete');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('crops/index.php');
csrf_check(); $id = (int)($_POST['id'] ?? 0);
try {
    $st = db()->prepare('DELETE FROM crops WHERE id = ?'); $st->execute([$id]);
    if ($st->rowCount()) { log_activity('Deleted crop', 'crop', $id); flash('success', 'Crop record deleted.'); } else flash('danger', 'Crop record not found.');
} catch (PDOException $e) { flash('danger', 'This crop is used by an insurance application and cannot be deleted.'); }
redirect('crops/index.php');
