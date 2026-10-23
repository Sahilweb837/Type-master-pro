<?php
/**
 * Custom Type Master Pro
 * Type-a-Balloon (Sky Balloon Pop Typing Game)
 * Inspired by classic Typing.com Type-a-Balloon with confetti physics & vector balloons
 */

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/games_data.php';

$currentUser = current_user();
$userId = $currentUser ? (int)$currentUser['id'] : 0;
$highScore = get_user_game_high_score($userId, 'balloon');

$pageTitle = 'Type-a-Balloon — Sky Pop Arcade';
$pageBadgeTitle = 'Type-a-Balloon';
$pageIcon = 'bi-balloon-fill';

include __DIR__ . '/includes/header.php';
?>

<style>
/* Type-a-Balloon Styles */
.balloon-container {
  max-width: 1100px;
  margin: 0 auto;
}

.balloon-hud-bar {
  background: #0f172a;
  border: 1px solid #1e293b;
  border-radius: 14px 14px 0 0;
  padding: 12px 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px;
  color: #f1f5f9;
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
  color: #ec4899;
}

.hud-stat-val.score {
  color: #10b981;
}

.hud-stat-val.lives {
  color: #ef4444;
}

.canvas-wrapper {
  position: relative;
  width: 100%;
  background: #0c1222;
  border-left: 1px solid #1e293b;
  border-right: 1px solid #1e293b;
  overflow: hidden;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.7);
}

#balloonCanvas {
  display: block;
  width: 100%;
  height: 580px;
  cursor: crosshair;
}

.game-control-strip {
  background: #0f172a;
  border: 1px solid #1e293b;
  border-top: none;
  border-radius: 0 0 14px 14px;
  padding: 12px 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px;
}

/* Modals & Overlays */
.balloon-overlay {
  position: absolute;
  inset: 0;
  background: rgba(12, 18, 34, 0.93);
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

.balloon-overlay.d-none {
  display: none !important;
}

.overlay-title {
  font-size: 2.5rem;
  font-weight: 900;
  letter-spacing: -0.03em;
  background: linear-gradient(135deg, #ec4899 0%, #a855f7 50%, #38bdf8 100%);
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
  background: #1e293b;
  border: 1px solid #334155;
  color: #cbd5e1;
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s ease;
}

.mode-pill-btn:hover {
  border-color: #ec4899;
  color: #ec4899;
}

.mode-pill-btn.active {
  background: #ec4899;
  border-color: #ec4899;
  color: #ffffff;
  font-weight: 700;
}

.btn-arcade-start {
  background: linear-gradient(135deg, #ec4899 0%, #d946ef 100%);
  color: #ffffff;
  border: none;
  font-weight: 800;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  padding: 12px 34px;
  border-radius: 10px;
  font-size: 1rem;
  box-shadow: 0 4px 18px rgba(236, 72, 153, 0.4);
  transition: all 0.2s ease;
}

.btn-arcade-start:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 24px rgba(236, 72, 153, 0.6);
  color: #ffffff;
}

.stat-metric-card {
  background: #1e293b;
  border: 1px solid #334155;
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

<div class="balloon-container py-3">

  <!-- Header & Back Navigation -->
  <div class="d-flex align-items-center justify-content-between mb-3">
    <div class="d-flex align-items-center gap-2">
      <a href="<?= BASE_URL ?>/games.php" class="btn btn-sm btn-control d-inline-flex align-items-center gap-1">
        <i class="bi bi-arrow-left"></i>
        <span>Arcade Hub</span>
      </a>
      <span class="badge" style="background: rgba(236, 72, 153, 0.15); color: #ec4899; border: 1px solid rgba(236, 72, 153, 0.3);">
        <i class="bi bi-balloon-fill me-1"></i> Type-a-Balloon
      </span>
    </div>

    <div class="d-flex align-items-center gap-3">
      <div class="small text-muted d-none d-md-block">
        Personal Record: <span class="fw-bold text-pink" style="color: #ec4899;" id="topScoreDisplay"><?= number_format($highScore['score']) ?></span> pts
      </div>
      <button class="btn btn-sm btn-control" id="btnToggleSound" title="Toggle Sound FX">
        <i class="bi bi-volume-up-fill" id="soundIcon"></i>
      </button>
    </div>
  </div>

  <!-- Game HUD Bar -->
  <div class="balloon-hud-bar">
    <div class="d-flex align-items-center gap-4 flex-wrap">
      <div class="hud-stat-box">
        <span class="hud-stat-label">Score</span>
        <span class="hud-stat-val score" id="hudScore">0</span>
      </div>

      <div class="hud-stat-box">
        <span class="hud-stat-label">Popped</span>
        <span class="hud-stat-val" id="hudPopped" style="color: #38bdf8;">0</span>
      </div>

      <div class="hud-stat-box">
        <span class="hud-stat-label">Streak</span>
        <span class="hud-stat-val text-warning" id="hudStreak">0</span>
        <span class="badge bg-warning text-dark px-1.5 py-0.5" id="hudMultiplier" style="font-size: 0.65rem; font-weight: 800;">1x</span>
      </div>
    </div>

    <div class="d-flex align-items-center gap-4 flex-wrap">
      <div class="hud-stat-box">
        <span class="hud-stat-label">Accuracy</span>
        <span class="hud-stat-val" id="hudAccuracy" style="color: #a855f7;">100%</span>
      </div>

      <div class="hud-stat-box">
        <span class="hud-stat-label">Lives Left</span>
        <div id="livesContainer" class="d-flex align-items-center gap-1 fs-5 text-danger">
          <i class="bi bi-balloon-fill"></i>
          <i class="bi bi-balloon-fill"></i>
          <i class="bi bi-balloon-fill"></i>
        </div>
      </div>
    </div>
  </div>

  <!-- Canvas Stage & Overlays -->
  <div class="canvas-wrapper">
    <canvas id="balloonCanvas" width="1000" height="580"></canvas>

    <!-- Start Overlay -->
    <div class="balloon-overlay" id="startOverlay">
      <div class="badge px-3 py-1 mb-2 rounded-pill" style="background: rgba(236, 72, 153, 0.15); color: #ec4899; border: 1px solid rgba(236, 72, 153, 0.3); font-size: 0.75rem;">
        ★ TYPING.COM SKY POPPING CLASSIC ★
      </div>
      <h1 class="overlay-title">Type-a-Balloon</h1>
      <p class="overlay-desc">
        Balloons are floating into the sky! Type the matching letter on your keyboard to throw a sharp dart and pop each balloon into colorful confetti bursts before it drifts beyond the stratosphere!
      </p>

      <!-- Mode Selection -->
      <div class="d-flex align-items-center gap-2 mb-3 flex-wrap justify-content-center">
        <span class="text-secondary small fw-bold me-1">Target Mode:</span>
        <button type="button" class="mode-pill-btn active" data-mode="letters">Single Letters (A-Z)</button>
        <button type="button" class="mode-pill-btn" data-mode="homerow">Home Row Focus</button>
        <button type="button" class="mode-pill-btn" data-mode="words">Short Words</button>
      </div>

      <!-- Difficulty Selection -->
      <div class="d-flex align-items-center gap-2 mb-4 flex-wrap justify-content-center">
        <span class="text-secondary small fw-bold me-1">Ascent Speed:</span>
        <button type="button" class="mode-pill-btn active" data-diff="easy">Gentle Breeze (Slow)</button>
        <button type="button" class="mode-pill-btn" data-diff="medium">High Altitude (Normal)</button>
        <button type="button" class="mode-pill-btn" data-diff="hard">Jet Stream (Fast)</button>
      </div>

      <button type="button" class="btn-arcade-start d-flex align-items-center gap-2" id="btnStartGame">
        <i class="bi bi-play-circle-fill fs-5"></i>
        <span>Launch Sky Pop</span>
      </button>

      <div class="mt-4 text-muted small">
        <i class="bi bi-star-fill text-warning me-1"></i> Golden balloons give <strong>3x points</strong>! Beware of gray storm balloons with ⚡!
      </div>
    </div>

    <!-- Pause Overlay -->
    <div class="balloon-overlay d-none" id="pauseOverlay">
      <h2 class="overlay-title">Flight Paused</h2>
      <p class="overlay-desc">Take a breather, stretch your fingers, and resume when ready.</p>
      <div class="d-flex gap-3">
        <button type="button" class="btn-arcade-start" id="btnResumeGame">Resume Flight</button>
        <button type="button" class="btn btn-outline-secondary px-4 py-2" id="btnRestartFromPause">Restart</button>
      </div>
    </div>

    <!-- Game Over Overlay -->
    <div class="balloon-overlay d-none" id="gameOverOverlay">
      <div class="badge bg-danger text-white px-3 py-1 mb-2 rounded-pill">OUT OF BALLOON LIVES</div>
      <h2 class="overlay-title">Sky Expedition Complete!</h2>
      <p class="overlay-desc" id="gameOverReason">Too many balloons drifted into the clouds.</p>

      <div class="d-flex gap-3 mb-4 flex-wrap justify-content-center">
        <div class="stat-metric-card">
          <div class="val text-success" id="finalScore">0</div>
          <div class="lbl">Final Score</div>
        </div>
        <div class="stat-metric-card">
          <div class="val text-pink" style="color: #ec4899;" id="finalPopped">0</div>
          <div class="lbl">Balloons Popped</div>
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
        <i class="bi bi-cloud-check-fill me-1"></i> High score saved to leaderboard!
      </div>

      <div class="d-flex gap-3 flex-wrap justify-content-center">
        <button type="button" class="btn-arcade-start" id="btnPlayAgain">
          <i class="bi bi-arrow-repeat me-1"></i> Play Again
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
      <span class="badge bg-dark border text-light"><kbd class="bg-transparent border-0 text-white p-0">A-Z</kbd> Type Letter / Word</span>
      <span class="badge bg-dark border text-light"><kbd class="bg-transparent border-0 text-white p-0">ESC</kbd> Pause</span>
    </div>

    <div class="d-flex align-items-center gap-2">
      <a href="<?= BASE_URL ?>/game-ninja.php" class="btn btn-sm btn-control">
        <i class="bi bi-slash-circle-fill me-1" style="color: #ff4d00;"></i> Keyboard Ninja
      </a>
      <a href="<?= BASE_URL ?>/game-racer.php" class="btn btn-sm btn-control">
        <i class="bi bi-speedometer2 me-1" style="color: #f59e0b;"></i> Cyber Grand Prix
      </a>
      <button type="button" class="btn btn-sm btn-control" id="btnRestartGame">
        <i class="bi bi-arrow-clockwise me-1"></i> Restart
      </button>
    </div>
  </div>

</div>

<!-- Synthetic Web Audio SFX Engine & Balloon Canvas Engine -->
<script>
(function() {
  // Web Audio Synthesizer Engine
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

    playPop() {
      if (!this.enabled || !this.ctx) return;
      const osc = this.ctx.createOscillator();
      const gain = this.ctx.createGain();
      const now = this.ctx.currentTime;

      osc.type = 'sine';
      osc.frequency.setValueAtTime(600, now);
      osc.frequency.exponentialRampToValueAtTime(140, now + 0.08);

      gain.gain.setValueAtTime(0.3, now);
      gain.gain.exponentialRampToValueAtTime(0.001, now + 0.08);

      osc.connect(gain);
      gain.connect(this.ctx.destination);
      osc.start(now);
      osc.stop(now + 0.08);
    },

    playGoldPop() {
      if (!this.enabled || !this.ctx) return;
      const notes = [659.25, 880, 1046.50];
      notes.forEach((freq, idx) => {
        const osc = this.ctx.createOscillator();
        const gain = this.ctx.createGain();
        const now = this.ctx.currentTime + idx * 0.05;

        osc.type = 'triangle';
        osc.frequency.setValueAtTime(freq, now);

        gain.gain.setValueAtTime(0.18, now);
        gain.gain.exponentialRampToValueAtTime(0.001, now + 0.12);

        osc.connect(gain);
        gain.connect(this.ctx.destination);
        osc.start(now);
        osc.stop(now + 0.12);
      });
    },

    playStormExplosion() {
      if (!this.enabled || !this.ctx) return;
      const osc = this.ctx.createOscillator();
      const gain = this.ctx.createGain();
      const now = this.ctx.currentTime;

      osc.type = 'sawtooth';
      osc.frequency.setValueAtTime(150, now);
      osc.frequency.exponentialRampToValueAtTime(40, now + 0.3);

      gain.gain.setValueAtTime(0.35, now);
      gain.gain.exponentialRampToValueAtTime(0.001, now + 0.3);

      osc.connect(gain);
      gain.connect(this.ctx.destination);
      osc.start(now);
      osc.stop(now + 0.3);
    },

    playMiss() {
      if (!this.enabled || !this.ctx) return;
      const osc = this.ctx.createOscillator();
      const gain = this.ctx.createGain();
      const now = this.ctx.currentTime;

      osc.type = 'triangle';
      osc.frequency.setValueAtTime(160, now);
      osc.frequency.setValueAtTime(110, now + 0.08);

      gain.gain.setValueAtTime(0.15, now);
      gain.gain.exponentialRampToValueAtTime(0.001, now + 0.12);

      osc.connect(gain);
      gain.connect(this.ctx.destination);
      osc.start(now);
      osc.stop(now + 0.12);
    }
  };

  // Target word/letter banks
  const HOMEROW_KEYS = ['a', 's', 'd', 'f', 'j', 'k', 'l', ';'];
  const ALL_LETTERS = 'abcdefghijklmnopqrstuvwxyz'.split('');
  const SHORT_WORDS = [
    "cat", "sky", "pop", "sun", "fly", "air", "red", "cup", "run", "joy",
    "top", "dot", "tag", "fox", "gem", "hop", "ice", "key", "map", "net"
  ];

  const BALLOON_COLORS = [
    { fill: '#ef4444', border: '#b91c1c' }, // Red
    { fill: '#3b82f6', border: '#1d4ed8' }, // Blue
    { fill: '#10b981', border: '#047857' }, // Green
    { fill: '#a855f7', border: '#7e22ce' }, // Purple
    { fill: '#ec4899', border: '#be185d' }, // Pink
    { fill: '#06b6d4', border: '#0e7490' }  // Cyan
  ];

  // Engine Variables
  const canvas = document.getElementById('balloonCanvas');
  const ctx = canvas.getContext('2d');

  let animationId = null;
  let isRunning = false;
  let isPaused = false;

  let currentMode = 'letters';
  let currentDiff = 'easy';

  let score = 0;
  let poppedCount = 0;
  let streak = 0;
  let highestStreak = 0;
  let totalKeystrokes = 0;
  let correctKeystrokes = 0;
  let lives = 3;

  let balloons = [];
  let confetti = [];
  let clouds = [];
  let spawnTimer = 0;

  // Active word lock (for word mode)
  let activeBalloon = null;

  // DOM Elements
  const hudScore = document.getElementById('hudScore');
  const hudPopped = document.getElementById('hudPopped');
  const hudStreak = document.getElementById('hudStreak');
  const hudMultiplier = document.getElementById('hudMultiplier');
  const hudAccuracy = document.getElementById('hudAccuracy');
  const livesContainer = document.getElementById('livesContainer');

  const startOverlay = document.getElementById('startOverlay');
  const pauseOverlay = document.getElementById('pauseOverlay');
  const gameOverOverlay = document.getElementById('gameOverOverlay');

  const finalScoreEl = document.getElementById('finalScore');
  const finalPoppedEl = document.getElementById('finalPopped');
  const finalStreakEl = document.getElementById('finalStreak');
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

  // --- Background Clouds ---
  function initClouds() {
    clouds = [];
    const count = 5;
    const w = canvas.getBoundingClientRect().width;
    for (let i = 0; i < count; i++) {
      clouds.push({
        x: Math.random() * w,
        y: 40 + Math.random() * 260,
        speed: 0.15 + Math.random() * 0.25,
        radius: 35 + Math.random() * 25
      });
    }
  }

  // --- Balloon Spawner ---
  function getSpeedMultiplier() {
    if (currentDiff === 'easy') return 0.75;
    if (currentDiff === 'medium') return 1.15;
    return 1.6;
  }

  function spawnBalloon() {
    const rect = canvas.getBoundingClientRect();
    const margin = 70;
    const x = margin + Math.random() * (rect.width - margin * 2);

    let text = '';
    if (currentMode === 'letters') {
      text = ALL_LETTERS[Math.floor(Math.random() * ALL_LETTERS.length)];
    } else if (currentMode === 'homerow') {
      text = HOMEROW_KEYS[Math.floor(Math.random() * HOMEROW_KEYS.length)];
    } else {
      text = SHORT_WORDS[Math.floor(Math.random() * SHORT_WORDS.length)];
    }

    // Determine type: Normal, Golden (10%), Storm Hazard (10%)
    const rand = Math.random();
    let type = 'normal';
    let colorObj = BALLOON_COLORS[Math.floor(Math.random() * BALLOON_COLORS.length)];

    if (rand < 0.12) {
      type = 'gold';
      colorObj = { fill: '#fbbf24', border: '#b45309' }; // Gold
    } else if (rand > 0.88 && currentMode === 'letters') {
      type = 'storm';
      colorObj = { fill: '#475569', border: '#1e293b' }; // Dark storm
      text = '⚡';
    }

    const balloon = {
      x: x,
      y: 620, // Start below canvas
      vy: (0.7 + Math.random() * 0.4) * getSpeedMultiplier(),
      wobbleAngle: Math.random() * Math.PI * 2,
      wobbleSpeed: 0.03 + Math.random() * 0.02,
      text: text,
      typedLen: 0,
      type: type,
      color: colorObj,
      radiusX: 28 + (text.length > 1 ? text.length * 5 : 0),
      radiusY: 36
    };

    balloons.push(balloon);
  }

  // --- Confetti Particle FX ---
  function createConfetti(x, y, color = '#ec4899', count = 28) {
    for (let i = 0; i < count; i++) {
      const angle = Math.random() * Math.PI * 2;
      const speed = Math.random() * 5 + 2;
      confetti.push({
        x: x,
        y: y,
        vx: Math.cos(angle) * speed,
        vy: Math.sin(angle) * speed,
        width: Math.random() * 6 + 4,
        height: Math.random() * 4 + 2,
        color: ['#ec4899', '#38bdf8', '#fbbf24', '#10b981', '#a855f7'][Math.floor(Math.random() * 5)],
        rotation: Math.random() * Math.PI * 2,
        rotSpeed: (Math.random() - 0.5) * 0.2,
        alpha: 1.0,
        decay: Math.random() * 0.025 + 0.015
      });
    }
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

    // Move Clouds
    clouds.forEach(c => {
      c.x += c.speed;
      if (c.x > rect.width + 60) {
        c.x = -60;
        c.y = 40 + Math.random() * 260;
      }
    });

    // Spawn timer
    spawnTimer++;
    const spawnPace = currentDiff === 'easy' ? 95 : (currentDiff === 'medium' ? 70 : 50);
    if (spawnTimer >= spawnPace) {
      spawnTimer = 0;
      spawnBalloon();
    }

    // Move Balloons
    for (let i = balloons.length - 1; i >= 0; i--) {
      const b = balloons[i];
      b.y -= b.vy;
      b.wobbleAngle += b.wobbleSpeed;

      // If balloon drifted off the top of screen
      if (b.y < -50) {
        if (b.type !== 'storm') {
          // Lost balloon penalty
          lives--;
          AudioEngine.playMiss();
          streak = 0;
          updateHUD();

          if (activeBalloon === b) activeBalloon = null;

          if (lives <= 0) {
            triggerGameOver('Three balloons drifted beyond reach.');
            return;
          }
        }
        balloons.splice(i, 1);
      }
    }

    // Update Confetti
    for (let i = confetti.length - 1; i >= 0; i--) {
      const p = confetti[i];
      p.x += p.vx;
      p.y += p.vy;
      p.vy += 0.12; // gravity
      p.rotation += p.rotSpeed;
      p.alpha -= p.decay;
      if (p.alpha <= 0) {
        confetti.splice(i, 1);
      }
    }
  }

  function draw() {
    const rect = canvas.getBoundingClientRect();
    const w = rect.width;
    const h = 580;

    ctx.clearRect(0, 0, w, h);

    // Sky Gradient (Atmospheric twilight to deep blue)
    const skyGrad = ctx.createLinearGradient(0, 0, 0, h);
    skyGrad.addColorStop(0, '#0c1222');
    skyGrad.addColorStop(0.5, '#131e3a');
    skyGrad.addColorStop(1, '#1e293b');
    ctx.fillStyle = skyGrad;
    ctx.fillRect(0, 0, w, h);

    // Draw Clouds
    clouds.forEach(c => {
      ctx.save();
      ctx.fillStyle = 'rgba(255, 255, 255, 0.05)';
      ctx.beginPath();
      ctx.arc(c.x, c.y, c.radius, 0, Math.PI * 2);
      ctx.arc(c.x + c.radius * 0.6, c.y - c.radius * 0.2, c.radius * 0.8, 0, Math.PI * 2);
      ctx.arc(c.x - c.radius * 0.6, c.y, c.radius * 0.7, 0, Math.PI * 2);
      ctx.fill();
      ctx.restore();
    });

    // Draw Balloons
    balloons.forEach(b => {
      drawBalloon(b);
    });

    // Draw Confetti
    confetti.forEach(p => {
      ctx.save();
      ctx.translate(p.x, p.y);
      ctx.rotate(p.rotation);
      ctx.fillStyle = p.color;
      ctx.globalAlpha = Math.max(0, p.alpha);
      ctx.fillRect(-p.width / 2, -p.height / 2, p.width, p.height);
      ctx.restore();
    });
  }

  // --- Vector Art: Helium Balloon ---
  function drawBalloon(b) {
    const wobbleX = Math.sin(b.wobbleAngle) * 6;
    const bx = b.x + wobbleX;
    const by = b.y;

    ctx.save();
    ctx.translate(bx, by);

    // Balloon String (wiggly line downward)
    ctx.strokeStyle = 'rgba(255, 255, 255, 0.35)';
    ctx.lineWidth = 1.2;
    ctx.beginPath();
    ctx.moveTo(0, b.radiusY);
    ctx.bezierCurveTo(-6, b.radiusY + 15, 6, b.radiusY + 30, 0, b.radiusY + 45);
    ctx.stroke();

    // Balloon Knot
    ctx.fillStyle = b.color.border;
    ctx.beginPath();
    ctx.moveTo(-4, b.radiusY);
    ctx.lineTo(4, b.radiusY);
    ctx.lineTo(0, b.radiusY + 6);
    ctx.closePath();
    ctx.fill();

    // Balloon Body (Oval)
    ctx.fillStyle = b.color.fill;
    ctx.strokeStyle = b.color.border;
    ctx.lineWidth = 2.5;

    ctx.beginPath();
    ctx.ellipse(0, 0, b.radiusX, b.radiusY, 0, 0, Math.PI * 2);
    ctx.fill();
    ctx.stroke();

    // Glossy 3D Highlight Curve
    ctx.fillStyle = 'rgba(255, 255, 255, 0.4)';
    ctx.beginPath();
    ctx.ellipse(-b.radiusX * 0.35, -b.radiusY * 0.35, b.radiusX * 0.22, b.radiusY * 0.3, -Math.PI / 6, 0, Math.PI * 2);
    ctx.fill();

    // Gold Sparkle Badge
    if (b.type === 'gold') {
      ctx.fillStyle = '#ffffff';
      ctx.font = 'bold 10px sans-serif';
      ctx.fillText('★ 3X', -12, -b.radiusY + 8);
    }

    // Text Label inside Balloon
    ctx.font = 'bold 22px "JetBrains Mono", monospace';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';

    if (b.type === 'storm') {
      ctx.fillStyle = '#fbbf24';
      ctx.fillText('⚡', 0, 0);
    } else {
      // In Word Mode, display typed vs untyped characters
      if (b.text.length > 1) {
        ctx.font = 'bold 17px "JetBrains Mono", monospace';
        const fullW = ctx.measureText(b.text).width;
        let startX = -fullW / 2;

        for (let i = 0; i < b.text.length; i++) {
          const char = b.text[i];
          const cw = ctx.measureText(char).width;
          ctx.fillStyle = (i < b.typedLen) ? '#10b981' : '#ffffff';
          ctx.fillText(char, startX + cw / 2, 0);
          startX += cw;
        }
      } else {
        ctx.fillStyle = '#ffffff';
        ctx.fillText(b.text.toUpperCase(), 0, 0);
      }
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

    if (e.key.length === 1 && !e.ctrlKey && !e.altKey && !e.metaKey) {
      handleKeyInput(e.key.toLowerCase());
    }
  });

  function handleKeyInput(char) {
    totalKeystrokes++;

    // 1. If currently locked onto a word balloon
    if (activeBalloon) {
      const expectedChar = activeBalloon.text[activeBalloon.typedLen].toLowerCase();
      if (char === expectedChar) {
        correctKeystrokes++;
        activeBalloon.typedLen++;
        streak++;
        if (streak > highestStreak) highestStreak = streak;

        if (activeBalloon.typedLen >= activeBalloon.text.length) {
          popBalloon(activeBalloon);
          activeBalloon = null;
        }
        updateHUD();
        return;
      } else {
        // Typo
        streak = 0;
        AudioEngine.playMiss();
        updateHUD();
        return;
      }
    }

    // 2. Search for balloon starting with typed char (prioritize highest on screen, lowest y)
    const sorted = [...balloons].sort((a, b) => a.y - b.y);

    // Check if user accidentally hit storm balloon (penalize!)
    const stormHit = sorted.find(b => b.type === 'storm');

    const candidate = sorted.find(b => {
      if (b.type === 'storm') return false;
      if (b.text.length > 1) {
        return b.text[0].toLowerCase() === char;
      }
      return b.text.toLowerCase() === char;
    });

    if (candidate) {
      correctKeystrokes++;
      streak++;
      if (streak > highestStreak) highestStreak = streak;

      if (candidate.text.length > 1) {
        // Multi-letter word
        activeBalloon = candidate;
        activeBalloon.typedLen = 1;
        if (activeBalloon.typedLen >= activeBalloon.text.length) {
          popBalloon(activeBalloon);
          activeBalloon = null;
        }
      } else {
        // Single letter instant pop!
        popBalloon(candidate);
      }
      updateHUD();
    } else {
      streak = 0;
      AudioEngine.playMiss();
      updateHUD();
    }
  }

  function popBalloon(b) {
    poppedCount++;

    const isGold = (b.type === 'gold');
    if (isGold) {
      AudioEngine.playGoldPop();
      createConfetti(b.x, b.y, '#fbbf24', 40);
    } else {
      AudioEngine.playPop();
      createConfetti(b.x, b.y, b.color.fill, 24);
    }

    const mult = streak >= 20 ? 3 : (streak >= 10 ? 2 : 1);
    const basePts = (b.text.length * 25) * mult;
    score += isGold ? (basePts * 3) : basePts;

    // Remove from array
    const idx = balloons.indexOf(b);
    if (idx !== -1) {
      balloons.splice(idx, 1);
    }
  }

  function updateHUD() {
    hudScore.textContent = score.toLocaleString();
    hudPopped.textContent = poppedCount.toLocaleString();
    hudStreak.textContent = streak;

    const mult = streak >= 20 ? 3 : (streak >= 10 ? 2 : 1);
    hudMultiplier.textContent = `${mult}x`;

    const acc = totalKeystrokes > 0 ? ((correctKeystrokes / totalKeystrokes) * 100).toFixed(0) : '100';
    hudAccuracy.textContent = `${acc}%`;

    // Render lives balloons
    let livesHtml = '';
    for (let i = 0; i < 3; i++) {
      if (i < lives) {
        livesHtml += '<i class="bi bi-balloon-fill text-danger"></i>';
      } else {
        livesHtml += '<i class="bi bi-balloon text-muted"></i>';
      }
    }
    livesContainer.innerHTML = livesHtml;
  }

  // --- Game Lifecycle ---
  function startGame() {
    AudioEngine.init();

    score = 0;
    poppedCount = 0;
    streak = 0;
    highestStreak = 0;
    totalKeystrokes = 0;
    correctKeystrokes = 0;
    lives = 3;

    balloons = [];
    confetti = [];
    activeBalloon = null;
    spawnTimer = 0;

    initClouds();
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

  function triggerGameOver(reason = 'Expedition ended.') {
    isRunning = false;
    if (animationId) cancelAnimationFrame(animationId);

    const acc = totalKeystrokes > 0 ? ((correctKeystrokes / totalKeystrokes) * 100).toFixed(1) : '100.0';

    finalScoreEl.textContent = score.toLocaleString();
    finalPoppedEl.textContent = poppedCount.toLocaleString();
    finalStreakEl.textContent = highestStreak;
    finalAccuracyEl.textContent = `${acc}%`;
    document.getElementById('gameOverReason').textContent = reason;

    gameOverOverlay.classList.remove('d-none');

    saveScore(score, highestStreak, parseFloat(acc), currentDiff, currentMode);
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
        game_slug: 'balloon',
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
          saveScoreFeedback.innerHTML = `<i class="bi bi-trophy-fill text-warning me-1"></i> <strong>New Personal Record!</strong> Score: ${scoreVal.toLocaleString()}`;
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

  // Mode Buttons
  document.querySelectorAll('[data-mode]').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('[data-mode]').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      currentMode = btn.getAttribute('data-mode');
    });
  });

  // Diff Buttons
  document.querySelectorAll('[data-diff]').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('[data-diff]').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      currentDiff = btn.getAttribute('data-diff');
    });
  });

  initClouds();
  draw();
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
