 <?php
/**
 * Custom Type Master Pro
 * Typing Lessons Curriculum Dashboard — Pixel Theme, Clean Sans-Serif
 */

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/lessons_data.php';

$pageTitle = 'Student Typing Lessons — Curriculum';
$currentUser = current_user();
$userId = $currentUser ? (int)$currentUser['id'] : 0;

$curriculumStats = get_user_curriculum_stats($userId);
$allLessons = get_all_lessons();
$progressMap = $curriculumStats['progress_map'];

$recommendedLesson = $allLessons[0];
foreach ($allLessons as $l) {
    $lid = $l['id'];
    if (empty($progressMap[$lid]) || (int)$progressMap[$lid]['stars'] === 0) {
        $recommendedLesson = $l;
        break;
    }
}

include __DIR__ . '/includes/header.php';
?>

<style>
/* ============================================================
   LESSONS CURRICULUM — Pixel Theme + Clean Sans-Serif
   ============================================================ */
.lessons-page {
  --ink:        #000000;
  --paper:      #ffffff;
  --paper-soft: #f4f4f4;
  --paper-hard: #e6e6e6;
  --shadow:     3px;
  --sans:       "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
  --mono:       "JetBrains Mono", ui-monospace, Menlo, Consolas, monospace;
  font-family: var(--sans);
  color: var(--ink);
}
html[data-theme="dark"] .lessons-page {
  --ink:        #ffffff;
  --paper:      #000000;
  --paper-soft: #141414;
  --paper-hard: #222222;
}

.lessons-page .pixel-card {
  background: var(--paper);
  border: 3px solid var(--ink);
  box-shadow: var(--shadow) var(--shadow) 0 var(--ink);
}
.lessons-page .pixel-card-lg {
  background: var(--paper);
  border: 3px solid var(--ink);
  box-shadow: 5px 5px 0 var(--ink);
}

/* ---------- Buttons ---------- */
.lessons-page .px-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 0.65rem 1.1rem;
  font-family: var(--sans);
  font-size: 0.86rem;
  font-weight: 700;
  letter-spacing: 0.03em;
  text-transform: uppercase;
  background: var(--paper);
  color: var(--ink);
  border: 3px solid var(--ink);
  box-shadow: var(--shadow) var(--shadow) 0 var(--ink);
  cursor: pointer;
  line-height: 1.2;
  text-decoration: none;
  transition: transform 0.05s steps(1), box-shadow 0.05s steps(1), background 0.05s steps(1);
}
.lessons-page .px-btn:hover { background: var(--paper-soft); color: var(--ink); }
.lessons-page .px-btn:active {
  transform: translate(var(--shadow), var(--shadow));
  box-shadow: 0 0 0 var(--ink);
}
.lessons-page .px-btn i { font-size: 0.95rem; }
.lessons-page .px-btn-primary {
  background: var(--ink);
  color: var(--paper);
}
.lessons-page .px-btn-primary:hover { background: var(--ink); color: var(--paper); }
.lessons-page .px-btn-primary i { color: var(--paper); }

.lessons-page .px-btn-sm {
  padding: 0.5rem 0.85rem;
  font-size: 0.76rem;
  box-shadow: 2px 2px 0 var(--ink);
}
.lessons-page .px-btn-sm:active {
  transform: translate(2px, 2px);
  box-shadow: 0 0 0 var(--ink);
}

/* ---------- Hero block ---------- */
.lessons-page .hero-block {
  background: var(--paper);
  border: 3px solid var(--ink);
  box-shadow: 5px 5px 0 var(--ink);
  padding: 2rem 1.75rem;
  margin-bottom: 1.25rem;
  position: relative;
  overflow: hidden;
}
.lessons-page .hero-block::before {
  content: "";
  position: absolute;
  inset: 0;
  background-image:
    linear-gradient(45deg, rgba(0,0,0,0.03) 25%, transparent 25%),
    linear-gradient(-45deg, rgba(0,0,0,0.03) 25%, transparent 25%),
    linear-gradient(45deg, transparent 75%, rgba(0,0,0,0.03) 75%),
    linear-gradient(-45deg, transparent 75%, rgba(0,0,0,0.03) 75%);
  background-size: 8px 8px;
  background-position: 0 0, 0 4px, 4px -4px, -4px 0;
  pointer-events: none;
}
html[data-theme="dark"] .lessons-page .hero-block::before {
  background-image:
    linear-gradient(45deg, rgba(255,255,255,0.04) 25%, transparent 25%),
    linear-gradient(-45deg, rgba(255,255,255,0.04) 25%, transparent 25%),
    linear-gradient(45deg, transparent 75%, rgba(255,255,255,0.04) 75%),
    linear-gradient(-45deg, transparent 75%, rgba(255,255,255,0.04) 75%);
}
.lessons-page .hero-block > * { position: relative; z-index: 1; }

.lessons-page .hero-eyebrow {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.3rem 0.65rem;
  background: var(--ink);
  color: var(--paper);
  border: 2px solid var(--ink);
  font-family: var(--sans);
  font-size: 0.68rem;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  margin-bottom: 1rem;
}
.lessons-page .hero-eyebrow i { font-size: 0.8rem; color: var(--paper); }

.lessons-page .hero-title {
  font-family: var(--sans);
  font-size: clamp(1.8rem, 3.6vw, 2.6rem);
  font-weight: 800;
  letter-spacing: -0.03em;
  line-height: 1.1;
  color: var(--ink);
  margin: 0 0 0.85rem;
}
.lessons-page .hero-title .accent {
  display: inline-block;
  border-bottom: 5px solid var(--ink);
  padding-bottom: 1px;
}

.lessons-page .hero-lead {
  font-family: var(--sans);
  font-size: 0.96rem;
  line-height: 1.65;
  color: var(--ink);
  opacity: 0.8;
  max-width: 640px;
  margin: 0 0 1.25rem;
}

.lessons-page .hero-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 0.6rem;
}

/* ---------- Mastery widget ---------- */
.lessons-page .mastery-widget {
  background: var(--paper-soft);
  border: 3px solid var(--ink);
  box-shadow: var(--shadow) var(--shadow) 0 var(--ink);
  padding: 1rem;
}
.lessons-page .mastery-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.65rem;
}
.lessons-page .mastery-title {
  font-family: var(--sans);
  font-size: 0.7rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--ink);
  opacity: 0.75;
}
.lessons-page .mastery-pct {
  font-family: var(--mono);
  font-size: 1rem;
  font-weight: 700;
  color: var(--ink);
}

.lessons-page .progress-rail {
  height: 12px;
  background: var(--paper-hard);
  border: 2px solid var(--ink);
  margin-bottom: 0.85rem;
  position: relative;
  overflow: hidden;
}
.lessons-page .progress-fill {
  height: 100%;
  background: var(--ink);
  width: 0;
  transition: width 0.3s ease;
}

.lessons-page .mastery-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.4rem;
}
.lessons-page .mastery-cell {
  background: var(--paper);
  border: 2px solid var(--ink);
  padding: 0.5rem 0.4rem;
  text-align: center;
}
.lessons-page .mastery-cell .mc-label {
  font-family: var(--sans);
  font-size: 0.6rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--ink);
  opacity: 0.7;
  margin-bottom: 0.2rem;
  line-height: 1.2;
}
.lessons-page .mastery-cell .mc-value {
  font-family: var(--mono);
  font-size: 1rem;
  font-weight: 700;
  color: var(--ink);
  line-height: 1.1;
}
.lessons-page .mastery-cell .mc-value small {
  font-size: 0.68rem;
  opacity: 0.6;
  font-weight: 600;
}

/* ---------- Filter tabs ---------- */
.lessons-page .filter-tabs-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.85rem;
  margin-bottom: 1rem;
  flex-wrap: wrap;
}
.lessons-page .filter-tabs {
  display: flex;
  flex-wrap: wrap;
  gap: 0.4rem;
  list-style: none;
  padding: 0;
  margin: 0;
}
.lessons-page .filter-tabs .nav-item { margin: 0; }
.lessons-page .filter-tab {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.45rem 0.85rem;
  background: var(--paper);
  color: var(--ink);
  border: 3px solid var(--ink);
  box-shadow: 2px 2px 0 var(--ink);
  font-family: var(--sans);
  font-size: 0.78rem;
  font-weight: 600;
  letter-spacing: 0.02em;
  cursor: pointer;
  line-height: 1.15;
  text-decoration: none;
  transition: transform 0.05s steps(1), box-shadow 0.05s steps(1), background 0.05s steps(1);
  -webkit-appearance: none;
  appearance: none;
}
.lessons-page .filter-tab:hover { background: var(--paper-soft); color: var(--ink); }
.lessons-page .filter-tab:active {
  transform: translate(2px, 2px);
  box-shadow: 0 0 0 var(--ink);
}
.lessons-page .filter-tab.active {
  background: var(--ink);
  color: var(--paper);
  box-shadow: 0 0 0 var(--ink);
}
.lessons-page .filter-tab.active i { color: var(--paper); }
.lessons-page .filter-tab .tab-count {
  font-family: var(--mono);
  font-size: 0.7rem;
  font-weight: 700;
  padding: 0.05rem 0.4rem;
  background: var(--paper-hard);
  color: var(--ink);
  border: 2px solid var(--ink);
  margin-left: 0.15rem;
}
.lessons-page .filter-tab.active .tab-count {
  background: var(--paper);
  color: var(--ink);
  border-color: var(--paper);
}

.lessons-page .filter-hint {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  font-family: var(--sans);
  font-size: 0.76rem;
  color: var(--ink);
  opacity: 0.75;
  font-weight: 500;
}
.lessons-page .filter-hint i { font-size: 0.85rem; }
.lessons-page .filter-hint strong { font-weight: 700; opacity: 1; }

/* ---------- Lesson cards ---------- */
.lessons-page .lesson-card {
  background: var(--paper);
  border: 3px solid var(--ink);
  box-shadow: var(--shadow) var(--shadow) 0 var(--ink);
  padding: 1.25rem 1.25rem 1rem;
  height: 100%;
  display: flex;
  flex-direction: column;
  transition: transform 0.05s steps(1), box-shadow 0.05s steps(1);
  image-rendering: pixelated;
}
.lessons-page .lesson-card:hover {
  transform: translate(-2px, -2px);
  box-shadow: 5px 5px 0 var(--ink);
}
.lessons-page .lesson-card:active {
  transform: translate(var(--shadow), var(--shadow));
  box-shadow: 0 0 0 var(--ink);
}

.lessons-page .lc-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.5rem;
  margin-bottom: 0.75rem;
}
.lessons-page .lc-lesson-num {
  font-family: var(--mono);
  font-size: 0.68rem;
  font-weight: 700;
  letter-spacing: 0.06em;
  padding: 0.2rem 0.5rem;
  background: var(--paper-soft);
  color: var(--ink);
  border: 2px solid var(--ink);
}
.lessons-page .lc-stage-badge {
  font-family: var(--sans);
  font-size: 0.62rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  padding: 0.2rem 0.5rem;
  background: var(--paper);
  color: var(--ink);
  border: 2px solid var(--ink);
}
.lessons-page .lc-stage-badge.stage-beginner   { background: var(--paper); }
.lessons-page .lc-stage-badge.stage-intermediate { background: var(--paper-soft); }
.lessons-page .lc-stage-badge.stage-advanced   { background: var(--ink); color: var(--paper); }

.lessons-page .lc-title {
  font-family: var(--sans);
  font-size: 1.05rem;
  font-weight: 800;
  letter-spacing: -0.01em;
  line-height: 1.3;
  color: var(--ink);
  margin: 0 0 0.5rem;
}
.lessons-page .lc-desc {
  font-family: var(--sans);
  font-size: 0.82rem;
  line-height: 1.55;
  color: var(--ink);
  opacity: 0.75;
  margin: 0 0 0.9rem;
  min-height: 2.6em;
}

.lessons-page .lc-keys {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 0.3rem;
  margin-bottom: 1rem;
}
.lessons-page .lc-keys-label {
  font-family: var(--sans);
  font-size: 0.62rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--ink);
  opacity: 0.6;
  margin-right: 0.25rem;
}
.lessons-page .lc-key-chip {
  font-family: var(--mono);
  font-size: 0.72rem;
  font-weight: 700;
  padding: 0.15rem 0.45rem;
  background: var(--paper-soft);
  color: var(--ink);
  border: 2px solid var(--ink);
}

.lessons-page .lc-footer {
  margin-top: auto;
  padding-top: 0.85rem;
  border-top: 2px dashed var(--ink);
}
.lessons-page .lc-meta-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.5rem;
  margin-bottom: 0.65rem;
}
.lessons-page .lc-stars {
  display: inline-flex;
  align-items: center;
  gap: 0.15rem;
}
.lessons-page .lc-stars i {
  font-size: 0.95rem;
  color: var(--ink);
}
.lessons-page .lc-stars i.bi-star {
  opacity: 0.2;
}
.lessons-page .lc-stars-text {
  font-family: var(--sans);
  font-size: 0.7rem;
  font-weight: 600;
  color: var(--ink);
  opacity: 0.7;
  margin-left: 0.3rem;
}
.lessons-page .lc-drill-count {
  font-family: var(--sans);
  font-size: 0.7rem;
  font-weight: 600;
  color: var(--ink);
  opacity: 0.7;
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
}
.lessons-page .lc-drill-count i { font-size: 0.78rem; }

.lessons-page .lc-best-record {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.4rem;
  padding: 0.4rem 0.6rem;
  background: var(--paper-soft);
  border: 2px solid var(--ink);
  font-family: var(--sans);
  font-size: 0.72rem;
  font-weight: 600;
  color: var(--ink);
  margin-bottom: 0.65rem;
}
.lessons-page .lc-best-record .br-label {
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
  opacity: 0.85;
}
.lessons-page .lc-best-record .br-value {
  font-family: var(--mono);
  font-weight: 700;
  font-size: 0.74rem;
}

.lessons-page .lc-cta {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.4rem;
  width: 100%;
  padding: 0.6rem 0.85rem;
  font-family: var(--sans);
  font-size: 0.78rem;
  font-weight: 700;
  letter-spacing: 0.03em;
  text-transform: uppercase;
  border: 3px solid var(--ink);
  box-shadow: 2px 2px 0 var(--ink);
  background: var(--paper);
  color: var(--ink);
  cursor: pointer;
  line-height: 1.2;
  text-decoration: none;
  transition: transform 0.05s steps(1), box-shadow 0.05s steps(1), background 0.05s steps(1);
  -webkit-appearance: none;
  appearance: none;
}
.lessons-page .lc-cta:hover { background: var(--paper-soft); color: var(--ink); }
.lessons-page .lc-cta:active {
  transform: translate(2px, 2px);
  box-shadow: 0 0 0 var(--ink);
}
.lessons-page .lc-cta.is-start {
  background: var(--ink);
  color: var(--paper);
}
.lessons-page .lc-cta.is-start:hover { background: var(--ink); color: var(--paper); }
.lessons-page .lc-cta.is-start i { color: var(--paper); }

.lessons-page .lc-cta i { font-size: 0.85rem; }

/* Responsive */
@media (max-width: 768px) {
  .lessons-page .hero-block { padding: 1.35rem 1.1rem; }
  .lessons-page .hero-title { font-size: 1.6rem; }
  .lessons-page .mastery-widget { padding: 0.85rem; }
  .lessons-page .lesson-card { padding: 1.1rem 1rem 0.9rem; }
  .lessons-page .lc-title { font-size: 1rem; }
  .lessons-page .filter-tab { padding: 0.4rem 0.65rem; font-size: 0.72rem; }
  .lessons-page .filter-tab .tab-count { font-size: 0.64rem; }
}
@media (max-width: 575px) {
  .lessons-page .hero-eyebrow { font-size: 0.6rem; padding: 0.25rem 0.5rem; }
  .lessons-page .hero-title { font-size: 1.4rem; }
  .lessons-page .hero-lead { font-size: 0.88rem; }
  .lessons-page .mastery-cell .mc-value { font-size: 0.9rem; }
  .lessons-page .lc-desc { font-size: 0.78rem; }
}
</style>

<div class="lessons-page content-wrapper">

  <!-- Hero -->
  <div class="hero-block">
    <div class="row align-items-center g-4">
      <div class="col-lg-8">
        <div class="hero-eyebrow">
          <i class="bi bi-mortarboard-fill"></i> Typing.com Inspired Curriculum
        </div>
        <h1 class="hero-title">
          Master Touch Typing,<br><span class="accent">Step by Step</span>
        </h1>
        <p class="hero-lead">
          From foundational home row anchor keys to complex code syntax and speed certification. Learn muscle memory with interactive finger guides, real-time key visualization, and three-star achievement goals.
        </p>

        <div class="hero-actions">
          <a href="<?= BASE_URL ?>/lesson-player.php?id=<?= urlencode($recommendedLesson['id']) ?>" class="px-btn px-btn-primary">
            <i class="bi bi-play-circle-fill"></i>
            <span><?= $curriculumStats['completed_lessons'] > 0 ? 'Continue: ' . e($recommendedLesson['title']) : 'Start Lesson 1: Home Row' ?></span>
          </a>
          <a href="<?= BASE_URL ?>/practice.php" class="px-btn">
            <i class="bi bi-keyboard"></i>
            <span>Open Practice Arena</span>
          </a>
        </div>
      </div>

      <!-- Mastery widget -->
      <div class="col-lg-4">
        <div class="mastery-widget">
          <div class="mastery-head">
            <span class="mastery-title">Curriculum Mastery</span>
            <span class="mastery-pct"><?= $curriculumStats['completion_pct'] ?>%</span>
          </div>

          <div class="progress-rail">
            <div class="progress-fill" style="width: <?= $curriculumStats['completion_pct'] ?>%;"></div>
          </div>

          <div class="mastery-grid">
            <div class="mastery-cell">
              <div class="mc-label">Lessons</div>
              <div class="mc-value"><?= $curriculumStats['completed_lessons'] ?> <small>/ <?= $curriculumStats['total_lessons'] ?></small></div>
            </div>
            <div class="mastery-cell">
              <div class="mc-label">Stars</div>
              <div class="mc-value"><?= $curriculumStats['total_stars'] ?> <small>/ <?= $curriculumStats['max_stars'] ?></small></div>
            </div>
            <div class="mastery-cell">
              <div class="mc-label">Avg Speed</div>
              <div class="mc-value"><?= $curriculumStats['avg_wpm'] ?> <small>WPM</small></div>
            </div>
            <div class="mastery-cell">
              <div class="mc-label">Avg Accuracy</div>
              <div class="mc-value"><?= $curriculumStats['avg_accuracy'] ?><small>%</small></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Filter tabs -->
  <div class="filter-tabs-row">
    <ul class="filter-tabs nav" id="lessonTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="filter-tab active" id="tab-all-btn" data-stage="all" type="button">
          All Lessons
          <span class="tab-count"><?= count($allLessons) ?></span>
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="filter-tab" id="tab-beginner-btn" data-stage="beginner" type="button">
          <i class="bi bi-compass"></i> Beginner
          <span class="tab-count"><?= $curriculumStats['stage_stats']['beginner']['completed'] ?>/9</span>
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="filter-tab" id="tab-intermediate-btn" data-stage="intermediate" type="button">
          <i class="bi bi-lightning-charge"></i> Intermediate
          <span class="tab-count"><?= $curriculumStats['stage_stats']['intermediate']['completed'] ?>/6</span>
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="filter-tab" id="tab-advanced-btn" data-stage="advanced" type="button">
          <i class="bi bi-award"></i> Advanced
          <span class="tab-count"><?= $curriculumStats['stage_stats']['advanced']['completed'] ?>/5</span>
        </button>
      </li>
    </ul>

    <div class="filter-hint">
      <i class="bi bi-info-circle"></i>
      <span>Earn 3 Stars with <strong>95%+ accuracy</strong></span>
    </div>
  </div>

  <!-- Lessons grid -->
  <div class="row g-3 g-md-4" id="lessonsGrid">
    <?php foreach ($allLessons as $index => $lesson):
      $lid = $lesson['id'];
      $progress = $progressMap[$lid] ?? null;
      $starsEarned = $progress ? (int)$progress['stars'] : 0;
      $isCompleted = ($starsEarned > 0 || ($progress && $progress['status'] === 'completed'));
      $bestWpm = $progress ? (float)$progress['highest_wpm'] : 0.0;
      $bestAcc = $progress ? (float)$progress['highest_accuracy'] : 0.0;
      $stage = $lesson['stage'];
    ?>
      <div class="col-md-6 col-xl-4 lesson-card-col" data-stage="<?= e($stage) ?>">
        <div class="lesson-card">

          <!-- Top row -->
          <div class="lc-top">
            <span class="lc-lesson-num">LESSON <?= sprintf('%02d', $lesson['number']) ?></span>
            <span class="lc-stage-badge stage-<?= e($stage) ?>"><?= e($lesson['stage_title']) ?></span>
          </div>

          <!-- Title + desc -->
          <h5 class="lc-title"><?= e($lesson['title']) ?></h5>
          <p class="lc-desc"><?= e($lesson['description']) ?></p>

          <!-- Keys -->
          <div class="lc-keys">
            <span class="lc-keys-label">Keys:</span>
            <?php foreach ($lesson['keys'] as $keyName): ?>
              <span class="lc-key-chip"><?= e($keyName) ?></span>
            <?php endforeach; ?>
          </div>

          <!-- Footer -->
          <div class="lc-footer">
            <div class="lc-meta-row">
              <div class="lc-stars">
                <?php for ($s = 1; $s <= 3; $s++): ?>
                  <i class="bi bi-star<?= ($s <= $starsEarned) ? '-fill' : '' ?>"></i>
                <?php endfor; ?>
                <span class="lc-stars-text"><?= $starsEarned > 0 ? "{$starsEarned}/3 Stars" : 'Not Started' ?></span>
              </div>
              <span class="lc-drill-count">
                <i class="bi bi-layers"></i> <?= count($lesson['exercises']) ?> Drills
              </span>
            </div>

            <?php if ($isCompleted): ?>
              <div class="lc-best-record">
                <span class="br-label">
                  <i class="bi bi-check-circle-fill"></i> Best:
                </span>
                <span class="br-value"><?= (int)$bestWpm ?> WPM · <?= (int)$bestAcc ?>%</span>
              </div>
            <?php endif; ?>

            <a href="<?= BASE_URL ?>/lesson-player.php?id=<?= urlencode($lesson['id']) ?>"
               class="lc-cta <?= $isCompleted ? '' : 'is-start' ?>">
              <span><?= $isCompleted ? 'Practice Again' : 'Start Lesson' ?></span>
              <i class="bi bi-arrow-right"></i>
            </a>
          </div>

        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const tabs = document.querySelectorAll('#lessonTabs button');
  const cards = document.querySelectorAll('.lesson-card-col');

  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      tabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');

      const stage = tab.getAttribute('data-stage');
      cards.forEach(card => {
        if (stage === 'all' || card.getAttribute('data-stage') === stage) {
          card.style.display = '';
        } else {
          card.style.display = 'none';
        }
      });
    });
  });
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>