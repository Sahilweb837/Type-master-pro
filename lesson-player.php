<?php
/**
 * Custom Type Master Pro
 * Interactive Student Lesson Player — Pixel Theme, Clean Sans-Serif
 */

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/lessons_data.php';

$lessonId = trim($_GET['id'] ?? 'beginner-1');
$lesson = get_lesson_by_id($lessonId);

if (!$lesson) {
    header('Location: ' . BASE_URL . '/lessons.php');
    exit;
}

$pageTitle = 'Lesson ' . sprintf('%02d', $lesson['number']) . ': ' . $lesson['title'];
$currentUser = current_user();

include __DIR__ . '/includes/header.php';
?>

<style>
/* ============================================================
   LESSON PLAYER — PIXEL THEME + CLEAN SANS-SERIF
   ============================================================ */
.lesson-player-wrapper {
  --ink:        #000000;
  --paper:      #ffffff;
  --paper-soft: #f4f4f4;
  --paper-hard: #e0e0e0;
  --shadow:     3px;
  --sans:       "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
  --mono:       "JetBrains Mono", ui-monospace, Menlo, Consolas, monospace;

  font-family: var(--sans);
  color: var(--ink);
}

html[data-theme="dark"] .lesson-player-wrapper {
  --ink:        #ffffff;
  --paper:      #000000;
  --paper-soft: #141414;
  --paper-hard: #222222;
}

/* ---------- Top row ---------- */
.lesson-player-wrapper .lp-topbar {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 1rem;
}

.lesson-player-wrapper .lp-back-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.55rem 0.9rem;
  background: var(--paper);
  border: 3px solid var(--ink);
  box-shadow: var(--shadow) var(--shadow) 0 var(--ink);
  color: var(--ink);
  font-family: var(--sans);
  font-size: 0.82rem;
  font-weight: 600;
  letter-spacing: 0.02em;
  text-decoration: none;
  cursor: pointer;
  transition: transform 0.05s steps(1), box-shadow 0.05s steps(1);
}
.lesson-player-wrapper .lp-back-btn:hover { background: var(--paper-soft); color: var(--ink); }
.lesson-player-wrapper .lp-back-btn:active {
  transform: translate(var(--shadow), var(--shadow));
  box-shadow: 0 0 0 var(--ink);
}

/* ---------- Stage + title ---------- */
.lesson-player-wrapper .lp-stage-row {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0.35rem;
}
.lesson-player-wrapper .lp-stage-badge {
  font-family: var(--sans);
  font-size: 0.66rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  padding: 0.22rem 0.55rem;
  background: var(--ink);
  color: var(--paper);
  border: 2px solid var(--ink);
}
.lesson-player-wrapper .lp-stage-num {
  font-family: var(--mono);
  font-size: 0.78rem;
  font-weight: 700;
  color: var(--ink);
  opacity: 0.65;
}
.lesson-player-wrapper .lp-title {
  font-family: var(--sans);
  font-size: 1.35rem;
  font-weight: 800;
  letter-spacing: -0.02em;
  line-height: 1.2;
  margin: 0;
  color: var(--ink);
}

/* ---------- HUD pills ---------- */
.lesson-player-wrapper .lp-hud-group {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  flex-wrap: wrap;
}
.lesson-player-wrapper .lp-hud-pill {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  padding: 0.5rem 0.85rem;
  background: var(--paper);
  border: 3px solid var(--ink);
  box-shadow: var(--shadow) var(--shadow) 0 var(--ink);
}
.lesson-player-wrapper .lp-hud-pill i {
  font-size: 1.1rem;
  color: var(--ink);
}
.lesson-player-wrapper .lp-hud-label {
  font-family: var(--sans);
  font-size: 0.62rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--ink);
  opacity: 0.7;
  line-height: 1;
  margin-bottom: 0.2rem;
}
.lesson-player-wrapper .lp-hud-value {
  font-family: var(--mono);
  font-size: 1.15rem;
  font-weight: 700;
  color: var(--ink);
  line-height: 1.05;
  letter-spacing: -0.02em;
}
.lesson-player-wrapper .lp-hud-value small {
  font-size: 0.68rem;
  font-weight: 600;
  opacity: 0.65;
  margin-left: 0.15rem;
}

.lesson-player-wrapper .lp-icon-btn {
  width: 42px;
  height: 42px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: var(--paper);
  border: 3px solid var(--ink);
  box-shadow: var(--shadow) var(--shadow) 0 var(--ink);
  color: var(--ink);
  cursor: pointer;
  padding: 0;
  transition: transform 0.05s steps(1), box-shadow 0.05s steps(1), background 0.05s steps(1);
}
.lesson-player-wrapper .lp-icon-btn:hover { background: var(--paper-soft); }
.lesson-player-wrapper .lp-icon-btn:active {
  transform: translate(var(--shadow), var(--shadow));
  box-shadow: 0 0 0 var(--ink);
}
.lesson-player-wrapper .lp-icon-btn i {
  font-size: 1.1rem;
  color: var(--ink);
}

/* ---------- Step bar ---------- */
.lesson-player-wrapper .lp-step-bar {
  display: flex;
  align-items: center;
  gap: 1rem;
  flex-wrap: wrap;
  padding: 0.9rem 1rem;
  background: var(--paper);
  border: 3px solid var(--ink);
  box-shadow: var(--shadow) var(--shadow) 0 var(--ink);
  margin-bottom: 1.25rem;
}
.lesson-player-wrapper .lp-step-pills {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  flex: 1;
  min-width: 0;
}
.lesson-player-wrapper .lp-step-pill-wrapper {
  flex: 1;
  min-width: 0;
}
.lesson-player-wrapper .lp-step-pill {
  height: 14px;
  background: var(--paper-hard);
  border: 2px solid var(--ink);
  transition: background 0.1s steps(1);
}
.lesson-player-wrapper .lp-step-pill.active {
  background: var(--ink);
  box-shadow: 0 0 0 2px var(--paper) inset;
}
.lesson-player-wrapper .lp-step-pill.completed {
  background: var(--ink);
  opacity: 0.5;
}
.lesson-player-wrapper .lp-step-counter {
  font-family: var(--sans);
  font-size: 0.78rem;
  font-weight: 600;
  color: var(--ink);
  padding-left: 1rem;
  border-left: 3px solid var(--ink);
  white-space: nowrap;
}
.lesson-player-wrapper .lp-step-counter strong {
  font-family: var(--mono);
  font-weight: 700;
}

/* ---------- Arena ---------- */
.lesson-player-wrapper .lp-arena {
  position: relative;
  background: var(--paper);
  border: 3px solid var(--ink);
  box-shadow: 5px 5px 0 var(--ink);
  padding: 2.5rem 2rem;
  text-align: center;
  min-height: 240px;
  margin-bottom: 1.25rem;
  overflow: hidden;
}
.lesson-player-wrapper .lp-arena::before {
  content: "";
  position: absolute;
  inset: 0;
  pointer-events: none;
  background-image:
    linear-gradient(45deg, rgba(0,0,0,0.035) 25%, transparent 25%),
    linear-gradient(-45deg, rgba(0,0,0,0.035) 25%, transparent 25%),
    linear-gradient(45deg, transparent 75%, rgba(0,0,0,0.035) 75%),
    linear-gradient(-45deg, transparent 75%, rgba(0,0,0,0.035) 75%);
  background-size: 8px 8px;
  background-position: 0 0, 0 4px, 4px -4px, -4px 0;
}
html[data-theme="dark"] .lesson-player-wrapper .lp-arena::before {
  background-image:
    linear-gradient(45deg, rgba(255,255,255,0.045) 25%, transparent 25%),
    linear-gradient(-45deg, rgba(255,255,255,0.045) 25%, transparent 25%),
    linear-gradient(45deg, transparent 75%, rgba(255,255,255,0.045) 75%),
    linear-gradient(-45deg, transparent 75%, rgba(255,255,255,0.045) 75%);
}
.lesson-player-wrapper .lp-arena > * {
  position: relative;
  z-index: 1;
}

/* Focus overlay */
.lesson-player-wrapper .lp-focus-prompt {
  position: absolute;
  inset: 0;
  background: var(--ink);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  z-index: 10;
  cursor: pointer;
  padding: 1.5rem;
  text-align: center;
  transition: opacity 0.25s ease;
}
.lesson-player-wrapper .lp-focus-icon {
  width: 64px;
  height: 64px;
  background: var(--paper);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 1rem;
  border: 3px solid var(--paper);
}
.lesson-player-wrapper .lp-focus-icon i {
  font-size: 1.6rem;
  color: var(--ink);
}
.lesson-player-wrapper .lp-focus-title {
  font-family: var(--sans);
  font-size: 1.05rem;
  font-weight: 800;
  color: var(--paper);
  letter-spacing: -0.01em;
  margin-bottom: 0.4rem;
}
.lesson-player-wrapper .lp-focus-sub {
  font-family: var(--sans);
  font-size: 0.85rem;
  color: var(--paper);
  opacity: 0.75;
  max-width: 360px;
  line-height: 1.5;
}

/* Typing display */
.lesson-player-wrapper .lp-stream-wrap {
  padding: 1rem 0.5rem;
  margin-bottom: 0.75rem;
}
.lesson-player-wrapper .lp-text-display {
  font-family: var(--mono);
  font-size: 2rem;
  font-weight: 500;
  letter-spacing: 0.05em;
  line-height: 1.7;
  color: var(--ink);
  word-break: break-word;
  user-select: none;
}
.lesson-player-wrapper .lp-char {
  display: inline-block;
  position: relative;
  transition: color 0.05s ease, background-color 0.05s ease;
  color: var(--ink);
  opacity: 0.28;
}
.lesson-player-wrapper .lp-char.correct {
  opacity: 1;
}
.lesson-player-wrapper .lp-char.incorrect {
  background: var(--ink);
  color: var(--paper);
  opacity: 1;
}
.lesson-player-wrapper .lp-char.active {
  opacity: 1;
  background: var(--paper-soft);
  box-shadow: 0 0 0 3px var(--ink);
  animation: lpCaret 1s steps(2) infinite;
}
@keyframes lpCaret {
  0%, 49%   { box-shadow: 0 0 0 3px var(--ink); }
  50%, 100% { box-shadow: 0 0 0 3px transparent; }
}

/* Finger hint */
.lesson-player-wrapper .lp-finger-hint-wrap {
  padding-top: 1rem;
  margin-top: 1rem;
  border-top: 3px dashed var(--ink);
  display: flex;
  align-items: center;
  justify-content: center;
}
.lesson-player-wrapper .lp-finger-hint {
  display: inline-flex;
  align-items: center;
  gap: 0.6rem;
  padding: 0.55rem 1rem;
  background: var(--paper);
  border: 3px solid var(--ink);
  box-shadow: var(--shadow) var(--shadow) 0 var(--ink);
  font-family: var(--sans);
  font-size: 0.88rem;
  font-weight: 500;
  color: var(--ink);
}
.lesson-player-wrapper .lp-finger-hint strong {
  font-weight: 700;
  color: var(--ink);
}
.lesson-player-wrapper .lp-finger-dot {
  display: inline-block;
  width: 12px;
  height: 12px;
  background: var(--ink);
  flex-shrink: 0;
}

/* ---------- Keyboard ---------- */
.lesson-player-wrapper .lp-keyboard-wrap {
  background: var(--paper);
  border: 3px solid var(--ink);
  box-shadow: var(--shadow) var(--shadow) 0 var(--ink);
  padding: 1.25rem;
  margin-bottom: 1rem;
}
.lesson-player-wrapper .lp-kb-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-bottom: 1rem;
  padding-bottom: 0.75rem;
  border-bottom: 2px dashed var(--ink);
  font-family: var(--sans);
  font-size: 0.78rem;
  font-weight: 600;
  color: var(--ink);
}
.lesson-player-wrapper .lp-kb-head i {
  color: var(--ink);
  font-size: 0.95rem;
}
.lesson-player-wrapper .lp-kb-legend {
  display: flex;
  flex-wrap: wrap;
  gap: 0.7rem;
  font-size: 0.66rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  color: var(--ink);
}
.lesson-player-wrapper .lp-legend-dot {
  display: inline-block;
  width: 10px;
  height: 10px;
  margin-right: 0.3rem;
  vertical-align: middle;
  border: 1.5px solid var(--ink);
}

.lesson-player-wrapper .lp-kb-board {
  max-width: 880px;
  margin: 0 auto;
  user-select: none;
}
.lesson-player-wrapper .lp-kb-row {
  display: flex;
  justify-content: center;
  gap: 6px;
  margin-bottom: 6px;
}
.lesson-player-wrapper .lp-kb-key {
  width: 44px;
  height: 44px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--paper);
  border: 2px solid var(--ink);
  border-bottom-width: 4px;
  font-family: var(--mono);
  font-size: 0.85rem;
  font-weight: 700;
  color: var(--ink);
  position: relative;
  transition: transform 0.08s ease, background 0.08s ease;
}
.lesson-player-wrapper .lp-kb-key.wide   { width: 68px;  font-size: 0.72rem; }
.lesson-player-wrapper .lp-kb-key.wider  { width: 78px;  font-size: 0.72rem; }
.lesson-player-wrapper .lp-kb-key.widest { width: 98px;  font-size: 0.72rem; }
.lesson-player-wrapper .lp-kb-key.space  { width: 280px; font-size: 0.72rem; }

/* Finger zone borders */
.lesson-player-wrapper .lp-kb-key.zone-lp { border-bottom-color: #a855f7; }
.lesson-player-wrapper .lp-kb-key.zone-lr { border-bottom-color: #3b82f6; }
.lesson-player-wrapper .lp-kb-key.zone-lm { border-bottom-color: #06b6d4; }
.lesson-player-wrapper .lp-kb-key.zone-li { border-bottom-color: #10b981; }
.lesson-player-wrapper .lp-kb-key.zone-th { border-bottom-color: #f59e0b; }
.lesson-player-wrapper .lp-kb-key.zone-ri { border-bottom-color: #eab308; }
.lesson-player-wrapper .lp-kb-key.zone-rm { border-bottom-color: #06b6d4; }
.lesson-player-wrapper .lp-kb-key.zone-rr { border-bottom-color: #3b82f6; }
.lesson-player-wrapper .lp-kb-key.zone-rp { border-bottom-color: #a855f7; }

/* F / J anchor bumps */
.lesson-player-wrapper .lp-kb-key.anchor .lp-kb-bump {
  position: absolute;
  bottom: 5px;
  width: 14px;
  height: 2px;
  background: var(--ink);
}

/* Target key highlight — inverted block */
.lesson-player-wrapper .lp-kb-key.target {
  background: var(--ink);
  color: var(--paper);
  border-color: var(--ink);
  border-bottom-color: var(--paper);
  transform: translateY(-3px);
  animation: lpKeyBounce 0.8s infinite alternate ease-in-out;
}
.lesson-player-wrapper .lp-kb-key.target .lp-kb-bump {
  background: var(--paper);
}
@keyframes lpKeyBounce {
  0%   { transform: translateY(-3px); }
  100% { transform: translateY(-6px); }
}

/* Pressed key */
.lesson-player-wrapper .lp-kb-key.pressed {
  transform: translateY(3px);
  background: var(--paper-soft);
}

/* ---------- Modal ---------- */
.lp-modal .modal-content {
  background: #ffffff;
  color: #000000;
  border: 3px solid #000000;
  border-radius: 0;
  box-shadow: 6px 6px 0 #000000;
  font-family: "Inter", -apple-system, sans-serif;
  padding: 1.75rem;
}
html[data-theme="dark"] .lp-modal .modal-content {
  background: #000000;
  color: #ffffff;
  border-color: #ffffff;
  box-shadow: 6px 6px 0 #ffffff;
}

.lp-modal .lp-modal-award {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 64px;
  height: 64px;
  background: #000000;
  color: #ffffff;
  border: 3px solid #000000;
  margin-bottom: 1rem;
}
html[data-theme="dark"] .lp-modal .lp-modal-award {
  background: #ffffff;
  color: #000000;
  border-color: #ffffff;
}
.lp-modal .lp-modal-award i {
  font-size: 1.8rem;
}

.lp-modal .lp-modal-title {
  font-family: "Inter", sans-serif;
  font-size: 1.35rem;
  font-weight: 800;
  color: inherit;
  letter-spacing: -0.02em;
  margin: 0 0 0.5rem;
}
.lp-modal .lp-modal-sub {
  font-family: "Inter", sans-serif;
  font-size: 0.88rem;
  color: inherit;
  opacity: 0.75;
  margin-bottom: 1.25rem;
}

.lp-modal .lp-stars-row {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  margin-bottom: 1.25rem;
}
.lp-modal .lp-stars-row i {
  font-size: 2rem;
  color: inherit;
}
.lp-modal .lp-stars-row i.bi-star {
  opacity: 0.18;
}

.lp-modal .lp-result-card {
  background: #f4f4f4;
  border: 2px solid #000000;
  padding: 0.7rem 0.5rem;
  text-align: center;
}
html[data-theme="dark"] .lp-modal .lp-result-card {
  background: #141414;
  border-color: #ffffff;
}
.lp-modal .lp-result-label {
  font-family: "Inter", sans-serif;
  font-size: 0.62rem;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  opacity: 0.7;
  margin-bottom: 0.3rem;
}
.lp-modal .lp-result-value {
  font-family: "JetBrains Mono", monospace;
  font-size: 1.3rem;
  font-weight: 700;
  line-height: 1;
}
.lp-modal .lp-result-sub {
  font-family: "Inter", sans-serif;
  font-size: 0.62rem;
  opacity: 0.6;
  margin-top: 0.25rem;
}

.lp-modal .lp-btn-primary {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.4rem;
  width: 100%;
  padding: 0.75rem 1rem;
  background: #000000;
  color: #ffffff;
  border: 3px solid #000000;
  box-shadow: 3px 3px 0 #000000;
  font-family: "Inter", sans-serif;
  font-size: 0.88rem;
  font-weight: 700;
  letter-spacing: 0.03em;
  text-transform: uppercase;
  text-decoration: none;
  cursor: pointer;
  transition: transform 0.05s steps(1), box-shadow 0.05s steps(1);
}
.lp-modal .lp-btn-primary:hover {
  background: #000000;
  color: #ffffff;
}
.lp-modal .lp-btn-primary:active {
  transform: translate(3px, 3px);
  box-shadow: 0 0 0 #000000;
}
html[data-theme="dark"] .lp-modal .lp-btn-primary {
  background: #ffffff;
  color: #000000;
  border-color: #ffffff;
  box-shadow: 3px 3px 0 #ffffff;
}
html[data-theme="dark"] .lp-modal .lp-btn-primary:active {
  box-shadow: 0 0 0 #ffffff;
}

.lp-modal .lp-btn-outline {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.4rem;
  width: 100%;
  padding: 0.65rem 1rem;
  background: transparent;
  color: inherit;
  border: 3px solid currentColor;
  box-shadow: 3px 3px 0 currentColor;
  font-family: "Inter", sans-serif;
  font-size: 0.82rem;
  font-weight: 600;
  letter-spacing: 0.02em;
  text-transform: uppercase;
  cursor: pointer;
  transition: transform 0.05s steps(1), box-shadow 0.05s steps(1);
}
.lp-modal .lp-btn-outline:active {
  transform: translate(3px, 3px);
  box-shadow: 0 0 0 currentColor;
}
.lp-modal .lp-back-link {
  display: block;
  text-align: center;
  padding: 0.5rem 0;
  font-family: "Inter", sans-serif;
  font-size: 0.78rem;
  color: inherit;
  text-decoration: none;
  opacity: 0.7;
}
.lp-modal .lp-back-link:hover {
  opacity: 1;
  text-decoration: underline;
}

/* Star pop */
.star-pop {
  animation: starPop 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) both;
}
@keyframes starPop {
  0%   { transform: scale(0) rotate(-45deg); opacity: 0; }
  100% { transform: scale(1) rotate(0deg); opacity: 1; }
}

/* ---------- Responsive ---------- */
@media (max-width: 768px) {
  .lesson-player-wrapper .lp-text-display { font-size: 1.4rem; }
  .lesson-player-wrapper .lp-arena { padding: 1.5rem 1rem; }
  .lesson-player-wrapper .lp-title { font-size: 1.1rem; }
  .lesson-player-wrapper .lp-hud-value { font-size: 1rem; }
  .lesson-player-wrapper .lp-hud-pill { padding: 0.45rem 0.65rem; }
  .lesson-player-wrapper .lp-finger-hint { font-size: 0.78rem; padding: 0.45rem 0.75rem; }

  .lesson-player-wrapper .lp-kb-key { width: 28px; height: 34px; font-size: 0.66rem; }
  .lesson-player-wrapper .lp-kb-key.wide   { width: 44px; }
  .lesson-player-wrapper .lp-kb-key.wider  { width: 50px; }
  .lesson-player-wrapper .lp-kb-key.widest { width: 60px; }
  .lesson-player-wrapper .lp-kb-key.space  { width: 140px; }
}
@media (max-width: 575px) {
  .lesson-player-wrapper .lp-kb-legend { display: none !important; }
  .lesson-player-wrapper .lp-step-counter { font-size: 0.7rem; padding-left: 0.75rem; }
  .lesson-player-wrapper .lp-hud-label { font-size: 0.56rem; }
}
</style>

<div class="content-wrapper lesson-player-wrapper">

  <!-- Top bar -->
  <div class="lp-topbar">
    <div class="d-flex align-items-center gap-3">
      <a href="<?= BASE_URL ?>/lessons.php" class="lp-back-btn">
        <i class="bi bi-arrow-left"></i>
        <span>All Lessons</span>
      </a>
      <div>
        <div class="lp-stage-row">
          <span class="lp-stage-badge"><?= e($lesson['stage_title']) ?></span>
          <span class="lp-stage-num">Lesson <?= sprintf('%02d', $lesson['number']) ?></span>
        </div>
        <h4 class="lp-title"><?= e($lesson['title']) ?></h4>
      </div>
    </div>

    <div class="lp-hud-group">
      <div class="lp-hud-pill">
        <i class="bi bi-speedometer2"></i>
        <div>
          <div class="lp-hud-label">Speed</div>
          <div class="lp-hud-value" id="hud-wpm">0<small>WPM</small></div>
        </div>
      </div>
      <div class="lp-hud-pill">
        <i class="bi bi-bullseye"></i>
        <div>
          <div class="lp-hud-label">Accuracy</div>
          <div class="lp-hud-value" id="hud-acc">100<small>%</small></div>
        </div>
      </div>
      <button type="button" class="lp-icon-btn" id="toggle-sound-btn" title="Toggle sound">
        <i class="bi bi-volume-up-fill" id="sound-icon"></i>
      </button>
      <button type="button" class="lp-icon-btn" id="toggle-keyboard-btn" title="Toggle keyboard">
        <i class="bi bi-keyboard-fill"></i>
      </button>
    </div>
  </div>

  <!-- Step bar -->
  <div class="lp-step-bar">
    <div class="lp-step-pills" id="stepPillsContainer">
      <?php foreach ($lesson['exercises'] as $exIndex => $ex): ?>
        <div class="lp-step-pill-wrapper" data-step-index="<?= $exIndex ?>">
          <div class="lp-step-pill <?= ($exIndex === 0) ? 'active' : '' ?>"></div>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="lp-step-counter">
      Step <strong id="currentStepText">1</strong> of <?= count($lesson['exercises']) ?>
    </div>
  </div>

  <!-- Arena -->
  <div class="lp-arena" id="lessonArenaContainer">
    <div id="lessonFocusPrompt" class="lp-focus-prompt">
      <div class="lp-focus-icon"><i class="bi bi-cursor-fill"></i></div>
      <div class="lp-focus-title">Click to Focus or Start Typing</div>
      <div class="lp-focus-sub">Your fingers will be guided by the visual keyboard below</div>
    </div>

    <input type="text" id="lessonVirtualInput" autocomplete="off" autocapitalize="none" spellcheck="false"
           style="position:absolute;opacity:0;pointer-events:none;left:-9999px;">

    <div class="lp-stream-wrap">
      <div class="lp-text-display" id="lessonTextDisplay"></div>
    </div>

    <div class="lp-finger-hint-wrap">
      <div class="lp-finger-hint" id="fingerHintBadge">
        <span class="lp-finger-dot" id="fingerHintDot"></span>
        <span id="fingerHintText">Press <strong>J</strong> with your <strong>Right Index Finger</strong></span>
      </div>
    </div>
  </div>

  <!-- Keyboard -->
  <div class="lp-keyboard-wrap" id="visualKeyboardContainer">
    <div class="lp-kb-head">
      <div class="d-flex align-items-center gap-2">
        <i class="bi bi-hand-index-thumb"></i>
        <span>Finger Guide</span>
      </div>
      <div class="lp-kb-legend d-none d-md-flex">
        <span><span class="lp-legend-dot" style="background:#a855f7;"></span>LP</span>
        <span><span class="lp-legend-dot" style="background:#3b82f6;"></span>LR</span>
        <span><span class="lp-legend-dot" style="background:#06b6d4;"></span>LM</span>
        <span><span class="lp-legend-dot" style="background:#10b981;"></span>LI</span>
        <span><span class="lp-legend-dot" style="background:#f59e0b;"></span>TH</span>
        <span><span class="lp-legend-dot" style="background:#eab308;"></span>RI</span>
        <span><span class="lp-legend-dot" style="background:#06b6d4;"></span>RM</span>
        <span><span class="lp-legend-dot" style="background:#3b82f6;"></span>RR</span>
        <span><span class="lp-legend-dot" style="background:#a855f7;"></span>RP</span>
      </div>
    </div>

    <div class="lp-kb-board" id="visualKeyboardBoard">
      <!-- Row 1 -->
      <div class="lp-kb-row">
        <div class="lp-kb-key zone-lp" data-key="`" data-char="~">`</div>
        <div class="lp-kb-key zone-lp" data-key="1" data-char="!">1</div>
        <div class="lp-kb-key zone-lr" data-key="2" data-char="@">2</div>
        <div class="lp-kb-key zone-lm" data-key="3" data-char="#">3</div>
        <div class="lp-kb-key zone-li" data-key="4" data-char="$">4</div>
        <div class="lp-kb-key zone-li" data-key="5" data-char="%">5</div>
        <div class="lp-kb-key zone-ri" data-key="6" data-char="^">6</div>
        <div class="lp-kb-key zone-ri" data-key="7" data-char="&">7</div>
        <div class="lp-kb-key zone-rm" data-key="8" data-char="*">8</div>
        <div class="lp-kb-key zone-rr" data-key="9" data-char="(">9</div>
        <div class="lp-kb-key zone-rp" data-key="0" data-char=")">0</div>
        <div class="lp-kb-key zone-rp" data-key="-" data-char="_">-</div>
        <div class="lp-kb-key zone-rp" data-key="=" data-char="+">=</div>
        <div class="lp-kb-key wide zone-rp" data-key="Backspace">⌫</div>
      </div>
      <!-- Row 2 -->
      <div class="lp-kb-row">
        <div class="lp-kb-key wide zone-lp" data-key="Tab">Tab</div>
        <div class="lp-kb-key zone-lp" data-key="q" data-char="Q">Q</div>
        <div class="lp-kb-key zone-lr" data-key="w" data-char="W">W</div>
        <div class="lp-kb-key zone-lm" data-key="e" data-char="E">E</div>
        <div class="lp-kb-key zone-li" data-key="r" data-char="R">R</div>
        <div class="lp-kb-key zone-li" data-key="t" data-char="T">T</div>
        <div class="lp-kb-key zone-ri" data-key="y" data-char="Y">Y</div>
        <div class="lp-kb-key zone-ri" data-key="u" data-char="U">U</div>
        <div class="lp-kb-key zone-rm" data-key="i" data-char="I">I</div>
        <div class="lp-kb-key zone-rr" data-key="o" data-char="O">O</div>
        <div class="lp-kb-key zone-rp" data-key="p" data-char="P">P</div>
        <div class="lp-kb-key zone-rp" data-key="[" data-char="{">[</div>
        <div class="lp-kb-key zone-rp" data-key="]" data-char="}">]</div>
        <div class="lp-kb-key zone-rp" data-key="\" data-char="|">\</div>
      </div>
      <!-- Row 3 -->
      <div class="lp-kb-row">
        <div class="lp-kb-key wider zone-lp" data-key="CapsLock">Caps</div>
        <div class="lp-kb-key zone-lp" data-key="a" data-char="A">A</div>
        <div class="lp-kb-key zone-lr" data-key="s" data-char="S">S</div>
        <div class="lp-kb-key zone-lm" data-key="d" data-char="D">D</div>
        <div class="lp-kb-key zone-li anchor" data-key="f" data-char="F">
          <span>F</span><div class="lp-kb-bump"></div>
        </div>
        <div class="lp-kb-key zone-li" data-key="g" data-char="G">G</div>
        <div class="lp-kb-key zone-ri" data-key="h" data-char="H">H</div>
        <div class="lp-kb-key zone-ri anchor" data-key="j" data-char="J">
          <span>J</span><div class="lp-kb-bump"></div>
        </div>
        <div class="lp-kb-key zone-rm" data-key="k" data-char="K">K</div>
        <div class="lp-kb-key zone-rr" data-key="l" data-char="L">L</div>
        <div class="lp-kb-key zone-rp" data-key=";" data-char=":">;</div>
        <div class="lp-kb-key zone-rp" data-key="'" data-char="&quot;">'</div>
        <div class="lp-kb-key wider zone-rp" data-key="Enter">Enter ↵</div>
      </div>
      <!-- Row 4 -->
      <div class="lp-kb-row">
        <div class="lp-kb-key widest zone-lp" data-key="ShiftLeft">Shift</div>
        <div class="lp-kb-key zone-lp" data-key="z" data-char="Z">Z</div>
        <div class="lp-kb-key zone-lr" data-key="x" data-char="X">X</div>
        <div class="lp-kb-key zone-lm" data-key="c" data-char="C">C</div>
        <div class="lp-kb-key zone-li" data-key="v" data-char="V">V</div>
        <div class="lp-kb-key zone-li" data-key="b" data-char="B">B</div>
        <div class="lp-kb-key zone-ri" data-key="n" data-char="N">N</div>
        <div class="lp-kb-key zone-ri" data-key="m" data-char="M">M</div>
        <div class="lp-kb-key zone-rm" data-key="," data-char="<">,</div>
        <div class="lp-kb-key zone-rr" data-key="." data-char=">">.</div>
        <div class="lp-kb-key zone-rp" data-key="/" data-char="?">/</div>
        <div class="lp-kb-key widest zone-rp" data-key="ShiftRight">Shift</div>
      </div>
      <!-- Row 5 -->
      <div class="lp-kb-row">
        <div class="lp-kb-key zone-lp" data-key="Control">Ctrl</div>
        <div class="lp-kb-key zone-lp" data-key="Alt">Alt</div>
        <div class="lp-kb-key space zone-th" data-key=" " data-char=" ">Space Bar</div>
        <div class="lp-kb-key zone-rp" data-key="AltGraph">Alt</div>
        <div class="lp-kb-key zone-rp" data-key="Control">Ctrl</div>
      </div>
    </div>
  </div>
</div>

<!-- Completion Modal -->
<div class="modal fade lp-modal" id="lessonCompleteModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center">
      <canvas id="confettiCanvas" style="position:absolute;top:0;left:0;width:100%;height:100%;pointer-events:none;z-index:1;"></canvas>

      <div style="position:relative;z-index:2;">
        <div class="lp-modal-award"><i class="bi bi-award-fill"></i></div>
        <h3 class="lp-modal-title">Lesson Complete!</h3>
        <p class="lp-modal-sub">Great work! You finished all exercises.</p>

        <div class="lp-stars-row" id="modalStarsContainer">
          <i class="bi bi-star-fill star-pop" style="animation-delay:0.1s;"></i>
          <i class="bi bi-star-fill star-pop" style="animation-delay:0.25s;"></i>
          <i class="bi bi-star-fill star-pop" style="animation-delay:0.4s;"></i>
        </div>

        <div class="row g-2 mb-4">
          <div class="col-4">
            <div class="lp-result-card">
              <div class="lp-result-label">Speed</div>
              <div class="lp-result-value" id="modalWpm">0</div>
              <div class="lp-result-sub">WPM</div>
            </div>
          </div>
          <div class="col-4">
            <div class="lp-result-card">
              <div class="lp-result-label">Accuracy</div>
              <div class="lp-result-value" id="modalAccuracy">0%</div>
              <div class="lp-result-sub">Precision</div>
            </div>
          </div>
          <div class="col-4">
            <div class="lp-result-card">
              <div class="lp-result-label">Time</div>
              <div class="lp-result-value" id="modalTime">0s</div>
              <div class="lp-result-sub">Duration</div>
            </div>
          </div>
        </div>

        <div class="d-flex flex-column gap-2">
          <a href="#" id="modalNextLessonBtn" class="lp-btn-primary">
            <span>Next Lesson</span>
            <i class="bi bi-arrow-right"></i>
          </a>
          <button type="button" class="lp-btn-outline" id="modalRetryBtn">
            <i class="bi bi-arrow-counterclockwise"></i>
            <span>Practice Again</span>
          </button>
          <a href="<?= BASE_URL ?>/lessons.php" class="lp-back-link">Back to Curriculum</a>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
window.LESSON_CONFIG = {
  id: <?= json_encode($lesson['id']) ?>,
  title: <?= json_encode($lesson['title']) ?>,
  stage: <?= json_encode($lesson['stage']) ?>,
  exercises: <?= json_encode($lesson['exercises']) ?>,
  fingerHints: <?= json_encode($lesson['finger_hints']) ?>,
  csrfToken: <?= json_encode(get_csrf_token()) ?>,
  saveUrl: <?= json_encode(BASE_URL . '/api/save-lesson-progress.php') ?>,
  curriculumUrl: <?= json_encode(BASE_URL . '/lessons.php') ?>
};
</script>

<script>
(function() {
  const config = window.LESSON_CONFIG;
  const exercises = config.exercises || [];
  let currentStepIndex = 0;
  let targetText = '';
  let currentIndex = 0;
  let errorCount = 0;
  let totalKeystrokes = 0;
  let correctKeystrokes = 0;
  let stepStartTime = null;
  let overallStartTime = null;
  let isStepFinished = false;
  let soundEnabled = true;

  const textDisplay = document.getElementById('lessonTextDisplay');
  const focusPrompt = document.getElementById('lessonFocusPrompt');
  const virtualInput = document.getElementById('lessonVirtualInput');
  const hudWpm = document.getElementById('hud-wpm');
  const hudAcc = document.getElementById('hud-acc');
  const currentStepText = document.getElementById('currentStepText');
  const fingerHintText = document.getElementById('fingerHintText');
  const fingerHintDot = document.getElementById('fingerHintDot');
  const soundBtn = document.getElementById('toggle-sound-btn');
  const soundIcon = document.getElementById('sound-icon');
  const kbToggleBtn = document.getElementById('toggle-keyboard-btn');
  const visualKbContainer = document.getElementById('visualKeyboardContainer');

  let audioCtx = null;
  function playBeep(type) {
    if (!soundEnabled) return;
    try {
      if (!audioCtx) {
        const AC = window.AudioContext || window.webkitAudioContext;
        if (AC) audioCtx = new AC();
      }
      if (audioCtx && audioCtx.state === 'suspended') audioCtx.resume();
      if (!audioCtx) return;
      const now = audioCtx.currentTime;
      const osc = audioCtx.createOscillator();
      const gain = audioCtx.createGain();

      if (type === 'click') {
        osc.type = 'triangle';
        osc.frequency.setValueAtTime(450, now);
        osc.frequency.exponentialRampToValueAtTime(150, now + 0.04);
        gain.gain.setValueAtTime(0.2, now);
        gain.gain.exponentialRampToValueAtTime(0.01, now + 0.04);
        osc.connect(gain); gain.connect(audioCtx.destination);
        osc.start(now); osc.stop(now + 0.04);
      } else if (type === 'error') {
        osc.type = 'sawtooth';
        osc.frequency.setValueAtTime(140, now);
        osc.frequency.linearRampToValueAtTime(90, now + 0.1);
        gain.gain.setValueAtTime(0.25, now);
        gain.gain.linearRampToValueAtTime(0.01, now + 0.1);
        osc.connect(gain); gain.connect(audioCtx.destination);
        osc.start(now); osc.stop(now + 0.1);
      } else if (type === 'chime') {
        [523.25, 659.25, 783.99].forEach(function(freq, i) {
          const o = audioCtx.createOscillator();
          const g = audioCtx.createGain();
          o.type = 'sine';
          o.frequency.setValueAtTime(freq, now + i * 0.08);
          g.gain.setValueAtTime(0.18, now + i * 0.08);
          g.gain.exponentialRampToValueAtTime(0.001, now + i * 0.08 + 0.28);
          o.connect(g); g.connect(audioCtx.destination);
          o.start(now + i * 0.08); o.stop(now + i * 0.08 + 0.28);
        });
      }
    } catch (e) {}
  }

  function loadStep(index) {
    if (index >= exercises.length) { finishWholeLesson(); return; }
    currentStepIndex = index;
    targetText = exercises[index];
    currentIndex = 0;
    isStepFinished = false;
    stepStartTime = null;
    currentStepText.textContent = (index + 1);

    document.querySelectorAll('.lp-step-pill-wrapper').forEach(function(w, idx) {
      const pill = w.querySelector('.lp-step-pill');
      pill.classList.remove('active');
      if (idx < index) pill.classList.add('completed');
      else if (idx === index) pill.classList.add('active');
    });

    textDisplay.innerHTML = '';
    for (let i = 0; i < targetText.length; i++) {
      const span = document.createElement('span');
      span.className = 'lp-char' + (i === 0 ? ' active' : '');
      span.textContent = targetText[i];
      span.id = 'char-' + i;
      textDisplay.appendChild(span);
    }

    updateActiveKeyGuide();
    focusInput();
  }

  function updateActiveKeyGuide() {
    document.querySelectorAll('.lp-kb-key.target').forEach(function(el) { el.classList.remove('target'); });
    if (currentIndex >= targetText.length) return;
    const nextChar = targetText[currentIndex];

    let matchedKey = null;
    document.querySelectorAll('.lp-kb-key').forEach(function(k) {
      const keyVal = k.getAttribute('data-key');
      const charVal = k.getAttribute('data-char');
      if (nextChar === ' ' && keyVal === ' ') matchedKey = k;
      else if (keyVal && keyVal.toLowerCase() === nextChar.toLowerCase()) matchedKey = k;
      else if (charVal && charVal === nextChar) matchedKey = k;
    });
    if (matchedKey) matchedKey.classList.add('target');

    const lowerChar = nextChar.toLowerCase();
    const hint = config.fingerHints && config.fingerHints[lowerChar]
      ? config.fingerHints[lowerChar]
      : getGenericFingerHint(nextChar);

    if (hint) {
      fingerHintText.innerHTML = 'Press <strong>' + (nextChar === ' ' ? 'SPACE' : nextChar.toUpperCase()) + '</strong> with your <strong>' + hint.finger + '</strong>';
      fingerHintDot.style.background = getFingerZoneColor(hint.finger);
    }
  }

  function getGenericFingerHint(ch) {
    ch = ch.toLowerCase();
    if (ch === ' ') return { finger: 'Thumb' };
    if ('1qaz'.indexOf(ch) !== -1) return { finger: 'Left Pinky' };
    if ('2wsx'.indexOf(ch) !== -1) return { finger: 'Left Ring' };
    if ('3edc'.indexOf(ch) !== -1) return { finger: 'Left Middle' };
    if ('45rtfgvb'.indexOf(ch) !== -1) return { finger: 'Left Index' };
    if ('67yuhjnm'.indexOf(ch) !== -1) return { finger: 'Right Index' };
    if ('8ik,'.indexOf(ch) !== -1) return { finger: 'Right Middle' };
    if ('9ol.'.indexOf(ch) !== -1) return { finger: 'Right Ring' };
    if ('0p;/-=[]\''.indexOf(ch) !== -1) return { finger: 'Right Pinky' };
    return { finger: 'Index Finger' };
  }

  function getFingerZoneColor(name) {
    if (name.indexOf('Pinky') !== -1) return '#a855f7';
    if (name.indexOf('Ring') !== -1) return '#3b82f6';
    if (name.indexOf('Middle') !== -1) return '#06b6d4';
    if (name.indexOf('Left Index') !== -1) return '#10b981';
    if (name.indexOf('Right Index') !== -1) return '#eab308';
    if (name.indexOf('Thumb') !== -1) return '#f59e0b';
    return '#000000';
  }

  function handleInputChar(charTyped) {
    if (isStepFinished) return;
    if (!overallStartTime) overallStartTime = performance.now();
    if (!stepStartTime) stepStartTime = performance.now();

    totalKeystrokes++;
    const expectedChar = targetText[currentIndex];
    highlightPhysicalKey(charTyped);

    if (charTyped === expectedChar) {
      correctKeystrokes++;
      playBeep('click');
      const span = document.getElementById('char-' + currentIndex);
      if (span) span.className = 'lp-char correct';
      currentIndex++;
      if (currentIndex < targetText.length) {
        const nextSpan = document.getElementById('char-' + currentIndex);
        if (nextSpan) nextSpan.className = 'lp-char active';
        updateActiveKeyGuide();
      } else {
        isStepFinished = true;
        playBeep('chime');
        updateTelemetries();
        setTimeout(function() { loadStep(currentStepIndex + 1); }, 320);
      }
    } else {
      errorCount++;
      playBeep('error');
      const span = document.getElementById('char-' + currentIndex);
      if (span) {
        span.classList.add('incorrect');
        setTimeout(function() { span.classList.remove('incorrect'); }, 280);
      }
    }
    updateTelemetries();
  }

  function highlightPhysicalKey(char) {
    document.querySelectorAll('.lp-kb-key').forEach(function(k) {
      const keyVal = k.getAttribute('data-key');
      const charVal = k.getAttribute('data-char');
      if ((char === ' ' && keyVal === ' ') ||
          (keyVal && keyVal.toLowerCase() === char.toLowerCase()) ||
          (charVal && charVal === char)) {
        k.classList.add('pressed');
        setTimeout(function() { k.classList.remove('pressed'); }, 140);
      }
    });
  }

  function updateTelemetries() {
    if (!overallStartTime) return;
    const elapsedMinutes = (performance.now() - overallStartTime) / 60000;
    const wpm = elapsedMinutes > 0 ? Math.round((correctKeystrokes / 5) / elapsedMinutes) : 0;
    const acc = totalKeystrokes > 0 ? Math.round((correctKeystrokes / totalKeystrokes) * 100) : 100;
    hudWpm.innerHTML = wpm + '<small>WPM</small>';
    hudAcc.innerHTML = acc + '<small>%</small>';
  }

  function finishWholeLesson() {
    const elapsedSeconds = Math.max(1, Math.round((performance.now() - overallStartTime) / 1000));
    const elapsedMinutes = elapsedSeconds / 60;
    const finalWpm = elapsedMinutes > 0 ? Math.round((correctKeystrokes / 5) / elapsedMinutes) : 0;
    const finalAcc = totalKeystrokes > 0 ? Math.round((correctKeystrokes / totalKeystrokes) * 100) : 100;

    let stars = 1;
    if (finalAcc >= 95) stars = 3;
    else if (finalAcc >= 85) stars = 2;

    document.getElementById('modalWpm').textContent = finalWpm;
    document.getElementById('modalAccuracy').textContent = finalAcc + '%';
    document.getElementById('modalTime').textContent = elapsedSeconds + 's';

    const starsContainer = document.getElementById('modalStarsContainer');
    starsContainer.innerHTML = '';
    for (let s = 1; s <= 3; s++) {
      const star = document.createElement('i');
      star.className = 'bi bi-star' + (s <= stars ? '-fill' : '') + ' star-pop';
      star.style.animationDelay = (s * 0.15) + 's';
      starsContainer.appendChild(star);
    }

    saveLessonProgress(stars, finalWpm, finalAcc);
    launchConfetti();

    const modal = new bootstrap.Modal(document.getElementById('lessonCompleteModal'));
    modal.show();
  }

  function saveLessonProgress(stars, wpm, accuracy) {
    const payload = {
      csrf_token: config.csrfToken,
      lesson_id: config.id,
      stage: config.stage,
      stars: stars,
      wpm: wpm,
      accuracy: accuracy
    };
    fetch(config.saveUrl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': config.csrfToken },
      body: JSON.stringify(payload)
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      document.getElementById('modalNextLessonBtn').href = (data && data.next_lesson_id)
        ? 'lesson-player.php?id=' + encodeURIComponent(data.next_lesson_id)
        : config.curriculumUrl;
    })
    .catch(function() {
      document.getElementById('modalNextLessonBtn').href = config.curriculumUrl;
    });
  }

  function launchConfetti() {
    const canvas = document.getElementById('confettiCanvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    canvas.width = canvas.parentElement.offsetWidth;
    canvas.height = canvas.parentElement.offsetHeight;

    const particles = [];
    const colors = ['#000000', '#ffffff', '#666666', '#aaaaaa'];

    for (let i = 0; i < 60; i++) {
      particles.push({
        x: canvas.width / 2,
        y: canvas.height / 2,
        vx: (Math.random() - 0.5) * 12,
        vy: (Math.random() - 0.7) * 14,
        size: Math.random() * 7 + 4,
        color: colors[Math.floor(Math.random() * colors.length)],
        rotation: Math.random() * 360,
        vr: (Math.random() - 0.5) * 10
      });
    }

    let frame = 0;
    function animate() {
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      particles.forEach(function(p) {
        p.x += p.vx;
        p.y += p.vy;
        p.vy += 0.35;
        p.rotation += p.vr;
        ctx.save();
        ctx.translate(p.x, p.y);
        ctx.rotate((p.rotation * Math.PI) / 180);
        ctx.fillStyle = p.color;
        ctx.fillRect(-p.size / 2, -p.size / 2, p.size, p.size);
        ctx.restore();
      });
      frame++;
      if (frame < 120) requestAnimationFrame(animate);
      else ctx.clearRect(0, 0, canvas.width, canvas.height);
    }
    requestAnimationFrame(animate);
  }

  function focusInput() {
    focusPrompt.style.opacity = '0';
    focusPrompt.style.pointerEvents = 'none';
    virtualInput.focus();
  }

  focusPrompt.addEventListener('click', focusInput);
  document.getElementById('lessonArenaContainer').addEventListener('click', focusInput);

  window.addEventListener('keydown', function(e) {
    if (e.ctrlKey || e.altKey || e.metaKey) return;
    if (e.key === 'Tab') { e.preventDefault(); return; }
    focusPrompt.style.opacity = '0';
    focusPrompt.style.pointerEvents = 'none';
    if (e.key.length === 1) {
      e.preventDefault();
      handleInputChar(e.key);
    } 

  });

  soundBtn.addEventListener('click', function() {
    soundEnabled = !soundEnabled;
    soundIcon.className = soundEnabled ? 'bi bi-volume-up-fill' : 'bi bi-volume-mute-fill';
    soundIcon.style.opacity = soundEnabled ? '1' : '0.5';
  });

  kbToggleBtn.addEventListener('click', function() {
    visualKbContainer.style.display = (visualKbContainer.style.display === 'none') ? 'block' : 'none';
  });

  document.getElementById('modalRetryBtn').addEventListener('click', function() {
    location.reload();
  });

  loadStep(0);
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>