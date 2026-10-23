<?php
/**
 * Custom Type Master Pro
 * SECURITY HONEYPOT / ACCESS LOCKOUT
 * Notice: Standard /admin has been relocated to /nickadmin005#
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/auth.php';

http_response_code(403);
header('X-Security-Policy: Access-Terminated-Admin-Relocated');

$ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
$time = date('Y-m-d H:i:s T');
$refId = 'SEC-' . strtoupper(substr(md5($ip . microtime()), 0, 8));
$isAuthorizedAdmin = is_admin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title>403 Forbidden — Administrative Gateway Relocated</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    :root {
      --bg: #07090e;
      --card-bg: rgba(18, 22, 34, 0.85);
      --border: rgba(244, 63, 94, 0.3);
      --danger: #f43f5e;
      --danger-glow: rgba(244, 63, 94, 0.15);
      --text: #f1f5f9;
      --muted: #94a3b8;
      --mono: 'JetBrains Mono', monospace;
      --sans: 'Inter', sans-serif;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      background: var(--bg);
      color: var(--text);
      font-family: var(--sans);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
      position: relative;
      overflow-x: hidden;
    }
    body::before {
      content: '';
      position: absolute;
      width: 500px;
      height: 500px;
      background: radial-gradient(circle, rgba(244, 63, 94, 0.12) 0%, transparent 70%);
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      pointer-events: none;
    }
    .lock-card {
      position: relative;
      background: var(--card-bg);
      border: 1px solid var(--border);
      box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6), 0 0 40px var(--danger-glow);
      border-radius: 16px;
      padding: 42px 36px;
      max-width: 560px;
      width: 100%;
      backdrop-filter: blur(16px);
      text-align: center;
      animation: fadeIn 0.4s ease-out;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(12px) scale(0.98); }
      to { opacity: 1; transform: translateY(0) scale(1); }
    }
    .shield-badge {
      width: 80px;
      height: 80px;
      margin: 0 auto 24px;
      background: rgba(244, 63, 94, 0.12);
      border: 1px solid rgba(244, 63, 94, 0.4);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--danger);
      font-size: 38px;
      box-shadow: 0 0 25px rgba(244, 63, 94, 0.25);
    }
    .sec-code {
      display: inline-block;
      font-family: var(--mono);
      font-size: 0.8rem;
      font-weight: 700;
      letter-spacing: 0.15em;
      text-transform: uppercase;
      color: var(--danger);
      background: rgba(244, 63, 94, 0.1);
      border: 1px solid rgba(244, 63, 94, 0.25);
      padding: 5px 14px;
      border-radius: 99px;
      margin-bottom: 16px;
    }
    h1 {
      font-size: 1.65rem;
      font-weight: 800;
      color: #fff;
      margin-bottom: 12px;
      letter-spacing: -0.02em;
    }
    p {
      color: var(--muted);
      font-size: 0.92rem;
      line-height: 1.6;
      margin-bottom: 24px;
    }
    .meta-box {
      background: rgba(0, 0, 0, 0.4);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 10px;
      padding: 14px 18px;
      font-family: var(--mono);
      font-size: 0.78rem;
      text-align: left;
      margin-bottom: 28px;
    }
    .meta-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 6px;
      color: #cbd5e1;
    }
    .meta-row:last-child { margin-bottom: 0; }
    .meta-label { color: #64748b; }
    .btn-group {
      display: flex;
      gap: 12px;
      justify-content: center;
      flex-wrap: wrap;
    }
    .btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 10px 22px;
      border-radius: 8px;
      font-size: 0.88rem;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.2s ease;
      cursor: pointer;
    }
    .btn-primary {
      background: #2563eb;
      color: #fff;
      border: 1px solid #3b82f6;
    }
    .btn-primary:hover { background: #1d4ed8; }
    .btn-secure {
      background: linear-gradient(135deg, #10b981 0%, #059669 100%);
      color: #fff;
      border: 1px solid #34d399;
      box-shadow: 0 0 20px rgba(16, 185, 129, 0.3);
    }
    .btn-secure:hover {
      box-shadow: 0 0 30px rgba(16, 185, 129, 0.5);
      transform: translateY(-1px);
    }
    .btn-outline {
      background: transparent;
      color: var(--muted);
      border: 1px solid rgba(255, 255, 255, 0.15);
    }
    .btn-outline:hover { color: #fff; border-color: rgba(255, 255, 255, 0.3); }
  </style>
</head>
<body>

<div class="lock-card">
  <div class="shield-badge">
    <i class="bi bi-shield-slash-fill"></i>
  </div>

  <div class="sec-code">HTTP 403 &bull; Access Forbidden</div>
  <h1>Administrative Gateway Relocated</h1>
  <p>The standard <code>/admin</code> route has been permanently locked down for platform security. Direct access to this namespace is strictly prohibited and logged.</p>

  <div class="meta-box">
    <div class="meta-row">
      <span class="meta-label">Incident Ref:</span>
      <span><?= e($refId) ?></span>
    </div>
    <div class="meta-row">
      <span class="meta-label">Target Route:</span>
      <span>/admin</span>
    </div>
    <div class="meta-row">
      <span class="meta-label">Client IP:</span>
      <span><?= e($ip) ?></span>
    </div>
    <div class="meta-row">
      <span class="meta-label">Timestamp:</span>
      <span><?= e($time) ?></span>
    </div>
  </div>

  <div class="btn-group">
    <a href="<?= BASE_URL ?>/dashboard.php" class="btn btn-primary">
      <i class="bi bi-speedometer2"></i> User Dashboard
    </a>
    <?php if ($isAuthorizedAdmin): ?>
      <a href="<?= ADMIN_URL ?>/index.php" class="btn btn-secure">
        <i class="bi bi-key-fill"></i> Enter Secure Admin Portal (/nickadmin005#)
      </a>
    <?php else: ?>
      <a href="<?= BASE_URL ?>/login.php" class="btn btn-outline">
        <i class="bi bi-box-arrow-in-right"></i> Sign In
      </a>
    <?php endif; ?>
  </div>
</div>

</body>
</html>
