<?php
/**
 * Custom Type Master Pro
 * Live Typing Practice Arena
 * Includes: Preset Passage Mode, Custom Text Drill, and Blank-Slate Free Typing (Write Anything)
 */

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/functions.php';

$currentUser = current_user();
$settings = $currentUser ? get_user_settings($currentUser['id']) : [
    'theme'               => 'light',
    'font_family'         => 'JetBrains Mono',
    'font_size'           => 22,
    'line_height'         => 1.8,
    'caret_style'         => 'line',
    'caret_color'         => '#38bdf8',
    'correct_color'       => '#4ade80',
    'incorrect_color'     => '#f87171',
    'typing_area_width'   => '920px',
    'sound_enabled'       => 1,
    'sound_theme'         => 'mechanical',
    'default_duration'    => 60,
    'default_difficulty'  => 'intermediate',
    'show_live_wpm'       => 1,
    'show_live_accuracy'  => 1,
    'show_live_word_count'=> 1
];

$pdo = getDBConnection();

// Initial selected difficulty & duration from query or user settings
$selectedDifficulty = $_GET['difficulty'] ?? $settings['default_difficulty'] ?? 'intermediate';
$selectedDuration = (int)($_GET['duration'] ?? $settings['default_duration'] ?? 60);
$passageId = isset($_GET['passage_id']) ? (int)$_GET['passage_id'] : null;

// Initial mode ('passage' or 'free')
$initialMode = $_GET['mode'] ?? 'passage';

// Load passage from DB or default fallback
$activePassage = null;
if ($pdo) {
    try {
        if ($passageId) {
            $stmt = $pdo->prepare("SELECT * FROM typing_passages WHERE id = :id AND status = 'active' LIMIT 1");
            $stmt->execute([':id' => $passageId]);
            $activePassage = $stmt->fetch();
        }

        if (!$activePassage) {
            // Pick a random passage matching difficulty
            $stmt = $pdo->prepare("
                SELECT * FROM typing_passages 
                WHERE difficulty = :diff AND status = 'active' 
                ORDER BY RAND() LIMIT 1
            ");
            $stmt->execute([':diff' => $selectedDifficulty]);
            $activePassage = $stmt->fetch();
        }

        // If still no passage found, get any active passage
        if (!$activePassage) {
            $activePassage = $pdo->query("SELECT * FROM typing_passages WHERE status = 'active' LIMIT 1")->fetch();
        }
    } catch (Exception $e) {
        error_log('Error loading passage: ' . $e->getMessage());
    }
}

// Fallback text if DB is unreachable
if (!$activePassage) {
    $activePassage = [
        'id'          => 0,
        'title'       => 'Welcome to Custom Type Master Pro',
        'difficulty'  => 'intermediate',
        'content'     => 'The World Wide Web relies on a foundational set of open protocols and standards. When a user requests a URL, the browser initiates a DNS lookup, establishes a secure TLS connection, and issues an HTTP request. The server interprets the request, queries relational databases using optimized SQL statements, and returns dynamic HTML, CSS, and JavaScript. Understanding this full-stack lifecycle empowers developers to build responsive and resilient web applications.',
        'word_count'  => 68,
        'char_count'  => 477
    ];
}

$pageTitle = 'Typing Practice Arena';
include __DIR__ . '/includes/header.php';
?>

<div class="container-fluid px-md-4 py-3">

  <!-- =====================================================================
       LAZY SKELETON LOADING SHIMMER WRAPPER (PRACTICE ARENA)
       ===================================================================== -->
  <div id="practice-skeleton-view" class="skeleton-wrapper">
    <!-- Skeleton Mode Switcher -->
    <div class="glass-card p-3 mb-3 skeleton-card d-flex align-items-center justify-content-between">
      <div class="skeleton" style="width: 320px; height: 38px; border-radius: 999px;"></div>
      <div class="d-flex gap-2">
        <div class="skeleton skeleton-circle" style="width: 36px; height: 36px;"></div>
        <div class="skeleton skeleton-circle" style="width: 36px; height: 36px;"></div>
      </div>
    </div>
    <!-- Skeleton The 7 Core Metrics HUD Pills -->
    <div class="d-flex gap-2 mb-3 overflow-hidden flex-wrap">
      <?php for ($i = 0; $i < 7; $i++): ?>
        <div class="skeleton flex-fill p-3" style="min-width: 120px; min-height: 72px; border-radius: 12px;"></div>
      <?php endfor; ?>
    </div>
    <!-- Skeleton Arena Box -->
    <div class="glass-card p-4 skeleton-card mb-3" style="min-height: 240px;">
      <div class="skeleton skeleton-title mb-3" style="width: 40%;"></div>
      <div class="skeleton skeleton-text mb-2" style="width: 95%;"></div>
      <div class="skeleton skeleton-text mb-2" style="width: 88%;"></div>
      <div class="skeleton skeleton-text mb-2" style="width: 92%;"></div>
      <div class="skeleton skeleton-text" style="width: 65%;"></div>
    </div>
    <!-- Skeleton Controls Toolbar -->
    <div class="glass-card p-3 skeleton-card d-flex justify-content-between align-items-center">
      <div class="d-flex gap-2">
        <div class="skeleton" style="width: 110px; height: 36px; border-radius: 8px;"></div>
        <div class="skeleton" style="width: 120px; height: 36px; border-radius: 8px;"></div>
        <div class="skeleton" style="width: 110px; height: 36px; border-radius: 8px;"></div>
      </div>
      <div class="skeleton" style="width: 180px; height: 28px; border-radius: 6px;"></div>
    </div>
  </div>

  <!-- Real Interactive Arena (Smoothly revealed from skeleton) -->
  <div id="practice-content-view" style="display: none;">
    <div class="typing-arena-wrapper">

    <!-- Unified Modern Product Ribbon -->
    <div class="product-ribbon">
      <!-- Mode Selection -->
      <div class="ribbon-group">
        <span class="ribbon-label"><i class="bi bi-grid-fill me-1 text-warning"></i>Mode:</span>
        <button type="button" class="ribbon-pill <?= ($initialMode !== 'free') ? 'active' : '' ?>" id="btn-mode-passage">
          <i class="bi bi-book-half"></i> Passage
        </button>
        <button type="button" class="ribbon-pill <?= ($initialMode === 'free') ? 'active' : '' ?>" id="btn-mode-free">
          <i class="bi bi-pencil-square"></i> Free Typing
        </button>
        <button type="button" class="ribbon-pill" id="btn-mode-custom" data-bs-toggle="modal" data-bs-target="#customTextModal">
          <i class="bi bi-code-slash"></i> Custom
        </button>
      </div>

      <div class="ribbon-divider d-none d-md-block"></div>

      <!-- Time Selectors (30s, 1m, 3m, 6m, 10m, 30m, Custom) -->
      <div class="ribbon-group">
        <span class="ribbon-label"><i class="bi bi-stopwatch-fill me-1" style="color: var(--first-color);"></i>Clock:</span>
        <?php
          $clockDurations = [
            30   => '30s',
            60   => '1m',
            180  => '3m',
            360  => '6m',
            600  => '10m',
            1800 => '30m'
          ];
          foreach ($clockDurations as $durSec => $durLabel):
        ?>
          <button type="button" class="ribbon-pill duration-selector-btn <?= ($selectedDuration === $durSec) ? 'active' : '' ?>" data-duration="<?= $durSec ?>">
            <?= $durLabel ?>
          </button>
        <?php endforeach; ?>
        <button type="button" class="ribbon-pill" id="btn-custom-duration-open" data-bs-toggle="modal" data-bs-target="#customDurationModal" title="Set Custom Time">
          <i class="bi bi-clock-history"></i> Custom
        </button>
      </div>

      <div class="ribbon-divider d-none d-lg-block"></div>

      <!-- Difficulty Selectors -->
      <div class="ribbon-group">
        <span class="ribbon-label"><i class="bi bi-speedometer2 me-1 text-warning"></i>Diff:</span>
        <a href="?difficulty=beginner&duration=<?= $selectedDuration ?>" class="ribbon-pill <?= ($selectedDifficulty === 'beginner') ? 'active' : '' ?>">Beginner</a>
        <a href="?difficulty=intermediate&duration=<?= $selectedDuration ?>" class="ribbon-pill <?= ($selectedDifficulty === 'intermediate') ? 'active' : '' ?>">Intermediate</a>
        <a href="?difficulty=advanced&duration=<?= $selectedDuration ?>" class="ribbon-pill <?= ($selectedDifficulty === 'advanced') ? 'active' : '' ?>">Advanced</a>
      </div>

      <div class="ribbon-divider d-none d-xl-block"></div>

      <!-- Quick Sound & Preferences Tools -->
      <div class="ribbon-group ms-auto">
        <button type="button" class="ribbon-pill" id="btn-sound-toggle" title="Toggle Typing Sounds">
          <i class="bi <?= (!empty($settings['sound_enabled']) && $settings['sound_theme'] !== 'silent') ? 'bi-volume-up-fill text-success' : 'bi-volume-mute-fill text-secondary' ?>" id="sound-icon"></i>
        </button>
        <a href="<?= BASE_URL ?>/settings.php" class="ribbon-pill" title="Typing Preferences">
          <i class="bi bi-gear-fill"></i>
        </a>
      </div>
    </div>

    <!-- =====================================================================
         VIEW 1: PASSAGE DRILL (Character-by-character highlight against passage)
         ===================================================================== -->
    <div id="passage-arena-view" class="<?= ($initialMode === 'free') ? 'd-none' : '' ?>">
      <!-- Last Test Captured & Quick Replay Banner -->
      <div id="last-test-captured-banner" class="last-capture-banner mb-3 d-none">
        <div class="d-flex align-items-center gap-2 flex-wrap">
          <i class="bi bi-bookmark-check-fill text-warning fs-5"></i>
          <span class="text-light fw-bold">Last Session Captured:</span>
          <span class="last-capture-badge" id="last-captured-net-wpm">0 WPM</span>
          <span class="badge bg-dark border border-secondary text-success" id="last-captured-acc">100% Acc</span>
          <span class="badge bg-dark border border-secondary text-info font-monospace" id="last-captured-cpm">0 CPM</span>
          <span class="text-muted small" id="last-captured-time"></span>
        </div>
        <div class="d-flex align-items-center gap-2">
          <button type="button" class="btn btn-sm btn-primary" id="btn-replay-last-captured">
            <i class="bi bi-arrow-repeat me-1"></i> Replay Passage
          </button>
          <button type="button" class="btn-close btn-close-white btn-sm" id="btn-dismiss-last-captured" aria-label="Dismiss"></button>
        </div>
      </div>

      <!-- Focused Zen Focus Bar (Distraction-Free Clean Typing: Telemetry Unlocks in Showing Card on Completion) -->
      <div class="typing-zen-bar mb-3 p-3 glass-card d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
          <span class="fw-semibold text-light" id="passage-title-display"><?= e($activePassage['title']) ?></span>
          <?= get_difficulty_badge($activePassage['difficulty']) ?>
          <span class="text-muted small ms-2"><strong id="passage-word-count-badge"><?= (int)$activePassage['word_count'] ?></strong> words</span>
          <span class="text-muted small">• <strong id="passage-char-count-badge"><?= (int)$activePassage['char_count'] ?></strong> chars</span>
        </div>

        <div class="d-flex align-items-center gap-3">
          <div class="zen-timer-badge">
            <i class="bi bi-stopwatch-fill text-warning fs-5"></i>
            <span class="zen-timer-val font-monospace" id="live-timer"><?= $selectedDuration ?>s</span>
          </div>
          <span class="badge border border-danger text-danger bg-danger-subtle px-2 py-1 small fw-bold">
            <i class="bi bi-slash-circle-fill me-1"></i>Strict Exam Rules Active
          </span>
        </div>

        <!-- In-memory DOM elements to keep engine calculating precision telemetry smoothly -->
        <div class="d-none" aria-hidden="true">
          <span id="live-words-count">0</span>
          <span id="live-accuracy">100%</span>
          <span id="live-wpm">0</span>
          <span id="live-cpm">0</span>
          <span id="live-chars-count">0</span>
          <span id="live-lines-count">1</span>
          <span id="live-net-wpm">0</span>
          <span id="live-errors-count">0</span>
        </div>
      </div>

      <!-- Real-Time Progress Bar -->
      <div class="typing-progress-container">
        <div class="typing-progress-bar" id="typing-progress-bar"></div>
      </div>

      <!-- Main Interactive Typing Arena -->
      <div class="position-relative">
        <div id="typing-box" class="typing-box">
          <!-- Rendered dynamically by TypingEngine -->
        </div>

        <!-- Hidden Key Capture Input -->
        <input type="text" id="hidden-typing-input" class="hidden-typing-input" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">

        <!-- Click to focus overlay prompt -->
        <div id="typing-focus-prompt" class="typing-focus-prompt">
          <div class="stat-icon" style="background: rgba(255, 106, 0, 0.2); color: #ff6a00;">
            <i class="bi bi-cursor-fill fs-3"></i>
          </div>
          <div class="fw-bold text-light fs-5">Click anywhere here or start typing</div>
          <div class="text-secondary small">Your live timer and word counter start on your first keypress</div>
        </div>

        <!-- Typing Paused Overlay -->
        <div id="typing-pause-overlay" class="typing-paused-overlay hidden">
          <div class="pause-icon-badge"><i class="bi bi-pause-fill"></i></div>
          <h4 class="fw-bold text-light mb-1">Practice Paused</h4>
          <p class="text-secondary small mb-3">Timer and metrics are frozen. Click Resume or press Space to continue.</p>
          <button type="button" class="btn btn-primary px-4 py-2 fw-bold" id="btn-resume-overlay">
            <i class="bi bi-play-fill me-1 fs-5"></i> Resume Test (<kbd class="text-white bg-dark">Space</kbd> / <kbd class="text-white bg-dark">Esc</kbd>)
          </button>
        </div>
      </div>

      <!-- Bottom Controls Toolbar -->
      <div class="typing-controls-toolbar">
        <div class="d-flex align-items-center gap-2 flex-wrap">
          <button type="button" class="btn btn-control" id="btn-pause-test" title="Pause / Resume Test (Esc)">
            <i class="bi bi-pause-fill"></i>
            <span id="pause-btn-text">Pause (<kbd class="kbd-pro">Esc</kbd>)</span>
          </button>
          <button type="button" class="btn btn-control" id="btn-restart-test" title="Restart Current Test (Tab + Enter)">
            <i class="bi bi-arrow-clockwise"></i>
            <span>Restart (<kbd class="kbd-pro">Tab+Enter</kbd>)</span>
          </button>
          <button type="button" class="btn btn-control" id="btn-next-passage" title="Load New Passage">
            <i class="bi bi-shuffle"></i>
            <span>New Passage</span>
          </button>

          <!-- Strict Mode: Backspace/Delete Disabled button requested by user -->
          <button type="button" class="btn btn-control text-danger fw-bold border-danger" id="btn-toggle-backspace" title="Toggle Backspace/Delete strict mode">
            <i class="bi bi-slash-circle-fill text-danger" id="backspace-mode-icon"></i>
            <span id="backspace-mode-text">Backspace: <strong class="text-danger">Disabled</strong></span>
          </button>

          <!-- Dedicated Backspace / Delete button (Disabled by default as requested) -->
          <button type="button" class="btn btn-control text-secondary" id="btn-touch-backspace" disabled title="Backspace & Delete are disabled in Strict Mode">
            <i class="bi bi-backspace-reverse-fill"></i>
            <span id="btn-touch-backspace-text">Backspace (Disabled)</span>
          </button>
        </div>

        <div class="d-flex align-items-center gap-2 text-muted small">
          <span class="badge bg-danger-subtle text-danger border border-danger"><i class="bi bi-slash-circle me-1"></i>Strict Exam Rules</span>
          <span class="d-none d-md-inline ms-1"><kbd class="kbd-pro">Esc</kbd> = Pause</span>
          <span class="d-none d-md-inline ms-1"><kbd class="kbd-pro">Tab+Enter</kbd> = Restart</span>
        </div>
      </div>
    </div>

    <!-- =====================================================================
         VIEW 2: FREE TYPING / WRITE ANYTHING (Zero pre-text - Blank Canvas)
         User writes anything freely with real-time advanced word count & WPM
         ===================================================================== -->
    <div id="free-arena-view" class="<?= ($initialMode !== 'free') ? 'd-none' : '' ?>">
      <!-- Focused Zen Focus Bar for Free Typing (Distraction-Free Clean Typing: Telemetry Unlocks in Showing Card on Completion) -->
      <div class="typing-zen-bar mb-3 p-3 glass-card d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
          <span class="badge-pro-orange">
            <i class="bi bi-pencil-fill me-1"></i>Free Typing
          </span>
          <div class="d-flex align-items-center gap-1 ms-2">
            <span class="text-secondary small fw-bold"><i class="bi bi-stopwatch me-1"></i>Mode:</span>
            <select id="free-timer-mode-select" class="form-select form-select-sm bg-dark text-light border-secondary" style="width: 150px; padding: 0.2rem 0.5rem; font-size: 0.8rem;">
              <option value="stopwatch">⏱️ Stopwatch</option>
              <option value="30">30 Seconds</option>
              <option value="60">1 Minute</option>
              <option value="180">3 Minutes</option>
              <option value="360">6 Minutes</option>
              <option value="600">10 Minutes</option>
              <option value="1800">30 Minutes</option>
              <option value="custom">⚙️ Custom Time...</option>
            </select>
          </div>
        </div>

        <div class="d-flex align-items-center gap-3">
          <div class="zen-timer-badge">
            <i class="bi bi-stopwatch-fill text-warning fs-5"></i>
            <span class="zen-timer-val font-monospace" id="live-free-timer">0:00</span>
          </div>
          <span class="badge border border-danger text-danger bg-danger-subtle px-2 py-1 small fw-bold">
            <i class="bi bi-slash-circle-fill me-1"></i>Strict Exam Rules Active
          </span>
        </div>

        <!-- In-memory DOM elements to keep FreeWritingEngine calculating precision telemetry smoothly without visual distraction -->
        <div class="d-none" aria-hidden="true">
          <span id="live-free-words">0</span>
          <span id="live-free-accuracy">100%</span>
          <span id="live-free-wpm">0</span>
          <span id="live-free-cpm">0</span>
          <span id="live-free-chars">0</span>
          <span id="live-free-chars-nospace">0</span>
          <span id="live-free-lines">0</span>
        </div>
      </div>

      <!-- Blank Canvas Textarea -->
      <div class="free-typing-wrapper">
        <textarea id="free-typing-textarea" class="free-typing-textarea" placeholder="Start typing anything here from scratch... (e.g. CSS code, ideas, speed typing drills). 

Tab key is enabled for code indentation! Your real-time words, characters, accuracy, and WPM calculate silently in memory and unlock in the Showing Card when completed." autofocus></textarea>
        
        <div class="free-stats-footer">
          <div class="d-flex align-items-center gap-3">
            <span class="badge bg-dark border border-secondary text-secondary small">
              <i class="bi bi-shield-lock me-1"></i>Distraction-free: Telemetry numbers unlock upon clicking Finish
            </span>
            <span class="free-stat-item"><kbd class="bg-dark text-info border border-secondary">Tab</kbd> = 4 Spaces</span>
          </div>
          <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-insert-css-sample" title="Insert CSS :root Sample Code">
              <i class="bi bi-code me-1 text-warning"></i> Insert CSS Sample
            </button>
          </div>
        </div>
      </div>

      <!-- Free Typing Action Toolbar -->
      <div class="typing-controls-toolbar">
        <div class="d-flex align-items-center gap-2 flex-wrap">
          <button type="button" class="btn btn-primary fw-bold" id="btn-free-finish">
            <i class="bi bi-check-circle-fill me-1"></i> Finish & Save Record
          </button>
          <!-- Free Typing Backspace Disabled Toggle -->
          <button type="button" class="btn btn-control text-danger fw-bold border-danger" id="btn-free-toggle-backspace" title="Toggle Backspace/Delete strict mode">
            <i class="bi bi-slash-circle-fill text-danger" id="free-backspace-mode-icon"></i>
            <span id="free-backspace-mode-text">Backspace: <strong class="text-danger">Disabled</strong></span>
          </button>
          <button type="button" class="btn btn-control text-secondary" id="btn-free-backspace-touch" disabled title="Backspace is disabled in Strict Mode">
            <i class="bi bi-backspace-reverse-fill"></i>
            <span id="free-backspace-btn-text">Backspace (Disabled)</span>
          </button>
          <button type="button" class="btn btn-control" id="btn-free-clear">
            <i class="bi bi-trash3 me-1"></i> Clear Canvas
          </button>
          <button type="button" class="btn btn-control" id="btn-free-copy">
            <i class="bi bi-clipboard me-1"></i> Copy Text
          </button>
          <button type="button" class="btn btn-control" id="btn-free-download">
            <i class="bi bi-download me-1"></i> Download File
          </button>
        </div>

        <div class="d-flex align-items-center gap-2">
          <button type="button" class="btn btn-outline-info btn-sm" id="btn-free-to-passage">
            <i class="bi bi-arrow-repeat me-1"></i> Use as Passage Drill
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- =====================================================================
     Custom Text Input Modal
     ===================================================================== -->
<div class="modal fade" id="customTextModal" tabindex="-1" aria-labelledby="customTextModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content glass-card border-secondary">
      <div class="modal-header border-secondary">
        <h5 class="modal-title text-light fw-bold" id="customTextModalLabel">
          <i class="bi bi-pencil-square text-info me-2"></i>Practice with Custom Text / Code
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="text-secondary small">
          Paste your custom text, programming code, or CSS variables. Our engine will calculate real-time word count and character-by-character accuracy automatically.
        </p>

        <!-- Quick Sample Snippet Buttons -->
        <div class="mb-3 d-flex align-items-center gap-2">
          <span class="text-secondary small fw-semibold">Quick Snippets:</span>
          <button type="button" class="btn btn-xs btn-outline-info py-1 px-2 small" id="btn-snippet-css">
            <i class="bi bi-filetype-css me-1"></i> CSS :root Variables
          </button>
          <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-2 small" id="btn-snippet-js">
            <i class="bi bi-filetype-js me-1"></i> JavaScript Function
          </button>
        </div>

        <div class="mb-3">
          <label class="form-label text-secondary small fw-bold">Passage Title (Optional)</label>
          <input type="text" id="custom-text-title" class="form-control bg-dark text-light border-secondary" placeholder="e.g. CSS Variables Practice Drill">
        </div>
        <div class="mb-3">
          <label class="form-label text-secondary small fw-bold">Text / Code Content</label>
          <textarea id="custom-text-content" rows="6" class="form-control bg-dark text-light border-secondary font-monospace" placeholder="Paste your practice text or code here..."></textarea>
          <div class="form-text text-muted d-flex justify-content-between">
            <span id="custom-word-counter">0 words</span>
            <span>Accurate real-time token count</span>
          </div>
        </div>
      </div>
      <div class="modal-footer border-secondary">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary fw-bold" id="btn-apply-custom-text">
          <i class="bi bi-play-circle-fill me-1"></i> Start Practice Drill
        </button>
      </div>
    </div>
  </div>
</div>

<!-- =====================================================================
     Test Results Modal
     ===================================================================== -->
<!-- =====================================================================
     Official 7-Metric Showing Card Results Modal (Unlocked Upon Completion)
     ===================================================================== -->
<div class="modal fade" id="resultsModal" tabindex="-1" aria-labelledby="resultsModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
    <div class="modal-content results-modal-card p-0 overflow-hidden">
      <!-- Modal Header Banner -->
      <div class="modal-header border-0 p-4" style="background: linear-gradient(135deg, rgba(255, 106, 0, 0.16) 0%, rgba(18, 24, 38, 0.98) 100%); border-bottom: 1px solid rgba(255, 106, 0, 0.3) !important;">
        <div class="d-flex align-items-center gap-3">
          <div class="results-trophy-badge">
            <i class="bi bi-trophy-fill"></i>
          </div>
          <div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
              <h4 class="modal-title fw-bold text-light mb-0" id="resultsModalLabel">
                Typing Performance Showing Card
              </h4>
              <span class="badge-pro-orange"><i class="bi bi-shield-check me-1"></i>Official Telemetry</span>
            </div>
            <p class="text-secondary small mb-0 mt-1">
              Typing session complete. All 7 core metrics and keystroke trajectory diagnostics unlocked below.
            </p>
          </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body p-4">
        <!-- Dynamic Performance Greeting & Accolades Banner -->
        <div id="res-greeting-alert" class="alert alert-soft-ui mb-4 d-flex align-items-center gap-3 p-3" role="alert" style="background: rgba(255, 85, 50, 0.08); border: 1px solid rgba(255, 85, 50, 0.3); border-radius: 14px;">
          <div class="fs-1" id="res-greeting-icon">🎉</div>
          <div class="flex-grow-1">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
              <h5 class="fw-bold mb-1" id="res-greeting-title" style="color: var(--first-color, #ff5532);">
                Outstanding Effort! Drill Completed
              </h5>
              <button type="button" class="btn btn-sm btn-outline-warning fw-bold d-inline-flex align-items-center gap-1" id="btn-quick-cert-view">
                <i class="bi bi-award-fill"></i> View Certificate
              </button>
            </div>
            <p class="mb-0 small text-secondary" id="res-greeting-subtitle">
              <?= $currentUser ? 'Excellent job, ' . e($currentUser['username']) . '!' : 'Congratulations!' ?> Your telemetry was officially recorded with full character metrics below.
            </p>
          </div>
        </div>

        <!-- Top Row: Hero Speed Score + Live Speed Trajectory Graph -->
        <div class="row g-4 mb-4 align-items-stretch">
          <!-- Left: Hero Net WPM Showcase Card -->
          <div class="col-lg-5">
            <div class="result-hero-metric h-100 d-flex flex-column justify-content-center p-4">
              <span class="text-uppercase fw-bold small text-warning letter-spacing-1 mb-1">Final Performance</span>
              <div class="result-hero-val font-monospace" id="res-net-wpm">0</div>
              <div class="result-hero-lbl">Net Words Per Minute (WPM)</div>
              <div class="mt-3" id="res-rating-badge">
                <span class="badge bg-primary fs-6 px-3 py-1">Professional</span>
              </div>
              <div class="mt-3 text-muted small" id="res-strict-mode-note">
                <i class="bi bi-slash-circle-fill text-danger me-1"></i> Strict Exam Rules: Backspace/Delete Disabled
              </div>
            </div>
          </div>

          <!-- Right: Speed Trajectory Live Curve -->
          <div class="col-lg-7">
            <div class="glass-card p-3 h-100 d-flex flex-column justify-content-between">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="fw-bold text-light small">
                  <i class="bi bi-graph-up text-warning me-1"></i> Live Speed Trajectory (WPM / Second)
                </span>
                <span class="badge bg-dark border border-secondary text-secondary small">Real-Time Progression</span>
              </div>
              <div style="height: 180px; position: relative;">
                <canvas id="sessionSpeedChart"></canvas>
              </div>
            </div>
          </div>
        </div>

        <!-- The 7 Core Metrics Showing Cards Grid (Symmetrical 4x2 Luxury Layout) -->
        <div class="mb-2">
          <span class="text-uppercase fw-bold small text-secondary letter-spacing-1">Core 7-Metric Telemetry Breakdown</span>
        </div>
        <div class="row g-3 mb-4">
          <!-- 1. Elapsed / Timer -->
          <div class="col-6 col-md-3">
            <div class="stat-showing-card p-3 text-center h-100 border-start border-3 border-warning">
              <div class="showing-card-icon mx-auto bg-warning-subtle text-warning">
                <i class="bi bi-stopwatch-fill"></i>
              </div>
              <div class="showing-card-lbl">1. Elapsed / Timer</div>
              <div class="showing-card-num text-warning" id="res-elapsed-timer">0:00</div>
              <div class="showing-card-sub" id="res-time-elapsed">0s duration</div>
            </div>
          </div>

          <!-- 2. Real-Time Words -->
          <div class="col-6 col-md-3">
            <div class="stat-showing-card p-3 text-center h-100 border-start border-3 border-success">
              <div class="showing-card-icon mx-auto bg-success-subtle text-success">
                <i class="bi bi-fonts"></i>
              </div>
              <div class="showing-card-lbl">2. Real-Time Words</div>
              <div class="showing-card-num text-success" id="res-words-typed">0</div>
              <div class="showing-card-sub">Completed words</div>
            </div>
          </div>

          <!-- 3. Live Accuracy -->
          <div class="col-6 col-md-3">
            <div class="stat-showing-card p-3 text-center h-100 border-start border-3 border-info">
              <div class="showing-card-icon mx-auto bg-info-subtle text-info">
                <i class="bi bi-bullseye"></i>
              </div>
              <div class="showing-card-lbl">3. Live Accuracy</div>
              <div class="showing-card-num text-info" id="res-accuracy">100%</div>
              <div class="showing-card-sub">Keystroke precision</div>
            </div>
          </div>

          <!-- 4. Gross WPM -->
          <div class="col-6 col-md-3">
            <div class="stat-showing-card p-3 text-center h-100 border-start border-3 border-primary">
              <div class="showing-card-icon mx-auto bg-primary-subtle text-primary">
                <i class="bi bi-speedometer2"></i>
              </div>
              <div class="showing-card-lbl">4. Gross WPM</div>
              <div class="showing-card-num text-light" id="res-gross-wpm">0</div>
              <div class="showing-card-sub">Raw typing speed</div>
            </div>
          </div>

          <!-- 5. CPM (Speed) -->
          <div class="col-6 col-md-3">
            <div class="stat-showing-card p-3 text-center h-100 border-start border-3 border-warning">
              <div class="showing-card-icon mx-auto bg-warning-subtle text-warning">
                <i class="bi bi-lightning-charge-fill"></i>
              </div>
              <div class="showing-card-lbl">5. CPM (Speed)</div>
              <div class="showing-card-num text-warning" id="res-cpm">0</div>
              <div class="showing-card-sub">Chars per minute</div>
            </div>
          </div>

          <!-- 6. Characters -->
          <div class="col-6 col-md-3">
            <div class="stat-showing-card p-3 text-center h-100 border-start border-3 border-secondary">
              <div class="showing-card-icon mx-auto bg-dark border border-secondary text-light">
                <i class="bi bi-keyboard-fill"></i>
              </div>
              <div class="showing-card-lbl">6. Characters</div>
              <div class="showing-card-num text-light" id="res-chars-count">0</div>
              <div class="showing-card-sub">Total characters</div>
            </div>
          </div>

          <!-- 7. Lines -->
          <div class="col-6 col-md-3">
            <div class="stat-showing-card p-3 text-center h-100 border-start border-3 border-warning">
              <div class="showing-card-icon mx-auto bg-warning-subtle text-warning">
                <i class="bi bi-text-paragraph"></i>
              </div>
              <div class="showing-card-lbl">7. Lines</div>
              <div class="showing-card-num text-warning" id="res-lines-count">1</div>
              <div class="showing-card-sub">Paragraph structure</div>
            </div>
          </div>

          <!-- 8. Keystroke Quality -->
          <div class="col-6 col-md-3">
            <div class="stat-showing-card p-3 text-center h-100 border-start border-3 border-success">
              <div class="showing-card-icon mx-auto bg-success-subtle text-success">
                <i class="bi bi-shield-check"></i>
              </div>
              <div class="showing-card-lbl">Keystroke Quality</div>
              <div class="showing-card-num text-success" id="res-quality-score">100%</div>
              <div class="showing-card-sub">Error-free execution</div>
            </div>
          </div>
        </div>

        <!-- Keystroke Detailed Diagnostic Bar -->
        <div class="showing-diagnostics-bar p-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-cpu-fill text-warning fs-5"></i>
            <span class="small fw-bold text-light">Keystroke Diagnostics:</span>
          </div>
          <div class="d-flex align-items-center gap-4 text-muted small font-monospace flex-wrap">
            <span>Total Keys: <strong class="text-light fs-6" id="res-total-keys">0</strong></span>
            <span>Correct: <strong class="text-success fs-6" id="res-correct-keys">0</strong></span>
            <span>Errors: <strong class="text-danger fs-6" id="res-error-keys">0</strong></span>
            <span>Backspaces: <strong class="text-warning fs-6" id="res-backspaces">0</strong></span>
            <span class="badge border border-danger text-danger bg-danger-subtle"><i class="bi bi-slash-circle-fill me-1"></i>Exam Rules Active</span>
          </div>
        </div>
      </div>

      <div class="modal-footer border-secondary justify-content-between p-3 px-4">
        <div>
          <?php if (!$currentUser): ?>
            <span class="text-secondary small">
              <i class="bi bi-info-circle me-1"></i>
              <a href="<?= BASE_URL ?>/register.php" class="text-warning fw-bold">Register</a> to permanently save results & track progress!
            </span>
          <?php else: ?>
            <span class="text-success small fw-semibold">
              <i class="bi bi-check-circle-fill me-1"></i>Session automatically recorded to your personal history.
            </span>
          <?php endif; ?>
        </div>
        <div class="d-flex gap-2 flex-wrap">
          <button type="button" class="btn btn-outline-warning fw-bold" id="btn-generate-cert">
            <i class="bi bi-award-fill me-1"></i> Generate Certificate
          </button>
          <button type="button" class="btn btn-outline-secondary" id="btn-res-retry">
            <i class="bi bi-arrow-clockwise me-1"></i> Practice Again
          </button>
          <button type="button" class="btn btn-primary fw-bold px-4" id="btn-res-next">
            <i class="bi bi-arrow-right-circle me-1"></i> Continue
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- =====================================================================
     Custom Time / Clock Modal
     ===================================================================== -->
<div class="modal fade" id="customDurationModal" tabindex="-1" aria-labelledby="customDurationModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content glass-card border-secondary">
      <div class="modal-header border-secondary">
        <h5 class="modal-title fw-bold text-light" id="customDurationModalLabel">
          <i class="bi bi-clock-history text-warning me-2"></i>Set Custom Practice Time
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="text-secondary small mb-3">
          Configure a custom countdown clock or free watch timer. Pick a quick preset or type any custom duration in seconds or minutes.
        </p>

        <!-- Quick Presets -->
        <div class="mb-3">
          <label class="form-label text-secondary small fw-bold">Quick Clock Presets</label>
          <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm quick-duration-btn" data-sec="30">30s</button>
            <button type="button" class="btn btn-outline-secondary btn-sm quick-duration-btn" data-sec="60">1 Min</button>
            <button type="button" class="btn btn-outline-secondary btn-sm quick-duration-btn" data-sec="180">3 Min</button>
            <button type="button" class="btn btn-outline-secondary btn-sm quick-duration-btn" data-sec="360">6 Min</button>
            <button type="button" class="btn btn-outline-secondary btn-sm quick-duration-btn" data-sec="600">10 Min</button>
            <button type="button" class="btn btn-outline-secondary btn-sm quick-duration-btn" data-sec="1800">30 Min</button>
          </div>
        </div>

        <div class="row g-2 mb-3">
          <div class="col-8">
            <label class="form-label text-secondary small fw-bold">Custom Time Value</label>
            <input type="number" id="custom-time-val" class="form-control bg-dark text-light border-secondary" min="5" max="7200" value="90" placeholder="e.g. 90">
          </div>
          <div class="col-4">
            <label class="form-label text-secondary small fw-bold">Unit</label>
            <select id="custom-time-unit" class="form-select bg-dark text-light border-secondary">
              <option value="sec" selected>Seconds</option>
              <option value="min">Minutes</option>
            </select>
          </div>
        </div>

        <div class="mb-2">
          <label class="form-label text-secondary small fw-bold">Apply To Mode</label>
          <div class="d-flex gap-3">
            <div class="form-check">
              <input class="form-check-input" type="radio" name="customTimeTarget" id="targetPassage" value="passage" checked>
              <label class="form-check-label small text-light" for="targetPassage">
                Passage Drill
              </label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="customTimeTarget" id="targetFree" value="free">
              <label class="form-check-label small text-light" for="targetFree">
                Free Typing Watch
              </label>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer border-secondary">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary fw-bold px-4" id="btn-apply-custom-duration">
          <i class="bi bi-check-circle-fill me-1"></i> Apply & Start Drill
        </button>
      </div>
    </div>
  </div>
</div>

<!-- =====================================================================
     Official Typing Proficiency Certificate Modal
     ===================================================================== -->
<div class="modal fade" id="certificateModal" tabindex="-1" aria-labelledby="certificateModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content glass-card p-0 border-secondary overflow-hidden">
      <div class="modal-header border-secondary p-3 px-4">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-award-fill text-warning fs-4"></i>
          <h5 class="modal-title fw-bold text-light mb-0" id="certificateModalLabel">Official Typing Proficiency Certificate</h5>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body p-4 bg-dark">
        <!-- Printable Certificate Box -->
        <div class="certificate-preview-box" id="printable-certificate">
          <div class="certificate-border-frame text-center">
            <!-- Frame Corners -->
            <div class="cert-corner cert-corner-tl"></div>
            <div class="cert-corner cert-corner-tr"></div>
            <div class="cert-corner cert-corner-bl"></div>
            <div class="cert-corner cert-corner-br"></div>

            <div class="cert-emblem mb-2">
              <i class="bi bi-award"></i>
            </div>
            <div class="small fw-bold text-uppercase letter-spacing-2" style="color: var(--first-color, #ff5532);">
              Type Master Accreditation Board
            </div>
            <h2 class="cert-title mb-1">Certificate of Typing Proficiency</h2>
            <div class="text-secondary small mb-3">Standardized Examination & Keying Velocity Verification</div>

            <p class="text-muted small mb-1">This official credential certifies that</p>
            <div class="cert-candidate-name" id="certCandidateName">
              <?= e($currentUser['username'] ?? 'Candidate Typist') ?>
            </div>
            <p class="text-secondary small mt-1 mb-4" style="max-width: 620px; margin: 0 auto;">
              has demonstrated typing velocity, accuracy, and keystroke integrity on standardized evaluation passages with the following verified metrics:
            </p>

            <!-- 4 Core Achievement Badges -->
            <div class="row g-3 justify-content-center mb-4">
              <div class="col-6 col-md-3">
                <div class="cert-metric-badge">
                  <div class="cert-metric-val" id="certNetWpm">--</div>
                  <div class="cert-metric-lbl">Net Speed (WPM)</div>
                </div>
              </div>
              <div class="col-6 col-md-3">
                <div class="cert-metric-badge">
                  <div class="cert-metric-val" id="certGrossWpm">--</div>
                  <div class="cert-metric-lbl">Gross Speed (WPM)</div>
                </div>
              </div>
              <div class="col-6 col-md-3">
                <div class="cert-metric-badge">
                  <div class="cert-metric-val" id="certAccuracy">--%</div>
                  <div class="cert-metric-lbl">Accuracy</div>
                </div>
              </div>
              <div class="col-6 col-md-3">
                <div class="cert-metric-badge">
                  <div class="cert-metric-val" id="certDuration">--</div>
                  <div class="cert-metric-lbl">Timed Duration</div>
                </div>
              </div>
            </div>

            <!-- Verification Footer Details -->
            <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mt-4 pt-3 border-top border-secondary text-start">
              <div>
                <div class="cert-seal mb-1">
                  <i class="bi bi-shield-fill-check"></i>
                  <span>VERIFIED RECORD</span>
                </div>
                <div class="text-muted" style="font-size: 0.72rem;">
                  Credential ID: <strong class="text-secondary font-monospace" id="certHash">TM-<?= strtoupper(substr(bin2hex(random_bytes(5)), 0, 10)) ?></strong>
                </div>
                <div class="text-muted" style="font-size: 0.72rem;">
                  Issue Date: <strong class="text-secondary" id="certDate"><?= date('F j, Y') ?></strong>
                </div>
              </div>

              <div class="text-end">
                <div class="fw-bold font-monospace text-light" style="border-bottom: 1px solid var(--first-color, #ff5532); padding-bottom: 2px;">
                  Automated Evaluation Engine
                </div>
                <div class="text-muted small" style="font-size: 0.72rem;">Type Master Verification Authority</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer border-secondary justify-content-between p-3 px-4">
        <span class="text-muted small">
          <i class="bi bi-info-circle me-1"></i>You can print this certificate or save it directly as a PDF.
        </span>
        <div class="d-flex gap-2">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary fw-bold" onclick="window.print()">
            <i class="bi bi-printer-fill me-1"></i> Print / Save as PDF
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Typing Engine Scripts -->
<script src="<?= BASE_URL ?>/assets/js/typing-engine.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
  let initialPassage = <?= json_encode($activePassage) ?>;
  let currentDuration = <?= (int)$selectedDuration ?>;
  let currentDifficulty = '<?= e($selectedDifficulty) ?>';
  let isSoundEnabled = <?= (!empty($settings['sound_enabled']) && $settings['sound_theme'] !== 'silent') ? 'true' : 'false' ?>;
  let soundTheme = '<?= e($settings['sound_theme'] ?? 'mechanical') ?>';
  let caretStyle = '<?= e($settings['caret_style'] ?? 'line') ?>';
  let csrfToken = '<?= e(get_csrf_token()) ?>';

  const resultsModal = new bootstrap.Modal(document.getElementById('resultsModal'));
  let sessionChartInstance = null;

  // CSS Sample snippet requested by user
  const cssSampleSnippet = `:root {
    --header-height: 3.5rem;
    --first-color: #ff5532;
    --second-color: #575757;
    --text-color: #111;
    --body-color: #fff;
    --shadow: rgba(0, 0, 0, 0.1) 0px 10px 50px;
    --normal-font-size: .938rem;
    --small-font-size: .813rem;
    --smaller-font-size: .75rem;
    --z-tooltip: 10;
    --z-fixed: 100;
}`;

  // Strict Exam Mode: Backspace & Delete disabled by default as requested
  let isBackspaceDisabled = true;

  // 1. Initialize Passage Engine with All 7 Core Metric Displays
  const engine = new TypingEngine({
    containerId: 'typing-box',
    hiddenInputId: 'hidden-typing-input',
    timerDisplayId: 'live-timer',
    wordsDisplayId: 'live-words-count',
    accuracyDisplayId: 'live-accuracy',
    wpmDisplayId: 'live-wpm',
    cpmDisplayId: 'live-cpm',
    charsDisplayId: 'live-chars-count',
    linesDisplayId: 'live-lines-count',
    netWpmDisplayId: 'live-net-wpm',
    errorsDisplayId: 'live-errors-count',
    progressBarId: 'typing-progress-bar',
    caretStyle: caretStyle,
    soundEnabled: isSoundEnabled,
    soundTheme: soundTheme,
    duration: currentDuration,
    mode: 'timed',
    passageId: initialPassage.id,
    difficulty: currentDifficulty,
    csrfToken: csrfToken,
    backspaceDisabled: isBackspaceDisabled,
    onFinish: (result) => {
      displayResultsModal(result);
    }
  });

  engine.loadText(initialPassage.content);

  // 2. Initialize Free Writing Engine (Blank Canvas) with All 7 Displays
  const freeEngine = new FreeWritingEngine({
    textareaId: 'free-typing-textarea',
    timerDisplayId: 'live-free-timer',
    wordsDisplayId: 'live-free-words',
    accuracyDisplayId: 'live-free-accuracy',
    wpmDisplayId: 'live-free-wpm',
    cpmDisplayId: 'live-free-cpm',
    charsDisplayId: 'live-free-chars',
    charsNoSpaceDisplayId: 'live-free-chars-nospace',
    linesDisplayId: 'live-free-lines',
    soundEnabled: isSoundEnabled,
    soundTheme: soundTheme,
    duration: 60,
    mode: 'stopwatch',
    csrfToken: csrfToken,
    backspaceDisabled: isBackspaceDisabled,
    onFinish: (result) => {
      displayResultsModal(result);
    }
  });

  // Lazy Skeleton Reveal: Smooth transition to interactive typing arena
  setTimeout(() => {
    const skel = document.getElementById('practice-skeleton-view');
    const content = document.getElementById('practice-content-view');
    if (skel) skel.style.display = 'none';
    if (content) {
      content.style.display = 'block';
      if (engine && engine.input) engine.input.focus();
    }
  }, 220);

  // Mode Switcher Controls
  const btnModePassage = document.getElementById('btn-mode-passage');
  const btnModeFree = document.getElementById('btn-mode-free');
  const passageView = document.getElementById('passage-arena-view');
  const freeView = document.getElementById('free-arena-view');

  btnModePassage.addEventListener('click', () => {
    btnModePassage.classList.add('active');
    btnModeFree.classList.remove('active');
    passageView.classList.remove('d-none');
    freeView.classList.add('d-none');
    engine.input.focus();
  });

  btnModeFree.addEventListener('click', () => {
    btnModeFree.classList.add('active');
    btnModePassage.classList.remove('active');
    freeView.classList.remove('d-none');
    passageView.classList.add('d-none');
    document.getElementById('free-typing-textarea').focus();
  });

  // Free Typing Timer Mode Dropdown
  document.getElementById('free-timer-mode-select').addEventListener('change', (e) => {
    const val = e.target.value;
    if (val === 'custom') {
      const targetFree = document.getElementById('targetFree');
      if (targetFree) targetFree.checked = true;
      const customModal = new bootstrap.Modal(document.getElementById('customDurationModal'));
      customModal.show();
      return;
    }
    if (val === 'stopwatch') {
      freeEngine.options.mode = 'stopwatch';
    } else {
      freeEngine.options.mode = 'timed';
      freeEngine.options.duration = parseInt(val);
    }
    freeEngine.clear();
  });

  // Free Typing Toolbar Actions
  document.getElementById('btn-free-finish').addEventListener('click', () => {
    freeEngine.finish();
  });

  document.getElementById('btn-free-clear').addEventListener('click', () => {
    if (confirm('Clear the canvas and reset the word counter?')) {
      freeEngine.clear();
    }
  });

  document.getElementById('btn-free-copy').addEventListener('click', () => {
    const text = document.getElementById('free-typing-textarea').value;
    navigator.clipboard.writeText(text).then(() => {
      showToast('success', 'Text copied to clipboard!');
    });
  });

  document.getElementById('btn-free-download').addEventListener('click', () => {
    const text = document.getElementById('free-typing-textarea').value;
    const blob = new Blob([text], { type: 'text/plain;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `typemaster-note-${Date.now()}.txt`;
    a.click();
    URL.revokeObjectURL(url);
    showToast('info', 'File downloaded!');
  });

  // Insert CSS Sample Button
  document.getElementById('btn-insert-css-sample').addEventListener('click', () => {
    const textarea = document.getElementById('free-typing-textarea');
    textarea.value = cssSampleSnippet;
    freeEngine.handleInput();
    textarea.focus();
    showToast('info', 'CSS Variables snippet loaded into canvas!');
  });

  // Convert Free Text to Passage Drill
  document.getElementById('btn-free-to-passage').addEventListener('click', () => {
    const text = document.getElementById('free-typing-textarea').value.trim();
    if (text.length < 10) {
      alert('Please write at least 10 characters before converting to a passage drill.');
      return;
    }
    const words = text.split(/\s+/).filter(w => w.length > 0);
    initialPassage = {
      id: null,
      title: 'Converted Free Writing Drill',
      content: text,
      difficulty: 'custom',
      word_count: words.length,
      char_count: text.length
    };
    document.getElementById('passage-title-display').textContent = initialPassage.title;
    document.getElementById('passage-word-count-badge').textContent = words.length;
    engine.options.passageId = null;
    engine.options.difficulty = 'custom';
    engine.loadText(text);

    btnModePassage.click();
    showToast('success', 'Converted to Passage Drill! Start typing to begin test.');
  });

  // Strict Exam Mode: Backspace/Delete State Controller
  function setBackspaceMode(disabled) {
    isBackspaceDisabled = disabled;
    engine.options.backspaceDisabled = disabled;
    freeEngine.options.backspaceDisabled = disabled;

    const toggleBtn = document.getElementById('btn-toggle-backspace');
    const toggleIcon = document.getElementById('backspace-mode-icon');
    const toggleText = document.getElementById('backspace-mode-text');
    const touchBtn = document.getElementById('btn-touch-backspace');
    const touchBtnText = document.getElementById('btn-touch-backspace-text');

    const freeToggleBtn = document.getElementById('btn-free-toggle-backspace');
    const freeToggleIcon = document.getElementById('free-backspace-mode-icon');
    const freeToggleText = document.getElementById('free-backspace-mode-text');
    const freeTouchBtn = document.getElementById('btn-free-backspace-touch');
    const freeTouchBtnText = document.getElementById('free-backspace-btn-text');

    if (disabled) {
      if (toggleBtn) toggleBtn.className = 'btn btn-control text-danger fw-bold border-danger';
      if (toggleIcon) toggleIcon.className = 'bi bi-slash-circle-fill text-danger';
      if (toggleText) toggleText.innerHTML = 'Backspace: <strong class="text-danger">Disabled</strong>';
      if (touchBtn) {
        touchBtn.disabled = true;
        touchBtn.className = 'btn btn-control text-secondary';
        touchBtn.title = 'Backspace & Delete are disabled in Strict Mode';
      }
      if (touchBtnText) touchBtnText.textContent = 'Backspace (Disabled)';

      if (freeToggleBtn) freeToggleBtn.className = 'btn btn-control text-danger fw-bold border-danger';
      if (freeToggleIcon) freeToggleIcon.className = 'bi bi-slash-circle-fill text-danger';
      if (freeToggleText) freeToggleText.innerHTML = 'Backspace: <strong class="text-danger">Disabled</strong>';
      if (freeTouchBtn) {
        freeTouchBtn.disabled = true;
        freeTouchBtn.className = 'btn btn-control text-secondary';
        freeTouchBtn.title = 'Backspace & Delete are disabled in Strict Mode';
      }
      if (freeTouchBtnText) freeTouchBtnText.textContent = 'Backspace (Disabled)';
    } else {
      if (toggleBtn) toggleBtn.className = 'btn btn-control text-success fw-bold border-success';
      if (toggleIcon) toggleIcon.className = 'bi bi-check-circle-fill text-success';
      if (toggleText) toggleText.innerHTML = 'Backspace: <strong class="text-success">Enabled</strong>';
      if (touchBtn) {
        touchBtn.disabled = false;
        touchBtn.className = 'btn btn-control text-warning';
        touchBtn.title = 'Manual Backspace (Tap to delete last char)';
      }
      if (touchBtnText) touchBtnText.textContent = 'Backspace (Touch)';

      if (freeToggleBtn) freeToggleBtn.className = 'btn btn-control text-success fw-bold border-success';
      if (freeToggleIcon) freeToggleIcon.className = 'bi bi-check-circle-fill text-success';
      if (freeToggleText) freeToggleText.innerHTML = 'Backspace: <strong class="text-success">Enabled</strong>';
      if (freeTouchBtn) {
        freeTouchBtn.disabled = false;
        freeTouchBtn.className = 'btn btn-control text-warning';
        freeTouchBtn.title = 'Manual Backspace (Tap to delete last char)';
      }
      if (freeTouchBtnText) freeTouchBtnText.textContent = 'Backspace (Touch)';
    }
  }

  // Initialize Strict Exam Mode (Disabled by default as requested by user)
  setBackspaceMode(true);

  // Toggle Backspace / Delete Strict Mode buttons
  const toggleBackspaceBtn = document.getElementById('btn-toggle-backspace');
  if (toggleBackspaceBtn) {
    toggleBackspaceBtn.addEventListener('click', () => {
      setBackspaceMode(!isBackspaceDisabled);
      showToast(isBackspaceDisabled ? 'warning' : 'info', isBackspaceDisabled ? 'Strict Exam Mode: Backspace & Delete disabled' : 'Standard Mode: Backspace & Delete enabled');
    });
  }

  const freeToggleBackspaceBtn = document.getElementById('btn-free-toggle-backspace');
  if (freeToggleBackspaceBtn) {
    freeToggleBackspaceBtn.addEventListener('click', () => {
      setBackspaceMode(!isBackspaceDisabled);
      showToast(isBackspaceDisabled ? 'warning' : 'info', isBackspaceDisabled ? 'Strict Exam Mode: Backspace & Delete disabled' : 'Standard Mode: Backspace & Delete enabled');
    });
  }

  // Dedicated Touch Backspace Buttons (Mobile & Desktop)
  const touchBackspaceBtn = document.getElementById('btn-touch-backspace');
  if (touchBackspaceBtn) {
    touchBackspaceBtn.addEventListener('click', (e) => {
      e.preventDefault();
      engine.manualBackspace();
    });
  }

  const freeTouchBackspaceBtn = document.getElementById('btn-free-backspace-touch');
  if (freeTouchBackspaceBtn) {
    freeTouchBackspaceBtn.addEventListener('click', (e) => {
      e.preventDefault();
      freeEngine.manualBackspace();
    });
  }

  // Pause / Resume Test Controls
  const btnPauseTest = document.getElementById('btn-pause-test');
  if (btnPauseTest) {
    btnPauseTest.addEventListener('click', () => {
      engine.togglePause();
    });
  }

  const btnResumeOverlay = document.getElementById('btn-resume-overlay');
  if (btnResumeOverlay) {
    btnResumeOverlay.addEventListener('click', () => {
      engine.resumeTest();
    });
  }

  // Last Test Captured & Quick Replay Banner Logic
  function renderLastCapturedBanner() {
    const banner = document.getElementById('last-test-captured-banner');
    if (!banner) return;
    try {
      const raw = localStorage.getItem('typemaster_last_result');
      if (!raw) return;
      const lastResult = JSON.parse(raw);
      if (!lastResult) return;

      const wpmEl = document.getElementById('last-captured-net-wpm');
      const accEl = document.getElementById('last-captured-acc');
      const cpmEl = document.getElementById('last-captured-cpm');
      const timeEl = document.getElementById('last-captured-time');

      if (wpmEl) wpmEl.textContent = `${lastResult.net_wpm || 0} WPM`;
      if (accEl) accEl.textContent = `${lastResult.accuracy || 100}% Acc`;
      if (cpmEl) cpmEl.textContent = `${lastResult.cpm || 0} CPM`;
      if (timeEl) timeEl.textContent = `(${lastResult.elapsed_seconds || 0}s duration)`;

      banner.classList.remove('d-none');
    } catch (e) {}
  }

  window.updateLastCapturedBanner = renderLastCapturedBanner;
  renderLastCapturedBanner();

  const btnReplayLast = document.getElementById('btn-replay-last-captured');
  if (btnReplayLast) {
    btnReplayLast.addEventListener('click', () => {
      if (initialPassage && initialPassage.content) {
        engine.loadText(initialPassage.content);
        showToast('info', 'Replaying previous test passage!');
        if (engine.input) engine.input.focus();
      }
    });
  }

  const btnDismissLast = document.getElementById('btn-dismiss-last-captured');
  if (btnDismissLast) {
    btnDismissLast.addEventListener('click', () => {
      const banner = document.getElementById('last-test-captured-banner');
      if (banner) banner.classList.add('d-none');
    });
  }

  // Quick Restart Keyboard Shortcut (Tab + Enter)
  let tabPressed = false;
  window.addEventListener('keydown', (e) => {
    if (e.key === 'Tab') {
      tabPressed = true;
      setTimeout(() => { tabPressed = false; }, 800);
    } else if (e.key === 'Enter' && tabPressed) {
      e.preventDefault();
      tabPressed = false;
      if (!passageView.classList.contains('d-none')) {
        engine.loadText(initialPassage.content);
        showToast('info', 'Test restarted!');
      } else {
        freeEngine.clear();
      }
    }
  });

  // Passage Controls
  document.getElementById('btn-restart-test').addEventListener('click', () => {
    engine.loadText(initialPassage.content);
  });

  document.getElementById('btn-next-passage').addEventListener('click', () => {
    loadNewRandomPassage(currentDifficulty);
  });

  // Sound Toggle Button
  const soundBtn = document.getElementById('btn-sound-toggle');
  const soundIcon = document.getElementById('sound-icon');
  soundBtn.addEventListener('click', () => {
    isSoundEnabled = !isSoundEnabled;
    engine.options.soundEnabled = isSoundEnabled;
    freeEngine.options.soundEnabled = isSoundEnabled;
    if (isSoundEnabled) {
      soundIcon.className = 'bi bi-volume-up-fill text-success';
      showToast('info', 'Sound effects enabled (' + soundTheme + ')');
    } else {
      soundIcon.className = 'bi bi-volume-mute-fill text-secondary';
      showToast('info', 'Sound effects muted');
    }
  });

  // Duration Selector Buttons for Passage Drill
  document.querySelectorAll('.duration-selector-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.duration-selector-btn').forEach(b => b.classList.remove('active', 'text-info', 'fw-bold'));
      btn.classList.add('active', 'text-info', 'fw-bold');

      currentDuration = parseInt(btn.dataset.duration);
      engine.options.duration = currentDuration;
      engine.loadText(initialPassage.content);
    });
  });

  // Custom Text Modal Snippets
  const customTextArea = document.getElementById('custom-text-content');
  const customWordCounter = document.getElementById('custom-word-counter');

  function updateCustomCounter() {
    const words = customTextArea.value.trim().split(/\s+/).filter(w => w.length > 0);
    customWordCounter.textContent = `${words.length} words`;
  }

  customTextArea.addEventListener('input', updateCustomCounter);

  document.getElementById('btn-snippet-css').addEventListener('click', () => {
    document.getElementById('custom-text-title').value = 'CSS Root Variables';
    customTextArea.value = cssSampleSnippet;
    updateCustomCounter();
  });

  document.getElementById('btn-snippet-js').addEventListener('click', () => {
    document.getElementById('custom-text-title').value = 'Fibonacci Sequence';
    customTextArea.value = `function fibonacci(n) {\n    if (n <= 1) return n;\n    return fibonacci(n - 1) + fibonacci(n - 2);\n}`;
    updateCustomCounter();
  });

  // Apply Custom Text
  document.getElementById('btn-apply-custom-text').addEventListener('click', () => {
    const customContent = customTextArea.value.trim();
    const customTitle = document.getElementById('custom-text-title').value.trim() || 'Custom Text Drill';

    if (customContent.length < 5) {
      alert('Please enter some practice text or code.');
      return;
    }

    const words = customContent.split(/\s+/).filter(w => w.length > 0);

    initialPassage = {
      id: null,
      title: customTitle,
      content: customContent,
      difficulty: 'custom',
      word_count: words.length,
      char_count: customContent.length
    };

    document.getElementById('passage-title-display').textContent = customTitle;
    document.getElementById('passage-word-count-badge').textContent = words.length;

    engine.options.passageId = null;
    engine.options.difficulty = 'custom';
    engine.loadText(customContent);

    bootstrap.Modal.getInstance(document.getElementById('customTextModal')).hide();
    btnModePassage.click();
    showToast('success', 'Custom practice drill loaded!');
  });

  // Results Modal Buttons
  document.getElementById('btn-res-retry').addEventListener('click', () => {
    resultsModal.hide();
    if (!passageView.classList.contains('d-none')) {
      engine.loadText(initialPassage.content);
    } else {
      freeEngine.clear();
    }
  });

  document.getElementById('btn-res-next').addEventListener('click', () => {
    resultsModal.hide();
    if (!passageView.classList.contains('d-none')) {
      loadNewRandomPassage(currentDifficulty);
    } else {
      freeEngine.clear();
    }
  });

  function loadNewRandomPassage(difficulty) {
    fetch(`api/get-passage.php?difficulty=${encodeURIComponent(difficulty)}`)
      .then(res => res.json())
      .then(data => {
        if (data.success && data.passage) {
          initialPassage = data.passage;
          document.getElementById('passage-title-display').textContent = initialPassage.title;
          document.getElementById('passage-word-count-badge').textContent = initialPassage.word_count;
          engine.options.passageId = initialPassage.id;
          engine.options.difficulty = initialPassage.difficulty;
          engine.loadText(initialPassage.content);
          showToast('info', 'Loaded: ' + initialPassage.title);
        } else {
          engine.loadText(initialPassage.content);
        }
      })
      .catch(() => {
        engine.loadText(initialPassage.content);
      });
  }

  let latestResultData = null;

  function displayResultsModal(res) {
    latestResultData = res;

    // 1. Hero Net WPM & Rating Badge
    const netWpm = res.net_wpm ?? 0;
    const elNetWpm = document.getElementById('res-net-wpm');
    if (elNetWpm) elNetWpm.textContent = netWpm;

    // Dynamic Greeting Alert
    const greetIcon = document.getElementById('res-greeting-icon');
    const greetTitle = document.getElementById('res-greeting-title');
    const greetSub = document.getElementById('res-greeting-subtitle');
    if (greetIcon && greetTitle && greetSub) {
      if (netWpm >= 80) {
        greetIcon.textContent = '🌟';
        greetTitle.textContent = 'Godspeed Typist! Extraordinary Velocity!';
        greetSub.textContent = `Magnificent execution! At ${netWpm} Net WPM, your typing speed is in the top 1% globally. Review your diagnostic showing card or claim your official Certificate of Proficiency below.`;
      } else if (netWpm >= 60) {
        greetIcon.textContent = '🚀';
        greetTitle.textContent = 'Masterful Run! Blazing Speed & Precision!';
        greetSub.textContent = `Impressive! You clocked ${netWpm} Net WPM with stellar consistency. Your official Certificate of Typing Proficiency is ready to claim.`;
      } else if (netWpm >= 40) {
        greetIcon.textContent = '⚡';
        greetTitle.textContent = 'Great Job! Solid Professional Rhythm!';
        greetSub.textContent = `Well done! ${netWpm} Net WPM demonstrates solid typing speed and control. Keep practicing or claim your verified Certificate below.`;
      } else {
        greetIcon.textContent = '🎯';
        greetTitle.textContent = 'Practice Complete! Steady Progress!';
        greetSub.textContent = `Completed with ${netWpm} Net WPM. Consistent training sharpens muscle memory and quickly escalates typing speed!`;
      }
    }

    const ratingBadge = document.getElementById('res-rating-badge');
    if (ratingBadge) {
      const rating = getSpeedRating(netWpm);
      ratingBadge.innerHTML = `<span class="badge fs-6 px-3 py-1" style="background-color: ${rating.color};"><i class="bi ${rating.icon} me-1"></i>${rating.label}</span>`;
    }

    // Strict Mode Indicator in Results Modal
    const strictNote = document.getElementById('res-strict-mode-note');
    if (strictNote) {
      if (res.backspace_disabled || isBackspaceDisabled) {
        strictNote.innerHTML = '<i class="bi bi-slash-circle-fill text-danger me-1"></i> Strict Exam Rules: Backspace/Delete Disabled';
        strictNote.className = 'mt-3 text-danger small fw-bold';
      } else {
        strictNote.innerHTML = '<i class="bi bi-check-circle-fill text-success me-1"></i> Standard Mode: Backspace Enabled';
        strictNote.className = 'mt-3 text-success small';
      }
    }

    // =========================================================================
    // The 7 Core Metrics Showing Card (As requested by user):
    // 1) Elapsed / Timer (Format m:ss)
    // 2) Real-Time Words
    // 3) Live Accuracy
    // 4) Gross WPM
    // 5) CPM (Speed)
    // 6) Characters
    // 7) Lines
    // =========================================================================
    const elapsedSec = Math.round(res.elapsed_seconds || 0);
    const minutes = Math.floor(elapsedSec / 60);
    const seconds = elapsedSec % 60;
    const timerStr = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;

    // 1. Elapsed / Timer
    const elElapsedTimer = document.getElementById('res-elapsed-timer');
    if (elElapsedTimer) elElapsedTimer.textContent = timerStr;
    const elTimeElapsed = document.getElementById('res-time-elapsed');
    if (elTimeElapsed) elTimeElapsed.textContent = `${res.elapsed_seconds || 0}s duration`;

    // 2. Real-Time Words
    const elWords = document.getElementById('res-words-typed');
    if (elWords) elWords.textContent = res.words_typed ?? 0;

    // 3. Live Accuracy
    const elAcc = document.getElementById('res-accuracy');
    if (elAcc) elAcc.textContent = `${res.accuracy ?? 100}%`;

    // 4. Gross WPM
    const elGross = document.getElementById('res-gross-wpm');
    if (elGross) elGross.textContent = res.gross_wpm ?? 0;

    // 5. CPM (Speed)
    const totalChars = res.total_characters ?? res.total_keystrokes ?? ((res.words_typed ?? 0) * 5);
    const cpmVal = res.cpm || Math.round(totalChars / Math.max(0.1, (res.elapsed_seconds || 1) / 60));
    const elCpm = document.getElementById('res-cpm');
    if (elCpm) elCpm.textContent = cpmVal;

    // 6. Characters
    const elChars = document.getElementById('res-chars-count');
    if (elChars) elChars.textContent = totalChars;

    // 7. Lines
    const linesVal = res.total_lines || Math.max(1, Math.ceil((res.words_typed || 1) / 12));
    const elLines = document.getElementById('res-lines-count');
    if (elLines) elLines.textContent = linesVal;

    // 8. Keystroke Quality Score
    const elQuality = document.getElementById('res-quality-score');
    if (elQuality) {
      const tot = res.total_keystrokes || totalChars || 1;
      const cor = res.correct_keystrokes ?? tot;
      const qScore = Math.min(100, Math.max(0, Math.round((cor / Math.max(1, tot)) * 100)));
      elQuality.textContent = `${qScore}%`;
    }

    // Detailed Keystrokes Breakdown
    const elTotalKeys = document.getElementById('res-total-keys');
    if (elTotalKeys) elTotalKeys.textContent = res.total_keystrokes ?? 0;
    const elCorrectKeys = document.getElementById('res-correct-keys');
    if (elCorrectKeys) elCorrectKeys.textContent = res.correct_keystrokes ?? 0;
    const elErrorKeys = document.getElementById('res-error-keys');
    if (elErrorKeys) elErrorKeys.textContent = res.error_keystrokes ?? 0;
    const elBackspaces = document.getElementById('res-backspaces');
    if (elBackspaces) elBackspaces.textContent = res.backspace_count ?? 0;

    // Render Trajectory Speed Graph with Dark Orange palette
    renderSessionChart(res.speed_history || []);
    resultsModal.show();
  }

  // Certificate Modal Generator Logic
  function openCertificateModal() {
    const res = latestResultData || {
      net_wpm: document.getElementById('res-net-wpm')?.textContent || 0,
      gross_wpm: document.getElementById('res-gross-wpm')?.textContent || 0,
      accuracy: (document.getElementById('res-accuracy')?.textContent || '100%').replace('%', ''),
      elapsed_seconds: currentDuration || 60
    };
    const elCertNet = document.getElementById('certNetWpm');
    const elCertGross = document.getElementById('certGrossWpm');
    const elCertAcc = document.getElementById('certAccuracy');
    const elCertDur = document.getElementById('certDuration');

    if (elCertNet) elCertNet.textContent = res.net_wpm || 0;
    if (elCertGross) elCertGross.textContent = res.gross_wpm || res.net_wpm || 0;
    if (elCertAcc) elCertAcc.textContent = `${res.accuracy || 100}%`;
    const durSec = Math.round(res.elapsed_seconds || currentDuration || 60);
    if (elCertDur) {
      if (durSec < 60) elCertDur.textContent = `${durSec}s`;
      else elCertDur.textContent = `${Math.round(durSec / 60)} min`;
    }
    const certModal = new bootstrap.Modal(document.getElementById('certificateModal'));
    certModal.show();
  }

  const btnGenCert = document.getElementById('btn-generate-cert');
  if (btnGenCert) btnGenCert.addEventListener('click', openCertificateModal);

  const btnQuickCert = document.getElementById('btn-quick-cert-view');
  if (btnQuickCert) btnQuickCert.addEventListener('click', openCertificateModal);

  // Custom Time Presets Logic
  document.querySelectorAll('.quick-duration-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const sec = parseInt(btn.dataset.sec);
      const valInput = document.getElementById('custom-time-val');
      const unitInput = document.getElementById('custom-time-unit');
      if (sec >= 60 && sec % 60 === 0) {
        if (valInput) valInput.value = sec / 60;
        if (unitInput) unitInput.value = 'min';
      } else {
        if (valInput) valInput.value = sec;
        if (unitInput) unitInput.value = 'sec';
      }
      document.querySelectorAll('.quick-duration-btn').forEach(b => b.classList.remove('btn-warning', 'text-dark', 'fw-bold'));
      btn.classList.add('btn-warning', 'text-dark', 'fw-bold');
    });
  });

  // Apply Custom Time Duration
  const btnApplyCustomDuration = document.getElementById('btn-apply-custom-duration');
  if (btnApplyCustomDuration) {
    btnApplyCustomDuration.addEventListener('click', () => {
      const val = parseInt(document.getElementById('custom-time-val').value) || 60;
      const unit = document.getElementById('custom-time-unit').value;
      const totalSeconds = unit === 'min' ? val * 60 : val;
      const targetMode = document.querySelector('input[name="customTimeTarget"]:checked')?.value || 'passage';

      if (totalSeconds < 5) {
        alert('Please enter a duration of at least 5 seconds.');
        return;
      }

      if (targetMode === 'passage') {
        currentDuration = totalSeconds;
        engine.options.duration = totalSeconds;
        const liveTimer = document.getElementById('live-timer');
        if (liveTimer) liveTimer.textContent = `${totalSeconds}s`;
        engine.loadText(initialPassage.content);
        btnModePassage.click();
      } else {
        freeEngine.options.mode = 'timed';
        freeEngine.options.duration = totalSeconds;
        const liveFreeTimer = document.getElementById('live-free-timer');
        if (liveFreeTimer) {
          const m = Math.floor(totalSeconds / 60);
          const s = totalSeconds % 60;
          liveFreeTimer.textContent = `${m}:${s < 10 ? '0' : ''}${s}`;
        }
        btnModeFree.click();
        freeEngine.clear();
      }

      bootstrap.Modal.getInstance(document.getElementById('customDurationModal'))?.hide();
      showToast('success', `Custom time set: ${totalSeconds} seconds for ${targetMode === 'passage' ? 'Passage Drill' : 'Free Typing'}!`);
    });
  }

  // Auto-open custom modal if mode=custom in URL
  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.get('mode') === 'custom') {
    const customModal = new bootstrap.Modal(document.getElementById('customDurationModal'));
    customModal.show();
  }

  function getSpeedRating(wpm) {
    if (wpm >= 100) return { label: 'Typing God', color: '#ff6a00', icon: 'bi-trophy-fill' };
    if (wpm >= 80) return { label: 'Grandmaster', color: '#ff8c33', icon: 'bi-award-fill' };
    if (wpm >= 60) return { label: 'Professional', color: '#38bdf8', icon: 'bi-star-fill' };
    if (wpm >= 40) return { label: 'Intermediate', color: '#10b981', icon: 'bi-check-circle-fill' };
    if (wpm >= 25) return { label: 'Developing', color: '#f59e0b', icon: 'bi-graph-up-arrow' };
    return { label: 'Beginner', color: '#94a3b8', icon: 'bi-seedling' };
  }

  function renderSessionChart(history) {
    const ctx = document.getElementById('sessionSpeedChart');
    if (!ctx) return;

    if (sessionChartInstance) {
      sessionChartInstance.destroy();
    }

    const labels = (history && history.length > 0) ? history.map(h => `${h.second}s`) : ['0s', '1s'];
    const dataPoints = (history && history.length > 0) ? history.map(h => h.wpm) : [0, 0];

    sessionChartInstance = new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'WPM',
          data: dataPoints,
          borderColor: '#ff6a00',
          backgroundColor: 'rgba(255, 106, 0, 0.16)',
          fill: true,
          tension: 0.35,
          borderWidth: 2.5,
          pointRadius: 2.5,
          pointBackgroundColor: '#ff6a00',
          pointHoverRadius: 5,
          pointHoverBackgroundColor: '#ff8c33'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: '#121826',
            borderColor: '#ff6a00',
            borderWidth: 1,
            titleColor: '#f1f5f9',
            bodyColor: '#ff6a00',
            displayColors: false
          }
        },
        scales: {
          x: { grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#64748b', font: { size: 10 } } },
          y: { beginAtZero: true, grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#64748b', font: { size: 10 } } }
        }
      }
    });
  }
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
