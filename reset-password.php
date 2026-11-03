<?php
require_once __DIR__ . '/includes/functions.php';
if (is_logged_in()) redirect('index.php');

$token   = trim($_GET['token'] ?? '');
$error   = '';
$success = false;
$user    = null;

// Validate token
if ($token === '') {
    redirect('forgot-password.php');
}

$st = db()->prepare('
    SELECT pr.id AS pr_id, pr.user_id, u.full_name, u.username
    FROM password_resets pr
    JOIN users u ON u.id = pr.user_id
    WHERE pr.token = ? AND pr.used = 0 AND pr.expires_at > NOW()
    LIMIT 1
');
$st->execute([$token]);
$row = $st->fetch();

if (!$row) {
    $error = 'This reset link is invalid or has expired. Please request a new one.';
} else {
    $user = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user) {
    csrf_check();
    $pass    = $_POST['password'] ?? '';
    $confirm = $_POST['password_confirm'] ?? '';

    if ($pass === '') {
        $error = 'Please enter a new password.';
    } elseif (strlen($pass) > 8) {
        $error = 'Password must be at most 8 characters.';
    } elseif (!preg_match('/[A-Z]/', $pass)) {
        $error = 'Password must contain at least 1 uppercase letter.';
    } elseif ($pass !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        db()->prepare('UPDATE users SET password_hash = ? WHERE id = ?')
           ->execute([$hash, $user['user_id']]);
        db()->prepare('UPDATE password_resets SET used = 1 WHERE id = ?')
           ->execute([$user['pr_id']]);
        log_activity('Password reset completed', 'users', $user['user_id']);
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password | Moncada Agriculture Office</title>
    <meta name="description" content="Set a new password for the Moncada Agricultural Records System.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --g900: #0d3d22; --g800: #14542f; --g700: #1a6b3a;
            --g600: #228048; --g500: #2a9a56;
        }
        body {
            font-family: 'Inter', system-ui, sans-serif;
            min-height: 100vh; min-height: 100dvh;
            background: #0d3d22; overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }
        .login-page {
            width: 100%; min-height: 100vh; min-height: 100dvh;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            position: relative;
            background-image: url('<?= url('assets/images/ricebg.jpg') ?>');
            background-size: cover; background-position: center;
            background-attachment: fixed; padding: 32px 16px;
        }
        .login-page::before {
            content: ''; position: fixed; inset: 0;
            background: linear-gradient(to bottom, rgba(5,30,14,.50) 0%, rgba(5,30,14,.30) 50%, rgba(5,30,14,.60) 100%);
            pointer-events: none; z-index: 1;
        }
        .login-card {
            width: 100%; max-width: 440px;
            background: rgba(255,255,255,0.18);
            backdrop-filter: blur(20px) saturate(1.4);
            -webkit-backdrop-filter: blur(20px) saturate(1.4);
            border-radius: 20px; border: 1px solid rgba(255,255,255,0.4);
            box-shadow: 0 8px 40px rgba(0,0,0,.35), inset 0 1px 0 rgba(255,255,255,.5);
            padding: 38px 32px 30px; margin: 0 auto;
            position: relative; z-index: 2;
            animation: slideUp .5s cubic-bezier(.22,.68,0,1.2) both;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .logo-wrap { display: flex; flex-direction: column; align-items: center; gap: 12px; margin-bottom: 18px; }
        .logo-circle {
            width: 80px; height: 80px; border-radius: 50%;
            background: rgba(255,255,255,.92); border: 3px solid rgba(255,255,255,.7);
            overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,.3);
        }
        .logo-circle img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .logo-title { text-align: center; }
        .logo-title h1 { font-size: 1.4rem; font-weight: 800; color: #fff; letter-spacing: -.02em; text-shadow: 0 2px 8px rgba(0,0,0,.4); }
        .logo-subtitle { font-size: .85rem; font-weight: 700; color: #d4f0df; margin-top: 2px; }
        hr.divider { border: none; border-top: 1px solid rgba(255,255,255,.35); margin: 18px 0 22px; }
        .form-heading h2 { font-size: 1.4rem; font-weight: 800; color: #fff; margin-bottom: 4px; }
        .form-heading p  { font-size: .87rem; color: rgba(255,255,255,.8); }
        .alert-err, .alert-ok {
            border-radius: 10px; padding: 11px 14px; font-size: .86rem;
            margin-top: 14px; display: flex; gap: 8px; align-items: flex-start;
        }
        .alert-err { background: #fdf0ef; border: 1px solid #f5c4c0; color: #8b2a22; animation: shake .35s ease both; }
        .alert-ok  { background: #eef8f1; border: 1px solid #b2dfc0; color: #1a5a2e; }
        @keyframes shake { 0%,100%{ transform: translateX(0) } 25%{ transform: translateX(-6px) } 75%{ transform: translateX(6px) } }
        .field { margin-top: 16px; }
        .field label { display: block; font-size: .82rem; font-weight: 600; color: rgba(255,255,255,.9); margin-bottom: 6px; }
        .field-hint { font-size: .75rem; color: rgba(255,255,255,.65); margin-top: 5px; }
        .field-inner {
            display: flex; align-items: center;
            border: 1.5px solid rgba(255,255,255,.4); border-radius: 10px;
            background: rgba(255,255,255,.2);
            transition: border-color .2s, box-shadow .2s, background .2s;
        }
        .field-inner:focus-within {
            border-color: rgba(255,255,255,.8);
            box-shadow: 0 0 0 3px rgba(255,255,255,.15);
            background: rgba(255,255,255,.28);
        }
        .field-icon { width: 44px; display: grid; place-items: center; color: rgba(255,255,255,.8); font-size: .95rem; flex-shrink: 0; }
        .field-toggle { width: 44px; display: grid; place-items: center; background: none; border: none; cursor: pointer; color: rgba(255,255,255,.75); font-size: .92rem; flex-shrink: 0; transition: color .15s; }
        .field-toggle:hover { color: #fff; }
        .field-inner input {
            flex: 1; min-width: 0; border: none; outline: none;
            background: transparent; font: inherit; font-size: 16px; color: #fff;
            padding: 13px 0 13px 2px;
        }
        .field-inner input::placeholder { color: rgba(255,255,255,.6); }
        .btn-submit {
            margin-top: 22px; width: 100%; padding: 14px;
            background: linear-gradient(135deg, var(--g700) 0%, var(--g800) 100%);
            color: #fff; border: none; border-radius: 10px;
            font: inherit; font-size: 1rem; font-weight: 700; cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,.35);
            transition: background .2s, transform .15s, box-shadow .2s;
        }
        .btn-submit:hover { background: linear-gradient(135deg, var(--g600) 0%, var(--g700) 100%); transform: translateY(-1px); box-shadow: 0 8px 28px rgba(0,0,0,.4); }
        .btn-submit:active { transform: translateY(0); }
        .back-link { text-align: center; margin-top: 16px; font-size: .85rem; }
        .back-link a { color: rgba(255,255,255,.85); text-decoration: none; font-weight: 500; transition: color .15s; }
        .back-link a:hover { color: #fff; text-decoration: underline; }
        .success-icon { font-size: 3rem; text-align: center; margin-bottom: 16px; }
        .btn-goto {
            display: block; margin-top: 22px; width: 100%; padding: 14px;
            background: linear-gradient(135deg, var(--g700) 0%, var(--g800) 100%);
            color: #fff; border: none; border-radius: 10px;
            font: inherit; font-size: 1rem; font-weight: 700; cursor: pointer;
            text-align: center; text-decoration: none;
            box-shadow: 0 4px 20px rgba(0,0,0,.35);
            transition: background .2s, transform .15s;
        }
        .btn-goto:hover { background: linear-gradient(135deg, var(--g600) 0%, var(--g700) 100%); transform: translateY(-1px); }
    </style>
</head>
<body>
<div class="login-page">
    <div class="login-card">
        <div class="logo-wrap">
            <div class="logo-circle">
                <img src="<?= url('assets/images/logo.jpg') ?>?v=<?= @filemtime(__DIR__ . '/assets/images/logo.jpg') ?>" alt="Moncada Agriculture Office Logo">
            </div>
            <div class="logo-title">
                <h1>Reset Password</h1>
                <div class="logo-subtitle">Agricultural Records System</div>
            </div>
        </div>

        <hr class="divider">

        <?php if ($success): ?>
            <div class="success-icon">✅</div>
            <div class="form-heading" style="text-align:center;">
                <h2>Password Updated!</h2>
                <p>Your password has been changed successfully. You can now log in with your new password.</p>
            </div>
            <a href="<?= url('login.php') ?>" class="btn-goto">
                <i class="fa-solid fa-arrow-right-to-bracket"></i> Go to Login
            </a>

        <?php elseif ($error && !$user): ?>
            <div class="form-heading">
                <h2>Link Expired</h2>
                <p>This reset link is no longer valid.</p>
            </div>
            <div class="alert-err"><i class="fa-solid fa-circle-exclamation"></i><?= e($error) ?></div>
            <div class="back-link" style="margin-top:20px;">
                <a href="<?= url('forgot-password.php') ?>"><i class="fa-solid fa-rotate-left"></i> Request a new link</a>
            </div>

        <?php else: ?>
            <div class="form-heading">
                <h2>New Password</h2>
                <p>Set a new password for <strong style="color:#d4f0df;"><?= e($user['username']) ?></strong>.</p>
            </div>

            <?php if ($error): ?>
            <div class="alert-err"><i class="fa-solid fa-circle-exclamation"></i><?= e($error) ?></div>
            <?php endif; ?>

            <form method="post" autocomplete="off">
                <?= csrf_field() ?>
                <input type="hidden" name="token" value="<?= e($token) ?>">

                <div class="field">
                    <label for="password">New Password</label>
                    <div class="field-inner">
                        <span class="field-icon"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" id="password" name="password"
                               placeholder="Max 8 chars, 1 uppercase" maxlength="8" required>
                        <button type="button" class="field-toggle" id="togglePw" aria-label="Show/hide password">
                            <i class="fa-regular fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                    <div class="field-hint">Max 8 characters · at least 1 uppercase letter</div>
                </div>

                <div class="field">
                    <label for="password_confirm">Confirm Password</label>
                    <div class="field-inner">
                        <span class="field-icon"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" id="password_confirm" name="password_confirm"
                               placeholder="Re-type password" maxlength="8" required>
                    </div>
                </div>

                <button class="btn-submit" type="submit">
                    <i class="fa-solid fa-check"></i> Set New Password
                </button>
            </form>

            <div class="back-link">
                <a href="<?= url('login.php') ?>"><i class="fa-solid fa-arrow-left"></i> Back to Login</a>
            </div>
        <?php endif; ?>
    </div>
</div>
<script>
    const pw  = document.getElementById('password');
    const btn = document.getElementById('togglePw');
    const ico = document.getElementById('eyeIcon');
    if (btn) btn.addEventListener('click', () => {
        const show = pw.type === 'password';
        pw.type = show ? 'text' : 'password';
        ico.className = show ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
    });
</script>
</body>
</html>
