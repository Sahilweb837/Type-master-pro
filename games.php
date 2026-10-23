<?php
/**
 * Custom Type Master Pro
 * Arcade Games Hub
 * Central portal for 5 arcade typing games:
 * 1. Keyboard Ninja
 * 2. Space Word Defender
 * 3. Cyber Grand Prix
 * 4. Type-a-Balloon
 * 5. Meteor Matrix Defense
 */

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/games_data.php';

$currentUser = current_user();
$userId = $currentUser ? (int)$currentUser['id'] : 0;

// High scores for all 5 games
$highScores = [
    'ninja'   => get_user_game_high_score($userId, 'ninja'),
    'space'   => get_user_game_high_score($userId, 'space'),
    'racer'   => get_user_game_high_score($userId, 'racer'),
    'balloon' => get_user_game_high_score($userId, 'balloon'),
    'meteor'  => get_user_game_high_score($userId, 'meteor')
];

// Leaderboards for all 5 games
$leaderboards = [
    'ninja'   => get_game_leaderboard('ninja', 6),
    'space'   => get_game_leaderboard('space', 6),
    'racer'   => get_game_leaderboard('racer', 6),
    'balloon' => get_game_leaderboard('balloon', 6),
    'meteor'  => get_game_leaderboard('meteor', 6)
];

$allGames = get_all_games();

$pageTitle = 'Arcade Games Hub — 5 Typing Arcade Challenges';
$pageBadgeTitle = 'Arcade Games';
$pageIcon = 'bi-controller';

include __DIR__ . '/includes/header.php';
?>

<style>
/* Arcade Hub Styles */
.arcade-hub-header {
  background: radial-gradient(circle at top right, rgba(255, 77, 0, 0.08) 0%, transparent 60%),
              radial-gradient(circle at bottom left, rgba(56, 189, 248, 0.08) 0%, transparent 60%),
              var(--bg-card);
  border: 1px solid var(--border-color);
  border-radius: 16px;
  padding: 36px 30px;
  margin-bottom: 30px;
  position: relative;
  overflow: hidden;
}

.arcade-game-card {
  background: var(--bg-card);
  border: 1px solid var(--border-color);
  border-radius: 16px;
  padding: 24px;
  height: 100%;
  display: flex;
  flex-direction: column;
  position: relative;
  transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
  overflow: hidden;
}

.arcade-game-card:hover {
  transform: translateY(-4px);
  border-color: rgba(255, 255, 255, 0.2);
  box-shadow: 0 16px 40px rgba(0, 0, 0, 0.15);
}

.card-hero-icon {
  width: 52px;
  height: 52px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.6rem;
  margin-bottom: 16px;
}

.card-tag-pill {
  font-size: 0.7rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  padding: 4px 10px;
  border-radius: 20px;
  display: inline-block;
}

.feature-pill-badge {
  background: var(--bg-primary);
  border: 1px solid var(--border-color);
  color: var(--text-secondary);
  font-size: 0.74rem;
  font-weight: 600;
  padding: 3px 8px;
  border-radius: 6px;
  display: inline-flex;
  align-items: center;
  gap: 4px;
}

.score-stat-box {
  background: var(--bg-primary);
  border: 1px solid var(--border-color);
  border-radius: 10px;
  padding: 9px 12px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.leaderboard-table th {
  font-size: 0.72rem;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: var(--text-secondary);
  font-weight: 700;
  border-bottom: 1px solid var(--border-color);
  padding: 12px 16px;
}

.leaderboard-table td {
  padding: 12px 16px;
  vertical-align: middle;
  border-bottom: 1px solid var(--border-color);
  font-size: 0.88rem;
}
</style>

<div class="container-fluid py-2 px-md-3">

  <!-- Header Banner -->
  <div class="arcade-hub-header">
    <div class="row align-items-center g-4">
      <div class="col-lg-8">
        <div class="d-inline-flex align-items-center gap-2 px-3 py-1 mb-3 rounded-pill" style="background: rgba(255, 77, 0, 0.12); border: 1px solid rgba(255, 77, 0, 0.3); color: #ff4d00; font-size: 0.78rem; font-weight: 800; letter-spacing: 0.06em;">
          <i class="bi bi-controller"></i> ARCADE TYPING ZONE &bull; 5 FULL GAMES
        </div>
        <h1 class="fw-bold mb-2" style="font-size: 2.2rem; letter-spacing: -0.03em; color: var(--text-primary);">
          Typing Arcade Arena
        </h1>
        <p class="text-secondary mb-3" style="max-width: 680px; font-size: 0.98rem; line-height: 1.6;">
          Level up your typing speed, peripheral perception, and muscle memory through 5 gamified retro arcade challenges. Slash flying fruit with katanas, fire orbital defense lasers, burn highway asphalt at 180 MPH, pop rising sky balloons, and neutralize falling meteors!
        </p>

        <div class="d-flex align-items-center gap-2 flex-wrap">
          <a href="<?= BASE_URL ?>/game-ninja.php" class="soft-btn soft-btn-primary px-3 py-2 fw-bold d-inline-flex align-items-center gap-1.5" style="font-size: 0.85rem;">
            <i class="bi bi-slash-circle-fill"></i> Keyboard Ninja
          </a>
          <a href="<?= BASE_URL ?>/game-space.php" class="soft-btn px-3 py-2 fw-bold d-inline-flex align-items-center gap-1.5" style="font-size: 0.85rem; background: rgba(56, 189, 248, 0.15); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.3);">
            <i class="bi bi-rocket-takeoff-fill"></i> Space Defender
          </a>
          <a href="<?= BASE_URL ?>/game-racer.php" class="soft-btn px-3 py-2 fw-bold d-inline-flex align-items-center gap-1.5" style="font-size: 0.85rem; background: rgba(245, 158, 11, 0.15); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3);">
            <i class="bi bi-speedometer2"></i> Cyber Racer
          </a>
          <a href="<?= BASE_URL ?>/game-balloon.php" class="soft-btn px-3 py-2 fw-bold d-inline-flex align-items-center gap-1.5" style="font-size: 0.85rem; background: rgba(236, 72, 153, 0.15); color: #ec4899; border: 1px solid rgba(236, 72, 153, 0.3);">
            <i class="bi bi-balloon-fill"></i> Balloon Pop
          </a>
          <a href="<?= BASE_URL ?>/game-meteor.php" class="soft-btn px-3 py-2 fw-bold d-inline-flex align-items-center gap-1.5" style="font-size: 0.85rem; background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3);">
            <i class="bi bi-fire"></i> Meteor Defense
          </a>
        </div>
      </div>

      <!-- Quick Player Stats Overview -->
      <div class="col-lg-4">
        <div class="soft-card p-3">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="small text-secondary fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.06em;">Your Personal Records</span>
            <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.65rem;">Cloud Synced</span>
          </div>

          <div class="score-stat-box mb-1.5">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-slash-circle-fill text-danger fs-6"></i>
              <span class="small fw-bold" style="color: var(--text-primary);">Ninja</span>
            </div>
            <span class="fw-bold font-monospace text-success small"><?= number_format($highScores['ninja']['score']) ?> pts</span>
          </div>

          <div class="score-stat-box mb-1.5">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-rocket-takeoff-fill text-info fs-6"></i>
              <span class="small fw-bold" style="color: var(--text-primary);">Space Defender</span>
            </div>
            <span class="fw-bold font-monospace text-info small"><?= number_format($highScores['space']['score']) ?> pts</span>
          </div>

          <div class="score-stat-box mb-1.5">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-speedometer2 text-warning fs-6"></i>
              <span class="small fw-bold" style="color: var(--text-primary);">Grand Prix</span>
            </div>
            <span class="fw-bold font-monospace text-warning small"><?= number_format($highScores['racer']['score']) ?> pts</span>
          </div>

          <div class="score-stat-box mb-1.5">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-balloon-fill text-pink fs-6" style="color: #ec4899;"></i>
              <span class="small fw-bold" style="color: var(--text-primary);">Balloon Pop</span>
            </div>
            <span class="fw-bold font-monospace text-pink small" style="color: #ec4899;"><?= number_format($highScores['balloon']['score']) ?> pts</span>
          </div>

          <div class="score-stat-box">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-fire text-success fs-6"></i>
              <span class="small fw-bold" style="color: var(--text-primary);">Meteor Defense</span>
            </div>
            <span class="fw-bold font-monospace text-success small"><?= number_format($highScores['meteor']['score']) ?> pts</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- All 5 Games Showcase Grid -->
  <div class="row g-4 mb-5">
    
    <!-- Game 1: Keyboard Ninja -->
    <div class="col-lg-4 col-md-6">
      <div class="arcade-game-card">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div class="card-hero-icon" style="background: rgba(255, 77, 0, 0.12); color: #ff4d00;">
            <i class="bi bi-slash-circle-fill"></i>
          </div>
          <span class="card-tag-pill" style="background: rgba(255, 77, 0, 0.15); color: #ff4d00; border: 1px solid rgba(255, 77, 0, 0.3);">
            ★ Most Popular
          </span>
        </div>

        <h4 class="fw-bold mb-1" style="color: var(--text-primary);">Keyboard Ninja</h4>
        <p class="text-secondary small mb-3" style="line-height: 1.5; min-height: 54px;">
          Slash flying fruit with katana strikes. Isolate Home Row, Top Row, or Numbers while dodging explosive bombs!
        </p>

        <div class="d-flex flex-wrap gap-1.5 mb-3">
          <span class="feature-pill-badge"><i class="bi bi-check2 text-danger"></i> Fruit Slicing</span>
          <span class="feature-pill-badge"><i class="bi bi-check2 text-danger"></i> Row Isolation</span>
          <span class="feature-pill-badge"><i class="bi bi-check2 text-danger"></i> Katana SFX</span>
        </div>

        <div class="mt-auto d-flex align-items-center justify-content-between pt-2 border-top" style="border-color: var(--border-color) !important;">
          <div>
            <div class="text-muted" style="font-size: 0.68rem;">BEST SCORE</div>
            <div class="fw-bold font-monospace text-success small"><?= number_format($highScores['ninja']['score']) ?> pts</div>
          </div>
          <a href="<?= BASE_URL ?>/game-ninja.php" class="soft-btn soft-btn-primary px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1" style="font-size: 0.85rem;">
            <span>Play</span>
            <i class="bi bi-arrow-right"></i>
          </a>
        </div>
      </div>
    </div>

    <!-- Game 2: Space Word Defender -->
    <div class="col-lg-4 col-md-6">
      <div class="arcade-game-card">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div class="card-hero-icon" style="background: rgba(56, 189, 248, 0.12); color: #38bdf8;">
            <i class="bi bi-rocket-takeoff-fill"></i>
          </div>
          <span class="card-tag-pill" style="background: rgba(56, 189, 248, 0.15); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.3);">
            ★ Arcade Shooter
          </span>
        </div>

        <h4 class="fw-bold mb-1" style="color: var(--text-primary);">Space Word Defender</h4>
        <p class="text-secondary small mb-3" style="line-height: 1.5; min-height: 54px;">
          Defend orbital command from descending alien warships. Type complete words to fire laser cannons!
        </p>

        <div class="d-flex flex-wrap gap-1.5 mb-3">
          <span class="feature-pill-badge"><i class="bi bi-check2 text-info"></i> Laser Beams</span>
          <span class="feature-pill-badge"><i class="bi bi-check2 text-info"></i> Target Lock</span>
          <span class="feature-pill-badge"><i class="bi bi-check2 text-info"></i> Shield HP</span>
        </div>

        <div class="mt-auto d-flex align-items-center justify-content-between pt-2 border-top" style="border-color: var(--border-color) !important;">
          <div>
            <div class="text-muted" style="font-size: 0.68rem;">BEST SCORE</div>
            <div class="fw-bold font-monospace text-info small"><?= number_format($highScores['space']['score']) ?> pts</div>
          </div>
          <a href="<?= BASE_URL ?>/game-space.php" class="soft-btn px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1" style="background: #38bdf8; color: #000; font-size: 0.85rem;">
            <span>Play</span>
            <i class="bi bi-arrow-right"></i>
          </a>
        </div>
      </div>
    </div>

    <!-- Game 3: Cyber Grand Prix -->
    <div class="col-lg-4 col-md-6">
      <div class="arcade-game-card">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div class="card-hero-icon" style="background: rgba(245, 158, 11, 0.12); color: #f59e0b;">
            <i class="bi bi-speedometer2"></i>
          </div>
          <span class="card-tag-pill" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3);">
            ★ Speed Racer
          </span>
        </div>

        <h4 class="fw-bold mb-1" style="color: var(--text-primary);">Cyber Grand Prix</h4>
        <p class="text-secondary small mb-3" style="line-height: 1.5; min-height: 54px;">
          Accelerate your supercar past 150 MPH! Type words to ignite nitro exhaust flames and overtake rivals from P25 to P1.
        </p>

        <div class="d-flex flex-wrap gap-1.5 mb-3">
          <span class="feature-pill-badge"><i class="bi bi-check2 text-warning"></i> Nitro Boost</span>
          <span class="feature-pill-badge"><i class="bi bi-check2 text-warning"></i> Overtake Physics</span>
          <span class="feature-pill-badge"><i class="bi bi-check2 text-warning"></i> Engine Rev SFX</span>
        </div>

        <div class="mt-auto d-flex align-items-center justify-content-between pt-2 border-top" style="border-color: var(--border-color) !important;">
          <div>
            <div class="text-muted" style="font-size: 0.68rem;">BEST SCORE</div>
            <div class="fw-bold font-monospace text-warning small"><?= number_format($highScores['racer']['score']) ?> pts</div>
          </div>
          <a href="<?= BASE_URL ?>/game-racer.php" class="soft-btn px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1" style="background: #f59e0b; color: #000; font-size: 0.85rem;">
            <span>Play</span>
            <i class="bi bi-arrow-right"></i>
          </a>
        </div>
      </div>
    </div>

    <!-- Game 4: Type-a-Balloon -->
    <div class="col-lg-6 col-md-6">
      <div class="arcade-game-card">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div class="card-hero-icon" style="background: rgba(236, 72, 153, 0.12); color: #ec4899;">
            <i class="bi bi-balloon-fill"></i>
          </div>
          <span class="card-tag-pill" style="background: rgba(236, 72, 153, 0.15); color: #ec4899; border: 1px solid rgba(236, 72, 153, 0.3);">
            ★ Typing.com Classic
          </span>
        </div>

        <h4 class="fw-bold mb-1" style="color: var(--text-primary);">Type-a-Balloon</h4>
        <p class="text-secondary small mb-3" style="line-height: 1.5; min-height: 54px;">
          Float into the clouds! Pop colorful helium balloons with sharp darts before they drift into the stratosphere. Perfect for finger accuracy and letter recognition.
        </p>

        <div class="d-flex flex-wrap gap-1.5 mb-3">
          <span class="feature-pill-badge"><i class="bi bi-check2" style="color: #ec4899;"></i> Confetti Bursts</span>
          <span class="feature-pill-badge"><i class="bi bi-check2" style="color: #ec4899;"></i> Gold 3X Balloons</span>
          <span class="feature-pill-badge"><i class="bi bi-check2" style="color: #ec4899;"></i> Storm Hazards</span>
        </div>

        <div class="mt-auto d-flex align-items-center justify-content-between pt-2 border-top" style="border-color: var(--border-color) !important;">
          <div>
            <div class="text-muted" style="font-size: 0.68rem;">BEST SCORE</div>
            <div class="fw-bold font-monospace text-pink small" style="color: #ec4899;"><?= number_format($highScores['balloon']['score']) ?> pts</div>
          </div>
          <a href="<?= BASE_URL ?>/game-balloon.php" class="soft-btn px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1" style="background: #ec4899; color: #fff; font-size: 0.85rem;">
            <span>Play</span>
            <i class="bi bi-arrow-right"></i>
          </a>
        </div>
      </div>
    </div>

    <!-- Game 5: Meteor Matrix Defense -->
    <div class="col-lg-6 col-md-12">
      <div class="arcade-game-card">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div class="card-hero-icon" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">
            <i class="bi bi-fire"></i>
          </div>
          <span class="card-tag-pill" style="background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3);">
            ★ Planetary Railgun
          </span>
        </div>

        <h4 class="fw-bold mb-1" style="color: var(--text-primary);">Meteor Matrix Defense</h4>
        <p class="text-secondary small mb-3" style="line-height: 1.5; min-height: 54px;">
          Fiery meteors are hurtling toward the metropolitan city grid. Fire ground railgun plasma rounds and detonate blue EMP shockwaves to save the grid!
        </p>

        <div class="d-flex flex-wrap gap-1.5 mb-3">
          <span class="feature-pill-badge"><i class="bi bi-check2 text-success"></i> Railgun Rounds</span>
          <span class="feature-pill-badge"><i class="bi bi-check2 text-success"></i> EMP Shockwaves</span>
          <span class="feature-pill-badge"><i class="bi bi-check2 text-success"></i> Fire Trail FX</span>
        </div>

        <div class="mt-auto d-flex align-items-center justify-content-between pt-2 border-top" style="border-color: var(--border-color) !important;">
          <div>
            <div class="text-muted" style="font-size: 0.68rem;">BEST SCORE</div>
            <div class="fw-bold font-monospace text-success small"><?= number_format($highScores['meteor']['score']) ?> pts</div>
          </div>
          <a href="<?= BASE_URL ?>/game-meteor.php" class="soft-btn px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1" style="background: #10b981; color: #000; font-size: 0.85rem;">
            <span>Play</span>
            <i class="bi bi-arrow-right"></i>
          </a>
        </div>
      </div>
    </div>

  </div>

  <!-- Arcade Hall of Fame Leaderboards (5 Tabs) -->
  <div class="soft-card p-4 mb-5">
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
      <div>
        <h4 class="fw-bold mb-1" style="color: var(--text-primary);"><i class="bi bi-trophy-fill text-warning me-2"></i> Arcade Hall of Fame</h4>
        <p class="text-secondary small mb-0">High scores recorded across all 5 arcade challenges.</p>
      </div>

      <ul class="nav nav-pills" id="arcadeTabs" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active py-1.5 px-3 small fw-bold" id="tab-ninja-btn" data-bs-toggle="pill" data-bs-target="#tab-ninja" type="button" role="tab">
            <i class="bi bi-slash-circle-fill text-danger me-1"></i> Ninja
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link py-1.5 px-3 small fw-bold" id="tab-space-btn" data-bs-toggle="pill" data-bs-target="#tab-space" type="button" role="tab">
            <i class="bi bi-rocket-takeoff-fill text-info me-1"></i> Space
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link py-1.5 px-3 small fw-bold" id="tab-racer-btn" data-bs-toggle="pill" data-bs-target="#tab-racer" type="button" role="tab">
            <i class="bi bi-speedometer2 text-warning me-1"></i> Racer
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link py-1.5 px-3 small fw-bold" id="tab-balloon-btn" data-bs-toggle="pill" data-bs-target="#tab-balloon" type="button" role="tab">
            <i class="bi bi-balloon-fill text-pink me-1" style="color: #ec4899;"></i> Balloon
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link py-1.5 px-3 small fw-bold" id="tab-meteor-btn" data-bs-toggle="pill" data-bs-target="#tab-meteor" type="button" role="tab">
            <i class="bi bi-fire text-success me-1"></i> Meteor
          </button>
        </li>
      </ul>
    </div>

    <div class="tab-content" id="arcadeTabsContent">
      
      <?php foreach (['ninja', 'space', 'racer', 'balloon', 'meteor'] as $slug): 
        $entries = $leaderboards[$slug] ?? [];
        $activeClass = ($slug === 'ninja') ? 'show active' : '';
      ?>
        <div class="tab-pane fade <?= $activeClass ?>" id="tab-<?= $slug ?>" role="tabpanel">
          <?php if (empty($entries)): ?>
            <div class="text-center py-4 text-muted">
              <i class="bi bi-controller fs-3 mb-2 d-block"></i>
              <p class="mb-2">No scores recorded yet for this arcade challenge.</p>
              <a href="<?= BASE_URL ?>/game-<?= $slug ?>.php" class="btn btn-sm soft-btn-primary">Be the First Champion</a>
            </div>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table leaderboard-table align-middle mb-0">
                <thead>
                  <tr>
                    <th style="width: 60px;">Rank</th>
                    <th>Typist</th>
                    <th class="text-end">Score</th>
                    <th class="text-end">Best Streak</th>
                    <th class="text-end">Accuracy</th>
                    <th class="text-end">Date</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($entries as $idx => $entry): ?>
                    <tr>
                      <td>
                        <?php if ($idx === 0): ?>
                          <span class="badge bg-warning text-dark px-2 py-1"><i class="bi bi-trophy-fill"></i> 1st</span>
                        <?php elseif ($idx === 1): ?>
                          <span class="badge bg-secondary text-light px-2 py-1">2nd</span>
                        <?php elseif ($idx === 2): ?>
                          <span class="badge bg-dark border text-light px-2 py-1">3rd</span>
                        <?php else: ?>
                          <span class="text-muted fw-bold ps-2">#<?= $idx + 1 ?></span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <div class="d-flex align-items-center gap-2">
                          <div class="user-avatar shadow-sm" style="width: 30px; height: 30px; font-size: 0.75rem;">
                            <?= strtoupper(substr($entry['username'], 0, 1)) ?>
                          </div>
                          <div>
                            <div class="fw-bold" style="color: var(--text-primary);"><?= e($entry['full_name'] ?: $entry['username']) ?></div>
                            <div class="text-muted" style="font-size: 0.7rem;">@<?= e($entry['username']) ?></div>
                          </div>
                        </div>
                      </td>
                      <td class="text-end font-monospace fw-bold text-success fs-6">
                        <?= number_format($entry['score']) ?>
                      </td>
                      <td class="text-end font-monospace text-warning">
                        <?= $entry['highest_streak'] ?> hits
                      </td>
                      <td class="text-end font-monospace">
                        <?= number_format($entry['accuracy'], 1) ?>%
                      </td>
                      <td class="text-end text-muted small">
                        <?= date('M j, Y', strtotime($entry['played_at'])) ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>

    </div>
  </div>

</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
