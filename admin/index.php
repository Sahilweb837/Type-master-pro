<?php
/**
 * Custom Type Master Pro
 * Admin Dashboard Overview â€” Black & White Design
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/functions.php';

require_admin();

$pdo = getDBConnection();
$currentUser = current_user();

$totalUsers    = 0;
$totalTests    = 0;
$totalPassages = 0;
$systemAvgWpm  = 0.0;
$recentUsers   = [];
$recentTests   = [];

if ($pdo) {
    try {
        $totalUsers    = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $totalTests    = (int)$pdo->query("SELECT COUNT(*) FROM typing_results")->fetchColumn();
        $totalPassages = (int)$pdo->query("SELECT COUNT(*) FROM typing_passages")->fetchColumn();
        $systemAvgWpm  = round((float)$pdo->query("SELECT COALESCE(AVG(net_wpm), 0) FROM typing_results")->fetchColumn(), 1);

        $recentUsers = $pdo->query("
            SELECT id, username, email, role, created_at
            FROM users ORDER BY created_at DESC LIMIT 6
        ")->fetchAll();

        $recentTests = $pdo->query("
            SELECT r.*, u.username, p.title AS passage_title
            FROM typing_results r
            JOIN users u ON r.user_id = u.id
            LEFT JOIN typing_passages p ON r.passage_id = p.id
            ORDER BY r.created_at DESC LIMIT 6
        ")->fetchAll();

    } catch (Exception $e) {
        error_log('Admin overview error: ' . $e->getMessage());
    }
}

$pageTitle = 'Admin Overview';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex">
  <title>Admin Overview â€” Type Master Pro</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin.css">
</head>
<body class="adm-page">

<div class="adm-wrap">

  <!-- â”€â”€ Sidebar â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ -->
  <aside class="adm-sidebar" id="admSidebar">

    <a href="<?= BASE_URL ?>/dashboard.php" class="adm-brand">
      <div class="adm-brand-icon">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
          <rect x="2" y="2" width="20" height="20" rx="5" fill="#0a0a0a"/>
          <path d="M8 9H16M12 9V17" stroke="#fff" stroke-width="2.2" stroke-linecap="round"/>
          <circle cx="16.5" cy="7.5" r="1.5" fill="#888"/>
        </svg>
      </div>
      <div>
        <div class="adm-brand-name">TypeMaster Pro</div>
        <div class="adm-brand-sub">Admin Panel</div>
      </div>
    </a>

    <nav class="adm-nav">
      <div class="adm-nav-section">Overview</div>
      <a href="<?= BASE_URL ?>/admin/index.php" class="adm-nav-link active">
        <i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span>
      </a>

      <div class="adm-nav-section">Management</div>
      <a href="<?= BASE_URL ?>/admin/users.php" class="adm-nav-link">
        <i class="bi bi-people-fill"></i><span>Users</span>
      </a>
      <a href="<?= BASE_URL ?>/admin/passages.php" class="adm-nav-link">
        <i class="bi bi-journal-text"></i><span>Passages</span>
      </a>

      <div class="adm-nav-section">Platform</div>
      <a href="<?= BASE_URL ?>/dashboard.php" class="adm-nav-link">
        <i class="bi bi-speedometer2"></i><span>User Dashboard</span>
      </a>
      <a href="<?= BASE_URL ?>/statistics.php" class="adm-nav-link">
        <i class="bi bi-graph-up-arrow"></i><span>Statistics</span>
      </a>
      <a href="<?= BASE_URL ?>/practice.php" class="adm-nav-link">
        <i class="bi bi-keyboard-fill"></i><span>Practice</span>
      </a>
    </nav>

    <div class="adm-sidebar-user">
      <div class="adm-sidebar-avatar"><?= strtoupper(substr($currentUser['username'] ?? 'A', 0, 1)) ?></div>
      <div class="adm-sidebar-user-info">
        <div class="adm-sidebar-user-name"><?= e($currentUser['full_name'] ?? $currentUser['username'] ?? 'Admin') ?></div>
        <div class="adm-sidebar-user-role">Administrator</div>
      </div>
      <a href="<?= BASE_URL ?>/logout.php" class="adm-sidebar-logout" title="Sign out">
        <i class="bi bi-box-arrow-right"></i>
      </a>
    </div>
  </aside>

  <!-- Mobile overlay -->
  <div class="adm-overlay" id="admOverlay"></div>

  <!-- â”€â”€ Main â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ -->
  <div class="adm-main">

    <!-- Navbar -->
    <header class="adm-navbar">
      <div class="adm-navbar-left">
        <button class="adm-toggle-btn" id="admToggle" aria-label="Toggle sidebar">
          <i class="bi bi-list"></i>
        </button>
        <div>
          <div class="adm-page-title">System Overview</div>
          <div class="adm-breadcrumb">
            <a href="<?= BASE_URL ?>/admin/index.php">Admin</a>
            <span class="sep">/</span>
            <span>Dashboard</span>
          </div>
        </div>
      </div>
      <div class="adm-navbar-right">
        <a href="<?= BASE_URL ?>/admin/passages.php" class="adm-btn adm-btn-ghost adm-btn-sm">
          <i class="bi bi-plus-circle"></i> Passages
        </a>
        <a href="<?= BASE_URL ?>/admin/users.php" class="adm-btn adm-btn-primary adm-btn-sm">
          <i class="bi bi-people-fill"></i> Users
        </a>
        <div class="adm-nav-avatar" title="<?= e($currentUser['username'] ?? 'Admin') ?>">
          <?= strtoupper(substr($currentUser['username'] ?? 'A', 0, 1)) ?>
        </div>
      </div>
    </header>

    <!-- Body -->
    <main class="adm-body">

      <!-- Page header -->
      <div class="adm-page-header">
        <div class="adm-page-header-text">
          <div class="adm-header-eyebrow">
            <i class="bi bi-shield-lock-fill"></i> Administrator Access
          </div>
          <h1>System Overview</h1>
          <p>Monitor platform usage, registered typists, passages and global performance.</p>
        </div>
        <div class="adm-header-actions">
          <a href="<?= BASE_URL ?>/admin/users.php" class="adm-btn adm-btn-secondary adm-btn-sm">
            <i class="bi bi-people"></i> Manage Users
          </a>
          <a href="<?= BASE_URL ?>/admin/passages.php" class="adm-btn adm-btn-primary adm-btn-sm">
            <i class="bi bi-journal-plus"></i> Add Passage
          </a>
        </div>
      </div>

      <?php $flash = get_flash(); if ($flash): ?>
        <div class="adm-alert adm-alert-<?= $flash['type'] === 'success' ? 'success' : ($flash['type'] === 'danger' ? 'danger' : 'info') ?>">
          <i class="bi bi-info-circle-fill"></i>
          <?= e($flash['message']) ?>
        </div>
      <?php endif; ?>

      <!-- Stat Cards -->
      <div class="adm-stats-grid">

        <div class="adm-stat-card adm-animate">
          <div class="adm-stat-top">
            <div class="adm-stat-label">Total Typists</div>
            <div class="adm-stat-icon"><i class="bi bi-people-fill"></i></div>
          </div>
          <div class="adm-stat-value"><?= number_format($totalUsers) ?></div>
          <div class="adm-stat-sub">Registered accounts</div>
        </div>

        <div class="adm-stat-card adm-animate">
          <div class="adm-stat-top">
            <div class="adm-stat-label">Tests Taken</div>
            <div class="adm-stat-icon"><i class="bi bi-check2-all"></i></div>
          </div>
          <div class="adm-stat-value"><?= number_format($totalTests) ?></div>
          <div class="adm-stat-sub">Completed drills</div>
        </div>

        <div class="adm-stat-card adm-animate">
          <div class="adm-stat-top">
            <div class="adm-stat-label">Passages</div>
            <div class="adm-stat-icon"><i class="bi bi-journal-text"></i></div>
          </div>
          <div class="adm-stat-value"><?= number_format($totalPassages) ?></div>
          <div class="adm-stat-sub">Practice passages</div>
        </div>

        <div class="adm-stat-card adm-animate">
          <div class="adm-stat-top">
            <div class="adm-stat-label">Avg Net WPM</div>
            <div class="adm-stat-icon"><i class="bi bi-speedometer2"></i></div>
          </div>
          <div class="adm-stat-value"><?= $systemAvgWpm ?></div>
          <div class="adm-stat-sub">Global platform average</div>
        </div>

      </div><!-- /stats -->

      <!-- Tables Row -->
      <div class="adm-grid-2">

        <!-- Recent Users -->
        <div class="adm-card adm-animate">
          <div class="adm-card-header">
            <div class="adm-card-title">
              <i class="bi bi-person-plus"></i> Recent Users
            </div>
            <a href="<?= BASE_URL ?>/admin/users.php" class="adm-btn adm-btn-ghost adm-btn-sm">View all</a>
          </div>
          <div class="adm-table-wrap">
            <table class="adm-table">
              <thead>
                <tr>
                  <th>User</th>
                  <th>Role</th>
                  <th>Joined</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($recentUsers)): ?>
                  <tr><td colspan="3"><div class="adm-empty"><i class="bi bi-people"></i><p>No users yet.</p></div></td></tr>
                <?php else: ?>
                  <?php foreach ($recentUsers as $u): ?>
                    <tr>
                      <td>
                        <div class="adm-td-primary"><?= e($u['username']) ?></div>
                        <div class="adm-td-sub"><?= e($u['email']) ?></div>
                      </td>
                      <td>
                        <span class="adm-badge <?= $u['role'] === 'admin' ? 'adm-badge-admin' : 'adm-badge-user' ?>">
                          <?= ucfirst($u['role']) ?>
                        </span>
                      </td>
                      <td style="color:var(--adm-text-3); font-size:0.78rem;"><?= time_ago($u['created_at']) ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Recent Tests -->
        <div class="adm-card adm-animate">
          <div class="adm-card-header">
            <div class="adm-card-title">
              <i class="bi bi-activity"></i> Recent Tests
            </div>
            <span style="font-size:0.76rem; color:var(--adm-text-3);">Latest activity</span>
          </div>
          <div class="adm-table-wrap">
            <table class="adm-table">
              <thead>
                <tr>
                  <th>Typist</th>
                  <th>Passage</th>
                  <th>WPM</th>
                  <th>Acc%</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($recentTests)): ?>
                  <tr><td colspan="4"><div class="adm-empty"><i class="bi bi-activity"></i><p>No tests yet.</p></div></td></tr>
                <?php else: ?>
                  <?php foreach ($recentTests as $rt): ?>
                    <tr>
                      <td class="adm-td-primary">@<?= e($rt['username']) ?></td>
                      <td style="max-width:130px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; color:var(--adm-text-3); font-size:0.8rem;">
                        <?= e($rt['passage_title'] ?: 'Custom') ?>
                      </td>
                      <td><span class="adm-badge-mono"><?= (float)$rt['net_wpm'] ?></span></td>
                      <td><span class="adm-badge-mono"><?= (float)$rt['accuracy'] ?>%</span></td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

      </div><!-- /grid-2 -->

    </main><!-- /adm-body -->
  </div><!-- /adm-main -->
</div><!-- /adm-wrap -->

<script>
  // Sidebar toggle (mobile)
  const sidebar  = document.getElementById('admSidebar');
  const overlay  = document.getElementById('admOverlay');
  const toggle   = document.getElementById('admToggle');
  const close = () => { sidebar.classList.remove('open'); overlay.classList.remove('active'); };
  toggle?.addEventListener('click', () => {
    const open = sidebar.classList.toggle('open');
    overlay.classList.toggle('active', open);
  });
  overlay?.addEventListener('click', close);
</script>
</body>
</html>
