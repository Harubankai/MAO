<?php
$pageTitle = 'Insurance Applications'; 
$active = 'insurance';
require __DIR__ . '/../includes/header.php';

$q = trim($_GET['q'] ?? '');
$s = trim($_GET['status'] ?? '');
$from = trim($_GET['from'] ?? ($_GET['date_submit'] ?? ''));
$to = trim($_GET['to'] ?? '');
$approved = trim($_GET['approved'] ?? ($_GET['date_approved'] ?? ''));

$w = ['1=1'];
$p = [];

if ($q !== '') {
    $w[] = '(a.app_no LIKE ? OR fr.full_name LIKE ? OR fr.farmer_id LIKE ?)';
    array_push($p, "%$q%", "%$q%", "%$q%");
}

if (in_array($s, APP_STATUSES, true)) {
    $w[] = 'a.status = ?';
    $p[] = $s;
} else {
    $s = '';
}

// Date of submit filter
if (valid_date($from)) {
    if (valid_date($to)) {
        $w[] = 'a.date_applied >= ? AND a.date_applied <= ?';
        $p[] = $from;
        $p[] = $to;
    } else {
        $w[] = 'a.date_applied = ?';
        $p[] = $from;
    }
} else {
    $from = '';
}

// Date approved filter
if (valid_date($approved)) {
    $w[] = 'a.id IN (SELECT vr.application_id FROM verification_records vr WHERE vr.new_status = "Approved" AND DATE(vr.changed_at) = ?)';
    $p[] = $approved;
} else {
    $approved = '';
}

$per = 10;
$page = max(1, (int)($_GET['page'] ?? 1));

$countSql = 'SELECT COUNT(*) FROM applications a JOIN farmers fr ON fr.id = a.farmer_id JOIN crops c ON c.id = a.crop_id WHERE ' . implode(' AND ', $w);
$st = db()->prepare($countSql);
$st->execute($p);
$total = (int)$st->fetchColumn();
$pages = max(1, (int)ceil($total / $per));
$page = min($page, $pages);
$offset = ($page - 1) * $per;

$sql = 'SELECT a.*, fr.full_name, fr.farmer_id AS fno, c.crop_type, ' .
       '(SELECT DATE(vr.changed_at) FROM verification_records vr WHERE vr.application_id = a.id AND vr.new_status = "Approved" ORDER BY vr.id DESC LIMIT 1) AS date_approved ' .
       'FROM applications a ' .
       'JOIN farmers fr ON fr.id = a.farmer_id ' .
       'JOIN crops c ON c.id = a.crop_id ' .
       'WHERE ' . implode(' AND ', $w) . ' ' .
       'ORDER BY a.id DESC LIMIT ' . $per . ' OFFSET ' . $offset;

$st = db()->prepare($sql);
$st->execute($p);
$rows = $st->fetchAll();
?>

<div class="page-head row">
    <div>
        <h1>Insurance Applications</h1>
        <p class="muted"><?= $total ?> application<?= $total === 1 ? '' : 's' ?><?= $q !== '' ? ' matching "' . e($q) . '"' : '' ?></p>
    </div>
    <a class="btn btn-primary" href="form.php"><i class="fa-solid fa-plus"></i> New application</a>
</div>

<section class="panel">
    <form method="get" class="search" id="insuranceFilterForm" style="flex-wrap:wrap;align-items:flex-end;gap:12px;">
        <div style="flex:1;min-width:200px;display:flex;flex-direction:column;gap:4px;">
            <label for="qInput" style="margin:0;font-size:.8rem;color:var(--mu);font-weight:600;">Search Application</label>
            <input type="search" id="qInput" name="q" value="<?= e($q) ?>" placeholder="Application no., farmer name, or ID">
        </div>

        <div style="display:flex;flex-direction:column;gap:4px;">
            <label for="statusFilter" style="margin:0;font-size:.8rem;color:var(--mu);font-weight:600;">Insurance Status</label>
            <select id="statusFilter" name="status" style="width:auto;min-width:160px;" onchange="this.form.submit()">
                <option value="">All statuses</option>
                <?php foreach (APP_STATUSES as $x): ?>
                <option value="<?= $x ?>" <?= $s === $x ? 'selected' : '' ?>><?= $x ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="display:flex;flex-direction:column;gap:4px;">
            <label for="dateSubmitFilter" style="margin:0;font-size:.8rem;color:var(--mu);font-weight:600;">Date of Submit</label>
            <input type="date" id="dateSubmitFilter" name="from" value="<?= e($from) ?>" style="width:auto;" onchange="this.form.submit()">
        </div>

        <div style="display:flex;flex-direction:column;gap:4px;">
            <label for="dateApprovedFilter" style="margin:0;font-size:.8rem;color:var(--mu);font-weight:600;">Date Approved</label>
            <input type="date" id="dateApprovedFilter" name="approved" value="<?= e($approved) ?>" style="width:auto;" onchange="this.form.submit()">
        </div>

        <button class="btn btn-primary" type="submit" style="align-self:flex-end;">
            <i class="fa-solid fa-filter"></i> Filter
        </button>
        <?php if ($q !== '' || $s !== '' || $from !== '' || $approved !== '' || $to !== ''): ?>
        <a class="btn btn-light" href="index.php" style="align-self:flex-end;">
            <i class="fa-solid fa-xmark"></i> Clear filter
        </a>
        <?php endif; ?>
    </form>

    <?php if (!$rows): ?>
        <p class="empty">No applications match your filter criteria. Try changing the filters or create a new application.</p>
    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Application no.</th>
                    <th>Farmer</th>
                    <th>Crop</th>
                    <th>Date of submit</th>
                    <th>Date approved</th>
                    <th>Status</th>
                    <th class="right">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $r): ?>
                <tr>
                    <td><strong><?= e($r['app_no']) ?></strong></td>
                    <td><?= e($r['full_name']) ?> <small class="muted"><?= e($r['fno']) ?></small></td>
                    <td><?= e($r['crop_type']) ?></td>
                    <td><?= e($r['date_applied']) ?></td>
                    <td>
                        <?php if (!empty($r['date_approved'])): ?>
                            <span style="color:var(--g700);font-weight:600;"><i class="fa-solid fa-circle-check"></i> <?= e($r['date_approved']) ?></span>
                        <?php elseif ($r['status'] === 'Approved'): ?>
                            <span style="color:var(--g700);font-weight:600;"><i class="fa-solid fa-circle-check"></i> Approved</span>
                        <?php else: ?>
                            <span class="muted">&mdash;</span>
                        <?php endif; ?>
                    </td>
                    <td><?= badge($r['status']) ?></td>
                    <td class="right actions">
                        <a class="icon-btn" title="Open" href="view.php?id=<?= $r['id'] ?>"><i class="fa-solid fa-eye"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if ($pages > 1): ?>
    <div class="pager">
        <?php for ($i = 1; $i <= $pages; $i++): ?>
            <a class="<?= $i === $page ? 'cur' : '' ?>" href="?<?= e(http_build_query(array_merge($_GET, ['page' => $i]))) ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>

