<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();
require_perm('manage_users');

$pageTitle = 'User Management';
$active = 'user';
require __DIR__ . '/../includes/header.php';

$q = trim($_GET['q'] ?? '');
$roleFilter = trim($_GET['role'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$per = 10;

$whereClauses = [];
$params = [];

if ($q !== '') {
    $whereClauses[] = '(username LIKE ? OR full_name LIKE ?)';
    $params[] = "%$q%";
    $params[] = "%$q%";
}
if ($roleFilter !== '' && in_array($roleFilter, ['admin', 'staff'], true)) {
    $whereClauses[] = 'role = ?';
    $params[] = $roleFilter;
}

$where = $whereClauses ? ('WHERE ' . implode(' AND ', $whereClauses)) : '';

$totalSt = db()->prepare("SELECT COUNT(*) FROM users $where");
$totalSt->execute($params);
$total = (int)$totalSt->fetchColumn();

$pages = max(1, (int)ceil($total / $per));
$page = min($page, $pages);
$offset = ($page - 1) * $per;

$st = db()->prepare("SELECT * FROM users $where ORDER BY id ASC LIMIT $per OFFSET $offset");
$st->execute($params);
$rows = $st->fetchAll();

// Metrics
$counts = db()->query("SELECT role, COUNT(*) as c FROM users GROUP BY role")->fetchAll(PDO::FETCH_KEY_PAIR);
$adminCount = (int)($counts['admin'] ?? 0);
$staffCount = (int)($counts['staff'] ?? 0);
$totalUsers = $adminCount + $staffCount;
$currentUserId = (int)($user['id'] ?? 0);
?>
<div class="page-head row">
    <div>
        <h1>User Management</h1>
        <p class="muted">Manage system administrators and staff accounts for Moncada Agriculture Office.</p>
    </div>
    <a class="btn btn-primary" href="<?= url('users/form.php') ?>"><i class="fa-solid fa-user-plus"></i> Add user</a>
</div>

<div class="stats">
    <div class="stat">
        <i class="fa-solid fa-users-gear"></i>
        <div>
            <div class="stat-n"><?= $totalUsers ?></div>
            <div class="stat-l">Total Accounts</div>
        </div>
    </div>
    <div class="stat">
        <i class="fa-solid fa-shield-halved"></i>
        <div>
            <div class="stat-n"><?= $adminCount ?></div>
            <div class="stat-l">Administrators</div>
        </div>
    </div>
    <div class="stat">
        <i class="fa-solid fa-user-tie"></i>
        <div>
            <div class="stat-n"><?= $staffCount ?></div>
            <div class="stat-l">Staff Members</div>
        </div>
    </div>
</div>

<section class="panel">
    <form method="get" class="search">
        <input type="search" name="q" value="<?= e($q) ?>" placeholder="Search by username or full name">
        <select name="role" style="max-width:180px;">
            <option value="">All Roles</option>
            <option value="admin" <?= $roleFilter === 'admin' ? 'selected' : '' ?>>Administrator</option>
            <option value="staff" <?= $roleFilter === 'staff' ? 'selected' : '' ?>>Staff</option>
        </select>
        <button class="btn btn-primary" type="submit"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
        <?php if ($q !== '' || $roleFilter !== ''): ?>
            <a class="btn btn-light" href="index.php">Clear</a>
        <?php endif; ?>
    </form>

    <?php if (!$rows): ?>
        <p class="empty"><?= ($q !== '' || $roleFilter !== '') ? 'No user accounts match your filters.' : 'No users registered yet.' ?></p>
    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Full name</th>
                    <th>Role</th>
                    <th>Last login</th>
                    <th>Date added</th>
                    <th class="right">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $r): ?>
                <tr>
                    <td>
                        <div class="user-cell">
                            <span class="user-avatar"><?= strtoupper(substr($r['username'], 0, 1)) ?></span>
                            <div>
                                <strong><?= e($r['username']) ?></strong>
                                <?php if ((int)$r['id'] === $currentUserId): ?>
                                    <small class="muted">(You)</small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                    <td><?= e($r['full_name']) ?></td>
                    <td>
                        <?php if ($r['role'] === 'admin'): ?>
                            <span class="badge badge-warn"><i class="fa-solid fa-shield-halved"></i> Administrator</span>
                        <?php else: ?>
                            <span class="badge badge-info"><i class="fa-solid fa-user"></i> Staff</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?= !empty($r['last_login']) ? e(date('M d, Y h:i A', strtotime($r['last_login']))) : '<span class="muted">Never logged in</span>' ?>
                    </td>
                    <td><?= e(date('M d, Y', strtotime($r['created_at']))) ?></td>
                    <td class="right actions">
                        <a class="icon-btn" title="Edit user" href="form.php?id=<?= $r['id'] ?>"><i class="fa-solid fa-pen"></i></a>
                        <?php if ((int)$r['id'] !== $currentUserId): ?>
                            <button class="icon-btn danger" title="Delete user" type="button" data-delete="<?= $r['id'] ?>" data-name="<?= e($r['username'] . ' (' . $r['full_name'] . ')') ?>"><i class="fa-solid fa-trash"></i></button>
                        <?php else: ?>
                            <span class="icon-btn" style="opacity:0.3;cursor:not-allowed;" title="You cannot delete your own account"><i class="fa-solid fa-trash"></i></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ($pages > 1): ?>
    <div class="pager">
        <?php for ($i = 1; $i <= $pages; $i++): ?>
            <a class="<?= $i === $page ? 'cur' : '' ?>" href="?q=<?= urlencode($q) ?>&role=<?= urlencode($roleFilter) ?>&page=<?= $i ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</section>

<div class="modal" id="deleteModal" hidden>
    <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="dm-title">
        <h3 id="dm-title">Delete this user account?</h3>
        <p>You are about to delete <strong id="dm-name"></strong>. This user will immediately lose access to the system. This cannot be undone.</p>
        <form method="post" action="delete.php">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="dm-id">
            <div class="modal-actions">
                <button type="button" class="btn btn-light" id="dm-cancel">Cancel</button>
                <button type="submit" class="btn btn-danger"><i class="fa-solid fa-trash"></i> Delete User</button>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
