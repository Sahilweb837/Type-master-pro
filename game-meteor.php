 <?php
/**
 * Custom Type Master Pro
 * Meteor Matrix Defense — Enhanced HD Arcade Edition
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
/* ============================================================
   METEOR MATRIX DEFENSE — HD ARCADE STYLES
   ============================================================ */
.mm-wrap {
  --mm-bg:        #040810;
  --mm-bg-2:      #080e1a;
  --mm-surface:   #0b1220;
  --mm-line:      #1a2740;
  --mm-line-2:    #233352;
  --mm-ink:       #e2e8f0;
  --mm-muted:     #64748b;
  --mm-muted-2:   #94a3b8;
  --mm-green:     #10b981;
  --mm-cyan:      #38bdf8;
  --mm-amber:     #f59e0b;
  --mm-red:       #ef4444;
  --mm-mono:      "JetBrains Mono", ui-monospace, monospace;
  --mm-sans:      "Inter", -apple-system, BlinkMacSystemFont, sans-serif;
  max-width: 1180px;
  margin: 0 auto;
  padding: 1rem;
  font-family: var(--mm-sans);
  color: var(--mm-ink);
  box-sizing: border-box;
}
.mm-wrap * { box-sizing: border-box; }

/* ---------- Top bar ---------- */
.mm-topbar {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1rem;
}
.mm-title-block { display: flex; align-items: center; gap: 0.85rem; }
.mm-back-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.5rem 0.9rem;
  background: var(--mm-surface);
  border: 1px solid var(--mm-line);
  border-radius: 8px;
  color: var(--mm-ink);
  text-decoration: none;
  font-size: 0.78rem;
  font-weight: 600;
  transition: all 0.15s ease;
}
.mm-back-btn:hover {
  background: var(--mm-bg-2);
  border-color: var(--mm-line-2);
  color: var(--mm-ink);
  transform: translateX(-2px);
}
.mm-title-text h1 {
  font-size: 1.35rem;
  font-weight: 800;
  letter-spacing: -0.03em;
  margin: 0;
  color: var(--mm-ink);
  line-height: 1.1;
}
.mm-title-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  font-family: var(--mm-mono);
  font-size: 0.6rem;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: var(--mm-green);
  margin-bottom: 0.25rem;
}

.mm-top-actions {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.mm-record-pill {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  padding: 0.5rem 0.85rem;
  background: var(--mm-surface);
  border: 1px solid var(--mm-line);
  border-radius: 8px;
  font-family: var(--mm-mono);
  font-size: 0.72rem;
  color: var(--mm-muted-2);
}
.mm-record-pill strong {
  color: var(--mm-green);
  font-weight: 800;
}
.mm-icon-btn {
  width: 38px;
  height: 38px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: var(--mm-surface);
  border: 1px solid var(--mm-line);
  border-radius: 8px;
  color: var(--mm-ink);
  cursor: pointer;
  font-size: 1rem;
  transition: all 0.15s ease;
  -webkit-appearance: none;
  appearance: none;
  flex-shrink: 0;
}
.mm-icon-btn:hover {
  border-color: var(--mm-line-2);
  background: var(--mm-bg-2);
  transform: translateY(-1px);
}

/* ---------- HUD ---------- */
.mm-hud {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  align-items: center;
  gap: 1rem;
  padding: 0.85rem 1.25rem;
  background: var(--mm-surface);
  border: 1px solid var(--mm-line);
  border-bottom: none;
  border-radius: 14px 14px 0 0;
}
.mm-hud-group {
  display: flex;
  align-items: center;
  gap: 1.25rem;
  flex-wrap: wrap;
}
.mm-hud-item {
  display: flex;
  flex-direction: column;
  gap: 0.15rem;
  min-width: 62px;
}
.mm-hud-label {
  font-family: var(--mm-mono);
  font-size: 0.58rem;
  font-weight: 600;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: var(--mm-muted);
}
.mm-hud-value {
  font-family: var(--mm-mono);
  font-size: 1.1rem;
  font-weight: 800;
  color: var(--mm-ink);
  letter-spacing: -0.01em;
  line-height: 1;
}
.mm-hud-value.score { color: var(--mm-cyan); }
.mm-hud-value.wave  { color: var(--mm-cyan); }
.mm-hud-value.streak{ color: var(--mm-amber); }
.mm-hud-value.acc   { color: var(--mm-green); }

.mm-mult-badge {
  display: inline-flex;
  align-items: center;
  padding: 0.1rem 0.4rem;
  font-family: var(--mm-mono);
  font-size: 0.62rem;
  font-weight: 800;
  background: var(--mm-amber);
  color: #0a0a0a;
  border-radius: 3px;
  line-height: 1;
  margin-left: 0.35rem;
  vertical-align: middle;
}

.mm-grid-meter {
  display: flex;
  align-items: center;
  gap: 0.6rem;
}
.mm-grid-bar {
  width: 130px;
  height: 8px;
  background: var(--mm-bg);
  border: 1px solid var(--mm-line);
  border-radius: 5px;
  overflow: hidden;
  position: relative;
}
.mm-grid-fill {
  height: 100%;
  width: 100%;
  background: linear-gradient(90deg, #ef4444 0%, #eab308 50%, #10b981 100%);
  transition: width 0.35s cubic-bezier(0.4, 0, 0.2, 1);
}
.mm-grid-text {
  font-family: var(--mm-mono);
  font-size: 0.78rem;
  font-weight: 700;
  color: var(--mm-green);
  min-width: 44px;
  text-align: right;
}

/* ---------- Stage ---------- */
.mm-stage {
  position: relative;
  background: var(--mm-bg);
  border-left: 1px solid var(--mm-line);
  border-right: 1px solid var(--mm-line);
  overflow: hidden;
  box-shadow: 0 24px 60px rgba(0,0,0,0.6);
}
.mm-stage canvas {
  display: block;
  width: 100%;
  height: 620px;
  cursor: crosshair;
  touch-action: none;
}
@media (max-width: 768px) {
  .mm-stage canvas { height: 520px; }
}
@media (max-width: 480px) {
  .mm-stage canvas { height: 460px; }
  .mm-hud { padding: 0.7rem 0.9rem; }
  .mm-hud-group { gap: 0.85rem; }
  .mm-hud-item { min-width: 52px; }
  .mm-hud-value { font-size: 0.95rem; }
  .mm-title-text h1 { font-size: 1.05rem; }
  .mm-grid-bar { width: 90px; }
}

/* ---------- Overlays ---------- */
.mm-overlay {
  position: absolute;
  inset: 0;
  display: none;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 2rem 1.25rem;
  background: radial-gradient(ellipse at center, rgba(4,8,16,0.9) 0%, rgba(2,4,8,0.97) 100%);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  z-index: 20;
  overflow-y: auto;
}
.mm-overlay.show { display: flex; }

.mm-overlay-eyebrow {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  font-family: var(--mm-mono);
  font-size: 0.65rem;
  font-weight: 700;
  letter-spacing: 0.18em;
  text-transform: uppercase;
  color: var(--mm-green);
  padding: 0.35rem 0.85rem;
  background: rgba(16,185,129,0.12);
  border: 1px solid rgba(16,185,129,0.3);
  border-radius: 999px;
  margin-bottom: 1rem;
}
.mm-overlay-eyebrow.danger {
  color: var(--mm-red);
  background: rgba(239,68,68,0.12);
  border-color: rgba(239,68,68,0.3);
}

.mm-overlay h2 {
  font-family: var(--mm-sans);
  font-size: clamp(1.8rem, 4.5vw, 2.75rem);
  font-weight: 900;
  letter-spacing: -0.035em;
  line-height: 1.05;
  margin: 0 0 0.75rem;
  background: linear-gradient(135deg, #10b981 0%, #38bdf8 50%, #f59e0b 100%);
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
  color: transparent;
}
.mm-overlay h2.danger {
  background: linear-gradient(135deg, #ef4444 0%, #f97316 100%);
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
}
.mm-overlay-desc {
  font-size: 0.92rem;
  line-height: 1.65;
  color: var(--mm-muted-2);
  max-width: 540px;
  margin: 0 0 1.5rem;
}

/* Mode picker */
.mm-picker-section {
  display: flex;
  align-items: center;
  gap: 0.55rem;
  flex-wrap: wrap;
  justify-content: center;
  margin-bottom: 0.9rem;
}
.mm-picker-label {
  font-family: var(--mm-mono);
  font-size: 0.65rem;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: var(--mm-muted);
}
.mm-pill {
  padding: 0.4rem 0.85rem;
  background: var(--mm-surface);
  border: 1px solid var(--mm-line);
  border-radius: 999px;
  color: var(--mm-muted-2);
  font-family: var(--mm-sans);
  font-size: 0.76rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s ease;
  -webkit-appearance: none;
  appearance: none;
  white-space: nowrap;
}
.mm-pill:hover {
  border-color: var(--mm-green);
  color: var(--mm-green);
}
.mm-pill.active {
  background: var(--mm-green);
  border-color: var(--mm-green);
  color: #0a0a0a;
  font-weight: 800;
}

/* Primary CTA */
.mm-cta {
  display: inline-flex;
  align-items: center;
  gap: 0.55rem;
  padding: 0.9rem 1.9rem;
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: #0a0a0a;
  border: none;
  border-radius: 10px;
  font-family: var(--mm-sans);
  font-size: 0.95rem;
  font-weight: 800;
  letter-spacing: 0.02em;
  cursor: pointer;
  transition: all 0.2s ease;
  box-shadow: 0 4px 18px rgba(16,185,129,0.35);
  -webkit-appearance: none;
  appearance: none;
  margin-top: 0.5rem;
}
.mm-cta:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 28px rgba(16,185,129,0.5);
}
.mm-cta:active {
  transform: translateY(0) scale(0.98);
}

.mm-cta-secondary {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.85rem 1.4rem;
  background: transparent;
  color: var(--mm-ink);
  border: 1px solid var(--mm-line);
  border-radius: 10px;
  font-family: var(--mm-sans);
  font-size: 0.9rem;
  font-weight: 700;
  text-decoration: none;
  cursor: pointer;
  transition: all 0.15s ease;
  -webkit-appearance: none;
  appearance: none;
}
.mm-cta-secondary:hover {
  background: var(--mm-surface);
  border-color: var(--mm-line-2);
  color: var(--mm-ink);
}

.mm-tip-note {
  font-size: 0.78rem;
  color: var(--mm-muted);
  margin-top: 1.5rem;
  max-width: 520px;
  line-height: 1.6;
}

/* Game over results */
.mm-results {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 0.75rem;
  max-width: 600px;
  width: 100%;
  margin: 0.5rem 0 1.25rem;
}
.mm-result-card {
  background: var(--mm-surface);
  border: 1px solid var(--mm-line);
  border-radius: 10px;
  padding: 0.9rem 0.6rem;
  text-align: center;
}
.mm-result-label {
  font-family: var(--mm-mono);
  font-size: 0.56rem;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: var(--mm-muted);
  margin-bottom: 0.4rem;
}
.mm-result-value {
  font-family: var(--mm-mono);
  font-size: 1.45rem;
  font-weight: 800;
  letter-spacing: -0.02em;
  line-height: 1;
  color: var(--mm-ink);
}
.mm-result-value.green { color: var(--mm-green); }
.mm-result-value.cyan  { color: var(--mm-cyan); }
.mm-result-value.amber { color: var(--mm-amber); }
.mm-result-value.blue  { color: #60a5fa; }
@media (max-width: 575px) {
  .mm-results { grid-template-columns: repeat(2, 1fr); max-width: 340px; }
}

.mm-save-feedback {
  display: none;
  align-items: center;
  justify-content: center;
  gap: 0.4rem;
  padding: 0.5rem 1rem;
  margin-bottom: 1rem;
  background: rgba(16,185,129,0.12);
  border: 1px solid rgba(16,185,129,0.3);
  border-radius: 8px;
  color: var(--mm-green);
  font-size: 0.78rem;
  font-weight: 700;
}
.mm-save-feedback.show { display: inline-flex; }

/* ---------- Bottom control strip ---------- */
.mm-bottom-strip {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  align-items: center;
  gap: 0.75rem;
  padding: 0.85rem 1.25rem;
  background: var(--mm-surface);
  border: 1px solid var(--mm-line);
  border-top: none;
  border-radius: 0 0 14px 14px;
  font-size: 0.78rem;
}
.mm-controls-hint {
  display: flex;
  align-items: center;
  gap: 0.85rem;
  flex-wrap: wrap;
  color: var(--mm-muted-2);
}
.mm-controls-hint kbd {
  font-family: var(--mm-mono);
  font-size: 0.68rem;
  background: rgba(255,255,255,0.06);
  border: 1px solid rgba(255,255,255,0.12);
  border-radius: 4px;
  padding: 0.1rem 0.4rem;
  color: var(--mm-ink);
  font-weight: 700;
}
.mm-game-links {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  flex-wrap: wrap;
}
.mm-mini-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  padding: 0.4rem 0.75rem;
  background: transparent;
  border: 1px solid var(--mm-line);
  border-radius: 6px;
  color: var(--mm-ink);
  font-size: 0.72rem;
  font-weight: 600;
  text-decoration: none;
  cursor: pointer;
  transition: all 0.15s ease;
  -webkit-appearance: none;
  appearance: none;
  font-family: inherit;
}
.mm-mini-btn:hover {
  background: var(--mm-bg-2);
  border-color: var(--mm-line-2);
  color: var(--mm-ink);
}
.mm-mini-btn i { font-size: 0.85rem; }
@media (max-width: 575px) {
  .mm-bottom-strip { padding: 0.7rem 0.85rem; }
  .mm-controls-hint { gap: 0.5rem; font-size: 0.72rem; }
}
</style>

<div class="mm-wrap">

  <!-- ============ TOP BAR ============ -->
  <div class="mm-topbar">
    <div class="mm-title-block">
      <a href="<?= BASE_URL ?>/games.php" class="mm-back-btn">
        <i class="bi bi-arrow-left"></i> Arcade
      </a>
      <div class="mm-title-text">
        <div class="mm-title-badge">
          <i class="bi bi-fire"></i> PLANETARY RAILGUN
        </div>
        <h1>Meteor Matrix Defense</h1>
      </div>
    </div>

    <div class="mm-top-actions">
      <div class="mm-record-pill d-none d-md-flex">
        <i class="bi bi-trophy-fill" style="color:var(--mm-amber);"></i>
        <span>Best: <strong id="topScoreDisplay"><?= number_format($highScore['score']) ?></strong></span>
      </div>
      <button class="mm-icon-btn" id="btnToggleSound" title="Toggle Sound FX" type="button">
        <i class="bi bi-volume-up-fill" id="soundIcon"></i>
      </button>
    </div>
  </div>

  <!-- ============ HUD ============ -->
  <div class="mm-hud">
    <div class="mm-hud-group">
      <div class="mm-hud-item">
        <div class="mm-hud-label">Score</div>
        <div class="mm-hud-value score" id="hudScore">0</div>
      </div>
      <div class="mm-hud-item">
        <div class="mm-hud-label">Wave</div>
        <div class="mm-hud-value wave" id="hudWave">1</div>
      </div>
      <div class="mm-hud-item">
        <div class="mm-hud-label">Streak</div>
        <div class="mm-hud-value streak">
          <span id="hudStreak">0</span>
          <span class="mm-mult-badge" id="hudMultiplier">1x</span>
        </div>
      </div>
    </div>

    <div class="mm-hud-group">
      <div class="mm-hud-item">
        <div class="mm-hud-label">Accuracy</div>
        <div class="mm-hud-value acc" id="hudAccuracy">100%</div>
      </div>
      <div class="mm-hud-item" style="min-width:auto;">
        <div class="mm-hud-label">Grid Integrity</div>
        <div class="mm-grid-meter">
          <div class="mm-grid-bar">
            <div class="mm-grid-fill" id="hudGridFill"></div>
          </div>
          <div class="mm-grid-text" id="hudGridText">100%</div>
        </div>
      </div>
    </div>
  </div>

  <!-- ============ STAGE ============ -->
  <div class="mm-stage">
    <canvas id="meteorCanvas"></canvas>

    <!-- Start overlay -->
    <div class="mm-overlay show" id="startOverlay">
      <div class="mm-overlay-eyebrow">
        <i class="bi bi-shield-shaded"></i> PLANETARY RAILGUN DEFENSE
      </div>
      <h2>Meteor Matrix Defense</h2>
      <p class="mm-overlay-desc">
        A catastrophic meteor shower is descending upon the metropolitan energy grid. Type the characters on each falling meteor to fire plasma railgun rounds before ground impact.
      </p>

      <div class="mm-picker-section">
        <span class="mm-picker-label">Target</span>
        <button type="button" class="mm-pill active" data-mode="letters">Letters (A–Z)</button>
        <button type="button" class="mm-pill" data-mode="words">Short Words</button>
        <button type="button" class="mm-pill" data-mode="emp">EMP Supernova</button>
      </div>

      <div class="mm-picker-section">
        <span class="mm-picker-label">Threat</span>
        <button type="button" class="mm-pill active" data-diff="easy">Normal</button>
        <button type="button" class="mm-pill" data-diff="medium">Veteran</button>
        <button type="button" class="mm-pill" data-diff="hard">Armageddon</button>
      </div>

      <button type="button" class="mm-cta" id="btnStartGame">
        <i class="bi bi-shield-fill-check"></i> Engage Railgun
      </button>

      <p class="mm-tip-note">
        <i class="bi bi-lightning-charge-fill" style="color:var(--mm-cyan);"></i>
        Look out for <strong>blue EMP meteors</strong>! Typing them detonates every active meteor instantly.
      </p>
    </div>

    <!-- Pause overlay -->
    <div class="mm-overlay" id="pauseOverlay">
      <div class="mm-overlay-eyebrow">
        <i class="bi bi-pause-fill"></i> SYSTEM PAUSED
      </div>
      <h2>Standby</h2>
      <p class="mm-overlay-desc">Targeting sensors holding position. Resume when you're ready.</p>
      <div style="display:flex;gap:0.6rem;flex-wrap:wrap;justify-content:center;">
        <button type="button" class="mm-cta" id="btnResumeGame">
          <i class="bi bi-play-fill"></i> Resume
        </button>
        <button type="button" class="mm-cta-secondary" id="btnRestartFromPause">
          <i class="bi bi-arrow-clockwise"></i> Restart
        </button>
      </div>
    </div>

    <!-- Game over overlay -->
    <div class="mm-overlay" id="gameOverOverlay">
      <div class="mm-overlay-eyebrow danger">
        <i class="bi bi-exclamation-triangle-fill"></i> GRID COLLAPSE
      </div>
      <h2 class="danger">Grid Breached</h2>
      <p class="mm-overlay-desc" id="gameOverReason">The central energy grid sustained terminal impact damage.</p>

      <div class="mm-results">
        <div class="mm-result-card">
          <div class="mm-result-label">Score</div>
          <div class="mm-result-value green" id="finalScore">0</div>
        </div>
        <div class="mm-result-card">
          <div class="mm-result-label">Waves</div>
          <div class="mm-result-value cyan" id="finalWave">1</div>
        </div>
        <div class="mm-result-card">
          <div class="mm-result-label">Best Streak</div>
          <div class="mm-result-value amber" id="finalStreak">0</div>
        </div>
        <div class="mm-result-card">
          <div class="mm-result-label">Accuracy</div>
          <div class="mm-result-value blue" id="finalAccuracy">100%</div>
        </div>
      </div>

      <div class="mm-save-feedback" id="saveScoreFeedback">
        <i class="bi bi-cloud-check-fill"></i>
        <span id="saveFeedbackText">High score saved to leaderboard!</span>
      </div>

      <div style="display:flex;gap:0.6rem;flex-wrap:wrap;justify-content:center;">
        <button type="button" class="mm-cta" id="btnPlayAgain">
          <i class="bi bi-arrow-repeat"></i> Deploy Again
        </button>
        <a href="<?= BASE_URL ?>/games.php" class="mm-cta-secondary">
          <i class="bi bi-grid-3x3-gap"></i> Arcade Hub
        </a>
      </div>
    </div>
  </div>

  <!-- ============ BOTTOM STRIP ============ -->
  <div class="mm-bottom-strip">
    <div class="mm-controls-hint">
      <span><i class="bi bi-info-circle"></i></span>
      <span><kbd>A–Z</kbd> Type Target</span>
      <span><kbd>ESC</kbd> Pause</span>
    </div>

    <div class="mm-game-links">
      <a href="<?= BASE_URL ?>/game-ninja.php" class="mm-mini-btn">
        <i class="bi bi-slash-circle-fill" style="color:#ff4d00;"></i> Ninja
      </a>
      <a href="<?= BASE_URL ?>/game-space.php" class="mm-mini-btn">
        <i class="bi bi-rocket-takeoff-fill" style="color:#38bdf8;"></i> Space
      </a>
      <button type="button" class="mm-mini-btn" id="btnRestartGame">
        <i class="bi bi-arrow-clockwise"></i> Restart
      </button>
    </div>
  </div>

</div>

<!-- ============================================================
     HD ENGINE
     ============================================================ -->
<script>
(function() {
  'use strict';

  /* ---------- Audio Engine ---------- */
  const AudioEngine = {
    ctx: null,
    enabled: true,
    init() {
      if (!this.ctx) {
        const AC = window.AudioContext || window.webkitAudioContext;
        if (AC) this.ctx = new AC();
      }
      if (this.ctx && this.ctx.state === 'suspended') this.ctx.resume();
    },
    toggle() {
      this.enabled = !this.enabled;
      return this.enabled;
    },
    playRailgun() {
      if (!this.enabled || !this.ctx) return;
      const t = this.ctx.currentTime;
      [880, 440].forEach((freq, i) => {
        const osc = this.ctx.createOscillator();
        const gain = this.ctx.createGain();
        osc.type = 'sawtooth';
        osc.frequency.setValueAtTime(freq, t);
        osc.frequency.exponentialRampToValueAtTime(70, t + 0.14);
        gain.gain.setValueAtTime(0.18 - i * 0.06, t);
        gain.gain.exponentialRampToValueAtTime(0.001, t + 0.14);
        osc.connect(gain).connect(this.ctx.destination);
        osc.start(t);
        osc.stop(t + 0.15);
      });
    },
    playExplosion() {
      if (!this.enabled || !this.ctx) return;
      const now = this.ctx.currentTime;
      const size = this.ctx.sampleRate * 0.3;
      const buffer = this.ctx.createBuffer(1, size, this.ctx.sampleRate);
      const out = buffer.getChannelData(0);
      for (let i = 0; i < size; i++) out[i] = (Math.random() * 2 - 1) * (1 - i / size);
      const noise = this.ctx.createBufferSource();
      noise.buffer = buffer;
      const filter = this.ctx.createBiquadFilter();
      filter.type = 'lowpass';
      filter.frequency.setValueAtTime(500, now);
      filter.frequency.exponentialRampToValueAtTime(60, now + 0.3);
      const gain = this.ctx.createGain();
      gain.gain.setValueAtTime(0.28, now);
      gain.gain.exponentialRampToValueAtTime(0.001, now + 0.3);
      noise.connect(filter).connect(gain).connect(this.ctx.destination);
      noise.start(now);
      noise.stop(now + 0.3);
    },
    playEmpWave() {
      if (!this.enabled || !this.ctx) return;
      const now = this.ctx.currentTime;
      const osc = this.ctx.createOscillator();
      const gain = this.ctx.createGain();
      osc.type = 'sine';
      osc.frequency.setValueAtTime(180, now);
      osc.frequency.linearRampToValueAtTime(950, now + 0.35);
      gain.gain.setValueAtTime(0.22, now);
      gain.gain.exponentialRampToValueAtTime(0.001, now + 0.35);
      osc.connect(gain).connect(this.ctx.destination);
      osc.start(now);
      osc.stop(now + 0.35);
    },
    playImpact() {
      if (!this.enabled || !this.ctx) return;
      const now = this.ctx.currentTime;
      const osc = this.ctx.createOscillator();
      const gain = this.ctx.createGain();
      osc.type = 'square';
      osc.frequency.setValueAtTime(120, now);
      osc.frequency.exponentialRampToValueAtTime(35, now + 0.35);
      gain.gain.setValueAtTime(0.3, now);
      gain.gain.exponentialRampToValueAtTime(0.001, now + 0.35);
      osc.connect(gain).connect(this.ctx.destination);
      osc.start(now);
      osc.stop(now + 0.35);
    }
  };

  /* ---------- Word banks ---------- */
  const ALPHABET = 'abcdefghijklmnopqrstuvwxyz'.split('');
  const SHORT_WORDS = [
    "core","fire","dust","rock","fall","nova","iron","flux",
    "burn","atom","blast","comet","spark","pulse","solar","blaze"
  ];

  /* ---------- Canvas setup ---------- */
  const canvas = document.getElementById('meteorCanvas');
  const ctx = canvas.getContext('2d', { alpha: false });
  let W = 1000, H = 620;

  function resizeCanvas() {
    const dpr = Math.min(window.devicePixelRatio || 1, 2);
    const rect = canvas.getBoundingClientRect();
    W = rect.width;
    H = rect.height;
    canvas.width = Math.floor(W * dpr);
    canvas.height = Math.floor(H * dpr);
    canvas.style.width = W + 'px';
    canvas.style.height = H + 'px';
    ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    turret.x = W / 2;
    turret.y = H - 55;
  }
  window.addEventListener('resize', () => {
    clearTimeout(window.__mmResize);
    window.__mmResize = setTimeout(resizeCanvas, 120);
  });

  /* ---------- State ---------- */
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
  let shockwaves = [];
  let stars = [];
  let activeMeteor = null;
  let spawnTimer = 0;
  let screenShake = 0;

  const turret = { x: 500, y: 565, angle: -Math.PI / 2 };

  /* ---------- DOM ---------- */
  const hudScore       = document.getElementById('hudScore');
  const hudWave        = document.getElementById('hudWave');
  const hudStreak      = document.getElementById('hudStreak');
  const hudMultiplier  = document.getElementById('hudMultiplier');
  const hudAccuracy    = document.getElementById('hudAccuracy');
  const hudGridFill    = document.getElementById('hudGridFill');
  const hudGridText    = document.getElementById('hudGridText');

  const startOverlay   = document.getElementById('startOverlay');
  const pauseOverlay   = document.getElementById('pauseOverlay');
  const gameOverOverlay= document.getElementById('gameOverOverlay');

  const finalScoreEl   = document.getElementById('finalScore');
  const finalWaveEl    = document.getElementById('finalWave');
  const finalStreakEl  = document.getElementById('finalStreak');
  const finalAccuracyEl= document.getElementById('finalAccuracy');
  const saveFeedback   = document.getElementById('saveScoreFeedback');
  const saveFeedbackTxt= document.getElementById('saveFeedbackText');

  /* ---------- Star field ---------- */
  function initStars() {
    stars = [];
    const count = Math.floor((W * H) / 8000);
    for (let i = 0; i < count; i++) {
      stars.push({
        x: Math.random() * W,
        y: Math.random() * H * 0.85,
        r: Math.random() * 1.4 + 0.2,
        a: Math.random() * 0.6 + 0.2,
        tw: Math.random() * Math.PI * 2,
        tws: Math.random() * 0.03 + 0.008
      });
    }
  }

  /* ---------- Meteor spawning ---------- */
  function getSpeedMultiplier() {
    let m = 1.0;
    if (currentDiff === 'easy') m = 0.75;
    if (currentDiff === 'medium') m = 1.15;
    if (currentDiff === 'hard') m = 1.55;
    return m + (wave - 1) * 0.08;
  }

  function spawnMeteor() {
    const margin = 80;
    const startX = margin + Math.random() * (W - margin * 2);
    const targetX = margin + Math.random() * (W - margin * 2);

    let text = '';
    let isEmp = false;

    if (currentMode === 'emp' && Math.random() < 0.22) {
      text = 'emp';
      isEmp = true;
    } else if (currentMode === 'words') {
      text = SHORT_WORDS[Math.floor(Math.random() * SHORT_WORDS.length)];
    } else {
      text = ALPHABET[Math.floor(Math.random() * ALPHABET.length)];
    }

    const angle = Math.atan2((H - 110) - (-30), targetX - startX);
    const speed = (0.55 + Math.random() * 0.35) * getSpeedMultiplier() * (H / 580);

    meteors.push({
      x: startX,
      y: -40,
      vx: Math.cos(angle) * speed,
      vy: Math.sin(angle) * speed,
      rotation: Math.random() * Math.PI * 2,
      rotSpeed: (Math.random() - 0.5) * 0.05,
      radius: text.length > 1 ? 26 + text.length * 3.5 : 26,
      text,
      typedLen: 0,
      isEmp,
      bob: Math.random() * Math.PI * 2
    });
  }

  /* ---------- FX ---------- */
  function createFlameParticle(x, y, isEmp = false) {
    flameParticles.push({
      x: x + (Math.random() * 10 - 5),
      y: y + (Math.random() * 10 - 5),
      vx: (Math.random() - 0.5) * 1.8,
      vy: -Math.random() * 2.2 - 0.4,
      radius: Math.random() * 5 + 2,
      color: isEmp
        ? (Math.random() > 0.5 ? '#38bdf8' : '#60a5fa')
        : (Math.random() > 0.5 ? '#f97316' : '#facc15'),
      alpha: 0.9
    });
  }

  function createDebris(x, y, isEmp = false) {
    const count = 28;
    for (let i = 0; i < count; i++) {
      const a = Math.random() * Math.PI * 2;
      const s = Math.random() * 5 + 1.5;
      rockDebris.push({
        x, y,
        vx: Math.cos(a) * s,
        vy: Math.sin(a) * s,
        radius: Math.random() * 4 + 1.5,
        color: isEmp ? '#38bdf8' : (Math.random() > 0.4 ? '#78716c' : '#f97316'),
        alpha: 1.0,
        decay: Math.random() * 0.03 + 0.015
      });
    }
  }

  function createShockwave(x, y, color) {
    shockwaves.push({
      x, y,
      radius: 6,
      maxRadius: 140,
      alpha: 1.0,
      color: color || '#38bdf8'
    });
  }

  function fireRailgun(target) {
    turret.angle = Math.atan2(target.y - turret.y, target.x - turret.x);
    railgunBeams.push({
      x1: turret.x, y1: turret.y,
      x2: target.x, y2: target.y,
      alpha: 1.0,
      color: target.isEmp ? '#38bdf8' : '#10b981'
    });
    AudioEngine.playRailgun();
  }

  /* ---------- Loop ---------- */
  function gameLoop() {
    if (!isRunning || isPaused) return;
    update();
    draw();
    animationId = requestAnimationFrame(gameLoop);
  }

  function update() {
    // Spawn
    spawnTimer++;
    const pace = currentDiff === 'easy' ? 115 : currentDiff === 'medium' ? 85 : 58;
    if (spawnTimer >= pace) {
      spawnTimer = 0;
      spawnMeteor();
    }

    // Meteors
    for (let i = meteors.length - 1; i >= 0; i--) {
      const m = meteors[i];
      m.x += m.vx;
      m.y += m.vy;
      m.rotation += m.rotSpeed;
      m.bob += 0.05;

      if (Math.random() < 0.55) createFlameParticle(m.x, m.y, m.isEmp);

      if (m.y >= H - 90) {
        AudioEngine.playImpact();
        createDebris(m.x, H - 90, m.isEmp);
        createShockwave(m.x, H - 90, '#ef4444');
        screenShake = 18;
        gridHp = Math.max(0, gridHp - 20);
        streak = 0;
        updateHUD();

        if (activeMeteor === m) activeMeteor = null;
        meteors.splice(i, 1);

        if (gridHp <= 0) {
          triggerGameOver('The central energy grid sustained catastrophic impact failure.');
          return;
        }
      }
    }

    // Beams
    for (let i = railgunBeams.length - 1; i >= 0; i--) {
      railgunBeams[i].alpha -= 0.16;
      if (railgunBeams[i].alpha <= 0) railgunBeams.splice(i, 1);
    }

    // Flames
    for (let i = flameParticles.length - 1; i >= 0; i--) {
      const f = flameParticles[i];
      f.x += f.vx;
      f.y += f.vy;
      f.alpha -= 0.038;
      f.radius *= 0.96;
      if (f.alpha <= 0) flameParticles.splice(i, 1);
    }

    // Debris
    for (let i = rockDebris.length - 1; i >= 0; i--) {
      const d = rockDebris[i];
      d.x += d.vx;
      d.y += d.vy;
      d.vy += 0.12;
      d.alpha -= d.decay;
      if (d.alpha <= 0) rockDebris.splice(i, 1);
    }

    // Shockwaves
    for (let i = shockwaves.length - 1; i >= 0; i--) {
      const s = shockwaves[i];
      s.radius += 4;
      s.alpha -= 0.03;
      if (s.alpha <= 0 || s.radius >= s.maxRadius) shockwaves.splice(i, 1);
    }

    // Stars twinkle
    for (const s of stars) s.tw += s.tws;

    if (screenShake > 0.4) screenShake *= 0.88;
    else screenShake = 0;
  }

  /* ---------- Draw ---------- */
  function draw() {
    // Screen shake
    ctx.save();
    if (screenShake > 0.5) {
      const sx = (Math.random() - 0.5) * screenShake;
      const sy = (Math.random() - 0.5) * screenShake;
      ctx.translate(sx, sy);
    }

    // Sky gradient
    const bg = ctx.createLinearGradient(0, 0, 0, H);
    bg.addColorStop(0, '#030712');
    bg.addColorStop(0.6, '#050d1f');
    bg.addColorStop(1, '#0a1526');
    ctx.fillStyle = bg;
    ctx.fillRect(0, 0, W, H);

    // Stars
    for (const s of stars) {
      const a = s.a * (0.55 + 0.45 * Math.sin(s.tw));
      ctx.globalAlpha = a;
      ctx.fillStyle = '#ffffff';
      ctx.beginPath();
      ctx.arc(s.x, s.y, s.r, 0, Math.PI * 2);
      ctx.fill();
    }
    ctx.globalAlpha = 1;

    // City skyline
    const groundY = H - 90;
    ctx.fillStyle = '#080d18';
    for (let x = 0; x < W; x += 40) {
      const bh = 40 + Math.sin(x * 0.12) * 28;
      ctx.fillRect(x, groundY - bh, 36, bh);

      // Windows
      ctx.fillStyle = 'rgba(56, 189, 248, 0.22)';
      for (let wy = groundY - bh + 10; wy < groundY - 10; wy += 14) {
        for (let wx = x + 6; wx < x + 32; wx += 10) {
          if (Math.random() > 0.55) {
            ctx.fillRect(wx, wy, 4, 5);
          }
        }
      }
      ctx.fillStyle = '#080d18';
    }

    // Grid line
    ctx.save();
    const gridColor = gridHp > 40 ? '#10b981' : '#ef4444';
    ctx.strokeStyle = gridColor;
    ctx.lineWidth = 3;
    ctx.shadowColor = gridColor;
    ctx.shadowBlur = 14;
    ctx.beginPath();
    ctx.moveTo(0, groundY);
    ctx.lineTo(W, groundY);
    ctx.stroke();
    ctx.shadowBlur = 0;
    ctx.restore();

    // Shockwaves
    for (const s of shockwaves) {
      ctx.save();
      ctx.globalAlpha = s.alpha;
      ctx.strokeStyle = s.color;
      ctx.lineWidth = 3;
      ctx.beginPath();
      ctx.arc(s.x, s.y, s.radius, 0, Math.PI * 2);
      ctx.stroke();
      ctx.restore();
    }

    // Flames
    for (const f of flameParticles) {
      ctx.save();
      ctx.fillStyle = f.color;
      ctx.globalAlpha = Math.max(0, f.alpha);
      ctx.shadowColor = f.color;
      ctx.shadowBlur = 8;
      ctx.beginPath();
      ctx.arc(f.x, f.y, f.radius, 0, Math.PI * 2);
      ctx.fill();
      ctx.restore();
    }

    // Railgun beams
    for (const b of railgunBeams) {
      ctx.save();
      ctx.globalAlpha = b.alpha;
      ctx.strokeStyle = b.color;
      ctx.lineWidth = 5;
      ctx.lineCap = 'round';
      ctx.shadowColor = b.color;
      ctx.shadowBlur = 18;
      ctx.beginPath();
      ctx.moveTo(b.x1, b.y1);
      ctx.lineTo(b.x2, b.y2);
      ctx.stroke();

      ctx.strokeStyle = '#ffffff';
      ctx.lineWidth = 1.8;
      ctx.shadowBlur = 0;
      ctx.stroke();
      ctx.restore();
    }

    // Meteors
    for (const m of meteors) drawMeteor(m);

    // Debris
    for (const d of rockDebris) {
      ctx.save();
      ctx.globalAlpha = Math.max(0, d.alpha);
      ctx.fillStyle = d.color;
      ctx.beginPath();
      ctx.arc(d.x, d.y, d.radius, 0, Math.PI * 2);
      ctx.fill();
      ctx.restore();
    }

    // Turret
    drawTurret();

    ctx.restore();
  }

  function drawTurret() {
    ctx.save();
    ctx.translate(turret.x, turret.y);
    ctx.rotate(turret.angle);

    // Barrels
    ctx.fillStyle = '#38bdf8';
    ctx.fillRect(0, -9, 38, 6);
    ctx.fillRect(0, 3, 38, 6);

    // Barrel glow
    ctx.shadowColor = '#38bdf8';
    ctx.shadowBlur = 12;
    ctx.fillStyle = '#7dd3fc';
    ctx.fillRect(34, -9, 4, 6);
    ctx.fillRect(34, 3, 4, 6);
    ctx.shadowBlur = 0;

    ctx.rotate(-turret.angle);

    // Dome
    const domeGrad = ctx.createLinearGradient(0, -26, 0, 0);
    domeGrad.addColorStop(0, '#1e293b');
    domeGrad.addColorStop(1, '#0f172a');
    ctx.fillStyle = domeGrad;
    ctx.beginPath();
    ctx.arc(0, 0, 26, Math.PI, 0, false);
    ctx.fill();
    ctx.strokeStyle = '#10b981';
    ctx.lineWidth = 3;
    ctx.stroke();

    // Core
    ctx.beginPath();
    ctx.arc(0, 0, 10, 0, Math.PI * 2);
    ctx.fillStyle = '#10b981';
    ctx.shadowColor = '#10b981';
    ctx.shadowBlur = 14;
    ctx.fill();
    ctx.shadowBlur = 0;

    ctx.restore();
  }

  function drawMeteor(m) {
    ctx.save();
    ctx.translate(m.x, m.y);
    ctx.rotate(m.rotation);

    // Aura
    const auraColor = m.isEmp ? '56, 189, 248' : '249, 115, 22';
    const aura = ctx.createRadialGradient(0, 0, m.radius * 0.5, 0, 0, m.radius * 2);
    aura.addColorStop(0, `rgba(${auraColor}, 0.35)`);
    aura.addColorStop(1, `rgba(${auraColor}, 0)`);
    ctx.fillStyle = aura;
    ctx.beginPath();
    ctx.arc(0, 0, m.radius * 2, 0, Math.PI * 2);
    ctx.fill();

    // Rock body
    const bodyGrad = ctx.createRadialGradient(-m.radius * 0.3, -m.radius * 0.3, 2, 0, 0, m.radius);
    if (m.isEmp) {
      bodyGrad.addColorStop(0, '#1e3a5f');
      bodyGrad.addColorStop(1, '#0f172a');
    } else {
      bodyGrad.addColorStop(0, '#4a3a2a');
      bodyGrad.addColorStop(1, '#1c1408');
    }
    ctx.fillStyle = bodyGrad;
    ctx.strokeStyle = m.isEmp ? '#38bdf8' : '#f97316';
    ctx.lineWidth = 2.5;

    ctx.beginPath();
    const sides = 8;
    for (let i = 0; i < sides; i++) {
      const a = (i / sides) * Math.PI * 2;
      const r = m.radius + Math.sin(i * 3 + m.bob) * 3;
      const px = Math.cos(a) * r;
      const py = Math.sin(a) * r;
      if (i === 0) ctx.moveTo(px, py);
      else ctx.lineTo(px, py);
    }
    ctx.closePath();
    ctx.fill();
    ctx.stroke();

    // Cracks
    ctx.strokeStyle = m.isEmp ? 'rgba(56,189,248,0.5)' : 'rgba(249,115,22,0.55)';
    ctx.lineWidth = 1.5;
    for (let i = 0; i < 3; i++) {
      const a = (i / 3) * Math.PI * 2 + m.bob * 0.1;
      ctx.beginPath();
      ctx.moveTo(0, 0);
      ctx.lineTo(Math.cos(a) * m.radius * 0.7, Math.sin(a) * m.radius * 0.7);
      ctx.stroke();
    }

    ctx.rotate(-m.rotation);

    // Text
    if (m.text.length > 1) {
      ctx.font = 'bold 15px "JetBrains Mono", monospace';
      ctx.textAlign = 'center';
      ctx.textBaseline = 'middle';
      const fullW = ctx.measureText(m.text).width;
      let startX = -fullW / 2;
      for (let i = 0; i < m.text.length; i++) {
        const ch = m.text[i];
        const cw = ctx.measureText(ch).width;
        ctx.fillStyle = (i < m.typedLen) ? '#10b981' : '#ffffff';
        ctx.fillText(ch, startX + cw / 2, 0);
        startX += cw;
      }
    } else {
      ctx.font = 'bold 22px "JetBrains Mono", monospace';
      ctx.textAlign = 'center';
      ctx.textBaseline = 'middle';
      ctx.fillStyle = m.isEmp ? '#38bdf8' : '#ffffff';
      ctx.shadowColor = 'rgba(0,0,0,0.9)';
      ctx.shadowBlur = 4;
      ctx.fillText(m.text.toUpperCase(), 0, 0);
      ctx.shadowBlur = 0;
    }

    ctx.restore();
  }

  /* ---------- Input ---------- */
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

    if (activeMeteor) {
      const expected = activeMeteor.text[activeMeteor.typedLen].toLowerCase();
      if (char === expected) {
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

    const sorted = [...meteors].sort((a, b) => b.y - a.y);
    const candidate = sorted.find(m =>
      m.text.length > 1 ? m.text[0].toLowerCase() === char : m.text.toLowerCase() === char
    );

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
    createShockwave(m.x, m.y, m.isEmp ? '#38bdf8' : '#10b981');
    AudioEngine.playExplosion();

    if (m.isEmp) triggerEmpWave();

    const mult = streak >= 20 ? 3 : (streak >= 10 ? 2 : 1);
    score += (m.text.length * 20 + wave * 5) * mult;

    const idx = meteors.indexOf(m);
    if (idx !== -1) meteors.splice(idx, 1);

    meteorsDestroyedInWave++;
    if (meteorsDestroyedInWave >= METEORS_PER_WAVE) nextWave();
  }

  function triggerEmpWave() {
    AudioEngine.playEmpWave();
    createShockwave(W / 2, H / 2, '#38bdf8');
    screenShake = 12;
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
    gridHp = Math.min(100, gridHp + 20);
    updateHUD();
  }

  function updateHUD() {
    hudScore.textContent = score.toLocaleString();
    hudWave.textContent = wave;
    hudStreak.textContent = streak;

    const mult = streak >= 20 ? 3 : (streak >= 10 ? 2 : 1);
    hudMultiplier.textContent = mult + 'x';

    const acc = totalKeystrokes > 0
      ? Math.round((correctKeystrokes / totalKeystrokes) * 100)
      : 100;
    hudAccuracy.textContent = acc + '%';

    hudGridFill.style.width = gridHp + '%';
    hudGridText.textContent = gridHp + '%';
    hudGridText.style.color = gridHp > 40 ? '#10b981' : '#ef4444';
  }

  /* ---------- Lifecycle ---------- */
  function startGame() {
    AudioEngine.init();

    score = 0; streak = 0; highestStreak = 0;
    totalKeystrokes = 0; correctKeystrokes = 0;
    gridHp = 100; wave = 1; meteorsDestroyedInWave = 0;

    meteors = []; railgunBeams = []; rockDebris = [];
    flameParticles = []; shockwaves = [];
    activeMeteor = null; spawnTimer = 0; screenShake = 0;

    updateHUD();
    startOverlay.classList.remove('show');
    pauseOverlay.classList.remove('show');
    gameOverOverlay.classList.remove('show');
    saveFeedback.classList.remove('show');

    isRunning = true;
    isPaused = false;

    if (animationId) cancelAnimationFrame(animationId);
    animationId = requestAnimationFrame(gameLoop);
  }

  function togglePause() {
    if (!isRunning) return;
    isPaused = !isPaused;
    if (isPaused) {
      pauseOverlay.classList.add('show');
      if (animationId) cancelAnimationFrame(animationId);
    } else {
      pauseOverlay.classList.remove('show');
      animationId = requestAnimationFrame(gameLoop);
    }
  }

  function triggerGameOver(reason) {
    isRunning = false;
    if (animationId) cancelAnimationFrame(animationId);

    const acc = totalKeystrokes > 0
      ? ((correctKeystrokes / totalKeystrokes) * 100).toFixed(1)
      : '100.0';

    finalScoreEl.textContent = score.toLocaleString();
    finalWaveEl.textContent = wave;
    finalStreakEl.textContent = highestStreak;
    finalAccuracyEl.textContent = acc + '%';
    document.getElementById('gameOverReason').textContent = reason;

    gameOverOverlay.classList.add('show');
    saveScore(score, highestStreak, parseFloat(acc), currentDiff, currentMode);
  }

  function saveScore(scoreVal, streakVal, accVal, diffVal, modeVal) {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    fetch('<?= BASE_URL ?>/api/save-game-score.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': csrf
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
    .then(r => r.json())
    .then(data => {
      if (data.status === 'success') {
        saveFeedback.classList.add('show');
        if (data.new_high_score) {
          saveFeedbackTxt.innerHTML = '<strong>New Personal Best!</strong> Score: ' + scoreVal.toLocaleString();
          const top = document.getElementById('topScoreDisplay');
          if (top) top.textContent = scoreVal.toLocaleString();
        } else {
          saveFeedbackTxt.textContent = 'Score saved to leaderboard!';
        }
      }
    })
    .catch(err => console.warn('Score save:', err));
  }

  /* ---------- Buttons ---------- */
  document.getElementById('btnStartGame').addEventListener('click', startGame);
  document.getElementById('btnPlayAgain').addEventListener('click', startGame);
  document.getElementById('btnRestartGame').addEventListener('click', startGame);
  document.getElementById('btnRestartFromPause').addEventListener('click', startGame);
  document.getElementById('btnResumeGame').addEventListener('click', togglePause);

  const btnToggleSound = document.getElementById('btnToggleSound');
  const soundIcon = document.getElementById('soundIcon');
  btnToggleSound.addEventListener('click', () => {
    AudioEngine.init();
    const on = AudioEngine.toggle();
    soundIcon.className = on ? 'bi bi-volume-up-fill' : 'bi bi-volume-mute-fill';
  });

  document.querySelectorAll('[data-mode]').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('[data-mode]').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      currentMode = btn.getAttribute('data-mode');
    });
  });

  document.querySelectorAll('[data-diff]').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('[data-diff]').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      currentDiff = btn.getAttribute('data-diff');
    });
  });

  /* ---------- Boot ---------- */
  resizeCanvas();
  initStars();
  draw();
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>