 <?php
/**
 * Custom Type Master Pro
 * Global Header Template — B&W Editorial Edition
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
  <meta name="theme-color" content="#0a0a0a">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-title" content="TypeMaster Pro">
  <link rel="manifest" href="<?= BASE_URL ?>/manifest.json">
  <title><?= e($pageTitle) ?> — TypeMaster Pro</title>

  <!-- Instant Client Theme Loader & PWA Service Worker -->
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

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600&family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>

  <!-- Custom Stylesheet -->
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">

  <!-- Dynamic User Customization Variables -->
  <style>
    :root {
      --typing-font: '<?= e($userSettings['font_family']) ?>', monospace;
      --typing-size: <?= (int)$userSettings['font_size'] ?>px;
      --typing-line-height: <?= (float)$userSettings['line_height'] ?>;
      --caret-color: <?= e($userSettings['caret_color']) ?>;
      --char-correct: <?= e($userSettings['correct_color']) ?>;
      --char-incorrect: <?= e($userSettings['incorrect_color']) ?>;
      --typing-area-width: <?= e($userSettings['typing_area_width']) ?>;
    }
  </style>

  <!-- ============================================================
       HEADER — B&W EDITORIAL SCOPED STYLES
       ============================================================ -->
  <style>
    /* ---------- Header shell ---------- */
    .app-navbar {
      --hd-paper:    #fafaf8;
      --hd-paper-2:  #f2f1ec;
      --hd-ink:      #0b0b0b;
      --hd-ink-2:    #1f1f1f;
      --hd-muted:    #6b6a63;
      --hd-muted-2:  #94918a;
      --hd-line:     #d8d6cd;
      --hd-line-2:   #c4c1b6;
      --hd-accent:   #ff4d00;
      --hd-mono:     "JetBrains Mono", ui-monospace, monospace;
      --hd-sans:     "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;

      position: sticky;
      top: 0;
      z-index: 1030;
      background: var(--hd-paper) !important;
      border-bottom: 1px solid var(--hd-line) !important;
      padding: 0.65rem 1.25rem !important;
      font-family: var(--hd-sans);
      -webkit-font-smoothing: antialiased;
    }
    @media (max-width: 768px) {
      .app-navbar { padding: 0.55rem 0.85rem !important; }
    }

    /* Dark theme overrides */
    html[data-theme="dark"] .app-navbar {
      --hd-paper:    #0b0b0b;
      --hd-paper-2:  #141414;
      --hd-ink:      #fafaf8;
      --hd-ink-2:    #e5e5e5;
      --hd-muted:    #8a8a8a;
      --hd-muted-2:  #6b6b6b;
      --hd-line:     #262626;
      --hd-line-2:   #333333;
    }

    /* ---------- Sidebar toggle (mobile) ---------- */
    .app-navbar .hd-icon-btn {
      width: 36px;
      height: 36px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: transparent;
      border: 1px solid var(--hd-line);
      border-radius: 8px;
      color: var(--hd-ink);
      cursor: pointer;
      transition: all 0.15s ease;
      padding: 0;
      flex-shrink: 0;
      -webkit-appearance: none;
      appearance: none;
    }
    .app-navbar .hd-icon-btn:hover {
      background: var(--hd-paper-2);
      border-color: var(--hd-ink);
    }
    .app-navbar .hd-icon-btn i {
      font-size: 1.1rem;
    }

    /* ---------- Brand ---------- */
    .app-navbar .app-brand {
      display: inline-flex;
      align-items: center;
      gap: 0.55rem;
      text-decoration: none !important;
      color: var(--hd-ink);
      flex-shrink: 0;
      margin-right: 0.25rem;
    }
    .app-navbar .brand-logo-badge {
      width: 32px !important;
      height: 32px !important;
      background: var(--hd-ink) !important;
      border-radius: 8px !important;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      transition: background 0.2s ease;
    }
    .app-navbar .app-brand:hover .brand-logo-badge {
      background: var(--hd-accent) !important;
    }
    .app-navbar .brand-logo-badge svg rect[stroke] {
      stroke: var(--hd-paper);
    }
    .app-navbar .brand-logo-badge svg path {
      stroke: var(--hd-paper);
    }
    html[data-theme="dark"] .app-navbar .brand-logo-badge {
      background: var(--hd-ink) !important;
    }
    .app-navbar .brand-name {
      font-weight: 800 !important;
      font-size: 1rem !important;
      letter-spacing: -0.02em !important;
      color: var(--hd-ink) !important;
    }
    .app-navbar .brand-name-wrap .badge {
      font-family: var(--hd-mono);
      font-size: 0.55rem !important;
      font-weight: 700 !important;
      letter-spacing: 0.06em !important;
      background: var(--hd-ink) !important;
      color: var(--hd-paper) !important;
      padding: 0.15rem 0.35rem !important;
      border-radius: 3px !important;
      vertical-align: middle;
      margin-left: 0.1rem;
    }
    @media (max-width: 480px) {
      .app-navbar .brand-name { font-size: 0.9rem !important; }
      .app-navbar .brand-logo-badge { width: 28px !important; height: 28px !important; }
    }

    /* ---------- Breadcrumb pill ---------- */
    .app-navbar .hd-crumb {
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      padding: 0.32rem 0.7rem;
      background: var(--hd-paper-2);
      border: 1px solid var(--hd-line);
      border-radius: 999px;
      font-family: var(--hd-mono);
      font-size: 0.68rem;
      font-weight: 600;
      letter-spacing: 0.06em;
      text-transform: uppercase;
      color: var(--hd-muted);
      white-space: nowrap;
      margin-left: 0.5rem;
    }
    .app-navbar .hd-crumb i {
      font-size: 0.72rem;
      color: var(--hd-ink);
    }

    /* ---------- Action buttons (generic) ---------- */
    .app-navbar .btn-control,
    .app-navbar .soft-btn-primary {
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      padding: 0.42rem 0.75rem !important;
      background: transparent;
      border: 1px solid var(--hd-line);
      border-radius: 8px;
      color: var(--hd-ink);
      font-family: var(--hd-sans);
      font-size: 0.78rem;
      font-weight: 600;
      text-decoration: none !important;
      cursor: pointer;
      transition: all 0.15s ease;
      white-space: nowrap;
      line-height: 1;
      -webkit-appearance: none;
      appearance: none;
    }
    .app-navbar .btn-control:hover {
      background: var(--hd-paper-2);
      border-color: var(--hd-ink);
      color: var(--hd-ink);
    }
    .app-navbar .btn-control i {
      font-size: 0.85rem;
      color: var(--hd-muted);
      transition: color 0.15s ease;
    }
    .app-navbar .btn-control:hover i {
      color: var(--hd-ink);
    }

    /* Primary action = inverted */
    .app-navbar .soft-btn-primary {
      background: var(--hd-ink);
      color: var(--hd-paper) !important;
      border-color: var(--hd-ink);
    }
    .app-navbar .soft-btn-primary:hover {
      background: transparent;
      color: var(--hd-ink) !important;
    }
    .app-navbar .soft-btn-primary i {
      color: var(--hd-paper);
    }
    .app-navbar .soft-btn-primary:hover i {
      color: var(--hd-ink);
    }

    /* Games button accent */
    .app-navbar .btn-control .bi-controller {
      color: var(--hd-accent);
    }

    /* ---------- Theme dropdown ---------- */
    .app-navbar .dropdown-toggle::after {
      display: none;
    }
    .app-navbar .hd-theme-btn {
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
      padding: 0.42rem 0.65rem !important;
      background: transparent;
      border: 1px solid var(--hd-line);
      border-radius: 8px;
      color: var(--hd-ink);
      font-family: var(--hd-sans);
      font-size: 0.78rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.15s ease;
      -webkit-appearance: none;
      appearance: none;
    }
    .app-navbar .hd-theme-btn:hover {
      background: var(--hd-paper-2);
      border-color: var(--hd-ink);
    }
    .app-navbar .dropdown-menu {
      background: var(--hd-paper) !important;
      border: 1px solid var(--hd-line) !important;
      border-radius: 8px;
      padding: 0.35rem !important;
      box-shadow: 0 8px 28px rgba(0,0,0,0.15) !important;
      min-width: 190px;
    }
    .app-navbar .dropdown-menu .dropdown-item {
      color: var(--hd-ink) !important;
      border-radius: 5px;
      padding: 0.5rem 0.7rem !important;
      font-size: 0.82rem;
      font-weight: 500;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      transition: background 0.12s ease;
    }
    .app-navbar .dropdown-menu .dropdown-item:hover,
    .app-navbar .dropdown-menu .dropdown-item:focus {
      background: var(--hd-paper-2) !important;
      color: var(--hd-ink) !important;
    }
    .app-navbar .dropdown-menu .dropdown-item i {
      font-size: 0.9rem;
      color: var(--hd-muted);
      width: 16px;
      text-align: center;
    }
    .app-navbar .dropdown-menu .dropdown-item:hover i {
      color: var(--hd-ink);
    }

    /* ---------- User dropdown ---------- */
    .app-navbar .user-avatar {
      width: 34px;
      height: 34px;
      border-radius: 8px;
      background: var(--hd-ink);
      color: var(--hd-paper);
      font-family: var(--hd-mono);
      font-weight: 800;
      font-size: 0.85rem;
      display: flex;
      align-items: center;
      justify-content: center;
      text-transform: uppercase;
      flex-shrink: 0;
      transition: background 0.15s ease;
    }
    .app-navbar .dropdown:hover .user-avatar {
      background: var(--hd-accent);
    }
    .app-navbar .user-meta {
      text-align: right;
      line-height: 1.15;
    }
    .app-navbar .user-meta .user-name {
      font-size: 0.82rem;
      font-weight: 700;
      color: var(--hd-ink);
      max-width: 130px;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }
    .app-navbar .user-meta .user-handle {
      font-family: var(--hd-mono);
      font-size: 0.65rem;
      color: var(--hd-muted);
    }
    .app-navbar .user-menu-header {
      padding: 0.65rem 0.7rem;
      border-bottom: 1px solid var(--hd-line);
      margin-bottom: 0.35rem;
    }
    .app-navbar .user-menu-header .u-name {
      font-weight: 700;
      font-size: 0.88rem;
      color: var(--hd-ink);
      line-height: 1.2;
    }
    .app-navbar .user-menu-header .u-handle {
      font-family: var(--hd-mono);
      font-size: 0.7rem;
      color: var(--hd-muted);
      margin-bottom: 0.4rem;
    }
    .app-navbar .user-role-badge {
      display: inline-flex;
      align-items: center;
      gap: 0.3rem;
      font-family: var(--hd-mono);
      font-size: 0.6rem;
      font-weight: 700;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      padding: 0.15rem 0.45rem;
      border-radius: 3px;
      background: var(--hd-ink);
      color: var(--hd-paper);
    }
    .app-navbar .user-role-badge.admin {
      background: var(--hd-accent);
    }
    .app-navbar .dropdown-divider {
      border-color: var(--hd-line) !important;
      margin: 0.35rem 0;
    }
    .app-navbar .dropdown-item.text-danger {
      color: #dc2626 !important;
    }
    .app-navbar .dropdown-item.text-danger:hover {
      background: rgba(220,38,38,0.08) !important;
      color: #dc2626 !important;
    }
    .app-navbar .dropdown-item.text-danger i {
      color: #dc2626;
    }

    /* ---------- Mobile breakpoints ---------- */
    @media (max-width: 991px) {
      .app-navbar .hd-crumb { display: none !important; }
    }
    @media (max-width: 768px) {
      .app-navbar .hd-hide-sm { display: none !important; }
    }
    @media (max-width: 575px) {
      .app-navbar { padding: 0.5rem 0.65rem !important; }
      .app-navbar .user-meta { display: none !important; }
      .app-navbar .hd-theme-btn span { display: none !important; }
      .app-navbar .hd-theme-btn { padding: 0.42rem 0.55rem !important; }
      .app-navbar .soft-btn-primary span,
      .app-navbar .btn-control span { display: none !important; }
      .app-navbar .soft-btn-primary,
      .app-navbar .btn-control { padding: 0.42rem 0.6rem !important; }
      .app-navbar .brand-name { font-size: 0.85rem !important; }
    }

    /* ---------- Ensure sidebar z-index is above header on mobile ---------- */
    @media (max-width: 991px) {
      .tm-sidebar { z-index: 1040 !important; }
      .tm-sb-backdrop { z-index: 1035 !important; }
    }
  </style>
</head>
<body>

<?php
// Database health banner
$pdoCheck = getDBConnection();
if (!$pdoCheck):
?>
  <div style="background:#0b0b0b;color:#fff;padding:0.55rem 1rem;font-size:0.78rem;text-align:center;border-bottom:1px solid #262626;">
    <div style="max-width:1240px;margin:0 auto;display:flex;align-items:center;justify-content:center;gap:0.5rem;flex-wrap:wrap;">
      <i class="bi bi-exclamation-triangle-fill" style="color:#facc15;"></i>
      <span><strong>Database Notice:</strong> Unable to connect to MySQL on <code style="background:rgba(255,255,255,0.1);padding:0.1rem 0.35rem;border-radius:3px;"><?= htmlspecialchars(defined('DB_HOST') ? DB_HOST : 'localhost') ?>:<?= htmlspecialchars(defined('DB_PORT') ? DB_PORT : '3306') ?></code> (DB: <code style="background:rgba(255,255,255,0.1);padding:0.1rem 0.35rem;border-radius:3px;"><?= htmlspecialchars(defined('DB_NAME') ? DB_NAME : '') ?></code>). Verify MySQL is running and credentials in <code style="background:rgba(255,255,255,0.1);padding:0.1rem 0.35rem;border-radius:3px;">config/database.php</code> are correct.</span>
    </div>
  </div>
<?php endif; ?>

<div class="app-container">
  <?php if ($currentUser): ?>
    <?php include __DIR__ . '/sidebar.php'; ?>
  <?php endif; ?>

  <div class="main-wrapper">
    <?php
      // Page-context icon & label
      $pageIcon = 'bi-speedometer2';
      $pageBadgeTitle = 'Dashboard';
      if (strpos($pageTitle, 'Practice') !== false || strpos($pageTitle, 'Typing Arena') !== false) {
          $pageIcon = 'bi-keyboard-fill';
          $pageBadgeTitle = 'Typing Arena';
      } elseif (strpos($pageTitle, 'Settings') !== false || strpos($pageTitle, 'Customization') !== false) {
          $pageIcon = 'bi-sliders';
          $pageBadgeTitle = 'Preferences';
      } elseif (strpos($pageTitle, 'History') !== false || strpos($pageTitle, 'Session Report') !== false || strpos($pageTitle, 'Results') !== false) {
          $pageIcon = 'bi-award-fill';
          $pageBadgeTitle = 'History & Telemetry';
      } elseif (strpos($pageTitle, 'Statistics') !== false || strpos($pageTitle, 'Analytics') !== false) {
          $pageIcon = 'bi-graph-up-arrow';
          $pageBadgeTitle = 'Analytics';
      } elseif (strpos($pageTitle, 'Lesson') !== false) {
          $pageIcon = 'bi-mortarboard-fill';
          $pageBadgeTitle = 'Typing Lessons';
      } elseif (strpos($pageTitle, 'Game') !== false || strpos($pageTitle, 'Ninja') !== false || strpos($pageTitle, 'Defender') !== false || strpos($pageTitle, 'Arcade') !== false) {
          $pageIcon = 'bi-controller';
          $pageBadgeTitle = 'Arcade Arena';
      } elseif (strpos($pageTitle, 'Admin') !== false) {
          $pageIcon = 'bi-shield-lock-fill';
          $pageBadgeTitle = 'Admin Portal';
      }
    ?>

    <!-- ============================================================
         HEADER BAR
         ============================================================ -->
    <header class="app-navbar d-flex align-items-center justify-content-between gap-2">

      <!-- LEFT: Sidebar toggle + brand + crumb -->
      <div class="d-flex align-items-center gap-2 gap-md-3" style="min-width:0;">

        <button id="sidebar-toggle-btn"
                class="hd-icon-btn <?= $currentUser ? 'd-lg-none' : 'd-none' ?>"
                aria-label="Toggle sidebar"
                type="button">
          <i class="bi bi-list"></i>
        </button>

        <a href="<?= BASE_URL ?>/<?= $currentUser ? 'dashboard.php' : 'index.php' ?>"
           class="app-brand"
           aria-label="TypeMaster PRO Home">

          <div class="brand-logo-badge">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <rect x="2" y="2" width="20" height="20" rx="4" fill="currentColor"/>
              <path d="M7 9H17M12 9V17" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
            </svg>
          </div>

          <div class="brand-name-wrap d-flex align-items-baseline gap-1">
            <span class="brand-name">TypeMaster</span>
            <span class="badge">PRO</span>
          </div>
        </a>

        <span class="hd-crumb d-none d-md-inline-flex">
          <i class="bi <?= $pageIcon ?>"></i>
          <span><?= e($pageBadgeTitle) ?></span>
        </span>

      </div>

      <!-- RIGHT: Actions + theme + user -->
      <div class="d-flex align-items-center gap-1 gap-md-2">

        <!-- Lessons -->
        <a href="<?= BASE_URL ?>/lessons.php"
           class="btn-control hd-hide-sm"
           title="Typing Lessons">
          <i class="bi bi-mortarboard-fill"></i>
          <span>Lessons</span>
        </a>

        <!-- Games -->
        <a href="<?= BASE_URL ?>/games.php"
           class="btn-control hd-hide-sm"
           title="Arcade Games">
          <i class="bi bi-controller"></i>
          <span>Games</span>
        </a>

        <!-- Practice (primary) -->
        <a href="<?= BASE_URL ?>/practice.php"
           class="soft-btn-primary hd-hide-sm"
           title="Launch Practice Drill">
          <i class="bi bi-keyboard-fill"></i>
          <span>Practice</span>
        </a>

        <!-- Watch (md+) -->
        <a href="<?= BASE_URL ?>/practice.php?mode=free"
           class="btn-control d-none d-md-inline-flex"
           title="Free Typing & Watch Timer">
          <i class="bi bi-stopwatch-fill"></i>
          <span>Watch</span>
        </a>

        <!-- Install (lg+) -->
        <button type="button"
                class="btn-control d-none d-lg-inline-flex"
                id="btn-install-header"
                title="Install TypeMaster on PC / Desktop">
          <i class="bi bi-laptop"></i>
          <span>Install</span>
        </button>

        <!-- Theme dropdown -->
        <div class="dropdown">
          <button class="hd-theme-btn dropdown-toggle"
                  type="button"
                  data-bs-toggle="dropdown"
                  aria-expanded="false"
                  title="Switch Theme">
            <i class="bi bi-circle-half"></i>
            <span class="d-none d-md-inline">Theme</span>
          </button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li>
              <a class="dropdown-item" href="#" data-set-theme="light">
                <i class="bi bi-sun-fill"></i> Light (White &amp; Black)
              </a>
            </li>
            <li>
              <a class="dropdown-item" href="#" data-set-theme="dark">
                <i class="bi bi-moon-stars-fill"></i> Dark (Black &amp; White)
              </a>
            </li>
          </ul>
        </div>

        <?php if ($currentUser): ?>
          <!-- User dropdown -->
          <div class="dropdown">
            <button class="btn p-0 border-0 d-flex align-items-center gap-2"
                    type="button"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                    aria-label="User menu">
              <div class="user-meta d-none d-xl-block">
                <div class="user-name"><?= e($currentUser['full_name']) ?></div>
                <div class="user-handle">@<?= e($currentUser['username']) ?></div>
              </div>
              <div class="user-avatar">
                <?= strtoupper(substr($currentUser['username'], 0, 1)) ?>
              </div>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li class="user-menu-header">
                <div class="u-name"><?= e($currentUser['full_name']) ?></div>
                <div class="u-handle">@<?= e($currentUser['username']) ?></div>
                <?php if (is_admin()): ?>
                  <span class="user-role-badge admin"><i class="bi bi-shield-lock-fill"></i> Admin</span>
                <?php else: ?>
                  <span class="user-role-badge"><i class="bi bi-patch-check-fill"></i> Member Typist</span>
                <?php endif; ?>
              </li>
              <li><a class="dropdown-item" href="<?= BASE_URL ?>/dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard Hub</a></li>
              <li><a class="dropdown-item" href="<?= BASE_URL ?>/games.php"><i class="bi bi-controller"></i> Arcade Games</a></li>
              <li><a class="dropdown-item" href="<?= BASE_URL ?>/lessons.php"><i class="bi bi-mortarboard-fill"></i> Typing Lessons</a></li>
              <li><a class="dropdown-item" href="<?= BASE_URL ?>/practice.php?mode=passage"><i class="bi bi-keyboard-fill"></i> Passage Practice</a></li>
              <li><a class="dropdown-item" href="<?= BASE_URL ?>/practice.php?mode=free"><i class="bi bi-stopwatch-fill"></i> Free Typing &amp; Watch</a></li>
              <li><a class="dropdown-item" href="<?= BASE_URL ?>/practice.php?mode=custom"><i class="bi bi-sliders2"></i> Custom Time Drills</a></li>
              <li><a class="dropdown-item" href="<?= BASE_URL ?>/history.php"><i class="bi bi-award-fill"></i> History &amp; Certificates</a></li>
              <li><a class="dropdown-item" href="<?= BASE_URL ?>/statistics.php"><i class="bi bi-graph-up-arrow"></i> Speed Analytics</a></li>
              <li><a class="dropdown-item" href="<?= BASE_URL ?>/settings.php"><i class="bi bi-gear-fill"></i> Customization</a></li>
              <?php if (is_admin()): ?>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/index.php"><i class="bi bi-shield-lock-fill"></i> Admin Panel</a></li>
              <?php endif; ?>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/logout.php"><i class="bi bi-box-arrow-right"></i> Sign Out</a></li>
            </ul>
          </div>
        <?php else: ?>
          <a href="<?= BASE_URL ?>/login.php" class="btn-control" title="Sign In">
            <i class="bi bi-box-arrow-in-right"></i>
            <span>Sign In</span>
          </a>
          <a href="<?= BASE_URL ?>/register.php" class="soft-btn-primary" title="Sign Up">
            <i class="bi bi-person-plus-fill"></i>
            <span>Sign Up</span>
          </a>
        <?php endif; ?>

      </div>
    </header>

    <!-- Main Content Container -->
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