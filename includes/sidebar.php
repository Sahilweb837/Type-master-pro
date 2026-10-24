<?php
/**
 * TypoPrac — Professional Typing Practice Software
 * Sidebar Navigation Template
 */

$currentPage = basename($_SERVER['PHP_SELF'] ?? '');
$currentDir = basename(dirname($_SERVER['PHP_SELF'] ?? ''));
$isAdminSection = ($currentDir === 'admin');
?>
<aside class="app-sidebar">
  <div class="sidebar-header border-bottom px-3 py-2 d-flex align-items-center" style="border-color: var(--border-color) !important; min-height: 64px;">
    <a href="<?= BASE_URL ?>/dashboard.php" class="app-brand text-decoration-none d-flex align-items-center gap-2 w-100">
      <img src="<?= BASE_URL ?>/assets/images/logo.png" alt="TypoPrac Logo" class="typoprac-brand-img" style="height: 38px; max-width: 190px; object-fit: contain;">
    </a>
  </div>

  <div class="sidebar-menu">
    <div class="nav-section-title">Navigation</div>
    <a href="<?= BASE_URL ?>/dashboard.php" class="sidebar-link <?= ($currentPage === 'dashboard.php') ? 'active' : '' ?>">
      <i class="bi bi-house-door-fill"></i>
      <span>Dashboard</span>
    </a>
    <a href="<?= BASE_URL ?>/practice.php?mode=passage" class="sidebar-link <?= ($currentPage === 'practice.php' && (!isset($_GET['mode']) || $_GET['mode'] === 'passage')) ? 'active' : '' ?>">
      <i class="bi bi-keyboard-fill"></i>
      <span>Practice</span>
    </a>
    <a href="<?= BASE_URL ?>/practice.php?mode=custom" class="sidebar-link <?= ($currentPage === 'practice.php' && isset($_GET['mode']) && $_GET['mode'] === 'custom') ? 'active' : '' ?>">
      <i class="bi bi-speedometer2"></i>
      <span>Typing Test</span>
    </a>
    <a href="<?= BASE_URL ?>/lessons.php" class="sidebar-link <?= ($currentPage === 'lessons.php' || $currentPage === 'lesson-player.php') ? 'active' : '' ?>">
      <i class="bi bi-book-fill"></i>
      <span>Lessons</span>
      <span class="badge ms-auto" style="font-size: 0.62rem; font-weight: 800; background: var(--first-color); color: #fff; padding: 2px 6px; border-radius: 4px;">PRO</span>
    </a>
    <a href="<?= BASE_URL ?>/games.php" class="sidebar-link <?= in_array($currentPage, ['games.php', 'game-ninja.php', 'game-space.php', 'game-racer.php', 'game-balloon.php', 'game-meteor.php', 'game-kindergarten.php']) ? 'active' : '' ?>">
      <i class="bi bi-controller"></i>
      <span>Games</span>
      <span class="badge ms-auto" style="font-size: 0.62rem; font-weight: 800; background: linear-gradient(135deg, #ec4899, #ff5722); color: #fff; padding: 2px 6px; border-radius: 4px;">KIDS &amp; ARCADE</span>
    </a>
    <a href="<?= BASE_URL ?>/statistics.php" class="sidebar-link <?= ($currentPage === 'statistics.php') ? 'active' : '' ?>">
      <i class="bi bi-bar-chart-line-fill"></i>
      <span>Progress</span>
    </a>
    <a href="<?= BASE_URL ?>/history.php" class="sidebar-link <?= ($currentPage === 'history.php' || $currentPage === 'results.php') ? 'active' : '' ?>">
      <i class="bi bi-award-fill"></i>
      <span>Certificates</span>
    </a>
    <a href="<?= BASE_URL ?>/settings.php" class="sidebar-link <?= ($currentPage === 'settings.php') ? 'active' : '' ?>">
      <i class="bi bi-gear-fill"></i>
      <span>Settings</span>
    </a>

    <?php if (is_admin()): ?>
      <div class="nav-section-title text-warning mt-2">Administration</div>
      <a href="<?= BASE_URL ?>/admin/index.php" class="sidebar-link <?= ($isAdminSection && $currentPage === 'index.php') ? 'active' : '' ?>">
        <i class="bi bi-shield-check"></i>
        <span>Admin Panel</span>
      </a>
      <a href="<?= BASE_URL ?>/admin/users.php" class="sidebar-link <?= ($currentPage === 'users.php') ? 'active' : '' ?>">
        <i class="bi bi-people-fill"></i>
        <span>User Accounts</span>
      </a>
    <?php endif; ?>

    <!-- Go Premium Widget (from mockup) -->
    <div class="sidebar-premium-card p-3 mx-2 mt-3 rounded-3" style="background: linear-gradient(145deg, #182236 0%, #0e1422 100%); border: 1px solid rgba(255, 87, 34, 0.25);">
      <div class="d-flex align-items-center gap-2 mb-1">
        <i class="bi bi-crown-fill text-warning"></i>
        <span class="fw-bold small text-white">Go Premium</span>
      </div>
      <p class="text-secondary mb-3" style="font-size: 0.73rem; line-height: 1.4;">Unlock advanced lessons, detailed analytics and more.</p>
      <a href="<?= BASE_URL ?>/practice.php?mode=passage" class="btn btn-sm w-100 py-1.5 fw-bold text-white rounded-2 d-flex align-items-center justify-content-center gap-1" style="background: linear-gradient(135deg, #ff5500 0%, #ff7a00 100%); box-shadow: 0 4px 14px rgba(255, 87, 34, 0.45); font-size: 0.78rem;">
        Upgrade Now
      </a>
    </div>

    <!-- Install Desktop PC App Button -->
    <div class="mt-auto px-2 pt-2 pb-1">
      <button type="button" class="btn btn-sm w-100 py-1.5 d-flex align-items-center justify-content-center gap-2 rounded-2" id="btn-install-pc" style="background: rgba(255, 255, 255, 0.04); color: var(--text-secondary); border: 1px solid var(--border-color); font-weight: 600; font-size: 0.75rem;">
        <i class="bi bi-laptop"></i>
        <span>Install PC App</span>
      </button>
    </div>
  </div>

  <div class="sidebar-user">
    <div class="user-avatar" style="background: linear-gradient(135deg, #ff5500 0%, #ff7a00 100%); color: #fff; font-weight: 800;">
      <?= strtoupper(substr($currentUser['username'] ?? 'U', 0, 1)) ?>
    </div>
    <div class="flex-grow-1 text-truncate">
      <div class="fw-bold text-light small text-truncate"><?= e($currentUser['full_name'] ?? 'Nitin Kapoor') ?></div>
      <div class="text-muted" style="font-size: 0.75rem;">Typing Learner</div>
    </div>
    <a href="<?= BASE_URL ?>/logout.php" class="btn btn-sm text-secondary" title="Sign Out">
      <i class="bi bi-box-arrow-right fs-5"></i>
    </a>
  </div>
</aside>
