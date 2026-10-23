<?php
/**
 * Custom Type Master Pro
 * Space Word Defender (Arcade Word Typing Game)
 * Inspired by retro space invaders and Typing.com keyboard games
 */

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/games_data.php';

$currentUser = current_user();
$userId = $currentUser ? (int)$currentUser['id'] : 0;
$highScore = get_user_game_high_score($userId, 'space');

$pageTitle = 'Space Word Defender — Arcade Typing';
$pageBadgeTitle = 'Space Defender';
$pageIcon = 'bi-rocket-takeoff-fill';

include __DIR__ . '/includes/header.php';
?>

<style>
/* Space Defender Game Styles */
.space-container {
  max-width: 1100px;
  margin: 0 auto;
}

.game-hud-bar {
  background: #0d1117;
  border: 1px solid #21262d;
  border-radius: 14px 14px 0 0;
  padding: 12px 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px;
  color: #c9d1d9;
}

.hud-stat-box {
  display: flex;
  align-items: center;
  gap: 8px;
}

.hud-stat-label {
  font-size: 0.72rem;
  font-family: 'JetBrains Mono', monospace;
  text-transform: uppercase;
  color: #8b949e;
  letter-spacing: 0.08em;
}

.hud-stat-val {
  font-size: 1.15rem;
  font-family: 'JetBrains Mono', monospace;
  font-weight: 800;
  color: #58a6ff;
}

.hud-stat-val.score {
  color: #3fb950;
}

.hud-stat-val.streak {
  color: #f0883e;
}

.hud-stat-val.shield {
  color: #38bdf8;
}

.canvas-wrapper {
  position: relative;
  width: 100%;
  background: #050811;
  border-left: 1px solid #21262d;
  border-right: 1px solid #21262d;
  overflow: hidden;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.7);
}

#spaceCanvas {
  display: block;
  width: 100%;
  height: 580px;
  cursor: crosshair;
}

.game-control-strip {
  background: #0d1117;
  border: 1px solid #21262d;
  border-top: none;
  border-radius: 0 0 14px 14px;
  padding: 12px 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px;
}

/* Modals & Overlays inside canvas */
.space-overlay {
  position: absolute;
  inset: 0;
  background: rgba(5, 8, 17, 0.92);
  backdrop-filter: blur(8px);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  z-index: 20;
  padding: 24px;
  text-align: center;
  transition: opacity 0.25s ease;
}

.space-overlay.d-none {
  display: none !important;
}

.overlay-title {
  font-size: 2.4rem;
  font-weight: 900;
  letter-spacing: -0.03em;
  background: linear-gradient(135deg, #38bdf8 0%, #818cf8 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  margin-bottom: 8px;
}

.overlay-desc {
  color: #8b949e;
  max-width: 540px;
  font-size: 0.95rem;
  line-height: 1.6;
  margin-bottom: 24px;
}

.mode-pill-btn {
  background: #161b22;
  border: 1px solid #30363d;
  color: #c9d1d9;
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s ease;
}

.mode-pill-btn:hover {
  border-color: #58a6ff;
  color: #58a6ff;
}

.mode-pill-btn.active {
  background: #1f6feb;
  border-color: #58a6ff;
  color: #ffffff;
}

.btn-arcade-start {
  background: linear-gradient(135deg, #38bdf8 0%, #2563eb 100%);
  color: #ffffff;
  border: none;
  font-weight: 800;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  padding: 12px 32px;
  border-radius: 10px;
  font-size: 1rem;
  box-shadow: 0 4px 18px rgba(37, 99, 235, 0.4);
  transition: all 0.2s ease;
}

.btn-arcade-start:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 24px rgba(37, 99, 235, 0.6);
  color: #ffffff;
}

.shield-progress-bar {
  width: 140px;
  height: 10px;
  background: #21262d;
  border-radius: 6px;
  overflow: hidden;
  border: 1px solid #30363d;
}

.shield-fill {
  height: 100%;
  width: 100%;
  background: linear-gradient(90deg, #f85149 0%, #e3b341 50%, #38bdf8 100%);
  transition: width 0.3s ease;
}

.stat-metric-card {
  background: #161b22;
  border: 1px solid #30363d;
  border-radius: 10px;
  padding: 12px 18px;
  min-width: 110px;
}

.stat-metric-card .val {
  font-family: 'JetBrains Mono', monospace;
  font-size: 1.5rem;
  font-weight: 800;
  color: #ffffff;
}

.stat-metric-card .lbl {
  font-size: 0.7rem;
  color: #8b949e;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}
</style>

<div class="space-container py-3">

  <!-- Breadcrumb & Back to Arcade -->
  <div class="d-flex align-items-center justify-content-between mb-3">
    <div class="d-flex align-items-center gap-2">
      <a href="<?= BASE_URL ?>/games.php" class="btn btn-sm btn-control d-inline-flex align-items-center gap-1">
        <i class="bi bi-arrow-left"></i>
        <span>Arcade Hub</span>
      </a>
      <span class="badge" style="background: rgba(56, 189, 248, 0.15); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.3);">
        <i class="bi bi-rocket-takeoff-fill me-1"></i> Word Velocity Shooter
      </span>
    </div>

    <div class="d-flex align-items-center gap-3">
      <div class="small text-muted d-none d-md-block">
        Personal Record: <span class="fw-bold text-info" id="topScoreDisplay"><?= number_format($highScore['score']) ?></span> pts
      </div>
      <button class="btn btn-sm btn-control" id="btnToggleSound" title="Toggle Sound FX">
        <i class="bi bi-volume-up-fill" id="soundIcon"></i>
      </button>
    </div>
  </div>

  <!-- Game HUD Bar -->
  <div class="game-hud-bar">
    <div class="d-flex align-items-center gap-4 flex-wrap">
      <div class="hud-stat-box">
        <span class="hud-stat-label">Score</span>
        <span class="hud-stat-val score" id="hudScore">0</span>
      </div>

      <div class="hud-stat-box">
        <span class="hud-stat-label">Wave</span>
        <span class="hud-stat-val" id="hudWave" style="color: #a371f7;">1</span>
      </div>

      <div class="hud-stat-box">
        <span class="hud-stat-label">Streak</span>
        <span class="hud-stat-val streak" id="hudStreak">0</span>
        <span class="badge bg-warning text-dark px-1.5 py-0.5" id="hudMultiplier" style="font-size: 0.65rem; font-weight: 800;">1x</span>
      </div>
    </div>

    <div class="d-flex align-items-center gap-4 flex-wrap">
      <div class="hud-stat-box">
        <span class="hud-stat-label">Accuracy</span>
        <span class="hud-stat-val" id="hudAccuracy" style="color: #58a6ff;">100%</span>
      </div>

      <div class="hud-stat-box">
        <span class="hud-stat-label">Shield</span>
        <div class="shield-progress-bar">
          <div class="shield-fill" id="hudShieldFill" style="width: 100%;"></div>
        </div>
        <span class="hud-stat-val shield" id="hudShieldText" style="font-size: 0.9rem;">100%</span>
      </div>
    </div>
  </div>

  <!-- Canvas Stage & Overlays -->
  <div class="canvas-wrapper">
    <canvas id="spaceCanvas" width="1000" height="580"></canvas>

    <!-- Start Overlay -->
    <div class="space-overlay" id="startOverlay">
      <div class="badge px-3 py-1 mb-2 rounded-pill" style="background: rgba(56, 189, 248, 0.15); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.3); font-size: 0.75rem;">
        ★ ARCADE DEFENSE SIMULATOR ★
      </div>
      <h1 class="overlay-title">Space Word Defender</h1>
      <p class="overlay-desc">
        Alien cruisers are descending upon your planetary perimeter. Type the full word floating above each enemy ship to target and fire high-frequency plasma lasers before they breach your shield!
      </p>

      <!-- Word Category Selection -->
      <div class="d-flex align-items-center gap-2 mb-3 flex-wrap justify-content-center">
        <span class="text-secondary small fw-bold me-1">Word Bank:</span>
        <button type="button" class="mode-pill-btn active" data-wordset="vocab">Core Vocabulary</button>
        <button type="button" class="mode-pill-btn" data-wordset="short">Speed Short (3-5 letters)</button>
        <button type="button" class="mode-pill-btn" data-wordset="tech">Code &amp; Tech Terms</button>
      </div>

      <!-- Difficulty Selection -->
      <div class="d-flex align-items-center gap-2 mb-4 flex-wrap justify-content-center">
        <span class="text-secondary small fw-bold me-1">Difficulty:</span>
        <button type="button" class="mode-pill-btn active" data-diff="easy">Cadet (Slow)</button>
        <button type="button" class="mode-pill-btn" data-diff="medium">Commander (Normal)</button>
        <button type="button" class="mode-pill-btn" data-diff="hard">Elite (Fast)</button>
      </div>

      <button type="button" class="btn-arcade-start d-flex align-items-center gap-2" id="btnStartGame">
        <i class="bi bi-play-circle-fill fs-5"></i>
        <span>Initialize Defense</span>
      </button>

      <div class="mt-4 text-muted small">
        <i class="bi bi-keyboard me-1"></i> Simply type the words on your keyboard. Press <kbd style="background: #21262d; color: #fff; padding: 2px 6px; border-radius: 4px;">ESC</kbd> to pause.
      </div>
    </div>

    <!-- Pause Overlay -->
    <div class="space-overlay d-none" id="pauseOverlay">
      <h2 class="overlay-title">System Paused</h2>
      <p class="overlay-desc">Defense batteries standing by. Take a breath and resume when ready.</p>
      <div class="d-flex gap-3">
        <button type="button" class="btn-arcade-start" id="btnResumeGame">Resume Mission</button>
        <button type="button" class="btn btn-outline-secondary px-4 py-2" id="btnRestartFromPause">Restart</button>
      </div>
    </div>

    <!-- Game Over Overlay -->
    <div class="space-overlay d-none" id="gameOverOverlay">
      <div class="badge bg-danger text-white px-3 py-1 mb-2 rounded-pill" style="font-size: 0.75rem;">SHIELD COMPROMISED</div>
      <h2 class="overlay-title" style="background: linear-gradient(135deg, #f85149 0%, #ff7b72 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Perimeter Breached!</h2>
      <p class="overlay-desc" id="gameOverReason">The planetary defense shield has collapsed under enemy bombardment.</p>

      <div class="d-flex gap-3 mb-4 flex-wrap justify-content-center">
        <div class="stat-metric-card">
          <div class="val text-success" id="finalScore">0</div>
          <div class="lbl">Final Score</div>
        </div>
        <div class="stat-metric-card">
          <div class="val text-info" id="finalWave">1</div>
          <div class="lbl">Wave Reached</div>
        </div>
        <div class="stat-metric-card">
          <div class="val text-warning" id="finalStreak">0</div>
          <div class="lbl">Best Streak</div>
        </div>
        <div class="stat-metric-card">
          <div class="val text-primary" id="finalAccuracy">100%</div>
          <div class="lbl">Accuracy</div>
        </div>
      </div>

      <div id="saveScoreFeedback" class="mb-3 small fw-bold text-success d-none">
        <i class="bi bi-cloud-check-fill me-1"></i> High score securely saved to leaderboard!
      </div>

      <div class="d-flex gap-3 flex-wrap justify-content-center">
        <button type="button" class="btn-arcade-start" id="btnPlayAgain">
          <i class="bi bi-arrow-repeat me-1"></i> Deploy Again
        </button>
        <a href="<?= BASE_URL ?>/games.php" class="btn btn-outline-secondary px-4 py-2">
          Back to Arcade Hub
        </a>
      </div>
    </div>
  </div>

  <!-- Bottom Control Strip -->
  <div class="game-control-strip">
    <div class="d-flex align-items-center gap-3">
      <span class="small text-muted"><i class="bi bi-info-circle me-1"></i> Controls:</span>
      <span class="badge bg-dark border text-light"><kbd class="bg-transparent border-0 text-white p-0">A-Z</kbd> Type Word</span>
      <span class="badge bg-dark border text-light"><kbd class="bg-transparent border-0 text-white p-0">ESC</kbd> Pause</span>
    </div>

    <div class="d-flex align-items-center gap-2">
      <a href="<?= BASE_URL ?>/game-ninja.php" class="btn btn-sm btn-control">
        <i class="bi bi-slash-circle-fill me-1" style="color: #ff4d00;"></i> Play Keyboard Ninja
      </a>
      <button type="button" class="btn btn-sm btn-control" id="btnRestartGame">
        <i class="bi bi-arrow-clockwise me-1"></i> Restart
      </button>
    </div>
  </div>

  <!-- Tips & Strategy Card -->
  <div class="soft-card p-4 mt-4">
    <div class="row g-4 align-items-center">
      <div class="col-md-8">
        <h5 class="fw-bold mb-2" style="color: var(--text-primary);"><i class="bi bi-lightbulb-fill text-warning me-2"></i> Tactical Typing Intel</h5>
        <p class="text-secondary small mb-2" style="line-height: 1.6;">
          Focus on the enemy ship closest to the bottom of the screen. As soon as you type the first letter of an enemy's word, that ship becomes <strong>locked on</strong> with a targeting reticle until destroyed. Once eliminated, your turret immediately frees up to lock onto the next closest threat!
        </p>
        <div class="d-flex gap-3 small text-muted">
          <span><i class="bi bi-check2-circle text-success me-1"></i> Higher streaks award up to <strong>4x</strong> score multiplier.</span>
          <span><i class="bi bi-shield-check text-info me-1"></i> Surviving each wave repairs <strong>15%</strong> shield HP.</span>
        </div>
      </div>
      <div class="col-md-4 text-md-end">
        <a href="<?= BASE_URL ?>/lessons.php" class="btn btn-sm soft-btn-primary">
          <i class="bi bi-mortarboard-fill me-1"></i> Improve Touch Typing
        </a>
      </div>
    </div>
  </div>

</div>

<!-- Synthetic Web Audio SFX Engine & Space Defender Game Engine -->
<script>
(function() {
  // --- Web Audio Synthesizer (Zero External File Dependencies) ---
  const AudioEngine = {
    ctx: null,
    enabled: true,

    init() {
      if (!this.ctx) {
        const AudioCtx = window.AudioContext || window.webkitAudioContext;
        if (AudioCtx) this.ctx = new AudioCtx();
      }
      if (this.ctx && this.ctx.state === 'suspended') {
        this.ctx.resume();
      }
    },

    toggle() {
      this.enabled = !this.enabled;
      return this.enabled;
    },

    playLaser() {
      if (!this.enabled || !this.ctx) return;
      const osc = this.ctx.createOscillator();
      const gain = this.ctx.createGain();
      const now = this.ctx.currentTime;

      osc.type = 'sawtooth';
      osc.frequency.setValueAtTime(880, now);
      osc.frequency.exponentialRampToValueAtTime(120, now + 0.14);

      gain.gain.setValueAtTime(0.18, now);
      gain.gain.exponentialRampToValueAtTime(0.001, now + 0.14);

      osc.connect(gain);
      gain.connect(this.ctx.destination);
      osc.start(now);
      osc.stop(now + 0.14);
    },

    playExplosion() {
      if (!this.enabled || !this.ctx) return;
      const now = this.ctx.currentTime;
      // White noise buffer
      const bufferSize = this.ctx.sampleRate * 0.3;
      const buffer = this.ctx.createBuffer(1, bufferSize, this.ctx.sampleRate);
      const output = buffer.getChannelData(0);
      for (let i = 0; i < bufferSize; i++) {
        output[i] = Math.random() * 2 - 1;
      }

      const whiteNoise = this.ctx.createBufferSource();
      whiteNoise.buffer = buffer;

      const filter = this.ctx.createBiquadFilter();
      filter.type = 'lowpass';
      filter.frequency.setValueAtTime(600, now);
      filter.frequency.exponentialRampToValueAtTime(80, now + 0.28);

      const gain = this.ctx.createGain();
      gain.gain.setValueAtTime(0.3, now);
      gain.gain.exponentialRampToValueAtTime(0.001, now + 0.28);

      whiteNoise.connect(filter);
      filter.connect(gain);
      gain.connect(this.ctx.destination);
      whiteNoise.start(now);
      whiteNoise.stop(now + 0.28);
    },

    playKeyCorrect() {
      if (!this.enabled || !this.ctx) return;
      const osc = this.ctx.createOscillator();
      const gain = this.ctx.createGain();
      const now = this.ctx.currentTime;

      osc.type = 'sine';
      osc.frequency.setValueAtTime(540, now);
      osc.frequency.exponentialRampToValueAtTime(720, now + 0.05);

      gain.gain.setValueAtTime(0.1, now);
      gain.gain.exponentialRampToValueAtTime(0.001, now + 0.05);

      osc.connect(gain);
      gain.connect(this.ctx.destination);
      osc.start(now);
      osc.stop(now + 0.05);
    },

    playKeyError() {
      if (!this.enabled || !this.ctx) return;
      const osc = this.ctx.createOscillator();
      const gain = this.ctx.createGain();
      const now = this.ctx.currentTime;

      osc.type = 'triangle';
      osc.frequency.setValueAtTime(150, now);
      osc.frequency.setValueAtTime(110, now + 0.06);

      gain.gain.setValueAtTime(0.2, now);
      gain.gain.exponentialRampToValueAtTime(0.001, now + 0.12);

      osc.connect(gain);
      gain.connect(this.ctx.destination);
      osc.start(now);
      osc.stop(now + 0.12);
    },

    playShieldHit() {
      if (!this.enabled || !this.ctx) return;
      const osc = this.ctx.createOscillator();
      const gain = this.ctx.createGain();
      const now = this.ctx.currentTime;

      osc.type = 'sawtooth';
      osc.frequency.setValueAtTime(220, now);
      osc.frequency.exponentialRampToValueAtTime(50, now + 0.3);

      gain.gain.setValueAtTime(0.35, now);
      gain.gain.exponentialRampToValueAtTime(0.001, now + 0.3);

      osc.connect(gain);
      gain.connect(this.ctx.destination);
      osc.start(now);
      osc.stop(now + 0.3);
    },

    playWaveClear() {
      if (!this.enabled || !this.ctx) return;
      const notes = [440, 554.37, 659.25, 880];
      notes.forEach((freq, idx) => {
        const osc = this.ctx.createOscillator();
        const gain = this.ctx.createGain();
        const noteTime = this.ctx.currentTime + idx * 0.09;

        osc.type = 'triangle';
        osc.frequency.setValueAtTime(freq, noteTime);

        gain.gain.setValueAtTime(0.18, noteTime);
        gain.gain.exponentialRampToValueAtTime(0.001, noteTime + 0.16);

        osc.connect(gain);
        gain.connect(this.ctx.destination);
        osc.start(noteTime);
        osc.stop(noteTime + 0.16);
      });
    }
  };

  // --- Word Lists ---
  const WORD_BANKS = {
    vocab: [
      "planet", "galaxy", "orbit", "rocket", "meteor", "comet", "shield", "laser",
      "radar", "sensor", "energy", "nebula", "beacon", "vector", "signal", "target",
      "flight", "system", "engine", "photon", "pulsar", "cosmos", "voyage", "thrust",
      "plasma", "capsule", "shuttle", "station", "patrol", "sector", "quantum", "gravity"
    ],
    short: [
      "sky", "star", "sun", "moon", "beam", "ship", "base", "core", "fire",
      "fast", "lock", "zoom", "dash", "glow", "grid", "hull", "jump", "apex",
      "nova", "zone", "zero", "warp", "flux", "dock", "axis", "code", "helm"
    ],
    tech: [
      "binary", "syntax", "array", "router", "matrix", "kernel", "server", "socket",
      "packet", "buffer", "crypto", "lambda", "memory", "thread", "daemon", "opcode",
      "client", "cipher", "script", "module", "string", "struct", "branch", "commit"
    ]
  };

  // --- Game State Variables ---
  const canvas = document.getElementById('spaceCanvas');
  const ctx = canvas.getContext('2d');

  let animationId = null;
  let isRunning = false;
  let isPaused = false;

  let currentWordSet = 'vocab';
  let currentDiff = 'easy'; // easy, medium, hard

  let score = 0;
  let streak = 0;
  let highestStreak = 0;
  let totalKeystrokes = 0;
  let correctKeystrokes = 0;
  let shieldHp = 100;
  let wave = 1;
  let wordsKilledInWave = 0;
  const WORDS_PER_WAVE = 8;

  // Entities
  let stars = [];
  let enemies = [];
  let lasers = [];
  let particles = [];
  let activeTarget = null; // Locked enemy
  let spawnTimer = 0;
  let spawnInterval = 140; // frames between spawns

  // Base turret position
  const turret = {
    x: 0,
    y: 0,
    angle: -Math.PI / 2
  };

  // --- DOM Elements ---
  const hudScore = document.getElementById('hudScore');
  const hudWave = document.getElementById('hudWave');
  const hudStreak = document.getElementById('hudStreak');
  const hudMultiplier = document.getElementById('hudMultiplier');
  const hudAccuracy = document.getElementById('hudAccuracy');
  const hudShieldFill = document.getElementById('hudShieldFill');
  const hudShieldText = document.getElementById('hudShieldText');

  const startOverlay = document.getElementById('startOverlay');
  const pauseOverlay = document.getElementById('pauseOverlay');
  const gameOverOverlay = document.getElementById('gameOverOverlay');

  const finalScoreEl = document.getElementById('finalScore');
  const finalWaveEl = document.getElementById('finalWave');
  const finalStreakEl = document.getElementById('finalStreak');
  const finalAccuracyEl = document.getElementById('finalAccuracy');
  const saveScoreFeedback = document.getElementById('saveScoreFeedback');

  // Resize canvas smoothly for responsive display
  function resizeCanvas() {
    const rect = canvas.getBoundingClientRect();
    canvas.width = rect.width * (window.devicePixelRatio || 1);
    canvas.height = 580 * (window.devicePixelRatio || 1);
    ctx.scale(window.devicePixelRatio || 1, window.devicePixelRatio || 1);
    turret.x = rect.width / 2;
    turret.y = 550;
  }
  window.addEventListener('resize', resizeCanvas);
  resizeCanvas();

  // --- Starfield Initialization ---
  function initStars() {
    stars = [];
    const count = 100;
    const w = canvas.getBoundingClientRect().width;
    const h = 580;
    for (let i = 0; i < count; i++) {
      stars.push({
        x: Math.random() * w,
        y: Math.random() * h,
        radius: Math.random() * 1.5 + 0.5,
        speed: Math.random() * 0.6 + 0.2,
        alpha: Math.random() * 0.7 + 0.3
      });
    }
  }

  // --- Enemy Spawn Generator ---
  function getSpeedMultiplier() {
    let mult = 1.0;
    if (currentDiff === 'easy') mult = 0.85;
    if (currentDiff === 'medium') mult = 1.25;
    if (currentDiff === 'hard') mult = 1.65;
    return mult + (wave - 1) * 0.12;
  }

  function spawnEnemy() {
    const list = WORD_BANKS[currentWordSet] || WORD_BANKS.vocab;
    const word = list[Math.floor(Math.random() * list.length)];

    const rect = canvas.getBoundingClientRect();
    const margin = 80;
    const x = margin + Math.random() * (rect.width - margin * 2);
    const speed = (0.35 + Math.random() * 0.25) * getSpeedMultiplier();

    // Design enemy ship
    const enemy = {
      x: x,
      y: -30,
      word: word,
      typedLen: 0,
      speed: speed,
      width: Math.max(70, word.length * 12 + 20),
      height: 38,
      color: '#38bdf8',
      type: Math.floor(Math.random() * 3), // 0: Cruiser, 1: Fighter, 2: Interceptor
      hp: word.length
    };

    enemies.push(enemy);
  }

  // --- Particles & FX ---
  function createExplosion(x, y, color = '#38bdf8') {
    const count = 28;
    for (let i = 0; i < count; i++) {
      const angle = Math.random() * Math.PI * 2;
      const speed = Math.random() * 4 + 1.5;
      particles.push({
        x: x,
        y: y,
        vx: Math.cos(angle) * speed,
        vy: Math.sin(angle) * speed,
        radius: Math.random() * 3.5 + 1.5,
        color: i % 2 === 0 ? color : '#f0883e',
        alpha: 1.0,
        decay: Math.random() * 0.025 + 0.02
      });
    }
  }

  function fireLaser(target) {
    turret.angle = Math.atan2(target.y - turret.y, target.x - turret.x);
    lasers.push({
      startX: turret.x,
      startY: turret.y,
      targetX: target.x,
      targetY: target.y,
      alpha: 1.0
    });
    AudioEngine.playLaser();
  }

  // --- Game Loop ---
  function gameLoop() {
    if (!isRunning || isPaused) return;

    update();
    draw();

    animationId = requestAnimationFrame(gameLoop);
  }

  function update() {
    const rect = canvas.getBoundingClientRect();

    // Move stars
    stars.forEach(s => {
      s.y += s.speed;
      if (s.y > 580) {
        s.y = 0;
        s.x = Math.random() * rect.width;
      }
    });

    // Enemy Spawning
    spawnTimer++;
    if (spawnTimer >= spawnInterval) {
      spawnTimer = 0;
      spawnEnemy();
    }

    // Move Enemies
    for (let i = enemies.length - 1; i >= 0; i--) {
      const e = enemies[i];
      e.y += e.speed;

      // Check Shield Collision at bottom (around y = 510)
      if (e.y >= 510) {
        // Impact!
        AudioEngine.playShieldHit();
        createExplosion(e.x, e.y, '#f85149');
        shieldHp = Math.max(0, shieldHp - 20);
        streak = 0;
        updateHUD();

        if (activeTarget === e) {
          activeTarget = null;
        }
        enemies.splice(i, 1);

        if (shieldHp <= 0) {
          triggerGameOver('Enemy warship breached your planetary perimeter defense!');
          return;
        }
      }
    }

    // Update Lasers
    for (let i = lasers.length - 1; i >= 0; i--) {
      lasers[i].alpha -= 0.15;
      if (lasers[i].alpha <= 0) {
        lasers.splice(i, 1);
      }
    }

    // Update Particles
    for (let i = particles.length - 1; i >= 0; i--) {
      const p = particles[i];
      p.x += p.vx;
      p.y += p.vy;
      p.alpha -= p.decay;
      if (p.alpha <= 0) {
        particles.splice(i, 1);
      }
    }
  }

  function draw() {
    const rect = canvas.getBoundingClientRect();
    const w = rect.width;
    const h = 580;

    // Clear background
    ctx.clearRect(0, 0, w, h);

    // Deep space gradient
    const bgGrad = ctx.createLinearGradient(0, 0, 0, h);
    bgGrad.addColorStop(0, '#040711');
    bgGrad.addColorStop(1, '#09101f');
    ctx.fillStyle = bgGrad;
    ctx.fillRect(0, 0, w, h);

    // Draw Parallax Stars
    stars.forEach(s => {
      ctx.fillStyle = `rgba(255, 255, 255, ${s.alpha})`;
      ctx.beginPath();
      ctx.arc(s.x, s.y, s.radius, 0, Math.PI * 2);
      ctx.fill();
    });

    // Draw Planetary Shield Line (glow)
    ctx.save();
    const shieldGrad = ctx.createLinearGradient(0, 510, 0, 550);
    shieldGrad.addColorStop(0, 'rgba(56, 189, 248, 0.4)');
    shieldGrad.addColorStop(1, 'rgba(56, 189, 248, 0.0)');
    ctx.fillStyle = shieldGrad;
    ctx.fillRect(0, 510, w, 40);

    ctx.strokeStyle = '#38bdf8';
    ctx.lineWidth = 2;
    ctx.setLineDash([8, 6]);
    ctx.beginPath();
    ctx.moveTo(0, 510);
    ctx.lineTo(w, 510);
    ctx.stroke();
    ctx.setLineDash([]);
    ctx.restore();

    // Draw Lasers
    lasers.forEach(l => {
      ctx.save();
      ctx.strokeStyle = `rgba(56, 189, 248, ${l.alpha})`;
      ctx.lineWidth = 4;
      ctx.shadowColor = '#38bdf8';
      ctx.shadowBlur = 15;
      ctx.beginPath();
      ctx.moveTo(l.startX, l.startY);
      ctx.lineTo(l.targetX, l.targetY);
      ctx.stroke();

      // Core beam
      ctx.strokeStyle = `rgba(255, 255, 255, ${l.alpha})`;
      ctx.lineWidth = 1.5;
      ctx.stroke();
      ctx.restore();
    });

    // Draw Turret at Base
    ctx.save();
    ctx.translate(turret.x, turret.y);
    ctx.rotate(turret.angle);

    // Turret barrel
    ctx.fillStyle = '#58a6ff';
    ctx.fillRect(0, -5, 32, 10);
    ctx.fillStyle = '#ffffff';
    ctx.fillRect(26, -3, 8, 6);

    ctx.rotate(-turret.angle); // back to center

    // Turret Dome Base
    ctx.beginPath();
    ctx.arc(0, 0, 24, Math.PI, 0, false);
    ctx.fillStyle = '#1f2937';
    ctx.fill();
    ctx.lineWidth = 3;
    ctx.strokeStyle = '#38bdf8';
    ctx.stroke();

    ctx.beginPath();
    ctx.arc(0, 0, 10, 0, Math.PI * 2);
    ctx.fillStyle = '#38bdf8';
    ctx.fill();
    ctx.restore();

    // Draw Enemies
    enemies.forEach(e => {
      drawEnemyShip(e);
    });

    // Draw Particles
    particles.forEach(p => {
      ctx.save();
      ctx.fillStyle = p.color;
      ctx.globalAlpha = Math.max(0, p.alpha);
      ctx.beginPath();
      ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
      ctx.fill();
      ctx.restore();
    });
  }

  function drawEnemyShip(e) {
    ctx.save();
    ctx.translate(e.x, e.y);

    const isLocked = (activeTarget === e);

    // Locked targeting reticle
    if (isLocked) {
      ctx.strokeStyle = '#f0883e';
      ctx.lineWidth = 1.5;
      ctx.setLineDash([4, 4]);
      ctx.beginPath();
      ctx.arc(0, 0, e.width / 2 + 8, 0, Math.PI * 2);
      ctx.stroke();
      ctx.setLineDash([]);
    }

    // Engine thruster flame
    ctx.fillStyle = Math.random() > 0.5 ? '#f0883e' : '#ff4d00';
    ctx.beginPath();
    ctx.moveTo(-6, -14);
    ctx.lineTo(6, -14);
    ctx.lineTo(0, -22 - Math.random() * 8);
    ctx.closePath();
    ctx.fill();

    // Ship Hull Vector Art
    ctx.fillStyle = isLocked ? '#1e293b' : '#0f172a';
    ctx.strokeStyle = isLocked ? '#f0883e' : '#38bdf8';
    ctx.lineWidth = 2;

    ctx.beginPath();
    // Delta wing geometry
    ctx.moveTo(0, 16);
    ctx.lineTo(e.width / 2 - 8, -8);
    ctx.lineTo(e.width / 2 - 14, -14);
    ctx.lineTo(0, -10);
    ctx.lineTo(-e.width / 2 + 14, -14);
    ctx.lineTo(-e.width / 2 + 8, -8);
    ctx.closePath();
    ctx.fill();
    ctx.stroke();

    // Cockpit canopy glow
    ctx.fillStyle = isLocked ? '#f0883e' : '#38bdf8';
    ctx.beginPath();
    ctx.ellipse(0, 2, 4, 8, 0, 0, Math.PI * 2);
    ctx.fill();

    // Word Tag Box above ship
    const tagY = -28;
    ctx.font = 'bold 15px "JetBrains Mono", monospace';
    const textWidth = ctx.measureText(e.word).width;
    const tagPad = 12;

    ctx.fillStyle = 'rgba(13, 17, 23, 0.9)';
    ctx.strokeStyle = isLocked ? '#f0883e' : '#30363d';
    ctx.lineWidth = 1;
    ctx.beginPath();
    ctx.roundRect(-textWidth / 2 - tagPad / 2, tagY - 16, textWidth + tagPad, 22, 6);
    ctx.fill();
    ctx.stroke();

    // Render Word Letters (Typed in bright green/cyan, Untyped in white)
    let curX = -textWidth / 2;
    for (let i = 0; i < e.word.length; i++) {
      const char = e.word[i];
      const charWidth = ctx.measureText(char).width;

      if (i < e.typedLen) {
        ctx.fillStyle = '#3fb950'; // typed letter
      } else {
        ctx.fillStyle = isLocked ? '#ffffff' : '#c9d1d9';
      }
      ctx.fillText(char, curX, tagY);
      curX += charWidth;
    }

    ctx.restore();
  }

  // --- Keyboard Input Handling ---
  window.addEventListener('keydown', (e) => {
    if (!isRunning) return;

    if (e.key === 'Escape') {
      e.preventDefault();
      togglePause();
      return;
    }

    if (isPaused) return;

    // Check letter input (a-z)
    if (e.key.length === 1 && !e.ctrlKey && !e.altKey && !e.metaKey) {
      handleKeyInput(e.key.toLowerCase());
    }
  });

  function handleKeyInput(char) {
    totalKeystrokes++;

    // 1. If we have an active locked target
    if (activeTarget) {
      const expectedChar = activeTarget.word[activeTarget.typedLen].toLowerCase();
      if (char === expectedChar) {
        correctKeystrokes++;
        activeTarget.typedLen++;
        streak++;
        if (streak > highestStreak) highestStreak = streak;
        AudioEngine.playKeyCorrect();

        // Check if word completed!
        if (activeTarget.typedLen >= activeTarget.word.length) {
          destroyEnemy(activeTarget);
          activeTarget = null;
        }
        updateHUD();
        return;
      } else {
        // Missed letter on locked target
        streak = 0;
        AudioEngine.playKeyError();
        updateHUD();
        return;
      }
    }

    // 2. No locked target -> Search for enemy starting with this character
    // Prioritize enemies closest to shield (highest y value)
    const sorted = [...enemies].sort((a, b) => b.y - a.y);
    const candidate = sorted.find(enemy => enemy.word[0].toLowerCase() === char);

    if (candidate) {
      correctKeystrokes++;
      activeTarget = candidate;
      activeTarget.typedLen = 1;
      streak++;
      if (streak > highestStreak) highestStreak = streak;
      AudioEngine.playKeyCorrect();

      // Single letter words (if any)
      if (activeTarget.typedLen >= activeTarget.word.length) {
        destroyEnemy(activeTarget);
        activeTarget = null;
      }
      updateHUD();
    } else {
      // Missed keystroke
      streak = 0;
      AudioEngine.playKeyError();
      updateHUD();
    }
  }

  function destroyEnemy(enemy) {
    fireLaser(enemy);
    createExplosion(enemy.x, enemy.y, '#38bdf8');
    AudioEngine.playExplosion();

    // Calculate score
    const mult = getMultiplier();
    const wordScore = (enemy.word.length * 15 + wave * 10) * mult;
    score += wordScore;

    // Remove from array
    const idx = enemies.indexOf(enemy);
    if (idx !== -1) {
      enemies.splice(idx, 1);
    }

    wordsKilledInWave++;
    if (wordsKilledInWave >= WORDS_PER_WAVE) {
      nextWave();
    }
  }

  function nextWave() {
    wave++;
    wordsKilledInWave = 0;
    // Reward player by repairing 15% shield
    shieldHp = Math.min(100, shieldHp + 15);
    AudioEngine.playWaveClear();

    // Slightly increase spawn pace
    spawnInterval = Math.max(70, 140 - wave * 10);
    updateHUD();
  }

  function getMultiplier() {
    if (streak >= 30) return 4;
    if (streak >= 20) return 3;
    if (streak >= 10) return 2;
    return 1;
  }

  function updateHUD() {
    hudScore.textContent = score.toLocaleString();
    hudWave.textContent = wave;
    hudStreak.textContent = streak;

    const mult = getMultiplier();
    hudMultiplier.textContent = `${mult}x`;
    if (mult > 1) {
      hudMultiplier.className = 'badge bg-warning text-dark px-1.5 py-0.5';
    } else {
      hudMultiplier.className = 'badge bg-secondary text-light px-1.5 py-0.5';
    }

    const acc = totalKeystrokes > 0 ? ((correctKeystrokes / totalKeystrokes) * 100).toFixed(0) : '100';
    hudAccuracy.textContent = `${acc}%`;

    hudShieldFill.style.width = `${shieldHp}%`;
    hudShieldText.textContent = `${shieldHp}%`;
    if (shieldHp <= 30) {
      hudShieldText.style.color = '#f85149';
    } else if (shieldHp <= 60) {
      hudShieldText.style.color = '#e3b341';
    } else {
      hudShieldText.style.color = '#38bdf8';
    }
  }

  // --- Game Lifecycle Functions ---
  function startGame() {
    AudioEngine.init();

    score = 0;
    streak = 0;
    highestStreak = 0;
    totalKeystrokes = 0;
    correctKeystrokes = 0;
    shieldHp = 100;
    wave = 1;
    wordsKilledInWave = 0;
    spawnTimer = 0;
    spawnInterval = currentDiff === 'easy' ? 150 : (currentDiff === 'medium' ? 120 : 95);

    enemies = [];
    lasers = [];
    particles = [];
    activeTarget = null;

    initStars();
    updateHUD();

    startOverlay.classList.add('d-none');
    pauseOverlay.classList.add('d-none');
    gameOverOverlay.classList.add('d-none');

    isRunning = true;
    isPaused = false;

    if (animationId) cancelAnimationFrame(animationId);
    gameLoop();
  }

  function togglePause() {
    if (!isRunning) return;
    isPaused = !isPaused;

    if (isPaused) {
      pauseOverlay.classList.remove('d-none');
    } else {
      pauseOverlay.classList.add('d-none');
      gameLoop();
    }
  }

  function triggerGameOver(reason = 'Planetary defense breached!') {
    isRunning = false;
    if (animationId) cancelAnimationFrame(animationId);

    const acc = totalKeystrokes > 0 ? ((correctKeystrokes / totalKeystrokes) * 100).toFixed(1) : '100.0';

    finalScoreEl.textContent = score.toLocaleString();
    finalWaveEl.textContent = wave;
    finalStreakEl.textContent = highestStreak;
    finalAccuracyEl.textContent = `${acc}%`;
    document.getElementById('gameOverReason').textContent = reason;

    gameOverOverlay.classList.remove('d-none');

    // Save Score to API
    saveScore(score, highestStreak, parseFloat(acc), currentDiff, currentWordSet);
  }

  function saveScore(scoreVal, streakVal, accVal, diffVal, modeVal) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    fetch('<?= BASE_URL ?>/api/save-game-score.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': csrfToken
      },
      body: JSON.stringify({
        game_slug: 'space',
        score: scoreVal,
        highest_streak: streakVal,
        accuracy: accVal,
        difficulty: diffVal,
        mode: modeVal
      })
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'success') {
        saveScoreFeedback.classList.remove('d-none');
        if (data.new_high_score) {
          saveScoreFeedback.innerHTML = `<i class="bi bi-trophy-fill text-warning me-1"></i> <strong>New Personal Best!</strong> Score: ${scoreVal.toLocaleString()}`;
          const topScoreDisplay = document.getElementById('topScoreDisplay');
          if (topScoreDisplay) topScoreDisplay.textContent = scoreVal.toLocaleString();
        }
      }
    })
    .catch(err => {
      console.warn('Score save notice:', err);
    });
  }

  // --- Button & Control Listeners ---
  document.getElementById('btnStartGame').addEventListener('click', startGame);
  document.getElementById('btnPlayAgain').addEventListener('click', startGame);
  document.getElementById('btnRestartGame').addEventListener('click', startGame);
  document.getElementById('btnRestartFromPause').addEventListener('click', startGame);
  document.getElementById('btnResumeGame').addEventListener('click', togglePause);

  // Sound toggle button
  const btnToggleSound = document.getElementById('btnToggleSound');
  const soundIcon = document.getElementById('soundIcon');
  btnToggleSound.addEventListener('click', () => {
    AudioEngine.init();
    const enabled = AudioEngine.toggle();
    if (enabled) {
      soundIcon.className = 'bi bi-volume-up-fill';
    } else {
      soundIcon.className = 'bi bi-volume-mute-fill';
    }
  });

  // Wordset Buttons
  document.querySelectorAll('[data-wordset]').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('[data-wordset]').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      currentWordSet = btn.getAttribute('data-wordset');
    });
  });

  // Difficulty Buttons
  document.querySelectorAll('[data-diff]').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('[data-diff]').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      currentDiff = btn.getAttribute('data-diff');
    });
  });

  // Initialize stars on canvas load
  initStars();
  draw();
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
