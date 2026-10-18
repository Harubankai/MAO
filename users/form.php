<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();
require_perm('manage_users');

$id = (int)($_GET['id'] ?? 0);
$u = [
    'username' => '',
    'full_name' => '',
    'role' => 'staff'
];

if ($id) {
    $st = db()->prepare('SELECT id, username, full_name, role, created_at FROM users WHERE id = ?');
    $st->execute([$id]);
    $u = $st->fetch();
    if (!$u) {
        flash('danger', 'User account not found.');
        redirect('users/index.php');
    }
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $u['username'] = trim($_POST['username'] ?? '');
    $u['full_name'] = trim($_POST['full_name'] ?? '');
    $u['role'] = trim($_POST['role'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    // Validation: Username
    if ($u['username'] === '') {
        $errors['username'] = 'Username is required.';
    } elseif (!preg_match('/^[A-Za-z0-9_]{3,30}$/', $u['username'])) {
        $errors['username'] = 'Use 3-30 letters, numbers, or underscores (no spaces).';
    } else {
        $dupSt = db()->prepare('SELECT id FROM users WHERE username = ? AND id <> ?');
        $dupSt->execute([$u['username'], $id]);
        if ($dupSt->fetch()) {
            $errors['username'] = 'This username is already taken. Please choose another.';
        }
    }

    // Validation: Full Name
    if ($u['full_name'] === '' || mb_strlen($u['full_name']) > 120) {
        $errors['full_name'] = 'Full name is required (up to 120 characters).';
    }

    // Validation: Role
    if (!in_array($u['role'], ['admin', 'staff'], true)) {
        $errors['role'] = 'Please select a valid role.';
    } elseif ($id && $u['role'] !== 'admin') {
        // Protect last admin
        $adminCount = (int)db()->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
        $currSt = db()->prepare("SELECT role FROM users WHERE id = ?");
        $currSt->execute([$id]);
        if ($currSt->fetchColumn() === 'admin' && $adminCount <= 1) {
            $errors['role'] = 'Cannot change the role of the only remaining Administrator.';
        }
    }

    // Validation: Password
    if (!$id) {
        // New user requires password
        if ($password === '') {
            $errors['password'] = 'Password is required for new accounts.';
        } elseif (strlen($password) < 6) {
            $errors['password'] = 'Password must be at least 6 characters.';
        } elseif ($password !== $password_confirm) {
            $errors['password_confirm'] = 'Passwords do not match.';
        }
    } else {
        // Editing: password is optional
        if ($password !== '') {
            if (strlen($password) < 6) {
                $errors['password'] = 'Password must be at least 6 characters.';
            } elseif ($password !== $password_confirm) {
                $errors['password_confirm'] = 'Passwords do not match.';
            }
        }
    }

    if (!$errors) {
        if ($id) {
            if ($password !== '') {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $upSt = db()->prepare('UPDATE users SET username = ?, full_name = ?, role = ?, password_hash = ? WHERE id = ?');
                $upSt->execute([$u['username'], $u['full_name'], $u['role'], $hash, $id]);
            } else {
                $upSt = db()->prepare('UPDATE users SET username = ?, full_name = ?, role = ? WHERE id = ?');
                $upSt->execute([$u['username'], $u['full_name'], $u['role'], $id]);
            }

            // If current user updated their own profile, sync session
            if ($id === (int)current_user()['id']) {
                $_SESSION['user']['username'] = $u['username'];
                $_SESSION['user']['full_name'] = $u['full_name'];
                $_SESSION['user']['role'] = $u['role'];
            }

            log_activity('Updated user', 'user', $id, $u['username']);
            flash('success', 'User "' . e($u['username']) . '" updated successfully.');
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $inSt = db()->prepare('INSERT INTO users (username, password_hash, full_name, role, created_at) VALUES (?, ?, ?, ?, NOW())');
            $inSt->execute([$u['username'], $hash, $u['full_name'], $u['role']]);
            $newId = (int)db()->lastInsertId();

            log_activity('Created user', 'user', $newId, $u['username']);
            flash('success', 'User account "' . e($u['username']) . '" created successfully.');
        }
        redirect('users/index.php');
    }
}

$editing = (bool)$id;
$pageTitle = $editing ? 'Edit User' : 'Add User';
$active = 'user';
require __DIR__ . '/../includes/header.php';
function uerr(array $errors, string $k): string {
    return isset($errors[$k]) ? '<small class="field-err">' . e($errors[$k]) . '</small>' : '';
}
?>
<div class="page-head row">
    <div>
        <h1><?= e($pageTitle) ?></h1>
        <p class="muted"><?= $editing ? 'Update user account details, role, or credentials.' : 'Create a new staff or administrator account.' ?></p>
    </div>
    <a class="btn btn-light" href="<?= url('users/index.php') ?>"><i class="fa-solid fa-arrow-left"></i> Back to users</a>
</div>

<section class="panel" style="max-width: 680px;">
    <form method="post" novalidate class="form-grid">
        <?= csrf_field() ?>

        <div>
            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="<?= e($u['username']) ?>" placeholder="e.g. jdelacruz" maxlength="30" required autocomplete="off">
            <?= uerr($errors, 'username') ?>
        </div>

        <div>
            <label for="role">Role</label>
            <select id="role" name="role" required>
                <option value="staff" <?= $u['role'] === 'staff' ? 'selected' : '' ?>>MAO Staff (records & updates)</option>
                <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>System Administrator (full access)</option>
            </select>
            <?= uerr($errors, 'role') ?>
        </div>

        <div class="full">
            <label for="full_name">Full Name</label>
            <input type="text" id="full_name" name="full_name" value="<?= e($u['full_name']) ?>" placeholder="e.g. Juan Dela Cruz" maxlength="120" required>
            <?= uerr($errors, 'full_name') ?>
        </div>

        <div>
            <label for="password"><?= $editing ? 'New Password <span class="muted" style="font-weight:normal">(optional)</span>' : 'Password' ?></label>
            <input type="password" id="password" name="password" placeholder="<?= $editing ? 'Leave blank to keep current' : 'At least 6 characters' ?>" autocomplete="new-password">
            <?= uerr($errors, 'password') ?>
        </div>

        <div>
            <label for="password_confirm">Confirm Password</label>
            <input type="password" id="password_confirm" name="password_confirm" placeholder="Re-type password" autocomplete="new-password">
            <?= uerr($errors, 'password_confirm') ?>
        </div>

        <div class="full form-actions" style="margin-top: 24px; padding-top: 14px; border-top: 1px solid var(--line);">
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid <?= $editing ? 'fa-check' : 'fa-user-plus' ?>"></i> <?= $editing ? 'Save Changes' : 'Create Account' ?>
            </button>
            <a href="<?= url('users/index.php') ?>" class="btn btn-light">Cancel</a>
        </div>
    </form>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
