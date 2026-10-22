<?php
/**
 * Custom Type Master Pro
 * Landing Page / Showcase
 */

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/auth.php';

// If user is already authenticated, redirect to personal dashboard
if (is_logged_in()) {
    header('Location: ' . BASE_URL . '/dashboard.php');
    exit;
}

$pageTitle = 'Master Your Typing Speed & Precision';
include __DIR__ . '/includes/header.php';
?>

<div class="container py-4 py-lg-5">
  <!-- Hero Section -->
  <div class="row align-items-center g-4 g-lg-5 mb-5">
    <div class="col-lg-6">
      <div class="d-inline-flex align-items-center gap-2 px-3 py-1 mb-3 rounded-pill" style="background: var(--first-color-light); border: 1px solid rgba(255, 85, 50, 0.25);">
        <i class="bi bi-fire" style="color: var(--first-color);"></i>
        <span style="color: var(--first-color); font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Pure Modern Web &bull; Version 2.0</span>
      </div>

      <h1 class="display-4 fw-extrabold mb-3 lh-sm" style="color: var(--text-primary); letter-spacing: -1px;">
        Accelerate Your <span style="background: var(--accent-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Typing Speed</span> With Precision Analytics.
      </h1>

      <p class="lead text-secondary mb-4" style="font-size: 1.15rem; line-height: 1.7;">
        Custom Type Master Pro gives you real-time keystroke diagnostics, scientific Gross & Net WPM formulas, distraction-free practice arenas, and in-depth speed trajectories.
      </p>

      <div class="d-flex flex-wrap gap-3 mb-4">
        <a href="<?= BASE_URL ?>/register.php" class="soft-btn soft-btn-primary px-4 py-3 fw-bold fs-6">
          <i class="bi bi-rocket-takeoff-fill"></i>
          <span>Create Free Account</span>
        </a>
        <a href="<?= BASE_URL ?>/practice.php" class="soft-btn px-4 py-3 fw-bold fs-6">
          <i class="bi bi-play-circle-fill" style="color: var(--first-color);"></i>
          <span>Full Practice Arena</span>
        </a>
        <a href="<?= BASE_URL ?>/login.php" class="soft-btn px-4 py-3 fw-bold fs-6" style="color: var(--second-color);">
          <span>Sign In</span>
        </a>
      </div>

      <!-- Quick Proof Highlights -->
      <div class="row g-3 pt-3 border-top" style="border-color: var(--border-color) !important;">
        <div class="col-4">
          <div class="fw-extrabold fs-3" style="color: var(--text-primary); line-height: 1.1;">&lt; 5ms</div>
          <div class="small text-secondary fw-semibold">Zero Input Lag</div>
        </div>
        <div class="col-4">
          <div class="fw-extrabold fs-3" style="color: var(--first-color); line-height: 1.1;">Gross &amp; Net</div>
          <div class="small text-secondary fw-semibold">Scientific Formulas</div>
        </div>
        <div class="col-4">
          <div class="fw-extrabold fs-3 text-success" style="line-height: 1.1;">100% Free</div>
          <div class="small text-secondary fw-semibold">No Grain &bull; Pure CSS</div>
        </div>
      </div>
    </div>

    <!-- Right Column: LIVE INLINE SPEED TEST ARENA (Like Dashboard) -->
    <div class="col-lg-6">
      <div class="soft-card p-4 position-relative" id="inlineArenaCard">
        <!-- Top Controls: Mode & Time Selector -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pb-3 mb-3 border-bottom" style="border-color: var(--border-color) !important;">
          <div class="d-flex align-items-center gap-2">
            <span class="badge rounded-pill px-2 py-1" style="background: var(--first-color-light); color: var(--first-color); font-weight: 700; font-size: 0.72rem;">
              <i class="bi bi-lightning-charge-fill me-1"></i> LIVE SPEED TEST
            </span>
            <span class="text-secondary small fw-semibold">Click &amp; Type to Start</span>
          </div>

          <div class="d-flex gap-1" id="inlineDurationPicker">
            <button type="button" class="btn btn-sm py-1 px-2 rounded-pill duration-btn active" data-duration="15" style="font-size: 0.75rem; font-weight: 700;">15s</button>
            <button type="button" class="btn btn-sm py-1 px-2 rounded-pill duration-btn" data-duration="30" style="font-size: 0.75rem; font-weight: 700;">30s</button>
            <button type="button" class="btn btn-sm py-1 px-2 rounded-pill duration-btn" data-duration="60" style="font-size: 0.75rem; font-weight: 700;">60s</button>
          </div>
        </div>

        <!-- Live Metrics HUD Bar -->
        <div class="row g-2 mb-3 text-center">
          <div class="col-3">
            <div class="p-2 rounded-3" style="background: var(--bg-primary); border: 1px solid var(--border-color); box-shadow: var(--soft-inset);">
              <div class="text-secondary small" style="font-size: 0.68rem; font-weight: 700; text-transform: uppercase;">WPM</div>
              <div class="fs-4 fw-extrabold" id="inlineWpm" style="color: var(--first-color); line-height: 1.1;">0</div>
            </div>
          </div>
          <div class="col-3">
            <div class="p-2 rounded-3" style="background: var(--bg-primary); border: 1px solid var(--border-color); box-shadow: var(--soft-inset);">
              <div class="text-secondary small" style="font-size: 0.68rem; font-weight: 700; text-transform: uppercase;">Net WPM</div>
              <div class="fs-4 fw-extrabold text-success" id="inlineNetWpm" style="line-height: 1.1;">0</div>
            </div>
          </div>
          <div class="col-3">
            <div class="p-2 rounded-3" style="background: var(--bg-primary); border: 1px solid var(--border-color); box-shadow: var(--soft-inset);">
              <div class="text-secondary small" style="font-size: 0.68rem; font-weight: 700; text-transform: uppercase;">Accuracy</div>
              <div class="fs-4 fw-extrabold" id="inlineAcc" style="color: var(--text-primary); line-height: 1.1;">100%</div>
            </div>
          </div>
          <div class="col-3">
            <div class="p-2 rounded-3" style="background: var(--bg-primary); border: 1px solid var(--border-color); box-shadow: var(--soft-inset);">
              <div class="text-secondary small" style="font-size: 0.68rem; font-weight: 700; text-transform: uppercase;">Timer</div>
              <div class="fs-4 fw-extrabold text-info" id="inlineTimer" style="line-height: 1.1;">15s</div>
            </div>
          </div>
        </div>

        <!-- Interactive Typing Board -->
        <div class="position-relative p-3 p-md-4 rounded-3 mb-3" id="inlineBoardWrapper" style="background: var(--bg-primary); border: 2px solid var(--border-color); box-shadow: var(--soft-inset); min-height: 155px; cursor: text; user-select: none;">
          <input type="text" id="inlineHiddenInput" autocomplete="off" autocapitalize="off" spellcheck="false" style="position: absolute; opacity: 0; left: -9999px; top: -9999px;">
          
          <div id="inlineTextDisplay" style="font-family: 'JetBrains Mono', monospace; font-size: 1.1rem; line-height: 1.8; color: var(--text-secondary); word-break: break-word;">
            <!-- Characters will be dynamically rendered here -->
          </div>

          <!-- Finished Overlay Banner -->
          <div id="inlineFinishedOverlay" class="position-absolute inset-0 d-none flex-column align-items-center justify-content-center p-3 text-center rounded-3" style="top: 0; left: 0; right: 0; bottom: 0; background: rgba(255, 255, 255, 0.94); backdrop-filter: blur(4px); z-index: 5;">
            <div class="badge px-3 py-1 mb-2 rounded-pill" style="background: var(--first-color); color: #fff; font-weight: 700;">Test Completed!</div>
            <h4 class="fw-bold mb-2" style="color: var(--text-primary);">
              Speed: <span id="overlayNetWpm" style="color: var(--first-color);">0</span> WPM &bull; <span id="overlayAcc">100</span>% Accuracy
            </h4>
            <p class="text-secondary small mb-3">Join other typists to track your daily progress and climb the leaderboard.</p>
            <div class="d-flex gap-2">
              <a href="<?= BASE_URL ?>/register.php" class="soft-btn soft-btn-primary btn-sm px-3 py-2 fw-bold">Save Score / Register</a>
              <button type="button" class="soft-btn btn-sm px-3 py-2" id="inlineRetryBtn">Retry Test</button>
            </div>
          </div>
        </div>

        <!-- Bottom Controls & Hints -->
        <div class="d-flex align-items-center justify-content-between text-secondary small">
          <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-outline-secondary px-2 py-1 rounded-pill" id="inlineResetBtn" title="Restart test">
              <i class="bi bi-arrow-clockwise me-1"></i> Restart
            </button>
            <span class="d-none d-sm-inline">Press <kbd class="px-1 rounded small" style="background: var(--bg-card); color: var(--first-color); border: 1px solid var(--border-color);">Tab</kbd> + <kbd class="px-1 rounded small" style="background: var(--bg-card); color: var(--first-color); border: 1px solid var(--border-color);">Enter</kbd></span>
          </div>
          <div>
            <a href="<?= BASE_URL ?>/practice.php" class="fw-bold text-decoration-none" style="color: var(--first-color);">
              Go to Full Arena <i class="bi bi-arrow-right"></i>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Key Features Grid: Modern Soft UI Cards -->
  <div class="row g-4 py-4 mb-4">
    <div class="col-md-4">
      <div class="soft-card h-100 p-4">
        <div class="stat-icon mb-3" style="background: var(--first-color-light); color: var(--first-color); width: 46px; height: 46px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.35rem; box-shadow: var(--soft-flat);">
          <i class="bi bi-calculator-fill"></i>
        </div>
        <h4 class="fw-bold mb-2" style="color: var(--text-primary); font-size: 1.25rem;">Scientific WPM Math</h4>
        <p class="text-secondary small mb-0" style="line-height: 1.6;">
          Gross WPM divides typed characters by standardized 5-character word lengths. Net WPM penalizes uncorrected errors per elapsed minute for true competitive accuracy.
        </p>
      </div>
    </div>

    <div class="col-md-4">
      <div class="soft-card h-100 p-4">
        <div class="stat-icon mb-3" style="background: rgba(16, 185, 129, 0.12); color: #10b981; width: 46px; height: 46px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.35rem; box-shadow: var(--soft-flat);">
          <i class="bi bi-graph-up-arrow"></i>
        </div>
        <h4 class="fw-bold mb-2" style="color: var(--text-primary); font-size: 1.25rem;">Live Analytics &amp; Trends</h4>
        <p class="text-secondary small mb-0" style="line-height: 1.6;">
          Monitor your speed trajectory over time with Chart.js visualization, discover your weak keys, review accuracy percentages, and export session logs to CSV and JSON.
        </p>
      </div>
    </div>

    <div class="col-md-4">
      <div class="soft-card h-100 p-4">
        <div class="stat-icon mb-3" style="background: rgba(56, 189, 248, 0.12); color: #0284c7; width: 46px; height: 46px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.35rem; box-shadow: var(--soft-flat);">
          <i class="bi bi-sliders2-vertical"></i>
        </div>
        <h4 class="fw-bold mb-2" style="color: var(--text-primary); font-size: 1.25rem;">Full UI Customization</h4>
        <p class="text-secondary small mb-0" style="line-height: 1.6;">
          Choose between Soft UI White, Signature Obsidian, Cyberpunk, and Nord Arctic. Adjust font sizing, caret styles (line, block, underline), and auditory feedback cues.
        </p>
      </div>
    </div>
  </div>

  <!-- Bottom Call to Action Banner -->
  <div class="soft-card p-4 p-md-5 text-center mt-3 position-relative overflow-hidden">
    <div style="max-width: 680px; margin: 0 auto;">
      <h2 class="fw-bold mb-2" style="color: var(--text-primary); letter-spacing: -0.5px;">Start Your Typing Journey Today</h2>
      <p class="text-secondary mb-4">Join developers, students, and competitive typists worldwide who practice with Custom Type Master Pro.</p>
      <div class="d-flex flex-wrap justify-content-center gap-3">
        <a href="<?= BASE_URL ?>/register.php" class="soft-btn soft-btn-primary px-4 py-3 fw-bold">
          <i class="bi bi-person-plus-fill me-1"></i> Register Free Account
        </a>
        <a href="<?= BASE_URL ?>/practice.php" class="soft-btn px-4 py-3 fw-bold">
          <i class="bi bi-keyboard me-1" style="color: var(--first-color);"></i> Practice as Guest
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Inline Interactive Speed Engine Script -->
<script>
(function() {
  const passages = [
    "Programming is the art of expressing algorithms in languages computers can execute reliably.",
    "Mastering touch typing increases coding productivity, frees cognitive load, and builds muscle memory.",
    "Web applications combine front-end reactivity with robust backend database systems for extreme scale.",
    "The keyboard is the primary tool of the modern digital creator; precision leads to true mastery."
  ];

  let currentPassage = passages[0];
  let duration = 15;
  let timeLeft = duration;
  let timerInterval = null;
  let hasStarted = false;
  let isFinished = false;
  let charIndex = 0;
  let correctKeystrokes = 0;
  let errorKeystrokes = 0;
  let startTime = null;

  const card = document.getElementById('inlineArenaCard');
  const board = document.getElementById('inlineBoardWrapper');
  const hiddenInput = document.getElementById('inlineHiddenInput');
  const textDisplay = document.getElementById('inlineTextDisplay');
  const wpmEl = document.getElementById('inlineWpm');
  const netWpmEl = document.getElementById('inlineNetWpm');
  const accEl = document.getElementById('inlineAcc');
  const timerEl = document.getElementById('inlineTimer');
  const overlay = document.getElementById('inlineFinishedOverlay');
  const overlayNetWpm = document.getElementById('overlayNetWpm');
  const overlayAcc = document.getElementById('overlayAcc');
  const resetBtn = document.getElementById('inlineResetBtn');
  const retryBtn = document.getElementById('inlineRetryBtn');
  const durationBtns = document.querySelectorAll('#inlineDurationPicker .duration-btn');

  function initPassage() {
    currentPassage = passages[Math.floor(Math.random() * passages.length)];
    textDisplay.innerHTML = '';
    for (let i = 0; i < currentPassage.length; i++) {
      const span = document.createElement('span');
      span.textContent = currentPassage[i];
      span.className = i === 0 ? 'inline-char current-char' : 'inline-char';
      textDisplay.appendChild(span);
    }
  }

  function resetTest() {
    clearInterval(timerInterval);
    timerInterval = null;
    hasStarted = false;
    isFinished = false;
    charIndex = 0;
    correctKeystrokes = 0;
    errorKeystrokes = 0;
    startTime = null;
    timeLeft = duration;

    wpmEl.textContent = '0';
    netWpmEl.textContent = '0';
    accEl.textContent = '100%';
    timerEl.textContent = `${duration}s`;
    overlay.classList.add('d-none');
    overlay.classList.remove('d-flex');
    hiddenInput.value = '';

    initPassage();
    hiddenInput.focus();
  }

  function startTimer() {
    hasStarted = true;
    startTime = Date.now();
    timerInterval = setInterval(() => {
      timeLeft--;
      timerEl.textContent = `${timeLeft}s`;
      updateMetrics();

      if (timeLeft <= 0) {
        finishTest();
      }
    }, 1000);
  }

  function updateMetrics() {
    if (!startTime) return;
    const elapsedMinutes = (Date.now() - startTime) / 60000;
    if (elapsedMinutes <= 0.01) return;

    const totalTyped = correctKeystrokes + errorKeystrokes;
    const grossWpm = Math.round((totalTyped / 5) / elapsedMinutes);
    const netWpm = Math.max(0, Math.round(grossWpm - (errorKeystrokes / elapsedMinutes)));
    const acc = totalTyped > 0 ? Math.round((correctKeystrokes / totalTyped) * 100) : 100;

    wpmEl.textContent = grossWpm;
    netWpmEl.textContent = netWpm;
    accEl.textContent = `${acc}%`;
  }

  function finishTest() {
    clearInterval(timerInterval);
    timerInterval = null;
    isFinished = true;
    updateMetrics();

    overlayNetWpm.textContent = netWpmEl.textContent;
    overlayAcc.textContent = accEl.textContent.replace('%', '');
    overlay.classList.remove('d-none');
    overlay.classList.add('d-flex');
  }

  // Handle typing input
  hiddenInput.addEventListener('keydown', (e) => {
    if (isFinished) return;

    if (e.key === 'Tab') {
      return; // allow default tab or Tab+Enter
    }

    if (!hasStarted && e.key.length === 1 && !e.ctrlKey && !e.metaKey && !e.altKey) {
      startTimer();
    }

    if (e.key === 'Backspace') {
      if (charIndex > 0) {
        charIndex--;
        const spans = textDisplay.querySelectorAll('.inline-char');
        spans[charIndex].className = 'inline-char current-char';
        if (spans[charIndex + 1]) {
          spans[charIndex + 1].className = 'inline-char';
        }
      }
      return;
    }

    if (e.key.length === 1 && !e.ctrlKey && !e.metaKey && !e.altKey) {
      if (charIndex < currentPassage.length) {
        const expected = currentPassage[charIndex];
        const spans = textDisplay.querySelectorAll('.inline-char');

        if (e.key === expected) {
          spans[charIndex].className = 'inline-char correct-char';
          correctKeystrokes++;
        } else {
          spans[charIndex].className = 'inline-char error-char';
          errorKeystrokes++;
        }

        charIndex++;
        if (charIndex < currentPassage.length) {
          spans[charIndex].classList.add('current-char');
        } else {
          // Finished passage early!
          finishTest();
        }
        updateMetrics();
      }
    }
  });

  // Focus board when user clicks anywhere in the card
  board.addEventListener('click', () => {
    hiddenInput.focus();
    board.style.borderColor = 'var(--first-color)';
  });

  document.addEventListener('click', (e) => {
    if (!board.contains(e.target)) {
      board.style.borderColor = 'var(--border-color)';
    }
  });

  // Duration buttons
  durationBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      durationBtns.forEach(b => {
        b.classList.remove('active');
        b.style.background = 'transparent';
        b.style.color = 'var(--second-color)';
      });
      btn.classList.add('active');
      btn.style.background = 'var(--first-color)';
      btn.style.color = '#fff';
      duration = parseInt(btn.dataset.duration, 10);
      resetTest();
    });
  });

  resetBtn.addEventListener('click', resetTest);
  retryBtn.addEventListener('click', resetTest);

  // Tab + Enter restart handler
  let tabPressed = false;
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Tab') {
      tabPressed = true;
    }
    if (e.key === 'Enter' && tabPressed) {
      e.preventDefault();
      resetTest();
    }
  });
  document.addEventListener('keyup', (e) => {
    if (e.key === 'Tab') {
      tabPressed = false;
    }
  });

  // Initial setup
  initPassage();
  const firstDurationBtn = document.querySelector('#inlineDurationPicker .duration-btn.active');
  if (firstDurationBtn) {
    firstDurationBtn.style.background = 'var(--first-color)';
    firstDurationBtn.style.color = '#fff';
  }
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
