<?php
require_once __DIR__ . '/includes/functions.php';
require_login();
$types = ['farmers' => 'Registered farmers', 'farms' => 'Farm records', 'crops' => 'Crop records', 'applications' => 'Insurance applications'];
$farm = farmer_farm_sql('x');
$defs = [
 'farmers' => ['SELECT x.farmer_id `Farmer ID`, x.full_name `Name`, x.birthday `Birthday`, x.id_number `RSBSA ID number`, x.contact_number `Contact`, x.gender `Gender`, ' . $farm['barangay'] . ' `Barangay`, ' . $farm['location'] . ' `Farm location`, ' . $farm['crop'] . ' `Crop`, ' . $farm['area'] . ' `Total area (ha)`, ' . $farm['insured'] . ' `Insured area (ha)`, ' . $farm['ownership'] . ' `Ownership`, x.date_registered `Date registered`, CASE x.status WHEN \'active\' THEN \'Approved\' WHEN \'pending\' THEN \'Pending Approval\' ELSE \'Rejected\' END `Approval status` FROM farmers x', 'x.date_registered'],
 'farms' => ['SELECT fr.farmer_id `Farmer ID`, fr.full_name `Farmer`, x.farm_name `Farm`, x.barangay `Barangay`, x.location `Location of farm and land`, x.area_ha `Area (ha)`, x.insured_area_ha `Insured Area (ha)`, x.ownership `Ownership`, DATE(x.created_at) `Recorded` FROM farms x JOIN farmers fr ON fr.id = x.farmer_id', 'DATE(x.created_at)'],
 'crops' => ['SELECT fr.full_name `Farmer`, f.farm_name `Farm`, x.crop_type `Crop`, x.season `Season`, x.planting_date `Planted`, x.area_planted_ha `Area (ha)`, x.status `Status` FROM crops x JOIN farms f ON f.id = x.farm_id JOIN farmers fr ON fr.id = f.farmer_id', 'x.planting_date'],
 'applications' => ['SELECT x.app_no `Application no.`, fr.full_name `Farmer`, c.crop_type `Crop`, x.date_applied `Date of submit`, (SELECT DATE(vr.changed_at) FROM verification_records vr WHERE vr.application_id = x.id AND vr.new_status = \'Approved\' ORDER BY vr.id DESC LIMIT 1) `Date approved`, x.status `Status` FROM applications x JOIN farmers fr ON fr.id = x.farmer_id JOIN crops c ON c.id = x.crop_id', 'x.date_applied'],
];
$type = isset($types[$_GET['type'] ?? '']) ? $_GET['type'] : 'applications';
$from = valid_date($_GET['from'] ?? '') ? $_GET['from'] : ''; $to = valid_date($_GET['to'] ?? '') ? $_GET['to'] : '';
$status = in_array($_GET['status'] ?? '', APP_STATUSES, true) ? $_GET['status'] : '';
[$sql, $dcol] = $defs[$type]; $w = ['1=1']; $p = [];
if ($from) { $w[] = "$dcol >= ?"; $p[] = $from; } if ($to) { $w[] = "$dcol <= ?"; $p[] = $to; }
if ($type === 'applications' && $status) { $w[] = 'x.status = ?'; $p[] = $status; }
$st = db()->prepare("$sql WHERE " . implode(' AND ', $w) . " ORDER BY $dcol DESC"); $st->execute($p); $rows = $st->fetchAll();
if (($_GET['export'] ?? '') === 'csv') {
    log_activity('Exported report', 'report', null, $type . ' CSV, ' . count($rows) . ' rows');
    header('Content-Type: text/csv; charset=utf-8'); header('Content-Disposition: attachment; filename="' . $type . '_' . date('Ymd') . '.csv"');
    $o = fopen('php://output', 'w'); fwrite($o, "\xEF\xBB\xBF");
    if ($rows) fputcsv($o, array_keys($rows[0]));
    foreach ($rows as $r) fputcsv($o, array_map(fn($v) => preg_match('/^[=+\-@]/', (string)$v) ? "'" . $v : $v, $r));
    exit;
}
log_activity('Generated report', 'report', null, $type . ', ' . count($rows) . ' rows');
$pageTitle = 'Reports'; $active = 'reports';
require __DIR__ . '/includes/header.php';
$q = http_build_query(['type' => $type, 'from' => $from, 'to' => $to, 'status' => $status, 'export' => 'csv']);
?>
<div class="page-head row">
    <h1>Reports</h1>
    <div class="no-print">
        <button class="btn btn-light" onclick="window.print()"><i class="fa-solid fa-print"></i> Print</button>
        <a class="btn btn-primary" href="?<?= e($q) ?>"><i class="fa-solid fa-file-csv"></i> Export CSV</a>
    </div>
</div>

<section class="panel no-print">
    <form method="get" class="search" id="reportForm" style="flex-wrap:wrap;align-items:flex-end;">
        <div style="display:flex;flex-direction:column;gap:4px;">
            <label style="margin:0;font-size:.8rem;color:var(--mu);">Report Type</label>
            <select name="type" style="width:auto;min-width:200px;" onchange="this.form.submit()">
                <?php foreach ($types as $k => $l): ?>
                <option value="<?= $k ?>" <?= $type === $k ? 'selected' : '' ?>><?= e($l) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="display:flex;flex-direction:column;gap:4px;">
            <label style="margin:0;font-size:.8rem;color:var(--mu);"><?= $type === 'applications' ? 'Date of Submit (From)' : 'Date From' ?></label>
            <input type="date" name="from" value="<?= e($from) ?>" style="width:auto;" aria-label="From date">
        </div>
        <div style="display:flex;flex-direction:column;gap:4px;">
            <label style="margin:0;font-size:.8rem;color:var(--mu);"><?= $type === 'applications' ? 'Date of Submit (To)' : 'Date To' ?></label>
            <input type="date" name="to" value="<?= e($to) ?>" style="width:auto;" aria-label="To date">
        </div>
        <div style="display:flex;flex-direction:column;gap:4px;">
            <label style="margin:0;font-size:.8rem;color:var(--mu);">Status (Applications)</label>
            <select name="status" style="width:auto;min-width:180px;" onchange="this.form.submit()">
                <option value="">Any status</option>
                <?php foreach (APP_STATUSES as $x): ?>
                <option <?= $status === $x ? 'selected' : '' ?>><?= $x ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button class="btn btn-primary" type="submit" style="align-self:flex-end;">
            <i class="fa-solid fa-chart-column"></i> Generate
        </button>
        <?php if ($from || $to || $status): ?>
        <a class="btn btn-light" href="?type=<?= urlencode($type) ?>" style="align-self:flex-end;">
            <i class="fa-solid fa-xmark"></i> Clear filters
        </a>
        <?php endif; ?>
    </form>
</section>

<section class="panel">
    <h2><?= e($types[$type]) ?></h2>
    <p class="muted">Municipal Agriculture Office, Moncada, Tarlac &middot; Generated <?= date('M d, Y g:i A') ?> by <?= e(current_user()['full_name']) ?><?= $from || $to ? ' &middot; Period: ' . e($from ?: 'start') . ' to ' . e($to ?: 'today') : '' ?></p>
    <p><strong>Total records: <?= count($rows) ?></strong><?php if ($type === 'applications' && $rows): foreach (array_count_values(array_column($rows, 'Status')) as $s => $c): ?> &middot; <?= e($s) ?>: <?= $c ?><?php endforeach; endif; ?></p>
    <?php if (!$rows): ?>
    <p class="empty">No records match these filters.</p>
    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><?php foreach (array_keys($rows[0]) as $h): ?><th><?= e($h) ?></th><?php endforeach; ?></tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $r): ?>
                <tr><?php foreach ($r as $v): ?><td><?= e($v) ?></td><?php endforeach; ?></tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>

