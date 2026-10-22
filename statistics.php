<?php
/**
 * Custom Type Master Pro
 * Deep Analytics & Statistics Page
 */

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/functions.php';

require_login();

$user = current_user();
$userId = $user['id'];
$pdo = getDBConnection();

$wpmHistory = [];
$accuracyHistory = [];
$labels = [];
$difficultyStats = [
    'beginner'     => ['avg' => 0, 'max' => 0, 'count' => 0],
    'intermediate' => ['avg' => 0, 'max' => 0, 'count' => 0],
    'advanced'     => ['avg' => 0, 'max' => 0, 'count' => 0],
];
$totalCorrectKeys = 0;
$totalErrorKeys = 0;
$totalBackspaces = 0;
$recentAvg = 0;
$previousAvg = 0;

if ($pdo) {
    try {
        // Chronological history for trend chart
        $stmt = $pdo->prepare("
            SELECT net_wpm, accuracy, DATE_FORMAT(created_at, '%m/%d') AS d_label 
            FROM typing_results
            WHERE user_id = :uid
            ORDER BY created_at ASC
            LIMIT 30
        ");
        $stmt->execute([':uid' => $userId]);
        $rows = $stmt->fetchAll();

        foreach ($rows as $r) {
            $labels[] = $r['d_label'];
            $wpmHistory[] = (float)$r['net_wpm'];
            $accuracyHistory[] = (float)$r['accuracy'];
        }

        // Difficulty breakdown
        $diffStmt = $pdo->prepare("
            SELECT difficulty, AVG(net_wpm) AS avg_wpm, MAX(net_wpm) AS max_wpm, COUNT(*) AS cnt 
            FROM typing_results
            WHERE user_id = :uid AND difficulty IN ('beginner', 'intermediate', 'advanced')
            GROUP BY difficulty
        ");
        $diffStmt->execute([':uid' => $userId]);
        $diffRows = $diffStmt->fetchAll();

        foreach ($diffRows as $dr) {
            $diffKey = strtolower($dr['difficulty']);
            if (isset($difficultyStats[$diffKey])) {
                $difficultyStats[$diffKey]['avg'] = round((float)$dr['avg_wpm'], 1);
                $difficultyStats[$diffKey]['max'] = round((float)$dr['max_wpm'], 1);
                $difficultyStats[$diffKey]['count'] = (int)$dr['cnt'];
            }
        }

        // Keystroke totals
        $keyStmt = $pdo->prepare("
            SELECT 
                COALESCE(SUM(correct_keystrokes), 0) AS total_correct,
                COALESCE(SUM(error_keystrokes), 0) AS total_errors,
                COALESCE(SUM(backspace_count), 0) AS total_backspaces
            FROM typing_results
            WHERE user_id = :uid
        ");
        $keyStmt->execute([':uid' => $userId]);
        $keyRow = $keyStmt->fetch();
        if ($keyRow) {
            $totalCorrectKeys = (int)$keyRow['total_correct'];
            $totalErrorKeys = (int)$keyRow['total_errors'];
            $totalBackspaces = (int)$keyRow['total_backspaces'];
        }

        // Comparison: Last 5 vs Previous 5
        $compStmt = $pdo->prepare("
            SELECT net_wpm FROM typing_results 
            WHERE user_id = :uid 
            ORDER BY created_at DESC 
            LIMIT 10
        ");
        $compStmt->execute([':uid' => $userId]);
        $allTen = $compStmt->fetchAll(PDO::FETCH_COLUMN);

        if (count($allTen) >= 5) {
            $recentFive = array_slice($allTen, 0, 5);
            $recentAvg = round(array_sum($recentFive) / count($recentFive), 1);

            $prevFive = array_slice($allTen, 5, 5);
            if (count($prevFive) > 0) {
                $previousAvg = round(array_sum($prevFive) / count($prevFive), 1);
            }
        }

    } catch (Exception $e) {
        error_log('Stats error: ' . $e->getMessage());
    }
}

$speedGrowth = ($previousAvg > 0) ? round((($recentAvg - $previousAvg) / $previousAvg) * 100, 1) : 0;

$pageTitle = 'Speed & Accuracy Analytics';
include __DIR__ . '/includes/header.php';
?>

<div class="container-fluid px-md-4 py-3">

  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
      <h2 class="fw-bold text-light mb-1"><i class="bi bi-graph-up text-info me-2"></i>Performance Statistics</h2>
      <p class="text-secondary small mb-0">Detailed statistical overview of your typing speed, accuracy patterns, and keystrokes.</p>
    </div>
    <a href="<?= BASE_URL ?>/practice.php" class="btn btn-primary btn-sm fw-bold">
      <i class="bi bi-play-circle-fill me-1"></i> Start Practice Session
    </a>
  </div>

  <!-- Comparison & Velocity Card -->
  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="glass-card p-3 h-100">
        <div class="text-secondary small fw-bold mb-1">Recent 5 Sessions Avg</div>
        <div class="fs-2 fw-bold text-info font-monospace"><?= $recentAvg ?> WPM</div>
        <div class="text-muted small">Average of your latest 5 completed tests</div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="glass-card p-3 h-100">
        <div class="text-secondary small fw-bold mb-1">Previous 5 Sessions Avg</div>
        <div class="fs-2 fw-bold text-secondary font-monospace"><?= $previousAvg ?> WPM</div>
        <div class="text-muted small">Baseline comparison</div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="glass-card p-3 h-100">
        <div class="text-secondary small fw-bold mb-1">Speed Growth Velocity</div>
        <div class="fs-2 fw-bold font-monospace <?= ($speedGrowth >= 0) ? 'text-success' : 'text-danger' ?>">
          <?= ($speedGrowth >= 0 ? '+' : '') . $speedGrowth ?>%
        </div>
        <div class="text-muted small">Trend change over recent runs</div>
      </div>
    </div>
  </div>

  <!-- Chart Grids -->
  <div class="row g-4 mb-4">
    <!-- WPM Trend Chart -->
    <div class="col-lg-8">
      <div class="glass-card p-4 h-100">
        <h5 class="fw-bold text-light mb-1"><i class="bi bi-activity text-info me-2"></i>Typing Speed (WPM) Trajectory</h5>
        <p class="text-secondary small mb-3">Chronological progression of Net WPM across recent sessions</p>

        <?php if (!empty($wpmHistory)): ?>
          <div style="height: 300px; position: relative;">
            <canvas id="wpmProgressChart"></canvas>
          </div>
        <?php else: ?>
          <div class="text-center py-5 text-muted">
            <i class="bi bi-graph-up display-3 d-block mb-3 opacity-25"></i>
            <p>Not enough data to graph yet. Complete more tests!</p>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Keystroke Breakdown Doughnut -->
    <div class="col-lg-4">
      <div class="glass-card p-4 h-100">
        <h5 class="fw-bold text-light mb-1"><i class="bi bi-pie-chart-fill text-warning me-2"></i>Keystroke Distribution</h5>
        <p class="text-secondary small mb-3">Ratio of correct vs error vs backspace inputs</p>

        <?php if ($totalCorrectKeys > 0 || $totalErrorKeys > 0): ?>
          <div style="height: 240px; position: relative;">
            <canvas id="keystrokesChart"></canvas>
          </div>
          <div class="mt-3 text-center small text-secondary">
            Total Keystrokes: <strong class="text-light"><?= number_format($totalCorrectKeys + $totalErrorKeys) ?></strong>
          </div>
        <?php else: ?>
          <div class="text-center py-5 text-muted">
            <p>No keystrokes recorded yet.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Difficulty Comparison & Accuracy Trends -->
  <div class="row g-4 mb-4">
    <!-- Difficulty Performance Bar Chart -->
    <div class="col-lg-6">
      <div class="glass-card p-4 h-100">
        <h5 class="fw-bold text-light mb-1"><i class="bi bi-bar-chart-fill text-primary me-2"></i>Speed by Difficulty</h5>
        <p class="text-secondary small mb-3">Average vs Personal Best WPM for each difficulty level</p>

        <div style="height: 260px; position: relative;">
          <canvas id="difficultyChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Accuracy Trend Chart -->
    <div class="col-lg-6">
      <div class="glass-card p-4 h-100">
        <h5 class="fw-bold text-light mb-1"><i class="bi bi-bullseye text-success me-2"></i>Accuracy Trend (%)</h5>
        <p class="text-secondary small mb-3">Maintaining accuracy >95% is key to developing high typing flow</p>

        <?php if (!empty($accuracyHistory)): ?>
          <div style="height: 260px; position: relative;">
            <canvas id="accuracyTrendChart"></canvas>
          </div>
        <?php else: ?>
          <div class="text-center py-5 text-muted">
            <p>No accuracy data available yet.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

</div>

<!-- Render Chart.js Analytics -->
<script>
document.addEventListener('DOMContentLoaded', () => {
  const labels = <?= json_encode($labels) ?>;
  const wpmData = <?= json_encode($wpmHistory) ?>;
  const accData = <?= json_encode($accuracyHistory) ?>;

  // 1. WPM Progress Chart
  const wpmCtx = document.getElementById('wpmProgressChart');
  if (wpmCtx && wpmData.length > 0) {
    new Chart(wpmCtx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'Net WPM',
          data: wpmData,
          borderColor: '#38bdf8',
          backgroundColor: 'rgba(56, 189, 248, 0.1)',
          fill: true,
          tension: 0.35,
          borderWidth: 2.5,
          pointRadius: 4,
          pointBackgroundColor: '#38bdf8'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          x: { grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#64748b' } },
          y: { beginAtZero: true, grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#64748b' } }
        }
      }
    });
  }

  // 2. Keystrokes Doughnut
  const keyCtx = document.getElementById('keystrokesChart');
  if (keyCtx) {
    new Chart(keyCtx, {
      type: 'doughnut',
      data: {
        labels: ['Correct', 'Errors', 'Backspaces'],
        datasets: [{
          data: [<?= $totalCorrectKeys ?>, <?= $totalErrorKeys ?>, <?= $totalBackspaces ?>],
          backgroundColor: ['#10b981', '#ef4444', '#f59e0b'],
          borderWidth: 0
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { position: 'bottom', labels: { color: '#94a3b8' } }
        }
      }
    });
  }

  // 3. Difficulty Bar Chart
  const diffCtx = document.getElementById('difficultyChart');
  if (diffCtx) {
    new Chart(diffCtx, {
      type: 'bar',
      data: {
        labels: ['Beginner', 'Intermediate', 'Advanced'],
        datasets: [
          {
            label: 'Average WPM',
            data: [
              <?= $difficultyStats['beginner']['avg'] ?>,
              <?= $difficultyStats['intermediate']['avg'] ?>,
              <?= $difficultyStats['advanced']['avg'] ?>
            ],
            backgroundColor: '#38bdf8'
          },
          {
            label: 'Max WPM',
            data: [
              <?= $difficultyStats['beginner']['max'] ?>,
              <?= $difficultyStats['intermediate']['max'] ?>,
              <?= $difficultyStats['advanced']['max'] ?>
            ],
            backgroundColor: '#f43f5e'
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          x: { grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#64748b' } },
          y: { beginAtZero: true, grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#64748b' } }
        },
        plugins: {
          legend: { labels: { color: '#94a3b8' } }
        }
      }
    });
  }

  // 4. Accuracy Trend Chart
  const accCtx = document.getElementById('accuracyTrendChart');
  if (accCtx && accData.length > 0) {
    new Chart(accCtx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'Accuracy %',
          data: accData,
          borderColor: '#10b981',
          backgroundColor: 'rgba(16, 185, 129, 0.1)',
          fill: true,
          tension: 0.35,
          borderWidth: 2,
          pointRadius: 3,
          pointBackgroundColor: '#10b981'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          x: { grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#64748b' } },
          y: { min: 60, max: 100, grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#64748b' } }
        }
      }
    });
  }
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
