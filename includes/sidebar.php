<?php
/**
 * Custom Type Master Pro
 * Sidebar Navigation Template
 */

$currentPage = basename($_SERVER['PHP_SELF'] ?? '');
$currentDir = basename(dirname($_SERVER['PHP_SELF'] ?? ''));
$isAdminSection = ($currentDir === 'admin');
?>
<aside class="app-sidebar">
  <div class="sidebar-header">
    <a href="<?= BASE_URL ?>/dashboard.php" class="app-brand">
      <div class="brand-logo-badge">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <rect x="2" y="2" width="20" height="20" rx="5" fill="#ff6a00"/>
          <rect x="4.5" y="4.5" width="15" height="15" rx="3.5" fill="#0a0e17" fill-opacity="0.55" stroke="rgba(255,255,255,0.3)" stroke-width="1"/>
          <path d="M8 9H16M12 9V17" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round"/>
          <circle cx="16.5" cy="7.5" r="1.5" fill="#ff8c33"/>
        </svg>
      </div>
      <div class="brand-name-wrap">
        <span class="brand-name">Type<span class="brand-accent">Master</span></span>
        <span class="brand-badge-pro">PRO</span>
      </div>
    </a>
  </div>

  <div class="sidebar-menu">
    <div class="nav-section-title">Training & Speed</div>
    <a href="<?= BASE_URL ?>/practice.php?mode=passage" class="sidebar-link <?= ($currentPage === 'practice.php' && (!isset($_GET['mode']) || $_GET['mode'] === 'passage')) ? 'active' : '' ?>">
      <i class="bi bi-keyboard-fill"></i>
      <span>Passage Practice</span>
    </a>
    <a href="<?= BASE_URL ?>/practice.php?mode=free" class="sidebar-link <?= ($currentPage === 'practice.php' && isset($_GET['mode']) && $_GET['mode'] === 'free') ? 'active' : '' ?>">
      <i class="bi bi-stopwatch-fill" style="color: var(--first-color);"></i>
      <span>Free Typing &amp; Watch</span>
    </a>
    <a href="<?= BASE_URL ?>/practice.php?mode=custom" class="sidebar-link <?= ($currentPage === 'practice.php' && isset($_GET['mode']) && $_GET['mode'] === 'custom') ? 'active' : '' ?>">
      <i class="bi bi-sliders2"></i>
      <span>Custom Time Drills</span>
    </a>

    <div class="nav-section-title">Analytics & Activity</div>
    <a href="<?= BASE_URL ?>/dashboard.php" class="sidebar-link <?= ($currentPage === 'dashboard.php') ? 'active' : '' ?>">
      <i class="bi bi-speedometer2"></i>
      <span>Dashboard</span>
    </a>
    <a href="<?= BASE_URL ?>/statistics.php" class="sidebar-link <?= ($currentPage === 'statistics.php') ? 'active' : '' ?>">
      <i class="bi bi-graph-up-arrow"></i>
      <span>Speed &amp; Accuracy</span>
    </a>
    <a href="<?= BASE_URL ?>/history.php" class="sidebar-link <?= ($currentPage === 'history.php' || $currentPage === 'results.php') ? 'active' : '' ?>">
      <i class="bi bi-clock-history"></i>
      <span>Practice History</span>
    </a>

    <div class="nav-section-title">Preferences</div>
    <a href="<?= BASE_URL ?>/settings.php" class="sidebar-link <?= ($currentPage === 'settings.php') ? 'active' : '' ?>">
      <i class="bi bi-palette"></i>
      <span>Customization</span>
    </a>
    <a href="<?= BASE_URL ?>/export.php" class="sidebar-link <?= ($currentPage === 'export.php') ? 'active' : '' ?>">
      <i class="bi bi-download"></i>
      <span>Export Data</span>
    </a>

    <?php if (is_admin()): ?>
      <div class="nav-section-title text-warning">Administration</div>
      <a href="<?= BASE_URL ?>/admin/index.php" class="sidebar-link <?= ($isAdminSection && $currentPage === 'index.php') ? 'active' : '' ?>">
        <i class="bi bi-shield-check"></i>
        <span>Admin Overview</span>
      </a>
      <a href="<?= BASE_URL ?>/admin/users.php" class="sidebar-link <?= ($currentPage === 'users.php') ? 'active' : '' ?>">
        <i class="bi bi-people-fill"></i>
        <span>User Accounts</span>
      </a>
      <a href="<?= BASE_URL ?>/admin/passages.php" class="sidebar-link <?= ($currentPage === 'passages.php') ? 'active' : '' ?>">
        <i class="bi bi-journal-text"></i>
        <span>Typing Passages</span>
      </a>
    <?php endif; ?>

    <!-- Install Desktop PC App Button -->
    <div class="mt-auto px-2 pt-3 pb-1">
      <button type="button" class="btn btn-sm w-100 py-2 d-flex align-items-center justify-content-center gap-2 rounded-3" id="btn-install-pc" style="background: var(--bg-card); color: var(--first-color); border: 1px solid var(--border-color); box-shadow: var(--soft-flat); font-weight: 700; font-size: 0.8rem;">
        <i class="bi bi-laptop fs-6"></i>
        <span>Install on PC / App</span>
      </button>
    </div>
  </div>

  <div class="sidebar-user">
    <div class="user-avatar">
      <?= strtoupper(substr($currentUser['username'] ?? 'U', 0, 1)) ?>
    </div>
    <div class="flex-grow-1 text-truncate">
      <div class="fw-bold text-light small text-truncate"><?= e($currentUser['full_name'] ?? 'User') ?></div>
      <div class="text-muted" style="font-size: 0.75rem;">@<?= e($currentUser['username'] ?? 'typist') ?></div>
    </div>
    <a href="<?= BASE_URL ?>/logout.php" class="btn btn-sm text-secondary" title="Sign Out">
      <i class="bi bi-box-arrow-right fs-5"></i>
    </a>
  </div>
</aside>
