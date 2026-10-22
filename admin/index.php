<?php
/**
 * Custom Type Master Pro
 * Admin Dashboard Overview
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/functions.php';

require_admin();

$pdo = getDBConnection();
$totalUsers = 0;
$totalTests = 0;
$totalPassages = 0;
$systemAvgWpm = 0.0;
$recentUsers = [];
$recentTests = [];

if ($pdo) {
    try {
        $totalUsers = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $totalTests = (int)$pdo->query("SELECT COUNT(*) FROM typing_results")->fetchColumn();
        $totalPassages = (int)$pdo->query("SELECT COUNT(*) FROM typing_passages")->fetchColumn();
        $systemAvgWpm = round((float)$pdo->query("SELECT COALESCE(AVG(net_wpm), 0) FROM typing_results")->fetchColumn(), 1);

        // Recent users
        $recentUsers = $pdo->query("
            SELECT id, username, email, role, created_at 
            FROM users 
            ORDER BY created_at DESC 
            LIMIT 5
        ")->fetchAll();

        // Recent tests
        $recentTests = $pdo->query("
            SELECT r.*, u.username, p.title AS passage_title 
            FROM typing_results r
            JOIN users u ON r.user_id = u.id
            LEFT JOIN typing_passages p ON r.passage_id = p.id
            ORDER BY r.created_at DESC 
            LIMIT 5
        ")->fetchAll();

    } catch (Exception $e) {
        error_log('Admin overview error: ' . $e->getMessage());
    }
}

$pageTitle = 'Administrator Overview';
include __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid px-md-4 py-3">

  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
      <div class="badge bg-danger-subtle text-danger border border-danger mb-1 px-3 py-1">
        <i class="bi bi-shield-lock-fill me-1"></i> Administrator Access Only
      </div>
      <h2 class="fw-bold text-light mb-1">System Overview</h2>
      <p class="text-secondary small mb-0">Monitor platform usage, registered typists, passages, and global performance.</p>
    </div>
    <div class="d-flex gap-2">
      <a href="<?= BASE_URL ?>/admin/passages.php" class="btn btn-outline-info btn-sm">
        <i class="bi bi-plus-circle me-1"></i> Manage Passages
      </a>
      <a href="<?= BASE_URL ?>/admin/users.php" class="btn btn-primary btn-sm fw-bold">
        <i class="bi bi-people-fill me-1"></i> Manage Users
      </a>
    </div>
  </div>

  <!-- Metric Cards -->
  <div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
      <div class="glass-card stat-card h-100">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div class="stat-icon" style="background: rgba(56, 189, 248, 0.15); color: #38bdf8;">
            <i class="bi bi-people-fill"></i>
          </div>
          <span class="text-muted small">Registered</span>
        </div>
        <div class="stat-value"><?= number_format($totalUsers) ?></div>
        <div class="stat-label">Total Typists</div>
      </div>
    </div>

    <div class="col-sm-6 col-xl-3">
      <div class="glass-card stat-card h-100">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div class="stat-icon" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">
            <i class="bi bi-check2-all"></i>
          </div>
          <span class="text-muted small">Completed</span>
        </div>
        <div class="stat-value text-success"><?= number_format($totalTests) ?></div>
        <div class="stat-label">Total Tests Taken</div>
      </div>
    </div>

    <div class="col-sm-6 col-xl-3">
      <div class="glass-card stat-card h-100">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div class="stat-icon" style="background: rgba(168, 85, 247, 0.15); color: #a855f7;">
            <i class="bi bi-journal-text"></i>
          </div>
          <span class="text-muted small">Content</span>
        </div>
        <div class="stat-value text-info"><?= number_format($totalPassages) ?></div>
        <div class="stat-label">Practice Passages</div>
      </div>
    </div>

    <div class="col-sm-6 col-xl-3">
      <div class="glass-card stat-card h-100">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div class="stat-icon" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b;">
            <i class="bi bi-speedometer2"></i>
          </div>
          <span class="text-muted small">Global Avg</span>
        </div>
        <div class="stat-value text-warning"><?= $systemAvgWpm ?></div>
        <div class="stat-label">Average Net WPM</div>
      </div>
    </div>
  </div>

  <div class="row g-4 mb-4">
    <!-- Recent Registered Users -->
    <div class="col-lg-6">
      <div class="glass-card p-4 h-100">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <h5 class="fw-bold text-light mb-0"><i class="bi bi-person-plus text-info me-2"></i>Recent Users</h5>
          <a href="<?= BASE_URL ?>/admin/users.php" class="btn btn-sm btn-outline-secondary">View All</a>
        </div>

        <div class="table-responsive">
          <table class="custom-table mb-0">
            <thead>
              <tr>
                <th>User</th>
                <th>Role</th>
                <th>Registered</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recentUsers as $u): ?>
                <tr>
                  <td>
                    <div class="fw-bold text-light"><?= e($u['username']) ?></div>
                    <div class="text-muted small"><?= e($u['email']) ?></div>
                  </td>
                  <td>
                    <span class="badge <?= ($u['role'] === 'admin') ? 'bg-danger' : 'bg-secondary' ?>">
                      <?= ucfirst($u['role']) ?>
                    </span>
                  </td>
                  <td class="text-muted small"><?= time_ago($u['created_at']) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Recent Global Tests -->
    <div class="col-lg-6">
      <div class="glass-card p-4 h-100">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <h5 class="fw-bold text-light mb-0"><i class="bi bi-activity text-success me-2"></i>Recent Global Tests</h5>
          <span class="text-muted small">Latest user drills</span>
        </div>

        <div class="table-responsive">
          <table class="custom-table mb-0">
            <thead>
              <tr>
                <th>Typist</th>
                <th>Passage</th>
                <th>Net WPM</th>
                <th>Accuracy</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recentTests as $rt): ?>
                <tr>
                  <td class="fw-bold text-light">@<?= e($rt['username']) ?></td>
                  <td class="text-truncate" style="max-width: 150px;"><?= e($rt['passage_title'] ?: 'Custom') ?></td>
                  <td><span class="fw-bold text-info font-monospace"><?= (float)$rt['net_wpm'] ?></span></td>
                  <td><span class="font-monospace text-success"><?= (float)$rt['accuracy'] ?>%</span></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
