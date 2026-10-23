<?php
/**
 * Custom Type Master Pro
 * Typing Lessons Curriculum Dashboard (Typing.com Style)
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

// Determine recommended next lesson
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

<div class="content-wrapper">
  <!-- Hero Section -->
  <div class="glass-card p-4 p-md-5 mb-4 position-relative overflow-hidden" style="background: linear-gradient(135deg, rgba(20,20,20,0.85) 0%, rgba(10,10,10,0.95) 100%); border: 1px solid var(--border-color); border-radius: 16px;">
    <div class="row align-items-center g-4">
      <div class="col-lg-8">
        <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-3" style="background: rgba(255,255,255,0.06); border: 1px solid var(--border-color); font-size: 0.8rem; font-weight: 700; letter-spacing: 0.05em; color: var(--first-color);">
          <i class="bi bi-mortarboard-fill"></i> TYPING.COM INSPIRED CURRICULUM
        </div>
        <h1 class="display-6 fw-bold mb-2 text-white" style="letter-spacing: -0.03em;">
          Master Touch Typing, <span style="color: var(--first-color);">Step by Step</span>
        </h1>
        <p class="text-secondary mb-4" style="max-width: 640px; font-size: 1.02rem; line-height: 1.6;">
          From foundational home row anchor keys to complex code syntax and speed certification. Learn muscle memory with interactive finger guides, real-time key visualization, and three-star achievement goals.
        </p>

        <div class="d-flex flex-wrap align-items-center gap-3">
          <a href="<?= BASE_URL ?>/lesson-player.php?id=<?= urlencode($recommendedLesson['id']) ?>" class="btn btn-primary px-4 py-2.5 fw-bold d-inline-flex align-items-center gap-2 shadow-sm" style="background: var(--first-color); border: none; border-radius: 10px;">
            <i class="bi bi-play-circle-fill fs-5"></i>
            <span><?= $curriculumStats['completed_lessons'] > 0 ? 'Continue: ' . e($recommendedLesson['title']) : 'Start Lesson 1: Home Row' ?></span>
          </a>
          <a href="<?= BASE_URL ?>/practice.php" class="btn btn-outline-secondary px-3 py-2.5 fw-semibold d-inline-flex align-items-center gap-2 text-white" style="border-radius: 10px; border-color: var(--border-color);">
            <i class="bi bi-keyboard"></i> Open Practice Arena
          </a>
        </div>
      </div>

      <!-- Stats Summary Widget -->
      <div class="col-lg-4">
        <div class="p-3.5 rounded-3" style="background: var(--bg-secondary); border: 1px solid var(--border-color);">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="text-secondary small fw-bold text-uppercase" style="letter-spacing: 0.05em;">Curriculum Mastery</span>
            <span class="fw-bold text-white"><?= $curriculumStats['completion_pct'] ?>%</span>
          </div>
          <div class="progress mb-3" style="height: 8px; background: rgba(255,255,255,0.08); border-radius: 4px;">
            <div class="progress-bar" role="progressbar" style="width: <?= $curriculumStats['completion_pct'] ?>%; background: var(--first-color);" aria-valuenow="<?= $curriculumStats['completion_pct'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
          </div>

          <div class="row g-2 text-center">
            <div class="col-6">
              <div class="p-2.5 rounded-2" style="background: var(--bg-primary); border: 1px solid var(--border-color);">
                <div class="text-secondary" style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase;">Lessons</div>
                <div class="fw-bold text-white fs-5"><?= $curriculumStats['completed_lessons'] ?> <span class="text-secondary" style="font-size: 0.8rem;">/ <?= $curriculumStats['total_lessons'] ?></span></div>
              </div>
            </div>
            <div class="col-6">
              <div class="p-2.5 rounded-2" style="background: var(--bg-primary); border: 1px solid var(--border-color);">
                <div class="text-secondary" style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase;">Stars</div>
                <div class="fw-bold fs-5 text-warning"><i class="bi bi-star-fill me-1" style="font-size: 0.85rem;"></i><?= $curriculumStats['total_stars'] ?> <span class="text-secondary" style="font-size: 0.8rem;">/ <?= $curriculumStats['max_stars'] ?></span></div>
              </div>
            </div>
            <div class="col-6">
              <div class="p-2.5 rounded-2" style="background: var(--bg-primary); border: 1px solid var(--border-color);">
                <div class="text-secondary" style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase;">Avg Speed</div>
                <div class="fw-bold text-white fs-5"><?= $curriculumStats['avg_wpm'] ?> <span class="text-secondary" style="font-size: 0.75rem;">WPM</span></div>
              </div>
            </div>
            <div class="col-6">
              <div class="p-2.5 rounded-2" style="background: var(--bg-primary); border: 1px solid var(--border-color);">
                <div class="text-secondary" style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase;">Avg Accuracy</div>
                <div class="fw-bold text-white fs-5"><?= $curriculumStats['avg_accuracy'] ?>%</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Stage Navigation Filter Tabs -->
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <ul class="nav nav-pills gap-2" id="lessonTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active rounded-pill px-3 py-1.5 fw-semibold" id="tab-all-btn" data-stage="all" type="button">
          All Lessons <span class="badge bg-black text-white ms-1 px-1.5">20</span>
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link rounded-pill px-3 py-1.5 fw-semibold" id="tab-beginner-btn" data-stage="beginner" type="button">
          <i class="bi bi-compass me-1"></i> Beginner <span class="badge bg-black text-white ms-1 px-1.5"><?= $curriculumStats['stage_stats']['beginner']['completed'] ?>/9</span>
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link rounded-pill px-3 py-1.5 fw-semibold" id="tab-intermediate-btn" data-stage="intermediate" type="button">
          <i class="bi bi-lightning-charge me-1"></i> Intermediate <span class="badge bg-black text-white ms-1 px-1.5"><?= $curriculumStats['stage_stats']['intermediate']['completed'] ?>/6</span>
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link rounded-pill px-3 py-1.5 fw-semibold" id="tab-advanced-btn" data-stage="advanced" type="button">
          <i class="bi bi-award me-1"></i> Advanced <span class="badge bg-black text-white ms-1 px-1.5"><?= $curriculumStats['stage_stats']['advanced']['completed'] ?>/5</span>
        </button>
      </li>
    </ul>

    <div class="d-flex align-items-center gap-2 text-secondary small">
      <i class="bi bi-info-circle"></i>
      <span>Earn 3 Stars by typing with <strong>95%+ accuracy</strong></span>
    </div>
  </div>

  <!-- Lessons Grid -->
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
        <div class="glass-card h-100 p-4 d-flex flex-column justify-content-between position-relative transition-all" style="background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: 14px; transition: transform 0.2s ease, border-color 0.2s ease;">
          <div>
            <!-- Card Header: Number & Stage Badge -->
            <div class="d-flex align-items-center justify-content-between mb-2">
              <span class="badge px-2 py-1 rounded" style="font-size: 0.7rem; font-weight: 800; letter-spacing: 0.05em; background: rgba(255,255,255,0.06); color: var(--text-secondary); border: 1px solid var(--border-color);">
                LESSON <?= sprintf('%02d', $lesson['number']) ?>
              </span>
              <span class="badge px-2 py-1 rounded text-uppercase" style="font-size: 0.68rem; font-weight: 700; background: <?= ($stage === 'beginner') ? 'rgba(56, 189, 248, 0.15); color: #38bdf8;' : (($stage === 'intermediate') ? 'rgba(250, 204, 21, 0.15); color: #facc15;' : 'rgba(244, 63, 94, 0.15); color: #f43f5e;') ?>">
                <?= e($lesson['stage_title']) ?>
              </span>
            </div>

            <!-- Title & Subtitle -->
            <h5 class="fw-bold text-white mb-2" style="letter-spacing: -0.02em; font-size: 1.1rem; line-height: 1.35;">
              <?= e($lesson['title']) ?>
            </h5>
            <p class="text-secondary small mb-3" style="line-height: 1.5; min-height: 42px;">
              <?= e($lesson['description']) ?>
            </p>

            <!-- Target Keys Chips -->
            <div class="d-flex flex-wrap align-items-center gap-1.5 mb-3">
              <span class="text-secondary me-1" style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase;">Keys:</span>
              <?php foreach ($lesson['keys'] as $keyName): ?>
                <span class="px-2 py-0.5 rounded font-monospace small" style="background: var(--bg-primary); border: 1px solid var(--border-color); font-size: 0.78rem; font-weight: 700; color: var(--text-primary); box-shadow: 0 1px 2px rgba(0,0,0,0.2);">
                  <?= e($keyName) ?>
                </span>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Card Footer: Stars & Action Button -->
          <div class="pt-3 border-top mt-2" style="border-color: var(--border-color) !important;">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <!-- Star Rating -->
              <div class="d-flex align-items-center gap-1">
                <?php for ($s = 1; $s <= 3; $s++): ?>
                  <i class="bi bi-star<?= ($s <= $starsEarned) ? '-fill text-warning' : ' text-secondary' ?>" style="font-size: 1.1rem; <?= ($s <= $starsEarned) ? 'filter: drop-shadow(0 0 4px rgba(250,204,21,0.5));' : 'opacity: 0.35;' ?>"></i>
                <?php endfor; ?>
                <span class="ms-1.5 small text-secondary" style="font-size: 0.75rem;">
                  <?= $starsEarned > 0 ? "{$starsEarned}/3 Stars" : 'Not Started' ?>
                </span>
              </div>

              <!-- Exercise count badge -->
              <span class="text-secondary small" style="font-size: 0.75rem;">
                <i class="bi bi-layers me-1"></i><?= count($lesson['exercises']) ?> Drills
              </span>
            </div>

            <!-- Best Record if completed -->
            <?php if ($isCompleted): ?>
              <div class="d-flex justify-content-between align-items-center px-2.5 py-1.5 rounded mb-3" style="background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.2); font-size: 0.75rem; color: #10b981;">
                <span><i class="bi bi-check-circle-fill me-1"></i> Best Record:</span>
                <span class="fw-bold font-monospace"><?= (int)$bestWpm ?> WPM • <?= (int)$bestAcc ?>% ACC</span>
              </div>
            <?php endif; ?>

            <a href="<?= BASE_URL ?>/lesson-player.php?id=<?= urlencode($lesson['id']) ?>" class="btn btn-sm w-100 fw-bold d-flex align-items-center justify-content-center gap-2 py-2" style="background: <?= $isCompleted ? 'var(--bg-primary)' : 'var(--first-color)' ?>; color: <?= $isCompleted ? 'var(--text-primary)' : '#ffffff' ?>; border: 1px solid <?= $isCompleted ? 'var(--border-color)' : 'transparent' ?>; border-radius: 8px;">
              <span><?= $isCompleted ? 'Practice Again' : 'Start Lesson' ?></span>
              <i class="bi bi-arrow-right"></i>
            </a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<style>
.lesson-card-col .glass-card:hover {
  transform: translateY(-3px);
  border-color: rgba(255, 255, 255, 0.25) !important;
}
#lessonTabs .nav-link {
  color: var(--text-secondary);
  background: var(--bg-secondary);
  border: 1px solid var(--border-color);
  font-size: 0.84rem;
  transition: all 0.2s ease;
}
#lessonTabs .nav-link.active,
#lessonTabs .nav-link:hover {
  color: #fff;
  background: var(--first-color);
  border-color: var(--first-color);
}
#lessonTabs .nav-link.active .badge {
  background: rgba(0,0,0,0.3) !important;
  color: #fff !important;
}
</style>

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
