 <?php
/**
 * Custom Type Master Pro
 * Global Header Template — Pixel Theme / Sans-Serif Readable
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/functions.php';

$currentUser = current_user();
$userSettings = $currentUser ? get_user_settings($currentUser['id']) : [
    'theme'               => 'light',
    'font_family'         => 'JetBrains Mono',
    'font_size'           => 22,
    'line_height'         => 1.8,
    'caret_style'         => 'line',
    'caret_color'         => '#ff5532',
    'correct_color'       => '#10b981',
    'incorrect_color'     => '#f43f5e',
    'typing_area_width'   => '920px',
    'sound_enabled'       => 1,
    'sound_theme'         => 'mechanical',
    'default_duration'    => 60,
    'default_difficulty'  => 'intermediate',
    'show_live_wpm'       => 1,
    'show_live_accuracy'  => 1,
    'show_live_word_count'=> 1
];

$activeTheme = $userSettings['theme'] ?? 'light';
if ($activeTheme === 'white-orange') {
    $activeTheme = 'light';
}
if ($activeTheme !== 'dark') $activeTheme = 'light';

$pageTitle = $pageTitle ?? 'Custom Type Master Pro';
?>
<!DOCTYPE html>
<html lang="en" data-theme="<?= e($activeTheme) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <meta name="csrf-token" content="<?= e(get_csrf_token()) ?>">
  <meta name="theme-color" content="#000000">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-title" content="TypeMaster Pro">
  <link rel="manifest" href="<?= BASE_URL ?>/manifest.json">
  <title><?= e($pageTitle) ?> — TypeMaster Pro</title>

  <script>
    (function() {
      try {
        var t = localStorage.getItem('typemaster_theme') || '<?= e($activeTheme) ?>' || 'light';
        if (t !== 'dark') t = 'light';
        document.documentElement.setAttribute('data-theme', t);
      } catch (e) {}
      if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
          navigator.serviceWorker.register('<?= BASE_URL ?>/sw.js').catch(function(){});
        });
      }
    })();
  </script>

  <!-- Fonts: Inter (readable body), JetBrains Mono (numbers), Press Start 2P (decorative only) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&family=Press+Start+2P&display=swap" rel="stylesheet">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>

  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">

  <style>
    :root {
      --typing-font: '<?= e($userSettings['font_family']) ?>', monospace;
      --typing-size: <?= (int)$userSettings['font_size'] ?>px;
      --typing-line-height: <?= (float)$userSettings['line_height'] ?>;
      --caret-color: <?= e($userSettings['caret_color']) ?>;
      --char-correct: <?= e($userSettings['correct_color']) ?>;
      --char-incorrect: <?= e($userSettings['incorrect_color']) ?>;
      --typing-area-width: <?= e($userSettings['typing_area_width']) ?>;

      --px-sans:  "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
      --px-pixel: "Press Start 2P", "Courier New", monospace;
      --px-mono:  "JetBrains Mono", ui-monospace, "SF Mono", Menlo, Consolas, monospace;
    }
  </style>

  <style>
    /* ============================================================
       HEADER — Pixel theme, sans-serif readable text
       ============================================================ */
    .app-navbar {
      --px-ink:    #000000;
      --px-paper:  #ffffff;
      --px-paper-2:#f0f0f0;
      --px-shadow: 2px;

      position: sticky;
      top: 0;
      z-index: 1030;
      background: var(--px-paper) !important;
      border-bottom: 4px solid var(--px-ink) !important;
      padding: 0.55rem 0.85rem !important;
      font-family: var(--px-sans);
      -webkit-font-smoothing: antialiased;
      image-rendering: pixelated;
    }
    html[data-theme="dark"] .app-navbar {
      --px-ink:    #ffffff;
      --px-paper:  #000000;
      --px-paper-2:#1a1a1a;
    }

    .app-navbar::before {
      content: '';
      position: absolute;
      inset: 0;
      background-image:
        linear-gradient(rgba(0,0,0,0.04) 1px, transparent 1px),
        linear-gradient(90deg, rgba(0,0,0,0.04) 1px, transparent 1px);
      background-size: 4px 4px;
      pointer-events: none;
      z-index: 0;
    }
    html[data-theme="dark"] .app-navbar::before {
      background-image:
        linear-gradient(rgba(255,255,255,0.05) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,0.05) 1px, transparent 1px);
    }
    .app-navbar > * { position: relative; z-index: 1; }

    /* Icon button */
    .app-navbar .px-icon-btn {
      width: 38px; height: 38px;
      display: inline-flex; align-items: center; justify-content: center;
      background: var(--px-paper);
      border: 3px solid var(--px-ink);
      box-shadow: var(--px-shadow) var(--px-shadow) 0 var(--px-ink);
      color: var(--px-ink);
      cursor: pointer; padding: 0; flex-shrink: 0;
      transition: transform 0.05s steps(1), box-shadow 0.05s steps(1);
      -webkit-appearance: none; appearance: none;
    }
    .app-navbar .px-icon-btn:hover { background: var(--px-paper-2); }
    .app-navbar .px-icon-btn:active {
      transform: translate(var(--px-shadow), var(--px-shadow));
      box-shadow: 0 0 0 var(--px-ink);
    }
    .app-navbar .px-icon-btn i { font-size: 1.15rem; }

    /* Brand */
    .app-navbar .px-brand {
      display: inline-flex; align-items: center; gap: 0.6rem;
      padding: 0.35rem 0.7rem;
      text-decoration: none !important;
      color: var(--px-ink);
      background: var(--px-paper);
      border: 3px solid var(--px-ink);
      box-shadow: var(--px-shadow) var(--px-shadow) 0 var(--px-ink);
      flex-shrink: 0;
      transition: transform 0.05s steps(1), box-shadow 0.05s steps(1);
    }
    .app-navbar .px-brand:hover { background: var(--px-paper-2); }
    .app-navbar .px-brand:active {
      transform: translate(var(--px-shadow), var(--px-shadow));
      box-shadow: 0 0 0 var(--px-ink);
    }

    .app-navbar .px-logo {
      width: 30px; height: 30px;
      background: var(--px-ink);
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
    }
    .app-navbar .px-logo svg { display: block; shape-rendering: crispEdges; }

    .app-navbar .px-brand-text {
      display: flex; flex-direction: column; gap: 0.15rem; line-height: 1;
    }
    /* DECORATIVE — pixel font allowed here */
    .app-navbar .px-brand-name {
      font-family: var(--px-pixel);
      font-weight: 400;
      font-size: 0.68rem;   /* ≈ 11px — min readable for Press Start 2P */
      letter-spacing: 0.02em;
      color: var(--px-ink);
      text-transform: uppercase;
      line-height: 1.5;
    }
    /* Decorative tag — sans-serif because it sits under the pixel name */
    .app-navbar .px-brand-tag {
      font-family: var(--px-sans);
      font-size: 0.62rem;
      font-weight: 700;
      letter-spacing: 0.14em;
      color: var(--px-ink);
      line-height: 1;
      text-transform: uppercase;
    }
    @media (max-width: 480px) {
      .app-navbar .px-brand { padding: 0.3rem 0.5rem; gap: 0.45rem; }
      .app-navbar .px-brand-name { font-size: 0.58rem; }
      .app-navbar .px-brand-tag { display: none; }
      .app-navbar .px-logo { width: 26px; height: 26px; }
    }

    /* Crumb */
    .app-navbar .px-crumb {
      display: inline-flex; align-items: center; gap: 0.4rem;
      padding: 0.3rem 0.6rem;
      background: var(--px-paper);
      border: 2px solid var(--px-ink);
      box-shadow: 1px 1px 0 var(--px-ink);
      font-family: var(--px-sans);
      font-size: 0.72rem;
      font-weight: 600;
      letter-spacing: 0.06em;
      text-transform: uppercase;
      color: var(--px-ink);
      white-space: nowrap;
      margin-left: 0.5rem;
    }
    .app-navbar .px-crumb i { font-size: 0.8rem; }

    /* Buttons — SANS-SERIF readable */
    .app-navbar .px-btn,
    .app-navbar .px-btn-primary {
      display: inline-flex; align-items: center; gap: 0.45rem;
      padding: 0.45rem 0.75rem;
      background: var(--px-paper);
      border: 3px solid var(--px-ink);
      box-shadow: var(--px-shadow) var(--px-shadow) 0 var(--px-ink);
      color: var(--px-ink);
      font-family: var(--px-sans);
      font-size: 0.78rem;
      font-weight: 600;
      letter-spacing: 0.03em;
      text-transform: uppercase;
      text-decoration: none !important;
      cursor: pointer;
      transition: transform 0.05s steps(1), box-shadow 0.05s steps(1);
      white-space: nowrap;
      line-height: 1.2;
      -webkit-appearance: none; appearance: none;
    }
    .app-navbar .px-btn:hover { background: var(--px-paper-2); }
    .app-navbar .px-btn:active {
      transform: translate(var(--px-shadow), var(--px-shadow));
      box-shadow: 0 0 0 var(--px-ink);
    }
    .app-navbar .px-btn i { font-size: 0.85rem; }

    .app-navbar .px-btn-primary {
      background: var(--px-ink);
      color: var(--px-paper) !important;
    }
    .app-navbar .px-btn-primary:hover {
      background: var(--px-ink);
      color: var(--px-paper) !important;
    }
    .app-navbar .px-btn-primary i { color: var(--px-paper); }

    /* Theme button */
    .app-navbar .dropdown-toggle::after { display: none; }
    .app-navbar .px-theme-btn {
      display: inline-flex; align-items: center; gap: 0.4rem;
      padding: 0.45rem 0.7rem;
      background: var(--px-paper);
      border: 3px solid var(--px-ink);
      box-shadow: var(--px-shadow) var(--px-shadow) 0 var(--px-ink);
      color: var(--px-ink);
      font-family: var(--px-sans);
      font-size: 0.78rem;
      font-weight: 600;
      letter-spacing: 0.03em;
      text-transform: uppercase;
      cursor: pointer;
      transition: transform 0.05s steps(1), box-shadow 0.05s steps(1);
      -webkit-appearance: none; appearance: none;
    }
    .app-navbar .px-theme-btn:hover { background: var(--px-paper-2); }
    .app-navbar .px-theme-btn:active {
      transform: translate(var(--px-shadow), var(--px-shadow));
      box-shadow: 0 0 0 var(--px-ink);
    }
    .app-navbar .px-theme-btn i { font-size: 0.9rem; }

    /* Dropdowns */
    .app-navbar .dropdown-menu {
      background: var(--px-paper) !important;
      border: 3px solid var(--px-ink) !important;
      border-radius: 0 !important;
      padding: 0.25rem !important;
      box-shadow: 3px 3px 0 var(--px-ink) !important;
      min-width: 230px;
      margin-top: 0.6rem !important;
      font-family: var(--px-sans);
    }
    .app-navbar .dropdown-menu .dropdown-item {
      color: var(--px-ink) !important;
      border-radius: 0;
      padding: 0.6rem 0.7rem !important;
      font-family: var(--px-sans);
      font-size: 0.85rem;
      font-weight: 500;
      display: flex; align-items: center; gap: 0.55rem;
      transition: none;
    }
    .app-navbar .dropdown-menu .dropdown-item:hover,
    .app-navbar .dropdown-menu .dropdown-item:focus {
      background: var(--px-ink) !important;
      color: var(--px-paper) !important;
    }
    .app-navbar .dropdown-menu .dropdown-item i {
      font-size: 0.9rem; width: 16px; text-align: center;
      color: var(--px-ink);
    }
    .app-navbar .dropdown-menu .dropdown-item:hover i,
    .app-navbar .dropdown-menu .dropdown-item:focus i {
      color: var(--px-paper);
    }

    /* User trigger */
    .app-navbar .px-user-btn {
      display: inline-flex; align-items: center; gap: 0.5rem;
      padding: 0.32rem 0.6rem 0.32rem 0.32rem;
      background: var(--px-paper);
      border: 3px solid var(--px-ink);
      box-shadow: var(--px-shadow) var(--px-shadow) 0 var(--px-ink);
      color: var(--px-ink); cursor: pointer;
      transition: transform 0.05s steps(1), box-shadow 0.05s steps(1);
      -webkit-appearance: none; appearance: none;
    }
    .app-navbar .px-user-btn:hover { background: var(--px-paper-2); }
    .app-navbar .px-user-btn:active {
      transform: translate(var(--px-shadow), var(--px-shadow));
      box-shadow: 0 0 0 var(--px-ink);
    }

    /* Avatar — DECORATIVE, pixel allowed for the initial */
    .app-navbar .px-user-avatar {
      width: 30px; height: 30px;
      background: var(--px-ink);
      color: var(--px-paper);
      font-family: var(--px-pixel);
      font-size: 0.6rem;
      display: flex; align-items: center; justify-content: center;
      text-transform: uppercase;
      flex-shrink: 0; position: relative;
    }
    .app-navbar .px-user-avatar::after {
      content: '';
      position: absolute; inset: 0;
      background-image:
        linear-gradient(45deg, var(--px-paper) 25%, transparent 25%),
        linear-gradient(-45deg, var(--px-paper) 25%, transparent 25%),
        linear-gradient(45deg, transparent 75%, var(--px-paper) 75%),
        linear-gradient(-45deg, transparent 75%, var(--px-paper) 75%);
      background-size: 4px 4px;
      background-position: 0 0, 0 2px, 2px -2px, -2px 0;
      opacity: 0.12; pointer-events: none;
    }
    .app-navbar .px-user-avatar span { position: relative; z-index: 1; }

    /* User meta — sans-serif */
    .app-navbar .px-user-meta { text-align: left; line-height: 1.3; }
    .app-navbar .px-user-name {
      font-family: var(--px-sans);
      font-size: 0.8rem;
      font-weight: 700;
      letter-spacing: 0.01em;
      color: var(--px-ink);
      max-width: 130px;
      overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    }
    .app-navbar .px-user-handle {
      font-family: var(--px-mono);
      font-size: 0.68rem;
      color: var(--px-ink);
      opacity: 0.65;
    }

    /* User menu header */
    .app-navbar .px-user-header {
      padding: 0.65rem 0.7rem;
      border-bottom: 3px solid var(--px-ink);
      margin-bottom: 0.25rem;
    }
    .app-navbar .px-user-header .u-name {
      font-family: var(--px-sans);
      font-size: 0.9rem;
      font-weight: 700;
      color: var(--px-ink);
      line-height: 1.3;
    }
    .app-navbar .px-user-header .u-handle {
      font-family: var(--px-mono);
      font-size: 0.72rem;
      color: var(--px-ink);
      opacity: 0.65;
      margin-bottom: 0.4rem;
    }
    .app-navbar .px-role {
      display: inline-flex; align-items: center; gap: 0.3rem;
      font-family: var(--px-sans);
      font-size: 0.62rem;
      font-weight: 700;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      padding: 0.15rem 0.45rem;
      background: var(--px-ink);
      color: var(--px-paper);
      border: 2px solid var(--px-ink);
    }
    .app-navbar .px-role i { font-size: 0.7rem; }

    .app-navbar .dropdown-divider {
      border-color: var(--px-ink) !important;
      border-width: 2px 0 0 0 !important;
      margin: 0.25rem 0; opacity: 1;
    }
    .app-navbar .dropdown-item.text-danger { color: var(--px-ink) !important; }
    .app-navbar .dropdown-item.text-danger:hover {
      background: var(--px-ink) !important;
      color: var(--px-paper) !important;
    }

    /* Live blink dot */
    .app-navbar .px-live {
      display: inline-block;
      width: 6px; height: 6px;
      background: var(--px-ink);
      margin-left: 0.25rem;
      animation: pxBlink 1.2s steps(1) infinite;
    }
    @keyframes pxBlink {
      0%, 49% { opacity: 1; }
      50%, 100% { opacity: 0; }
    }

    /* Responsive */
    @media (max-width: 1199px) { .app-navbar .px-crumb { display: none !important; } }
    @media (max-width: 991px) { .app-navbar .px-user-meta { display: none !important; } }
    @media (max-width: 768px) {
      .app-navbar .px-hide-sm { display: none !important; }
      .app-navbar { padding: 0.45rem 0.6rem !important; }
    }
    @media (max-width: 575px) {
      .app-navbar .px-theme-btn span,
      .app-navbar .px-btn span,
      .app-navbar .px-btn-primary span { display: none !important; }
      .app-navbar .px-btn,
      .app-navbar .px-theme-btn,
      .app-navbar .px-btn-primary { padding: 0.45rem 0.6rem; }
      .app-navbar .px-user-btn { padding: 0.28rem 0.32rem 0.28rem 0.28rem; }
    }
    @media (max-width: 991px) {
      .tm-sidebar { z-index: 1040 !important; }
      .tm-sb-backdrop { z-index: 1035 !important; }
    }
  </style>
</head>
<body>

<?php
$pdoCheck = getDBConnection();
if (!$pdoCheck):
?>
  <div style="background:#000;color:#fff;padding:0.55rem 1rem;font-size:0.82rem;text-align:center;font-family:'Inter',-apple-system,sans-serif;border-bottom:4px solid #fff;">
    <div style="max-width:1240px;margin:0 auto;display:flex;align-items:center;justify-content:center;gap:0.5rem;flex-wrap:wrap;">
      <span style="display:inline-block;width:8px;height:8px;background:#fff;animation:pxBlink 1.2s steps(1) infinite;"></span>
      <span><strong>DB OFFLINE:</strong> Unable to connect to MySQL on <code style="background:#222;padding:0.1rem 0.35rem;font-family:'JetBrains Mono',monospace;"><?= htmlspecialchars(defined('DB_HOST') ? DB_HOST : 'localhost') ?>:<?= htmlspecialchars(defined('DB_PORT') ? DB_PORT : '3306') ?></code> (DB: <code style="background:#222;padding:0.1rem 0.35rem;font-family:'JetBrains Mono',monospace;"><?= htmlspecialchars(defined('DB_NAME') ? DB_NAME : '') ?></code>). Verify MySQL is running and credentials in <code style="background:#222;padding:0.1rem 0.35rem;font-family:'JetBrains Mono',monospace;">config/database.php</code> are correct.</span>
    </div>
  </div>
<?php endif; ?>

<div class="app-container">
  <?php if ($currentUser): ?>
    <?php include __DIR__ . '/sidebar.php'; ?>
  <?php endif; ?>

  <div class="main-wrapper">
    <?php
      $pageIcon = 'bi-speedometer2';
      $pageBadgeTitle = 'Dashboard';
      if (strpos($pageTitle, 'Practice') !== false || strpos($pageTitle, 'Typing Arena') !== false) {
          $pageIcon = 'bi-keyboard-fill'; $pageBadgeTitle = 'Typing Arena';
      } elseif (strpos($pageTitle, 'Settings') !== false || strpos($pageTitle, 'Customization') !== false) {
          $pageIcon = 'bi-sliders'; $pageBadgeTitle = 'Preferences';
      } elseif (strpos($pageTitle, 'History') !== false || strpos($pageTitle, 'Session Report') !== false || strpos($pageTitle, 'Results') !== false) {
          $pageIcon = 'bi-award-fill'; $pageBadgeTitle = 'History & Telemetry';
      } elseif (strpos($pageTitle, 'Statistics') !== false || strpos($pageTitle, 'Analytics') !== false) {
          $pageIcon = 'bi-graph-up-arrow'; $pageBadgeTitle = 'Analytics';
      } elseif (strpos($pageTitle, 'Lesson') !== false) {
          $pageIcon = 'bi-mortarboard-fill'; $pageBadgeTitle = 'Typing Lessons';
      } elseif (strpos($pageTitle, 'Game') !== false || strpos($pageTitle, 'Ninja') !== false || strpos($pageTitle, 'Defender') !== false || strpos($pageTitle, 'Arcade') !== false) {
          $pageIcon = 'bi-controller'; $pageBadgeTitle = 'Arcade Arena';
      } elseif (strpos($pageTitle, 'Admin') !== false) {
          $pageIcon = 'bi-shield-lock-fill'; $pageBadgeTitle = 'Admin Portal';
      }
    ?>

    <header class="app-navbar d-flex align-items-center justify-content-between gap-2">

      <div class="d-flex align-items-center gap-2" style="min-width:0;">
        <button id="sidebar-toggle-btn"
                class="px-icon-btn <?= $currentUser ? 'd-lg-none' : 'd-none' ?>"
                aria-label="Toggle sidebar" type="button">
          <i class="bi bi-list"></i>
        </button>

        <a href="<?= BASE_URL ?>/<?= $currentUser ? 'dashboard.php' : 'index.php' ?>"
           class="px-brand" aria-label="TypeMaster PRO Home">
          <div class="px-logo">
            <svg width="18" height="18" viewBox="0 0 8 8" fill="currentColor" xmlns="http://www.w3.org/2000/svg" shape-rendering="crispEdges">
              <rect x="0" y="0" width="8" height="8" fill="var(--px-ink)"/>
              <rect x="1" y="1" width="3" height="1" fill="var(--px-paper)"/>
              <rect x="2" y="2" width="1" height="4" fill="var(--px-paper)"/>
              <rect x="5" y="1" width="1" height="5" fill="var(--px-paper)"/>
              <rect x="6" y="1" width="1" height="1" fill="var(--px-paper)"/>
              <rect x="6" y="3" width="1" height="1" fill="var(--px-paper)"/>
              <rect x="6" y="5" width="1" height="1" fill="var(--px-paper)"/>
            </svg>
          </div>
          <div class="px-brand-text">
            <span class="px-brand-name">TypeMaster<span class="px-live"></span></span>
            <span class="px-brand-tag">PRO</span>
          </div>
        </a>

        <span class="px-crumb d-none d-xl-inline-flex">
          <i class="bi <?= $pageIcon ?>"></i>
          <span><?= e($pageBadgeTitle) ?></span>
        </span>
      </div>

      <div class="d-flex align-items-center gap-1 gap-md-2">
        <a href="<?= BASE_URL ?>/lessons.php" class="px-btn px-hide-sm" title="Typing Lessons">
          <i class="bi bi-mortarboard-fill"></i><span>Lessons</span>
        </a>
        <a href="<?= BASE_URL ?>/games.php" class="px-btn px-hide-sm" title="Arcade Games">
          <i class="bi bi-controller"></i><span>Games</span>
        </a>
        <a href="<?= BASE_URL ?>/practice.php" class="px-btn-primary px-hide-sm" title="Launch Practice Drill">
          <i class="bi bi-keyboard-fill"></i><span>Practice</span>
        </a>
        <a href="<?= BASE_URL ?>/practice.php?mode=free" class="px-btn d-none d-md-inline-flex" title="Free Typing">
          <i class="bi bi-stopwatch-fill"></i><span>Watch</span>
        </a>
        <button type="button" class="px-btn d-none d-lg-inline-flex" id="btn-install-header" title="Install">
          <i class="bi bi-laptop"></i><span>Install</span>
        </button>

        <div class="dropdown">
          <button class="px-theme-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Switch Theme">
            <i class="bi bi-circle-half"></i><span class="d-none d-md-inline">Theme</span>
          </button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="#" data-set-theme="light"><i class="bi bi-sun-fill"></i> Light</a></li>
            <li><a class="dropdown-item" href="#" data-set-theme="dark"><i class="bi bi-moon-stars-fill"></i> Dark</a></li>
          </ul>
        </div>

        <?php if ($currentUser): ?>
          <div class="dropdown">
            <button class="px-user-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="User menu">
              <div class="px-user-meta d-none d-xl-block">
                <div class="px-user-name"><?= e($currentUser['full_name']) ?></div>
                <div class="px-user-handle">@<?= e($currentUser['username']) ?></div>
              </div>
              <div class="px-user-avatar">
                <span><?= strtoupper(substr($currentUser['username'], 0, 1)) ?></span>
              </div>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li class="px-user-header">
                <div class="u-name"><?= e($currentUser['full_name']) ?></div>
                <div class="u-handle">@<?= e($currentUser['username']) ?></div>
                <?php if (is_admin()): ?>
                  <span class="px-role"><i class="bi bi-shield-lock-fill"></i> Admin</span>
                <?php else: ?>
                  <span class="px-role"><i class="bi bi-patch-check-fill"></i> Member</span>
                <?php endif; ?>
              </li>
              <li><a class="dropdown-item" href="<?= BASE_URL ?>/dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
              <li><a class="dropdown-item" href="<?= BASE_URL ?>/games.php"><i class="bi bi-controller"></i> Games</a></li>
              <li><a class="dropdown-item" href="<?= BASE_URL ?>/lessons.php"><i class="bi bi-mortarboard-fill"></i> Lessons</a></li>
              <li><a class="dropdown-item" href="<?= BASE_URL ?>/practice.php?mode=passage"><i class="bi bi-keyboard-fill"></i> Passage Practice</a></li>
              <li><a class="dropdown-item" href="<?= BASE_URL ?>/practice.php?mode=free"><i class="bi bi-stopwatch-fill"></i> Free &amp; Watch</a></li>
              <li><a class="dropdown-item" href="<?= BASE_URL ?>/practice.php?mode=custom"><i class="bi bi-sliders2"></i> Custom Drills</a></li>
              <li><a class="dropdown-item" href="<?= BASE_URL ?>/history.php"><i class="bi bi-award-fill"></i> History</a></li>
              <li><a class="dropdown-item" href="<?= BASE_URL ?>/statistics.php"><i class="bi bi-graph-up-arrow"></i> Analytics</a></li>
              <li><a class="dropdown-item" href="<?= BASE_URL ?>/settings.php"><i class="bi bi-gear-fill"></i> Settings</a></li>
              <?php if (is_admin()): ?>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/index.php"><i class="bi bi-shield-lock-fill"></i> Admin Panel</a></li>
              <?php endif; ?>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/logout.php"><i class="bi bi-box-arrow-right"></i> Sign Out</a></li>
            </ul>
          </div>
        <?php else: ?>
          <a href="<?= BASE_URL ?>/login.php" class="px-btn" title="Sign In">
            <i class="bi bi-box-arrow-in-right"></i><span>Sign In</span>
          </a>
          <a href="<?= BASE_URL ?>/register.php" class="px-btn-primary" title="Sign Up">
            <i class="bi bi-person-plus-fill"></i><span>Sign Up</span>
          </a>
        <?php endif; ?>
      </div>
    </header>

    <main class="flex-grow-1 p-3 p-md-4">
      <?php
      $flash = get_flash();
      if ($flash):
      ?>
        <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show shadow-sm mb-4" role="alert">
          <i class="bi bi-info-circle-fill me-2"></i>
          <?= e($flash['message']) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>