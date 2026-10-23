<?php
/**
 * Custom Type Master Pro
 * Meteor Matrix Defense (Arcade Meteor Typing Game)
 * Railgun plasma strikes, burning meteor trails, EMP shockwaves, and city defense
 */

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/games_data.php';

$currentUser = current_user();
$userId = $currentUser ? (int)$currentUser['id'] : 0;
$highScore = get_user_game_high_score($userId, 'meteor');

$pageTitle = 'Meteor Matrix Defense — Arcade Railgun Typing';
$pageBadgeTitle = 'Meteor Defense';
$pageIcon = 'bi-fire';

include __DIR__ . '/includes/header.php';
?>

<style>
/* Meteor Defense Styles */
.meteor-container {
  max-width: 1100px;
  margin: 0 auto;
}

.meteor-hud-bar {
  background: #090e17;
  border: 1px solid #162234;
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
  color: #64748b;
  letter-spacing: 0.08em;
}

.hud-stat-val {
  font-size: 1.15rem;
  font-family: 'JetBrains Mono', monospace;
  font-weight: 800;
  color: #10b981;
}

.hud-stat-val.score {
  color: #38bdf8;
}

.hud-stat-val.grid {
  color: #10b981;
}

.canvas-wrapper {
  position: relative;
  width: 100%;
  background: #040810;
  border-left: 1px solid #162234;
  border-right: 1px solid #162234;
  overflow: hidden;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.8);
}

#meteorCanvas {
  display: block;
  width: 100%;
  height: 580px;
  cursor: crosshair;
}

.game-control-strip {
  background: #090e17;
  border: 1px solid #162234;
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
.meteor-overlay {
  position: absolute;
  inset: 0;
  background: rgba(4, 8, 16, 0.94);
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

.meteor-overlay.d-none {
  display: none !important;
}

.overlay-title {
  font-size: 2.5rem;
  font-weight: 900;
  letter-spacing: -0.03em;
  background: linear-gradient(135deg, #10b981 0%, #38bdf8 50%, #f59e0b 100%);
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
  background: #101928;
  border: 1px solid #1e2f4a;
  color: #cbd5e1;
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s ease;
}

.mode-pill-btn:hover {
  border-color: #10b981;
  color: #10b981;
}

.mode-pill-btn.active {
  background: #10b981;
  border-color: #10b981;
  color: #000000;
  font-weight: 800;
}

.btn-arcade-start {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: #000000;
  border: none;
  font-weight: 800;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  padding: 12px 34px;
  border-radius: 10px;
  font-size: 1rem;
  box-shadow: 0 4px 18px rgba(16, 185, 129, 0.4);
  transition: all 0.2s ease;
}

.btn-arcade-start:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 24px rgba(16, 185, 129, 0.6);
  color: #000000;
}

.grid-bar-wrap {
  width: 140px;
  height: 10px;
  background: #101928;
  border-radius: 6px;
  overflow: hidden;
  border: 1px solid #1e2f4a;
}

.grid-bar-fill {
  height: 100%;
  width: 100%;
  background: linear-gradient(90deg, #ef4444 0%, #eab308 50%, #10b981 100%);
  transition: width 0.3s ease;
}

.stat-metric-card {
  background: #101928;
  border: 1px solid #1e2f4a;
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

<div class="meteor-container py-3">

  <!-- Header & Back Navigation -->
  <div class="d-flex align-items-center justify-content-between mb-3">
    <div class="d-flex align-items-center gap-2">
      <a href="<?= BASE_URL ?>/games.php" class="btn btn-sm btn-control d-inline-flex align-items-center gap-1">
        <i class="bi bi-arrow-left"></i>
        <span>Arcade Hub</span>
      </a>
      <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3);">
        <i class="bi bi-fire me-1"></i> Meteor Matrix Defense
      </span>
    </div>

    <div class="d-flex align-items-center gap-3">
      <div class="small text-muted d-none d-md-block">
        Personal Record: <span class="fw-bold text-success" id="topScoreDisplay"><?= number_format($highScore['score']) ?></span> pts
      </div>
      <button class="btn btn-sm btn-control" id="btnToggleSound" title="Toggle Sound FX">
        <i class="bi bi-volume-up-fill" id="soundIcon"></i>
      </button>
    </div>
  </div>

  <!-- Game HUD Bar -->
  <div class="meteor-hud-bar">
    <div class="d-flex align-items-center gap-4 flex-wrap">
      <div class="hud-stat-box">
        <span class="hud-stat-label">Score</span>
        <span class="hud-stat-val score" id="hudScore">0</span>
      </div>

      <div class="hud-stat-box">
        <span class="hud-stat-label">Wave</span>
        <span class="hud-stat-val" id="hudWave" style="color: #38bdf8;">1</span>
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
        <span class="hud-stat-val" id="hudAccuracy" style="color: #10b981;">100%</span>
      </div>

      <div class="hud-stat-box">
        <span class="hud-stat-label">Grid Integrity</span>
        <div class="grid-bar-wrap">
          <div class="grid-bar-fill" id="hudGridFill"></div>
        </div>
        <span class="hud-stat-val grid" id="hudGridText" style="font-size: 0.85rem;">100%</span>
      </div>
    </div>
  </div>

  <!-- Canvas Stage & Overlays -->
  <div class="canvas-wrapper">
    <canvas id="meteorCanvas" width="1000" height="580"></canvas>

    <!-- Start Overlay -->
    <div class="meteor-overlay" id="startOverlay">
      <div class="badge px-3 py-1 mb-2 rounded-pill" style="background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); font-size: 0.75rem;">
        ★ PLANETARY RAILGUN DEFENSE ★
      </div>
      <h1 class="overlay-title">Meteor Matrix Defense</h1>
      <p class="overlay-desc">
        A catastrophic meteor shower is descending upon the metropolitan energy grid. Type the characters on each falling meteor to fire high-energy plasma railgun rounds before ground impact!
      </p>

      <!-- Target Mode Selection -->
      <div class="d-flex align-items-center gap-2 mb-3 flex-wrap justify-content-center">
        <span class="text-secondary small fw-bold me-1">Target Mode:</span>
        <button type="button" class="mode-pill-btn active" data-mode="letters">Single Characters (A-Z)</button>
        <button type="button" class="mode-pill-btn" data-mode="words">Short Words (3-5 letters)</button>
        <button type="button" class="mode-pill-btn" data-mode="emp">EMP Supernova Rocks</button>
      </div>

      <!-- Difficulty Selection -->
      <div class="d-flex align-items-center gap-2 mb-4 flex-wrap justify-content-center">
        <span class="text-secondary small fw-bold me-1">Threat Level:</span>
        <button type="button" class="mode-pill-btn active" data-diff="easy">Normal (Manageable)</button>
        <button type="button" class="mode-pill-btn" data-diff="medium">Veteran (Dense Shower)</button>
        <button type="button" class="mode-pill-btn" data-diff="hard">Armageddon (Terminal)</button>
      </div>

      <button type="button" class="btn-arcade-start d-flex align-items-center gap-2" id="btnStartGame">
        <i class="bi bi-shield-shaded fs-5"></i>
        <span>Engage Railgun Batteries</span>
      </button>

      <div class="mt-4 text-muted small">
        <i class="bi bi-lightning-charge-fill text-warning me-1"></i> Look out for blue glowing <strong>[EMP]</strong> meteors! Typing them detonates all active meteors!
      </div>
    </div>

    <!-- Pause Overlay -->
    <div class="meteor-overlay d-none" id="pauseOverlay">
      <h2 class="overlay-title">System Paused</h2>
      <p class="overlay-desc">Targeting sensors holding position. Resume when ready.</p>
      <div class="d-flex gap-3">
        <button type="button" class="btn-arcade-start" id="btnResumeGame">Resume Defense</button>
        <button type="button" class="btn btn-outline-secondary px-4 py-2" id="btnRestartFromPause">Restart</button>
      </div>
    </div>

    <!-- Game Over Overlay -->
    <div class="meteor-overlay d-none" id="gameOverOverlay">
      <div class="badge bg-danger text-white px-3 py-1 mb-2 rounded-pill">METROPOLITAN GRID COLLAPSE</div>
      <h2 class="overlay-title" style="background: linear-gradient(135deg, #ef4444 0%, #f97316 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Grid Breached!</h2>
      <p class="overlay-desc" id="gameOverReason">The central energy grid sustained terminal impact damage.</p>

      <div class="d-flex gap-3 mb-4 flex-wrap justify-content-center">
        <div class="stat-metric-card">
          <div class="val text-success" id="finalScore">0</div>
          <div class="lbl">Final Score</div>
        </div>
        <div class="stat-metric-card">
          <div class="val text-info" id="finalWave">1</div>
          <div class="lbl">Waves Survived</div>
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
      <span class="badge bg-dark border text-light"><kbd class="bg-transparent border-0 text-white p-0">A-Z</kbd> Type Target</span>
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

<!-- Synthetic Web Audio SFX & Meteor Defense Canvas Engine -->
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

    playRailgun() {
      if (!this.enabled || !this.ctx) return;
      const osc = this.ctx.createOscillator();
      const gain = this.ctx.createGain();
      const now = this.ctx.currentTime;

      osc.type = 'sawtooth';
      osc.frequency.setValueAtTime(440, now);
      osc.frequency.exponentialRampToValueAtTime(70, now + 0.12);

      gain.gain.setValueAtTime(0.25, now);
      gain.gain.exponentialRampToValueAtTime(0.001, now + 0.12);

      osc.connect(gain);
      gain.connect(this.ctx.destination);
      osc.start(now);
      osc.stop(now + 0.12);
    },

    playExplosion() {
      if (!this.enabled || !this.ctx) return;
      const now = this.ctx.currentTime;
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
      filter.frequency.setValueAtTime(450, now);
      filter.frequency.exponentialRampToValueAtTime(60, now + 0.3);

      const gain = this.ctx.createGain();
      gain.gain.setValueAtTime(0.3, now);
      gain.gain.exponentialRampToValueAtTime(0.001, now + 0.3);

      whiteNoise.connect(filter);
      filter.connect(gain);
      gain.connect(this.ctx.destination);
      whiteNoise.start(now);
      whiteNoise.stop(now + 0.3);
    },

    playEmpWave() {
      if (!this.enabled || !this.ctx) return;
      const osc = this.ctx.createOscillator();
      const gain = this.ctx.createGain();
      const now = this.ctx.currentTime;

      osc.type = 'sine';
      osc.frequency.setValueAtTime(200, now);
      osc.frequency.linearRampToValueAtTime(900, now + 0.3);

      gain.gain.setValueAtTime(0.2, now);
      gain.gain.exponentialRampToValueAtTime(0.001, now + 0.3);

      osc.connect(gain);
      gain.connect(this.ctx.destination);
      osc.start(now);
      osc.stop(now + 0.3);
    },

    playImpact() {
      if (!this.enabled || !this.ctx) return;
      const osc = this.ctx.createOscillator();
      const gain = this.ctx.createGain();
      const now = this.ctx.currentTime;

      osc.type = 'square';
      osc.frequency.setValueAtTime(110, now);
      osc.frequency.exponentialRampToValueAtTime(35, now + 0.35);

      gain.gain.setValueAtTime(0.3, now);
      gain.gain.exponentialRampToValueAtTime(0.001, now + 0.35);

      osc.connect(gain);
      gain.connect(this.ctx.destination);
      osc.start(now);
      osc.stop(now + 0.35);
    }
  };

  // Word & Character Banks
  const ALPHABET = 'abcdefghijklmnopqrstuvwxyz'.split('');
  const SHORT_WORDS = [
    "core", "fire", "dust", "rock", "fall", "nova", "iron", "flux",
    "burn", "atom", "blast", "comet", "spark", "pulse", "solar", "blaze"
  ];

  // Engine Variables
  const canvas = document.getElementById('meteorCanvas');
  const ctx = canvas.getContext('2d');

  let animationId = null;
  let isRunning = false;
  let isPaused = false;

  let currentMode = 'letters';
  let currentDiff = 'easy';

  let score = 0;
  let streak = 0;
  let highestStreak = 0;
  let totalKeystrokes = 0;
  let correctKeystrokes = 0;
  let gridHp = 100;
  let wave = 1;
  let meteorsDestroyedInWave = 0;
  const METEORS_PER_WAVE = 10;

  let meteors = [];
  let railgunBeams = [];
  let rockDebris = [];
  let flameParticles = [];
  let activeMeteor = null;
  let spawnTimer = 0;

  // Railgun Turret position at bottom center
  const turret = {
    x: 0,
    y: 540,
    angle: -Math.PI / 2
  };

  // DOM Elements
  const hudScore = document.getElementById('hudScore');
  const hudWave = document.getElementById('hudWave');
  const hudStreak = document.getElementById('hudStreak');
  const hudMultiplier = document.getElementById('hudMultiplier');
  const hudAccuracy = document.getElementById('hudAccuracy');
  const hudGridFill = document.getElementById('hudGridFill');
  const hudGridText = document.getElementById('hudGridText');

  const startOverlay = document.getElementById('startOverlay');
  const pauseOverlay = document.getElementById('pauseOverlay');
  const gameOverOverlay = document.getElementById('gameOverOverlay');

  const finalScoreEl = document.getElementById('finalScore');
  const finalWaveEl = document.getElementById('finalWave');
  const finalStreakEl = document.getElementById('finalStreak');
  const finalAccuracyEl = document.getElementById('finalAccuracy');
  const saveScoreFeedback = document.getElementById('saveScoreFeedback');

  function resizeCanvas() {
    const rect = canvas.getBoundingClientRect();
    canvas.width = rect.width * (window.devicePixelRatio || 1);
    canvas.height = 580 * (window.devicePixelRatio || 1);
    ctx.scale(window.devicePixelRatio || 1, window.devicePixelRatio || 1);
    turret.x = rect.width / 2;
    turret.y = 540;
  }
  window.addEventListener('resize', resizeCanvas);
  resizeCanvas();

  // --- Meteor Generator ---
  function getSpeedMultiplier() {
    let mult = 1.0;
    if (currentDiff === 'easy') mult = 0.8;
    if (currentDiff === 'medium') mult = 1.2;
    if (currentDiff === 'hard') mult = 1.6;
    return mult + (wave - 1) * 0.1;
  }

  function spawnMeteor() {
    const rect = canvas.getBoundingClientRect();
    const margin = 80;
    const startX = margin + Math.random() * (rect.width - margin * 2);
    const targetX = margin + Math.random() * (rect.width - margin * 2);

    let text = '';
    let isEmp = false;

    if (currentMode === 'emp' && Math.random() < 0.25) {
      text = 'emp';
      isEmp = true;
    } else if (currentMode === 'words') {
      text = SHORT_WORDS[Math.floor(Math.random() * SHORT_WORDS.length)];
    } else {
      text = ALPHABET[Math.floor(Math.random() * ALPHABET.length)];
    }

    const angle = Math.atan2(510 - (-30), targetX - startX);
    const speed = (0.5 + Math.random() * 0.35) * getSpeedMultiplier();

    const meteor = {
      x: startX,
      y: -30,
      vx: Math.cos(angle) * speed,
      vy: Math.sin(angle) * speed,
      rotation: Math.random() * Math.PI * 2,
      rotSpeed: (Math.random() - 0.5) * 0.05,
      radius: text.length > 1 ? 26 + text.length * 4 : 24,
      text: text,
      typedLen: 0,
      isEmp: isEmp
    };

    meteors.push(meteor);
  }

  // --- FX: Flame Trails & Debris ---
  function createFlameParticle(x, y, isEmp = false) {
    flameParticles.push({
      x: x + (Math.random() * 8 - 4),
      y: y + (Math.random() * 8 - 4),
      vx: (Math.random() - 0.5) * 1.5,
      vy: -Math.random() * 2 - 0.5,
      radius: Math.random() * 5 + 3,
      color: isEmp ? '#38bdf8' : (Math.random() > 0.5 ? '#f97316' : '#eab308'),
      alpha: 0.85
    });
  }

  function createDebris(x, y, isEmp = false) {
    const count = 24;
    for (let i = 0; i < count; i++) {
      const angle = Math.random() * Math.PI * 2;
      const speed = Math.random() * 4 + 1.5;
      rockDebris.push({
        x: x,
        y: y,
        vx: Math.cos(angle) * speed,
        vy: Math.sin(angle) * speed,
        radius: Math.random() * 4 + 1.5,
        color: isEmp ? '#38bdf8' : '#78716c',
        alpha: 1.0,
        decay: Math.random() * 0.03 + 0.02
      });
    }
  }

  function fireRailgun(target) {
    turret.angle = Math.atan2(target.y - turret.y, target.x - turret.x);
    railgunBeams.push({
      startX: turret.x,
      startY: turret.y,
      targetX: target.x,
      targetY: target.y,
      alpha: 1.0,
      color: target.isEmp ? '#38bdf8' : '#10b981'
    });
    AudioEngine.playRailgun();
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

    // Spawner
    spawnTimer++;
    const spawnPace = currentDiff === 'easy' ? 115 : (currentDiff === 'medium' ? 85 : 60);
    if (spawnTimer >= spawnPace) {
      spawnTimer = 0;
      spawnMeteor();
    }

    // Move Meteors
    for (let i = meteors.length - 1; i >= 0; i--) {
      const m = meteors[i];
      m.x += m.vx;
      m.y += m.vy;
      m.rotation += m.rotSpeed;

      // Spawn tail smoke/flame
      if (Math.random() < 0.6) {
        createFlameParticle(m.x, m.y, m.isEmp);
      }

      // Ground Impact Check (y >= 510)
      if (m.y >= 510) {
        AudioEngine.playImpact();
        createDebris(m.x, 510, m.isEmp);
        gridHp = Math.max(0, gridHp - 20);
        streak = 0;
        updateHUD();

        if (activeMeteor === m) activeMeteor = null;
        meteors.splice(i, 1);

        if (gridHp <= 0) {
          triggerGameOver('The central energy grid sustained catastrophic impact failure!');
          return;
        }
      }
    }

    // Railgun Beams decay
    for (let i = railgunBeams.length - 1; i >= 0; i--) {
      railgunBeams[i].alpha -= 0.18;
      if (railgunBeams[i].alpha <= 0) {
        railgunBeams.splice(i, 1);
      }
    }

    // Flame particles decay
    for (let i = flameParticles.length - 1; i >= 0; i--) {
      const f = flameParticles[i];
      f.x += f.vx;
      f.y += f.vy;
      f.alpha -= 0.04;
      f.radius *= 0.96;
      if (f.alpha <= 0) {
        flameParticles.splice(i, 1);
      }
    }

    // Rock Debris decay
    for (let i = rockDebris.length - 1; i >= 0; i--) {
      const d = rockDebris[i];
      d.x += d.vx;
      d.y += d.vy;
      d.alpha -= d.decay;
      if (d.alpha <= 0) {
        rockDebris.splice(i, 1);
      }
    }
  }

  function draw() {
    const rect = canvas.getBoundingClientRect();
    const w = rect.width;
    const h = 580;

    ctx.clearRect(0, 0, w, h);

    // Deep Space Cosmic Gradient
    const bg = ctx.createLinearGradient(0, 0, 0, h);
    bg.addColorStop(0, '#030712');
    bg.addColorStop(0.7, '#071020');
    bg.addColorStop(1, '#0c1b30');
    ctx.fillStyle = bg;
    ctx.fillRect(0, 0, w, h);

    // Metropolitan City Skyline Vector Silhouettes
    ctx.fillStyle = '#0a101d';
    for (let x = 0; x < w; x += 40) {
      const bh = 40 + Math.sin(x * 0.12) * 28;
      ctx.fillRect(x, 510 - bh, 36, bh);

      // Tiny yellow/cyan lit windows
      ctx.fillStyle = 'rgba(56, 189, 248, 0.25)';
      ctx.fillRect(x + 8, 510 - bh + 10, 4, 6);
      ctx.fillRect(x + 20, 510 - bh + 10, 4, 6);
      ctx.fillStyle = '#0a101d';
    }

    // City Energy Grid Line (Glow)
    ctx.save();
    ctx.strokeStyle = gridHp > 40 ? '#10b981' : '#ef4444';
    ctx.lineWidth = 3;
    ctx.shadowColor = gridHp > 40 ? '#10b981' : '#ef4444';
    ctx.shadowBlur = 10;
    ctx.beginPath();
    ctx.moveTo(0, 510);
    ctx.lineTo(w, 510);
    ctx.stroke();
    ctx.shadowBlur = 0;
    ctx.restore();

    // Draw Flame Particles
    flameParticles.forEach(f => {
      ctx.save();
      ctx.fillStyle = f.color;
      ctx.globalAlpha = Math.max(0, f.alpha);
      ctx.beginPath();
      ctx.arc(f.x, f.y, f.radius, 0, Math.PI * 2);
      ctx.fill();
      ctx.restore();
    });

    // Draw Railgun Beams
    railgunBeams.forEach(b => {
      ctx.save();
      ctx.strokeStyle = b.color;
      ctx.globalAlpha = b.alpha;
      ctx.lineWidth = 4;
      ctx.shadowColor = b.color;
      ctx.shadowBlur = 15;
      ctx.beginPath();
      ctx.moveTo(b.startX, b.startY);
      ctx.lineTo(b.targetX, b.targetY);
      ctx.stroke();

      // Core white ray
      ctx.strokeStyle = '#ffffff';
      ctx.lineWidth = 1.5;
      ctx.stroke();
      ctx.restore();
    });

    // Draw Meteors
    meteors.forEach(m => {
      drawMeteor(m);
    });

    // Draw Rock Debris
    rockDebris.forEach(d => {
      ctx.save();
      ctx.fillStyle = d.color;
      ctx.globalAlpha = Math.max(0, d.alpha);
      ctx.beginPath();
      ctx.arc(d.x, d.y, d.radius, 0, Math.PI * 2);
      ctx.fill();
      ctx.restore();
    });

    // Draw Dual Railgun Battery Turret at base
    ctx.save();
    ctx.translate(turret.x, turret.y);
    ctx.rotate(turret.angle);

    // Twin Railgun Barrels
    ctx.fillStyle = '#38bdf8';
    ctx.fillRect(0, -9, 36, 6);
    ctx.fillRect(0, 3, 36, 6);

    ctx.rotate(-turret.angle);

    // Base Dome
    ctx.beginPath();
    ctx.arc(0, 0, 26, Math.PI, 0, false);
    ctx.fillStyle = '#0f172a';
    ctx.fill();
    ctx.lineWidth = 3;
    ctx.strokeStyle = '#10b981';
    ctx.stroke();

    ctx.beginPath();
    ctx.arc(0, 0, 10, 0, Math.PI * 2);
    ctx.fillStyle = '#10b981';
    ctx.fill();
    ctx.restore();
  }

  // --- Vector Art: Fiery Meteor Asteroid ---
  function drawMeteor(m) {
    ctx.save();
    ctx.translate(m.x, m.y);
    ctx.rotate(m.rotation);

    // Outer Meteor Rock
    ctx.fillStyle = m.isEmp ? '#0f172a' : '#292524';
    ctx.strokeStyle = m.isEmp ? '#38bdf8' : '#f97316';
    ctx.lineWidth = 3;

    ctx.beginPath();
    const sides = 8;
    for (let i = 0; i < sides; i++) {
      const a = (i / sides) * Math.PI * 2;
      const r = m.radius + Math.sin(i * 3 + m.x) * 3;
      const px = Math.cos(a) * r;
      const py = Math.sin(a) * r;
      if (i === 0) ctx.moveTo(px, py);
      else ctx.lineTo(px, py);
    }
    ctx.closePath();
    ctx.fill();
    ctx.stroke();

    ctx.rotate(-m.rotation); // unrotate text

    // Render Text Inside Meteor
    ctx.font = 'bold 20px "JetBrains Mono", monospace';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';

    if (m.text.length > 1) {
      ctx.font = 'bold 15px "JetBrains Mono", monospace';
      const fullW = ctx.measureText(m.text).width;
      let startX = -fullW / 2;

      for (let i = 0; i < m.text.length; i++) {
        const char = m.text[i];
        const cw = ctx.measureText(char).width;
        ctx.fillStyle = (i < m.typedLen) ? '#10b981' : '#ffffff';
        ctx.fillText(char, startX + cw / 2, 0);
        startX += cw;
      }
    } else {
      ctx.fillStyle = m.isEmp ? '#38bdf8' : '#ffffff';
      ctx.fillText(m.text.toUpperCase(), 0, 0);
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

    // 1. If currently locked onto a multi-letter meteor
    if (activeMeteor) {
      const expectedChar = activeMeteor.text[activeMeteor.typedLen].toLowerCase();
      if (char === expectedChar) {
        correctKeystrokes++;
        activeMeteor.typedLen++;
        streak++;
        if (streak > highestStreak) highestStreak = streak;

        if (activeMeteor.typedLen >= activeMeteor.text.length) {
          destroyMeteor(activeMeteor);
          activeMeteor = null;
        }
        updateHUD();
        return;
      } else {
        streak = 0;
        updateHUD();
        return;
      }
    }

    // 2. Search for meteor starting with typed char (prioritize lowest on screen, closest to city)
    const sorted = [...meteors].sort((a, b) => b.y - a.y);
    const candidate = sorted.find(m => {
      if (m.text.length > 1) {
        return m.text[0].toLowerCase() === char;
      }
      return m.text.toLowerCase() === char;
    });

    if (candidate) {
      correctKeystrokes++;
      streak++;
      if (streak > highestStreak) highestStreak = streak;

      if (candidate.text.length > 1) {
        activeMeteor = candidate;
        activeMeteor.typedLen = 1;
        if (activeMeteor.typedLen >= activeMeteor.text.length) {
          destroyMeteor(activeMeteor);
          activeMeteor = null;
        }
      } else {
        destroyMeteor(candidate);
      }
      updateHUD();
    } else {
      streak = 0;
      updateHUD();
    }
  }

  function destroyMeteor(m) {
    fireRailgun(m);
    createDebris(m.x, m.y, m.isEmp);
    AudioEngine.playExplosion();

    // Check EMP Supernova
    if (m.isEmp) {
      triggerEmpWave();
    }

    const mult = streak >= 20 ? 3 : (streak >= 10 ? 2 : 1);
    const basePts = (m.text.length * 20 + wave * 5) * mult;
    score += basePts;

    const idx = meteors.indexOf(m);
    if (idx !== -1) {
      meteors.splice(idx, 1);
    }

    meteorsDestroyedInWave++;
    if (meteorsDestroyedInWave >= METEORS_PER_WAVE) {
      nextWave();
    }
  }

  function triggerEmpWave() {
    AudioEngine.playEmpWave();
    // Wipe all visible meteors!
    meteors.forEach(met => {
      createDebris(met.x, met.y, true);
      score += 50;
    });
    meteors = [];
    activeMeteor = null;
  }

  function nextWave() {
    wave++;
    meteorsDestroyedInWave = 0;
    gridHp = Math.min(100, gridHp + 20); // repair grid
    updateHUD();
  }

  function updateHUD() {
    hudScore.textContent = score.toLocaleString();
    hudWave.textContent = wave;
    hudStreak.textContent = streak;

    const mult = streak >= 20 ? 3 : (streak >= 10 ? 2 : 1);
    hudMultiplier.textContent = `${mult}x`;

    const acc = totalKeystrokes > 0 ? ((correctKeystrokes / totalKeystrokes) * 100).toFixed(0) : '100';
    hudAccuracy.textContent = `${acc}%`;

    hudGridFill.style.width = `${gridHp}%`;
    hudGridText.textContent = `${gridHp}%`;
    hudGridText.style.color = gridHp > 40 ? '#10b981' : '#ef4444';
  }

  // --- Lifecycle ---
  function startGame() {
    AudioEngine.init();

    score = 0;
    streak = 0;
    highestStreak = 0;
    totalKeystrokes = 0;
    correctKeystrokes = 0;
    gridHp = 100;
    wave = 1;
    meteorsDestroyedInWave = 0;

    meteors = [];
    railgunBeams = [];
    rockDebris = [];
    flameParticles = [];
    activeMeteor = null;
    spawnTimer = 0;

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

  function triggerGameOver(reason = 'Metropolitan grid collapsed.') {
    isRunning = false;
    if (animationId) cancelAnimationFrame(animationId);

    const acc = totalKeystrokes > 0 ? ((correctKeystrokes / totalKeystrokes) * 100).toFixed(1) : '100.0';

    finalScoreEl.textContent = score.toLocaleString();
    finalWaveEl.textContent = wave;
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
        game_slug: 'meteor',
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

  draw();
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
