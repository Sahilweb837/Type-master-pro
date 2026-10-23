<?php
/**
 * Custom Type Master Pro
 * Cyber Grand Prix (Arcade Speed Racer Typing Game)
 * High-speed typing racer with vector graphics, nitro exhaust flames, and rival overtakes
 */

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/games_data.php';

$currentUser = current_user();
$userId = $currentUser ? (int)$currentUser['id'] : 0;
$highScore = get_user_game_high_score($userId, 'racer');

$pageTitle = 'Cyber Grand Prix — Arcade Typing Racer';
$pageBadgeTitle = 'Speed Racer';
$pageIcon = 'bi-speedometer2';

include __DIR__ . '/includes/header.php';
?>

<style>
/* Cyber Grand Prix Racer Styles */
.racer-container {
  max-width: 1100px;
  margin: 0 auto;
}

.racer-hud-bar {
  background: #0d0f17;
  border: 1px solid #1f2438;
  border-radius: 14px 14px 0 0;
  padding: 12px 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px;
  color: #e2e8f0;
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
  color: #94a3b8;
  letter-spacing: 0.08em;
}

.hud-stat-val {
  font-size: 1.15rem;
  font-family: 'JetBrains Mono', monospace;
  font-weight: 800;
  color: #f59e0b;
}

.hud-stat-val.mph {
  color: #38bdf8;
}

.hud-stat-val.pos {
  color: #ec4899;
}

.canvas-wrapper {
  position: relative;
  width: 100%;
  background: #080914;
  border-left: 1px solid #1f2438;
  border-right: 1px solid #1f2438;
  overflow: hidden;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.75);
}

#racerCanvas {
  display: block;
  width: 100%;
  height: 580px;
}

.game-control-strip {
  background: #0d0f17;
  border: 1px solid #1f2438;
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
.racer-overlay {
  position: absolute;
  inset: 0;
  background: rgba(8, 9, 20, 0.94);
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

.racer-overlay.d-none {
  display: none !important;
}

.overlay-title {
  font-size: 2.5rem;
  font-weight: 900;
  letter-spacing: -0.03em;
  background: linear-gradient(135deg, #f59e0b 0%, #ec4899 50%, #38bdf8 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  margin-bottom: 8px;
}

.overlay-desc {
  color: #94a3b8;
  max-width: 560px;
  font-size: 0.95rem;
  line-height: 1.6;
  margin-bottom: 24px;
}

.mode-pill-btn {
  background: #151828;
  border: 1px solid #28304d;
  color: #cbd5e1;
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s ease;
}

.mode-pill-btn:hover {
  border-color: #f59e0b;
  color: #f59e0b;
}

.mode-pill-btn.active {
  background: #f59e0b;
  border-color: #f59e0b;
  color: #000000;
  font-weight: 800;
}

.btn-arcade-start {
  background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
  color: #000000;
  border: none;
  font-weight: 800;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  padding: 12px 34px;
  border-radius: 10px;
  font-size: 1rem;
  box-shadow: 0 4px 18px rgba(245, 158, 11, 0.4);
  transition: all 0.2s ease;
}

.btn-arcade-start:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 24px rgba(245, 158, 11, 0.6);
  color: #000000;
}

.nitro-bar-wrap {
  width: 140px;
  height: 10px;
  background: #1e2438;
  border-radius: 6px;
  overflow: hidden;
  border: 1px solid #2d3755;
}

.nitro-bar-fill {
  height: 100%;
  width: 0%;
  background: linear-gradient(90deg, #ec4899 0%, #38bdf8 100%);
  box-shadow: 0 0 10px #38bdf8;
  transition: width 0.2s ease;
}

.stat-metric-card {
  background: #151828;
  border: 1px solid #28304d;
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
  color: #94a3b8;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}
</style>

<div class="racer-container py-3">

  <!-- Breadcrumb & Controls -->
  <div class="d-flex align-items-center justify-content-between mb-3">
    <div class="d-flex align-items-center gap-2">
      <a href="<?= BASE_URL ?>/games.php" class="btn btn-sm btn-control d-inline-flex align-items-center gap-1">
        <i class="bi bi-arrow-left"></i>
        <span>Arcade Hub</span>
      </a>
      <span class="badge" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3);">
        <i class="bi bi-speedometer2 me-1"></i> Cyber Grand Prix
      </span>
    </div>

    <div class="d-flex align-items-center gap-3">
      <div class="small text-muted d-none d-md-block">
        Personal Record: <span class="fw-bold text-warning" id="topScoreDisplay"><?= number_format($highScore['score']) ?></span> pts
      </div>
      <button class="btn btn-sm btn-control" id="btnToggleSound" title="Toggle Sound FX">
        <i class="bi bi-volume-up-fill" id="soundIcon"></i>
      </button>
    </div>
  </div>

  <!-- Game HUD Bar -->
  <div class="racer-hud-bar">
    <div class="d-flex align-items-center gap-4 flex-wrap">
      <div class="hud-stat-box">
        <span class="hud-stat-label">Position</span>
        <span class="hud-stat-val pos" id="hudPosition">P25</span>
      </div>

      <div class="hud-stat-box">
        <span class="hud-stat-label">Speed</span>
        <span class="hud-stat-val mph" id="hudSpeed">60 MPH</span>
      </div>

      <div class="hud-stat-box">
        <span class="hud-stat-label">Score</span>
        <span class="hud-stat-val" id="hudScore">0</span>
      </div>
    </div>

    <div class="d-flex align-items-center gap-4 flex-wrap">
      <div class="hud-stat-box">
        <span class="hud-stat-label">Streak</span>
        <span class="hud-stat-val text-warning" id="hudStreak">0</span>
        <span class="badge bg-warning text-dark px-1.5 py-0.5" id="hudMultiplier" style="font-size: 0.65rem; font-weight: 800;">1x</span>
      </div>

      <div class="hud-stat-box">
        <span class="hud-stat-label">Nitro Boost</span>
        <div class="nitro-bar-wrap">
          <div class="nitro-bar-fill" id="hudNitroFill"></div>
        </div>
        <span class="hud-stat-val" id="hudNitroText" style="font-size: 0.85rem; color: #38bdf8;">0%</span>
      </div>
    </div>
  </div>

  <!-- Canvas Stage & Overlays -->
  <div class="canvas-wrapper">
    <canvas id="racerCanvas" width="1000" height="580"></canvas>

    <!-- Start Overlay -->
    <div class="racer-overlay" id="startOverlay">
      <div class="badge px-3 py-1 mb-2 rounded-pill" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3); font-size: 0.75rem;">
        ★ HIGHWAY NITRO GRAND PRIX ★
      </div>
      <h1 class="overlay-title">Cyber Grand Prix</h1>
      <p class="overlay-desc">
        Start at the back of the grid (P25). Type the neon words hovering over rival supercars to accelerate your engine, ignite twin nitro exhaust flames, and overtake your way to 1st place!
      </p>

      <!-- Word Bank Selection -->
      <div class="d-flex align-items-center gap-2 mb-3 flex-wrap justify-content-center">
        <span class="text-secondary small fw-bold me-1">Word Category:</span>
        <button type="button" class="mode-pill-btn active" data-wordset="racing">Racing &amp; Velocity</button>
        <button type="button" class="mode-pill-btn" data-wordset="vocab">Core Vocabulary</button>
        <button type="button" class="mode-pill-btn" data-wordset="code">Tech &amp; Code Syntax</button>
      </div>

      <!-- Difficulty Selection -->
      <div class="d-flex align-items-center gap-2 mb-4 flex-wrap justify-content-center">
        <span class="text-secondary small fw-bold me-1">Class:</span>
        <button type="button" class="mode-pill-btn active" data-diff="easy">Amateur (Gentle)</button>
        <button type="button" class="mode-pill-btn" data-diff="medium">Pro (Speedway)</button>
        <button type="button" class="mode-pill-btn" data-diff="hard">Formula-1 (Extreme)</button>
      </div>

      <button type="button" class="btn-arcade-start d-flex align-items-center gap-2" id="btnStartGame">
        <i class="bi bi-flag-fill me-1"></i>
        <span>Start Engine &amp; Race</span>
      </button>

      <div class="mt-4 text-muted small">
        <i class="bi bi-keyboard me-1"></i> Type the words as rivals approach. Press <kbd style="background: #1e2438; color: #fff; padding: 2px 6px; border-radius: 4px;">ESC</kbd> to pause.
      </div>
    </div>

    <!-- Pause Overlay -->
    <div class="racer-overlay d-none" id="pauseOverlay">
      <h2 class="overlay-title">Race Paused</h2>
      <p class="overlay-desc">In the pit lane. Catch your breath and hit resume when ready.</p>
      <div class="d-flex gap-3">
        <button type="button" class="btn-arcade-start" id="btnResumeGame">Resume Race</button>
        <button type="button" class="btn btn-outline-secondary px-4 py-2" id="btnRestartFromPause">Restart</button>
      </div>
    </div>

    <!-- Game Over / Victory Overlay -->
    <div class="racer-overlay d-none" id="gameOverOverlay">
      <div class="badge bg-warning text-dark px-3 py-1 mb-2 rounded-pill fw-bold" id="podiumBadge">CHECKERED FLAG!</div>
      <h2 class="overlay-title" id="finishTitle">Grand Prix Complete!</h2>
      <p class="overlay-desc" id="finishSubtitle">You conquered the neon highway with blistering typing velocity.</p>

      <div class="d-flex gap-3 mb-4 flex-wrap justify-content-center">
        <div class="stat-metric-card">
          <div class="val text-warning" id="finalScore">0</div>
          <div class="lbl">Final Score</div>
        </div>
        <div class="stat-metric-card">
          <div class="val text-info" id="finalPosition">P1</div>
          <div class="lbl">Final Position</div>
        </div>
        <div class="stat-metric-card">
          <div class="val text-success" id="finalTopSpeed">165 MPH</div>
          <div class="lbl">Top Speed</div>
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
          <i class="bi bi-arrow-repeat me-1"></i> Race Again
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
        <i class="bi bi-slash-circle-fill me-1" style="color: #ff4d00;"></i> Keyboard Ninja
      </a>
      <a href="<?= BASE_URL ?>/game-space.php" class="btn btn-sm btn-control">
        <i class="bi bi-rocket-takeoff-fill me-1" style="color: #38bdf8;"></i> Space Defender
      </a>
      <button type="button" class="btn btn-sm btn-control" id="btnRestartGame">
        <i class="bi bi-arrow-clockwise me-1"></i> Restart
      </button>
    </div>
  </div>

</div>

<!-- Synthetic Web Audio SFX & Cyber Grand Prix Engine -->
<script>
(function() {
  // --- Web Audio Synthesizer Engine ---
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

    playEngineRev() {
      if (!this.enabled || !this.ctx) return;
      const osc = this.ctx.createOscillator();
      const gain = this.ctx.createGain();
      const now = this.ctx.currentTime;

      osc.type = 'sawtooth';
      osc.frequency.setValueAtTime(110, now);
      osc.frequency.exponentialRampToValueAtTime(280, now + 0.15);

      gain.gain.setValueAtTime(0.12, now);
      gain.gain.exponentialRampToValueAtTime(0.001, now + 0.15);

      osc.connect(gain);
      gain.connect(this.ctx.destination);
      osc.start(now);
      osc.stop(now + 0.15);
    },

    playNitroWhoosh() {
      if (!this.enabled || !this.ctx) return;
      const now = this.ctx.currentTime;
      const bufferSize = this.ctx.sampleRate * 0.25;
      const buffer = this.ctx.createBuffer(1, bufferSize, this.ctx.sampleRate);
      const output = buffer.getChannelData(0);
      for (let i = 0; i < bufferSize; i++) {
        output[i] = Math.random() * 2 - 1;
      }

      const whiteNoise = this.ctx.createBufferSource();
      whiteNoise.buffer = buffer;

      const filter = this.ctx.createBiquadFilter();
      filter.type = 'bandpass';
      filter.frequency.setValueAtTime(1200, now);
      filter.frequency.exponentialRampToValueAtTime(400, now + 0.25);

      const gain = this.ctx.createGain();
      gain.gain.setValueAtTime(0.25, now);
      gain.gain.exponentialRampToValueAtTime(0.001, now + 0.25);

      whiteNoise.connect(filter);
      filter.connect(gain);
      gain.connect(this.ctx.destination);
      whiteNoise.start(now);
      whiteNoise.stop(now + 0.25);
    },

    playOvertake() {
      if (!this.enabled || !this.ctx) return;
      const osc = this.ctx.createOscillator();
      const gain = this.ctx.createGain();
      const now = this.ctx.currentTime;

      osc.type = 'sine';
      osc.frequency.setValueAtTime(320, now);
      osc.frequency.exponentialRampToValueAtTime(640, now + 0.12);

      gain.gain.setValueAtTime(0.15, now);
      gain.gain.exponentialRampToValueAtTime(0.001, now + 0.12);

      osc.connect(gain);
      gain.connect(this.ctx.destination);
      osc.start(now);
      osc.stop(now + 0.12);
    },

    playKeyError() {
      if (!this.enabled || !this.ctx) return;
      const osc = this.ctx.createOscillator();
      const gain = this.ctx.createGain();
      const now = this.ctx.currentTime;

      osc.type = 'triangle';
      osc.frequency.setValueAtTime(140, now);
      osc.frequency.setValueAtTime(90, now + 0.08);

      gain.gain.setValueAtTime(0.18, now);
      gain.gain.exponentialRampToValueAtTime(0.001, now + 0.12);

      osc.connect(gain);
      gain.connect(this.ctx.destination);
      osc.start(now);
      osc.stop(now + 0.12);
    },

    playVictory() {
      if (!this.enabled || !this.ctx) return;
      const notes = [523.25, 659.25, 783.99, 1046.50];
      notes.forEach((freq, idx) => {
        const osc = this.ctx.createOscillator();
        const gain = this.ctx.createGain();
        const noteTime = this.ctx.currentTime + idx * 0.1;

        osc.type = 'triangle';
        osc.frequency.setValueAtTime(freq, noteTime);

        gain.gain.setValueAtTime(0.2, noteTime);
        gain.gain.exponentialRampToValueAtTime(0.001, noteTime + 0.2);

        osc.connect(gain);
        gain.connect(this.ctx.destination);
        osc.start(noteTime);
        osc.stop(noteTime + 0.2);
      });
    }
  };

  // --- Word Lists ---
  const WORD_BANKS = {
    racing: [
      "turbo", "speed", "boost", "drift", "drive", "track", "clutch", "piston",
      "gears", "motor", "shift", "brake", "apex", "chassis", "torque", "steer",
      "sprint", "vector", "octane", "intake", "exhaust", "asphalt", "throttle", "circuit"
    ],
    vocab: [
      "power", "flash", "light", "quick", "storm", "spark", "pulse", "flame",
      "hyper", "pilot", "orbit", "sonic", "blaze", "focus", "rider", "glider",
      "energy", "rocket", "engine", "falcon", "comet", "strike", "stride", "stream"
    ],
    code: [
      "async", "await", "const", "yield", "class", "stack", "queue", "cache",
      "proxy", "route", "fetch", "patch", "spawn", "scope", "token", "index",
      "buffer", "socket", "packet", "thread", "stream", "binary", "switch", "return"
    ]
  };

  // --- Game Engine Variables ---
  const canvas = document.getElementById('racerCanvas');
  const ctx = canvas.getContext('2d');

  let animationId = null;
  let isRunning = false;
  let isPaused = false;

  let currentWordSet = 'racing';
  let currentDiff = 'easy';

  let score = 0;
  let streak = 0;
  let highestStreak = 0;
  let totalKeystrokes = 0;
  let correctKeystrokes = 0;

  let speedMph = 60;
  let topSpeedMph = 60;
  let position = 25; // Start at P25, aim for P1
  let nitroPercent = 0;

  // Track & Road Environment
  let roadOffset = 0;
  const LANES = [-140, 0, 140]; // Left, Center, Right lanes relative to center
  let rivalCars = [];
  let nitroFlames = [];
  let sparks = [];
  let activeRival = null; // Currently targeted rival car
  let spawnTimer = 0;

  // DOM Elements
  const hudScore = document.getElementById('hudScore');
  const hudSpeed = document.getElementById('hudSpeed');
  const hudPosition = document.getElementById('hudPosition');
  const hudStreak = document.getElementById('hudStreak');
  const hudMultiplier = document.getElementById('hudMultiplier');
  const hudNitroFill = document.getElementById('hudNitroFill');
  const hudNitroText = document.getElementById('hudNitroText');

  const startOverlay = document.getElementById('startOverlay');
  const pauseOverlay = document.getElementById('pauseOverlay');
  const gameOverOverlay = document.getElementById('gameOverOverlay');

  const finalScoreEl = document.getElementById('finalScore');
  const finalPositionEl = document.getElementById('finalPosition');
  const finalTopSpeedEl = document.getElementById('finalTopSpeed');
  const finalAccuracyEl = document.getElementById('finalAccuracy');
  const saveScoreFeedback = document.getElementById('saveScoreFeedback');

  function resizeCanvas() {
    const rect = canvas.getBoundingClientRect();
    canvas.width = rect.width * (window.devicePixelRatio || 1);
    canvas.height = 580 * (window.devicePixelRatio || 1);
    ctx.scale(window.devicePixelRatio || 1, window.devicePixelRatio || 1);
  }
  window.addEventListener('resize', resizeCanvas);
  resizeCanvas();

  // --- Spawn Rival Cars Ahead ---
  function spawnRival() {
    if (position <= 1) return; // All rivals cleared!

    const list = WORD_BANKS[currentWordSet] || WORD_BANKS.racing;
    const word = list[Math.floor(Math.random() * list.length)];
    const laneIndex = Math.floor(Math.random() * 3);
    const laneX = LANES[laneIndex];

    const car = {
      laneX: laneX,
      x: 0,
      y: 190, // Horizon start
      scale: 0.35,
      word: word,
      typedLen: 0,
      color: ['#ec4899', '#38bdf8', '#a855f7', '#10b981'][Math.floor(Math.random() * 4)],
      speedDelta: 0.8 + Math.random() * 0.4
    };

    rivalCars.push(car);
  }

  // --- Game Loop ---
  function gameLoop() {
    if (!isRunning || isPaused) return;

    update();
    draw();

    animationId = requestAnimationFrame(gameLoop);
  }

  function update() {
    const roadSpeed = (speedMph / 60) * 12;
    roadOffset = (roadOffset + roadSpeed) % 80;

    // Gradual speed decay if not typing
    if (speedMph > 60) {
      speedMph = Math.max(60, speedMph - 0.08);
    }
    if (speedMph > topSpeedMph) {
      topSpeedMph = Math.round(speedMph);
    }

    // Spawn rivals
    spawnTimer++;
    const spawnRate = currentDiff === 'easy' ? 140 : (currentDiff === 'medium' ? 110 : 85);
    if (spawnTimer >= spawnRate && rivalCars.length < 3) {
      spawnTimer = 0;
      spawnRival();
    }

    // Move rival cars toward player
    const rect = canvas.getBoundingClientRect();
    const centerX = rect.width / 2;

    for (let i = rivalCars.length - 1; i >= 0; i--) {
      const c = rivalCars[i];
      c.y += (speedMph / 50) * c.speedDelta * 2.2;

      // Scale perspective based on y position (from horizon y=190 to bottom y=520)
      const progress = Math.max(0, Math.min(1, (c.y - 190) / 330));
      c.scale = 0.35 + progress * 0.85;
      c.x = centerX + c.laneX * c.scale;

      // If rival passes behind player without being overtaken
      if (c.y > 540) {
        if (activeRival === c) activeRival = null;
        rivalCars.splice(i, 1);
        streak = 0;
        updateHUD();
      }
    }

    // Update Nitro Flames
    for (let i = nitroFlames.length - 1; i >= 0; i--) {
      const f = nitroFlames[i];
      f.y += f.vy;
      f.alpha -= 0.08;
      f.radius *= 0.95;
      if (f.alpha <= 0) {
        nitroFlames.splice(i, 1);
      }
    }

    // Update Sparks
    for (let i = sparks.length - 1; i >= 0; i--) {
      const s = sparks[i];
      s.x += s.vx;
      s.y += s.vy;
      s.alpha -= 0.05;
      if (s.alpha <= 0) {
        sparks.splice(i, 1);
      }
    }

    updateHUD();
  }

  function draw() {
    const rect = canvas.getBoundingClientRect();
    const w = rect.width;
    const h = 580;
    const centerX = w / 2;
    const horizonY = 190;

    ctx.clearRect(0, 0, w, h);

    // 1. Synthwave Sunset Sky & Neon Skyline
    const skyGrad = ctx.createLinearGradient(0, 0, 0, horizonY);
    skyGrad.addColorStop(0, '#0a0a1a');
    skyGrad.addColorStop(0.6, '#1a0b2e');
    skyGrad.addColorStop(1, '#ff3366');
    ctx.fillStyle = skyGrad;
    ctx.fillRect(0, 0, w, horizonY);

    // Neon Cyberpunk Grid Sun
    ctx.save();
    ctx.beginPath();
    ctx.arc(centerX, horizonY - 15, 65, Math.PI, 0, false);
    const sunGrad = ctx.createLinearGradient(0, horizonY - 80, 0, horizonY);
    sunGrad.addColorStop(0, '#ffcc00');
    sunGrad.addColorStop(1, '#ff0066');
    ctx.fillStyle = sunGrad;
    ctx.fill();

    // Horizontal sun lines
    ctx.strokeStyle = '#1a0b2e';
    ctx.lineWidth = 3;
    for (let y = horizonY - 50; y < horizonY; y += 8) {
      ctx.beginPath();
      ctx.moveTo(centerX - 70, y);
      ctx.lineTo(centerX + 70, y);
      ctx.stroke();
    }
    ctx.restore();

    // Distant Cityscape Silhouette
    ctx.fillStyle = '#0f0c22';
    for (let x = 0; x < w; x += 36) {
      const bHeight = 25 + Math.sin(x * 0.08) * 18;
      ctx.fillRect(x, horizonY - bHeight, 32, bHeight);
    }

    // 2. Perspective Highway Road
    const roadGrad = ctx.createLinearGradient(0, horizonY, 0, h);
    roadGrad.addColorStop(0, '#101322');
    roadGrad.addColorStop(1, '#050711');
    ctx.fillStyle = roadGrad;

    ctx.beginPath();
    ctx.moveTo(centerX - 60, horizonY);
    ctx.lineTo(centerX + 60, horizonY);
    ctx.lineTo(centerX + 380, h);
    ctx.lineTo(centerX - 380, h);
    ctx.closePath();
    ctx.fill();

    // Road Neon Edges (Glowing Curbs)
    ctx.strokeStyle = '#38bdf8';
    ctx.lineWidth = 4;
    ctx.shadowColor = '#38bdf8';
    ctx.shadowBlur = 12;
    ctx.beginPath();
    ctx.moveTo(centerX - 60, horizonY);
    ctx.lineTo(centerX - 380, h);
    ctx.stroke();

    ctx.beginPath();
    ctx.moveTo(centerX + 60, horizonY);
    ctx.lineTo(centerX + 380, h);
    ctx.stroke();
    ctx.shadowBlur = 0;

    // Animated Dashed Lane Dividers (Left and Right)
    ctx.save();
    ctx.strokeStyle = 'rgba(255, 255, 255, 0.4)';
    ctx.lineWidth = 3;
    ctx.setLineDash([20, 25]);
    ctx.lineDashOffset = -roadOffset;

    // Left lane dash
    ctx.beginPath();
    ctx.moveTo(centerX - 20, horizonY);
    ctx.lineTo(centerX - 130, h);
    ctx.stroke();

    // Right lane dash
    ctx.beginPath();
    ctx.moveTo(centerX + 20, horizonY);
    ctx.lineTo(centerX + 130, h);
    ctx.stroke();
    ctx.restore();

    // 3. Draw Approaching Rival Cars
    // Sort so further away cars draw first
    const sortedRivals = [...rivalCars].sort((a, b) => a.y - b.y);
    sortedRivals.forEach(car => {
      drawRivalCar(car);
    });

    // 4. Draw Player Supercar at bottom center
    drawPlayerCar(centerX, 480);

    // 5. Draw Nitro Exhaust Flames
    nitroFlames.forEach(f => {
      ctx.save();
      ctx.fillStyle = f.color;
      ctx.globalAlpha = Math.max(0, f.alpha);
      ctx.beginPath();
      ctx.arc(f.x, f.y, f.radius, 0, Math.PI * 2);
      ctx.fill();
      ctx.restore();
    });

    // 6. Draw Sparks
    sparks.forEach(s => {
      ctx.save();
      ctx.fillStyle = s.color;
      ctx.globalAlpha = Math.max(0, s.alpha);
      ctx.beginPath();
      ctx.arc(s.x, s.y, 2, 0, Math.PI * 2);
      ctx.fill();
      ctx.restore();
    });
  }

  // --- Vector Art: Rival Cars ---
  function drawRivalCar(c) {
    ctx.save();
    ctx.translate(c.x, c.y);
    ctx.scale(c.scale, c.scale);

    const isLocked = (activeRival === c);

    // Car Body Shadow
    ctx.fillStyle = 'rgba(0, 0, 0, 0.45)';
    ctx.beginPath();
    ctx.ellipse(0, 22, 42, 10, 0, 0, Math.PI * 2);
    ctx.fill();

    // Car Chassis
    ctx.fillStyle = c.color;
    ctx.strokeStyle = isLocked ? '#ffffff' : '#000000';
    ctx.lineWidth = 2;
    ctx.beginPath();
    ctx.roundRect(-36, -18, 72, 36, 8);
    ctx.fill();
    ctx.stroke();

    // Rear Windshield
    ctx.fillStyle = '#0f172a';
    ctx.beginPath();
    ctx.roundRect(-24, -12, 48, 16, 4);
    ctx.fill();

    // Tail lights
    ctx.fillStyle = '#ff2255';
    ctx.fillRect(-32, 8, 14, 6);
    ctx.fillRect(18, 8, 14, 6);

    // Wheels
    ctx.fillStyle = '#0a0a0a';
    ctx.fillRect(-40, -14, 6, 16);
    ctx.fillRect(34, -14, 6, 16);
    ctx.fillRect(-40, 6, 6, 16);
    ctx.fillRect(34, 6, 6, 16);

    // Word Tag Floating Above Car
    const tagY = -34;
    ctx.font = 'bold 16px "JetBrains Mono", monospace';
    const textWidth = ctx.measureText(c.word).width;
    const pad = 12;

    ctx.fillStyle = 'rgba(13, 17, 23, 0.92)';
    ctx.strokeStyle = isLocked ? '#f59e0b' : '#334155';
    ctx.lineWidth = 1.5;
    ctx.beginPath();
    ctx.roundRect(-textWidth / 2 - pad / 2, tagY - 18, textWidth + pad, 24, 6);
    ctx.fill();
    ctx.stroke();

    // Render Letters
    let curX = -textWidth / 2;
    for (let i = 0; i < c.word.length; i++) {
      const char = c.word[i];
      const charWidth = ctx.measureText(char).width;

      if (i < c.typedLen) {
        ctx.fillStyle = '#f59e0b'; // Typed
      } else {
        ctx.fillStyle = isLocked ? '#ffffff' : '#cbd5e1';
      }
      ctx.fillText(char, curX, tagY);
      curX += charWidth;
    }

    ctx.restore();
  }

  // --- Vector Art: Player Futuristic Supercar ---
  function drawPlayerCar(x, y) {
    ctx.save();
    ctx.translate(x, y);

    // Car Shadow
    ctx.fillStyle = 'rgba(0, 0, 0, 0.6)';
    ctx.beginPath();
    ctx.ellipse(0, 32, 60, 16, 0, 0, Math.PI * 2);
    ctx.fill();

    // Main Chassis (Sleek Matte Obsidian with Cyan Accent Lines)
    ctx.fillStyle = '#0f1322';
    ctx.strokeStyle = '#38bdf8';
    ctx.lineWidth = 3;
    ctx.beginPath();
    ctx.roundRect(-50, -28, 100, 56, 12);
    ctx.fill();
    ctx.stroke();

    // Aerodynamic Rear Wing Spoiler
    ctx.fillStyle = '#1e293b';
    ctx.fillRect(-56, 16, 112, 10);

    // Rear Windshield Glass
    ctx.fillStyle = '#0369a1';
    ctx.beginPath();
    ctx.roundRect(-34, -20, 68, 22, 6);
    ctx.fill();

    // Twin High-Intensity Tail Lights
    ctx.fillStyle = '#ff0055';
    ctx.shadowColor = '#ff0055';
    ctx.shadowBlur = 10;
    ctx.fillRect(-44, 18, 22, 8);
    ctx.fillRect(22, 18, 22, 8);
    ctx.shadowBlur = 0;

    // Dual Exhaust Ports
    ctx.fillStyle = '#475569';
    ctx.fillRect(-26, 26, 12, 6);
    ctx.fillRect(14, 26, 12, 6);

    ctx.restore();
  }

  // --- Exhaust & Nitro FX ---
  function createNitroBurst(count = 16) {
    const rect = canvas.getBoundingClientRect();
    const centerX = rect.width / 2;
    const startY = 512;

    for (let i = 0; i < count; i++) {
      // Left exhaust
      nitroFlames.push({
        x: centerX - 20 + (Math.random() * 8 - 4),
        y: startY,
        vy: Math.random() * 5 + 4,
        radius: Math.random() * 6 + 3,
        color: Math.random() > 0.5 ? '#38bdf8' : '#ec4899',
        alpha: 1.0
      });

      // Right exhaust
      nitroFlames.push({
        x: centerX + 20 + (Math.random() * 8 - 4),
        y: startY,
        vy: Math.random() * 5 + 4,
        radius: Math.random() * 6 + 3,
        color: Math.random() > 0.5 ? '#38bdf8' : '#ec4899',
        alpha: 1.0
      });
    }
  }

  function createSparks(count = 12) {
    const rect = canvas.getBoundingClientRect();
    const centerX = rect.width / 2;

    for (let i = 0; i < count; i++) {
      sparks.push({
        x: centerX + (Math.random() * 60 - 30),
        y: 500,
        vx: (Math.random() - 0.5) * 8,
        vy: (Math.random() - 0.5) * 6,
        color: '#f59e0b',
        alpha: 1.0
      });
    }
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

    if (e.key.length === 1 && !e.ctrlKey && !e.altKey && !e.metaKey) {
      handleKeyInput(e.key.toLowerCase());
    }
  });

  function handleKeyInput(char) {
    totalKeystrokes++;

    // 1. If currently locked onto a rival
    if (activeRival) {
      const expectedChar = activeRival.word[activeRival.typedLen].toLowerCase();
      if (char === expectedChar) {
        correctKeystrokes++;
        activeRival.typedLen++;
        streak++;
        if (streak > highestStreak) highestStreak = streak;

        speedMph = Math.min(195, speedMph + 4.5);
        createNitroBurst(6);
        AudioEngine.playEngineRev();

        // Check if word completed -> OVERTAKE!
        if (activeRival.typedLen >= activeRival.word.length) {
          overtakeRival(activeRival);
          activeRival = null;
        }
        updateHUD();
        return;
      } else {
        // Typo
        streak = 0;
        speedMph = Math.max(60, speedMph - 8);
        createSparks(8);
        AudioEngine.playKeyError();
        updateHUD();
        return;
      }
    }

    // 2. No active lock -> Target closest rival starting with this character
    const sorted = [...rivalCars].sort((a, b) => b.y - a.y);
    const candidate = sorted.find(car => car.word[0].toLowerCase() === char);

    if (candidate) {
      correctKeystrokes++;
      activeRival = candidate;
      activeRival.typedLen = 1;
      streak++;
      if (streak > highestStreak) highestStreak = streak;

      speedMph = Math.min(195, speedMph + 4.5);
      createNitroBurst(6);
      AudioEngine.playEngineRev();

      if (activeRival.typedLen >= activeRival.word.length) {
        overtakeRival(activeRival);
        activeRival = null;
      }
      updateHUD();
    } else {
      // Missed key
      streak = 0;
      speedMph = Math.max(60, speedMph - 5);
      createSparks(6);
      AudioEngine.playKeyError();
      updateHUD();
    }
  }

  function overtakeRival(rival) {
    createNitroBurst(20);
    AudioEngine.playNitroWhoosh();
    AudioEngine.playOvertake();

    // Advance position
    if (position > 1) {
      position--;
    }

    // Calculate score
    const mult = streak >= 20 ? 3 : (streak >= 10 ? 2 : 1);
    const overtakePts = (rival.word.length * 20 + Math.round(speedMph)) * mult;
    score += overtakePts;

    // Remove rival
    const idx = rivalCars.indexOf(rival);
    if (idx !== -1) {
      rivalCars.splice(idx, 1);
    }

    // Nitro gauge
    nitroPercent = Math.min(100, nitroPercent + 15);

    // If P1 Reached -> VICTORY!
    if (position <= 1) {
      triggerVictory();
    }
  }

  function updateHUD() {
    hudScore.textContent = score.toLocaleString();
    hudPosition.textContent = `P${position}`;
    hudSpeed.textContent = `${Math.round(speedMph)} MPH`;
    hudStreak.textContent = streak;

    const mult = streak >= 20 ? 3 : (streak >= 10 ? 2 : 1);
    hudMultiplier.textContent = `${mult}x`;

    hudNitroFill.style.width = `${nitroPercent}%`;
    hudNitroText.textContent = `${Math.round(nitroPercent)}%`;
  }

  // --- Game Lifecycle ---
  function startGame() {
    AudioEngine.init();

    score = 0;
    streak = 0;
    highestStreak = 0;
    totalKeystrokes = 0;
    correctKeystrokes = 0;
    speedMph = 60;
    topSpeedMph = 60;
    position = 25;
    nitroPercent = 0;

    rivalCars = [];
    nitroFlames = [];
    sparks = [];
    activeRival = null;
    spawnTimer = 0;

    startOverlay.classList.add('d-none');
    pauseOverlay.classList.add('d-none');
    gameOverOverlay.classList.add('d-none');

    isRunning = true;
    isPaused = false;

    updateHUD();

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

  function triggerVictory() {
    isRunning = false;
    if (animationId) cancelAnimationFrame(animationId);
    AudioEngine.playVictory();

    const acc = totalKeystrokes > 0 ? ((correctKeystrokes / totalKeystrokes) * 100).toFixed(1) : '100.0';

    finalScoreEl.textContent = score.toLocaleString();
    finalPositionEl.textContent = 'P1 (CHAMPION)';
    finalTopSpeedEl.textContent = `${topSpeedMph} MPH`;
    finalAccuracyEl.textContent = `${acc}%`;

    gameOverOverlay.classList.remove('d-none');

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
        game_slug: 'racer',
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

  // Button Listeners
  document.getElementById('btnStartGame').addEventListener('click', startGame);
  document.getElementById('btnPlayAgain').addEventListener('click', startGame);
  document.getElementById('btnRestartGame').addEventListener('click', startGame);
  document.getElementById('btnRestartFromPause').addEventListener('click', startGame);
  document.getElementById('btnResumeGame').addEventListener('click', togglePause);

  // Sound toggle
  const btnToggleSound = document.getElementById('btnToggleSound');
  const soundIcon = document.getElementById('soundIcon');
  btnToggleSound.addEventListener('click', () => {
    AudioEngine.init();
    const enabled = AudioEngine.toggle();
    soundIcon.className = enabled ? 'bi bi-volume-up-fill' : 'bi bi-volume-mute-fill';
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

  draw();
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
