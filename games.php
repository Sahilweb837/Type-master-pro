<?php
/**
 * Custom Type Master Pro
 * Arcade Games Hub
 * Central portal for Keyboard Ninja, Space Defender, and typing challenges
 */

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/games_data.php';

$currentUser = current_user();
$userId = $currentUser ? (int)$currentUser['id'] : 0;

$ninjaHighScore = get_user_game_high_score($userId, 'ninja');
$spaceHighScore = get_user_game_high_score($userId, 'space');

$ninjaLeaderboard = get_game_leaderboard('ninja', 6);
$spaceLeaderboard = get_game_leaderboard('space', 6);

$allGames = get_all_games();

$pageTitle = 'Arcade Games Hub — Typing.com Inspired';
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
  padding: 28px;
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
  width: 58px;
  height: 58px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.8rem;
  margin-bottom: 20px;
}

.card-tag-pill {
  font-size: 0.72rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  padding: 4px 10px;
  border-radius: 20px;
  display: inline-block;
  margin-bottom: 12px;
}

.feature-pill-badge {
  background: var(--bg-primary);
  border: 1px solid var(--border-color);
  color: var(--text-secondary);
  font-size: 0.76rem;
  font-weight: 600;
  padding: 4px 10px;
  border-radius: 8px;
  display: inline-flex;
  align-items: center;
  gap: 5px;
}

.score-stat-box {
  background: var(--bg-primary);
  border: 1px solid var(--border-color);
  border-radius: 10px;
  padding: 10px 14px;
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
          <i class="bi bi-controller"></i> ARCADE TYPING ZONE
        </div>
        <h1 class="fw-bold mb-2" style="font-size: 2.2rem; letter-spacing: -0.03em; color: var(--text-primary);">
          Typing Arcade Arena
        </h1>
        <p class="text-secondary mb-3" style="max-width: 680px; font-size: 0.98rem; line-height: 1.6;">
          Transform repetitive finger drills into thrilling arcade reflex tests. Inspired by games like <em>Keyboard Ninja</em> and space defense typing, train your subconscious muscle memory while having fun!
        </p>

        <div class="d-flex align-items-center gap-2 flex-wrap">
          <a href="<?= BASE_URL ?>/game-ninja.php" class="soft-btn soft-btn-primary px-4 py-2 fw-bold d-inline-flex align-items-center gap-2">
            <i class="bi bi-slash-circle-fill"></i> Play Keyboard Ninja
          </a>
          <a href="<?= BASE_URL ?>/game-space.php" class="soft-btn px-4 py-2 fw-bold d-inline-flex align-items-center gap-2">
            <i class="bi bi-rocket-takeoff-fill" style="color: #38bdf8;"></i> Space Word Defender
          </a>
          <a href="<?= BASE_URL ?>/lessons.php" class="btn btn-sm btn-control px-3 py-2">
            <i class="bi bi-mortarboard me-1"></i> Structured Lessons
          </a>
        </div>
      </div>

      <!-- Quick Player Stats -->
      <div class="col-lg-4">
        <div class="soft-card p-3 p-md-4">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <span class="small text-secondary fw-bold text-uppercase" style="letter-spacing: 0.06em;">Your Personal Bests</span>
            <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.7rem;">Synced</span>
          </div>

          <div class="score-stat-box mb-2">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-slash-circle-fill text-danger fs-5"></i>
              <div>
                <div class="fw-bold small" style="color: var(--text-primary);">Keyboard Ninja</div>
                <div class="text-muted" style="font-size: 0.7rem;">Streak: <?= $ninjaHighScore['highest_streak'] ?> hits</div>
              </div>
            </div>
            <div class="text-end">
              <span class="fw-bold font-monospace text-success fs-5"><?= number_format($ninjaHighScore['score']) ?></span>
              <div class="text-muted" style="font-size: 0.65rem;">POINTS</div>
            </div>
          </div>

          <div class="score-stat-box">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-rocket-takeoff-fill text-info fs-5"></i>
              <div>
                <div class="fw-bold small" style="color: var(--text-primary);">Space Defender</div>
                <div class="text-muted" style="font-size: 0.7rem;">Streak: <?= $spaceHighScore['highest_streak'] ?> hits</div>
              </div>
            </div>
            <div class="text-end">
              <span class="fw-bold font-monospace text-info fs-5"><?= number_format($spaceHighScore['score']) ?></span>
              <div class="text-muted" style="font-size: 0.65rem;">POINTS</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Game Cards Section -->
  <div class="row g-4 mb-5">
    
    <!-- Game 1: Keyboard Ninja -->
    <div class="col-lg-6">
      <div class="arcade-game-card">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div class="card-hero-icon" style="background: rgba(255, 77, 0, 0.12); color: #ff4d00;">
            <i class="bi bi-slash-circle-fill"></i>
          </div>
          <span class="card-tag-pill" style="background: rgba(255, 77, 0, 0.15); color: #ff4d00; border: 1px solid rgba(255, 77, 0, 0.3);">
            ★ Most Popular
          </span>
        </div>

        <h3 class="fw-bold mb-2" style="color: var(--text-primary); font-size: 1.45rem;">Keyboard Ninja</h3>
        <p class="text-secondary small mb-3" style="line-height: 1.6;">
          Slash flying fruit targets mid-air with sharp katana strikes. Isolate specific keyboard rows (Home Row, Top Row, Bottom Row, Numbers) or test full alphabet reflexes while dodging explosive ninja bombs!
        </p>

        <!-- Feature Pills -->
        <div class="d-flex flex-wrap gap-2 mb-4">
          <span class="feature-pill-badge"><i class="bi bi-check2 text-danger"></i> Fruit Slicing Physics</span>
          <span class="feature-pill-badge"><i class="bi bi-check2 text-danger"></i> Sword Sound FX</span>
          <span class="feature-pill-badge"><i class="bi bi-check2 text-danger"></i> Combo Multipliers</span>
          <span class="feature-pill-badge"><i class="bi bi-check2 text-danger"></i> Row Isolation Modes</span>
        </div>

        <!-- Mode Select Preview -->
        <div class="p-3 rounded-3 mb-4" style="background: var(--bg-primary); border: 1px solid var(--border-color);">
          <div class="small fw-bold text-muted mb-2 text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.05em;">Keyboard Practice Rows:</div>
          <div class="d-flex flex-wrap gap-1.5">
            <span class="badge bg-secondary-subtle text-secondary">Home Row (ASDF JKL;)</span>
            <span class="badge bg-secondary-subtle text-secondary">Top Row (QWERTY)</span>
            <span class="badge bg-secondary-subtle text-secondary">Bottom Row (ZXCVBNM)</span>
            <span class="badge bg-secondary-subtle text-secondary">Numbers (1234567890)</span>
            <span class="badge bg-secondary-subtle text-secondary">All Letters</span>
          </div>
        </div>

        <div class="mt-auto d-flex align-items-center justify-content-between pt-2">
          <div>
            <div class="text-muted" style="font-size: 0.7rem;">PERSONAL BEST</div>
            <div class="fw-bold font-monospace text-success fs-5"><?= number_format($ninjaHighScore['score']) ?> pts</div>
          </div>
          <a href="<?= BASE_URL ?>/game-ninja.php" class="soft-btn soft-btn-primary px-4 py-2.5 fw-bold d-inline-flex align-items-center gap-2">
            <span>Play Ninja</span>
            <i class="bi bi-arrow-right"></i>
          </a>
        </div>
      </div>
    </div>

    <!-- Game 2: Space Word Defender -->
    <div class="col-lg-6">
      <div class="arcade-game-card">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div class="card-hero-icon" style="background: rgba(56, 189, 248, 0.12); color: #38bdf8;">
            <i class="bi bi-rocket-takeoff-fill"></i>
          </div>
          <span class="card-tag-pill" style="background: rgba(56, 189, 248, 0.15); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.3);">
            ★ Arcade Shooter
          </span>
        </div>

        <h3 class="fw-bold mb-2" style="color: var(--text-primary); font-size: 1.45rem;">Space Word Defender</h3>
        <p class="text-secondary small mb-3" style="line-height: 1.6;">
          Defend your orbital planetary base from invading alien warships. Type complete vocabulary words to swivel high-frequency laser cannons and obliterate targets before they breach your energy shields!
        </p>

        <!-- Feature Pills -->
        <div class="d-flex flex-wrap gap-2 mb-4">
          <span class="feature-pill-badge"><i class="bi bi-check2 text-info"></i> Laser Particle Beams</span>
          <span class="feature-pill-badge"><i class="bi bi-check2 text-info"></i> Planetary Shield HP</span>
          <span class="feature-pill-badge"><i class="bi bi-check2 text-info"></i> Progressive Alien Waves</span>
          <span class="feature-pill-badge"><i class="bi bi-check2 text-info"></i> Code &amp; Vocab Banks</span>
        </div>

        <!-- Mode Select Preview -->
        <div class="p-3 rounded-3 mb-4" style="background: var(--bg-primary); border: 1px solid var(--border-color);">
          <div class="small fw-bold text-muted mb-2 text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.05em;">Word Banks &amp; Difficulties:</div>
          <div class="d-flex flex-wrap gap-1.5">
            <span class="badge bg-secondary-subtle text-secondary">Core Vocabulary</span>
            <span class="badge bg-secondary-subtle text-secondary">Speed Short Words</span>
            <span class="badge bg-secondary-subtle text-secondary">Tech &amp; Code Syntax</span>
            <span class="badge bg-secondary-subtle text-secondary">Cadet</span>
            <span class="badge bg-secondary-subtle text-secondary">Commander</span>
            <span class="badge bg-secondary-subtle text-secondary">Elite</span>
          </div>
        </div>

        <div class="mt-auto d-flex align-items-center justify-content-between pt-2">
          <div>
            <div class="text-muted" style="font-size: 0.7rem;">PERSONAL BEST</div>
            <div class="fw-bold font-monospace text-info fs-5"><?= number_format($spaceHighScore['score']) ?> pts</div>
          </div>
          <a href="<?= BASE_URL ?>/game-space.php" class="soft-btn px-4 py-2.5 fw-bold d-inline-flex align-items-center gap-2" style="background: #38bdf8; color: #000;">
            <span>Deploy Defender</span>
            <i class="bi bi-arrow-right"></i>
          </a>
        </div>
      </div>
    </div>

  </div>

  <!-- Arcade Leaderboards Section -->
  <div class="soft-card p-4 mb-5">
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
      <div>
        <h4 class="fw-bold mb-1" style="color: var(--text-primary);"><i class="bi bi-trophy-fill text-warning me-2"></i> Arcade Hall of Fame</h4>
        <p class="text-secondary small mb-0">Top high scores achieved across all registered typists.</p>
      </div>

      <ul class="nav nav-pills" id="arcadeTabs" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active py-1.5 px-3 small fw-bold" id="tab-ninja-btn" data-bs-toggle="pill" data-bs-target="#tab-ninja" type="button" role="tab">
            <i class="bi bi-slash-circle-fill text-danger me-1"></i> Keyboard Ninja
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link py-1.5 px-3 small fw-bold" id="tab-space-btn" data-bs-toggle="pill" data-bs-target="#tab-space" type="button" role="tab">
            <i class="bi bi-rocket-takeoff-fill text-info me-1"></i> Space Defender
          </button>
        </li>
      </ul>
    </div>

    <div class="tab-content" id="arcadeTabsContent">
      
      <!-- Tab 1: Ninja Leaderboard -->
      <div class="tab-pane fade show active" id="tab-ninja" role="tabpanel">
        <?php if (empty($ninjaLeaderboard)): ?>
          <div class="text-center py-4 text-muted">
            <i class="bi bi-slash-circle fs-3 mb-2 d-block"></i>
            <p class="mb-2">No scores recorded yet for Keyboard Ninja.</p>
            <a href="<?= BASE_URL ?>/game-ninja.php" class="btn btn-sm soft-btn-primary">Be the First Champion</a>
          </div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table leaderboard-table align-middle mb-0">
              <thead>
                <tr>
                  <th style="width: 60px;">Rank</th>
                  <th>Typist</th>
                  <th class="text-end">High Score</th>
                  <th class="text-end">Best Streak</th>
                  <th class="text-end">Accuracy</th>
                  <th class="text-end">Date</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($ninjaLeaderboard as $idx => $entry): ?>
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

      <!-- Tab 2: Space Defender Leaderboard -->
      <div class="tab-pane fade" id="tab-space" role="tabpanel">
        <?php if (empty($spaceLeaderboard)): ?>
          <div class="text-center py-4 text-muted">
            <i class="bi bi-rocket-takeoff fs-3 mb-2 d-block"></i>
            <p class="mb-2">No scores recorded yet for Space Word Defender.</p>
            <a href="<?= BASE_URL ?>/game-space.php" class="btn btn-sm btn-info text-dark fw-bold">Defend the Base First</a>
          </div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table leaderboard-table align-middle mb-0">
              <thead>
                <tr>
                  <th style="width: 60px;">Rank</th>
                  <th>Typist</th>
                  <th class="text-end">High Score</th>
                  <th class="text-end">Best Streak</th>
                  <th class="text-end">Accuracy</th>
                  <th class="text-end">Date</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($spaceLeaderboard as $idx => $entry): ?>
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
                    <td class="text-end font-monospace fw-bold text-info fs-6">
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

    </div>
  </div>

  <!-- Educational & Muscle Memory Intel -->
  <div class="row g-4">
    <div class="col-md-4">
      <div class="soft-card p-4 h-100">
        <div class="d-inline-flex p-2.5 rounded-3 mb-3" style="background: rgba(255, 77, 0, 0.12); color: #ff4d00; font-size: 1.3rem;">
          <i class="bi bi-lightning-charge-fill"></i>
        </div>
        <h5 class="fw-bold mb-2" style="color: var(--text-primary);">Subconscious Reflexes</h5>
        <p class="text-secondary small mb-0" style="line-height: 1.6;">
          Games force rapid involuntary reactions under time pressure. Your brain bypasses conscious visual verification and triggers muscle memory in under 120 milliseconds.
        </p>
      </div>
    </div>

    <div class="col-md-4">
      <div class="soft-card p-4 h-100">
        <div class="d-inline-flex p-2.5 rounded-3 mb-3" style="background: rgba(56, 189, 248, 0.12); color: #38bdf8; font-size: 1.3rem;">
          <i class="bi bi-eye-fill"></i>
        </div>
        <h5 class="fw-bold mb-2" style="color: var(--text-primary);">Peripheral Vision</h5>
        <p class="text-secondary small mb-0" style="line-height: 1.6;">
          Tracking moving targets across canvas screens widens your visual fixation field, allowing you to read 2-3 words ahead when typing standard text documents.
        </p>
      </div>
    </div>

    <div class="col-md-4">
      <div class="soft-card p-4 h-100">
        <div class="d-inline-flex p-2.5 rounded-3 mb-3" style="background: rgba(16, 185, 129, 0.12); color: #10b981; font-size: 1.3rem;">
          <i class="bi bi-bullseye"></i>
        </div>
        <h5 class="fw-bold mb-2" style="color: var(--text-primary);">Stress Inoculation</h5>
        <p class="text-secondary small mb-0" style="line-height: 1.6;">
          Maintaining high accuracy during bomb hazard countdowns or planetary shield alerts builds confidence and prevents accuracy collapse during high-speed typing runs.
        </p>
      </div>
    </div>
  </div>

</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
