<?php
require_once __DIR__ . '/includes/functions.php';
if (is_logged_in()) redirect('index.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $u = trim($_POST['username'] ?? '');
    $p = $_POST['password'] ?? '';
    $st = db()->prepare('SELECT * FROM users WHERE username = ?');
    $st->execute([$u]);
    $row = $st->fetch();
    if ($row && password_verify($p, $row['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id' => $row['id'], 'username' => $row['username'], 'full_name' => $row['full_name'], 'role' => $row['role']];
        db()->prepare('UPDATE users SET last_login = NOW() WHERE id = ?')->execute([$row['id']]);
        log_activity('Login');
        redirect('index.php');
    }
    log_activity('Failed login', null, null, 'username: ' . mb_substr($u, 0, 50));
    $error = 'Incorrect username or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log in | Moncada Agriculture Office</title>
    <meta name="description" content="Login to the Moncada, Tarlac Agricultural Records System. Authorized staff only.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --g900: #0d3d22;
            --g800: #14542f;
            --g700: #1a6b3a;
            --g600: #228048;
            --g500: #2a9a56;
            --gold: #c8991a;
            --tx: #1a2e22;
            --mu: #3d5c45;
            --border: rgba(255,255,255,0.35);
        }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            height: 100vh;
            overflow: hidden;
            margin: 0;
        }

        .login-page {
            width: 100%;
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            background-image: url('<?= url('assets/images/ricebg.jpg') ?>');
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
        }

        /* dark-green overlay for contrast */
        .login-page::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(
                to bottom,
                rgba(5, 30, 14, 0.45) 0%,
                rgba(5, 30, 14, 0.25) 50%,
                rgba(5, 30, 14, 0.55) 100%
            );
            pointer-events: none;
        }

        /* ── Card ── */
        .login-card {
            width: 100%;
            max-width: 440px;
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(20px) saturate(1.4);
            -webkit-backdrop-filter: blur(20px) saturate(1.4);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow:
                0 8px 40px rgba(0, 0, 0, 0.35),
                inset 0 1px 0 rgba(255,255,255,0.5);
            padding: 44px 40px 36px;
            margin: 0 16px;
            position: relative;
            z-index: 2;
            animation: slideUp 0.5s cubic-bezier(.22,.68,0,1.2) both;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Logo ── */
        .logo-wrap {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            margin-bottom: 18px;
        }

        .logo-circle {
            width: 96px;
            height: 96px;
            border-radius: 50%;
            background: rgba(255,255,255,0.92);
            border: 3px solid rgba(255,255,255,0.7);
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0,0,0,.3);
            flex-shrink: 0;
        }

        .logo-circle img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .logo-title { text-align: center; }

        .logo-title h1 {
            font-size: 1.55rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: -.02em;
            line-height: 1.15;
            text-shadow: 0 2px 8px rgba(0,0,0,.4);
        }

        .logo-subtitle {
            font-size: .9rem;
            font-weight: 700;
            color: #d4f0df;
            margin-top: 2px;
            text-shadow: 0 1px 4px rgba(0,0,0,.3);
        }

        .tagline {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0;
            font-size: .75rem;
            color: rgba(255,255,255,0.82);
            flex-wrap: wrap;
            margin-top: 5px;
            text-shadow: 0 1px 3px rgba(0,0,0,.3);
        }
        .tagline span { padding: 0 5px; }
        .tagline .dot { color: #a8dfc0; font-size: .55rem; line-height: 1.8; }

        hr.divider {
            border: none;
            border-top: 1px solid rgba(255,255,255,0.35);
            margin: 18px 0 22px;
        }

        .form-heading h2 {
            font-size: 1.5rem;
            font-weight: 800;
            color: #fff;
            margin-bottom: 4px;
            text-shadow: 0 2px 6px rgba(0,0,0,.3);
        }
        .form-heading p {
            font-size: .87rem;
            color: rgba(255,255,255,0.8);
        }

        .alert-err {
            background: #fdf0ef;
            border: 1px solid #f5c4c0;
            color: #8b2a22;
            border-radius: 10px;
            padding: 11px 14px;
            font-size: .86rem;
            margin-top: 14px;
            display: flex;
            gap: 8px;
            align-items: center;
            animation: shake .35s ease both;
        }
        @keyframes shake {
            0%,100%{ transform: translateX(0) }
            25%    { transform: translateX(-6px) }
            75%    { transform: translateX(6px) }
        }

        /* ── Inputs ── */
        .field { margin-top: 16px; }

        .field-inner {
            display: flex;
            align-items: center;
            border: 1.5px solid rgba(255,255,255,0.4);
            border-radius: 10px;
            background: rgba(255,255,255,0.2);
            transition: border-color .2s, box-shadow .2s, background .2s;
        }
        .field-inner:focus-within {
            border-color: rgba(255,255,255,0.8);
            box-shadow: 0 0 0 3px rgba(255,255,255,.15);
            background: rgba(255,255,255,0.28);
        }

        .field-icon {
            width: 44px;
            display: grid;
            place-items: center;
            color: rgba(255,255,255,0.8);
            font-size: .95rem;
            flex-shrink: 0;
        }

        .field-inner input {
            flex: 1;
            border: none;
            outline: none;
            background: transparent;
            font: inherit;
            font-size: .95rem;
            color: #fff;
            padding: 13px 0 13px 2px;
        }
        .field-inner input::placeholder { color: rgba(255,255,255,0.6); }

        .field-toggle {
            width: 44px;
            display: grid;
            place-items: center;
            background: none;
            border: none;
            cursor: pointer;
            color: rgba(255,255,255,0.75);
            font-size: .92rem;
            flex-shrink: 0;
            transition: color .15s;
        }
        .field-toggle:hover { color: #fff; }

        /* ── Login button ── */
        .btn-login {
            margin-top: 22px;
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--g700) 0%, var(--g800) 100%);
            color: #fff;
            border: none;
            border-radius: 10px;
            font: inherit;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,.35);
            transition: background .2s, transform .15s, box-shadow .2s;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, var(--g600) 0%, var(--g700) 100%);
            transform: translateY(-1px);
            box-shadow: 0 8px 28px rgba(0,0,0,.4);
        }
        .btn-login:active { transform: translateY(0); }
        .btn-login .arrow {
            background: rgba(255,255,255,.2);
            width: 26px; height: 26px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            font-size: .85rem;
        }

        .forgot {
            text-align: center;
            margin-top: 16px;
            font-size: .85rem;
        }
        .forgot a {
            color: rgba(255,255,255,0.85);
            text-decoration: none;
            font-weight: 500;
            transition: color .15s;
        }
        .forgot a:hover { color: #fff; text-decoration: underline; }

        /* ── Footer ── */
        .login-footer {
            width: 100%;
            max-width: 440px;
            text-align: center;
            padding: 20px 16px 0;
            z-index: 2;
        }
        .footer-leaf {
            font-size: 1.4rem;
            margin-bottom: 6px;
            display: block;
            animation: sway 3s ease-in-out infinite;
        }
        @keyframes sway {
            0%,100% { transform: rotate(-8deg); }
            50%      { transform: rotate(8deg); }
        }
        .footer-office {
            font-size: .85rem;
            font-weight: 700;
            color: #fff;
            text-shadow: 0 1px 4px rgba(0,0,0,.4);
        }
        .footer-location {
            font-size: .8rem;
            color: rgba(255,255,255,0.78);
            margin-top: 2px;
            text-shadow: 0 1px 3px rgba(0,0,0,.3);
        }
        .footer-tagline {
            font-size: .75rem;
            font-style: italic;
            color: rgba(255,255,255,0.68);
            margin-top: 6px;
            line-height: 1.6;
            text-shadow: 0 1px 3px rgba(0,0,0,.3);
        }

        @media (max-width: 480px) {
            .login-card { padding: 28px 20px 24px; }
            .logo-circle { width: 76px; height: 76px; }
            .logo-title h1 { font-size: 1.25rem; }
        }
    </style>
</head>
<body>
<div class="login-page">

    <div class="login-card">
        <!-- Logo + Branding -->
        <div class="logo-wrap">
            <div class="logo-circle">
                <img src="<?= url('assets/images/logo.jpg') ?>" alt="Moncada Agriculture Office Logo">
            </div>
            <div class="logo-title">
                <h1>Moncada, Tarlac</h1>
                <div class="logo-subtitle">Agricultural Records System</div>
                <div class="tagline">
                    <span>Manage Farmers</span>
                    <span class="dot">•</span>
                    <span>Support Agriculture</span>
                    <span class="dot">•</span>
                    <span>Build a Better Future</span>
                </div>
            </div>
        </div>

        <hr class="divider">

        <!-- Form heading -->
        <div class="form-heading">
            <h2>Login</h2>
            <p>Please enter your credentials to continue.</p>
        </div>

        <!-- Error alert -->
        <?php if ($error): ?>
        <div class="alert-err">
            <i class="fa-solid fa-circle-exclamation"></i>
            <?= e($error) ?>
        </div>
        <?php endif; ?>

        <!-- Login form -->
        <form method="post" autocomplete="off">
            <?= csrf_field() ?>

            <div class="field">
                <div class="field-inner">
                    <span class="field-icon"><i class="fa-regular fa-user"></i></span>
                    <input type="text" id="username" name="username"
                           value="<?= e($_POST['username'] ?? '') ?>"
                           placeholder="Username" required autofocus>
                </div>
            </div>

            <div class="field">
                <div class="field-inner">
                    <span class="field-icon"><i class="fa-solid fa-lock"></i></span>
                    <input type="password" id="password" name="password"
                           placeholder="Password" required>
                    <button type="button" class="field-toggle" id="togglePw" aria-label="Toggle password visibility">
                        <i class="fa-regular fa-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <button class="btn-login" type="submit">
                Login
                <span class="arrow"><i class="fa-solid fa-arrow-right"></i></span>
            </button>
        </form>

        <div class="forgot">
            <a href="#">Forgot password?</a>
        </div>
    </div>

    <!-- Footer -->
    <div class="login-footer">
        <span class="footer-leaf">🌿</span>
        <div class="footer-office">Municipal Agriculture Office</div>
        <div class="footer-location">Moncada, Tarlac</div>
        <div class="footer-tagline">Healthy Farmers, Productive Fields,<br>Prosperous Community</div>
    </div>

</div>

<script>
    const pw  = document.getElementById('password');
    const btn = document.getElementById('togglePw');
    const ico = document.getElementById('eyeIcon');
    btn.addEventListener('click', () => {
        const show = pw.type === 'password';
        pw.type = show ? 'text' : 'password';
        ico.className = show ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
    });
</script>
</body>
</html>
