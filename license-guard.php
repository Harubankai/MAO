<?php
// license-guard.php

require_once __DIR__ . '/license-config.php';
require_once __DIR__ . '/RoldLicenseClient.php';

use RoldAdmin\Client\RoldLicenseClient;

$publicKeyPath = __DIR__ . '/config/public_key.pem';

if (!is_file($publicKeyPath)) {
    http_response_code(503);
    exit('License verification unavailable: public key missing.');
}

try {
    $licenseClient = new RoldLicenseClient(
        LICENSE_SERVER_URL,
        LICENSE_KEY,
        $publicKeyPath,
        __DIR__ . '/.license_cache',
        300
    );

    $result = $licenseClient->validate(true);

    if (!($result['valid'] ?? false)) {
        http_response_code(403);
        $status = strtoupper((string)($result['status'] ?? 'SUSPENDED'));
        $msg = (string)($result['message'] ?? 'Your software license has been suspended by the administrator.');
        $clientName = (string)($result['data']['client_name'] ?? 'Authorized Client');
        $licenseId = (string)($result['data']['license_id'] ?? '');
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>License <?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?> | Moncada Agri</title>
            <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
            <style>
                * { box-sizing: border-box; margin: 0; padding: 0; }
                body {
                    font-family: 'Inter', system-ui, sans-serif;
                    background: #0d2919;
                    min-height: 100vh;
                    display: grid;
                    place-items: center;
                    padding: 20px;
                    color: #fff;
                }
                .box {
                    max-width: 480px;
                    width: 100%;
                    background: rgba(255,255,255,0.06);
                    border: 1px solid rgba(255,255,255,0.15);
                    border-radius: 16px;
                    padding: 36px 30px;
                    text-align: center;
                    backdrop-filter: blur(16px);
                    box-shadow: 0 20px 40px rgba(0,0,0,0.4);
                }
                .icon {
                    width: 64px;
                    height: 64px;
                    border-radius: 50%;
                    background: rgba(239, 68, 68, 0.15);
                    color: #f87171;
                    display: grid;
                    place-items: center;
                    margin: 0 auto 18px;
                    font-size: 28px;
                    border: 1.5px solid rgba(239, 68, 68, 0.35);
                }
                h1 { font-size: 1.4rem; margin-bottom: 8px; font-weight: 800; }
                .badge {
                    display: inline-block;
                    padding: 4px 12px;
                    border-radius: 99px;
                    font-size: 0.78rem;
                    font-weight: 700;
                    letter-spacing: .05em;
                    background: #f87171;
                    color: #450a0a;
                    margin-bottom: 16px;
                }
                p { font-size: 0.92rem; color: #cbd5e1; line-height: 1.5; margin-bottom: 20px; }
                .details {
                    background: rgba(0,0,0,0.25);
                    border-radius: 10px;
                    padding: 14px;
                    font-size: 0.82rem;
                    color: #94a3b8;
                    text-align: left;
                    margin-bottom: 22px;
                }
                .details div { display: flex; justify-content: space-between; margin-bottom: 6px; }
                .details div:last-child { margin-bottom: 0; }
                .details strong { color: #f1f5f9; }
                .contact { font-size: 0.8rem; color: #94a3b8; }
            </style>
        </head>
        <body>
            <div class="box">
                <div class="icon">&#9888;</div>
                <span class="badge"><?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?></span>
                <h1>Access Restricted</h1>
                <p><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></p>
                <?php if ($licenseId || $clientName): ?>
                <div class="details">
                    <?php if ($clientName): ?><div><span>Licensed To:</span><strong><?= htmlspecialchars($clientName, ENT_QUOTES, 'UTF-8') ?></strong></div><?php endif; ?>
                    <?php if ($licenseId): ?><div><span>License ID:</span><strong><?= htmlspecialchars($licenseId, ENT_QUOTES, 'UTF-8') ?></strong></div><?php endif; ?>
                    <div><span>Server Status:</span><strong style="color:#f87171;"><?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?></strong></div>
                </div>
                <?php endif; ?>
                <div class="contact">Please contact the system administrator to reactivate this license.</div>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
} catch (\Throwable $e) {
    error_log('HMSYSTEM license check failed: ' . $e->getMessage());
    http_response_code(503);
    exit('License server unavailable. Please try again later.');
}
