<?php
/**
 * Custom Type Master Pro
 * User Dashboard - Software-Grade Hub
 */

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/functions.php';

require_login();

$user = current_user();
$userId = $user['id'];
$pdo = getDBConnection();

// Initialize stats
$totalTests = 0;
$avgWpm = 0.0;
$bestWpm = 0.0;
$avgAccuracy = 0.0;
$totalSeconds = 0;
$totalWords = 0;
$recentTests = [];
$weeklyChartData = [];

if ($pdo) {
    try {
        // Aggregated summary stats
        $stmt = $pdo->prepare("
            SELECT 
                COUNT(*) AS total_tests,
                COALESCE(AVG(net_wpm), 0) AS avg_wpm,
                COALESCE(MAX(net_wpm), 0) AS best_wpm,
                COALESCE(AVG(accuracy), 0) AS avg_accuracy,
                COALESCE(SUM(elapsed_seconds), 0) AS total_seconds,
                COALESCE(SUM(words_typed), 0) AS total_words
            FROM typing_results
            WHERE user_id = :uid
        ");
        $stmt->execute([':uid' => $userId]);
        $stats = $stmt->fetch();

        if ($stats) {
            $totalTests = (int)$stats['total_tests'];
            $avgWpm = round((float)$stats['avg_wpm'], 1);
            $bestWpm = round((float)$stats['best_wpm'], 1);
            $avgAccuracy = round((float)$stats['avg_accuracy'], 1);
            $totalSeconds = (int)$stats['total_seconds'];
            $totalWords = (int)$stats['total_words'];
        }

        // Recent practice sessions (last 6)
        $recentStmt = $pdo->prepare("
            SELECT r.*, p.title AS passage_title 
            FROM typing_results r
            LEFT JOIN typing_passages p ON r.passage_id = p.id
            WHERE r.user_id = :uid
            ORDER BY r.created_at DESC
            LIMIT 6
        ");
        $recentStmt->execute([':uid' => $userId]);
        $recentTests = $recentStmt->fetchAll();

        // Weekly progress chart data (last 14 completed tests)
        $chartStmt = $pdo->prepare("
            SELECT DATE_FORMAT(created_at, '%b %d') AS test_day, net_wpm, accuracy 
            FROM typing_results
            WHERE user_id = :uid
            ORDER BY created_at ASC
            LIMIT 14
        ");
        $chartStmt->execute([':uid' => $userId]);
        $weeklyChartData = $chartStmt->fetchAll();

    } catch (Exception $e) {
        error_log('Dashboard data fetch error: ' . $e->getMessage());
    }
}

$rating = get_speed_rating($bestWpm);
$userLevel = min(50, max(1, (int)floor($bestWpm / 3) + (int)floor($totalTests / 5)));
$lastSession = !empty($recentTests) ? $recentTests[0] : null;

// Determine next milestone
$nextMilestoneWpm = 40;
$nextMilestoneLabel = '40+ WPM Club';
if ($bestWpm >= 80) {
    $nextMilestoneWpm = 100;
    $nextMilestoneLabel = 'Typing God (100+ WPM)';
} elseif ($bestWpm >= 60) {
    $nextMilestoneWpm = 80;
    $nextMilestoneLabel = 'Grandmaster (80+ WPM)';
} elseif ($bestWpm >= 40) {
    $nextMilestoneWpm = 60;
    $nextMilestoneLabel = 'Professional (60+ WPM)';
}
$milestoneProgressPercent = min(100, max(5, round(($bestWpm / $nextMilestoneWpm) * 100)));

$pageTitle = 'Dashboard';
include __DIR__ . '/includes/header.php';
?>

<div class="container-fluid px-md-4 py-3">

  <!-- =====================================================================
       LAZY SKELETON LOADING SHIMMER WRAPPER
       (Displayed during initial render, seamlessly replaced when ready)
       ===================================================================== -->
  <div id="dashboard-skeleton-view" class="skeleton-wrapper">
    <!-- Skeleton Hero -->
    <div class="glass-card p-4 mb-4 skeleton-card d-flex flex-column gap-3">
      <div class="skeleton skeleton-title" style="width: 35%;"></div>
      <div class="skeleton skeleton-text" style="width: 60%;"></div>
      <div class="d-flex gap-2 mt-2">
        <div class="skeleton" style="width: 160px; height: 42px; border-radius: 8px;"></div>
        <div class="skeleton" style="width: 140px; height: 42px; border-radius: 8px;"></div>
      </div>
    </div>

    <!-- Skeleton Stat Cards -->
    <div class="row g-3 mb-4">
      <?php for ($i = 0; $i < 6; $i++): ?>
        <div class="col-sm-6 col-xl-2">
          <div class="glass-card p-3 skeleton-card d-flex flex-column justify-content-between">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div class="skeleton skeleton-circle" style="width: 36px; height: 36px;"></div>
              <div class="skeleton" style="width: 40px; height: 16px;"></div>
            </div>
            <div class="skeleton skeleton-title" style="width: 60%; height: 26px;"></div>
            <div class="skeleton skeleton-text short"></div>
          </div>
        </div>
      <?php endfor; ?>
    </div>

    <!-- Skeleton Chart & Milestones -->
    <div class="row g-4 mb-4">
      <div class="col-lg-8">
        <div class="glass-card p-4 skeleton-card" style="height: 380px;">
          <div class="skeleton skeleton-title" style="width: 40%;"></div>
          <div class="skeleton skeleton-chart" style="height: 280px;"></div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="glass-card p-4 skeleton-card" style="height: 380px;">
          <div class="skeleton skeleton-title" style="width: 60%;"></div>
          <div class="skeleton skeleton-text mb-3"></div>
          <div class="skeleton skeleton-text mb-3"></div>
          <div class="skeleton skeleton-text mb-3"></div>
          <div class="skeleton skeleton-text"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- =====================================================================
       REAL CONTENT VIEW (Interactive Software-Grade Dashboard)
       ===================================================================== -->
  <div id="dashboard-content-view" class="content-loaded" style="display: none;">

    <!-- Software Hero Banner -->
    <div class="glass-card soft-hero-banner p-4 mb-4 shadow-lg">
      <div class="row align-items-center g-3">
        <div class="col-lg-8">
          <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
            <span class="badge-pro-orange">Type Master Pro v2.0</span>
            <span class="rank-level-badge">
              <i class="bi bi-shield-fill-check text-warning"></i>
              Level <?= $userLevel ?> Typist
            </span>
            <span class="badge px-3 py-1 text-white" style="background-color: <?= $rating['color'] ?>;">
              <i class="bi <?= $rating['icon'] ?> me-1"></i><?= $rating['label'] ?>
            </span>
          </div>

          <h2 class="fw-extrabold text-light mb-1">
            Welcome, <?= e($user['full_name']) ?>!
          </h2>
          <p class="text-secondary small mb-3" style="max-width: 640px;">
            Precision typing analytics engine active. Track keystroke-level cadence, monitor gross and net WPM progression, and drill with speed passages.
          </p>

          <div class="d-flex flex-wrap align-items-center gap-2">
            <a href="<?= BASE_URL ?>/practice.php" class="btn soft-btn-primary px-4 py-2 d-inline-flex align-items-center gap-2 shadow-sm">
              <i class="bi bi-play-circle-fill fs-5"></i>
              <span>Start 60s Speed Test</span>
            </a>
            <a href="<?= BASE_URL ?>/practice.php?mode=free" class="btn btn-outline-warning px-3 py-2 d-inline-flex align-items-center gap-2">
              <i class="bi bi-stopwatch-fill"></i>
              <span>Free Typing &amp; Watch</span>
            </a>
            <a href="<?= BASE_URL ?>/practice.php?mode=custom" class="btn btn-outline-info px-3 py-2 d-inline-flex align-items-center gap-2">
              <i class="bi bi-sliders2"></i>
              <span>Custom Time Drills</span>
            </a>
            <a href="<?= BASE_URL ?>/statistics.php" class="btn btn-outline-secondary px-3 py-2 d-inline-flex align-items-center gap-1">
              <i class="bi bi-graph-up-arrow"></i>
              <span>Deep Analytics</span>
            </a>
          </div>
        </div>

        <div class="col-lg-4 text-lg-end">
          <div class="dashboard-streak-pill mb-2">
            <i class="bi bi-fire text-warning fs-5"></i>
            <span>Daily Typist Streak: Active</span>
          </div>
          <div class="text-secondary small">
            <i class="bi bi-award text-warning me-1"></i>Record: <strong><?= $bestWpm ?> WPM</strong>
            <span class="mx-2">•</span>
            <i class="bi bi-bullseye text-success me-1"></i>Accuracy: <strong><?= $avgAccuracy ?>%</strong>
          </div>
        </div>
      </div>
    </div>

    <!-- Last Test Captured & Quick Resume Banner ("if user stop ... all that last capture") -->
    <?php if ($lastSession): ?>
      <div class="last-session-card mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
          <div class="d-flex align-items-center gap-3">
            <div class="stat-icon" style="background: rgba(255, 106, 0, 0.2); color: #ff6a00; width: 48px; height: 48px; border-radius: 12px; font-size: 1.4rem;">
              <i class="bi bi-bookmark-check-fill"></i>
            </div>
            <div>
              <div class="d-flex align-items-center gap-2">
                <span class="text-uppercase fw-bold text-warning small tracking-wide">Last Session Captured</span>
                <span class="text-muted small">• <?= time_ago($lastSession['created_at']) ?></span>
              </div>
              <h6 class="fw-bold text-light mb-0 text-truncate" style="max-width: 380px;">
                <?= e($lastSession['passage_title'] ?? 'Typing Drill') ?>
              </h6>
            </div>
          </div>

          <!-- Captured Metrics Pills -->
          <div class="d-flex flex-wrap align-items-center gap-2">
            <div class="px-3 py-1 rounded bg-black-subtle border border-secondary text-center">
              <span class="text-muted d-block" style="font-size: 0.7rem;">Net Speed</span>
              <strong class="text-info fs-6 font-monospace"><?= (float)$lastSession['net_wpm'] ?> WPM</strong>
            </div>
            <div class="px-3 py-1 rounded bg-black-subtle border border-secondary text-center">
              <span class="text-muted d-block" style="font-size: 0.7rem;">Accuracy</span>
              <strong class="text-success fs-6 font-monospace"><?= (float)$lastSession['accuracy'] ?>%</strong>
            </div>
            <div class="px-3 py-1 rounded bg-black-subtle border border-secondary text-center">
              <span class="text-muted d-block" style="font-size: 0.7rem;">Time</span>
              <strong class="text-secondary fs-6 font-monospace"><?= (int)$lastSession['duration_seconds'] ?>s</strong>
            </div>

            <div class="ms-md-2 d-flex gap-2">
              <a href="<?= BASE_URL ?>/practice.php?passage_id=<?= (int)$lastSession['passage_id'] ?>" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1 shadow-sm" title="Replay same passage again">
                <i class="bi bi-arrow-repeat"></i>
                <span>Replay Passage</span>
              </a>
              <a href="<?= BASE_URL ?>/practice.php" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1" title="Start next random test">
                <i class="bi bi-shuffle"></i>
                <span>Next Test</span>
              </a>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <!-- Software-Grade Key Metrics Grid (6 Cards) -->
    <div class="row g-3 mb-4">
      <!-- Tests Completed -->
      <div class="col-sm-6 col-xl-2">
        <div class="glass-card stat-card h-100">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <div class="stat-icon" style="background: rgba(255, 106, 0, 0.15); color: #ff6a00;">
              <i class="bi bi-keyboard-fill"></i>
            </div>
            <span class="text-muted small">Total</span>
          </div>
          <div class="stat-value font-monospace"><?= $totalTests ?></div>
          <div class="stat-label">Tests Completed</div>
        </div>
      </div>

      <!-- Best WPM Score -->
      <div class="col-sm-6 col-xl-2">
        <div class="glass-card stat-card h-100">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <div class="stat-icon" style="background: rgba(255, 69, 0, 0.18); color: #ff4500;">
              <i class="bi bi-trophy-fill"></i>
            </div>
            <span class="badge border border-warning text-warning" style="background: rgba(255, 106, 0, 0.1);">Record</span>
          </div>
          <div class="stat-value text-warning font-monospace"><?= $bestWpm ?></div>
          <div class="stat-label">Personal Best WPM</div>
        </div>
      </div>

      <!-- Average Net WPM -->
      <div class="col-sm-6 col-xl-2">
        <div class="glass-card stat-card h-100">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <div class="stat-icon" style="background: rgba(255, 140, 51, 0.15); color: #ff8c33;">
              <i class="bi bi-lightning-charge-fill"></i>
            </div>
            <span class="text-muted small">Average</span>
          </div>
          <div class="stat-value text-info font-monospace"><?= $avgWpm ?></div>
          <div class="stat-label">Average Net WPM</div>
        </div>
      </div>

      <!-- Average Accuracy -->
      <div class="col-sm-6 col-xl-2">
        <div class="glass-card stat-card h-100">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <div class="stat-icon" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">
              <i class="bi bi-bullseye"></i>
            </div>
            <span class="text-muted small">Precision</span>
          </div>
          <div class="stat-value text-success font-monospace"><?= $avgAccuracy ?>%</div>
          <div class="stat-label">Average Accuracy</div>
        </div>
      </div>

      <!-- Total Words Typed -->
      <div class="col-sm-6 col-xl-2">
        <div class="glass-card stat-card h-100">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <div class="stat-icon" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b;">
              <i class="bi bi-fonts"></i>
            </div>
            <span class="text-muted small">Volume</span>
          </div>
          <div class="stat-value text-light font-monospace"><?= number_format($totalWords) ?></div>
          <div class="stat-label">Words Typed</div>
        </div>
      </div>

      <!-- Total Practice Time -->
      <div class="col-sm-6 col-xl-2">
        <div class="glass-card stat-card h-100">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <div class="stat-icon" style="background: rgba(99, 102, 241, 0.15); color: #818cf8;">
              <i class="bi bi-clock-history"></i>
            </div>
            <span class="text-muted small">Time</span>
          </div>
          <div class="stat-value font-monospace" style="font-size: 1.6rem;"><?= format_duration($totalSeconds) ?></div>
          <div class="stat-label">Practice Time</div>
        </div>
      </div>
    </div>

    <!-- Charts & Milestone Row -->
    <div class="row g-4 mb-4">
      <!-- Speed Progression Chart (Dark Orange Theme) -->
      <div class="col-lg-8">
        <div class="glass-card p-4 h-100">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
              <h5 class="fw-bold text-light mb-0">
                <i class="bi bi-graph-up text-info me-2"></i>Speed Progression Over Time
              </h5>
              <span class="text-secondary small">Real-time Net Words Per Minute and Accuracy per session</span>
            </div>
            <div class="d-flex gap-2">
              <a href="<?= BASE_URL ?>/statistics.php" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-bar-chart-fill me-1"></i> Analytics
              </a>
            </div>
          </div>

          <?php if (!empty($weeklyChartData)): ?>
            <div style="height: 300px; position: relative;">
              <canvas id="dashboardWeeklyChart"></canvas>
            </div>
          <?php else: ?>
            <div class="text-center py-5 text-muted">
              <i class="bi bi-bar-chart-line display-3 d-block mb-3 opacity-25" style="color: #ff6a00;"></i>
              <p class="mb-2">No typing test history recorded yet.</p>
              <a href="<?= BASE_URL ?>/practice.php" class="btn btn-sm btn-primary">Take Your First Test</a>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Milestones & Performance Target Dial -->
      <div class="col-lg-4">
        <div class="glass-card p-4 h-100 d-flex flex-column justify-content-between">
          <div>
            <div class="d-flex align-items-center justify-content-between mb-3">
              <h5 class="fw-bold text-light mb-0">
                <i class="bi bi-award-fill text-warning me-2"></i>Milestones & Target
              </h5>
              <span class="badge bg-dark border border-secondary text-info"><?= $rating['label'] ?></span>
            </div>

            <!-- Next Milestone Progress Bar -->
            <div class="p-3 rounded glass-panel mb-3">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="small fw-bold text-secondary">Target: <?= $nextMilestoneLabel ?></span>
                <span class="small font-monospace text-info"><?= $bestWpm ?> / <?= $nextMilestoneWpm ?> WPM</span>
              </div>
              <div class="progress" style="height: 8px; background: rgba(255,255,255,0.06);">
                <div class="progress-bar typing-progress-bar" style="width: <?= $milestoneProgressPercent ?>%;"></div>
              </div>
              <div class="text-muted mt-2 small" style="font-size: 0.75rem;">
                <?= ($bestWpm >= $nextMilestoneWpm) ? '🎉 Target unlocked!' : (round($nextMilestoneWpm - $bestWpm, 1) . ' WPM needed to level up.') ?>
              </div>
            </div>

            <!-- Milestone Badges List -->
            <div class="d-flex flex-column gap-2">
              <!-- 40 WPM Badge -->
              <div class="p-2 px-3 rounded glass-panel d-flex align-items-center gap-3 <?= ($bestWpm >= 40) ? 'border-success' : 'opacity-40' ?>">
                <i class="bi bi-check2-circle fs-5 <?= ($bestWpm >= 40) ? 'text-success' : 'text-secondary' ?>"></i>
                <div class="flex-grow-1">
                  <div class="fw-bold text-light small">40+ WPM Intermediate</div>
                </div>
                <span class="badge <?= ($bestWpm >= 40) ? 'bg-success' : 'bg-secondary' ?> ms-auto"><?= ($bestWpm >= 40) ? 'Unlocked' : 'Locked' ?></span>
              </div>

              <!-- 60 WPM Badge -->
              <div class="p-2 px-3 rounded glass-panel d-flex align-items-center gap-3 <?= ($bestWpm >= 60) ? 'border-info' : 'opacity-40' ?>">
                <i class="bi bi-speedometer fs-5 <?= ($bestWpm >= 60) ? 'text-info' : 'text-secondary' ?>"></i>
                <div class="flex-grow-1">
                  <div class="fw-bold text-light small">60+ WPM Professional</div>
                </div>
                <span class="badge <?= ($bestWpm >= 60) ? 'bg-primary' : 'bg-secondary' ?> ms-auto"><?= ($bestWpm >= 60) ? 'Unlocked' : 'Locked' ?></span>
              </div>

              <!-- 80 WPM Badge -->
              <div class="p-2 px-3 rounded glass-panel d-flex align-items-center gap-3 <?= ($bestWpm >= 80) ? 'border-warning' : 'opacity-40' ?>">
                <i class="bi bi-lightning-charge-fill fs-5 <?= ($bestWpm >= 80) ? 'text-warning' : 'text-secondary' ?>"></i>
                <div class="flex-grow-1">
                  <div class="fw-bold text-light small">80+ WPM Grandmaster</div>
                </div>
                <span class="badge <?= ($bestWpm >= 80) ? 'bg-warning text-dark' : 'bg-secondary' ?> ms-auto"><?= ($bestWpm >= 80) ? 'Unlocked' : 'Locked' ?></span>
              </div>

              <!-- 100 WPM Badge -->
              <div class="p-2 px-3 rounded glass-panel d-flex align-items-center gap-3 <?= ($bestWpm >= 100) ? 'border-danger' : 'opacity-40' ?>">
                <i class="bi bi-fire fs-5 <?= ($bestWpm >= 100) ? 'text-danger' : 'text-secondary' ?>"></i>
                <div class="flex-grow-1">
                  <div class="fw-bold text-light small">100+ WPM Typing God</div>
                </div>
                <span class="badge <?= ($bestWpm >= 100) ? 'bg-danger' : 'bg-secondary' ?> ms-auto"><?= ($bestWpm >= 100) ? 'Unlocked' : 'Locked' ?></span>
              </div>
            </div>
          </div>

          <div class="mt-3 pt-3 border-top border-secondary text-center">
            <a href="<?= BASE_URL ?>/practice.php" class="btn btn-sm btn-outline-info w-100">
              <i class="bi bi-trophy me-1"></i> Beat Your Record
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Practice Passage Drills Deck -->
    <div class="mb-4">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <h5 class="fw-bold text-light mb-0">
          <i class="bi bi-play-btn-fill text-warning me-2"></i>Quick Training Modes
        </h5>
        <a href="<?= BASE_URL ?>/practice.php" class="text-info small text-decoration-none">
          All Drills <i class="bi bi-chevron-right"></i>
        </a>
      </div>

      <div class="row g-3">
        <!-- Beginner 30s Sprint -->
        <div class="col-sm-6 col-xl-3">
          <div class="glass-card p-3 h-100 d-flex flex-column justify-content-between border-start border-3 border-success">
            <div>
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge badge-diff-beginner"><i class="bi bi-shield-check me-1"></i>Beginner</span>
                <span class="text-muted small font-monospace"><i class="bi bi-stopwatch me-1"></i>30s</span>
              </div>
              <h6 class="fw-bold text-light mb-1">Foundational Home Row</h6>
              <p class="text-secondary small mb-3">Develop muscle memory, index anchor habits, and rhythmic finger placement.</p>
            </div>
            <a href="<?= BASE_URL ?>/practice.php?difficulty=beginner&duration=30" class="btn btn-sm btn-control d-flex align-items-center justify-content-center gap-1">
              <i class="bi bi-play-fill fs-5"></i> Launch 30s Drill
            </a>
          </div>
        </div>

        <!-- Intermediate 1m Sprint -->
        <div class="col-sm-6 col-xl-3">
          <div class="glass-card p-3 h-100 d-flex flex-column justify-content-between border-start border-3 border-warning">
            <div>
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge badge-diff-intermediate"><i class="bi bi-lightning-charge me-1"></i>Intermediate</span>
                <span class="text-muted small font-monospace"><i class="bi bi-stopwatch me-1"></i>1m (60s)</span>
              </div>
              <h6 class="fw-bold text-light mb-1">Standard Speed Sprint</h6>
              <p class="text-secondary small mb-3">Balanced mix of common English vocabulary, punctuation, and capital letters.</p>
            </div>
            <a href="<?= BASE_URL ?>/practice.php?difficulty=intermediate&duration=60" class="btn btn-sm soft-btn-primary d-flex align-items-center justify-content-center gap-1">
              <i class="bi bi-play-fill fs-5"></i> Launch 1m Drill
            </a>
          </div>
        </div>

        <!-- Advanced 3m Endurance -->
        <div class="col-sm-6 col-xl-3">
          <div class="glass-card p-3 h-100 d-flex flex-column justify-content-between border-start border-3 border-danger">
            <div>
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge badge-diff-advanced"><i class="bi bi-fire me-1"></i>Advanced</span>
                <span class="text-muted small font-monospace"><i class="bi bi-stopwatch me-1"></i>3m (180s)</span>
              </div>
              <h6 class="fw-bold text-light mb-1">Endurance & Code Syntax</h6>
              <p class="text-secondary small mb-3">Complex passages with special symbols, brackets, and developer terminology.</p>
            </div>
            <a href="<?= BASE_URL ?>/practice.php?difficulty=advanced&duration=180" class="btn btn-sm btn-control d-flex align-items-center justify-content-center gap-1">
              <i class="bi bi-play-fill fs-5"></i> Launch 3m Drill
            </a>
          </div>
        </div>

        <!-- Free Typing & Watch -->
        <div class="col-sm-6 col-xl-3">
          <div class="glass-card p-3 h-100 d-flex flex-column justify-content-between border-start border-3 border-info">
            <div>
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge bg-info-subtle text-info border border-info"><i class="bi bi-pencil-fill me-1"></i>Free Canvas</span>
                <span class="text-muted small font-monospace"><i class="bi bi-stopwatch-fill text-warning me-1"></i>Watch</span>
              </div>
              <h6 class="fw-bold text-light mb-1">Free Typing &amp; Watch</h6>
              <p class="text-secondary small mb-3">Write freely without constraints. Live telemetry calculates silently in memory.</p>
            </div>
            <a href="<?= BASE_URL ?>/practice.php?mode=free" class="btn btn-sm btn-outline-info d-flex align-items-center justify-content-center gap-1">
              <i class="bi bi-pencil-square fs-5"></i> Open Free Canvas
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Sessions Table -->
    <div class="glass-card p-4">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
          <h5 class="fw-bold text-light mb-0">
            <i class="bi bi-clock-history text-info me-2"></i>Recent Practice Sessions
          </h5>
          <span class="text-secondary small">Your latest saved typing drills with keystroke metrics</span>
        </div>
        <a href="<?= BASE_URL ?>/history.php" class="btn btn-sm btn-outline-secondary">
          <span>View Full History</span>
          <i class="bi bi-arrow-right ms-1"></i>
        </a>
      </div>

      <?php if (!empty($recentTests)): ?>
        <div class="table-responsive">
          <table class="custom-table mb-0">
            <thead>
              <tr>
                <th>Passage</th>
                <th>Difficulty</th>
                <th>Net WPM</th>
                <th>Gross WPM</th>
                <th>Accuracy</th>
                <th>Duration</th>
                <th>Words</th>
                <th>Time</th>
                <th class="text-end">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recentTests as $row): ?>
                <tr>
                  <td>
                    <div class="fw-bold text-light text-truncate" style="max-width: 200px;">
                      <i class="bi bi-file-text text-warning me-1"></i>
                      <?= e($row['passage_title'] ?? 'Custom Text') ?>
                    </div>
                  </td>
                  <td><?= get_difficulty_badge($row['difficulty']) ?></td>
                  <td>
                    <span class="fw-bold text-info fs-5 font-monospace"><?= (float)$row['net_wpm'] ?></span>
                  </td>
                  <td class="font-monospace text-secondary"><?= (float)$row['gross_wpm'] ?></td>
                  <td>
                    <div class="d-flex align-items-center gap-2">
                      <div class="progress flex-grow-1" style="height: 6px; width: 60px;">
                        <div class="progress-bar bg-success" style="width: <?= (float)$row['accuracy'] ?>%;"></div>
                      </div>
                      <span class="font-monospace small fw-semibold"><?= (float)$row['accuracy'] ?>%</span>
                    </div>
                  </td>
                  <td class="font-monospace text-secondary"><?= (int)$row['duration_seconds'] ?>s</td>
                  <td class="font-monospace text-secondary"><?= (int)$row['words_typed'] ?></td>
                  <td class="text-muted small"><?= time_ago($row['created_at']) ?></td>
                  <td class="text-end">
                    <?php if (!empty($row['passage_id'])): ?>
                      <a href="<?= BASE_URL ?>/practice.php?passage_id=<?= (int)$row['passage_id'] ?>" class="btn btn-sm btn-outline-info py-0 px-2" title="Practice this passage again">
                        <i class="bi bi-arrow-repeat"></i> Replay
                      </a>
                    <?php else: ?>
                      <span class="text-muted small">—</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <div class="text-center py-5 text-muted">
          <i class="bi bi-keyboard display-4 d-block mb-3 opacity-25" style="color: #ff6a00;"></i>
          <h5 class="text-light fw-bold">No sessions logged yet</h5>
          <p class="text-secondary small mb-3">Take your first typing practice drill to see your speed and accuracy analytics here.</p>
          <a href="<?= BASE_URL ?>/practice.php" class="btn btn-primary fw-bold px-4 py-2">Start Practicing Now</a>
        </div>
      <?php endif; ?>
    </div>

  </div> <!-- End Content View -->

</div>

<!-- Chart initialization & Skeleton Loader Reveal Script -->
<script>
document.addEventListener('DOMContentLoaded', () => {
  // Skeleton transition effect
  setTimeout(() => {
    const skeleton = document.getElementById('dashboard-skeleton-view');
    const content = document.getElementById('dashboard-content-view');
    if (skeleton && content) {
      skeleton.style.display = 'none';
      content.style.display = 'block';
    }
  }, 120);

  <?php if (!empty($weeklyChartData)): ?>
  const chartData = <?= json_encode($weeklyChartData) ?>;
  const ctx = document.getElementById('dashboardWeeklyChart');
  if (ctx) {
    const labels = chartData.map(d => d.test_day);
    const wpmData = chartData.map(d => parseFloat(d.net_wpm));
    const accuracyData = chartData.map(d => parseFloat(d.accuracy));

    new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [
          {
            label: 'Net WPM',
            data: wpmData,
            borderColor: '#ff6a00',
            backgroundColor: 'rgba(255, 106, 0, 0.18)',
            fill: true,
            tension: 0.35,
            borderWidth: 3,
            pointRadius: 4,
            pointBackgroundColor: '#ff6a00',
            yAxisID: 'y'
          },
          {
            label: 'Accuracy (%)',
            data: accuracyData,
            borderColor: '#10b981',
            borderDash: [5, 5],
            tension: 0.35,
            borderWidth: 2,
            pointRadius: 3,
            pointBackgroundColor: '#10b981',
            yAxisID: 'y1'
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
          legend: { labels: { color: '#94a3b8', font: { family: 'Inter' } } }
        },
        scales: {
          x: {
            grid: { color: 'rgba(255, 106, 0, 0.05)' },
            ticks: { color: '#64748b' }
          },
          y: {
            type: 'linear',
            display: true,
            position: 'left',
            beginAtZero: true,
            grid: { color: 'rgba(255, 106, 0, 0.06)' },
            ticks: { color: '#ff8c33' },
            title: { display: true, text: 'WPM', color: '#ff8c33' }
          },
          y1: {
            type: 'linear',
            display: true,
            position: 'right',
            min: 60,
            max: 100,
            grid: { drawOnChartArea: false },
            ticks: { color: '#10b981' },
            title: { display: true, text: 'Accuracy %', color: '#10b981' }
          }
        }
      }
    });
  }
  <?php endif; ?>
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
