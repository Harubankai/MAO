<?php
require_once __DIR__ . '/includes/functions.php';
if (is_logged_in()) redirect('index.php');

// Handle ?restart=1 — clear session and reload to Step 1 (placed before any HTML output)
if (isset($_GET['restart'])) {
    unset($_SESSION['pr_otp']);
    redirect('forgot-password.php');
}

$step  = 'email';   // 'email' | 'code' | 'password' | 'success'
$error = '';

// Check existing session state
if (!empty($_SESSION['pr_otp'])) {
    if (time() > ($_SESSION['pr_otp']['expires'] ?? 0)) {
        unset($_SESSION['pr_otp']);
        $error = 'Your code has expired. Please enter your Gmail to request a new code.';
        $step  = 'email';
    } elseif (!empty($_SESSION['pr_otp']['verified'])) {
        $step = 'password';
    } else {
        $step = 'code';
    }
}

// ── STEP 3: Set New Password ───────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['step'] ?? '') === 'password') {
    csrf_check();
    $sess = $_SESSION['pr_otp'] ?? null;

    if (!$sess || empty($sess['verified']) || time() > ($sess['expires'] ?? 0)) {
        unset($_SESSION['pr_otp']);
        $error = 'Your session has expired. Please start over.';
        $step  = 'email';
    } else {
        $pass    = $_POST['password'] ?? '';
        $confirm = $_POST['password_confirm'] ?? '';

        if ($pass === '') {
            $error = 'Please enter a new password.';
            $step  = 'password';
        } elseif (strlen($pass) > 8) {
            $error = 'Password must be at most 8 characters.';
            $step  = 'password';
        } elseif (!preg_match('/[A-Z]/', $pass)) {
            $error = 'Password must contain at least 1 uppercase letter.';
            $step  = 'password';
        } elseif ($pass !== $confirm) {
            $error = 'Passwords do not match.';
            $step  = 'password';
        } else {
            // Update user password
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            db()->prepare('UPDATE users SET password_hash = ? WHERE id = ?')
               ->execute([$hash, $sess['user_id']]);

            // Invalidate password reset records
            db()->prepare('UPDATE password_resets SET used = 1 WHERE user_id = ?')
               ->execute([$sess['user_id']]);

            log_activity('Password reset completed via OTP', 'users', $sess['user_id']);
            unset($_SESSION['pr_otp']);
            $step = 'success';
        }
    }
}

// ── STEP 2: Verify the OTP code ────────────────────────────────────────────
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['step'] ?? '') === 'code') {
    csrf_check();
    $otp  = trim($_POST['otp'] ?? '');
    $sess = $_SESSION['pr_otp'] ?? null;

    if (!$sess || time() > ($sess['expires'] ?? 0)) {
        unset($_SESSION['pr_otp']);
        $error = 'Your code has expired. Please enter your Gmail to get a new code.';
        $step  = 'email';
    } elseif ($otp === '') {
        $error = 'Please enter the 6-digit code.';
        $step  = 'code';
    } elseif (!hash_equals((string)$sess['code'], (string)$otp)) {
        $sess['attempts'] = ($sess['attempts'] ?? 0) + 1;
        $_SESSION['pr_otp'] = $sess;

        if ($sess['attempts'] >= 5) {
            unset($_SESSION['pr_otp']);
            $error = 'Too many wrong attempts. Please start over.';
            $step  = 'email';
        } else {
            $remaining = 5 - $sess['attempts'];
            $error = 'Incorrect code. ' . $remaining . ' attempt' . ($remaining === 1 ? '' : 's') . ' remaining.';
            $step  = 'code';
        }
    } else {
        // Code is correct! Mark verified and move to password step
        $_SESSION['pr_otp']['verified'] = true;
        // Extend session by 15 mins for password entry
        $_SESSION['pr_otp']['expires']  = time() + 900;
        $step = 'password';
    }
}

// ── STEP 1: Send OTP to Gmail ───────────────────────────────────────────────
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['step'] ?? '') === 'email') {
    csrf_check();
    $email = trim($_POST['email'] ?? '');

    if ($email === '') {
        $error = 'Please enter your Gmail address.';
        $step  = 'email';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
        $step  = 'email';
    } else {
        $st = db()->prepare('SELECT id, full_name, email FROM users WHERE email = ?');
        $st->execute([$email]);
        $user = $st->fetch();

        if ($user) {
            // Generate 6-digit OTP + a DB token
            $otp   = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $token = bin2hex(random_bytes(32));

            // Invalidate old tokens
            db()->prepare('UPDATE password_resets SET used = 1 WHERE user_id = ?')->execute([$user['id']]);

            // Save reset record in DB with 15 minutes validity
            db()->prepare('INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 15 MINUTE))')
               ->execute([$user['id'], $token]);

            // Send OTP email
            require_once __DIR__ . '/includes/mailer.php';
            $ok = send_otp_email($user['email'], $user['full_name'], $otp);

            if ($ok) {
                // Store OTP state in session (15 minutes)
                $_SESSION['pr_otp'] = [
                    'user_id'  => (int)$user['id'],
                    'code'     => $otp,
                    'token'    => $token,
                    'email'    => $user['email'],
                    'expires'  => time() + 900,
                    'attempts' => 0,
                    'verified' => false,
                ];
                log_activity('Password reset OTP sent', 'users', $user['id']);
                $step = 'code';
            } else {
                $error = 'Could not send the email. Please contact your administrator or try again later.';
                $step  = 'email';
            }
        } else {
            // Inform the user clearly that this email is not associated with an account
            $error = 'No account found with this Gmail address. Please check your spelling or contact the administrator.';
            $step  = 'email';
        }
    }
}

// Prepare masked email for display in Step 2
$maskedEmail = '';
if (!empty($_SESSION['pr_otp']['email'])) {
    $e  = $_SESSION['pr_otp']['email'];
    $at = strpos($e, '@');
    if ($at !== false && $at > 2) {
        $maskedEmail = substr($e, 0, 2) . str_repeat('*', max(1, $at - 3)) . substr($e, $at - 1);
    } else {
        $maskedEmail = $e;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password | Moncada Agriculture Office</title>
    <meta name="description" content="Reset your password for the Moncada Agricultural Records System.">
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
            align-items: center; justify-content: center; position: relative;
            background-image: url('<?= url('assets/images/ricebg.jpg') ?>');
            background-size: cover; background-position: center;
            background-attachment: fixed; padding: 32px 16px;
        }
        .login-page::before {
            content: ''; position: fixed; inset: 0;
            background: linear-gradient(to bottom,rgba(5,30,14,.50) 0%,rgba(5,30,14,.30) 50%,rgba(5,30,14,.60) 100%);
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
        .logo-wrap { display: flex; flex-direction: column; align-items: center; gap: 10px; margin-bottom: 16px; }
        .logo-circle {
            width: 76px; height: 76px; border-radius: 50%;
            background: rgba(255,255,255,.92); border: 3px solid rgba(255,255,255,.7);
            overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,.3);
        }
        .logo-circle img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .logo-title { text-align: center; }
        .logo-title h1 { font-size: 1.35rem; font-weight: 800; color: #fff; letter-spacing: -.02em; text-shadow: 0 2px 8px rgba(0,0,0,.4); }
        .logo-subtitle { font-size: .82rem; font-weight: 700; color: #d4f0df; margin-top: 2px; }

        hr.divider { border: none; border-top: 1px solid rgba(255,255,255,.35); margin: 16px 0 20px; }

        /* Step indicator */
        .steps { display: flex; align-items: center; justify-content: center; gap: 0; margin-bottom: 20px; }
        .step-dot {
            width: 28px; height: 28px; border-radius: 50%;
            display: grid; place-items: center; font-size: .75rem; font-weight: 700;
            border: 2px solid rgba(255,255,255,.4); color: rgba(255,255,255,.5);
            transition: all .3s;
        }
        .step-dot.active { background: #fff; color: var(--g800); border-color: #fff; box-shadow: 0 0 12px rgba(255,255,255,.5); }
        .step-dot.done   { background: var(--g500); color: #fff; border-color: var(--g500); }
        .step-line { flex: 1; height: 2px; background: rgba(255,255,255,.25); max-width: 50px; }
        .step-line.done { background: var(--g500); }

        .form-heading { margin-bottom: 4px; }
        .form-heading h2 { font-size: 1.3rem; font-weight: 800; color: #fff; margin-bottom: 3px; text-shadow: 0 2px 6px rgba(0,0,0,.3); }
        .form-heading p  { font-size: .85rem; color: rgba(255,255,255,.8); line-height: 1.5; }
        .form-heading p strong { color: #d4f0df; }

        .alert-err, .alert-ok {
            border-radius: 10px; padding: 11px 14px; font-size: .84rem;
            margin-top: 12px; display: flex; gap: 8px; align-items: flex-start;
        }
        .alert-err { background: #fdf0ef; border: 1px solid #f5c4c0; color: #8b2a22; animation: shake .35s ease both; }
        .alert-ok  { background: #eef8f1; border: 1px solid #b2dfc0; color: #1a5a2e; }
        @keyframes shake { 0%,100%{transform:translateX(0)} 25%{transform:translateX(-6px)} 75%{transform:translateX(6px)} }

        .field { margin-top: 14px; }
        .field label { display: block; font-size: .82rem; font-weight: 600; color: rgba(255,255,255,.9); margin-bottom: 6px; }
        .field-hint { font-size: .74rem; color: rgba(255,255,255,.75); margin-top: 4px; }
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
            padding: 13px 4px 13px 2px;
        }
        .field-inner input::placeholder { color: rgba(255,255,255,.55); }

        /* OTP input — large centered digits */
        .otp-input {
            text-align: center !important;
            font-size: 1.85rem !important;
            font-weight: 800 !important;
            letter-spacing: .35em !important;
            padding: 14px 8px !important;
        }

        .btn-submit {
            margin-top: 20px; width: 100%; padding: 14px;
            background: linear-gradient(135deg, var(--g700) 0%, var(--g800) 100%);
            color: #fff; border: none; border-radius: 10px;
            font: inherit; font-size: 1rem; font-weight: 700; cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,.35);
            transition: background .2s, transform .15s, box-shadow .2s;
        }
        .btn-submit:hover:not(:disabled) { background: linear-gradient(135deg, var(--g600) 0%, var(--g700) 100%); transform: translateY(-1px); box-shadow: 0 8px 28px rgba(0,0,0,.4); }
        .btn-submit:disabled { opacity: .7; cursor: not-allowed; }

        .resend-row { text-align: center; margin-top: 14px; font-size: .82rem; color: rgba(255,255,255,.75); }
        .resend-row a { color: #d4f0df; font-weight: 600; text-decoration: none; }
        .resend-row a:hover { text-decoration: underline; }

        .back-link { text-align: center; margin-top: 16px; font-size: .84rem; }
        .back-link a { color: rgba(255,255,255,.85); text-decoration: none; font-weight: 500; transition: color .15s; }
        .back-link a:hover { color: #fff; text-decoration: underline; }

        #countdown { font-weight: 700; color: #d4f0df; }
    </style>
</head>
<body>
<div class="login-page">
    <div class="login-card">

        <div class="logo-wrap">
            <div class="logo-circle">
                <img src="<?= url('assets/images/logo.jpg') ?>?v=<?= @filemtime(__DIR__ . '/assets/images/logo.jpg') ?>" alt="Logo">
            </div>
            <div class="logo-title">
                <h1>Forgot Password</h1>
                <div class="logo-subtitle">Agricultural Records System</div>
            </div>
        </div>

        <hr class="divider">

        <!-- 3-Step Indicator -->
        <div class="steps">
            <div class="step-dot <?= in_array($step, ['code', 'password', 'success']) ? 'done' : 'active' ?>">
                <?= in_array($step, ['code', 'password', 'success']) ? '<i class="fa-solid fa-check" style="font-size:.65rem"></i>' : '1' ?>
            </div>
            <div class="step-line <?= in_array($step, ['code', 'password', 'success']) ? 'done' : '' ?>"></div>
            <div class="step-dot <?= in_array($step, ['password', 'success']) ? 'done' : ($step === 'code' ? 'active' : '') ?>">
                <?= in_array($step, ['password', 'success']) ? '<i class="fa-solid fa-check" style="font-size:.65rem"></i>' : '2' ?>
            </div>
            <div class="step-line <?= in_array($step, ['password', 'success']) ? 'done' : '' ?>"></div>
            <div class="step-dot <?= $step === 'success' ? 'done' : ($step === 'password' ? 'active' : '') ?>">
                <?= $step === 'success' ? '<i class="fa-solid fa-check" style="font-size:.65rem"></i>' : '3' ?>
            </div>
        </div>

        <?php if ($step === 'email'): ?>
        <!-- ── STEP 1: Email ─────────────────────────────── -->
        <div class="form-heading">
            <h2>Enter Gmail</h2>
            <p>We'll send a <strong>6-digit verification code</strong> to your registered Gmail address.</p>
        </div>

        <?php if ($error): ?>
        <div class="alert-err"><i class="fa-solid fa-circle-exclamation"></i><?= e($error) ?></div>
        <?php endif; ?>

        <form method="post" autocomplete="off" id="formEmail">
            <?= csrf_field() ?>
            <input type="hidden" name="step" value="email">
            <div class="field">
                <label for="email">Gmail Address</label>
                <div class="field-inner">
                    <span class="field-icon"><i class="fa-regular fa-envelope"></i></span>
                    <input type="email" id="email" name="email"
                           value="<?= e($_POST['email'] ?? '') ?>"
                           placeholder="your@gmail.com" required autofocus>
                </div>
            </div>
            <button class="btn-submit" type="submit" id="btnSend">
                <i class="fa-solid fa-paper-plane"></i> Send Code
            </button>
        </form>

        <?php elseif ($step === 'code'): ?>
        <!-- ── STEP 2: OTP Code ──────────────────────────── -->
        <div class="form-heading">
            <h2>Enter the Code</h2>
            <p>A 6-digit code was sent to <strong><?= e($maskedEmail) ?></strong>. Check your Gmail inbox.</p>
        </div>

        <?php if ($error): ?>
        <div class="alert-err"><i class="fa-solid fa-circle-exclamation"></i><?= e($error) ?></div>
        <?php endif; ?>

        <form method="post" autocomplete="off" id="formCode">
            <?= csrf_field() ?>
            <input type="hidden" name="step" value="code">
            <div class="field">
                <label for="otp">6-Digit Code</label>
                <div class="field-inner">
                    <span class="field-icon"><i class="fa-solid fa-key"></i></span>
                    <input type="text" id="otp" name="otp"
                           class="otp-input"
                           placeholder="000000"
                           maxlength="6" inputmode="numeric" pattern="\d{6}"
                           autocomplete="one-time-code" required autofocus>
                </div>
            </div>
            <button class="btn-submit" type="submit" id="btnVerify">
                <i class="fa-solid fa-shield-halved"></i> Verify Code
            </button>
        </form>

        <div class="resend-row">
            Code expires in <span id="countdown">--:--</span> &nbsp;·&nbsp;
            <a href="<?= url('forgot-password.php') ?>?restart=1">Change email / start over</a>
        </div>

        <?php elseif ($step === 'password'): ?>
        <!-- ── STEP 3: Set New Password ──────────────────── -->
        <div class="form-heading">
            <h2>Create New Password</h2>
            <p>Code verified! Please choose a new password.</p>
        </div>

        <?php if ($error): ?>
        <div class="alert-err"><i class="fa-solid fa-circle-exclamation"></i><?= e($error) ?></div>
        <?php endif; ?>

        <form method="post" autocomplete="off" id="formPassword">
            <?= csrf_field() ?>
            <input type="hidden" name="step" value="password">

            <div class="field">
                <label for="password">New Password</label>
                <div class="field-inner">
                    <span class="field-icon"><i class="fa-solid fa-lock"></i></span>
                    <input type="password" id="password" name="password"
                           placeholder="Max 8 chars, 1 uppercase" maxlength="8" required autofocus>
                    <button type="button" class="field-toggle" id="togglePw1" aria-label="Toggle password">
                        <i class="fa-regular fa-eye" id="eye1"></i>
                    </button>
                </div>
                <div class="field-hint">Max 8 characters · at least 1 uppercase letter</div>
            </div>

            <div class="field">
                <label for="password_confirm">Confirm Password</label>
                <div class="field-inner">
                    <span class="field-icon"><i class="fa-solid fa-lock"></i></span>
                    <input type="password" id="password_confirm" name="password_confirm"
                           placeholder="Re-enter password" maxlength="8" required>
                    <button type="button" class="field-toggle" id="togglePw2" aria-label="Toggle confirm password">
                        <i class="fa-regular fa-eye" id="eye2"></i>
                    </button>
                </div>
            </div>

            <button class="btn-submit" type="submit" id="btnSetPw">
                <i class="fa-solid fa-check"></i> Set New Password
            </button>
        </form>

        <div class="resend-row">
            <a href="<?= url('forgot-password.php') ?>?restart=1">Cancel / start over</a>
        </div>

        <?php elseif ($step === 'success'): ?>
        <!-- ── STEP 4: Success Confirmation ──────────────── -->
        <div style="text-align:center; padding:10px 0;">
          
            <div class="form-heading">
                <h2>Password Changed!</h2>
                <p>Your password has been reset successfully. You can now log in with your new password.</p>
            </div>
            <a href="<?= url('login.php') ?>" class="btn-submit" style="text-decoration:none; margin-top:24px;">
                <i class="fa-solid fa-arrow-right-to-bracket"></i> Go to Login
            </a>
        </div>
        <?php endif; ?>

        <?php if ($step !== 'success'): ?>
        <div class="back-link">
            <a href="<?= url('login.php') ?>"><i class="fa-solid fa-arrow-left"></i> Back to Login</a>
        </div>
        <?php endif; ?>

    </div>
</div>

<script>
// Prevent double submission and show loading spinner on forms
function attachSubmitGuard(formId, btnId, loadingText) {
    const form = document.getElementById(formId);
    const btn  = document.getElementById(btnId);
    if (!form || !btn) return;

    let submitted = false;
    form.addEventListener('submit', (e) => {
        if (submitted) {
            e.preventDefault();
            return false;
        }
        submitted = true;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> ' + loadingText;
    });
}

attachSubmitGuard('formEmail', 'btnSend', 'Sending code…');
attachSubmitGuard('formCode', 'btnVerify', 'Verifying…');
attachSubmitGuard('formPassword', 'btnSetPw', 'Saving password…');

// OTP formatting: digits only
const otpEl = document.getElementById('otp');
if (otpEl) {
    otpEl.addEventListener('input', () => {
        otpEl.value = otpEl.value.replace(/\D/g, '').slice(0, 6);
    });
}

// Password eye toggles
function setupToggle(btnId, inputId, eyeId) {
    const btn = document.getElementById(btnId);
    const inp = document.getElementById(inputId);
    const eye = document.getElementById(eyeId);
    if (!btn || !inp || !eye) return;

    btn.addEventListener('click', () => {
        const isPw = inp.type === 'password';
        inp.type = isPw ? 'text' : 'password';
        eye.className = isPw ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
    });
}
setupToggle('togglePw1', 'password', 'eye1');
setupToggle('togglePw2', 'password_confirm', 'eye2');

// Countdown timer (reads from server expiry)
<?php if ($step === 'code' && !empty($_SESSION['pr_otp']['expires'])): ?>
const expiresAt = <?= (int)$_SESSION['pr_otp']['expires'] ?> * 1000;
function updateCountdown() {
    const remaining = Math.max(0, Math.round((expiresAt - Date.now()) / 1000));
    const m = String(Math.floor(remaining / 60)).padStart(2, '0');
    const s = String(remaining % 60).padStart(2, '0');
    const el = document.getElementById('countdown');
    if (el) {
        if (remaining > 0) {
            el.textContent = m + ':' + s;
            setTimeout(updateCountdown, 1000);
        } else {
            el.textContent = 'expired';
            const btn = document.getElementById('btnVerify');
            if (btn) btn.disabled = true;
        }
    }
}
updateCountdown();
<?php endif; ?>
</script>
</body>
</html>
