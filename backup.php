<?php
require_once __DIR__ . '/includes/functions.php';
require_login(); require_perm('backup');
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $proc = @proc_open([MYSQLDUMP_PATH, '--single-transaction', '--routines', '-h', DB_HOST, '-u', DB_USER, DB_NAME], [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, null, ['MYSQL_PWD' => DB_PASS, 'PATH' => getenv('PATH')]);
    if (!is_resource($proc)) $err = 'Could not run mysqldump. Set MYSQLDUMP_PATH in config/database.php.';
    else {
        $dump = stream_get_contents($pipes[1]); stream_get_contents($pipes[2]); $rc = proc_close($proc);
        if ($rc !== 0 || $dump === '') $err = 'Backup failed. Check MYSQLDUMP_PATH and database credentials in config/database.php.';
        else {
            log_activity('Database backup', 'backup', null, strlen($dump) . ' bytes');
            header('Content-Type: application/sql'); header('Content-Disposition: attachment; filename="moncada_agri_' . date('Ymd_His') . '.sql"'); echo $dump; exit;
        }
    }
}
$pageTitle = 'Backup'; $active = 'backup';
require __DIR__ . '/includes/header.php';
?>
<div class="page-head"><h1>Database backup</h1></div>
<?php if ($err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endif; ?>
<section class="panel"><p>Download a full SQL backup of all records. Backups contain personal information, so store them securely (Data Privacy Act of 2012).</p>
<form method="post"><?= csrf_field() ?><button class="btn btn-primary"><i class="fa-solid fa-download"></i> Download backup</button></form></section>
<section class="panel"><h2>Restoring a backup</h2><p>Restoring overwrites current data, so it is done outside the web app by an administrator:</p>
<pre class="code">mysql -u root -p moncada_agri &lt; moncada_agri_YYYYMMDD_HHMMSS.sql</pre><p class="muted">Test restores on a copy of the database first.</p></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
