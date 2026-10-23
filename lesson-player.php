<?php
/**
 * Custom Type Master Pro
 * Interactive Student Lesson Player (Typing.com Experience)
 * Features: Visual On-Screen Keyboard, Finger Placement Guides, 
 * Step-by-Step Drill Progression, Star Ratings & Real-Time Telemetry
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

<div class="content-wrapper lesson-player-wrapper">
  <!-- Top Navigation & Stage HUD -->
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
    <div class="d-flex align-items-center gap-3">
      <a href="<?= BASE_URL ?>/lessons.php" class="btn btn-sm btn-control d-inline-flex align-items-center gap-1.5 px-3 py-1.5" title="Return to Curriculum">
        <i class="bi bi-arrow-left"></i>
        <span>All Lessons</span>
      </a>

      <div>
        <div class="d-flex align-items-center gap-2 mb-0.5">
          <span class="badge px-2 py-0.5 rounded text-uppercase" style="font-size: 0.65rem; font-weight: 800; background: rgba(56, 189, 248, 0.15); color: #38bdf8;">
            <?= e($lesson['stage_title']) ?>
          </span>
          <span class="text-secondary small fw-bold">Lesson <?= sprintf('%02d', $lesson['number']) ?></span>
        </div>
        <h4 class="fw-bold mb-0 text-white" style="letter-spacing: -0.02em; font-size: 1.25rem;">
          <?= e($lesson['title']) ?>
        </h4>
      </div>
    </div>

    <!-- Live Telemetry Pill Badges -->
    <div class="d-flex align-items-center gap-2">
      <!-- Live Speed HUD -->
      <div class="px-3 py-1.5 rounded-3 d-flex align-items-center gap-2" style="background: var(--bg-secondary); border: 1px solid var(--border-color);">
        <i class="bi bi-speedometer2 text-info"></i>
        <div>
          <div class="text-secondary" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase;">Speed</div>
          <div class="fw-bold text-white fs-6 font-monospace" id="hud-wpm">0 <span class="text-secondary" style="font-size: 0.7rem;">WPM</span></div>
        </div>
      </div>

      <!-- Live Accuracy HUD -->
      <div class="px-3 py-1.5 rounded-3 d-flex align-items-center gap-2" style="background: var(--bg-secondary); border: 1px solid var(--border-color);">
        <i class="bi bi-bullseye text-success"></i>
        <div>
          <div class="text-secondary" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase;">Accuracy</div>
          <div class="fw-bold text-white fs-6 font-monospace" id="hud-acc">100<span class="text-secondary" style="font-size: 0.7rem;">%</span></div>
        </div>
      </div>

      <!-- Controls Dropdown / Audio Toggle -->
      <button type="button" class="btn btn-sm btn-control px-2.5 py-2" id="toggle-sound-btn" title="Toggle Mechanical Audio Feedback">
        <i class="bi bi-volume-up-fill fs-6" id="sound-icon"></i>
      </button>

      <button type="button" class="btn btn-sm btn-control px-2.5 py-2" id="toggle-keyboard-btn" title="Toggle On-Screen Keyboard">
        <i class="bi bi-keyboard-fill fs-6"></i>
      </button>
    </div>
  </div>

  <!-- Screen Multi-Step Progression Bar -->
  <div class="glass-card p-3 mb-4 d-flex flex-wrap align-items-center justify-content-between gap-3" style="background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: 12px;">
    <div class="d-flex align-items-center gap-2 flex-grow-1" id="stepPillsContainer">
      <?php foreach ($lesson['exercises'] as $exIndex => $ex): ?>
        <div class="step-pill-wrapper flex-grow-1" data-step-index="<?= $exIndex ?>">
          <div class="step-pill <?= ($exIndex === 0) ? 'active' : '' ?>">
            <span class="step-num"><?= $exIndex + 1 ?></span>
            <span class="step-check"><i class="bi bi-check-lg"></i></span>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="d-flex align-items-center gap-2 text-secondary small fw-bold ps-2 border-start" style="border-color: var(--border-color) !important;">
      <span>Step <span id="currentStepText" class="text-white">1</span> of <?= count($lesson['exercises']) ?></span>
    </div>
  </div>

  <!-- Main Interactive Typing Arena -->
  <div class="glass-card p-4 p-md-5 mb-4 position-relative text-center overflow-hidden" id="lessonArenaContainer" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; min-height: 220px; box-shadow: var(--shadow-md);">
    <!-- Focus Prompt Overlay -->
    <div id="lessonFocusPrompt" class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center" style="background: rgba(10,10,10,0.85); backdrop-filter: blur(4px); z-index: 10; cursor: pointer; transition: opacity 0.25s ease;">
      <div class="p-3 rounded-circle mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background: rgba(255, 106, 0, 0.15); color: var(--first-color); border: 1px solid rgba(255, 106, 0, 0.3);">
        <i class="bi bi-cursor-fill fs-3"></i>
      </div>
      <div class="fw-bold text-white fs-5 mb-1">Click to Focus or Start Typing</div>
      <div class="text-secondary small">Your fingers will be guided by the visual keyboard below</div>
    </div>

    <!-- Hidden Input for Mobile / Virtual Focus -->
    <input type="text" id="lessonVirtualInput" autocomplete="off" autocapitalize="none" spellcheck="false" style="position: absolute; opacity: 0; pointer-events: none; left: -9999px;">

    <!-- Exercise Text Stream Display -->
    <div class="typing-stream-wrap py-3" id="typingStreamWrap">
      <div class="typing-text-display font-monospace" id="lessonTextDisplay" style="font-size: 2.1rem; letter-spacing: 0.08em; line-height: 1.8; user-select: none;">
        <!-- Dynamically rendered character spans -->
      </div>
    </div>

    <!-- Dynamic Finger & Hand Placement Hint -->
    <div class="mt-4 pt-3 border-top d-flex align-items-center justify-content-center gap-2" style="border-color: var(--border-color) !important;">
      <div class="finger-hint-pill d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill" id="fingerHintBadge" style="background: var(--bg-secondary); border: 1px solid var(--border-color); font-size: 0.88rem; font-weight: 600; color: var(--text-primary);">
        <span class="finger-zone-dot" id="fingerHintDot"></span>
        <span id="fingerHintText">Press <strong class="text-white">J</strong> with your <strong style="color: #facc15;">Right Index Finger</strong></span>
      </div>
    </div>
  </div>

  <!-- On-Screen Keyboard Guide (Typing.com Signature Component) -->
  <div class="glass-card p-3 p-md-4 mb-4 keyboard-guide-container" id="visualKeyboardContainer" style="background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: 16px;">
    <div class="d-flex align-items-center justify-content-between mb-3 px-1">
      <div class="d-flex align-items-center gap-2 small text-secondary">
        <i class="bi bi-hand-index-thumb"></i>
        <span>Finger Guide Legend:</span>
      </div>

      <!-- Color Legend for Finger Placement Zones -->
      <div class="d-none d-md-flex align-items-center gap-3" style="font-size: 0.72rem; font-weight: 700;">
        <span class="d-inline-flex align-items-center gap-1.5"><span class="legend-color-dot" style="background: #a855f7;"></span> Left Pinky</span>
        <span class="d-inline-flex align-items-center gap-1.5"><span class="legend-color-dot" style="background: #3b82f6;"></span> Left Ring</span>
        <span class="d-inline-flex align-items-center gap-1.5"><span class="legend-color-dot" style="background: #06b6d4;"></span> Left Middle</span>
        <span class="d-inline-flex align-items-center gap-1.5"><span class="legend-color-dot" style="background: #10b981;"></span> Left Index</span>
        <span class="d-inline-flex align-items-center gap-1.5"><span class="legend-color-dot" style="background: #f59e0b;"></span> Thumbs</span>
        <span class="d-inline-flex align-items-center gap-1.5"><span class="legend-color-dot" style="background: #eab308;"></span> Right Index</span>
        <span class="d-inline-flex align-items-center gap-1.5"><span class="legend-color-dot" style="background: #06b6d4;"></span> Right Middle</span>
        <span class="d-inline-flex align-items-center gap-1.5"><span class="legend-color-dot" style="background: #3b82f6;"></span> Right Ring</span>
        <span class="d-inline-flex align-items-center gap-1.5"><span class="legend-color-dot" style="background: #a855f7;"></span> Right Pinky</span>
      </div>
    </div>

    <!-- Visual QWERTY Keyboard Grid -->
    <div class="keyboard-board" id="visualKeyboardBoard">
      <!-- Row 1: Numbers -->
      <div class="kb-row">
        <div class="kb-key key-pinky-l" data-key="`" data-char="~">`</div>
        <div class="kb-key key-pinky-l" data-key="1" data-char="!">1</div>
        <div class="kb-key key-ring-l" data-key="2" data-char="@">2</div>
        <div class="kb-key key-middle-l" data-key="3" data-char="#">3</div>
        <div class="kb-key key-index-l" data-key="4" data-char="$">4</div>
        <div class="kb-key key-index-l" data-key="5" data-char="%">5</div>
        <div class="kb-key key-index-r" data-key="6" data-char="^">6</div>
        <div class="kb-key key-index-r" data-key="7" data-char="&">7</div>
        <div class="kb-key key-middle-r" data-key="8" data-char="*">8</div>
        <div class="kb-key key-ring-r" data-key="9" data-char="(">9</div>
        <div class="kb-key key-pinky-r" data-key="0" data-char=")">0</div>
        <div class="kb-key key-pinky-r" data-key="-" data-char="_">-</div>
        <div class="kb-key key-pinky-r" data-key="=" data-char="+">=</div>
        <div class="kb-key kb-key-wide key-pinky-r" data-key="Backspace">⌫</div>
      </div>

      <!-- Row 2: Top Row (QWERTY) -->
      <div class="kb-row">
        <div class="kb-key kb-key-wide key-pinky-l" data-key="Tab">Tab</div>
        <div class="kb-key key-pinky-l" data-key="q" data-char="Q">Q</div>
        <div class="kb-key key-ring-l" data-key="w" data-char="W">W</div>
        <div class="kb-key key-middle-l" data-key="e" data-char="E">E</div>
        <div class="kb-key key-index-l" data-key="r" data-char="R">R</div>
        <div class="kb-key key-index-l" data-key="t" data-char="T">T</div>
        <div class="kb-key key-index-r" data-key="y" data-char="Y">Y</div>
        <div class="kb-key key-index-r" data-key="u" data-char="U">U</div>
        <div class="kb-key key-middle-r" data-key="i" data-char="I">I</div>
        <div class="kb-key key-ring-r" data-key="o" data-char="O">O</div>
        <div class="kb-key key-pinky-r" data-key="p" data-char="P">P</div>
        <div class="kb-key key-pinky-r" data-key="[" data-char="{">[</div>
        <div class="kb-key key-pinky-r" data-key="]" data-char="}">]</div>
        <div class="kb-key key-pinky-r" data-key="\" data-char="|">\</div>
      </div>

      <!-- Row 3: Home Row (ASDF - JKL;) -->
      <div class="kb-row">
        <div class="kb-key kb-key-wider key-pinky-l" data-key="CapsLock">Caps</div>
        <div class="kb-key key-pinky-l" data-key="a" data-char="A">A</div>
        <div class="kb-key key-ring-l" data-key="s" data-char="S">S</div>
        <div class="kb-key key-middle-l" data-key="d" data-char="D">D</div>
        <div class="kb-key key-index-l kb-anchor-key" data-key="f" data-char="F">
          <span>F</span>
          <div class="home-bump"></div>
        </div>
        <div class="kb-key key-index-l" data-key="g" data-char="G">G</div>
        <div class="kb-key key-index-r" data-key="h" data-char="H">H</div>
        <div class="kb-key key-index-r kb-anchor-key" data-key="j" data-char="J">
          <span>J</span>
          <div class="home-bump"></div>
        </div>
        <div class="kb-key key-middle-r" data-key="k" data-char="K">K</div>
        <div class="kb-key key-ring-r" data-key="l" data-char="L">L</div>
        <div class="kb-key key-pinky-r" data-key=";" data-char=":">;</div>
        <div class="kb-key key-pinky-r" data-key="'" data-char="&quot;">'</div>
        <div class="kb-key kb-key-wider key-pinky-r" data-key="Enter">Enter ↵</div>
      </div>

      <!-- Row 4: Bottom Row (ZXCVBNM) -->
      <div class="kb-row">
        <div class="kb-key kb-key-widest key-pinky-l" data-key="ShiftLeft">Shift</div>
        <div class="kb-key key-pinky-l" data-key="z" data-char="Z">Z</div>
        <div class="kb-key key-ring-l" data-key="x" data-char="X">X</div>
        <div class="kb-key key-middle-l" data-key="c" data-char="C">C</div>
        <div class="kb-key key-index-l" data-key="v" data-char="V">V</div>
        <div class="kb-key key-index-l" data-key="b" data-char="B">B</div>
        <div class="kb-key key-index-r" data-key="n" data-char="N">N</div>
        <div class="kb-key key-index-r" data-key="m" data-char="M">M</div>
        <div class="kb-key key-middle-r" data-key="," data-char="<">,</div>
        <div class="kb-key key-ring-r" data-key="." data-char=">">.</div>
        <div class="kb-key key-pinky-r" data-key="/" data-char="?">/</div>
        <div class="kb-key kb-key-widest key-pinky-r" data-key="ShiftRight">Shift</div>
      </div>

      <!-- Row 5: Space Bar -->
      <div class="kb-row">
        <div class="kb-key key-pinky-l" data-key="Control">Ctrl</div>
        <div class="kb-key key-pinky-l" data-key="Alt">Alt</div>
        <div class="kb-key kb-key-space key-thumb" data-key=" " data-char=" ">Space Bar</div>
        <div class="kb-key key-pinky-r" data-key="AltGraph">Alt</div>
        <div class="kb-key key-pinky-r" data-key="Control">Ctrl</div>
      </div>
    </div>
  </div>
</div>

<!-- Lesson Completion Modal -->
<div class="modal fade" id="lessonCompleteModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content glass-card p-4 text-center border-0" style="background: rgba(18, 18, 18, 0.98); border: 1px solid var(--border-color); border-radius: 20px; box-shadow: var(--shadow-lg);">
      <!-- Confetti Canvas -->
      <canvas id="confettiCanvas" style="position: absolute; top:0; left:0; width:100%; height:100%; pointer-events: none; z-index: 1;"></canvas>

      <div style="position: relative; z-index: 2;">
        <div class="mb-3 d-inline-flex p-3 rounded-circle" style="background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3);">
          <i class="bi bi-award-fill fs-2"></i>
        </div>

        <h3 class="fw-bold text-white mb-1" style="letter-spacing: -0.03em;">Lesson Complete!</h3>
        <p class="text-secondary small mb-4">Great work! You have finished all exercises in this lesson.</p>

        <!-- Stars Earned Container -->
        <div class="d-flex align-items-center justify-content-center gap-2 mb-4" id="modalStarsContainer">
          <i class="bi bi-star-fill text-warning fs-1 star-pop" style="animation-delay: 0.1s;"></i>
          <i class="bi bi-star-fill text-warning fs-1 star-pop" style="animation-delay: 0.25s;"></i>
          <i class="bi bi-star-fill text-warning fs-1 star-pop" style="animation-delay: 0.4s;"></i>
        </div>

        <!-- Result Metrics Cards -->
        <div class="row g-2 mb-4">
          <div class="col-4">
            <div class="p-2.5 rounded-3" style="background: var(--bg-primary); border: 1px solid var(--border-color);">
              <div class="text-secondary" style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase;">Speed</div>
              <div class="fw-bold text-white fs-5 font-monospace" id="modalWpm">0</div>
              <div class="text-secondary" style="font-size: 0.65rem;">WPM</div>
            </div>
          </div>
          <div class="col-4">
            <div class="p-2.5 rounded-3" style="background: var(--bg-primary); border: 1px solid var(--border-color);">
              <div class="text-secondary" style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase;">Accuracy</div>
              <div class="fw-bold text-white fs-5 font-monospace" id="modalAccuracy">0%</div>
              <div class="text-secondary" style="font-size: 0.65rem;">Precision</div>
            </div>
          </div>
          <div class="col-4">
            <div class="p-2.5 rounded-3" style="background: var(--bg-primary); border: 1px solid var(--border-color);">
              <div class="text-secondary" style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase;">Time</div>
              <div class="fw-bold text-white fs-5 font-monospace" id="modalTime">0s</div>
              <div class="text-secondary" style="font-size: 0.65rem;">Duration</div>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="d-flex flex-column gap-2">
          <a href="#" id="modalNextLessonBtn" class="btn btn-primary py-2.5 fw-bold d-flex align-items-center justify-content-center gap-2" style="background: var(--first-color); border: none; border-radius: 10px;">
            <span>Next Lesson</span>
            <i class="bi bi-arrow-right"></i>
          </a>
          <button type="button" class="btn btn-outline-secondary py-2 fw-semibold text-white" id="modalRetryBtn" style="border-radius: 10px; border-color: var(--border-color);">
            <i class="bi bi-arrow-counterclockwise me-1"></i> Practice This Lesson Again
          </button>
          <a href="<?= BASE_URL ?>/lessons.php" class="btn btn-link text-secondary text-decoration-none small py-1">
            Back to Curriculum Overview
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
/* Step Pills Bar */
.step-pill-wrapper {
  position: relative;
}
.step-pill {
  height: 8px;
  background: rgba(255,255,255,0.08);
  border-radius: 4px;
  transition: all 0.3s ease;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
}
.step-pill.active {
  background: var(--first-color);
  box-shadow: 0 0 10px rgba(255, 106, 0, 0.4);
}
.step-pill.completed {
  background: #10b981;
}
.step-pill .step-num,
.step-pill .step-check {
  display: none;
}

/* Typing Stream Area */
.char-span {
  display: inline-block;
  position: relative;
  transition: color 0.05s ease, background-color 0.05s ease;
}
.char-correct {
  color: #10b981;
}
.char-incorrect {
  color: #f43f5e;
  background: rgba(244, 63, 94, 0.18);
  border-radius: 2px;
}
.char-active {
  color: var(--text-primary);
  background: rgba(255, 255, 255, 0.1);
  border-radius: 4px;
  box-shadow: 0 0 0 2px var(--first-color);
  animation: pulse-caret 1s infinite alternate;
}
@keyframes pulse-caret {
  0% { box-shadow: 0 0 0 2px var(--first-color); }
  100% { box-shadow: 0 0 10px 3px var(--first-color); }
}

/* Finger Hint Dot */
.finger-zone-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background: var(--first-color);
  display: inline-block;
}
.legend-color-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  display: inline-block;
}

/* Visual Keyboard Board */
.keyboard-board {
  max-width: 880px;
  margin: 0 auto;
  user-select: none;
}
.kb-row {
  display: flex;
  justify-content: center;
  gap: 6px;
  margin-bottom: 6px;
}
.kb-key {
  width: 44px;
  height: 44px;
  background: var(--bg-primary);
  border: 1px solid var(--border-color);
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: var(--typing-font), monospace;
  font-weight: 700;
  font-size: 0.88rem;
  color: var(--text-primary);
  box-shadow: 0 2px 0 rgba(0,0,0,0.4);
  transition: all 0.08s ease;
  position: relative;
}

/* Width multipliers */
.kb-key-wide { width: 68px; font-size: 0.78rem; }
.kb-key-wider { width: 78px; font-size: 0.78rem; }
.kb-key-widest { width: 98px; font-size: 0.78rem; }
.kb-key-space { width: 280px; font-size: 0.78rem; }

/* Finger Zone Colors for border/accent */
.key-pinky-l  { border-bottom: 3px solid #a855f7; }
.key-ring-l   { border-bottom: 3px solid #3b82f6; }
.key-middle-l { border-bottom: 3px solid #06b6d4; }
.key-index-l  { border-bottom: 3px solid #10b981; }
.key-thumb    { border-bottom: 3px solid #f59e0b; }
.key-index-r  { border-bottom: 3px solid #eab308; }
.key-middle-r { border-bottom: 3px solid #06b6d4; }
.key-ring-r   { border-bottom: 3px solid #3b82f6; }
.key-pinky-r  { border-bottom: 3px solid #a855f7; }

/* Tactile Bump on F and J */
.kb-anchor-key .home-bump {
  position: absolute;
  bottom: 5px;
  width: 14px;
  height: 2px;
  background: var(--text-secondary);
  border-radius: 2px;
}

/* Active Highlight Key (Current Target) */
.kb-key.kb-target-key {
  background: var(--first-color) !important;
  color: #ffffff !important;
  border-color: #ffffff !important;
  box-shadow: 0 0 16px var(--first-color), 0 2px 0 #b33900 !important;
  transform: translateY(-2px);
  animation: key-bounce 0.8s infinite alternate ease-in-out;
}
@keyframes key-bounce {
  0% { transform: translateY(-2px); }
  100% { transform: translateY(-4px) scale(1.05); }
}

/* Physical Key Pressed */
.kb-key.kb-pressed {
  transform: translateY(2px);
  box-shadow: 0 0 0 rgba(0,0,0,0);
  background: rgba(255,255,255,0.25) !important;
}

/* Star Pop Animation in Modal */
.star-pop {
  animation: starPop 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) both;
}
@keyframes starPop {
  0% { transform: scale(0) rotate(-45deg); opacity: 0; }
  100% { transform: scale(1) rotate(0deg); opacity: 1; }
}

@media (max-width: 768px) {
  .kb-key {
    width: 28px;
    height: 34px;
    font-size: 0.72rem;
  }
  .kb-key-wide { width: 44px; }
  .kb-key-wider { width: 50px; }
  .kb-key-widest { width: 60px; }
  .kb-key-space { width: 140px; }
  .typing-text-display { font-size: 1.4rem !important; }
}
</style>

<!-- Pass lesson data cleanly into JavaScript -->
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

<!-- Interactive Lesson Engine Controller -->
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

  // DOM Elements
  const textDisplay = document.getElementById('lessonTextDisplay');
  const focusPrompt = document.getElementById('lessonFocusPrompt');
  const virtualInput = document.getElementById('lessonVirtualInput');
  const hudWpm = document.getElementById('hud-wpm');
  const hudAcc = document.getElementById('hud-acc');
  const currentStepText = document.getElementById('currentStepText');
  const fingerHintBadge = document.getElementById('fingerHintBadge');
  const fingerHintText = document.getElementById('fingerHintText');
  const fingerHintDot = document.getElementById('fingerHintDot');
  const soundBtn = document.getElementById('toggle-sound-btn');
  const soundIcon = document.getElementById('sound-icon');
  const kbToggleBtn = document.getElementById('toggle-keyboard-btn');
  const visualKbContainer = document.getElementById('visualKeyboardContainer');

  // Web Audio Synthesizer
  let audioCtx = null;
  function playBeep(type = 'click') {
    if (!soundEnabled) return;
    try {
      if (!audioCtx) {
        const AudioClass = window.AudioContext || window.webkitAudioContext;
        if (AudioClass) audioCtx = new AudioClass();
      }
      if (audioCtx && audioCtx.state === 'suspended') {
        audioCtx.resume();
      }
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
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.start(now);
        osc.stop(now + 0.04);
      } else if (type === 'error') {
        osc.type = 'sawtooth';
        osc.frequency.setValueAtTime(140, now);
        osc.frequency.linearRampToValueAtTime(90, now + 0.1);
        gain.gain.setValueAtTime(0.25, now);
        gain.gain.linearRampToValueAtTime(0.01, now + 0.1);
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.start(now);
        osc.stop(now + 0.1);
      } else if (type === 'chime') {
        [523.25, 659.25, 783.99].forEach((freq, i) => {
          const o = audioCtx.createOscillator();
          const g = audioCtx.createGain();
          o.type = 'sine';
          o.frequency.setValueAtTime(freq, now + i * 0.08);
          g.gain.setValueAtTime(0.18, now + i * 0.08);
          g.gain.exponentialRampToValueAtTime(0.001, now + i * 0.08 + 0.28);
          o.connect(g);
          g.connect(audioCtx.destination);
          o.start(now + i * 0.08);
          o.stop(now + i * 0.08 + 0.28);
        });
      }
    } catch(e) {}
  }

  // Load an exercise step
  function loadStep(index) {
    if (index >= exercises.length) {
      finishWholeLesson();
      return;
    }

    currentStepIndex = index;
    targetText = exercises[index];
    currentIndex = 0;
    isStepFinished = false;
    stepStartTime = null;

    currentStepText.textContent = (index + 1);

    // Update Step Pills
    document.querySelectorAll('.step-pill-wrapper').forEach((w, idx) => {
      const pill = w.querySelector('.step-pill');
      pill.classList.remove('active');
      if (idx < index) {
        pill.classList.add('completed');
      } else if (idx === index) {
        pill.classList.add('active');
      }
    });

    // Render characters
    textDisplay.innerHTML = '';
    for (let i = 0; i < targetText.length; i++) {
      const span = document.createElement('span');
      span.className = 'char-span' + (i === 0 ? ' char-active' : '');
      span.textContent = targetText[i];
      span.id = 'char-' + i;
      textDisplay.appendChild(span);
    }

    updateActiveKeyGuide();
    focusInput();
  }

  // Update on-screen keyboard target highlight & finger hint
  function updateActiveKeyGuide() {
    // Clear existing target keys
    document.querySelectorAll('.kb-key.kb-target-key').forEach(el => el.classList.remove('kb-target-key'));

    if (currentIndex >= targetText.length) return;
    const nextChar = targetText[currentIndex];

    // Find on-screen key
    let matchedKey = null;
    const allKeys = document.querySelectorAll('.kb-key');
    allKeys.forEach(k => {
      const keyVal = k.getAttribute('data-key');
      const charVal = k.getAttribute('data-char');
      if (nextChar === ' ' && keyVal === ' ') {
        matchedKey = k;
      } else if (keyVal && keyVal.toLowerCase() === nextChar.toLowerCase()) {
        matchedKey = k;
      } else if (charVal && charVal === nextChar) {
        matchedKey = k;
      }
    });

    if (matchedKey) {
      matchedKey.classList.add('kb-target-key');
    }

    // Finger guide text
    const lowerChar = nextChar.toLowerCase();
    const hint = config.fingerHints && config.fingerHints[lowerChar] 
      ? config.fingerHints[lowerChar] 
      : getGenericFingerHint(nextChar);

    if (hint) {
      fingerHintText.innerHTML = `Press <strong class="text-white">${nextChar === ' ' ? 'SPACE' : nextChar.toUpperCase()}</strong> with your <strong style="color: var(--first-color);">${hint.finger}</strong>`;
      fingerHintDot.style.background = getFingerZoneColor(hint.finger);
    }
  }

  function getGenericFingerHint(ch) {
    ch = ch.toLowerCase();
    if (ch === ' ') return { finger: 'Thumb' };
    if ('1qaz'.includes(ch)) return { finger: 'Left Pinky' };
    if ('2wsx'.includes(ch)) return { finger: 'Left Ring' };
    if ('3edc'.includes(ch)) return { finger: 'Left Middle' };
    if ('45rtfgvb'.includes(ch)) return { finger: 'Left Index' };
    if ('67yuhjnm'.includes(ch)) return { finger: 'Right Index' };
    if ('8ik,'.includes(ch)) return { finger: 'Right Middle' };
    if ('9ol.'.includes(ch)) return { finger: 'Right Ring' };
    if ('0p;/-=[]\''.includes(ch)) return { finger: 'Right Pinky' };
    return { finger: 'Index Finger' };
  }

  function getFingerZoneColor(fingerName) {
    if (fingerName.includes('Pinky')) return '#a855f7';
    if (fingerName.includes('Ring')) return '#3b82f6';
    if (fingerName.includes('Middle')) return '#06b6d4';
    if (fingerName.includes('Left Index')) return '#10b981';
    if (fingerName.includes('Right Index')) return '#eab308';
    if (fingerName.includes('Thumb')) return '#f59e0b';
    return 'var(--first-color)';
  }

  // Handle typing input
  function handleInputChar(charTyped) {
    if (isStepFinished) return;

    if (!overallStartTime) {
      overallStartTime = performance.now();
    }
    if (!stepStartTime) {
      stepStartTime = performance.now();
    }

    totalKeystrokes++;
    const expectedChar = targetText[currentIndex];

    // Animate visual keyboard physical press
    highlightPhysicalKey(charTyped);

    if (charTyped === expectedChar) {
      // Correct!
      correctKeystrokes++;
      playBeep('click');

      const span = document.getElementById('char-' + currentIndex);
      if (span) {
        span.className = 'char-span char-correct';
      }

      currentIndex++;

      if (currentIndex < targetText.length) {
        const nextSpan = document.getElementById('char-' + currentIndex);
        if (nextSpan) {
          nextSpan.className = 'char-span char-active';
        }
        updateActiveKeyGuide();
      } else {
        // Step complete!
        isStepFinished = true;
        playBeep('chime');
        updateTelemetries();

        setTimeout(() => {
          loadStep(currentStepIndex + 1);
        }, 320);
      }
    } else {
      // Mistake!
      errorCount++;
      playBeep('error');

      const span = document.getElementById('char-' + currentIndex);
      if (span) {
        span.classList.add('char-incorrect');
        setTimeout(() => span.classList.remove('char-incorrect'), 280);
      }
    }

    updateTelemetries();
  }

  // Animate on-screen key press for physical key
  function highlightPhysicalKey(char) {
    const allKeys = document.querySelectorAll('.kb-key');
    allKeys.forEach(k => {
      const keyVal = k.getAttribute('data-key');
      const charVal = k.getAttribute('data-char');
      if ((char === ' ' && keyVal === ' ') || 
          (keyVal && keyVal.toLowerCase() === char.toLowerCase()) || 
          (charVal && charVal === char)) {
        k.classList.add('kb-pressed');
        setTimeout(() => k.classList.remove('kb-pressed'), 140);
      }
    });
  }

  function updateTelemetries() {
    if (!overallStartTime) return;
    const elapsedMinutes = (performance.now() - overallStartTime) / 60000;
    const grossWpm = elapsedMinutes > 0 ? Math.round((correctKeystrokes / 5) / elapsedMinutes) : 0;
    const accuracy = totalKeystrokes > 0 ? Math.round((correctKeystrokes / totalKeystrokes) * 100) : 100;

    hudWpm.innerHTML = `${grossWpm} <span class="text-secondary" style="font-size: 0.7rem;">WPM</span>`;
    hudAcc.innerHTML = `${accuracy}<span class="text-secondary" style="font-size: 0.7rem;">%</span>`;
  }

  function finishWholeLesson() {
    const elapsedSeconds = Math.max(1, Math.round((performance.now() - overallStartTime) / 1000));
    const elapsedMinutes = elapsedSeconds / 60;
    const finalWpm = elapsedMinutes > 0 ? Math.round((correctKeystrokes / 5) / elapsedMinutes) : 0;
    const finalAccuracy = totalKeystrokes > 0 ? Math.round((correctKeystrokes / totalKeystrokes) * 100) : 100;

    // Determine Stars (Typing.com standard: 95%+ = 3 stars, 85%+ = 2 stars, 70%+ = 1 star)
    let stars = 1;
    if (finalAccuracy >= 95) stars = 3;
    else if (finalAccuracy >= 85) stars = 2;

    // Fill modal
    document.getElementById('modalWpm').textContent = finalWpm;
    document.getElementById('modalAccuracy').textContent = finalAccuracy + '%';
    document.getElementById('modalTime').textContent = elapsedSeconds + 's';

    const starsContainer = document.getElementById('modalStarsContainer');
    starsContainer.innerHTML = '';
    for (let s = 1; s <= 3; s++) {
      const star = document.createElement('i');
      star.className = `bi bi-star${s <= stars ? '-fill text-warning' : ' text-secondary'} fs-1 star-pop`;
      star.style.animationDelay = (s * 0.15) + 's';
      starsContainer.appendChild(star);
    }

    // Save progress asynchronously
    saveLessonProgress(stars, finalWpm, finalAccuracy);

    // Launch Confetti Celebration
    launchConfetti();

    // Show modal
    const modalEl = document.getElementById('lessonCompleteModal');
    const modal = new bootstrap.Modal(modalEl);
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
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': config.csrfToken
      },
      body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(data => {
      if (data && data.next_lesson_id) {
        document.getElementById('modalNextLessonBtn').href = 'lesson-player.php?id=' + encodeURIComponent(data.next_lesson_id);
      } else {
        document.getElementById('modalNextLessonBtn').href = config.curriculumUrl;
      }
    })
    .catch(() => {
      document.getElementById('modalNextLessonBtn').href = config.curriculumUrl;
    });
  }

  // Pure Canvas Confetti particles
  function launchConfetti() {
    const canvas = document.getElementById('confettiCanvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    canvas.width = canvas.parentElement.offsetWidth;
    canvas.height = canvas.parentElement.offsetHeight;

    const particles = [];
    const colors = ['#ff5532', '#10b981', '#38bdf8', '#facc15', '#a855f7', '#ffffff'];

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
      particles.forEach(p => {
        p.x += p.vx;
        p.y += p.vy;
        p.vy += 0.35; // gravity
        p.rotation += p.vr;

        ctx.save();
        ctx.translate(p.x, p.y);
        ctx.rotate((p.rotation * Math.PI) / 180);
        ctx.fillStyle = p.color;
        ctx.fillRect(-p.size / 2, -p.size / 2, p.size, p.size);
        ctx.restore();
      });

      frame++;
      if (frame < 120) {
        requestAnimationFrame(animate);
      } else {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
      }
    }
    requestAnimationFrame(animate);
  }

  function focusInput() {
    focusPrompt.style.opacity = '0';
    focusPrompt.style.pointerEvents = 'none';
    virtualInput.focus();
  }

  // Event Listeners
  focusPrompt.addEventListener('click', focusInput);
  document.getElementById('lessonArenaContainer').addEventListener('click', focusInput);

  window.addEventListener('keydown', (e) => {
    // Ignore function keys / tabs / shortcuts
    if (e.ctrlKey || e.altKey || e.metaKey) return;
    if (e.key === 'Tab') { e.preventDefault(); return; }

    focusPrompt.style.opacity = '0';
    focusPrompt.style.pointerEvents = 'none';

    if (e.key.length === 1) {
      e.preventDefault();
      handleInputChar(e.key);
    }
  });

  soundBtn.addEventListener('click', () => {
    soundEnabled = !soundEnabled;
    soundIcon.className = soundEnabled ? 'bi bi-volume-up-fill fs-6' : 'bi bi-volume-mute-fill fs-6 text-secondary';
  });

  kbToggleBtn.addEventListener('click', () => {
    if (visualKbContainer.style.display === 'none') {
      visualKbContainer.style.display = 'block';
    } else {
      visualKbContainer.style.display = 'none';
    }
  });

  document.getElementById('modalRetryBtn').addEventListener('click', () => {
    location.reload();
  });

  // Start with Step 0
  loadStep(0);
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
