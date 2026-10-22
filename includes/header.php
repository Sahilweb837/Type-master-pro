<?php
/**
 * Custom Type Master Pro
 * Global Header Template
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
$pageTitle = $pageTitle ?? 'Custom Type Master Pro';
?>
<!DOCTYPE html>
<html lang="en" data-theme="<?= e($activeTheme) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="<?= e(get_csrf_token()) ?>">
  <meta name="theme-color" content="#ff5532">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-title" content="TypeMaster Pro">
  <link rel="manifest" href="<?= BASE_URL ?>/manifest.json">
  <title><?= e($pageTitle) ?> — Custom Type Master Pro</title>

  <!-- Instant Client Theme Loader & PWA Service Worker -->
  <script>
    (function() {
      try {
        var t = localStorage.getItem('typemaster_theme');
        if (!t || t === 'white-orange') {
          t = '<?= e($activeTheme) ?>' || 'light';
        }
        document.documentElement.setAttribute('data-theme', t);
      } catch (e) {}
      if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
          navigator.serviceWorker.register('<?= BASE_URL ?>/sw.js').catch(function(){});
        });
      }
    })();
  </script>

  <!-- Google Fonts: Inter & Monospace Suite -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600&family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">

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
</head>
<body>

<?php
// Check if database is reachable
$pdoCheck = getDBConnection();
if (!$pdoCheck):
?>
  <div class="alert alert-warning mb-0 border-0 rounded-0 text-center py-2">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>
    <strong>Database Connection Notice:</strong> Unable to connect to MySQL database service on localhost:3306. Please verify that the database server is running.
  </div>
<?php endif; ?>

<div class="app-container">
  <?php if ($currentUser): ?>
    <?php include __DIR__ . '/sidebar.php'; ?>
  <?php endif; ?>

  <div class="main-wrapper">
    <!-- Top Navigation Bar (Soft UI Dashboard-Ready Header) -->
    <?php
      $pageIcon = 'bi-speedometer2';
      $pageBadgeTitle = 'Dashboard Hub';
      if (strpos($pageTitle, 'Practice') !== false || strpos($pageTitle, 'Typing Arena') !== false) {
          $pageIcon = 'bi-keyboard-fill';
          $pageBadgeTitle = 'Typing Arena';
      } elseif (strpos($pageTitle, 'Settings') !== false || strpos($pageTitle, 'Customization') !== false) {
          $pageIcon = 'bi-sliders';
          $pageBadgeTitle = 'Preferences';
      } elseif (strpos($pageTitle, 'History') !== false || strpos($pageTitle, 'Session Report') !== false || strpos($pageTitle, 'Results') !== false) {
          $pageIcon = 'bi-award-fill';
          $pageBadgeTitle = 'Telemetry & Records';
      } elseif (strpos($pageTitle, 'Statistics') !== false || strpos($pageTitle, 'Analytics') !== false) {
          $pageIcon = 'bi-graph-up-arrow';
          $pageBadgeTitle = 'Speed Analytics';
      } elseif (strpos($pageTitle, 'Admin') !== false) {
          $pageIcon = 'bi-shield-lock-fill';
          $pageBadgeTitle = 'Admin Portal';
      }
    ?>
    <header class="app-navbar">
      <div class="d-flex align-items-center gap-2 gap-md-3">
        <button id="sidebar-toggle-btn" class="btn btn-sm btn-control <?= $currentUser ? 'd-lg-none' : 'd-none' ?>" aria-label="Toggle sidebar">
          <i class="bi bi-list fs-5"></i>
        </button>
        <a href="<?= BASE_URL ?>/<?= $currentUser ? 'dashboard.php' : 'index.php' ?>" class="app-brand me-1">
          <div class="brand-logo-badge">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <rect x="2" y="2" width="20" height="20" rx="5" fill="#ff5532"/>
              <rect x="4.5" y="4.5" width="15" height="15" rx="3.5" fill="#ffffff" fill-opacity="0.3" stroke="rgba(255,255,255,0.6)" stroke-width="1"/>
              <path d="M8 9H16M12 9V17" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round"/>
              <circle cx="16.5" cy="7.5" r="1.5" fill="#ff7654"/>
            </svg>
          </div>
          <div class="brand-name-wrap">
            <span class="brand-name">Type<span class="brand-accent">Master</span></span>
            <span class="brand-badge-pro">PRO</span>
          </div>
        </a>
        
        <!-- Page Context Breadcrumb Pill -->
        <span class="soft-nav-pill d-none d-md-inline-flex">
          <i class="bi <?= $pageIcon ?>" style="color: var(--first-color);"></i>
          <span><?= e($pageBadgeTitle) ?></span>
        </span>

        <!-- Engine Telemetry Status Dot -->
        <div class="d-none d-xl-flex align-items-center gap-2 soft-status-badge">
          <span class="status-pulse-dot"></span>
          <span class="small fw-bold text-secondary">
            <i class="bi bi-shield-check text-success me-1"></i>Zero Input Lag
          </span>
        </div>
      </div>

      <div class="d-flex align-items-center gap-2 gap-md-2">
        <!-- Quick Action: Practice Arena -->
        <a href="<?= BASE_URL ?>/practice.php" class="btn btn-sm soft-btn-primary d-none d-sm-inline-flex align-items-center gap-1.5 shadow-sm" title="Launch Practice Drill">
          <i class="bi bi-keyboard-fill"></i>
          <span>Practice</span>
        </a>

        <!-- Quick Action: Free Typing & Watch -->
        <a href="<?= BASE_URL ?>/practice.php?mode=free" class="btn btn-sm btn-control d-none d-md-inline-flex align-items-center gap-1.5" title="Free Typing & Watch Timer">
          <i class="bi bi-stopwatch-fill" style="color: var(--first-color);"></i>
          <span>Watch</span>
        </a>

        <!-- Quick Action: Install App on PC -->
        <button type="button" class="btn btn-sm btn-control d-none d-lg-inline-flex align-items-center gap-1.5" id="btn-install-header" title="Install TypeMaster on PC / Desktop">
          <i class="bi bi-laptop" style="color: var(--first-color);"></i>
          <span>Install App</span>
        </button>

        <!-- Theme Picker Dropdown -->
        <?php
          $displayTheme = 'Soft UI White';
          $themeIcon = 'bi-sun-fill';
          $themeIconStyle = 'color: #ff5532 !important;';

          if ($activeTheme === 'orange') {
              $displayTheme = 'Dark Orange';
              $themeIcon = 'bi-fire';
              $themeIconStyle = 'color: #ff6a00 !important;';
          } elseif ($activeTheme === 'dark') {
              $displayTheme = 'Dark Obsidian';
              $themeIcon = 'bi-moon-stars-fill text-primary';
              $themeIconStyle = '';
          } elseif ($activeTheme === 'cyberpunk') {
              $displayTheme = 'Cyberpunk Neon';
              $themeIcon = 'bi-lightning-charge-fill text-danger';
              $themeIconStyle = '';
          } elseif ($activeTheme === 'nord') {
              $displayTheme = 'Nord Arctic';
              $themeIcon = 'bi-snow text-info';
              $themeIconStyle = '';
          }
        ?>
        <div class="dropdown">
          <button class="btn btn-sm btn-control dropdown-toggle d-flex align-items-center gap-1.5" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Switch Theme">
            <i class="bi <?= $themeIcon ?>" style="<?= $themeIconStyle ?>"></i>
            <span class="d-none d-md-inline ms-1"><?= $displayTheme ?></span>
          </button>
          <ul class="dropdown-menu dropdown-menu-end shadow border-secondary">
            <li><h6 class="dropdown-header"><i class="bi bi-palette-fill me-1"></i>Select Color Theme</h6></li>
            <li><a class="dropdown-item d-flex align-items-center gap-2" href="#" data-set-theme="light"><i class="bi bi-sun-fill" style="color: #ff5532 !important;"></i> Soft UI White (Coral)</a></li>
            <li><a class="dropdown-item d-flex align-items-center gap-2" href="#" data-set-theme="orange"><i class="bi bi-fire" style="color: #ff6a00 !important;"></i> Dark Orange (Signature)</a></li>
            <li><a class="dropdown-item d-flex align-items-center gap-2" href="#" data-set-theme="dark"><i class="bi bi-moon-stars-fill text-primary"></i> Dark Obsidian</a></li>
            <li><a class="dropdown-item d-flex align-items-center gap-2" href="#" data-set-theme="cyberpunk"><i class="bi bi-lightning-charge-fill text-danger"></i> Cyberpunk Neon</a></li>
            <li><a class="dropdown-item d-flex align-items-center gap-2" href="#" data-set-theme="nord"><i class="bi bi-snow text-info"></i> Nord Arctic</a></li>
          </ul>
        </div>

        <?php if ($currentUser): ?>
          <!-- User Profile Dropdown -->
          <div class="dropdown">
            <button class="btn p-0 border-0 d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <div class="d-none d-xl-block text-end">
                <div class="fw-bold small text-truncate" style="max-width: 130px; line-height: 1.1; color: var(--text-color);"><?= e($currentUser['full_name']) ?></div>
                <div class="text-muted" style="font-size: 0.72rem;">@<?= e($currentUser['username']) ?></div>
              </div>
              <div class="user-avatar shadow-sm">
                <?= strtoupper(substr($currentUser['username'], 0, 1)) ?>
              </div>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-secondary py-2" style="min-width: 220px;">
              <li class="px-3 py-2 border-bottom border-secondary mb-1">
                <div class="fw-bold text-light"><?= e($currentUser['full_name']) ?></div>
                <div class="small text-muted">@<?= e($currentUser['username']) ?></div>
                <?php if (is_admin()): ?>
                  <span class="badge bg-danger mt-1"><i class="bi bi-shield-lock-fill me-1"></i>Admin</span>
                <?php else: ?>
                  <span class="badge bg-primary-subtle text-primary border border-primary mt-1"><i class="bi bi-patch-check-fill me-1"></i>Member Typist</span>
                <?php endif; ?>
              </li>
              <li><a class="dropdown-item py-1.5" href="<?= BASE_URL ?>/dashboard.php"><i class="bi bi-speedometer2 me-2 text-warning"></i> Dashboard Hub</a></li>
              <li><a class="dropdown-item py-1.5" href="<?= BASE_URL ?>/practice.php?mode=passage"><i class="bi bi-keyboard-fill me-2" style="color: var(--first-color);"></i> Passage Practice</a></li>
              <li><a class="dropdown-item py-1.5" href="<?= BASE_URL ?>/practice.php?mode=free"><i class="bi bi-stopwatch-fill me-2 text-warning"></i> Free Typing &amp; Watch</a></li>
              <li><a class="dropdown-item py-1.5" href="<?= BASE_URL ?>/practice.php?mode=custom"><i class="bi bi-sliders2 me-2 text-info"></i> Custom Time Drills</a></li>
              <li><a class="dropdown-item py-1.5" href="<?= BASE_URL ?>/history.php"><i class="bi bi-award-fill me-2 text-warning"></i> History &amp; Certificates</a></li>
              <li><a class="dropdown-item py-1.5" href="<?= BASE_URL ?>/statistics.php"><i class="bi bi-graph-up-arrow me-2 text-success"></i> Speed Analytics</a></li>
              <li><a class="dropdown-item py-1.5" href="<?= BASE_URL ?>/settings.php"><i class="bi bi-gear-fill me-2 text-secondary"></i> Customization</a></li>
              <?php if (is_admin()): ?>
                <li><hr class="dropdown-divider border-secondary"></li>
                <li><a class="dropdown-item py-1.5 text-warning" href="<?= BASE_URL ?>/admin/index.php"><i class="bi bi-shield-lock-fill me-2"></i> Admin Panel</a></li>
              <?php endif; ?>
              <li><hr class="dropdown-divider border-secondary"></li>
              <li><a class="dropdown-item py-1.5 text-danger" href="<?= BASE_URL ?>/logout.php"><i class="bi bi-box-arrow-right me-2"></i> Sign Out</a></li>
            </ul>
          </div>
        <?php else: ?>
          <a href="<?= BASE_URL ?>/login.php" class="btn btn-sm btn-control d-inline-flex align-items-center gap-1">
            <i class="bi bi-box-arrow-in-right"></i>
            <span>Sign In</span>
          </a>
          <a href="<?= BASE_URL ?>/register.php" class="btn btn-sm soft-btn-primary d-inline-flex align-items-center gap-1">
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
