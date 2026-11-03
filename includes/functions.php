<?php
date_default_timezone_set('Asia/Manila');
require_once __DIR__ . '/../config/database.php';
session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
session_start();

// Web path of project root, e.g. /moncada-farmers
define('BASE_URL', rtrim(str_replace('\\', '/', str_replace(str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'])), '', str_replace('\\', '/', dirname(__DIR__)))), '/'));

function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function url(string $p = ''): string { return BASE_URL . '/' . ltrim($p, '/'); }
function redirect(string $p): void { header('Location: ' . url($p)); exit; }
function is_logged_in(): bool { return !empty($_SESSION['user']); }
function require_login(): void { if (!is_logged_in()) redirect('login.php'); }
function current_user(): array { return $_SESSION['user'] ?? []; }

function csrf_token(): string {
    return $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
}
function csrf_field(): string { return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">'; }
function csrf_check(): void {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(400);
        exit('Invalid request token. Go back and try again.');
    }
}
function flash(string $type, string $msg): void { $_SESSION['flash'] = [$type, $msg]; }
function take_flash(): ?array { $f = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $f; }

const APP_STATUSES = ['Submitted', 'Under Review', 'Verified', 'Approved', 'Returned'];
// PLACEHOLDER checklist: confirm the actual requirements with the MAO and edit this list.
const REQUIRED_DOCS = ['Valid ID', 'Barangay certification', 'Proof of land ownership or tenancy', 'Farm location or sketch map'];

// Permission matrix. Admin: everything except encoding farmers (sole authority to review them).
// Staff: records, status updates and farmer encoding - never farmer approval.
function can(string $p): bool {
    $role = $_SESSION['user']['role'] ?? '';
    if ($role === 'admin') return $p !== 'encode_farmers';
    return $role === 'staff' && in_array($p, ['manage_records', 'update_status', 'encode_farmers'], true);
}

// Farmer approval workflow. DB value => label shown to users ('active' kept for existing records).
const FARMER_STATUSES = ['pending' => 'Pending Approval', 'active' => 'Approved', 'rejected' => 'Rejected'];
function farmer_badge(string $status): string { return badge(FARMER_STATUSES[$status] ?? $status); }
// SQL expressions for a farmer's farm data, from the farm and crop records. Falls back to the farmer's own columns for old records.
function farmer_farm_sql(string $t): array {
    $list = fn($col, $old) => "COALESCE((SELECT GROUP_CONCAT(DISTINCT fm.$col ORDER BY fm.$col SEPARATOR ', ') FROM farms fm WHERE fm.farmer_id = $t.id), $t.$old)";
    $sum = fn($col, $old) => "COALESCE((SELECT SUM(fm.$col) FROM farms fm WHERE fm.farmer_id = $t.id), $t.$old)";
    return [
        'barangay' => $list('barangay', 'barangay'),
        'location' => $list('location', 'farm_location'),
        'ownership' => $list('ownership', 'farm_ownership'),
        'area' => $sum('area_ha', 'total_farm_area_ha'),
        'insured' => $sum('insured_area_ha', 'insured_area_ha'),
        'crop' => "COALESCE((SELECT GROUP_CONCAT(DISTINCT cr.crop_type ORDER BY cr.crop_type SEPARATOR ', ') FROM crops cr JOIN farms fm ON fm.id = cr.farm_id WHERE fm.farmer_id = $t.id), $t.crop_type)",
    ];
}
function log_farmer_approval(int $farmerId, string $action, ?string $remarks = null): void {
    db()->prepare('INSERT INTO farmer_approvals (farmer_id, action, remarks, acted_by) VALUES (?,?,?,?)')
        ->execute([$farmerId, $action, $remarks, $_SESSION['user']['id'] ?? null]);
}
function require_perm(string $p): void {
    if (!can($p)) { http_response_code(403); exit('You do not have permission to do this.'); }
}
// Log IDs and reference numbers only - never farmer names or contact details.
function log_activity(string $action, ?string $entity = null, ?int $id = null, ?string $details = null): void {
    try {
        db()->prepare('INSERT INTO activity_logs (user_id, action, entity, entity_id, details, ip_address) VALUES (?,?,?,?,?,?)')
            ->execute([$_SESSION['user']['id'] ?? null, $action, $entity, $id, $details, $_SERVER['REMOTE_ADDR'] ?? null]);
    } catch (Throwable $e) { /* logging must not break the request */ }
}
function badge(string $s): string {
    $c = ['Submitted'=>'info','Under Review'=>'warn','Verified'=>'ok','Approved'=>'ok','Returned'=>'bad','Rejected'=>'bad','Pending Approval'=>'warn','Planted'=>'info','Harvested'=>'ok','Failed'=>'bad'];
    return '<span class="badge badge-' . ($c[$s] ?? 'info') . '">' . e($s) . '</span>';
}
function field_err(array $errors, string $k): string {
    return isset($errors[$k]) ? '<small class="field-err">' . e($errors[$k]) . '</small>' : '';
}
function valid_date(string $d): bool {
    $x = DateTime::createFromFormat('Y-m-d', $d);
    return $x && $x->format('Y-m-d') === $d;
}
