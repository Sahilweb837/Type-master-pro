 <?php
/**
 * Custom Type Master Pro
 * User Dashboard — MONOCHROME PRO UI v4.0
 */

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/functions.php';

require_login();

$user = current_user();
$userId = $user['id'];
$pdo = getDBConnection();

$totalTests = 0; $avgWpm = 0.0; $bestWpm = 0.0; $avgAccuracy = 0.0;
$totalSeconds = 0; $totalWords = 0; $recentTests = []; $weeklyChartData = [];

if ($pdo) {
    try {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) AS total_tests,
                COALESCE(AVG(net_wpm), 0) AS avg_wpm,
                COALESCE(MAX(net_wpm), 0) AS best_wpm,
                COALESCE(AVG(accuracy), 0) AS avg_accuracy,
                COALESCE(SUM(elapsed_seconds), 0) AS total_seconds,
                COALESCE(SUM(words_typed), 0) AS total_words
            FROM typing_results WHERE user_id = :uid
        ");
        $stmt->execute([':uid' => $userId]);
        $stats = $stmt->fetch();
        if ($stats) {
            $totalTests   = (int)$stats['total_tests'];
            $avgWpm       = round((float)$stats['avg_wpm'], 1);
            $bestWpm      = round((float)$stats['best_wpm'], 1);
            $avgAccuracy  = round((float)$stats['avg_accuracy'], 1);
            $totalSeconds = (int)$stats['total_seconds'];
            $totalWords   = (int)$stats['total_words'];
        }

        $recentStmt = $pdo->prepare("
            SELECT r.*, p.title AS passage_title
            FROM typing_results r
            LEFT JOIN typing_passages p ON r.passage_id = p.id
            WHERE r.user_id = :uid
            ORDER BY r.created_at DESC LIMIT 6
        ");
        $recentStmt->execute([':uid' => $userId]);
        $recentTests = $recentStmt->fetchAll();

        $chartStmt = $pdo->prepare("
            SELECT DATE_FORMAT(created_at, '%b %d') AS test_day, net_wpm, accuracy
            FROM typing_results WHERE user_id = :uid
            ORDER BY created_at ASC LIMIT 14
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

$nextMilestoneWpm = 40; $nextMilestoneLabel = '40+ WPM';
if ($bestWpm >= 80) { $nextMilestoneWpm = 100; $nextMilestoneLabel = '100+ WPM'; }
elseif ($bestWpm >= 60) { $nextMilestoneWpm = 80; $nextMilestoneLabel = '80+ WPM'; }
elseif ($bestWpm >= 40) { $nextMilestoneWpm = 60; $nextMilestoneLabel = '60+ WPM'; }
$milestoneProgressPercent = min(100, max(5, round(($bestWpm / $nextMilestoneWpm) * 100)));

$pageTitle = 'Dashboard';
include __DIR__ . '/includes/header.php';
?>

<style>
/* ============================================================
   MONOCHROME PRO UI — v4.0
   High-contrast black & white, Linear/Vercel inspired
   ============================================================ */
:root {
  --mono-bg: #000000;
  --mono-surface-1: #0a0a0a;
  --mono-surface-2: #111111;
  --mono-surface-3: #171717;
  --mono-surface-4: #1f1f1f;
  --mono-border: #262626;
  --mono-border-strong: #3a3a3a;
  --mono-text: #ffffff;
  --mono-text-dim: #a1a1a1;
  --mono-text-muted: #6b6b6b;
  --mono-accent: #ffffff;
  --mono-inverse: #000000;
  --mono-radius: 12px;
  --mono-radius-sm: 8px;
  --mono-radius-lg: 16px;
  --mono-transition: cubic-bezier(0.4, 0, 0.2, 1);
  --mono-font-mono: 'JetBrains Mono', 'SF Mono', 'Fira Code', ui-monospace, monospace;
}

.dash-mono {
  background: var(--mono-bg);
  color: var(--mono-text);
  font-feature-settings: 'ss01', 'cv01', 'cv11';
  padding: 1rem 0 2.5rem;
  min-height: 100vh;
  -webkit-font-smoothing: antialiased;
}

/* ===== Shared utility ===== */
.mono-eyebrow {
  display: inline-flex; align-items: center; gap: .45rem;
  font-family: var(--mono-font-mono);
  font-size: .68rem; font-weight: 600;
  letter-spacing: .12em; text-transform: uppercase;
  color: var(--mono-text-muted);
}
.mono-eyebrow::before {
  content: ''; width: 5px; height: 5px;
  background: var(--mono-text); border-radius: 50%;
}
.mono-section-label {
  font-family: var(--mono-font-mono);
  font-size: .7rem; font-weight: 700;
  letter-spacing: .15em; text-transform: uppercase;
  color: var(--mono-text-muted);
  margin: 0 0 .85rem;
  display: flex; align-items: center; gap: .6rem;
}
.mono-section-label::after {
  content: ''; flex: 1; height: 1px;
  background: linear-gradient(90deg, var(--mono-border), transparent);
}
.mono-divider { height: 1px; background: var(--mono-border); border: none; margin: 0; }

/* ===== Buttons ===== */
.mono-btn {
  display: inline-flex; align-items: center; gap: .5rem;
  padding: .55rem .95rem;
  font-size: .82rem; font-weight: 600;
  border-radius: var(--mono-radius-sm);
  border: 1px solid var(--mono-border);
  background: var(--mono-surface-2);
  color: var(--mono-text);
  text-decoration: none;
  transition: all .18s var(--mono-transition);
  white-space: nowrap;
  cursor: pointer;
}
.mono-btn:hover {
  background: var(--mono-surface-3);
  border-color: var(--mono-border-strong);
  color: var(--mono-text);
  transform: translateY(-1px);
}
.mono-btn-primary {
  background: var(--mono-text);
  color: var(--mono-inverse);
  border-color: var(--mono-text);
  font-weight: 700;
}
.mono-btn-primary:hover {
  background: var(--mono-text-dim);
  border-color: var(--mono-text-dim);
  color: var(--mono-inverse);
}
.mono-btn-ghost { background: transparent; }
.mono-btn-sm { padding: .38rem .75rem; font-size: .76rem; }

/* ===== Tag / chip ===== */
.mono-tag {
  display: inline-flex; align-items: center; gap: .3rem;
  padding: .2rem .55rem;
  font-family: var(--mono-font-mono);
  font-size: .66rem; font-weight: 700;
  letter-spacing: .05em; text-transform: uppercase;
  border-radius: 4px;
  border: 1px solid var(--mono-border);
  background: var(--mono-surface-2);
  color: var(--mono-text-dim);
}
.mono-tag-solid {
  background: var(--mono-text);
  color: var(--mono-inverse);
  border-color: var(--mono-text);
}

/* ===== HERO ===== */
.mono-hero {
  position: relative;
  border: 1px solid var(--mono-border);
  border-radius: var(--mono-radius-lg);
  padding: 2rem;
  background: var(--mono-surface-1);
  overflow: hidden;
}
.mono-hero::before {
  content: '';
  position: absolute; inset: 0;
  background-image:
    linear-gradient(var(--mono-border) 1px, transparent 1px),
    linear-gradient(90deg, var(--mono-border) 1px, transparent 1px);
  background-size: 48px 48px;
  opacity: .35;
  mask-image: radial-gradient(ellipse 70% 80% at 85% 15%, #000 10%, transparent 70%);
  pointer-events: none;
}
.mono-hero > * { position: relative; z-index: 1; }

.mono-hero-title {
  font-size: 2.35rem;
  font-weight: 800;
  letter-spacing: -.035em;
  line-height: 1.05;
  margin: .85rem 0 .6rem;
  color: var(--mono-text);
}
.mono-hero-title .dim { color: var(--mono-text-muted); font-weight: 300; }
.mono-hero-sub {
  color: var(--mono-text-dim);
  font-size: .92rem;
  line-height: 1.6;
  max-width: 600px;
  margin-bottom: 1.4rem;
}

/* Level meter */
.mono-level {
  display: inline-flex;
  align-items: center;
  gap: .85rem;
  padding: .65rem .9rem;
  border-radius: var(--mono-radius-sm);
  background: var(--mono-surface-2);
  border: 1px solid var(--mono-border);
  margin-bottom: 1.25rem;
}
.mono-level-num {
  font-family: var(--mono-font-mono);
  font-size: 1.5rem;
  font-weight: 800;
  color: var(--mono-text);
  line-height: 1;
}
.mono-level-meta { min-width: 160px; }
.mono-level-label {
  font-family: var(--mono-font-mono);
  font-size: .64rem;
  font-weight: 700;
  letter-spacing: .1em;
  text-transform: uppercase;
  color: var(--mono-text-muted);
  margin-bottom: .3rem;
}
.mono-level-bar {
  height: 3px; background: var(--mono-surface-4);
  border-radius: 99px; overflow: hidden;
}
.mono-level-bar > span {
  display: block; height: 100%;
  background: var(--mono-text);
  border-radius: 99px;
  transition: width 1.2s var(--mono-transition);
}

/* Hero stats (inverse block) */
.mono-hero-stats {
  border-radius: var(--mono-radius);
  border: 1px solid var(--mono-border);
  background: var(--mono-surface-2);
  padding: .35rem 1rem;
}
.mono-hero-stat {
  display: flex; justify-content: space-between; align-items: baseline;
  padding: .7rem 0;
  border-bottom: 1px solid var(--mono-border);
}
.mono-hero-stat:last-child { border-bottom: none; }
.mono-hero-stat-label {
  font-family: var(--mono-font-mono);
  font-size: .68rem; font-weight: 600;
  letter-spacing: .08em; text-transform: uppercase;
  color: var(--mono-text-muted);
}
.mono-hero-stat-value {
  font-family: var(--mono-font-mono);
  font-size: 1.2rem; font-weight: 800;
  color: var(--mono-text);
}
.mono-hero-stat-value small {
  font-size: .62rem; font-weight: 600;
  color: var(--mono-text-muted);
  margin-left: 3px;
}

/* ===== LAST SESSION ===== */
.mono-last {
  display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between;
  gap: 1rem;
  padding: .9rem 1.1rem;
  border-radius: var(--mono-radius);
  background: var(--mono-surface-2);
  border: 1px solid var(--mono-border);
  border-left: 3px solid var(--mono-text);
}
.mono-last-cell {
  display: flex; flex-direction: column; gap: .15rem;
}
.mono-last-cell-label {
  font-family: var(--mono-font-mono);
  font-size: .62rem; font-weight: 700;
  letter-spacing: .1em; text-transform: uppercase;
  color: var(--mono-text-muted);
}
.mono-last-cell-value {
  font-family: var(--mono-font-mono);
  font-size: .95rem; font-weight: 800;
  color: var(--mono-text);
}

/* ===== METRIC GRID ===== */
.mono-metrics {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
  gap: 1px;
  background: var(--mono-border);
  border: 1px solid var(--mono-border);
  border-radius: var(--mono-radius);
  overflow: hidden;
}
.mono-metric {
  background: var(--mono-surface-1);
  padding: 1.15rem 1.2rem;
  transition: background .18s var(--mono-transition);
  position: relative;
}
.mono-metric:hover { background: var(--mono-surface-2); }
.mono-metric::before {
  content: '';
  position: absolute; top: 0; left: 0; right: 0;
  height: 1px; background: var(--mono-text);
  transform: scaleX(0); transform-origin: left;
  transition: transform .3s var(--mono-transition);
}
.mono-metric:hover::before { transform: scaleX(1); }
.mono-metric-top {
  display: flex; justify-content: space-between; align-items: center;
  margin-bottom: .9rem;
}
.mono-metric-icon {
  width: 30px; height: 30px;
  display: flex; align-items: center; justify-content: center;
  border-radius: 6px;
  background: var(--mono-surface-3);
  border: 1px solid var(--mono-border);
  color: var(--mono-text-dim);
  font-size: .9rem;
}
.mono-metric-tag {
  font-family: var(--mono-font-mono);
  font-size: .6rem; font-weight: 700;
  letter-spacing: .1em; text-transform: uppercase;
  color: var(--mono-text-muted);
}
.mono-metric-value {
  font-family: var(--mono-font-mono);
  font-size: 1.75rem; font-weight: 800;
  letter-spacing: -.02em;
  color: var(--mono-text);
  line-height: 1;
}
.mono-metric-value .unit {
  font-size: .68rem; font-weight: 600;
  color: var(--mono-text-muted);
  margin-left: 3px;
}
.mono-metric-label {
  font-size: .74rem;
  color: var(--mono-text-dim);
  margin-top: .45rem;
  font-weight: 500;
}

/* ===== PANELS ===== */
.mono-panel {
  background: var(--mono-surface-1);
  border: 1px solid var(--mono-border);
  border-radius: var(--mono-radius);
  padding: 1.35rem;
  height: 100%;
}
.mono-panel-head {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 1.15rem;
  padding-bottom: .9rem;
  border-bottom: 1px solid var(--mono-border);
}
.mono-panel-title {
  font-size: .95rem; font-weight: 700;
  color: var(--mono-text);
  margin: 0;
  display: flex; align-items: center; gap: .5rem;
}
.mono-panel-title i { color: var(--mono-text-muted); }

/* ===== MILESTONES ===== */
.mono-ms-list { display: flex; flex-direction: column; gap: .5rem; }
.mono-ms {
  display: flex; align-items: center; gap: .75rem;
  padding: .65rem .8rem;
  border-radius: var(--mono-radius-sm);
  background: var(--mono-surface-2);
  border: 1px solid var(--mono-border);
  transition: all .18s var(--mono-transition);
}
.mono-ms.is-locked { opacity: .45; }
.mono-ms.is-unlocked:hover {
  border-color: var(--mono-border-strong);
  background: var(--mono-surface-3);
  transform: translateX(2px);
}
.mono-ms-icon {
  width: 26px; height: 26px;
  display: flex; align-items: center; justify-content: center;
  border-radius: 5px;
  background: var(--mono-surface-3);
  color: var(--mono-text-muted);
  font-size: .8rem;
  flex-shrink: 0;
}
.mono-ms.is-unlocked .mono-ms-icon {
  background: var(--mono-text);
  color: var(--mono-inverse);
}
.mono-ms-label {
  font-size: .8rem; font-weight: 600;
  color: var(--mono-text);
  flex: 1;
}

/* ===== PROGRESS ===== */
.mono-progress {
  height: 4px;
  background: var(--mono-surface-4);
  border-radius: 99px;
  overflow: hidden;
  position: relative;
}
.mono-progress > span {
  display: block; height: 100%;
  background: var(--mono-text);
  border-radius: 99px;
  transition: width 1.3s var(--mono-transition);
  position: relative;
  overflow: hidden;
}
.mono-progress > span::after {
  content: '';
  position: absolute; inset: 0;
  background: linear-gradient(90deg, transparent, rgba(0,0,0,.4), transparent);
  animation: mono-shimmer 1.8s infinite;
}
@keyframes mono-shimmer {
  0% { transform: translateX(-100%); }
  100% { transform: translateX(100%); }
}

/* ===== TRAINING ===== */
.mono-train {
  border-radius: var(--mono-radius);
  background: var(--mono-surface-1);
  border: 1px solid var(--mono-border);
  padding: 1.1rem;
  height: 100%;
  display: flex; flex-direction: column; justify-content: space-between;
  gap: .9rem;
  transition: all .2s var(--mono-transition);
  position: relative;
}
.mono-train:hover {
  border-color: var(--mono-border-strong);
  background: var(--mono-surface-2);
  transform: translateY(-2px);
}
.mono-train-head {
  display: flex; justify-content: space-between; align-items: center;
}
.mono-train h6 {
  font-size: .92rem; font-weight: 700;
  color: var(--mono-text); margin: .55rem 0 .3rem;
}
.mono-train p {
  font-size: .78rem;
  color: var(--mono-text-dim);
  margin: 0 0 .9rem;
  line-height: 1.5;
}

/* ===== TABLE ===== */
.mono-table {
  width: 100%;
  border-collapse: collapse;
  font-size: .82rem;
}
.mono-table thead th {
  font-family: var(--mono-font-mono);
  font-size: .64rem; font-weight: 700;
  letter-spacing: .1em; text-transform: uppercase;
  color: var(--mono-text-muted);
  padding: .6rem .65rem;
  text-align: left;
  border-bottom: 1px solid var(--mono-border);
}
.mono-table tbody td {
  padding: .8rem .65rem;
  border-bottom: 1px solid var(--mono-border);
  color: var(--mono-text-dim);
  vertical-align: middle;
}
.mono-table tbody tr:last-child td { border-bottom: none; }
.mono-table tbody tr {
  transition: background .15s var(--mono-transition);
}
.mono-table tbody tr:hover td {
  background: var(--mono-surface-2);
  color: var(--mono-text);
}
.mono-table .mono-wpm {
  font-family: var(--mono-font-mono);
  font-weight: 800;
  color: var(--mono-text) !important;
  font-size: .95rem;
}
.mono-table .mono-passage {
  color: var(--mono-text);
  font-weight: 600;
  max-width: 200px;
  overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
}
.mono-mini-bar {
  width: 52px; height: 3px;
  background: var(--mono-surface-4);
  border-radius: 99px;
  overflow: hidden;
  display: inline-block; vertical-align: middle;
}
.mono-mini-bar > span {
  display: block; height: 100%;
  background: var(--mono-text);
  border-radius: 99px;
}

/* ===== SKELETON ===== */
.mono-sk-wrap { display: flex; flex-direction: column; gap: 1rem; }
.mono-sk {
  background: linear-gradient(90deg, var(--mono-surface-1) 0%, var(--mono-surface-3) 50%, var(--mono-surface-1) 100%);
  background-size: 200% 100%;
  border-radius: 8px;
  border: 1px solid var(--mono-border);
  animation: mono-sk-bg 1.4s infinite;
}
@keyframes mono-sk-bg {
  0% { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}

/* ===== FADE IN ===== */
.mono-reveal { animation: monoFade .5s var(--mono-transition) forwards; }
@keyframes monoFade {
  from { opacity: 0; transform: translateY(6px); }
  to { opacity: 1; transform: translateY(0); }
}

/* ===== RESPONSIVE ===== */
@media (max-width: 992px) {
  .mono-hero-title { font-size: 1.85rem; }
  .mono-hero { padding: 1.5rem; }
  .mono-metric-value { font-size: 1.5rem; }
}
@media (max-width: 576px) {
  .mono-hero-title { font-size: 1.55rem; }
  .mono-metrics { grid-template-columns: repeat(2, 1fr); }
  .mono-metric { padding: .9rem 1rem; }
  .mono-metric-value { font-size: 1.3rem; }
}
</style>

<div class="container-fluid px-md-4 dash-mono">

  <!-- ============================================================
       SKELETON
       ============================================================ -->
  <div id="mono-skeleton" class="mono-sk-wrap">
    <div class="mono-sk" style="height: 220px;"></div>
    <div class="row g-3">
      <?php for ($i = 0; $i < 6; $i++): ?>
        <div class="col-6 col-xl-2"><div class="mono-sk" style="height: 130px;"></div></div>
      <?php endfor; ?>
    </div>
    <div class="row g-3">
      <div class="col-lg-8"><div class="mono-sk" style="height: 380px;"></div></div>
      <div class="col-lg-4"><div class="mono-sk" style="height: 380px;"></div></div>
    </div>
  </div>

  <!-- ============================================================
       REAL CONTENT
       ============================================================ -->
  <div id="mono-content" style="display: none;">

    <!-- ================= HERO ================= -->
    <div class="mono-hero mb-4">
      <div class="row align-items-center g-4">
        <div class="col-lg-8">
          <span class="mono-eyebrow">Type Master Pro &mdash; v4.0</span>

          <h1 class="mono-hero-title">
            Welcome back,<br>
            <span class="dim"><?= e($user['full_name']) ?></span>
          </h1>

          <p class="mono-hero-sub">
            Precision typing analytics engine active. Track keystroke cadence, monitor WPM progression, and push toward your next milestone.
          </p>

          <!-- Level meter -->
          <div class="mono-level">
            <div class="mono-level-num"><?= str_pad((string)$userLevel, 2, '0', STR_PAD_LEFT) ?></div>
            <div class="mono-level-meta">
              <div class="mono-level-label">Level &mdash; <?= $rating['label'] ?></div>
              <div class="mono-level-bar"><span style="width: <?= $milestoneProgressPercent ?>%;"></span></div>
            </div>
          </div>

          <div class="d-flex flex-wrap gap-2">
            <a href="<?= BASE_URL ?>/practice.php" class="mono-btn mono-btn-primary">
              <i class="bi bi-play-fill"></i> Start 60s Test
            </a>
            <a href="<?= BASE_URL ?>/lessons.php" class="mono-btn">
              <i class="bi bi-mortarboard-fill"></i> Lessons
            </a>
            <a href="<?= BASE_URL ?>/statistics.php" class="mono-btn mono-btn-ghost">
              <i class="bi bi-graph-up-arrow"></i> Analytics
            </a>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="mono-hero-stats">
            <div class="mono-hero-stat">
              <span class="mono-hero-stat-label">Record</span>
              <span class="mono-hero-stat-value"><?= $bestWpm ?><small>WPM</small></span>
            </div>
            <div class="mono-hero-stat">
              <span class="mono-hero-stat-label">Avg Speed</span>
              <span class="mono-hero-stat-value"><?= $avgWpm ?><small>WPM</small></span>
            </div>
            <div class="mono-hero-stat">
              <span class="mono-hero-stat-label">Accuracy</span>
              <span class="mono-hero-stat-value"><?= $avgAccuracy ?><small>%</small></span>
            </div>
            <div class="mono-hero-stat">
              <span class="mono-hero-stat-label">Streak</span>
              <span class="mono-hero-stat-value">Active</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ================= LAST SESSION ================= -->
    <?php if ($lastSession): ?>
      <div class="mono-last mb-4">
        <div class="d-flex align-items-center gap-3">
          <div class="mono-metric-icon"><i class="bi bi-bookmark-fill"></i></div>
          <div>
            <div class="mono-eyebrow mb-1" style="font-size: .62rem;">
              Last Session &bull; <?= time_ago($lastSession['created_at']) ?>
            </div>
            <div class="mono-passage text-light fw-bold" style="font-size: .88rem;">
              <?= e($lastSession['passage_title'] ?? 'Typing Drill') ?>
            </div>
          </div>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-3">
          <div class="mono-last-cell">
            <span class="mono-last-cell-label">Net WPM</span>
            <span class="mono-last-cell-value"><?= (float)$lastSession['net_wpm'] ?></span>
          </div>
          <div class="mono-last-cell">
            <span class="mono-last-cell-label">Accuracy</span>
            <span class="mono-last-cell-value"><?= (float)$lastSession['accuracy'] ?>%</span>
          </div>
          <div class="mono-last-cell">
            <span class="mono-last-cell-label">Duration</span>
            <span class="mono-last-cell-value"><?= (int)$lastSession['duration_seconds'] ?>s</span>
          </div>
          <a href="<?= BASE_URL ?>/practice.php?passage_id=<?= (int)$lastSession['passage_id'] ?>" class="mono-btn mono-btn-primary mono-btn-sm">
            <i class="bi bi-arrow-repeat"></i> Replay
          </a>
        </div>
      </div>
    <?php endif; ?>

    <!-- ================= METRICS ================= -->
    <div class="mono-section-label">Performance Snapshot</div>
    <div class="mono-metrics mb-4">
      <div class="mono-metric">
        <div class="mono-metric-top">
          <div class="mono-metric-icon"><i class="bi bi-keyboard"></i></div>
          <span class="mono-metric-tag">Total</span>
        </div>
        <div class="mono-metric-value"><?= number_format($totalTests) ?></div>
        <div class="mono-metric-label">Tests Completed</div>
      </div>

      <div class="mono-metric">
        <div class="mono-metric-top">
          <div class="mono-metric-icon"><i class="bi bi-trophy"></i></div>
          <span class="mono-tag mono-tag-solid">Record</span>
        </div>
        <div class="mono-metric-value"><?= $bestWpm ?><span class="unit">WPM</span></div>
        <div class="mono-metric-label">Personal Best</div>
      </div>

      <div class="mono-metric">
        <div class="mono-metric-top">
          <div class="mono-metric-icon"><i class="bi bi-lightning-charge"></i></div>
          <span class="mono-metric-tag">Average</span>
        </div>
        <div class="mono-metric-value"><?= $avgWpm ?><span class="unit">WPM</span></div>
        <div class="mono-metric-label">Avg Net Speed</div>
      </div>

      <div class="mono-metric">
        <div class="mono-metric-top">
          <div class="mono-metric-icon"><i class="bi bi-bullseye"></i></div>
          <span class="mono-metric-tag">Precision</span>
        </div>
        <div class="mono-metric-value"><?= $avgAccuracy ?><span class="unit">%</span></div>
        <div class="mono-metric-label">Avg Accuracy</div>
      </div>

      <div class="mono-metric">
        <div class="mono-metric-top">
          <div class="mono-metric-icon"><i class="bi bi-fonts"></i></div>
          <span class="mono-metric-tag">Volume</span>
        </div>
        <div class="mono-metric-value"><?= number_format($totalWords) ?></div>
        <div class="mono-metric-label">Words Typed</div>
      </div>

      <div class="mono-metric">
        <div class="mono-metric-top">
          <div class="mono-metric-icon"><i class="bi bi-clock-history"></i></div>
          <span class="mono-metric-tag">Time</span>
        </div>
        <div class="mono-metric-value" style="font-size: 1.35rem;"><?= format_duration($totalSeconds) ?></div>
        <div class="mono-metric-label">Practice Time</div>
      </div>
    </div>

    <!-- ================= CHART + MILESTONES ================= -->
    <div class="row g-3 mb-4">
      <div class="col-lg-8">
        <div class="mono-panel">
          <div class="mono-panel-head">
            <h5 class="mono-panel-title"><i class="bi bi-graph-up"></i> Speed Progression</h5>
            <a href="<?= BASE_URL ?>/statistics.php" class="mono-btn mono-btn-sm mono-btn-ghost">
              <i class="bi bi-bar-chart"></i> Full Analytics
            </a>
          </div>
          <?php if (!empty($weeklyChartData)): ?>
            <div style="height: 300px; position: relative;">
              <canvas id="monoWeeklyChart"></canvas>
            </div>
          <?php else: ?>
            <div class="text-center py-5">
              <i class="bi bi-bar-chart-line d-block mb-3" style="font-size: 3rem; color: var(--mono-text-muted); opacity: .3;"></i>
              <p class="text-secondary mb-3">No test history yet.</p>
              <a href="<?= BASE_URL ?>/practice.php" class="mono-btn mono-btn-primary mono-btn-sm">Take First Test</a>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="mono-panel d-flex flex-column justify-content-between">
          <div>
            <div class="mono-panel-head">
              <h5 class="mono-panel-title"><i class="bi bi-award"></i> Milestones</h5>
              <span class="mono-tag"><?= $rating['label'] ?></span>
            </div>

            <!-- Next milestone -->
            <div class="p-3 rounded mb-3" style="background: var(--mono-surface-2); border: 1px solid var(--mono-border);">
              <div class="d-flex justify-content-between align-items-baseline mb-2">
                <span class="mono-metric-tag">Next: <?= $nextMilestoneLabel ?></span>
                <span class="font-monospace small" style="color: var(--mono-text);"><?= $bestWpm ?>/<?= $nextMilestoneWpm ?></span>
              </div>
              <div class="mono-progress"><span style="width: <?= $milestoneProgressPercent ?>%;"></span></div>
              <div class="text-muted mt-2" style="font-size: .7rem;">
                <?= ($bestWpm >= $nextMilestoneWpm) ? 'Target unlocked' : (round($nextMilestoneWpm - $bestWpm, 1) . ' WPM to level up') ?>
              </div>
            </div>

            <!-- Milestone list -->
            <?php
            $milestones = [
                ['wpm' => 40,  'label' => '40+ Intermediate',  'icon' => 'bi-check2-circle'],
                ['wpm' => 60,  'label' => '60+ Professional',  'icon' => 'bi-speedometer'],
                ['wpm' => 80,  'label' => '80+ Grandmaster',   'icon' => 'bi-lightning-charge-fill'],
                ['wpm' => 100, 'label' => '100+ Typing God',   'icon' => 'bi-fire'],
            ];
            ?>
            <div class="mono-ms-list">
              <?php foreach ($milestones as $ms):
                $unlocked = $bestWpm >= $ms['wpm'];
              ?>
                <div class="mono-ms <?= $unlocked ? 'is-unlocked' : 'is-locked' ?>">
                  <div class="mono-ms-icon"><i class="bi <?= $ms['icon'] ?>"></i></div>
                  <div class="mono-ms-label"><?= $ms['label'] ?></div>
                  <?php if ($unlocked): ?>
                    <i class="bi bi-check-lg" style="color: var(--mono-text);"></i>
                  <?php else: ?>
                    <i class="bi bi-lock" style="color: var(--mono-text-muted); font-size: .75rem;"></i>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

          <a href="<?= BASE_URL ?>/practice.php" class="mono-btn mono-btn-primary mono-btn-sm w-100 justify-content-center mt-3">
            <i class="bi bi-trophy"></i> Beat Your Record
          </a>
        </div>
      </div>
    </div>

    <!-- ================= ARCADE BANNER ================= -->
    <div class="mono-panel mb-4" style="padding: 1.15rem 1.35rem;">
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
          <div class="mono-eyebrow mb-1" style="font-size: .62rem;">Arcade Zone &bull; 5 Games</div>
          <h5 class="fw-bold text-light mb-1" style="font-size: .98rem;">Typing Arcade Arena</h5>
          <p class="text-secondary small mb-0" style="max-width: 640px;">
            Sharpen muscle memory with <strong style="color: var(--mono-text);">Keyboard Ninja</strong>, <strong style="color: var(--mono-text);">Space Word Defender</strong>, <strong style="color: var(--mono-text);">Cyber Grand Prix</strong>, and more.
          </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
          <a href="<?= BASE_URL ?>/game-ninja.php" class="mono-btn mono-btn-primary mono-btn-sm">
            <i class="bi bi-slash-circle"></i> Play Ninja
          </a>
          <a href="<?= BASE_URL ?>/games.php" class="mono-btn mono-btn-sm">
            <i class="bi bi-grid-3x3-gap"></i> All Games
          </a>
        </div>
      </div>
    </div>

    <!-- ================= TRAINING ================= -->
    <div class="mono-section-label">Quick Training Modes</div>
    <div class="row g-3 mb-4">
      <?php
      $modes = [
          ['title' => 'Foundational Home Row', 'desc' => 'Develop muscle memory and rhythmic finger placement.', 'tag' => 'Beginner', 'time' => '30s', 'url' => 'practice.php?difficulty=beginner&duration=30', 'icon' => 'bi-shield-check', 'cta' => 'Launch 30s'],
          ['title' => 'Standard Speed Sprint', 'desc' => 'Balanced mix of vocabulary, punctuation, and capitals.',  'tag' => 'Intermediate', 'time' => '60s', 'url' => 'practice.php?difficulty=intermediate&duration=60', 'icon' => 'bi-lightning-charge', 'cta' => 'Launch 60s'],
          ['title' => 'Endurance & Code', 'desc' => 'Complex passages with symbols, brackets, dev terms.', 'tag' => 'Advanced', 'time' => '180s', 'url' => 'practice.php?difficulty=advanced&duration=180', 'icon' => 'bi-fire', 'cta' => 'Launch 3m'],
          ['title' => 'Free Typing & Watch', 'desc' => 'Write freely. Live telemetry calculates silently.', 'tag' => 'Free', 'time' => 'Watch', 'url' => 'practice.php?mode=free', 'icon' => 'bi-pencil', 'cta' => 'Open Canvas'],
      ];
      foreach ($modes as $m): ?>
        <div class="col-sm-6 col-xl-3">
          <div class="mono-train">
            <div>
              <div class="mono-train-head">
                <span class="mono-tag"><?= $m['tag'] ?></span>
                <span class="mono-metric-tag" style="font-family: var(--mono-font-mono);"><i class="bi bi-stopwatch me-1"></i><?= $m['time'] ?></span>
              </div>
              <h6><i class="bi <?= $m['icon'] ?> me-1" style="color: var(--mono-text-muted);"></i><?= $m['title'] ?></h6>
              <p><?= $m['desc'] ?></p>
            </div>
            <a href="<?= BASE_URL ?>/<?= $m['url'] ?>" class="mono-btn mono-btn-sm w-100 justify-content-center">
              <i class="bi bi-play-fill"></i> <?= $m['cta'] ?>
            </a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- ================= RECENT SESSIONS ================= -->
    <div class="mono-panel">
      <div class="mono-panel-head">
        <h5 class="mono-panel-title"><i class="bi bi-clock-history"></i> Recent Sessions</h5>
        <a href="<?= BASE_URL ?>/history.php" class="mono-btn mono-btn-sm mono-btn-ghost">
          Full History <i class="bi bi-arrow-right"></i>
        </a>
      </div>

      <?php if (!empty($recentTests)): ?>
        <div class="table-responsive">
          <table class="mono-table">
            <thead>
              <tr>
                <th>Passage</th>
                <th>Difficulty</th>
                <th>Net WPM</th>
                <th>Gross</th>
                <th>Accuracy</th>
                <th>Duration</th>
                <th>Time</th>
                <th class="text-end">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recentTests as $row): ?>
                <tr>
                  <td><div class="mono-passage"><i class="bi bi-file-text me-1" style="color: var(--mono-text-muted);"></i><?= e($row['passage_title'] ?? 'Custom Text') ?></div></td>
                  <td><?= get_difficulty_badge($row['difficulty']) ?></td>
                  <td><span class="mono-wpm"><?= (float)$row['net_wpm'] ?></span></td>
                  <td class="font-monospace"><?= (float)$row['gross_wpm'] ?></td>
                  <td>
                    <div class="d-flex align-items-center gap-2">
                      <div class="mono-mini-bar"><span style="width: <?= (float)$row['accuracy'] ?>%;"></span></div>
                      <span class="font-monospace small" style="color: var(--mono-text);"><?= (float)$row['accuracy'] ?>%</span>
                    </div>
                  </td>
                  <td class="font-monospace"><?= (int)$row['duration_seconds'] ?>s</td>
                  <td class="text-muted small"><?= time_ago($row['created_at']) ?></td>
                  <td class="text-end">
                    <?php if (!empty($row['passage_id'])): ?>
                      <a href="<?= BASE_URL ?>/practice.php?passage_id=<?= (int)$row['passage_id'] ?>" class="mono-btn mono-btn-sm mono-btn-ghost" title="Practice again">
                        <i class="bi bi-arrow-repeat"></i>
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
        <div class="text-center py-5">
          <i class="bi bi-keyboard d-block mb-3" style="font-size: 3rem; color: var(--mono-text-muted); opacity: .3;"></i>
          <h5 class="text-light fw-bold">No sessions logged yet</h5>
          <p class="text-secondary small mb-3">Take your first typing drill to unlock analytics.</p>
          <a href="<?= BASE_URL ?>/practice.php" class="mono-btn mono-btn-primary mono-btn-sm">Start Practicing</a>
        </div>
      <?php endif; ?>
    </div>

  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  setTimeout(() => {
    const sk = document.getElementById('mono-skeleton');
    const content = document.getElementById('mono-content');
    if (sk && content) {
      sk.style.display = 'none';
      content.style.display = 'block';
      content.classList.add('mono-reveal');
    }
  }, 100);

  <?php if (!empty($weeklyChartData)): ?>
  const data = <?= json_encode($weeklyChartData) ?>;
  const ctx = document.getElementById('monoWeeklyChart');
  if (ctx) {
    const labels = data.map(d => d.test_day);
    const wpm = data.map(d => parseFloat(d.net_wpm));
    const acc = data.map(d => parseFloat(d.accuracy));

    const g = ctx.getContext('2d').createLinearGradient(0, 0, 0, 300);
    g.addColorStop(0, 'rgba(255,255,255,0.14)');
    g.addColorStop(1, 'rgba(255,255,255,0.00)');

    new Chart(ctx, {
      type: 'line',
      data: {
        labels,
        datasets: [
          {
            label: 'Net WPM',
            data: wpm,
            borderColor: '#ffffff',
            backgroundColor: g,
            fill: true,
            tension: 0.4,
            borderWidth: 2.5,
            pointRadius: 3,
            pointHoverRadius: 6,
            pointBackgroundColor: '#000',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            yAxisID: 'y'
          },
          {
            label: 'Accuracy',
            data: acc,
            borderColor: '#6b6b6b',
            borderDash: [5, 4],
            tension: 0.4,
            borderWidth: 1.5,
            pointRadius: 2.5,
            pointHoverRadius: 5,
            pointBackgroundColor: '#6b6b6b',
            yAxisID: 'y1'
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
          legend: {
            labels: {
              color: '#a1a1a1',
              font: { family: 'JetBrains Mono, monospace', size: 11, weight: '600' },
              usePointStyle: true,
              padding: 14,
              boxWidth: 8, boxHeight: 8
            }
          },
          tooltip: {
            backgroundColor: '#0a0a0a',
            borderColor: '#262626',
            borderWidth: 1,
            titleColor: '#ffffff',
            bodyColor: '#a1a1a1',
            padding: 12,
            cornerRadius: 6,
            titleFont: { family: 'JetBrains Mono, monospace', weight: '700' },
            bodyFont: { family: 'JetBrains Mono, monospace' }
          }
        },
        scales: {
          x: {
            grid: { color: 'rgba(255,255,255,0.04)' },
            ticks: { color: '#6b6b6b', font: { family: 'JetBrains Mono, monospace', size: 10 } }
          },
          y: {
            beginAtZero: true,
            grid: { color: 'rgba(255,255,255,0.06)' },
            ticks: { color: '#a1a1a1', font: { family: 'JetBrains Mono, monospace', size: 10 } },
            title: { display: true, text: 'WPM', color: '#a1a1a1', font: { family: 'JetBrains Mono, monospace', size: 10, weight: '600' } }
          },
          y1: {
            position: 'right', min: 60, max: 100,
            grid: { drawOnChartArea: false },
            ticks: { color: '#6b6b6b', font: { family: 'JetBrains Mono, monospace', size: 10 } },
            title: { display: true, text: 'ACCURACY %', color: '#6b6b6b', font: { family: 'JetBrains Mono, monospace', size: 10, weight: '600' } }
          }
        }
      }
    });
  }
  <?php endif; ?>
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>