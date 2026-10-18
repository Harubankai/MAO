<?php
require_once __DIR__ . '/../includes/functions.php';
require_login(); require_perm('delete');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('farms/index.php');
csrf_check(); $id = (int)($_POST['id'] ?? 0);
try {
    $st = db()->prepare('DELETE FROM farms WHERE id = ?'); $st->execute([$id]);
    if ($st->rowCount()) { log_activity('Deleted farm', 'farm', $id); flash('success', 'Farm deleted.'); } else flash('danger', 'Farm not found.');
} catch (PDOException $e) { flash('danger', 'This farm still has crop records or applications. Remove those first.'); }
redirect('farms/index.php');
