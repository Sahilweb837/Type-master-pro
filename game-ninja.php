 <?php
/**
 * Custom Type Master Pro
 * Game 1: Keyboard Ninja — HD Arcade Edition
 */

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/games_data.php';

$pageTitle = 'Keyboard Ninja — HD Typing Arcade';
$currentUser = current_user();
$userId = $currentUser ? (int)$currentUser['id'] : 0;

$userHighScore = get_user_game_high_score($userId, 'ninja');
$leaderboard = get_game_leaderboard('ninja', 5);

include __DIR__ . '/includes/header.php';
?>

<style>
/* ============================================================
   KEYBOARD NINJA — HD ARCADE STYLING
   ============================================================ */
.kn-wrap {
    --kn-bg:       #07090d;
    --kn-bg-2:     #0e1118;
    --kn-surface:  #131722;
    --kn-line:     #232838;
    --kn-ink:      #f5f5f4;
    --kn-muted:    #8b8f9c;
    --kn-accent:   #ff4d00;
    --kn-mono:     "JetBrains Mono", ui-monospace, monospace;
    --kn-sans:     "Inter", -apple-system, BlinkMacSystemFont, sans-serif;
    max-width: 1320px;
    margin: 0 auto;
    padding: 1rem;
    font-family: var(--kn-sans);
    color: var(--kn-ink);
}
.kn-wrap * { box-sizing: border-box; }

/* ---------- Top bar ---------- */
.kn-topbar {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}
.kn-title-block { display: flex; align-items: center; gap: 0.85rem; }
.kn-back-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.5rem 0.9rem;
    background: var(--kn-surface);
    border: 1px solid var(--kn-line);
    border-radius: 8px;
    color: var(--kn-ink);
    text-decoration: none;
    font-size: 0.78rem;
    font-weight: 600;
    transition: all 0.15s ease;
}
.kn-back-btn:hover {
    background: var(--kn-bg-2);
    border-color: var(--kn-muted);
    color: var(--kn-ink);
}
.kn-title-text h1 {
    font-size: 1.35rem;
    font-weight: 800;
    letter-spacing: -0.03em;
    margin: 0;
    color: var(--kn-ink);
    line-height: 1.1;
}
.kn-title-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-family: var(--kn-mono);
    font-size: 0.6rem;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: var(--kn-accent);
    margin-bottom: 0.25rem;
}

/* ---------- HUD row ---------- */
.kn-hud {
    display: flex;
    align-items: stretch;
    gap: 0.5rem;
    flex-wrap: wrap;
}
.kn-hud-chip {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    padding: 0.55rem 0.85rem;
    background: var(--kn-surface);
    border: 1px solid var(--kn-line);
    border-radius: 10px;
    min-width: 82px;
}
.kn-hud-chip .kn-hud-icon {
    font-size: 1rem;
    color: var(--kn-accent);
    line-height: 1;
}
.kn-hud-chip .kn-hud-icon.gold { color: #facc15; }
.kn-hud-chip .kn-hud-icon.heart { color: #ef4444; }
.kn-hud-meta { display: flex; flex-direction: column; line-height: 1.1; }
.kn-hud-label {
    font-family: var(--kn-mono);
    font-size: 0.55rem;
    font-weight: 600;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: var(--kn-muted);
    margin-bottom: 0.15rem;
}
.kn-hud-value {
    font-family: var(--kn-mono);
    font-size: 1.1rem;
    font-weight: 800;
    color: var(--kn-ink);
    letter-spacing: -0.01em;
}

.kn-lives {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}
.kn-lives i { font-size: 1.15rem; transition: all 0.25s ease; }

.kn-icon-btn {
    width: 40px;
    height: 40px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: var(--kn-surface);
    border: 1px solid var(--kn-line);
    border-radius: 10px;
    color: var(--kn-ink);
    cursor: pointer;
    font-size: 1rem;
    transition: all 0.15s ease;
    -webkit-appearance: none;
    appearance: none;
    flex-shrink: 0;
}
.kn-icon-btn:hover {
    border-color: var(--kn-muted);
    background: var(--kn-bg-2);
    transform: translateY(-1px);
}

/* ---------- Control bar ---------- */
.kn-controls {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    background: var(--kn-surface);
    border: 1px solid var(--kn-line);
    border-radius: 12px;
    margin-bottom: 1rem;
}
.kn-control-group {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    flex-wrap: wrap;
}
.kn-control-label {
    font-family: var(--kn-mono);
    font-size: 0.65rem;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: var(--kn-muted);
}
.kn-seg {
    display: inline-flex;
    padding: 3px;
    background: var(--kn-bg-2);
    border: 1px solid var(--kn-line);
    border-radius: 8px;
    gap: 2px;
}
.kn-seg button {
    padding: 0.4rem 0.7rem;
    background: transparent;
    border: none;
    font-family: var(--kn-mono);
    font-size: 0.7rem;
    font-weight: 600;
    letter-spacing: 0.02em;
    color: var(--kn-muted);
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.15s ease;
    -webkit-appearance: none;
    appearance: none;
    white-space: nowrap;
}
.kn-seg button:hover { color: var(--kn-ink); }
.kn-seg button.active {
    background: var(--kn-accent);
    color: #fff;
}

/* ---------- Game stage ---------- */
.kn-stage {
    position: relative;
    width: 100%;
    height: 620px;
    background: #05070b;
    border: 1px solid var(--kn-line);
    border-radius: 16px;
    overflow: hidden;
    box-shadow:
        0 0 0 1px rgba(255,255,255,0.02) inset,
        0 24px 60px rgba(0,0,0,0.55),
        0 6px 16px rgba(0,0,0,0.35);
}
.kn-stage canvas {
    display: block;
    width: 100%;
    height: 100%;
    touch-action: none;
    cursor: crosshair;
}
@media (max-width: 768px) {
    .kn-stage { height: 500px; }
}
@media (max-width: 480px) {
    .kn-stage { height: 440px; border-radius: 12px; }
    .kn-hud-chip { min-width: 66px; padding: 0.45rem 0.6rem; }
    .kn-hud-value { font-size: 0.95rem; }
    .kn-hud-label { font-size: 0.5rem; }
    .kn-icon-btn { width: 36px; height: 36px; }
    .kn-title-text h1 { font-size: 1.1rem; }
    .kn-controls { padding: 0.65rem 0.75rem; }
    .kn-control-label { display: none; }
}

/* ---------- Overlays ---------- */
.kn-overlay {
    position: absolute;
    inset: 0;
    display: none;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 1.5rem;
    background: radial-gradient(ellipse at center, rgba(13,17,26,0.92) 0%, rgba(5,7,11,0.97) 100%);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    z-index: 10;
}
.kn-overlay.show { display: flex; }

.kn-overlay-icon {
    width: 88px;
    height: 88px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 2.4rem;
    margin-bottom: 1.25rem;
    position: relative;
}
.kn-overlay-icon.kn-slash {
    background: rgba(255, 77, 0, 0.1);
    border: 2px solid rgba(255, 77, 0, 0.35);
    color: var(--kn-accent);
    animation: knFloat 3s ease-in-out infinite;
}
.kn-overlay-icon.kn-dead {
    background: rgba(239, 68, 68, 0.1);
    border: 2px solid rgba(239, 68, 68, 0.35);
    color: #ef4444;
}
@keyframes knFloat {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-8px); }
}

.kn-overlay h2 {
    font-family: var(--kn-sans);
    font-size: clamp(1.75rem, 4vw, 2.75rem);
    font-weight: 800;
    letter-spacing: -0.03em;
    color: var(--kn-ink);
    margin: 0 0 0.6rem;
    line-height: 1.05;
}
.kn-overlay p.kn-overlay-desc {
    font-size: 0.95rem;
    line-height: 1.6;
    color: var(--kn-muted);
    max-width: 480px;
    margin: 0 0 1.75rem;
}

.kn-play-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.6rem;
    padding: 0.95rem 2rem;
    background: var(--kn-accent);
    color: #fff;
    border: none;
    border-radius: 10px;
    font-family: var(--kn-sans);
    font-size: 1rem;
    font-weight: 800;
    letter-spacing: 0.01em;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 8px 24px rgba(255,77,0,0.35);
    -webkit-appearance: none;
    appearance: none;
}
.kn-play-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 32px rgba(255,77,0,0.5);
    background: #ff5d17;
}

.kn-shortcuts {
    display: flex;
    align-items: center;
    gap: 1.25rem;
    font-size: 0.78rem;
    color: var(--kn-muted);
    margin-top: 1.75rem;
    flex-wrap: wrap;
    justify-content: center;
}
.kn-shortcuts kbd {
    display: inline-block;
    padding: 0.15rem 0.45rem;
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 4px;
    font-family: var(--kn-mono);
    font-size: 0.7rem;
    color: var(--kn-ink);
    margin: 0 0.1rem;
}

/* Results grid */
.kn-results {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.75rem;
    max-width: 520px;
    width: 100%;
    margin: 0 0 1.75rem;
}
.kn-result-card {
    background: var(--kn-surface);
    border: 1px solid var(--kn-line);
    border-radius: 10px;
    padding: 1rem 0.75rem;
    text-align: center;
}
.kn-result-label {
    font-family: var(--kn-mono);
    font-size: 0.6rem;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: var(--kn-muted);
    margin-bottom: 0.4rem;
}
.kn-result-value {
    font-family: var(--kn-mono);
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--kn-ink);
    letter-spacing: -0.02em;
    line-height: 1;
}
.kn-result-value.gold { color: #facc15; }
.kn-result-value.green { color: #10b981; }

.kn-overlay-actions {
    display: flex;
    gap: 0.6rem;
    flex-wrap: wrap;
    justify-content: center;
}
.kn-secondary-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.85rem 1.4rem;
    background: transparent;
    color: var(--kn-ink);
    border: 1px solid var(--kn-line);
    border-radius: 10px;
    font-family: var(--kn-sans);
    font-size: 0.9rem;
    font-weight: 700;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.15s ease;
    -webkit-appearance: none;
    appearance: none;
}
.kn-secondary-btn:hover {
    background: var(--kn-surface);
    border-color: var(--kn-muted);
    color: var(--kn-ink);
}

/* ---------- Tips row ---------- */
.kn-tips {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.75rem;
    margin-top: 1rem;
}
.kn-tip {
    display: flex;
    align-items: flex-start;
    gap: 0.85rem;
    padding: 1rem;
    background: var(--kn-surface);
    border: 1px solid var(--kn-line);
    border-radius: 12px;
}
.kn-tip-icon {
    width: 38px;
    height: 38px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 9px;
    font-size: 1rem;
}
.kn-tip-icon.orange { background: rgba(255,77,0,0.12); color: var(--kn-accent); }
.kn-tip-icon.red    { background: rgba(239,68,68,0.12); color: #ef4444; }
.kn-tip-icon.cyan   { background: rgba(56,189,248,0.12); color: #38bdf8; }
.kn-tip-body h4 {
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--kn-ink);
    margin: 0 0 0.25rem;
}
.kn-tip-body p {
    font-size: 0.78rem;
    line-height: 1.55;
    color: var(--kn-muted);
    margin: 0;
}
@media (max-width: 768px) {
    .kn-tips { grid-template-columns: 1fr; }
    .kn-results { grid-template-columns: 1fr; max-width: 340px; }
}
</style>

<div class="kn-wrap">

  <!-- ============ TOP BAR ============ -->
  <div class="kn-topbar">
    <div class="kn-title-block">
      <a href="<?= BASE_URL ?>/games.php" class="kn-back-btn">
        <i class="bi bi-arrow-left"></i> Arcade
      </a>
      <div class="kn-title-text">
        <div class="kn-title-badge">
          <i class="bi bi-slash-circle"></i> ARCADE REFLEX
        </div>
        <h1>Keyboard Ninja 🥷</h1>
      </div>
    </div>

    <div class="kn-hud">
      <!-- Best score -->
      <div class="kn-hud-chip">
        <i class="bi bi-trophy-fill kn-hud-icon gold"></i>
        <div class="kn-hud-meta">
          <div class="kn-hud-label">Best</div>
          <div class="kn-hud-value" id="bestScoreText"><?= number_format($userHighScore['score']) ?></div>
        </div>
      </div>

      <!-- Current score -->
      <div class="kn-hud-chip">
        <i class="bi bi-lightning-charge-fill kn-hud-icon"></i>
        <div class="kn-hud-meta">
          <div class="kn-hud-label">Score</div>
          <div class="kn-hud-value" id="currentScore">0</div>
        </div>
      </div>

      <!-- Lives -->
      <div class="kn-hud-chip">
        <div class="kn-lives" id="livesContainer">
          <i class="bi bi-heart-fill text-danger" id="heart-1"></i>
          <i class="bi bi-heart-fill text-danger" id="heart-2"></i>
          <i class="bi bi-heart-fill text-danger" id="heart-3"></i>
        </div>
      </div>

      <!-- Sound toggle -->
      <button type="button" class="kn-icon-btn" id="toggleSoundBtn" title="Toggle Sound">
        <i class="bi bi-volume-up-fill" id="soundIcon"></i>
      </button>

      <!-- Restart -->
      <button type="button" class="kn-icon-btn" id="restartGameBtn" title="Restart Game">
        <i class="bi bi-arrow-clockwise"></i>
      </button>
    </div>
  </div>

  <!-- ============ CONTROL BAR ============ -->
  <div class="kn-controls">
    <div class="kn-control-group">
      <span class="kn-control-label">Practice</span>
      <div class="kn-seg" id="modePickerGroup">
        <button type="button" class="active" data-mode="home">Home Row</button>
        <button type="button" data-mode="top">Top Row</button>
        <button type="button" data-mode="bottom">Bottom Row</button>
        <button type="button" data-mode="numbers">Numbers</button>
        <button type="button" data-mode="all">All Letters</button>
      </div>
    </div>

    <div class="kn-control-group">
      <span class="kn-control-label">Difficulty</span>
      <div class="kn-seg" id="diffPickerGroup">
        <button type="button" data-diff="easy">Easy</button>
        <button type="button" class="active" data-diff="medium">Medium</button>
        <button type="button" data-diff="hard">Hard</button>
      </div>
    </div>
  </div>

  <!-- ============ GAME STAGE ============ -->
  <div class="kn-stage" id="ninjaGameContainer">
    <canvas id="ninjaCanvas"></canvas>

    <!-- Start overlay -->
    <div class="kn-overlay show" id="ninjaStartOverlay">
      <div class="kn-overlay-icon kn-slash">
        <i class="bi bi-slash-circle-fill"></i>
      </div>
      <h2>Keyboard Ninja</h2>
      <p class="kn-overlay-desc">
        Type the letter on each fruit to slice it mid-air. Avoid the bombs. Don't let fruit hit the ground.
      </p>
      <button type="button" class="kn-play-btn" id="startPlayBtn">
        <i class="bi bi-play-fill"></i> Play Now
      </button>
      <div class="kn-shortcuts">
        <span><kbd>Any Key</kbd> to Slash</span>
        <span>•</span>
        <span><kbd>ESC</kbd> to Pause</span>
      </div>
    </div>

    <!-- Pause overlay -->
    <div class="kn-overlay" id="ninjaPauseOverlay">
      <div class="kn-overlay-icon kn-slash">
        <i class="bi bi-pause-fill"></i>
      </div>
      <h2>Paused</h2>
      <p class="kn-overlay-desc">Take a breath. Your katana is ready when you are.</p>
      <button type="button" class="kn-play-btn" id="resumeGameBtn">
        <i class="bi bi-play-fill"></i> Resume
      </button>
    </div>

    <!-- Game over overlay -->
    <div class="kn-overlay" id="ninjaGameOverOverlay">
      <div class="kn-overlay-icon kn-dead">
        <i class="bi bi-x-octagon-fill"></i>
      </div>
      <h2>Game Over</h2>
      <p class="kn-overlay-desc">Every missed stroke is a lesson for the next katana strike.</p>

      <div class="kn-results">
        <div class="kn-result-card">
          <div class="kn-result-label">Score</div>
          <div class="kn-result-value" id="goFinalScore">0</div>
        </div>
        <div class="kn-result-card">
          <div class="kn-result-label">Max Streak</div>
          <div class="kn-result-value gold" id="goMaxStreak">0</div>
        </div>
        <div class="kn-result-card">
          <div class="kn-result-label">Accuracy</div>
          <div class="kn-result-value green" id="goAccuracy">0%</div>
        </div>
      </div>

      <div class="kn-overlay-actions">
        <button type="button" class="kn-play-btn" id="playAgainBtn">
          <i class="bi bi-arrow-counterclockwise"></i> Play Again
        </button>
        <a href="<?= BASE_URL ?>/games.php" class="kn-secondary-btn">
          <i class="bi bi-grid-3x3-gap"></i> Arcade Hub
        </a>
      </div>
    </div>
  </div>

  <!-- ============ TIPS ============ -->
  <div class="kn-tips">
    <div class="kn-tip">
      <div class="kn-tip-icon orange"><i class="bi bi-lightning-fill"></i></div>
      <div class="kn-tip-body">
        <h4>Build Multiplier Combos</h4>
        <p>Slice consecutive fruits without missing to earn up to 5× bonus points.</p>
      </div>
    </div>
    <div class="kn-tip">
      <div class="kn-tip-icon red"><i class="bi bi-radioactive"></i></div>
      <div class="kn-tip-body">
        <h4>Beware of Bombs 💣</h4>
        <p>Never type the letter on a glowing bomb. Letting them pass is safe.</p>
      </div>
    </div>
    <div class="kn-tip">
      <div class="kn-tip-icon cyan"><i class="bi bi-bullseye"></i></div>
      <div class="kn-tip-body">
        <h4>Target Muscle Memory</h4>
        <p>Switch between Home Row, Top Row, and Numbers to target weaknesses.</p>
      </div>
    </div>
  </div>

</div>

<!-- Config -->
<script>
window.NINJA_CONFIG = {
  csrfToken: <?= json_encode(get_csrf_token()) ?>,
  saveUrl: <?= json_encode(BASE_URL . '/api/save-game-score.php') ?>,
  bestScore: <?= (int)$userHighScore['score'] ?>
};
</script>

<!-- ============================================================
     HD ARCADE GAME ENGINE
     ============================================================ -->
<script>
(function() {
  'use strict';

  const config = window.NINJA_CONFIG;
  const canvas = document.getElementById('ninjaCanvas');
  const ctx = canvas.getContext('2d', { alpha: false });

  /* ---------- DOM ---------- */
  const currentScoreEl     = document.getElementById('currentScore');
  const bestScoreEl        = document.getElementById('bestScoreText');
  const startOverlay       = document.getElementById('ninjaStartOverlay');
  const pauseOverlay       = document.getElementById('ninjaPauseOverlay');
  const gameOverOverlay    = document.getElementById('ninjaGameOverOverlay');
  const soundIcon          = document.getElementById('soundIcon');
  const toggleSoundBtn     = document.getElementById('toggleSoundBtn');
  const restartBtn         = document.getElementById('restartGameBtn');
  const startPlayBtn       = document.getElementById('startPlayBtn');
  const resumeBtn          = document.getElementById('resumeGameBtn');
  const playAgainBtn       = document.getElementById('playAgainBtn');

  /* ---------- Key sets ---------- */
  const keySets = {
    home:    ['A','S','D','F','J','K','L',';'],
    top:     ['Q','W','E','R','T','Y','U','I','O','P'],
    bottom:  ['Z','X','C','V','B','N','M'],
    numbers: ['1','2','3','4','5','6','7','8','9','0'],
    all:     'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('')
  };

  /* ---------- Fruit palette ---------- */
  const fruitPalette = [
    { name:'watermelon', rind:'#0d9488', rindDark:'#0f766e', flesh:'#f43f5e', seed:'#3f0a13', r:42, points:10 },
    { name:'orange',     rind:'#f97316', rindDark:'#c2410c', flesh:'#fdba74', seed:'#7c2d12', r:38, points:12 },
    { name:'apple',      rind:'#dc2626', rindDark:'#991b1b', flesh:'#fef3c7', seed:'#451a03', r:36, points:10 },
    { name:'lemon',      rind:'#eab308', rindDark:'#a16207', flesh:'#fef9c3', seed:'#713f12', r:34, points:15 },
    { name:'grape',      rind:'#7c3aed', rindDark:'#5b21b6', flesh:'#ddd6fe', seed:'#312e81', r:30, points:15 },
    { name:'strawberry', rind:'#e11d48', rindDark:'#9f1239', flesh:'#fecdd3', seed:'#fef08a', r:32, points:15 }
  ];

  /* ---------- State ---------- */
  let activeMode = 'home';
  let activeDiff = 'medium';
  let isRunning  = false;
  let isPaused   = false;
  let soundEnabled = true;
  let score = 0;
  let streak = 0;
  let maxStreak = 0;
  let totalStrikes = 0;
  let correctStrikes = 0;
  let lives = 3;
  let spawnTimer = 0;
  let spawnInterval = 75;
  let animationId = null;
  let lastTime = 0;
  let frameTime = 0;

  /* ---------- Screen shake ---------- */
  let shakeAmount = 0;

  /* ---------- Entities ---------- */
  let fruits = [];
  let halves = [];
  let particles = [];
  let slashes = [];
  let floating = [];
  let ribbons = [];

  /* ---------- Star field background ---------- */
  let stars = [];
  function initStars() {
    stars = [];
    const count = Math.floor((canvas.width * canvas.height) / 12000);
    for (let i = 0; i < count; i++) {
      stars.push({
        x: Math.random() * canvas.width,
        y: Math.random() * canvas.height,
        r: Math.random() * 1.5 + 0.3,
        a: Math.random() * 0.5 + 0.15,
        tw: Math.random() * Math.PI * 2,
        tws: Math.random() * 0.02 + 0.005
      });
    }
  }

  /* ---------- Canvas sizing ---------- */
  function resizeCanvas() {
    const dpr = Math.min(window.devicePixelRatio || 1, 2);
    const rect = canvas.parentElement.getBoundingClientRect();
    canvas.width  = Math.floor(rect.width * dpr);
    canvas.height = Math.floor(rect.height * dpr);
    canvas.style.width = rect.width + 'px';
    canvas.style.height = rect.height + 'px';
    ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    initStars();
  }
  window.addEventListener('resize', () => {
    clearTimeout(window.__knResize);
    window.__knResize = setTimeout(resizeCanvas, 120);
  });

  /* ---------- Audio ---------- */
  let audioCtx = null;
  function playSound(type) {
    if (!soundEnabled) return;
    try {
      if (!audioCtx) {
        const AC = window.AudioContext || window.webkitAudioContext;
        if (AC) audioCtx = new AC();
      }
      if (audioCtx && audioCtx.state === 'suspended') audioCtx.resume();
      if (!audioCtx) return;

      const now = audioCtx.currentTime;

      if (type === 'slash') {
        // metallic swoosh — two oscillators
        [1400, 900].forEach((freq, i) => {
          const osc = audioCtx.createOscillator();
          const gain = audioCtx.createGain();
          osc.type = 'sine';
          osc.frequency.setValueAtTime(freq + Math.random() * 300, now);
          osc.frequency.exponentialRampToValueAtTime(120, now + 0.14);
          gain.gain.setValueAtTime(0.18 - i * 0.06, now);
          gain.gain.exponentialRampToValueAtTime(0.001, now + 0.14);
          osc.connect(gain).connect(audioCtx.destination);
          osc.start(now);
          osc.stop(now + 0.15);
        });
      } else if (type === 'bomb') {
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.type = 'sawtooth';
        osc.frequency.setValueAtTime(140, now);
        osc.frequency.exponentialRampToValueAtTime(25, now + 0.4);
        gain.gain.setValueAtTime(0.45, now);
        gain.gain.exponentialRampToValueAtTime(0.001, now + 0.4);
        osc.connect(gain).connect(audioCtx.destination);
        osc.start(now);
        osc.stop(now + 0.42);
      } else if (type === 'miss') {
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.type = 'triangle';
        osc.frequency.setValueAtTime(200, now);
        osc.frequency.exponentialRampToValueAtTime(60, now + 0.2);
        gain.gain.setValueAtTime(0.22, now);
        gain.gain.exponentialRampToValueAtTime(0.001, now + 0.2);
        osc.connect(gain).connect(audioCtx.destination);
        osc.start(now);
        osc.stop(now + 0.22);
      }
    } catch (e) {}
  }

  /* ---------- Spawn ---------- */
  function spawnFruit() {
    const modeKeys = keySets[activeMode] || keySets.home;
    const key = modeKeys[Math.floor(Math.random() * modeKeys.length)];

    const bombChance = activeDiff === 'hard' ? 0.22 : activeDiff === 'medium' ? 0.12 : 0.04;
    const isBomb = Math.random() < bombChance;

    const speedMult = activeDiff === 'hard' ? 1.22 : activeDiff === 'easy' ? 0.82 : 1.0;
    const W = canvas.clientWidth;
    const H = canvas.clientHeight;

    const r = isBomb ? 32 : fruitPalette[Math.floor(Math.random() * fruitPalette.length)].r;
    const x = 80 + Math.random() * (W - 160);
    const y = H + r + 20;
    const vy = -(11 + Math.random() * 4) * speedMult * 0.9;
    const vx = (Math.random() - 0.5) * 5 * speedMult;

    fruits.push({
      x, y, vx, vy,
      key,
      rotation: Math.random() * Math.PI * 2,
      spin: (Math.random() - 0.5) * 0.08,
      radius: r,
      isBomb,
      type: isBomb ? null : fruitPalette.find(f => f.r === r) || fruitPalette[0],
      sliced: false,
      bob: Math.random() * Math.PI * 2
    });
  }

  /* ---------- Slice key ---------- */
  function sliceKey(pressedChar) {
    totalStrikes++;
    const upper = pressedChar.toUpperCase();

    let target = null, idx = -1;
    for (let i = 0; i < fruits.length; i++) {
      const f = fruits[i];
      if (!f.sliced && f.key === upper) {
        if (!target || f.y < target.y) { target = f; idx = i; }
      }
    }

    if (!target) return;
    correctStrikes++;

    if (target.isBomb) {
      playSound('bomb');
      createExplosion(target.x, target.y);
      shakeAmount = 22;
      loseLife('Bomb!');
      fruits.splice(idx, 1);
      streak = 0;
      return;
    }

    playSound('slash');
    streak++;
    if (streak > maxStreak) maxStreak = streak;

    const multiplier = Math.min(5, Math.floor(streak / 5) + 1);
    const earned = (target.type ? target.type.points : 10) * multiplier;
    score += earned;
    currentScoreEl.textContent = score.toLocaleString();

    // Slash line
    const angle = Math.random() * Math.PI * 2;
    const len = target.radius * 2.2;
    slashes.push({
      x1: target.x - Math.cos(angle) * len,
      y1: target.y - Math.sin(angle) * len,
      x2: target.x + Math.cos(angle) * len,
      y2: target.y + Math.sin(angle) * len,
      life: 1.0
    });

    createHalves(target);
    createJuice(target.x, target.y, target.type ? target.type.flesh : '#ff4d00', 24);

    floating.push({
      text: '+' + earned + (multiplier > 1 ? ' ×' + multiplier : ''),
      x: target.x,
      y: target.y,
      life: 1.0,
      color: multiplier > 1 ? '#facc15' : '#ffffff'
    });

    fruits.splice(idx, 1);
  }

  /* ---------- Effects ---------- */
  function createHalves(f) {
    const baseAngle = f.rotation;
    for (let side of [-1, 1]) {
      halves.push({
        x: f.x, y: f.y,
        vx: f.vx + side * 3,
        vy: f.vy * 0.7,
        rotation: baseAngle,
        spin: side * 0.1,
        type: f.type,
        side: side === -1 ? 'left' : 'right',
        radius: f.radius,
        life: 1.0
      });
    }
  }

  function createJuice(x, y, color, count) {
    for (let i = 0; i < count; i++) {
      const a = Math.random() * Math.PI * 2;
      const s = Math.random() * 8 + 2;
      particles.push({
        x, y,
        vx: Math.cos(a) * s,
        vy: Math.sin(a) * s,
        color,
        size: Math.random() * 5 + 2,
        life: 1.0,
        decay: 0.018 + Math.random() * 0.02,
        gravity: 0.25
      });
    }
  }

  function createExplosion(x, y) {
    const cols = ['#ef4444', '#f97316', '#facc15', '#ffffff', '#7c2d12'];
    for (let i = 0; i < 60; i++) {
      const a = Math.random() * Math.PI * 2;
      const s = Math.random() * 14 + 3;
      particles.push({
        x, y,
        vx: Math.cos(a) * s,
        vy: Math.sin(a) * s,
        color: cols[Math.floor(Math.random() * cols.length)],
        size: Math.random() * 8 + 3,
        life: 1.0,
        decay: 0.025,
        gravity: 0.3
      });
    }
  }

  function loseLife(reason) {
    lives--;
    updateLivesUI();
    floating.push({
      text: reason || 'Missed!',
      x: canvas.clientWidth / 2,
      y: canvas.clientHeight / 2,
      life: 1.4,
      color: '#ef4444',
      size: 26
    });
    if (lives <= 0) endGame();
  }

  function updateLivesUI() {
    for (let i = 1; i <= 3; i++) {
      const h = document.getElementById('heart-' + i);
      if (!h) continue;
      if (i > lives) {
        h.className = 'bi bi-heart text-secondary';
        h.style.opacity = '0.35';
      } else {
        h.className = 'bi bi-heart-fill text-danger';
        h.style.opacity = '1';
      }
    }
  }

  /* ---------- Draw helpers ---------- */
  function drawBackground() {
    const W = canvas.clientWidth, H = canvas.clientHeight;
    const g = ctx.createLinearGradient(0, 0, 0, H);
    g.addColorStop(0, '#05070b');
    g.addColorStop(0.5, '#0a0f1a');
    g.addColorStop(1, '#050810');
    ctx.fillStyle = g;
    ctx.fillRect(0, 0, W, H);

    // Stars
    for (const s of stars) {
      s.tw += s.tws;
      const alpha = s.a * (0.6 + 0.4 * Math.sin(s.tw));
      ctx.globalAlpha = alpha;
      ctx.fillStyle = '#ffffff';
      ctx.beginPath();
      ctx.arc(s.x, s.y, s.r, 0, Math.PI * 2);
      ctx.fill();
    }
    ctx.globalAlpha = 1;

    // Moon glow
    ctx.save();
    const moonX = W - 110, moonY = 90;
    const moonGrad = ctx.createRadialGradient(moonX, moonY, 0, moonX, moonY, 90);
    moonGrad.addColorStop(0, 'rgba(255,220,150,0.18)');
    moonGrad.addColorStop(1, 'rgba(255,220,150,0)');
    ctx.fillStyle = moonGrad;
    ctx.fillRect(moonX - 90, moonY - 90, 180, 180);
    ctx.restore();

    // Ground fog
    const fog = ctx.createLinearGradient(0, H - 60, 0, H);
    fog.addColorStop(0, 'rgba(20,30,50,0)');
    fog.addColorStop(1, 'rgba(10,20,40,0.5)');
    ctx.fillStyle = fog;
    ctx.fillRect(0, H - 60, W, 60);
  }

  function drawFruit(f) {
    ctx.save();
    ctx.translate(f.x, f.y);
    ctx.rotate(f.rotation);

    if (f.isBomb) {
      // Bomb glow
      const g = ctx.createRadialGradient(0, 0, 0, 0, 0, f.radius * 1.8);
      g.addColorStop(0, 'rgba(239,68,68,0.35)');
      g.addColorStop(1, 'rgba(239,68,68,0)');
      ctx.fillStyle = g;
      ctx.beginPath();
      ctx.arc(0, 0, f.radius * 1.8, 0, Math.PI * 2);
      ctx.fill();

      // Bomb body
      const bodyGrad = ctx.createRadialGradient(-f.radius * 0.3, -f.radius * 0.3, 2, 0, 0, f.radius);
      bodyGrad.addColorStop(0, '#3a3a44');
      bodyGrad.addColorStop(1, '#0f0f14');
      ctx.fillStyle = bodyGrad;
      ctx.beginPath();
      ctx.arc(0, 0, f.radius, 0, Math.PI * 2);
      ctx.fill();
      ctx.strokeStyle = '#ef4444';
      ctx.lineWidth = 2.5;
      ctx.stroke();

      // Fuse
      ctx.strokeStyle = '#92400e';
      ctx.lineWidth = 4;
      ctx.beginPath();
      ctx.moveTo(0, -f.radius);
      ctx.quadraticCurveTo(f.radius * 0.5, -f.radius - 12, 0, -f.radius - 18);
      ctx.stroke();

      // Spark
      ctx.fillStyle = '#facc15';
      ctx.beginPath();
      const sparkR = 4 + Math.sin(f.bob * 8) * 2;
      ctx.arc(0, -f.radius - 18, sparkR, 0, Math.PI * 2);
      ctx.fill();
      ctx.fillStyle = '#ffffff';
      ctx.beginPath();
      ctx.arc(0, -f.radius - 18, sparkR * 0.5, 0, Math.PI * 2);
      ctx.fill();
    } else if (f.type) {
      // Fruit body
      const g = ctx.createRadialGradient(-f.radius * 0.4, -f.radius * 0.4, 2, 0, 0, f.radius);
      g.addColorStop(0, f.type.rind);
      g.addColorStop(1, f.type.rindDark);
      ctx.fillStyle = g;
      ctx.beginPath();
      ctx.arc(0, 0, f.radius, 0, Math.PI * 2);
      ctx.fill();

      // Inner flesh hint
      ctx.fillStyle = f.type.flesh;
      ctx.globalAlpha = 0.85;
      ctx.beginPath();
      ctx.arc(0, 0, f.radius * 0.72, 0, Math.PI * 2);
      ctx.fill();
      ctx.globalAlpha = 1;

      // Highlights
      ctx.fillStyle = 'rgba(255,255,255,0.35)';
      ctx.beginPath();
      ctx.arc(-f.radius * 0.35, -f.radius * 0.35, f.radius * 0.28, 0, Math.PI * 2);
      ctx.fill();
      ctx.fillStyle = 'rgba(255,255,255,0.15)';
      ctx.beginPath();
      ctx.arc(f.radius * 0.4, f.radius * 0.3, f.radius * 0.15, 0, Math.PI * 2);
      ctx.fill();
    }

    // Key badge (upright)
    ctx.rotate(-f.rotation);
    ctx.fillStyle = f.isBomb ? 'rgba(239,68,68,0.95)' : 'rgba(10,10,10,0.92)';
    ctx.beginPath();
    ctx.arc(0, 0, 20, 0, Math.PI * 2);
    ctx.fill();
    ctx.strokeStyle = f.isBomb ? '#fca5a5' : '#ffffff';
    ctx.lineWidth = 2;
    ctx.stroke();

    ctx.fillStyle = '#ffffff';
    ctx.font = 'bold 17px "JetBrains Mono", monospace';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.fillText(f.key, 0, 1);

    ctx.restore();
  }

  function drawHalf(h) {
    ctx.save();
    ctx.translate(h.x, h.y);
    ctx.rotate(h.rotation);
    ctx.globalAlpha = Math.max(0, h.life);

    if (h.type) {
      const pathAngle = h.side === 'left'
        ? [Math.PI * 0.5, Math.PI * 1.5]
        : [-Math.PI * 0.5, Math.PI * 0.5];

      // Rind
      const rg = ctx.createRadialGradient(-h.radius * 0.3, -h.radius * 0.3, 2, 0, 0, h.radius);
      rg.addColorStop(0, h.type.rind);
      rg.addColorStop(1, h.type.rindDark);
      ctx.fillStyle = rg;
      ctx.beginPath();
      ctx.arc(0, 0, h.radius, pathAngle[0], pathAngle[1]);
      ctx.closePath();
      ctx.fill();

      // Flesh
      ctx.fillStyle = h.type.flesh;
      ctx.beginPath();
      ctx.arc(0, 0, h.radius * 0.85, pathAngle[0], pathAngle[1]);
      ctx.closePath();
      ctx.fill();

      // Seeds
      ctx.fillStyle = h.type.seed;
      for (let i = 0; i < 3; i++) {
        const a = pathAngle[0] + (pathAngle[1] - pathAngle[0]) * (0.3 + i * 0.15);
        const rr = h.radius * 0.45;
        ctx.beginPath();
        ctx.ellipse(Math.cos(a) * rr, Math.sin(a) * rr, 2.5, 4, a, 0, Math.PI * 2);
        ctx.fill();
      }
    }
    ctx.restore();
  }

  function drawParticle(p) {
    ctx.save();
    ctx.globalAlpha = Math.max(0, p.life);
    ctx.fillStyle = p.color;
    ctx.beginPath();
    ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
    ctx.fill();
    ctx.restore();
  }

  function drawSlash(s) {
    ctx.save();
    ctx.globalAlpha = Math.max(0, s.life);
    ctx.strokeStyle = '#ffffff';
    ctx.lineWidth = 5 * s.life;
    ctx.lineCap = 'round';
    ctx.shadowColor = '#38bdf8';
    ctx.shadowBlur = 18;
    ctx.beginPath();
    ctx.moveTo(s.x1, s.y1);
    ctx.lineTo(s.x2, s.y2);
    ctx.stroke();
    ctx.strokeStyle = 'rgba(56,189,248,' + s.life + ')';
    ctx.lineWidth = 2 * s.life;
    ctx.stroke();
    ctx.restore();
  }

  function drawFloating(t) {
    ctx.save();
    ctx.globalAlpha = Math.max(0, t.life);
    ctx.font = 'bold ' + (t.size || 20) + 'px "JetBrains Mono", monospace';
    ctx.fillStyle = t.color;
    ctx.textAlign = 'center';
    ctx.shadowColor = 'rgba(0,0,0,0.9)';
    ctx.shadowBlur = 6;
    ctx.fillText(t.text, t.x, t.y);
    ctx.restore();
  }

  /* ---------- Main loop (delta-time based) ---------- */
  function gameLoop(ts) {
    if (!isRunning || isPaused) return;

    if (!lastTime) lastTime = ts;
    let dt = (ts - lastTime) / 16.666; // normalize to 60fps
    lastTime = ts;
    if (dt > 3) dt = 3; // cap on tab-return spikes

    const W = canvas.clientWidth;
    const H = canvas.clientHeight;

    // Screen shake
    ctx.save();
    if (shakeAmount > 0.5) {
      const sx = (Math.random() - 0.5) * shakeAmount;
      const sy = (Math.random() - 0.5) * shakeAmount;
      ctx.translate(sx, sy);
      shakeAmount *= 0.88;
    } else {
      shakeAmount = 0;
    }

    // Background
    drawBackground();

    // Spawn
    spawnTimer += dt;
    if (spawnTimer >= spawnInterval) {
      spawnFruit();
      if (activeDiff === 'hard' && Math.random() > 0.55) spawnFruit();
      spawnTimer = 0;
    }

    // Fruits
    for (let i = fruits.length - 1; i >= 0; i--) {
      const f = fruits[i];
      f.x += f.vx * dt;
      f.y += f.vy * dt;
      f.vy += 0.28 * dt;
      f.rotation += f.spin * dt;
      f.bob += 0.05 * dt;

      if (f.y > H + 80 && f.vy > 0) {
        if (!f.isBomb) {
          playSound('miss');
          loseLife('Dropped!');
        }
        fruits.splice(i, 1);
        continue;
      }
      drawFruit(f);
    }

    // Halves
    for (let i = halves.length - 1; i >= 0; i--) {
      const h = halves[i];
      h.x += h.vx * dt;
      h.y += h.vy * dt;
      h.vy += 0.35 * dt;
      h.rotation += h.spin * dt;
      h.life -= 0.012 * dt;
      drawHalf(h);
      if (h.life <= 0 || h.y > H + 80) halves.splice(i, 1);
    }

    // Particles
    for (let i = particles.length - 1; i >= 0; i--) {
      const p = particles[i];
      p.x += p.vx * dt;
      p.y += p.vy * dt;
      p.vy += p.gravity * dt;
      p.life -= p.decay * dt;
      drawParticle(p);
      if (p.life <= 0) particles.splice(i, 1);
    }

    // Slashes
    for (let i = slashes.length - 1; i >= 0; i--) {
      const s = slashes[i];
      s.life -= 0.075 * dt;
      drawSlash(s);
      if (s.life <= 0) slashes.splice(i, 1);
    }

    // Floating texts
    for (let i = floating.length - 1; i >= 0; i--) {
      const t = floating[i];
      t.y -= 1.4 * dt;
      t.life -= 0.018 * dt;
      drawFloating(t);
      if (t.life <= 0) floating.splice(i, 1);
    }

    ctx.restore();

    animationId = requestAnimationFrame(gameLoop);
  }

  /* ---------- Start / end ---------- */
  function startGame() {
    score = 0; streak = 0; maxStreak = 0;
    totalStrikes = 0; correctStrikes = 0;
    lives = 3; spawnTimer = 0;
    fruits = []; halves = []; particles = []; slashes = []; floating = [];
    shakeAmount = 0;
    lastTime = 0;

    spawnInterval = activeDiff === 'hard' ? 50 : activeDiff === 'easy' ? 95 : 75;

    currentScoreEl.textContent = '0';
    updateLivesUI();

    startOverlay.classList.remove('show');
    pauseOverlay.classList.remove('show');
    gameOverOverlay.classList.remove('show');

    isRunning = true;
    isPaused = false;

    if (animationId) cancelAnimationFrame(animationId);
    animationId = requestAnimationFrame(gameLoop);
  }

  function endGame() {
    isRunning = false;
    if (animationId) cancelAnimationFrame(animationId);

    const accuracy = totalStrikes > 0 ? Math.round((correctStrikes / totalStrikes) * 100) : 100;

    document.getElementById('goFinalScore').textContent = score.toLocaleString();
    document.getElementById('goMaxStreak').textContent = maxStreak + '×';
    document.getElementById('goAccuracy').textContent = accuracy + '%';

    gameOverOverlay.classList.add('show');

    if (score > config.bestScore) {
      config.bestScore = score;
      bestScoreEl.textContent = score.toLocaleString();
    }

    fetch(config.saveUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': config.csrfToken
      },
      body: JSON.stringify({
        csrf_token: config.csrfToken,
        game_slug: 'ninja',
        score, highest_streak: maxStreak, accuracy,
        difficulty: activeDiff, mode: activeMode
      })
    }).catch(() => {});
  }

  /* ---------- Input ---------- */
  window.addEventListener('keydown', (e) => {
    if (!isRunning) return;

    if (e.key === 'Escape') {
      e.preventDefault();
      isPaused = !isPaused;
      if (isPaused) {
        pauseOverlay.classList.add('show');
        if (animationId) cancelAnimationFrame(animationId);
      } else {
        pauseOverlay.classList.remove('show');
        lastTime = 0;
        animationId = requestAnimationFrame(gameLoop);
      }
      return;
    }

    if (isPaused) return;

    if (e.key.length === 1 && !e.ctrlKey && !e.altKey && !e.metaKey) {
      e.preventDefault();
      sliceKey(e.key);
    }
  });

  /* ---------- Buttons ---------- */
  startPlayBtn.addEventListener('click', startGame);
  playAgainBtn.addEventListener('click', startGame);
  restartBtn.addEventListener('click', () => {
    if (isRunning || isPaused) { isPaused = false; isRunning = false; }
    startGame();
  });
  resumeBtn.addEventListener('click', () => {
    isPaused = false;
    pauseOverlay.classList.remove('show');
    lastTime = 0;
    animationId = requestAnimationFrame(gameLoop);
  });
  toggleSoundBtn.addEventListener('click', () => {
    soundEnabled = !soundEnabled;
    soundIcon.className = soundEnabled ? 'bi bi-volume-up-fill' : 'bi bi-volume-mute-fill';
  });

  /* ---------- Pickers ---------- */
  document.querySelectorAll('#modePickerGroup button').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('#modePickerGroup button').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      activeMode = btn.getAttribute('data-mode');
    });
  });
  document.querySelectorAll('#diffPickerGroup button').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('#diffPickerGroup button').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      activeDiff = btn.getAttribute('data-diff');
      spawnInterval = activeDiff === 'hard' ? 50 : activeDiff === 'easy' ? 95 : 75;
    });
  });

  /* ---------- Boot ---------- */
  resizeCanvas();
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>