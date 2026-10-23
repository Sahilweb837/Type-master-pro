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
  <div class="sidebar-header border-bottom px-3 d-flex align-items-center" style="border-color: var(--border-color) !important;">
    <a href="<?= BASE_URL ?>/dashboard.php" class="app-brand text-decoration-none d-flex align-items-center gap-2">
      <div class="brand-logo-badge d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #0a0a0a; border-radius: 8px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <rect x="2" y="2" width="20" height="20" rx="4" fill="#0a0a0a" stroke="#ffffff" stroke-width="1.8"/>
          <path d="M7 9H17M12 9V17" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round"/>
        </svg>
      </div>
      <div class="brand-name-wrap d-flex align-items-baseline gap-1">
        <span class="brand-name fw-bold" style="color: var(--text-primary); letter-spacing: -0.03em; font-size: 1.05rem;">TypeMaster</span>
        <span class="badge bg-black text-white px-1.5 py-0.5 rounded" style="font-size: 0.62rem; font-weight: 800; letter-spacing: 0.05em;">PRO</span>
      </div>
    </a>
  </div>

  <div class="sidebar-menu">
    <div class="nav-section-title">Training & Speed</div>
    <a href="<?= BASE_URL ?>/lessons.php" class="sidebar-link <?= ($currentPage === 'lessons.php' || $currentPage === 'lesson-player.php') ? 'active' : '' ?>">
      <i class="bi bi-mortarboard-fill" style="color: var(--first-color);"></i>
      <span>Typing Lessons</span>
      <span class="badge ms-auto" style="font-size: 0.62rem; font-weight: 800; background: var(--first-color); color: #fff; padding: 2px 6px; border-radius: 4px;">NEW</span>
    </a>
    <a href="<?= BASE_URL ?>/games.php" class="sidebar-link <?= in_array($currentPage, ['games.php', 'game-ninja.php', 'game-space.php', 'game-racer.php', 'game-balloon.php', 'game-meteor.php']) ? 'active' : '' ?>">
      <i class="bi bi-controller" style="color: #ff3366;"></i>
      <span>Arcade Games</span>
      <span class="badge ms-auto" style="font-size: 0.62rem; font-weight: 800; background: #ff3366; color: #fff; padding: 2px 6px; border-radius: 4px;">ARCADE</span>
    </a>
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
      <button type="button" class="btn btn-sm w-100 py-2 d-flex align-items-center justify-content-center gap-2 rounded-2" id="btn-install-pc" style="background: var(--bg-card); color: var(--text-primary); border: 1px solid var(--border-color); font-weight: 700; font-size: 0.8rem;">
        <i class="bi bi-laptop fs-6"></i>
        <span>Install PC App</span>
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
