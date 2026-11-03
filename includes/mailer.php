<?php
/**
 * Mailer helper using PHPMailer + Gmail SMTP.
 * Gmail App Password: ppzi zkxn xpep ikyi
 */
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

function send_reset_email(string $toEmail, string $toName, string $resetLink): bool {
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'akatsukiharold25@gmail.com';
        $mail->Password   = 'ppzizkxnxpepikyi'; // App Password (spaces removed)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('akatsukiharold25@gmail.com', 'Moncada Agriculture Office');
        $mail->addAddress($toEmail, $toName);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset - Moncada Agricultural Records System';
        $mail->Body    = '
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#f4f7f4;font-family:Inter,Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f7f4;padding:32px 16px;">
    <tr><td align="center">
      <table width="480" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08);">
        <tr>
          <td style="background:linear-gradient(135deg,#1a6b3a,#0d3d22);padding:28px 32px;text-align:center;">
            <div style="font-size:2rem;">🌿</div>
            <h1 style="color:#fff;margin:8px 0 4px;font-size:1.4rem;font-weight:800;">Moncada Agriculture Office</h1>
            <p style="color:#a8dfc0;margin:0;font-size:.85rem;">Municipal Agricultural Records System</p>
          </td>
        </tr>
        <tr>
          <td style="padding:32px;">
            <h2 style="color:#0d3d22;margin:0 0 12px;font-size:1.2rem;">Password Reset Request</h2>
            <p style="color:#444;line-height:1.7;margin:0 0 20px;">
              Hello <strong>' . htmlspecialchars($toName) . '</strong>,<br><br>
              We received a request to reset your password. Click the button below to set a new password.
              This link will expire in <strong>1 hour</strong>.
            </p>
            <div style="text-align:center;margin:24px 0;">
              <a href="' . htmlspecialchars($resetLink) . '"
                 style="display:inline-block;background:linear-gradient(135deg,#1a6b3a,#0d3d22);color:#fff;
                        padding:14px 32px;border-radius:8px;text-decoration:none;font-weight:700;font-size:1rem;
                        letter-spacing:.02em;">
                Reset My Password
              </a>
            </div>
            <p style="color:#777;font-size:.82rem;line-height:1.6;margin:0;">
              If you did not request a password reset, you can safely ignore this email — your password will remain unchanged.<br><br>
              Or copy and paste this URL into your browser:<br>
              <a href="' . htmlspecialchars($resetLink) . '" style="color:#1a6b3a;word-break:break-all;">' . htmlspecialchars($resetLink) . '</a>
            </p>
          </td>
        </tr>
        <tr>
          <td style="background:#f4f7f4;padding:16px 32px;text-align:center;border-top:1px solid #e8f0e8;">
            <p style="color:#aaa;font-size:.75rem;margin:0;">
              Moncada, Tarlac · Municipal Agriculture Office<br>
              <em>Healthy Farmers, Productive Fields, Prosperous Community</em>
            </p>
          </td>
        </tr>
      </table>
    </td></tr>
  </table>
</body>
</html>';
        $mail->AltBody = "Password Reset\n\nClick the link below to reset your password (expires in 1 hour):\n{$resetLink}\n\nIf you did not request this, ignore this email.";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Mailer error: ' . $mail->ErrorInfo);
        return false;
    }
}

/**
 * Send a 6-digit OTP code email for password reset.
 */
function send_otp_email(string $toEmail, string $toName, string $otp): bool {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'akatsukiharold25@gmail.com';
        $mail->Password   = 'ppzizkxnxpepikyi';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('akatsukiharold25@gmail.com', 'Moncada Agriculture Office');
        $mail->addAddress($toEmail, $toName);

        $mail->isHTML(true);
        $mail->Subject = 'Your Password Reset Code - Moncada Agricultural Records System';
        $mail->Body    = '
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#f4f7f4;font-family:Inter,Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f7f4;padding:32px 16px;">
    <tr><td align="center">
      <table width="480" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08);">
        <tr>
          <td style="background:linear-gradient(135deg,#1a6b3a,#0d3d22);padding:28px 32px;text-align:center;">
            <div style="font-size:2rem;">🌿</div>
            <h1 style="color:#fff;margin:8px 0 4px;font-size:1.4rem;font-weight:800;">Password Reset Code</h1>
            <p style="color:#a8dfc0;margin:0;font-size:.85rem;">Moncada Agricultural Records System</p>
          </td>
        </tr>
        <tr>
          <td style="padding:32px;text-align:center;">
            <p style="color:#444;font-size:.95rem;margin:0 0 24px;">
              Hello <strong>' . htmlspecialchars($toName) . '</strong>, use the code below to reset your password.
            </p>

            <!-- Big OTP code -->
            <div style="background:#f0faf4;border:2px dashed #2a9a56;border-radius:12px;padding:24px 32px;margin:0 auto 24px;display:inline-block;">
              <div style="font-size:3rem;font-weight:900;letter-spacing:.4em;color:#0d3d22;font-family:monospace;">' . htmlspecialchars($otp) . '</div>
            </div>

            <p style="color:#777;font-size:.82rem;line-height:1.6;margin:0;">
              ⏱ This code expires in <strong>15 minutes</strong>.<br>
              If you did not request this, please ignore this email.
            </p>
          </td>
        </tr>
        <tr>
          <td style="background:#f4f7f4;padding:16px 32px;text-align:center;border-top:1px solid #e8f0e8;">
            <p style="color:#aaa;font-size:.75rem;margin:0;">
              Moncada, Tarlac · Municipal Agriculture Office<br>
              <em>Healthy Farmers, Productive Fields, Prosperous Community</em>
            </p>
          </td>
        </tr>
      </table>
    </td></tr>
  </table>
</body>
</html>';
        $mail->AltBody = "Password Reset Code\n\nHello {$toName},\n\nYour 6-digit reset code is: {$otp}\n\nThis code expires in 15 minutes.\n\nIf you did not request this, please ignore this email.";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Mailer OTP error: ' . $mail->ErrorInfo);
        return false;
    }
}
