<?php
/**
 * Custom Type Master Pro
 * Deep Analytics & Statistics Page — B&W Redesign
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
$totalSessions = count($wpmHistory);
$totalKeystrokes = $totalCorrectKeys + $totalErrorKeys;
$accuracyRate = $totalKeystrokes > 0 ? round(($totalCorrectKeys / $totalKeystrokes) * 100, 1) : 0;

$pageTitle = 'Speed & Accuracy Analytics';
include __DIR__ . '/includes/header.php';
?>

<style>
/* ============================================================
   B&W ANALYTICS PAGE — scoped, non-destructive
   ============================================================ */
:root {
    --bw-bg:        #ffffff;
    --bw-surface:   #ffffff;
    --bw-surface-2: #f7f7f7;
    --bw-border:    #e5e5e5;
    --bw-border-2:  #d4d4d4;
    --bw-ink:       #0a0a0a;
    --bw-ink-2:     #1f1f1f;
    --bw-muted:     #6b6b6b;
    --bw-muted-2:   #8f8f8f;
    --bw-radius:    14px;
    --bw-radius-sm: 10px;
    --bw-shadow:    0 1px 2px rgba(0,0,0,0.04), 0 4px 16px rgba(0,0,0,0.05);
    --bw-font:      -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    --bw-mono:      ui-monospace, SFMono-Regular, "SF Mono", Menlo, Consolas, monospace;
}

.bw-page {
    background: var(--bw-bg);
    color: var(--bw-ink);
    font-family: var(--bw-font);
    min-height: 100vh;
    padding: 2rem 0 3rem;
}

/* --- Page header --- */
.bw-head {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    justify-content: space-between;
    align-items: flex-end;
    padding-bottom: 1.5rem;
    margin-bottom: 1.75rem;
    border-bottom: 1px solid var(--bw-border);
}
.bw-head h1 {
    font-size: 1.75rem;
    font-weight: 800;
    letter-spacing: -0.02em;
    margin: 0 0 0.25rem;
    color: var(--bw-ink);
    display: flex;
    align-items: center;
    gap: 0.6rem;
}
.bw-head h1 i {
    font-size: 1.5rem;
    color: var(--bw-ink);
}
.bw-head p {
    margin: 0;
    font-size: 0.875rem;
    color: var(--bw-muted);
    max-width: 560px;
    line-height: 1.5;
}

/* --- Buttons --- */
.bw-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.65rem 1.15rem;
    font-size: 0.85rem;
    font-weight: 700;
    letter-spacing: 0.01em;
    border-radius: var(--bw-radius-sm);
    text-decoration: none;
    transition: all 0.2s ease;
    border: 1.5px solid var(--bw-ink);
    cursor: pointer;
    white-space: nowrap;
}
.bw-btn-dark {
    background: var(--bw-ink);
    color: #fff;
}
.bw-btn-dark:hover {
    background: #fff;
    color: var(--bw-ink);
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(0,0,0,0.12);
}
.bw-btn-light {
    background: #fff;
    color: var(--bw-ink);
}
.bw-btn-light:hover {
    background: var(--bw-ink);
    color: #fff;
}

/* --- Cards --- */
.bw-card {
    background: var(--bw-surface);
    border: 1px solid var(--bw-border);
    border-radius: var(--bw-radius);
    padding: 1.5rem;
    height: 100%;
    box-shadow: var(--bw-shadow);
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}
.bw-card:hover {
    border-color: var(--bw-border-2);
    box-shadow: 0 2px 6px rgba(0,0,0,0.05), 0 8px 24px rgba(0,0,0,0.07);
}
.bw-card-title {
    font-size: 0.95rem;
    font-weight: 800;
    color: var(--bw-ink);
    margin: 0 0 0.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    letter-spacing: -0.01em;
}
.bw-card-title i {
    color: var(--bw-ink);
    font-size: 1rem;
}
.bw-card-sub {
    font-size: 0.78rem;
    color: var(--bw-muted);
    margin: 0 0 1.15rem;
    line-height: 1.5;
}

/* --- KPI / stat cards --- */
.bw-kpi-label {
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--bw-muted);
    margin-bottom: 0.5rem;
}
.bw-kpi-value {
    font-family: var(--bw-mono);
    font-size: 2.25rem;
    font-weight: 800;
    line-height: 1;
    color: var(--bw-ink);
    letter-spacing: -0.03em;
    display: flex;
    align-items: baseline;
    gap: 0.35rem;
}
.bw-kpi-value small {
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--bw-muted);
    letter-spacing: 0;
}
.bw-kpi-foot {
    margin-top: 0.6rem;
    font-size: 0.75rem;
    color: var(--bw-muted-2);
    line-height: 1.4;
}

/* trend pill */
.bw-trend {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    font-size: 0.72rem;
    font-weight: 700;
    padding: 0.2rem 0.55rem;
    border-radius: 999px;
    border: 1px solid var(--bw-border);
    background: var(--bw-surface-2);
    color: var(--bw-ink);
}
.bw-trend.up   { background: #0a0a0a; color: #fff; border-color: #0a0a0a; }
.bw-trend.down { background: #fff; color: #0a0a0a; border-color: #0a0a0a; }

/* --- Chart wrappers --- */
.bw-chart {
    position: relative;
    width: 100%;
}
.bw-chart-sm { height: 240px; }
.bw-chart-md { height: 280px; }
.bw-chart-lg { height: 320px; }

/* --- Empty state --- */
.bw-empty {
    text-align: center;
    padding: 3rem 1rem;
    color: var(--bw-muted);
}
.bw-empty i {
    font-size: 2.5rem;
    color: var(--bw-border-2);
    display: block;
    margin-bottom: 0.75rem;
}
.bw-empty p {
    margin: 0;
    font-size: 0.85rem;
}

/* --- Keystroke legend --- */
.bw-legend {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 1rem;
    margin-top: 1rem;
    font-size: 0.78rem;
    color: var(--bw-muted);
}
.bw-legend-item {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
}
.bw-legend-dot {
    width: 10px;
    height: 10px;
    border-radius: 3px;
    border: 1px solid var(--bw-border-2);
}
.bw-legend-dot.k-correct { background: #0a0a0a; border-color: #0a0a0a; }
.bw-legend-dot.k-error   { background: #fff; border-color: #0a0a0a; }
.bw-legend-dot.k-back    { background: #a3a3a3; border-color: #a3a3a3; }

.bw-total-keys {
    text-align: center;
    margin-top: 0.85rem;
    font-size: 0.8rem;
    color: var(--bw-muted);
}
.bw-total-keys strong {
    color: var(--bw-ink);
    font-family: var(--bw-mono);
}

/* --- Section heading --- */
.bw-section-label {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    font-size: 0.72rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--bw-muted);
    margin: 0.5rem 0 1rem;
}
.bw-section-label::after {
    content: "";
    flex: 1;
    height: 1px;
    background: var(--bw-border);
}

/* --- Responsive tweaks --- */
@media (max-width: 767px) {
    .bw-page { padding: 1.25rem 0 2rem; }
    .bw-head h1 { font-size: 1.4rem; }
    .bw-kpi-value { font-size: 1.85rem; }
    .bw-card { padding: 1.15rem; }
    .bw-chart-lg { height: 260px; }
    .bw-chart-md { height: 240px; }
}
</style>

<div class="bw-page">
  <div class="container-fluid px-md-4">

    <!-- ============ PAGE HEADER ============ -->
    <div class="bw-head">
      <div>
        <h1><i class="bi bi-graph-up"></i> Performance Statistics</h1>
        <p>Detailed statistical overview of your typing speed, accuracy patterns, and keystroke efficiency.</p>
      </div>
      <a href="<?= BASE_URL ?>/practice.php" class="bw-btn bw-btn-dark">
        <i class="bi bi-play-circle-fill"></i> Start Practice Session
      </a>
    </div>

    <!-- ============ KPI ROW ============ -->
    <div class="bw-section-label">Session Comparison</div>

    <div class="row g-3 mb-4">
      <div class="col-md-4">
        <div class="bw-card">
          <div class="bw-kpi-label">Recent 5 Sessions Avg</div>
          <div class="bw-kpi-value">
            <?= number_format($recentAvg, 1) ?><small>WPM</small>
          </div>
          <div class="bw-kpi-foot">Average of your latest 5 completed tests</div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="bw-card">
          <div class="bw-kpi-label">Previous 5 Sessions Avg</div>
          <div class="bw-kpi-value">
            <?= number_format($previousAvg, 1) ?><small>WPM</small>
          </div>
          <div class="bw-kpi-foot">Baseline comparison from older runs</div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="bw-card">
          <div class="bw-kpi-label">Speed Growth Velocity</div>
          <div class="bw-kpi-value">
            <?= ($speedGrowth >= 0 ? '+' : '') . number_format($speedGrowth, 1) ?><small>%</small>
          </div>
          <div class="bw-kpi-foot">
            <span class="bw-trend <?= $speedGrowth >= 0 ? 'up' : 'down' ?>">
              <i class="bi bi-arrow-<?= $speedGrowth >= 0 ? 'up' : 'down' ?>-right"></i>
              <?= $speedGrowth >= 0 ? 'Improving' : 'Declining' ?>
            </span>
            <span class="ms-1">Trend change over recent runs</span>
          </div>
        </div>
      </div>
    </div>

    <!-- ============ CHARTS: WPM + KEYSTROKES ============ -->
    <div class="bw-section-label">Progress Charts</div>

    <div class="row g-4 mb-4">
      <!-- WPM Trend -->
      <div class="col-lg-8">
        <div class="bw-card">
          <h5 class="bw-card-title"><i class="bi bi-activity"></i> Typing Speed (WPM) Trajectory</h5>
          <p class="bw-card-sub">Chronological progression of Net WPM across your recent sessions.</p>

          <?php if (!empty($wpmHistory)): ?>
            <div class="bw-chart bw-chart-lg">
              <canvas id="wpmProgressChart"></canvas>
            </div>
          <?php else: ?>
            <div class="bw-empty">
              <i class="bi bi-graph-up"></i>
              <p>Not enough data to graph yet. Complete more tests!</p>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Keystroke Breakdown -->
      <div class="col-lg-4">
        <div class="bw-card">
          <h5 class="bw-card-title"><i class="bi bi-pie-chart-fill"></i> Keystroke Distribution</h5>
          <p class="bw-card-sub">Ratio of correct vs error vs backspace inputs.</p>

          <?php if ($totalCorrectKeys > 0 || $totalErrorKeys > 0): ?>
            <div class="bw-chart bw-chart-sm">
              <canvas id="keystrokesChart"></canvas>
            </div>

            <div class="bw-legend">
              <span class="bw-legend-item"><span class="bw-legend-dot k-correct"></span> Correct</span>
              <span class="bw-legend-item"><span class="bw-legend-dot k-error"></span> Errors</span>
              <span class="bw-legend-item"><span class="bw-legend-dot k-back"></span> Backspaces</span>
            </div>

            <div class="bw-total-keys">
              Total Keystrokes: <strong><?= number_format($totalKeystrokes) ?></strong>
            </div>
          <?php else: ?>
            <div class="bw-empty">
              <i class="bi bi-keyboard"></i>
              <p>No keystrokes recorded yet.</p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- ============ CHARTS: DIFFICULTY + ACCURACY ============ -->
    <div class="bw-section-label">Skill Breakdown</div>

    <div class="row g-4 mb-2">
      <!-- Difficulty Performance -->
      <div class="col-lg-6">
        <div class="bw-card">
          <h5 class="bw-card-title"><i class="bi bi-bar-chart-fill"></i> Speed by Difficulty</h5>
          <p class="bw-card-sub">Average vs personal best WPM for each difficulty level.</p>

          <div class="bw-chart bw-chart-md">
            <canvas id="difficultyChart"></canvas>
          </div>
        </div>
      </div>

      <!-- Accuracy Trend -->
      <div class="col-lg-6">
        <div class="bw-card">
          <h5 class="bw-card-title"><i class="bi bi-bullseye"></i> Accuracy Trend (%)</h5>
          <p class="bw-card-sub">Maintaining accuracy above 95% is key to developing high typing flow.</p>

          <?php if (!empty($accuracyHistory)): ?>
            <div class="bw-chart bw-chart-md">
              <canvas id="accuracyTrendChart"></canvas>
            </div>
          <?php else: ?>
            <div class="bw-empty">
              <i class="bi bi-bullseye"></i>
              <p>No accuracy data available yet.</p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- ============================================================
     CHART.JS — B&W styled
     ============================================================ -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const labels  = <?= json_encode($labels) ?>;
  const wpmData = <?= json_encode($wpmHistory) ?>;
  const accData = <?= json_encode($accuracyHistory) ?>;

  // Shared B&W chart defaults
  const gridColor  = 'rgba(0,0,0,0.06)';
  const tickColor  = '#6b6b6b';
  const fontFamily = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';

  Chart.defaults.font.family = fontFamily;
  Chart.defaults.font.size   = 11;
  Chart.defaults.color       = tickColor;

  const baseScales = {
    x: {
      grid:  { color: gridColor, drawTicks: false },
      ticks: { color: tickColor, padding: 8 },
      border: { display: false }
    },
    y: {
      beginAtZero: true,
      grid:  { color: gridColor, drawTicks: false },
      ticks: { color: tickColor, padding: 8 },
      border: { display: false }
    }
  };

  // 1. WPM Progress Chart — solid black line, subtle fill
  const wpmCtx = document.getElementById('wpmProgressChart');
  if (wpmCtx && wpmData.length > 0) {
    const ctx = wpmCtx.getContext('2d');
    const grad = ctx.createLinearGradient(0, 0, 0, 320);
    grad.addColorStop(0, 'rgba(10,10,10,0.14)');
    grad.addColorStop(1, 'rgba(10,10,10,0.00)');

    new Chart(wpmCtx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'Net WPM',
          data: wpmData,
          borderColor: '#0a0a0a',
          backgroundColor: grad,
          fill: true,
          tension: 0.35,
          borderWidth: 2.5,
          pointRadius: 3,
          pointHoverRadius: 5,
          pointBackgroundColor: '#0a0a0a',
          pointBorderColor: '#ffffff',
          pointBorderWidth: 1.5
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: '#0a0a0a',
            titleColor: '#ffffff',
            bodyColor: '#ffffff',
            padding: 10,
            cornerRadius: 6,
            displayColors: false
          }
        },
        scales: baseScales,
        interaction: { intersect: false, mode: 'index' }
      }
    });
  }

  // 2. Keystrokes Doughnut — black / white / gray
  const keyCtx = document.getElementById('keystrokesChart');
  if (keyCtx) {
    new Chart(keyCtx, {
      type: 'doughnut',
      data: {
        labels: ['Correct', 'Errors', 'Backspaces'],
        datasets: [{
          data: [<?= $totalCorrectKeys ?>, <?= $totalErrorKeys ?>, <?= $totalBackspaces ?>],
          backgroundColor: ['#0a0a0a', '#ffffff', '#a3a3a3'],
          borderColor: ['#0a0a0a', '#0a0a0a', '#a3a3a3'],
          borderWidth: 2,
          hoverOffset: 6
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '68%',
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: '#0a0a0a',
            titleColor: '#fff',
            bodyColor: '#fff',
            padding: 10,
            cornerRadius: 6
          }
        }
      }
    });
  }

  // 3. Difficulty Bar Chart — solid black vs outlined
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
            backgroundColor: '#0a0a0a',
            borderRadius: 6,
            borderSkipped: false,
            barPercentage: 0.7,
            categoryPercentage: 0.6
          },
          {
            label: 'Max WPM',
            data: [
              <?= $difficultyStats['beginner']['max'] ?>,
              <?= $difficultyStats['intermediate']['max'] ?>,
              <?= $difficultyStats['advanced']['max'] ?>
            ],
            backgroundColor: '#ffffff',
            borderColor: '#0a0a0a',
            borderWidth: 1.5,
            borderRadius: 6,
            borderSkipped: false,
            barPercentage: 0.7,
            categoryPercentage: 0.6
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              color: tickColor,
              boxWidth: 10,
              boxHeight: 10,
              padding: 14,
              font: { size: 11, weight: '600' }
            }
          },
          tooltip: {
            backgroundColor: '#0a0a0a',
            titleColor: '#fff',
            bodyColor: '#fff',
            padding: 10,
            cornerRadius: 6
          }
        },
        scales: baseScales
      }
    });
  }

  // 4. Accuracy Trend Chart — solid black line
  const accCtx = document.getElementById('accuracyTrendChart');
  if (accCtx && accData.length > 0) {
    const ctx = accCtx.getContext('2d');
    const grad = ctx.createLinearGradient(0, 0, 0, 280);
    grad.addColorStop(0, 'rgba(10,10,10,0.12)');
    grad.addColorStop(1, 'rgba(10,10,10,0.00)');

    new Chart(accCtx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'Accuracy %',
          data: accData,
          borderColor: '#0a0a0a',
          backgroundColor: grad,
          fill: true,
          tension: 0.35,
          borderWidth: 2,
          pointRadius: 2.5,
          pointHoverRadius: 5,
          pointBackgroundColor: '#0a0a0a',
          pointBorderColor: '#ffffff',
          pointBorderWidth: 1.5
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: '#0a0a0a',
            titleColor: '#fff',
            bodyColor: '#fff',
            padding: 10,
            cornerRadius: 6,
            displayColors: false
          }
        },
        scales: {
          x: baseScales.x,
          y: {
            min: 60,
            max: 100,
            grid:  { color: gridColor, drawTicks: false },
            ticks: { color: tickColor, padding: 8, callback: v => v + '%' },
            border: { display: false }
          }
        },
        interaction: { intersect: false, mode: 'index' }
      }
    });
  }
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>