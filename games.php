<?php
/**
 * Custom Type Master Pro
 * Arcade Games Hub — Pixel Theme / Sans-Serif Readable
 */

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/games_data.php';

$currentUser = current_user();
$userId = $currentUser ? (int)$currentUser['id'] : 0;

$highScores = [
    'ninja'   => get_user_game_high_score($userId, 'ninja'),
    'space'   => get_user_game_high_score($userId, 'space'),
    'racer'   => get_user_game_high_score($userId, 'racer'),
    'balloon' => get_user_game_high_score($userId, 'balloon'),
    'meteor'  => get_user_game_high_score($userId, 'meteor')
];

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
.arcade-hub {
  --px-ink:    #000000;
  --px-paper:  #ffffff;
  --px-paper-2:#f0f0f0;
  --px-paper-3:#e0e0e0;
  --px-shadow: 3px;
  --px-sans:   "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
  --px-pixel:  "Press Start 2P", "Courier New", monospace;
  --px-mono:   "JetBrains Mono", ui-monospace, monospace;
  font-family: var(--px-sans);
}
html[data-theme="dark"] .arcade-hub {
  --px-ink:    #ffffff;
  --px-paper:  #000000;
  --px-paper-2:#1a1a1a;
  --px-paper-3:#222222;
}

/* Buttons — sans-serif readable */
.arcade-hub .px-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 0.85rem;
  font-family: var(--px-sans);
  font-size: 0.82rem;
  font-weight: 600;
  letter-spacing: 0.03em;
  text-transform: uppercase;
  text-decoration: none !important;
  border: 3px solid var(--px-ink);
  background: var(--px-paper);
  color: var(--px-ink);
  box-shadow: var(--px-shadow) var(--px-shadow) 0 var(--px-ink);
  cursor: pointer;
  line-height: 1.2;
  transition: transform 0.05s steps(1), box-shadow 0.05s steps(1);
  -webkit-appearance: none;
  appearance: none;
}
.arcade-hub .px-btn:hover { background: var(--px-paper-2); color: var(--px-ink); }
.arcade-hub .px-btn:active {
  transform: translate(var(--px-shadow), var(--px-shadow));
  box-shadow: 0 0 0 var(--px-ink);
}
.arcade-hub .px-btn i { font-size: 0.9rem; }
.arcade-hub .px-btn-primary { background: var(--px-ink); color: var(--px-paper); }
.arcade-hub .px-btn-primary:hover { background: var(--px-ink); color: var(--px-paper); }
.arcade-hub .px-btn-primary i { color: var(--px-paper); }
.arcade-hub .px-btn-sm {
  padding: 0.4rem 0.65rem;
  font-size: 0.74rem;
  box-shadow: 2px 2px 0 var(--px-ink);
}
.arcade-hub .px-btn-sm:active {
  transform: translate(2px, 2px);
  box-shadow: 0 0 0 var(--px-ink);
}

/* Header banner */
.arcade-hub-header {
  background: var(--px-paper);
  border: 3px solid var(--px-ink);
  box-shadow: 5px 5px 0 var(--px-ink);
  padding: 2rem 1.75rem;
  margin-bottom: 1.75rem;
  position: relative;
  overflow: hidden;
}
.arcade-hub-header::before {
  content: "";
  position: absolute;
  inset: 0;
  background-image:
    linear-gradient(45deg, rgba(0,0,0,0.045) 25%, transparent 25%),
    linear-gradient(-45deg, rgba(0,0,0,0.045) 25%, transparent 25%),
    linear-gradient(45deg, transparent 75%, rgba(0,0,0,0.045) 75%),
    linear-gradient(-45deg, transparent 75%, rgba(0,0,0,0.045) 75%);
  background-size: 8px 8px;
  background-position: 0 0, 0 4px, 4px -4px, -4px 0;
  pointer-events: none;
}
html[data-theme="dark"] .arcade-hub-header::before {
  background-image:
    linear-gradient(45deg, rgba(255,255,255,0.05) 25%, transparent 25%),
    linear-gradient(-45deg, rgba(255,255,255,0.05) 25%, transparent 25%),
    linear-gradient(45deg, transparent 75%, rgba(255,255,255,0.05) 75%),
    linear-gradient(-45deg, transparent 75%, rgba(255,255,255,0.05) 75%);
}
.arcade-hub-header > * { position: relative; z-index: 1; }

/* Eyebrow — sans-serif */
.arcade-hub .arcade-eyebrow {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.35rem 0.7rem;
  margin-bottom: 1.1rem;
  background: var(--px-paper);
  border: 2px solid var(--px-ink);
  box-shadow: 2px 2px 0 var(--px-ink);
  font-family: var(--px-sans);
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--px-ink);
}
.arcade-hub .arcade-eyebrow i { font-size: 0.85rem; }
.arcade-hub .arcade-eyebrow .blink {
  width: 6px; height: 6px;
  background: var(--px-ink);
  animation: pxBlink 1.2s steps(1) infinite;
  margin-left: 0.15rem;
}
@keyframes pxBlink {
  0%, 49% { opacity: 1; }
  50%, 100% { opacity: 0; }
}

/* Main title — sans-serif, big & bold */
.arcade-hub .arcade-title {
  font-family: var(--px-sans);
  font-size: clamp(1.6rem, 3.6vw, 2.4rem);
  font-weight: 800;
  line-height: 1.15;
  letter-spacing: -0.02em;
  margin: 0 0 0.85rem;
  color: var(--px-ink);
}
.arcade-hub .arcade-title .underscore {
  display: inline-block;
  border-bottom: 5px solid var(--px-ink);
  padding-bottom: 1px;
}

/* Lead — sans-serif */
.arcade-hub .arcade-lead {
  font-family: var(--px-sans);
  font-size: 0.98rem;
  line-height: 1.65;
  color: var(--px-ink);
  max-width: 700px;
  margin: 0 0 1.25rem;
}

.arcade-hub .arcade-quicknav {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

/* Records panel */
.arcade-hub .records-panel {
  background: var(--px-paper);
  border: 3px solid var(--px-ink);
  box-shadow: var(--px-shadow) var(--px-shadow) 0 var(--px-ink);
  padding: 1rem;
}
.arcade-hub .records-panel-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 0.85rem;
  padding-bottom: 0.65rem;
  border-bottom: 2px dashed var(--px-ink);
}
.arcade-hub .records-panel-title {
  font-family: var(--px-sans);
  font-size: 0.78rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--px-ink);
}
.arcade-hub .records-badge {
  font-family: var(--px-sans);
  font-size: 0.66rem;
  font-weight: 700;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  background: var(--px-ink);
  color: var(--px-paper);
  padding: 0.15rem 0.45rem;
  border: 2px solid var(--px-ink);
}

/* Score rows */
.arcade-hub .score-stat-box {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.55rem 0.7rem;
  background: var(--px-paper);
  border: 2px solid var(--px-ink);
  margin-bottom: 0.45rem;
}
.arcade-hub .score-stat-box:last-child { margin-bottom: 0; }
.arcade-hub .score-stat-box .left {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.arcade-hub .score-stat-box .left i {
  color: var(--px-ink);
  font-size: 0.95rem;
}
.arcade-hub .score-stat-box .left span {
  font-family: var(--px-sans);
  font-size: 0.82rem;
  font-weight: 600;
  color: var(--px-ink);
}
.arcade-hub .score-stat-box .pts {
  font-family: var(--px-mono);
  font-size: 0.82rem;
  font-weight: 700;
  color: var(--px-ink);
}

/* Game cards */
.arcade-hub .arcade-game-card {
  background: var(--px-paper);
  border: 3px solid var(--px-ink);
  box-shadow: var(--px-shadow) var(--px-shadow) 0 var(--px-ink);
  padding: 1.4rem 1.35rem;
  height: 100%;
  display: flex;
  flex-direction: column;
  position: relative;
  transition: transform 0.05s steps(1), box-shadow 0.05s steps(1);
}
.arcade-hub .arcade-game-card:hover {
  transform: translate(-2px, -2px);
  box-shadow: 5px 5px 0 var(--px-ink);
}
.arcade-hub .arcade-game-card:active {
  transform: translate(var(--px-shadow), var(--px-shadow));
  box-shadow: 0 0 0 var(--px-ink);
}

.arcade-hub .card-top-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 1rem;
}

/* Hero icon — pixel block, no text */
.arcade-hub .card-hero-icon {
  width: 48px;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.35rem;
  background: var(--px-ink);
  color: var(--px-paper);
  border: 0;
  position: relative;
}
.arcade-hub .card-hero-icon::after {
  content: "";
  position: absolute;
  inset: 0;
  background-image:
    linear-gradient(45deg, var(--px-paper) 25%, transparent 25%),
    linear-gradient(-45deg, var(--px-paper) 25%, transparent 25%);
  background-size: 4px 4px;
  opacity: 0.14;
  pointer-events: none;
}
.arcade-hub .card-hero-icon i { position: relative; z-index: 1; }

/* Tag pill — sans-serif */
.arcade-hub .card-tag-pill {
  font-family: var(--px-sans);
  font-size: 0.68rem;
  font-weight: 700;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  padding: 0.2rem 0.5rem;
  border: 2px solid var(--px-ink);
  background: var(--px-paper);
  color: var(--px-ink);
  white-space: nowrap;
}

/* Card title — sans-serif, readable */
.arcade-hub .arcade-game-card h4 {
  font-family: var(--px-sans);
  font-size: 1.05rem;
  font-weight: 800;
  line-height: 1.25;
  letter-spacing: -0.01em;
  color: var(--px-ink);
  margin: 0 0 0.7rem;
}

/* Card description — sans-serif */
.arcade-hub .arcade-game-card p {
  font-family: var(--px-sans);
  font-size: 0.86rem;
  line-height: 1.55;
  color: var(--px-ink);
  margin: 0 0 1rem;
  min-height: 60px;
}

/* Feature pills — sans-serif */
.arcade-hub .feature-pill-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  font-family: var(--px-sans);
  font-size: 0.7rem;
  font-weight: 600;
  letter-spacing: 0.02em;
  text-transform: uppercase;
  background: var(--px-paper);
  border: 2px solid var(--px-ink);
  padding: 0.15rem 0.5rem;
  color: var(--px-ink);
}
.arcade-hub .feature-pill-badge i {
  font-size: 0.78rem;
  color: var(--px-ink);
}

/* Card footer */
.arcade-hub .arcade-card-foot {
  margin-top: auto;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.6rem;
  padding-top: 0.9rem;
  border-top: 2px dashed var(--px-ink);
}
.arcade-hub .arcade-card-foot .best-label {
  font-family: var(--px-sans);
  font-size: 0.66rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--px-ink);
  opacity: 0.7;
}
.arcade-hub .arcade-card-foot .best-score {
  font-family: var(--px-mono);
  font-size: 0.9rem;
  font-weight: 700;
  color: var(--px-ink);
}

/* Hall of fame */
.arcade-hub .hall-of-fame {
  background: var(--px-paper);
  border: 3px solid var(--px-ink);
  box-shadow: 5px 5px 0 var(--px-ink);
  padding: 1.75rem;
  margin-bottom: 2rem;
}
.arcade-hub .hall-title {
  font-family: var(--px-sans);
  font-size: 1.1rem;
  font-weight: 800;
  color: var(--px-ink);
  margin: 0 0 0.5rem;
  letter-spacing: -0.01em;
}
.arcade-hub .hall-sub {
  font-family: var(--px-sans);
  font-size: 0.85rem;
  color: var(--px-ink);
  opacity: 0.75;
  margin: 0;
}

/* Tabs — sans-serif */
.arcade-hub .nav-pills {
  display: flex;
  flex-wrap: wrap;
  gap: 0.35rem;
  border: 0;
}
.arcade-hub .nav-pills .nav-item { margin: 0; }
.arcade-hub .nav-pills .nav-link {
  font-family: var(--px-sans);
  font-size: 0.74rem;
  font-weight: 600;
  letter-spacing: 0.03em;
  text-transform: uppercase;
  color: var(--px-ink);
  background: var(--px-paper);
  border: 2px solid var(--px-ink);
  border-radius: 0;
  padding: 0.4rem 0.7rem;
  margin: 0;
  line-height: 1.2;
  transition: transform 0.05s steps(1), box-shadow 0.05s steps(1), background 0.05s steps(1);
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  box-shadow: 2px 2px 0 var(--px-ink);
}
.arcade-hub .nav-pills .nav-link i { color: var(--px-ink); font-size: 0.82rem; }
.arcade-hub .nav-pills .nav-link:hover { background: var(--px-paper-2); color: var(--px-ink); }
.arcade-hub .nav-pills .nav-link.active {
  background: var(--px-ink);
  color: var(--px-paper);
  box-shadow: 0 0 0 var(--px-ink);
  transform: translate(2px, 2px);
}
.arcade-hub .nav-pills .nav-link.active i { color: var(--px-paper); }

/* Leaderboard table */
.arcade-hub .leaderboard-table {
  border-collapse: separate;
  border-spacing: 0;
  width: 100%;
  margin: 0;
  font-family: var(--px-sans);
}
.arcade-hub .leaderboard-table thead th {
  font-family: var(--px-sans);
  font-size: 0.7rem;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--px-ink);
  font-weight: 700;
  border-top: 3px solid var(--px-ink);
  border-bottom: 3px solid var(--px-ink);
  padding: 0.65rem 0.9rem;
  background: var(--px-paper-2);
  white-space: nowrap;
}
.arcade-hub .leaderboard-table tbody td {
  padding: 0.7rem 0.9rem;
  vertical-align: middle;
  border-bottom: 2px solid var(--px-ink);
  font-size: 0.86rem;
  color: var(--px-ink);
  background: var(--px-paper);
}
.arcade-hub .leaderboard-table tbody tr:last-child td {
  border-bottom: 3px solid var(--px-ink);
}
.arcade-hub .leaderboard-table tbody tr:hover td {
  background: var(--px-paper-2);
}

/* Rank badges */
.arcade-hub .rank-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
  font-family: var(--px-sans);
  font-size: 0.66rem;
  font-weight: 700;
  letter-spacing: 0.04em;
  text-transform: uppercase;
  padding: 0.15rem 0.5rem;
  border: 2px solid var(--px-ink);
  white-space: nowrap;
}
.arcade-hub .rank-badge-1 { background: var(--px-ink); color: var(--px-paper); }
.arcade-hub .rank-badge-1 i { color: var(--px-paper); }
.arcade-hub .rank-badge-2 { background: var(--px-paper); color: var(--px-ink); }
.arcade-hub .rank-badge-3 { background: var(--px-paper-2); color: var(--px-ink); }
.arcade-hub .rank-plain {
  font-family: var(--px-mono);
  font-size: 0.78rem;
  font-weight: 700;
  color: var(--px-ink);
  padding-left: 0.35rem;
}

/* Pixel avatar — decorative initial */
.arcade-hub .user-avatar-pixel {
  width: 30px;
  height: 30px;
  background: var(--px-ink);
  color: var(--px-paper);
  font-family: var(--px-pixel);
  font-size: 0.55rem;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  text-transform: uppercase;
  flex-shrink: 0;
  position: relative;
}
.arcade-hub .user-avatar-pixel::after {
  content: "";
  position: absolute;
  inset: 0;
  background-image:
    linear-gradient(45deg, var(--px-paper) 25%, transparent 25%),
    linear-gradient(-45deg, var(--px-paper) 25%, transparent 25%);
  background-size: 4px 4px;
  opacity: 0.15;
  pointer-events: none;
}
.arcade-hub .user-avatar-pixel span { position: relative; z-index: 1; }

/* Player name */
.arcade-hub .player-name {
  font-family: var(--px-sans);
  font-size: 0.88rem;
  font-weight: 700;
  color: var(--px-ink);
  line-height: 1.25;
}
.arcade-hub .player-handle {
  font-family: var(--px-mono);
  font-size: 0.7rem;
  color: var(--px-ink);
  opacity: 0.6;
}

/* Empty state */
.arcade-hub .empty-state {
  text-align: center;
  padding: 2.5rem 1rem;
  border: 2px dashed var(--px-ink);
  font-family: var(--px-sans);
  color: var(--px-ink);
  background: var(--px-paper);
}
.arcade-hub .empty-state i {
  font-size: 2rem;
  display: block;
  margin-bottom: 0.85rem;
  color: var(--px-ink);
}
.arcade-hub .empty-state p {
  font-size: 0.9rem;
  margin: 0 0 1rem;
}

/* Neutralize colored Bootstrap utilities */
.arcade-hub .text-danger,
.arcade-hub .text-info,
.arcade-hub .text-warning,
.arcade-hub .text-success,
.arcade-hub .text-pink,
.arcade-hub .text-muted {
  color: var(--px-ink) !important;
}

@media (max-width: 640px) {
  .arcade-hub-header { padding: 1.35rem 1.1rem; }
  .arcade-hub .arcade-game-card { padding: 1.15rem 1rem; }
  .arcade-hub .hall-of-fame { padding: 1.15rem; }
  .arcade-hub .leaderboard-table thead th,
  .arcade-hub .leaderboard-table tbody td {
    padding: 0.55rem 0.6rem;
    font-size: 0.8rem;
  }
  .arcade-hub .arcade-card-foot {
    flex-direction: column;
    align-items: flex-start;
  }
  .arcade-hub .arcade-card-foot .px-btn { width: 100%; justify-content: center; }
}
</style>

<div class="arcade-hub container-fluid py-2 px-md-3">

  <div class="arcade-hub-header">
    <div class="row align-items-center g-4">
      <div class="col-lg-8">
        <div class="arcade-eyebrow">
          <i class="bi bi-controller"></i>
          Arcade Zone &bull; 5 Games
          <span class="blink"></span>
        </div>
        <h1 class="arcade-title">
          Typing <span class="underscore">Arcade</span> Arena
        </h1>
        <p class="arcade-lead">
          Level up your typing speed, peripheral perception, and muscle memory through 5 gamified retro arcade challenges. Slash flying fruit with katanas, fire orbital defense lasers, burn highway asphalt at 180 MPH, pop rising sky balloons, and neutralize falling meteors!
        </p>

        <div class="arcade-quicknav">
          <a href="<?= BASE_URL ?>/game-ninja.php" class="px-btn px-btn-sm">
            <i class="bi bi-slash-circle-fill"></i> Ninja
          </a>
          <a href="<?= BASE_URL ?>/game-space.php" class="px-btn px-btn-sm">
            <i class="bi bi-rocket-takeoff-fill"></i> Space
          </a>
          <a href="<?= BASE_URL ?>/game-racer.php" class="px-btn px-btn-sm">
            <i class="bi bi-speedometer2"></i> Racer
          </a>
          <a href="<?= BASE_URL ?>/game-balloon.php" class="px-btn px-btn-sm">
            <i class="bi bi-balloon-fill"></i> Balloon
          </a>
          <a href="<?= BASE_URL ?>/game-meteor.php" class="px-btn px-btn-sm">
            <i class="bi bi-fire"></i> Meteor
          </a>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="records-panel">
          <div class="records-panel-head">
            <span class="records-panel-title">Your Records</span>
            <span class="records-badge">Cloud</span>
          </div>

          <div class="score-stat-box">
            <div class="left"><i class="bi bi-slash-circle-fill"></i><span>Ninja</span></div>
            <span class="pts"><?= number_format($highScores['ninja']['score']) ?> PTS</span>
          </div>
          <div class="score-stat-box">
            <div class="left"><i class="bi bi-rocket-takeoff-fill"></i><span>Space</span></div>
            <span class="pts"><?= number_format($highScores['space']['score']) ?> PTS</span>
          </div>
          <div class="score-stat-box">
            <div class="left"><i class="bi bi-speedometer2"></i><span>Grand Prix</span></div>
            <span class="pts"><?= number_format($highScores['racer']['score']) ?> PTS</span>
          </div>
          <div class="score-stat-box">
            <div class="left"><i class="bi bi-balloon-fill"></i><span>Balloon</span></div>
            <span class="pts"><?= number_format($highScores['balloon']['score']) ?> PTS</span>
          </div>
          <div class="score-stat-box">
            <div class="left"><i class="bi bi-fire"></i><span>Meteor</span></div>
            <span class="pts"><?= number_format($highScores['meteor']['score']) ?> PTS</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4 mb-5">

    <div class="col-lg-4 col-md-6">
      <div class="arcade-game-card">
        <div class="card-top-row">
          <div class="card-hero-icon"><i class="bi bi-slash-circle-fill"></i></div>
          <span class="card-tag-pill">★ Most Popular</span>
        </div>
        <h4>Keyboard Ninja</h4>
        <p>Slash flying fruit with katana strikes. Isolate Home Row, Top Row, or Numbers while dodging explosive bombs!</p>
        <div class="d-flex flex-wrap gap-2 mb-3">
          <span class="feature-pill-badge"><i class="bi bi-check2"></i> Fruit</span>
          <span class="feature-pill-badge"><i class="bi bi-check2"></i> Row Isolation</span>
          <span class="feature-pill-badge"><i class="bi bi-check2"></i> Katana SFX</span>
        </div>
        <div class="arcade-card-foot">
          <div>
            <div class="best-label">Best Score</div>
            <div class="best-score"><?= number_format($highScores['ninja']['score']) ?> PTS</div>
          </div>
          <a href="<?= BASE_URL ?>/game-ninja.php" class="px-btn px-btn-primary px-btn-sm">
            <span>Play</span><i class="bi bi-arrow-right"></i>
          </a>
        </div>
      </div>
    </div>

    <div class="col-lg-4 col-md-6">
      <div class="arcade-game-card">
        <div class="card-top-row">
          <div class="card-hero-icon"><i class="bi bi-rocket-takeoff-fill"></i></div>
          <span class="card-tag-pill">★ Shooter</span>
        </div>
        <h4>Space Word Defender</h4>
        <p>Defend orbital command from descending alien warships. Type complete words to fire laser cannons!</p>
        <div class="d-flex flex-wrap gap-2 mb-3">
          <span class="feature-pill-badge"><i class="bi bi-check2"></i> Lasers</span>
          <span class="feature-pill-badge"><i class="bi bi-check2"></i> Target Lock</span>
          <span class="feature-pill-badge"><i class="bi bi-check2"></i> Shield HP</span>
        </div>
        <div class="arcade-card-foot">
          <div>
            <div class="best-label">Best Score</div>
            <div class="best-score"><?= number_format($highScores['space']['score']) ?> PTS</div>
          </div>
          <a href="<?= BASE_URL ?>/game-space.php" class="px-btn px-btn-primary px-btn-sm">
            <span>Play</span><i class="bi bi-arrow-right"></i>
          </a>
        </div>
      </div>
    </div>

    <div class="col-lg-4 col-md-6">
      <div class="arcade-game-card">
        <div class="card-top-row">
          <div class="card-hero-icon"><i class="bi bi-speedometer2"></i></div>
          <span class="card-tag-pill">★ Racer</span>
        </div>
        <h4>Cyber Grand Prix</h4>
        <p>Accelerate your supercar past 150 MPH! Type words to ignite nitro exhaust flames and overtake rivals from P25 to P1.</p>
        <div class="d-flex flex-wrap gap-2 mb-3">
          <span class="feature-pill-badge"><i class="bi bi-check2"></i> Nitro</span>
          <span class="feature-pill-badge"><i class="bi bi-check2"></i> Overtake</span>
          <span class="feature-pill-badge"><i class="bi bi-check2"></i> Engine SFX</span>
        </div>
        <div class="arcade-card-foot">
          <div>
            <div class="best-label">Best Score</div>
            <div class="best-score"><?= number_format($highScores['racer']['score']) ?> PTS</div>
          </div>
          <a href="<?= BASE_URL ?>/game-racer.php" class="px-btn px-btn-primary px-btn-sm">
            <span>Play</span><i class="bi bi-arrow-right"></i>
          </a>
        </div>
      </div>
    </div>

    <div class="col-lg-6 col-md-6">
      <div class="arcade-game-card">
        <div class="card-top-row">
          <div class="card-hero-icon"><i class="bi bi-balloon-fill"></i></div>
          <span class="card-tag-pill">★ Classic</span>
        </div>
        <h4>Type-a-Balloon</h4>
        <p>Float into the clouds! Pop colorful helium balloons with sharp darts before they drift into the stratosphere. Perfect for finger accuracy and letter recognition.</p>
        <div class="d-flex flex-wrap gap-2 mb-3">
          <span class="feature-pill-badge"><i class="bi bi-check2"></i> Confetti</span>
          <span class="feature-pill-badge"><i class="bi bi-check2"></i> Gold 3X</span>
          <span class="feature-pill-badge"><i class="bi bi-check2"></i> Storm</span>
        </div>
        <div class="arcade-card-foot">
          <div>
            <div class="best-label">Best Score</div>
            <div class="best-score"><?= number_format($highScores['balloon']['score']) ?> PTS</div>
          </div>
          <a href="<?= BASE_URL ?>/game-balloon.php" class="px-btn px-btn-primary px-btn-sm">
            <span>Play</span><i class="bi bi-arrow-right"></i>
          </a>
        </div>
      </div>
    </div>

    <div class="col-lg-6 col-md-12">
      <div class="arcade-game-card">
        <div class="card-top-row">
          <div class="card-hero-icon"><i class="bi bi-fire"></i></div>
          <span class="card-tag-pill">★ Railgun</span>
        </div>
        <h4>Meteor Matrix Defense</h4>
        <p>Fiery meteors are hurtling toward the metropolitan city grid. Fire ground railgun plasma rounds and detonate blue EMP shockwaves to save the grid!</p>
        <div class="d-flex flex-wrap gap-2 mb-3">
          <span class="feature-pill-badge"><i class="bi bi-check2"></i> Railgun</span>
          <span class="feature-pill-badge"><i class="bi bi-check2"></i> EMP Shockwave</span>
          <span class="feature-pill-badge"><i class="bi bi-check2"></i> Fire Trail</span>
        </div>
        <div class="arcade-card-foot">
          <div>
            <div class="best-label">Best Score</div>
            <div class="best-score"><?= number_format($highScores['meteor']['score']) ?> PTS</div>
          </div>
          <a href="<?= BASE_URL ?>/game-meteor.php" class="px-btn px-btn-primary px-btn-sm">
            <span>Play</span><i class="bi bi-arrow-right"></i>
          </a>
        </div>
      </div>
    </div>

  </div>

  <div class="hall-of-fame">
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
      <div>
        <h4 class="hall-title"><i class="bi bi-trophy-fill me-2"></i>Hall of Fame</h4>
        <p class="hall-sub">High scores recorded across all 5 arcade challenges.</p>
      </div>

      <ul class="nav nav-pills" id="arcadeTabs" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="tab-ninja-btn" data-bs-toggle="pill" data-bs-target="#tab-ninja" type="button" role="tab">
            <i class="bi bi-slash-circle-fill"></i> Ninja
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="tab-space-btn" data-bs-toggle="pill" data-bs-target="#tab-space" type="button" role="tab">
            <i class="bi bi-rocket-takeoff-fill"></i> Space
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="tab-racer-btn" data-bs-toggle="pill" data-bs-target="#tab-racer" type="button" role="tab">
            <i class="bi bi-speedometer2"></i> Racer
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="tab-balloon-btn" data-bs-toggle="pill" data-bs-target="#tab-balloon" type="button" role="tab">
            <i class="bi bi-balloon-fill"></i> Balloon
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="tab-meteor-btn" data-bs-toggle="pill" data-bs-target="#tab-meteor" type="button" role="tab">
            <i class="bi bi-fire"></i> Meteor
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
            <div class="empty-state">
              <i class="bi bi-controller"></i>
              <p>No scores recorded yet for this arcade challenge.</p>
              <a href="<?= BASE_URL ?>/game-<?= $slug ?>.php" class="px-btn px-btn-primary px-btn-sm">
                Be the First Champion
              </a>
            </div>
          <?php else: ?>
            <div class="table-responsive">
              <table class="leaderboard-table">
                <thead>
                  <tr>
                    <th style="width: 90px;">Rank</th>
                    <th>Typist</th>
                    <th class="text-end">Score</th>
                    <th class="text-end">Streak</th>
                    <th class="text-end">Accuracy</th>
                    <th class="text-end">Date</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($entries as $idx => $entry): ?>
                    <tr>
                      <td>
                        <?php if ($idx === 0): ?>
                          <span class="rank-badge rank-badge-1"><i class="bi bi-trophy-fill"></i> 1st</span>
                        <?php elseif ($idx === 1): ?>
                          <span class="rank-badge rank-badge-2">2nd</span>
                        <?php elseif ($idx === 2): ?>
                          <span class="rank-badge rank-badge-3">3rd</span>
                        <?php else: ?>
                          <span class="rank-plain">#<?= $idx + 1 ?></span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <div class="d-flex align-items-center gap-2">
                          <div class="user-avatar-pixel">
                            <span><?= strtoupper(substr($entry['username'], 0, 1)) ?></span>
                          </div>
                          <div>
                            <div class="player-name"><?= e($entry['full_name'] ?: $entry['username']) ?></div>
                            <div class="player-handle">@<?= e($entry['username']) ?></div>
                          </div>
                        </div>
                      </td>
                      <td class="text-end fw-bold" style="font-family: var(--px-mono); color: var(--px-ink);">
                        <?= number_format($entry['score']) ?>
                      </td>
                      <td class="text-end" style="font-family: var(--px-mono); color: var(--px-ink);">
                        <?= $entry['highest_streak'] ?> hits
                      </td>
                      <td class="text-end" style="font-family: var(--px-mono); color: var(--px-ink);">
                        <?= number_format($entry['accuracy'], 1) ?>%
                      </td>
                      <td class="text-end" style="font-family: var(--px-mono); color: var(--px-ink); opacity: 0.75;">
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