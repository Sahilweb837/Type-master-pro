<?php
/**
 * Custom Type Master Pro
 * Live Typing Practice Arena — Pixel Theme, Clean Sans-Serif
 * Modes: Passage, Free Typing, Custom Text
 */

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/functions.php';

$currentUser = current_user();
$settings = $currentUser ? get_user_settings($currentUser['id']) : [
    'theme' => 'light', 'font_family' => 'JetBrains Mono', 'font_size' => 22,
    'line_height' => 1.8, 'caret_style' => 'line', 'caret_color' => '#38bdf8',
    'correct_color' => '#4ade80', 'incorrect_color' => '#f87171',
    'typing_area_width' => '920px', 'sound_enabled' => 1, 'sound_theme' => 'mechanical',
    'default_duration' => 60, 'default_difficulty' => 'intermediate',
    'show_live_wpm' => 1, 'show_live_accuracy' => 1, 'show_live_word_count' => 1
];

require_once __DIR__ . '/includes/passages_data.php';
$allPassagesList = get_all_passages();

$pdo = getDBConnection();
$selectedDifficulty = $_GET['difficulty'] ?? $settings['default_difficulty'] ?? 'intermediate';
$selectedDuration = (int)($_GET['duration'] ?? $settings['default_duration'] ?? 60);
$passageId = isset($_GET['passage_id']) ? (int)$_GET['passage_id'] : null;
$initialMode = $_GET['mode'] ?? 'passage';

$activePassage = null;
if ($pdo) {
    try {
        if ($passageId) {
            $stmt = $pdo->prepare("SELECT * FROM typing_passages WHERE id = :id AND status = 'active' LIMIT 1");
            $stmt->execute([':id' => $passageId]);
            $activePassage = $stmt->fetch();
        }
        if (!$activePassage) {
            $stmt = $pdo->prepare("SELECT * FROM typing_passages WHERE difficulty = :diff AND status = 'active' ORDER BY RAND() LIMIT 1");
            $stmt->execute([':diff' => $selectedDifficulty]);
            $activePassage = $stmt->fetch();
        }
        if (!$activePassage) {
            $activePassage = $pdo->query("SELECT * FROM typing_passages WHERE status = 'active' LIMIT 1")->fetch();
        }
    } catch (Exception $e) {
        error_log('Error loading passage: ' . $e->getMessage());
    }
}

if (!$activePassage) {
    $activePassage = ($passageId ? get_passage_by_id($passageId) : null) ?: get_random_passage($selectedDifficulty);
}

$pageTitle = 'Typing Practice Arena';
include __DIR__ . '/includes/header.php';
?>

<style>
/* ============================================================
   PRACTICE ARENA — TypoPrac Dark Obsidian & Flame Orange
   ============================================================ */
.practice-arena-wrap {
  --ink:        #f8fafc;
  --paper:      #131a29;
  --paper-soft: #192338;
  --paper-hard: #0b0f17;
  --shadow:     0px;
  --sans:       "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
  --mono:       "JetBrains Mono", ui-monospace, Menlo, Consolas, monospace;
  font-family: var(--sans);
  color: var(--ink);
}
html[data-theme="dark"] .practice-arena-wrap {
  --ink:        #f8fafc;
  --paper:      #131a29;
  --paper-soft: #192338;
  --paper-hard: #0b0f17;
}

/* ---------- Generic surfaces ---------- */
.practice-arena-wrap .pixel-card {
  background: var(--paper);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 14px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
}
.practice-arena-wrap .pixel-card-lg {
  background: var(--paper);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 16px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
}

/* ---------- Buttons ---------- */
.practice-arena-wrap .px-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.45rem;
  padding: 0.5rem 0.85rem;
  font-family: var(--sans);
  font-size: 0.82rem;
  font-weight: 600;
  letter-spacing: 0.02em;
  background: #192338;
  color: #f8fafc;
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 8px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
  cursor: pointer;
  line-height: 1.2;
  transition: all 0.15s ease;
  -webkit-appearance: none;
  appearance: none;
  text-decoration: none;
}
.practice-arena-wrap .px-btn:hover { background: #222f4b; color: #ff7a00; border-color: rgba(255, 87, 34, 0.4); }
.practice-arena-wrap .px-btn:active {
  transform: translateY(1px);
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
}
.practice-arena-wrap .px-btn-primary {
  background: linear-gradient(135deg, #ff5500 0%, #ff7a00 100%) !important;
  color: #ffffff !important;
  border: 1px solid rgba(255, 87, 34, 0.5) !important;
  box-shadow: 0 4px 14px rgba(255, 87, 34, 0.45) !important;
}
.practice-arena-wrap .px-btn:hover { background: var(--paper-soft); color: var(--ink); }
.practice-arena-wrap .px-btn:active {
  transform: translate(var(--shadow), var(--shadow));
  box-shadow: 0 0 0 var(--ink);
}
.practice-arena-wrap .px-btn i { font-size: 0.9rem; }
.practice-arena-wrap .px-btn-primary {
  background: var(--ink);
  color: var(--paper);
}
.practice-arena-wrap .px-btn-primary:hover { background: var(--ink); color: var(--paper); }
.practice-arena-wrap .px-btn-primary i { color: var(--paper); }
.practice-arena-wrap .px-btn-sm {
  padding: 0.4rem 0.65rem;
  font-size: 0.74rem;
  box-shadow: 2px 2px 0 var(--ink);
}
.practice-arena-wrap .px-btn-sm:active {
  transform: translate(2px, 2px);
  box-shadow: 0 0 0 var(--ink);
}
.practice-arena-wrap .px-btn-danger {
  color: #dc2626;
  border-color: #dc2626;
  box-shadow: 3px 3px 0 #dc2626;
}
.practice-arena-wrap .px-btn-danger:hover { background: #fee2e2; color: #dc2626; }
html[data-theme="dark"] .practice-arena-wrap .px-btn-danger:hover { background: #3f0f0f; }
.practice-arena-wrap .px-btn-danger:active { box-shadow: 0 0 0 #dc2626; }

.practice-arena-wrap kbd {
  font-family: var(--mono);
  font-size: 0.72rem;
  font-weight: 700;
  background: var(--ink);
  color: var(--paper);
  border: 2px solid var(--ink);
  padding: 0.1rem 0.35rem;
  line-height: 1.1;
  display: inline-block;
}

/* ---------- Ribbon (top toolbar) ---------- */
.practice-arena-wrap .ribbon {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 0.85rem;
  padding: 0.85rem 1rem;
  background: var(--paper);
  border: 3px solid var(--ink);
  box-shadow: var(--shadow) var(--shadow) 0 var(--ink);
  margin-bottom: 1rem;
}
.practice-arena-wrap .ribbon-group {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  flex-wrap: wrap;
}
.practice-arena-wrap .ribbon-label {
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
  font-family: var(--sans);
  font-size: 0.7rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--ink);
  opacity: 0.75;
  margin-right: 0.15rem;
}
.practice-arena-wrap .ribbon-label i { color: var(--ink); opacity: 0.85; font-size: 0.8rem; }
.practice-arena-wrap .ribbon-pill {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  padding: 0.35rem 0.7rem;
  background: var(--paper);
  color: var(--ink);
  border: 2px solid var(--ink);
  font-family: var(--sans);
  font-size: 0.72rem;
  font-weight: 600;
  letter-spacing: 0.02em;
  text-transform: uppercase;
  cursor: pointer;
  line-height: 1.15;
  text-decoration: none;
  transition: transform 0.05s steps(1), box-shadow 0.05s steps(1), background 0.05s steps(1);
  box-shadow: 2px 2px 0 var(--ink);
  -webkit-appearance: none;
  appearance: none;
}
.practice-arena-wrap .ribbon-pill:hover { background: var(--paper-soft); color: var(--ink); }
.practice-arena-wrap .ribbon-pill:active {
  transform: translate(2px, 2px);
  box-shadow: 0 0 0 var(--ink);
}
.practice-arena-wrap .ribbon-pill.active {
  background: var(--ink);
  color: var(--paper);
  box-shadow: 0 0 0 var(--ink);
}
.practice-arena-wrap .ribbon-pill.active i { color: var(--paper); }
.practice-arena-wrap .ribbon-divider {
  width: 2px;
  height: 26px;
  background: var(--ink);
  opacity: 0.3;
}

/* ---------- Skeleton ---------- */
.practice-arena-wrap .skeleton {
  background: linear-gradient(90deg, var(--paper-soft) 0%, var(--paper-hard) 50%, var(--paper-soft) 100%);
  background-size: 200% 100%;
  animation: pxShimmer 1.4s ease-in-out infinite;
  border: 2px solid var(--ink);
  border-radius: 0;
}
.practice-arena-wrap .skeleton-circle { border-radius: 50%; }
@keyframes pxShimmer {
  0%   { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}

/* ---------- Zen bar ---------- */
.practice-arena-wrap .zen-bar {
  background: var(--paper);
  border: 3px solid var(--ink);
  box-shadow: var(--shadow) var(--shadow) 0 var(--ink);
  padding: 0.85rem 1rem;
  margin-bottom: 0.75rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
  flex-wrap: wrap;
}
.practice-arena-wrap .zen-bar .zen-title {
  font-family: var(--sans);
  font-size: 0.92rem;
  font-weight: 700;
  color: var(--ink);
  letter-spacing: -0.01em;
}
.practice-arena-wrap .zen-bar .zen-meta {
  font-family: var(--sans);
  font-size: 0.74rem;
  color: var(--ink);
  opacity: 0.7;
  font-weight: 500;
}
.practice-arena-wrap .zen-timer-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.35rem 0.65rem;
  background: var(--paper);
  border: 2px solid var(--ink);
  box-shadow: 2px 2px 0 var(--ink);
}
.practice-arena-wrap .zen-timer-badge i { color: var(--ink); font-size: 1rem; }
.practice-arena-wrap .zen-timer-val {
  font-family: var(--mono);
  font-size: 0.95rem;
  font-weight: 700;
  color: var(--ink);
  letter-spacing: -0.02em;
}
.practice-arena-wrap .strict-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
  padding: 0.3rem 0.6rem;
  background: var(--paper);
  color: #dc2626;
  border: 2px solid #dc2626;
  font-family: var(--sans);
  font-size: 0.68rem;
  font-weight: 700;
  letter-spacing: 0.04em;
  text-transform: uppercase;
}
.practice-arena-wrap .strict-badge i { color: #dc2626; font-size: 0.75rem; }

/* ---------- Progress ---------- */
.practice-arena-wrap .progress-wrap {
  height: 8px;
  background: var(--paper-hard);
  border: 2px solid var(--ink);
  margin-bottom: 1rem;
  position: relative;
  overflow: hidden;
}
.practice-arena-wrap .progress-bar-pixel {
  height: 100%;
  width: 0%;
  background: var(--ink);
  transition: width 0.15s linear;
}

/* ---------- Typing box ---------- */
.practice-arena-wrap .typing-surface {
  position: relative;
  background: var(--paper);
  border: 3px solid var(--ink);
  box-shadow: 5px 5px 0 var(--ink);
  padding: 2rem 1.75rem;
  min-height: 200px;
  margin-bottom: 1rem;
}
.practice-arena-wrap .typing-surface::before {
  content: "";
  position: absolute;
  inset: 0;
  background-image:
    linear-gradient(45deg, rgba(0,0,0,0.03) 25%, transparent 25%),
    linear-gradient(-45deg, rgba(0,0,0,0.03) 25%, transparent 25%),
    linear-gradient(45deg, transparent 75%, rgba(0,0,0,0.03) 75%),
    linear-gradient(-45deg, transparent 75%, rgba(0,0,0,0.03) 75%);
  background-size: 8px 8px;
  background-position: 0 0, 0 4px, 4px -4px, -4px 0;
  pointer-events: none;
}
html[data-theme="dark"] .practice-arena-wrap .typing-surface::before {
  background-image:
    linear-gradient(45deg, rgba(255,255,255,0.04) 25%, transparent 25%),
    linear-gradient(-45deg, rgba(255,255,255,0.04) 25%, transparent 25%),
    linear-gradient(45deg, transparent 75%, rgba(255,255,255,0.04) 75%),
    linear-gradient(-45deg, transparent 75%, rgba(255,255,255,0.04) 75%);
}
.practice-arena-wrap .typing-surface > * { position: relative; z-index: 1; }

.practice-arena-wrap .typing-box {
  font-family: var(--mono);
  font-size: 1.35rem;
  line-height: 1.85;
  letter-spacing: 0.01em;
  color: var(--ink);
  word-break: break-word;
  user-select: none;
}
.practice-arena-wrap .typing-box .char { opacity: 0.35; }
.practice-arena-wrap .typing-box .char.correct { opacity: 1; }
.practice-arena-wrap .typing-box .char.incorrect {
  background: var(--ink);
  color: var(--paper);
  opacity: 1;
}
.practice-arena-wrap .typing-box .char.active {
  opacity: 1;
  background: var(--paper-soft);
  box-shadow: 0 0 0 3px var(--ink);
  animation: pxCaret 1s steps(2) infinite;
}
@keyframes pxCaret {
  0%, 49%   { box-shadow: 0 0 0 3px var(--ink); }
  50%, 100% { box-shadow: 0 0 0 3px transparent; }
}

.practice-arena-wrap .hidden-typing-input {
  position: absolute;
  opacity: 0;
  left: -9999px;
  top: -9999px;
  pointer-events: none;
}

/* Focus prompt overlay */
.practice-arena-wrap .focus-overlay {
  position: absolute;
  inset: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 1.5rem;
  background: rgba(19, 26, 41, 0.88);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 87, 34, 0.35);
  border-radius: 12px;
  cursor: pointer;
  text-align: center;
  transition: opacity 0.2s ease, visibility 0.2s ease;
  z-index: 10;
}
.practice-arena-wrap .focus-overlay.hidden,
.practice-arena-wrap .focus-overlay[style*="display: none"] {
  display: none !important;
  opacity: 0 !important;
  visibility: hidden !important;
  pointer-events: none !important;
}
.practice-arena-wrap .focus-overlay .focus-icon-box {
  width: 58px;
  height: 58px;
  background: rgba(255, 87, 34, 0.15);
  color: #ff5722;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 0.85rem;
  border: 1px solid rgba(255, 87, 34, 0.4);
  border-radius: 14px;
  box-shadow: 0 4px 16px rgba(255, 87, 34, 0.3);
}
.practice-arena-wrap .focus-overlay .focus-icon-box i {
  font-size: 1.6rem;
  color: #ff5722;
}
.practice-arena-wrap .focus-overlay .focus-title {
  font-family: var(--sans);
  font-size: 1.15rem;
  font-weight: 700;
  color: #ffffff;
  letter-spacing: -0.01em;
  margin-bottom: 0.35rem;
}
.practice-arena-wrap .focus-overlay .focus-sub {
  font-family: var(--sans);
  font-size: 0.85rem;
  color: #94a3b8;
  max-width: 360px;
  line-height: 1.5;
}

/* Pause overlay */
.practice-arena-wrap .pause-overlay {
  position: absolute;
  inset: 0;
  display: none;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  background: var(--ink);
  color: var(--paper);
  padding: 1.25rem;
  text-align: center;
  z-index: 6;
  border: 3px solid var(--ink);
}
.practice-arena-wrap .pause-overlay.show { display: flex; }
.practice-arena-wrap .pause-overlay .pause-icon {
  width: 60px;
  height: 60px;
  background: var(--paper);
  color: var(--ink);
  display: flex;
  align-items: center;
  justify-content: center;
  border: 3px solid var(--paper);
  margin-bottom: 1rem;
}
.practice-arena-wrap .pause-overlay .pause-icon i {
  font-size: 1.6rem;
  color: var(--ink);
}
.practice-arena-wrap .pause-overlay h4 {
  font-family: var(--sans);
  font-size: 1.1rem;
  font-weight: 800;
  color: var(--paper);
  margin: 0 0 0.4rem;
}
.practice-arena-wrap .pause-overlay p {
  font-family: var(--sans);
  font-size: 0.85rem;
  color: var(--paper);
  opacity: 0.75;
  max-width: 340px;
  margin-bottom: 1rem;
}

/* ---------- Controls toolbar ---------- */
.practice-arena-wrap .controls-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 0.75rem;
  padding: 0.85rem 1rem;
  background: var(--paper);
  border: 3px solid var(--ink);
  box-shadow: var(--shadow) var(--shadow) 0 var(--ink);
  margin-bottom: 1rem;
}
.practice-arena-wrap .controls-bar .ctrl-hint {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  font-family: var(--sans);
  font-size: 0.72rem;
  color: var(--ink);
  opacity: 0.75;
  font-weight: 500;
}

/* ---------- Free typing area ---------- */
.practice-arena-wrap .free-textarea-wrap {
  position: relative;
  background: var(--paper);
  border: 3px solid var(--ink);
  box-shadow: 5px 5px 0 var(--ink);
  padding: 0;
  margin-bottom: 1rem;
}
.practice-arena-wrap .free-textarea {
  width: 100%;
  min-height: 340px;
  padding: 1.5rem 1.75rem;
  background: transparent;
  color: var(--ink);
  border: 0;
  font-family: var(--mono);
  font-size: 1.1rem;
  line-height: 1.8;
  letter-spacing: 0.01em;
  resize: vertical;
  outline: none;
}
.practice-arena-wrap .free-textarea::placeholder {
  color: var(--ink);
  opacity: 0.35;
}
.practice-arena-wrap .free-stats {
  display: flex;
  justify-content: space-between;
  gap: 0.75rem;
  flex-wrap: wrap;
  padding: 0.75rem 1rem;
  border-top: 2px dashed var(--ink);
  font-family: var(--sans);
  font-size: 0.74rem;
  color: var(--ink);
  opacity: 0.75;
  background: var(--paper);
}
.practice-arena-wrap .free-stats kbd {
  background: var(--ink);
  color: var(--paper);
  border: 2px solid var(--ink);
  padding: 0.1rem 0.35rem;
  font-size: 0.7rem;
  font-family: var(--mono);
}

/* ---------- Free timer mode dropdown ---------- */
.practice-arena-wrap .free-mode-select {
  font-family: var(--sans);
  font-size: 0.78rem;
  font-weight: 500;
  background: var(--paper);
  color: var(--ink);
  border: 2px solid var(--ink);
  padding: 0.35rem 0.65rem;
  cursor: pointer;
  outline: none;
}

/* ---------- Last captured banner ---------- */
.practice-arena-wrap .last-capture-banner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 0.75rem;
  padding: 0.75rem 1rem;
  background: var(--paper);
  border: 3px solid var(--ink);
  box-shadow: var(--shadow) var(--shadow) 0 var(--ink);
  margin-bottom: 1rem;
}
.practice-arena-wrap .last-capture-banner .lc-label {
  font-family: var(--sans);
  font-size: 0.78rem;
  font-weight: 700;
  color: var(--ink);
  text-transform: uppercase;
  letter-spacing: 0.06em;
}
.practice-arena-wrap .last-capture-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
  padding: 0.15rem 0.5rem;
  font-family: var(--mono);
  font-size: 0.74rem;
  font-weight: 700;
  color: var(--ink);
  background: var(--paper-soft);
  border: 2px solid var(--ink);
}

/* ---------- Modals ---------- */
.practice-arena-wrap .modal-content,
.modal.pixel-modal .modal-content {
  background: var(--paper, #ffffff);
  color: var(--ink, #000000);
  border: 3px solid var(--ink, #000000);
  border-radius: 0;
  box-shadow: 6px 6px 0 var(--ink, #000000);
  font-family: var(--sans, "Inter", sans-serif);
  padding: 0;
}
html[data-theme="dark"] .modal.pixel-modal .modal-content,
html[data-theme="dark"] .practice-arena-wrap .modal-content {
  background: #000000;
  color: #ffffff;
  border-color: #ffffff;
  box-shadow: 6px 6px 0 #ffffff;
}

.modal.pixel-modal .modal-header {
  border-bottom: 3px solid currentColor;
  padding: 1rem 1.25rem;
  background: transparent;
}
.modal.pixel-modal .modal-title {
  font-family: "Inter", sans-serif;
  font-size: 1.05rem;
  font-weight: 800;
  color: inherit;
  letter-spacing: -0.01em;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.modal.pixel-modal .modal-title i { color: inherit; }
.modal.pixel-modal .modal-body {
  padding: 1.25rem;
  color: inherit;
}
.modal.pixel-modal .modal-footer {
  border-top: 3px solid currentColor;
  padding: 1rem 1.25rem;
  background: transparent;
}

.modal.pixel-modal .form-label {
  font-family: "Inter", sans-serif;
  font-size: 0.78rem;
  font-weight: 700;
  letter-spacing: 0.02em;
  color: inherit;
  margin-bottom: 0.4rem;
}
.modal.pixel-modal .form-control,
.modal.pixel-modal .form-select {
  font-family: "Inter", sans-serif;
  font-size: 0.88rem;
  background: transparent;
  color: inherit;
  border: 2px solid currentColor;
  border-radius: 0;
  padding: 0.55rem 0.7rem;
  outline: none;
}
.modal.pixel-modal .form-control:focus,
.modal.pixel-modal .form-select:focus {
  border-color: currentColor;
  box-shadow: 0 0 0 2px currentColor;
}
.modal.pixel-modal .form-control::placeholder {
  color: inherit;
  opacity: 0.35;
}
.modal.pixel-modal .form-text {
  font-family: "Inter", sans-serif;
  font-size: 0.72rem;
  color: inherit;
  opacity: 0.6;
}
.modal.pixel-modal .form-check-label {
  font-family: "Inter", sans-serif;
  font-size: 0.85rem;
  color: inherit;
}
.modal.pixel-modal .form-check-input {
  border: 2px solid currentColor;
  background: transparent;
  border-radius: 0;
}
.modal.pixel-modal .form-check-input:checked {
  background: currentColor;
  border-color: currentColor;
}

.modal.pixel-modal .btn-close {
  filter: invert(0);
}
html[data-theme="dark"] .modal.pixel-modal .btn-close {
  filter: invert(1);
}

.modal.pixel-modal .px-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.45rem;
  padding: 0.5rem 0.85rem;
  font-family: "Inter", sans-serif;
  font-size: 0.82rem;
  font-weight: 600;
  letter-spacing: 0.02em;
  text-transform: uppercase;
  background: transparent;
  color: inherit;
  border: 3px solid currentColor;
  box-shadow: 3px 3px 0 currentColor;
  cursor: pointer;
  transition: transform 0.05s steps(1), box-shadow 0.05s steps(1);
}
.modal.pixel-modal .px-btn:active {
  transform: translate(3px, 3px);
  box-shadow: 0 0 0 currentColor;
}
.modal.pixel-modal .px-btn-primary {
  background: currentColor;
}
.modal.pixel-modal .px-btn-primary span,
.modal.pixel-modal .px-btn-primary i {
  color: var(--paper, #ffffff);
}
html[data-theme="dark"] .modal.pixel-modal .px-btn-primary span,
html[data-theme="dark"] .modal.pixel-modal .px-btn-primary i {
  color: #000000;
}

/* ---------- Showing Card Modal ---------- */
.showing-card-modal .modal-content {
  background: var(--paper, #ffffff);
  color: var(--ink, #000000);
  border: 3px solid var(--ink, #000000);
  border-radius: 0;
  box-shadow: 8px 8px 0 var(--ink, #000000);
  font-family: var(--sans, "Inter", sans-serif);
  padding: 0;
  overflow: hidden;
}
html[data-theme="dark"] .showing-card-modal .modal-content {
  background: #000000;
  color: #ffffff;
  border-color: #ffffff;
  box-shadow: 8px 8px 0 #ffffff;
}
.showing-card-modal .sc-header {
  padding: 1.25rem 1.5rem;
  background: var(--paper, #ffffff);
  color: var(--ink, #000000);
  border-bottom: 3px solid var(--ink, #000000);
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
  flex-wrap: wrap;
}
html[data-theme="dark"] .showing-card-modal .sc-header {
  background: #000000;
  color: #ffffff;
  border-color: #ffffff;
}
.showing-card-modal .sc-title-wrap {
  display: flex;
  align-items: center;
  gap: 0.85rem;
}
.showing-card-modal .sc-trophy-box {
  width: 48px;
  height: 48px;
  background: var(--ink, #000000);
  color: var(--paper, #ffffff);
  display: flex;
  align-items: center;
  justify-content: center;
  border: 3px solid var(--ink, #000000);
  flex-shrink: 0;
}
html[data-theme="dark"] .showing-card-modal .sc-trophy-box {
  background: #ffffff;
  color: #000000;
  border-color: #ffffff;
}
.showing-card-modal .sc-trophy-box i { font-size: 1.4rem; }
.showing-card-modal .sc-headline {
  font-family: var(--sans, "Inter", sans-serif);
  font-size: 1.1rem;
  font-weight: 800;
  color: inherit;
  letter-spacing: -0.01em;
  margin: 0 0 0.15rem;
}
.showing-card-modal .sc-subline {
  font-family: var(--sans, "Inter", sans-serif);
  font-size: 0.78rem;
  color: inherit;
  opacity: 0.7;
  margin: 0;
}
.showing-card-modal .sc-body {
  padding: 1.5rem;
  background: var(--paper, #ffffff);
  color: var(--ink, #000000);
}
html[data-theme="dark"] .showing-card-modal .sc-body {
  background: #000000;
  color: #ffffff;
}

/* Greeting */
.showing-card-modal .sc-greeting {
  display: flex;
  align-items: center;
  gap: 0.85rem;
  padding: 0.9rem 1rem;
  background: var(--paper-soft, #f4f4f4);
  border: 3px solid currentColor;
  box-shadow: 3px 3px 0 currentColor;
  margin-bottom: 1.25rem;
}
html[data-theme="dark"] .showing-card-modal .sc-greeting {
  background: #141414;
}
.showing-card-modal .sc-greeting-icon { font-size: 2rem; flex-shrink: 0; }
.showing-card-modal .sc-greeting-title {
  font-family: var(--sans, "Inter", sans-serif);
  font-size: 1rem;
  font-weight: 800;
  color: inherit;
  margin: 0 0 0.2rem;
}
.showing-card-modal .sc-greeting-sub {
  font-family: var(--sans, "Inter", sans-serif);
  font-size: 0.8rem;
  color: inherit;
  opacity: 0.75;
  margin: 0;
}

/* Hero metric */
.showing-card-modal .sc-hero-box {
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 1.25rem;
  background: var(--paper-soft, #f4f4f4);
  border: 3px solid currentColor;
  box-shadow: 3px 3px 0 currentColor;
  height: 100%;
}
html[data-theme="dark"] .showing-card-modal .sc-hero-box {
  background: #141414;
}
.showing-card-modal .sc-hero-label {
  font-family: var(--sans, "Inter", sans-serif);
  font-size: 0.7rem;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: inherit;
  opacity: 0.7;
  margin-bottom: 0.4rem;
}
.showing-card-modal .sc-hero-value {
  font-family: var(--mono, "JetBrains Mono", monospace);
  font-size: 3rem;
  font-weight: 700;
  color: inherit;
  line-height: 1;
  letter-spacing: -0.03em;
  margin-bottom: 0.3rem;
}
.showing-card-modal .sc-hero-sublabel {
  font-family: var(--sans, "Inter", sans-serif);
  font-size: 0.78rem;
  font-weight: 500;
  color: inherit;
  opacity: 0.7;
}
.showing-card-modal .sc-rating-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
  padding: 0.3rem 0.7rem;
  font-family: var(--sans, "Inter", sans-serif);
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.04em;
  text-transform: uppercase;
  background: var(--ink, #000000);
  color: var(--paper, #ffffff);
  border: 2px solid var(--ink, #000000);
  margin-top: 0.75rem;
  align-self: flex-start;
}
html[data-theme="dark"] .showing-card-modal .sc-rating-badge {
  background: #ffffff;
  color: #000000;
  border-color: #ffffff;
}
.showing-card-modal .sc-strict-note {
  font-family: var(--sans, "Inter", sans-serif);
  font-size: 0.75rem;
  font-weight: 600;
  margin-top: 0.75rem;
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
}

/* Chart box */
.showing-card-modal .sc-chart-box {
  background: var(--paper-soft, #f4f4f4);
  border: 3px solid currentColor;
  box-shadow: 3px 3px 0 currentColor;
  padding: 1rem;
  height: 100%;
}
html[data-theme="dark"] .showing-card-modal .sc-chart-box {
  background: #141414;
}
.showing-card-modal .sc-chart-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.5rem;
  margin-bottom: 0.75rem;
  flex-wrap: wrap;
}
.showing-card-modal .sc-chart-title {
  font-family: var(--sans, "Inter", sans-serif);
  font-size: 0.82rem;
  font-weight: 700;
  color: inherit;
}
.showing-card-modal .sc-chart-tag {
  font-family: var(--sans, "Inter", sans-serif);
  font-size: 0.66rem;
  font-weight: 600;
  letter-spacing: 0.04em;
  text-transform: uppercase;
  padding: 0.15rem 0.45rem;
  background: var(--ink, #000000);
  color: var(--paper, #ffffff);
  border: 2px solid var(--ink, #000000);
}
html[data-theme="dark"] .showing-card-modal .sc-chart-tag {
  background: #ffffff;
  color: #000000;
  border-color: #ffffff;
}

/* Stats grid */
.showing-card-modal .sc-section-title {
  font-family: var(--sans, "Inter", sans-serif);
  font-size: 0.7rem;
  font-weight: 700;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: inherit;
  opacity: 0.7;
  margin-bottom: 0.75rem;
  display: block;
}
.showing-card-modal .sc-stat-card {
  background: var(--paper, #ffffff);
  border: 3px solid currentColor;
  box-shadow: 3px 3px 0 currentColor;
  padding: 0.85rem 0.65rem;
  text-align: center;
  height: 100%;
}
html[data-theme="dark"] .showing-card-modal .sc-stat-card {
  background: #000000;
}
.showing-card-modal .sc-stat-icon {
  width: 36px;
  height: 36px;
  margin: 0 auto 0.55rem;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--ink, #000000);
  color: var(--paper, #ffffff);
  border: 2px solid currentColor;
}
html[data-theme="dark"] .showing-card-modal .sc-stat-icon {
  background: #ffffff;
  color: #000000;
}
.showing-card-modal .sc-stat-icon i { font-size: 1rem; }
.showing-card-modal .sc-stat-label {
  font-family: var(--sans, "Inter", sans-serif);
  font-size: 0.62rem;
  font-weight: 700;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: inherit;
  opacity: 0.7;
  margin-bottom: 0.25rem;
  line-height: 1.2;
  min-height: 1.6em;
}
.showing-card-modal .sc-stat-value {
  font-family: var(--mono, "JetBrains Mono", monospace);
  font-size: 1.35rem;
  font-weight: 700;
  color: inherit;
  line-height: 1;
  letter-spacing: -0.02em;
}
.showing-card-modal .sc-stat-sub {
  font-family: var(--sans, "Inter", sans-serif);
  font-size: 0.62rem;
  color: inherit;
  opacity: 0.6;
  margin-top: 0.25rem;
}

/* Diagnostics */
.showing-card-modal .sc-diagnostics {
  background: var(--paper-soft, #f4f4f4);
  border: 3px solid currentColor;
  box-shadow: 3px 3px 0 currentColor;
  padding: 0.85rem 1rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
  flex-wrap: wrap;
  margin-bottom: 1.25rem;
}
html[data-theme="dark"] .showing-card-modal .sc-diagnostics {
  background: #141414;
}
.showing-card-modal .sc-diag-title {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  font-family: var(--sans, "Inter", sans-serif);
  font-size: 0.82rem;
  font-weight: 700;
  color: inherit;
}
.showing-card-modal .sc-diag-list {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  font-family: var(--sans, "Inter", sans-serif);
  font-size: 0.75rem;
  color: inherit;
  opacity: 0.75;
}
.showing-card-modal .sc-diag-list strong {
  font-family: var(--mono, "JetBrains Mono", monospace);
  font-size: 0.85rem;
  font-weight: 700;
  color: inherit;
  opacity: 1;
  margin-left: 0.15rem;
}

.showing-card-modal .sc-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
  padding: 1rem 1.5rem;
  background: var(--paper, #ffffff);
  color: var(--ink, #000000);
  border-top: 3px solid var(--ink, #000000);
  flex-wrap: wrap;
}
html[data-theme="dark"] .showing-card-modal .sc-footer {
  background: #000000;
  color: #ffffff;
  border-color: #ffffff;
}
.showing-card-modal .sc-footer-note {
  font-family: var(--sans, "Inter", sans-serif);
  font-size: 0.76rem;
  color: inherit;
  opacity: 0.75;
}
.showing-card-modal .sc-footer-actions {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.modal.pixel-modal .px-btn,
.showing-card-modal .px-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.45rem;
  padding: 0.5rem 0.9rem;
  font-family: "Inter", sans-serif;
  font-size: 0.82rem;
  font-weight: 600;
  letter-spacing: 0.02em;
  text-transform: uppercase;
  background: transparent;
  color: inherit;
  border: 3px solid currentColor;
  box-shadow: 3px 3px 0 currentColor;
  cursor: pointer;
  transition: transform 0.05s steps(1), box-shadow 0.05s steps(1);
}
.modal.pixel-modal .px-btn:active,
.showing-card-modal .px-btn:active {
  transform: translate(3px, 3px);
  box-shadow: 0 0 0 currentColor;
}
.modal.pixel-modal .px-btn-primary,
.showing-card-modal .px-btn-primary {
  background: currentColor;
}
.modal.pixel-modal .px-btn-primary span,
.modal.pixel-modal .px-btn-primary i,
.showing-card-modal .px-btn-primary span,
.showing-card-modal .px-btn-primary i {
  color: var(--paper, #ffffff);
}
html[data-theme="dark"] .modal.pixel-modal .px-btn-primary span,
html[data-theme="dark"] .modal.pixel-modal .px-btn-primary i,
html[data-theme="dark"] .showing-card-modal .px-btn-primary span,
html[data-theme="dark"] .showing-card-modal .px-btn-primary i {
  color: #000000;
}

/* ---------- Certificate ---------- */
.certificate-modal .modal-content {
  background: var(--paper, #ffffff);
  color: var(--ink, #000000);
  border: 3px solid var(--ink, #000000);
  border-radius: 0;
  box-shadow: 6px 6px 0 var(--ink, #000000);
  font-family: var(--sans, "Inter", sans-serif);
  padding: 0;
}
html[data-theme="dark"] .certificate-modal .modal-content {
  background: #000000;
  color: #ffffff;
  border-color: #ffffff;
  box-shadow: 6px 6px 0 #ffffff;
}
.certificate-modal .modal-header {
  border-bottom: 3px solid currentColor;
  padding: 1rem 1.25rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.certificate-modal .modal-title {
  font-family: "Inter", sans-serif;
  font-size: 1rem;
  font-weight: 800;
  color: inherit;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.certificate-modal .modal-body { padding: 1.5rem; }

.certificate-modal .cert-frame {
  border: 3px double currentColor;
  padding: 2rem 1.75rem;
  text-align: center;
  position: relative;
  background: var(--paper, #ffffff);
  color: var(--ink, #000000);
}
html[data-theme="dark"] .certificate-modal .cert-frame {
  background: #000000;
  color: #ffffff;
}
.certificate-modal .cert-corner {
  position: absolute;
  width: 16px;
  height: 16px;
  border: 3px solid currentColor;
}
.certificate-modal .cert-corner-tl { top: 8px; left: 8px; border-right: 0; border-bottom: 0; }
.certificate-modal .cert-corner-tr { top: 8px; right: 8px; border-left: 0; border-bottom: 0; }
.certificate-modal .cert-corner-bl { bottom: 8px; left: 8px; border-right: 0; border-top: 0; }
.certificate-modal .cert-corner-br { bottom: 8px; right: 8px; border-left: 0; border-top: 0; }

.certificate-modal .cert-emblem {
  width: 60px;
  height: 60px;
  margin: 0 auto 0.85rem;
  background: currentColor;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 3px solid currentColor;
}
.certificate-modal .cert-emblem i {
  font-size: 1.8rem;
  color: var(--paper, #ffffff);
}
html[data-theme="dark"] .certificate-modal .cert-emblem i {
  color: #000000;
}

.certificate-modal .cert-board {
  font-family: var(--sans, "Inter", sans-serif);
  font-size: 0.68rem;
  font-weight: 700;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  color: inherit;
  opacity: 0.75;
  margin-bottom: 0.4rem;
}
.certificate-modal .cert-title {
  font-family: var(--sans, "Inter", sans-serif);
  font-size: 1.4rem;
  font-weight: 800;
  letter-spacing: -0.02em;
  color: inherit;
  margin: 0 0 0.35rem;
}
.certificate-modal .cert-subtitle {
  font-family: var(--sans, "Inter", sans-serif);
  font-size: 0.8rem;
  color: inherit;
  opacity: 0.7;
  margin-bottom: 1.5rem;
}
.certificate-modal .cert-certifies {
  font-family: var(--sans, "Inter", sans-serif);
  font-size: 0.78rem;
  color: inherit;
  opacity: 0.7;
  margin-bottom: 0.25rem;
}
.certificate-modal .cert-candidate {
  font-family: var(--sans, "Inter", sans-serif);
  font-size: 1.5rem;
  font-weight: 800;
  color: inherit;
  letter-spacing: -0.01em;
  margin-bottom: 1rem;
  border-bottom: 2px solid currentColor;
  display: inline-block;
  padding-bottom: 0.25rem;
}
.certificate-modal .cert-description {
  font-family: var(--sans, "Inter", sans-serif);
  font-size: 0.82rem;
  color: inherit;
  opacity: 0.8;
  max-width: 560px;
  margin: 0 auto 1.5rem;
  line-height: 1.5;
}
.certificate-modal .cert-metric-badge {
  background: var(--paper-soft, #f4f4f4);
  border: 3px solid currentColor;
  box-shadow: 3px 3px 0 currentColor;
  padding: 0.75rem 0.5rem;
  text-align: center;
}
html[data-theme="dark"] .certificate-modal .cert-metric-badge {
  background: #141414;
}
.certificate-modal .cert-metric-val {
  font-family: var(--mono, "JetBrains Mono", monospace);
  font-size: 1.4rem;
  font-weight: 700;
  color: inherit;
  line-height: 1;
  margin-bottom: 0.3rem;
}
.certificate-modal .cert-metric-lbl {
  font-family: var(--sans, "Inter", sans-serif);
  font-size: 0.65rem;
  font-weight: 600;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: inherit;
  opacity: 0.75;
}
.certificate-modal .cert-seal {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.25rem 0.6rem;
  background: currentColor;
  border: 2px solid currentColor;
  font-family: var(--sans, "Inter", sans-serif);
  font-size: 0.66rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--paper, #ffffff);
  margin-bottom: 0.4rem;
}
html[data-theme="dark"] .certificate-modal .cert-seal {
  color: #000000;
}
.certificate-modal .cert-seal i { font-size: 0.75rem; }
.certificate-modal .cert-meta-row {
  font-family: var(--sans, "Inter", sans-serif);
  font-size: 0.72rem;
  color: inherit;
  opacity: 0.7;
}
.certificate-modal .cert-meta-row strong {
  font-family: var(--mono, "JetBrains Mono", monospace);
  color: inherit;
  opacity: 1;
  font-weight: 700;
}
.certificate-modal .cert-signature {
  font-family: var(--mono, "JetBrains Mono", monospace);
  font-weight: 700;
  font-size: 0.85rem;
  color: inherit;
  border-bottom: 2px solid currentColor;
  padding-bottom: 0.15rem;
  display: inline-block;
}

.certificate-modal .modal-footer {
  border-top: 3px solid currentColor;
  padding: 0.85rem 1.25rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 0.5rem;
}

/* ---------- Responsive ---------- */
@media (max-width: 768px) {
  .practice-arena-wrap .typing-box { font-size: 1.1rem; line-height: 1.7; }
  .practice-arena-wrap .ribbon { padding: 0.65rem 0.75rem; gap: 0.55rem; }
  .practice-arena-wrap .ribbon-pill { font-size: 0.68rem; padding: 0.3rem 0.55rem; }
  .practice-arena-wrap .ribbon-label { font-size: 0.64rem; }
  .practice-arena-wrap .typing-surface { padding: 1.25rem 1rem; }
  .practice-arena-wrap .free-textarea { font-size: 1rem; padding: 1.1rem 1rem; min-height: 260px; }
  .showing-card-modal .sc-hero-value { font-size: 2.2rem; }
  .certificate-modal .cert-title { font-size: 1.1rem; }
  .certificate-modal .cert-candidate { font-size: 1.2rem; }
  .certificate-modal .cert-frame { padding: 1.25rem 1rem; }
}
@media (max-width: 575px) {
  .practice-arena-wrap .zen-bar { padding: 0.65rem 0.75rem; gap: 0.5rem; }
  .practice-arena-wrap .zen-bar .zen-title { font-size: 0.84rem; }
  .practice-arena-wrap .controls-bar { padding: 0.65rem 0.75rem; gap: 0.5rem; }
  .practice-arena-wrap .px-btn { font-size: 0.72rem; padding: 0.4rem 0.65rem; }
  .practice-arena-wrap .px-btn-sm { font-size: 0.66rem; padding: 0.35rem 0.55rem; }
  .practice-arena-wrap .strict-badge { font-size: 0.6rem; padding: 0.2rem 0.45rem; }
  .practice-arena-wrap .free-stats { font-size: 0.66rem; }
  .showing-card-modal .sc-stat-value { font-size: 1.05rem; }
  .showing-card-modal .sc-stat-label { font-size: 0.56rem; }
  .showing-card-modal .sc-body { padding: 1rem; }
}
</style>

<div class="practice-arena-wrap container-fluid px-md-4 py-3">

  <!-- Skeleton loading wrapper -->
  <div id="practice-skeleton-view" class="skeleton-wrapper">
    <div class="pixel-card p-3 mb-3 d-flex align-items-center justify-content-between" style="gap:0.75rem;flex-wrap:wrap;">
      <div class="skeleton" style="width:320px;height:38px;max-width:100%;"></div>
      <div class="d-flex gap-2">
        <div class="skeleton skeleton-circle" style="width:36px;height:36px;"></div>
        <div class="skeleton skeleton-circle" style="width:36px;height:36px;"></div>
      </div>
    </div>
    <div class="d-flex gap-2 mb-3 overflow-hidden flex-wrap">
      <?php for ($i = 0; $i < 7; $i++): ?>
        <div class="skeleton flex-fill p-3" style="min-width:120px;min-height:72px;"></div>
      <?php endfor; ?>
    </div>
    <div class="pixel-card p-4 mb-3" style="min-height:240px;">
      <div class="skeleton mb-3" style="width:40%;height:24px;"></div>
      <div class="skeleton mb-2" style="width:95%;height:14px;"></div>
      <div class="skeleton mb-2" style="width:88%;height:14px;"></div>
      <div class="skeleton mb-2" style="width:92%;height:14px;"></div>
      <div class="skeleton" style="width:65%;height:14px;"></div>
    </div>
    <div class="pixel-card p-3 d-flex justify-content-between align-items-center" style="gap:0.75rem;flex-wrap:wrap;">
      <div class="d-flex gap-2 flex-wrap">
        <div class="skeleton" style="width:110px;height:36px;"></div>
        <div class="skeleton" style="width:120px;height:36px;"></div>
        <div class="skeleton" style="width:110px;height:36px;"></div>
      </div>
      <div class="skeleton" style="width:180px;height:28px;"></div>
    </div>
  </div>

  <!-- Real content -->
  <!-- Real content -->
  <div id="practice-content-view" style="display:none;">

    <!-- Panoramic Hero Banner (from Mockup) -->
    <div class="dash-hero-banner mb-4">
      <div class="dash-hero-overlay">
        <div>
          <div class="text-orange text-uppercase fw-bold mb-1" style="font-size: 0.72rem; letter-spacing: 0.15em; color: #ff7a00;">
            Practice • Improve • Master
          </div>
          <h2 class="fw-bold mb-2 text-white" style="font-size: 1.85rem; letter-spacing: -0.5px;">
            Typing Practice <span style="color: #ff5722;">Arena</span>
          </h2>
          <p class="text-secondary small mb-0" style="max-width: 520px; line-height: 1.5;">
            Master your keyboard speed, accuracy, and rhythmic flow with 195+ curated drills.
          </p>
        </div>

        <!-- Level XP Card -->
        <div class="dash-level-card d-none d-md-block">
          <div class="d-flex align-items-center justify-content-between mb-1">
            <div class="d-flex align-items-center gap-1.5">
              <i class="bi bi-star-fill text-warning" style="font-size: 0.85rem;"></i>
              <span class="fw-bold text-white small">Level 3 Master</span>
            </div>
            <span class="badge" style="background: rgba(255, 87, 34, 0.2); color: #ff7a00; font-size: 0.65rem;">Active Drill</span>
          </div>
          <div class="dash-level-bar">
            <div class="dash-level-bar-fill" style="width: 65%;"></div>
          </div>
          <div class="d-flex justify-content-between text-secondary" style="font-size: 0.7rem;">
            <span>350 XP</span>
            <span>650 XP to Level 4</span>
          </div>
        </div>
      </div>
    </div>

    <!-- 4 Mode Cards (from Mockup) -->
    <div class="mode-row mb-4">
      <a href="<?= BASE_URL ?>/practice.php?mode=passage" class="mode-card active">
        <div class="d-flex align-items-center gap-3">
          <div class="mode-card-icon" style="background: rgba(255, 87, 34, 0.15); color: #ff5722;">
            <i class="bi bi-file-earmark-text-fill"></i>
          </div>
          <div>
            <div class="fw-bold small text-white">Text Practice</div>
            <div class="text-secondary" style="font-size: 0.75rem;">Practice with curated texts</div>
          </div>
        </div>
        <i class="bi bi-chevron-right text-orange fs-6"></i>
      </a>

      <a href="<?= BASE_URL ?>/practice.php?mode=custom" class="mode-card">
        <div class="d-flex align-items-center gap-3">
          <div class="mode-card-icon" style="background: rgba(0, 230, 118, 0.1); color: #00e676;">
            <i class="bi bi-speedometer2"></i>
          </div>
          <div>
            <div class="fw-bold small text-white">Typing Test</div>
            <div class="text-secondary" style="font-size: 0.75rem;">Check speed &amp; accuracy</div>
          </div>
        </div>
        <i class="bi bi-chevron-right text-secondary fs-6"></i>
      </a>

      <a href="<?= BASE_URL ?>/lessons.php" class="mode-card">
        <div class="d-flex align-items-center gap-3">
          <div class="mode-card-icon" style="background: rgba(56, 189, 248, 0.1); color: #38bdf8;">
            <i class="bi bi-mortarboard-fill"></i>
          </div>
          <div>
            <div class="fw-bold small text-white">Lessons</div>
            <div class="text-secondary" style="font-size: 0.75rem;">Step by step learning</div>
          </div>
        </div>
        <i class="bi bi-chevron-right text-secondary fs-6"></i>
      </a>

      <a href="<?= BASE_URL ?>/games.php" class="mode-card">
        <div class="d-flex align-items-center gap-3">
          <div class="mode-card-icon" style="background: rgba(236, 72, 153, 0.1); color: #ec4899;">
            <i class="bi bi-controller"></i>
          </div>
          <div>
            <div class="fw-bold small text-white">Games</div>
            <div class="text-secondary" style="font-size: 0.75rem;">Fun arcade practice</div>
          </div>
        </div>
        <i class="bi bi-chevron-right text-secondary fs-6"></i>
      </a>
    </div>

    <div class="typing-arena-wrapper">

      <!-- Ribbon Controls -->
      <div class="ribbon">
        <div class="ribbon-group">
          <span class="ribbon-label"><i class="bi bi-grid-fill"></i> Mode:</span>
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

        <div class="ribbon-group">
          <span class="ribbon-label"><i class="bi bi-stopwatch-fill"></i> Clock:</span>
          <?php
            $clockDurations = [30 => '30s', 60 => '1m', 180 => '3m', 360 => '6m', 600 => '10m', 1800 => '30m'];
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

        <div class="ribbon-group">
          <span class="ribbon-label"><i class="bi bi-speedometer2"></i> Diff:</span>
          <a href="?difficulty=beginner&duration=<?= $selectedDuration ?>" class="ribbon-pill <?= ($selectedDifficulty === 'beginner') ? 'active' : '' ?>">Beginner</a>
          <a href="?difficulty=intermediate&duration=<?= $selectedDuration ?>" class="ribbon-pill <?= ($selectedDifficulty === 'intermediate') ? 'active' : '' ?>">Intermediate</a>
          <a href="?difficulty=advanced&duration=<?= $selectedDuration ?>" class="ribbon-pill <?= ($selectedDifficulty === 'advanced') ? 'active' : '' ?>">Advanced</a>
        </div>

        <div class="ribbon-divider d-none d-xl-block"></div>

        <div class="ribbon-group ms-auto">
          <button type="button" class="ribbon-pill" id="btn-sound-toggle" title="Toggle Typing Sounds">
            <i class="bi <?= (!empty($settings['sound_enabled']) && $settings['sound_theme'] !== 'silent') ? 'bi-volume-up-fill' : 'bi-volume-mute-fill' ?>" id="sound-icon"></i>
          </button>
          <a href="<?= BASE_URL ?>/settings.php" class="ribbon-pill" title="Typing Preferences">
            <i class="bi bi-gear-fill"></i>
          </a>
        </div>
      </div>

      <!-- PASSAGE VIEW -->
      <div id="passage-arena-view" class="<?= ($initialMode === 'free') ? 'd-none' : '' ?>">
        <div id="last-test-captured-banner" class="last-capture-banner d-none">
          <div class="d-flex align-items-center gap-2 flex-wrap">
            <i class="bi bi-bookmark-check-fill" style="font-size:1.1rem;"></i>
            <span class="lc-label">Last Session Captured:</span>
            <span class="last-capture-badge" id="last-captured-net-wpm">0 WPM</span>
            <span class="last-capture-badge" id="last-captured-acc">100% Acc</span>
            <span class="last-capture-badge" id="last-captured-cpm">0 CPM</span>
            <span class="lc-label" style="font-weight:500;" id="last-captured-time"></span>
          </div>
          <div class="d-flex align-items-center gap-2">
            <button type="button" class="px-btn px-btn-sm px-btn-primary" id="btn-replay-last-captured">
              <i class="bi bi-arrow-repeat"></i> Replay
            </button>
            <button type="button" class="btn-close" id="btn-dismiss-last-captured" aria-label="Dismiss"></button>
          </div>
        </div>

        <div class="zen-bar p-3 rounded-3 mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2" style="background: #131a29; border: 1px solid rgba(255,255,255,0.08);">
          <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="zen-title fw-bold text-white fs-6" id="passage-title-display"><?= e($activePassage['title']) ?></span>
            <?= get_difficulty_badge($activePassage['difficulty']) ?>
            <span class="badge" style="background: rgba(255,255,255,0.06); color: #94a3b8; font-weight: 500;">
              <strong id="passage-word-count-badge" class="text-white"><?= (int)$activePassage['word_count'] ?></strong> words
            </span>
            <span class="badge" style="background: rgba(255,255,255,0.06); color: #94a3b8; font-weight: 500;">
              <strong id="passage-char-count-badge" class="text-white"><?= (int)$activePassage['char_count'] ?></strong> chars
            </span>
          </div>

          <!-- Prominent Live Telemetry Badges (Live Timer, Live Words, Live Speed, Live Acc) -->
          <div class="d-flex align-items-center gap-2 flex-wrap">
            <!-- Timer Badge -->
            <div class="d-flex align-items-center gap-1.5 px-3 py-1.5 rounded-2" style="background: rgba(255, 87, 34, 0.15); border: 1px solid rgba(255, 87, 34, 0.4); color: #ff7a00; font-family: 'JetBrains Mono', monospace; font-weight: 700;">
              <i class="bi bi-clock-fill"></i>
              <span id="live-timer" style="font-size: 0.95rem;"><?= $selectedDuration ?>s</span>
            </div>

            <!-- Words Counter Badge -->
            <div class="d-flex align-items-center gap-1.5 px-2.5 py-1.5 rounded-2" style="background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.08); font-size: 0.8rem;">
              <i class="bi bi-file-text-fill text-info"></i>
              <span class="text-secondary">Words:</span>
              <span id="live-words-count" class="text-white fw-bold">0</span>
            </div>

            <!-- Live Speed Badge -->
            <div class="d-flex align-items-center gap-1.5 px-2.5 py-1.5 rounded-2" style="background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.08); font-size: 0.8rem;">
              <i class="bi bi-speedometer2 text-warning"></i>
              <span class="text-secondary">Speed:</span>
              <span id="live-wpm" class="text-warning fw-bold">0</span>
              <span style="font-size: 0.68rem; color: #64748b;">WPM</span>
            </div>

            <!-- Live Accuracy Badge -->
            <div class="d-flex align-items-center gap-1.5 px-2.5 py-1.5 rounded-2" style="background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.08); font-size: 0.8rem;">
              <i class="bi bi-bullseye text-success"></i>
              <span class="text-secondary">Acc:</span>
              <span id="live-accuracy" class="text-success fw-bold">100%</span>
            </div>

            <!-- Hidden metrics for calculation -->
            <div class="d-none" aria-hidden="true">
              <span id="live-cpm">0</span>
              <span id="live-chars-count">0</span>
              <span id="live-lines-count">1</span>
              <span id="live-net-wpm">0</span>
              <span id="live-errors-count">0</span>
            </div>
          </div>
        </div>

        <div class="progress-wrap">
          <div class="progress-bar-pixel" id="typing-progress-bar"></div>
        </div>

        <div class="position-relative">
          <div class="typing-surface" id="typing-box-wrapper" onclick="window.activateTypingFocus()" style="cursor: text;">
            <div id="typing-box" class="typing-box"></div>
          </div>

          <input type="text" id="hidden-typing-input" class="hidden-typing-input" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">

          <div id="typing-focus-prompt" class="focus-overlay" onclick="window.activateTypingFocus()" style="position:absolute;top:0;left:0;">
            <div class="focus-icon-box"><i class="bi bi-cursor-fill"></i></div>
            <div class="focus-title">Click anywhere here or start typing</div>
            <div class="focus-sub">Your live timer and word counter start on your first keypress</div>
          </div>

          <div id="typing-pause-overlay" class="pause-overlay">
            <div class="pause-icon"><i class="bi bi-pause-fill"></i></div>
            <h4>Practice Paused</h4>
            <p>Timer and metrics are frozen. Click Resume or press Space / Esc to continue.</p>
            <button type="button" class="px-btn px-btn-primary" id="btn-resume-overlay">
              <i class="bi bi-play-fill"></i> Resume (<kbd>Space</kbd> / <kbd>Esc</kbd>)
            </button>
          </div>
        </div>

        <div class="controls-bar">
          <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="px-btn" id="btn-pause-test">
              <i class="bi bi-pause-fill"></i>
              <span id="pause-btn-text">Pause (<kbd>Esc</kbd>)</span>
            </button>
            <button type="button" class="px-btn" id="btn-restart-test">
              <i class="bi bi-arrow-clockwise"></i>
              <span>Restart (<kbd>Tab+Enter</kbd>)</span>
            </button>
            <button type="button" class="px-btn" id="btn-next-passage">
              <i class="bi bi-shuffle"></i>
              <span>New Passage</span>
            </button>
            <button type="button" class="px-btn px-btn-danger" id="btn-toggle-backspace" title="Toggle strict mode">
              <i class="bi bi-slash-circle-fill" id="backspace-mode-icon"></i>
              <span id="backspace-mode-text">Backspace: <strong>Disabled</strong></span>
            </button>
            <button type="button" class="px-btn" id="btn-touch-backspace" disabled>
              <i class="bi bi-backspace-reverse-fill"></i>
              <span id="btn-touch-backspace-text">Backspace (Disabled)</span>
            </button>
          </div>
          <div class="ctrl-hint">
            <kbd>Esc</kbd> Pause · <kbd>Tab+Enter</kbd> Restart
          </div>
        </div>
      </div>

      <!-- FREE TYPING VIEW -->
      <div id="free-arena-view" class="<?= ($initialMode !== 'free') ? 'd-none' : '' ?>">
        <div class="zen-bar">
          <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="ribbon-pill active" style="cursor:default;">
              <i class="bi bi-pencil-fill"></i> Free Typing
            </span>
            <div class="d-flex align-items-center gap-1">
              <span class="ribbon-label"><i class="bi bi-stopwatch"></i> Mode:</span>
              <select id="free-timer-mode-select" class="free-mode-select">
                <option value="stopwatch">Stopwatch</option>
                <option value="30">30 Seconds</option>
                <option value="60">1 Minute</option>
                <option value="180">3 Minutes</option>
                <option value="360">6 Minutes</option>
                <option value="600">10 Minutes</option>
                <option value="1800">30 Minutes</option>
                <option value="custom">Custom Time...</option>
              </select>
            </div>
          </div>
          <div class="d-flex align-items-center gap-2 flex-wrap">
            <div class="zen-timer-badge">
              <i class="bi bi-stopwatch-fill"></i>
              <span class="zen-timer-val" id="live-free-timer">0:00</span>
            </div>
            <span class="strict-badge"><i class="bi bi-slash-circle-fill"></i> Strict Rules</span>
          </div>
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

        <div class="free-textarea-wrap">
          <textarea id="free-typing-textarea" class="free-textarea" placeholder="Start typing anything here... (CSS, ideas, drill text)

Tab key is enabled for code indentation. Your real-time words, characters, accuracy and WPM calculate silently in memory and unlock in the Showing Card when you finish." autofocus></textarea>

          <div class="free-stats">
            <div class="d-flex align-items-center gap-3 flex-wrap">
              <span><i class="bi bi-shield-lock me-1"></i>Distraction-free · telemetry unlocks on Finish</span>
              <span><kbd>Tab</kbd> = 4 spaces</span>
            </div>
            <div class="d-flex align-items-center gap-2">
              <button type="button" class="px-btn px-btn-sm" id="btn-insert-css-sample">
                <i class="bi bi-code"></i> Insert CSS Sample
              </button>
            </div>
          </div>
        </div>

        <div class="controls-bar">
          <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="px-btn px-btn-primary" id="btn-free-finish">
              <i class="bi bi-check-circle-fill"></i> Finish &amp; Save
            </button>
            <button type="button" class="px-btn px-btn-danger" id="btn-free-toggle-backspace">
              <i class="bi bi-slash-circle-fill" id="free-backspace-mode-icon"></i>
              <span id="free-backspace-mode-text">Backspace: <strong>Disabled</strong></span>
            </button>
            <button type="button" class="px-btn" id="btn-free-backspace-touch" disabled>
              <i class="bi bi-backspace-reverse-fill"></i>
              <span id="free-backspace-btn-text">Backspace (Disabled)</span>
            </button>
            <button type="button" class="px-btn" id="btn-free-clear">
              <i class="bi bi-trash3"></i> Clear
            </button>
            <button type="button" class="px-btn" id="btn-free-copy">
              <i class="bi bi-clipboard"></i> Copy
            </button>
            <button type="button" class="px-btn" id="btn-free-download">
              <i class="bi bi-download"></i> Download
            </button>
          </div>
          <button type="button" class="px-btn px-btn-sm" id="btn-free-to-passage">
            <i class="bi bi-arrow-repeat"></i> Use as Passage
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Custom Text Modal -->
<div class="modal fade pixel-modal" id="customTextModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="bi bi-pencil-square"></i> Practice with Custom Text / Code
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="form-text mb-3">
          Paste your custom text, code, or CSS. The engine calculates real-time word count and character-by-character accuracy automatically.
        </p>

        <div class="mb-3 d-flex align-items-center gap-2 flex-wrap">
          <span class="form-label" style="margin:0;">Quick Snippets:</span>
          <button type="button" class="px-btn px-btn-sm" id="btn-snippet-css"><i class="bi bi-filetype-css"></i> CSS :root</button>
          <button type="button" class="px-btn px-btn-sm" id="btn-snippet-js"><i class="bi bi-filetype-js"></i> JS Function</button>
        </div>

        <div class="mb-3">
          <label class="form-label">Passage Title (Optional)</label>
          <input type="text" id="custom-text-title" class="form-control" placeholder="e.g. CSS Variables Practice Drill">
        </div>

        <div class="mb-3">
          <label class="form-label">Text / Code Content</label>
          <textarea id="custom-text-content" rows="6" class="form-control" style="font-family:'JetBrains Mono',monospace;" placeholder="Paste your practice text or code here..."></textarea>
          <div class="form-text d-flex justify-content-between mt-2">
            <span id="custom-word-counter">0 words</span>
            <span>Accurate token count</span>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="px-btn" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="px-btn px-btn-primary" id="btn-apply-custom-text">
          <i class="bi bi-play-circle-fill"></i> Start Practice
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Showing Card Results Modal -->
<div class="modal fade showing-card-modal" id="resultsModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
    <div class="modal-content">

      <!-- Header -->
      <div class="sc-header">
        <div class="sc-title-wrap">
          <div class="sc-trophy-box"><i class="bi bi-trophy-fill"></i></div>
          <div>
            <h4 class="sc-headline">Typing Performance Showing Card</h4>
            <p class="sc-subline">Session complete · 7 core metrics &amp; trajectory diagnostics unlocked</p>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="sc-body">
        <!-- Greeting -->
        <div class="sc-greeting">
          <div class="sc-greeting-icon" id="res-greeting-icon">🎉</div>
          <div class="flex-grow-1">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
              <h5 class="sc-greeting-title" id="res-greeting-title">Outstanding Effort! Drill Completed</h5>
              <button type="button" class="px-btn px-btn-sm" id="btn-quick-cert-view">
                <i class="bi bi-award-fill"></i> View Certificate
              </button>
            </div>
            <p class="sc-greeting-sub" id="res-greeting-subtitle">
              <?= $currentUser ? 'Excellent job, ' . e($currentUser['username']) . '!' : 'Congratulations!' ?> Your telemetry was officially recorded.
            </p>
          </div>
        </div>

        <!-- Hero + chart -->
        <div class="row g-3 mb-3">
          <div class="col-lg-5">
            <div class="sc-hero-box">
              <span class="sc-hero-label">Final Performance</span>
              <div class="sc-hero-value" id="res-net-wpm">0</div>
              <div class="sc-hero-sublabel">Net Words Per Minute (WPM)</div>
              <div class="mt-2" id="res-rating-badge">
                <span class="sc-rating-badge"><i class="bi bi-star-fill"></i> Professional</span>
              </div>
              <div class="sc-strict-note" id="res-strict-mode-note">
                <i class="bi bi-slash-circle-fill" style="color:#dc2626;"></i> Strict Exam Rules: Backspace disabled
              </div>
            </div>
          </div>
          <div class="col-lg-7">
            <div class="sc-chart-box">
              <div class="sc-chart-head">
                <span class="sc-chart-title"><i class="bi bi-graph-up me-1"></i> Live Speed Trajectory (WPM/sec)</span>
                <span class="sc-chart-tag">Real-Time</span>
              </div>
              <div style="height:180px;position:relative;">
                <canvas id="sessionSpeedChart"></canvas>
              </div>
            </div>
          </div>
        </div>

        <!-- Stat cards -->
        <span class="sc-section-title">Core 7-Metric Telemetry Breakdown</span>
        <div class="row g-3 mb-3">
          <div class="col-6 col-md-3">
            <div class="sc-stat-card">
              <div class="sc-stat-icon"><i class="bi bi-stopwatch-fill"></i></div>
              <div class="sc-stat-label">1. Elapsed / Timer</div>
              <div class="sc-stat-value" id="res-elapsed-timer">0:00</div>
              <div class="sc-stat-sub" id="res-time-elapsed">0s duration</div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="sc-stat-card">
              <div class="sc-stat-icon"><i class="bi bi-fonts"></i></div>
              <div class="sc-stat-label">2. Real-Time Words</div>
              <div class="sc-stat-value" id="res-words-typed">0</div>
              <div class="sc-stat-sub">Completed words</div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="sc-stat-card">
              <div class="sc-stat-icon"><i class="bi bi-bullseye"></i></div>
              <div class="sc-stat-label">3. Live Accuracy</div>
              <div class="sc-stat-value" id="res-accuracy">100%</div>
              <div class="sc-stat-sub">Keystroke precision</div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="sc-stat-card">
              <div class="sc-stat-icon"><i class="bi bi-speedometer2"></i></div>
              <div class="sc-stat-label">4. Gross WPM</div>
              <div class="sc-stat-value" id="res-gross-wpm">0</div>
              <div class="sc-stat-sub">Raw typing speed</div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="sc-stat-card">
              <div class="sc-stat-icon"><i class="bi bi-lightning-charge-fill"></i></div>
              <div class="sc-stat-label">5. CPM (Speed)</div>
              <div class="sc-stat-value" id="res-cpm">0</div>
              <div class="sc-stat-sub">Chars per minute</div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="sc-stat-card">
              <div class="sc-stat-icon"><i class="bi bi-keyboard-fill"></i></div>
              <div class="sc-stat-label">6. Characters</div>
              <div class="sc-stat-value" id="res-chars-count">0</div>
              <div class="sc-stat-sub">Total characters</div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="sc-stat-card">
              <div class="sc-stat-icon"><i class="bi bi-text-paragraph"></i></div>
              <div class="sc-stat-label">7. Lines</div>
              <div class="sc-stat-value" id="res-lines-count">1</div>
              <div class="sc-stat-sub">Paragraph structure</div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="sc-stat-card">
              <div class="sc-stat-icon"><i class="bi bi-shield-check"></i></div>
              <div class="sc-stat-label">Keystroke Quality</div>
              <div class="sc-stat-value" id="res-quality-score">100%</div>
              <div class="sc-stat-sub">Error-free execution</div>
            </div>
          </div>
        </div>

        <!-- Diagnostics -->
        <div class="sc-diagnostics">
          <div class="sc-diag-title"><i class="bi bi-cpu-fill"></i> Keystroke Diagnostics</div>
          <div class="sc-diag-list">
            <span>Total: <strong id="res-total-keys">0</strong></span>
            <span>Correct: <strong id="res-correct-keys">0</strong></span>
            <span>Errors: <strong id="res-error-keys">0</strong></span>
            <span>Backspaces: <strong id="res-backspaces">0</strong></span>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="sc-footer">
        <div class="sc-footer-note">
          <?php if (!$currentUser): ?>
            <i class="bi bi-info-circle me-1"></i>
            <a href="<?= BASE_URL ?>/register.php" style="color:inherit;font-weight:700;text-decoration:underline;">Register</a> to save results permanently.
          <?php else: ?>
            <i class="bi bi-check-circle-fill me-1"></i> Session recorded to your personal history.
          <?php endif; ?>
        </div>
        <div class="sc-footer-actions">
          <button type="button" class="px-btn" id="btn-generate-cert">
            <i class="bi bi-award-fill"></i> Generate Certificate
          </button>
          <button type="button" class="px-btn" id="btn-res-retry">
            <i class="bi bi-arrow-clockwise"></i> Practice Again
          </button>
          <button type="button" class="px-btn px-btn-primary" id="btn-res-next">
            <i class="bi bi-arrow-right-circle"></i> Continue
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Custom Duration Modal -->
<div class="modal fade pixel-modal" id="customDurationModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-clock-history"></i> Set Custom Practice Time</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="form-text mb-3">Configure a countdown clock or free watch timer.</p>

        <div class="mb-3">
          <label class="form-label">Quick Presets</label>
          <div class="d-flex flex-wrap gap-2">
            <button type="button" class="px-btn px-btn-sm quick-duration-btn" data-sec="30">30s</button>
            <button type="button" class="px-btn px-btn-sm quick-duration-btn" data-sec="60">1 Min</button>
            <button type="button" class="px-btn px-btn-sm quick-duration-btn" data-sec="180">3 Min</button>
            <button type="button" class="px-btn px-btn-sm quick-duration-btn" data-sec="360">6 Min</button>
            <button type="button" class="px-btn px-btn-sm quick-duration-btn" data-sec="600">10 Min</button>
            <button type="button" class="px-btn px-btn-sm quick-duration-btn" data-sec="1800">30 Min</button>
          </div>
        </div>

        <div class="row g-2 mb-3">
          <div class="col-8">
            <label class="form-label">Custom Time Value</label>
            <input type="number" id="custom-time-val" class="form-control" min="5" max="7200" value="90">
          </div>
          <div class="col-4">
            <label class="form-label">Unit</label>
            <select id="custom-time-unit" class="form-select">
              <option value="sec" selected>Seconds</option>
              <option value="min">Minutes</option>
            </select>
          </div>
        </div>

        <div class="mb-2">
          <label class="form-label">Apply To</label>
          <div class="d-flex gap-3">
            <div class="form-check">
              <input class="form-check-input" type="radio" name="customTimeTarget" id="targetPassage" value="passage" checked>
              <label class="form-check-label" for="targetPassage">Passage Drill</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="customTimeTarget" id="targetFree" value="free">
              <label class="form-check-label" for="targetFree">Free Typing</label>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="px-btn" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="px-btn px-btn-primary" id="btn-apply-custom-duration">
          <i class="bi bi-check-circle-fill"></i> Apply &amp; Start
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Certificate Modal -->
<div class="modal fade certificate-modal" id="certificateModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-award-fill"></i> Official Typing Proficiency Certificate</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <div class="cert-frame" id="printable-certificate">
          <div class="cert-corner cert-corner-tl"></div>
          <div class="cert-corner cert-corner-tr"></div>
          <div class="cert-corner cert-corner-bl"></div>
          <div class="cert-corner cert-corner-br"></div>

          <div class="cert-emblem"><i class="bi bi-award"></i></div>
          <div class="cert-board">Type Master Accreditation Board</div>
          <h2 class="cert-title">Certificate of Typing Proficiency</h2>
          <div class="cert-subtitle">Standardized Examination &amp; Keying Velocity Verification</div>

          <p class="cert-certifies">This official credential certifies that</p>
          <div class="cert-candidate" id="certCandidateName">
            <?= e($currentUser['username'] ?? 'Candidate Typist') ?>
          </div>
          <p class="cert-description">
            has demonstrated typing velocity, accuracy, and keystroke integrity on standardized evaluation passages with the following verified metrics:
          </p>

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

          <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mt-4 pt-3 border-top" style="border-color: currentColor !important;">
            <div class="text-start">
              <div class="cert-seal"><i class="bi bi-shield-fill-check"></i> VERIFIED RECORD</div>
              <div class="cert-meta-row">
                Credential ID: <strong id="certHash">TM-<?= strtoupper(substr(bin2hex(random_bytes(5)), 0, 10)) ?></strong>
              </div>
              <div class="cert-meta-row">
                Issue Date: <strong id="certDate"><?= date('F j, Y') ?></strong>
              </div>
            </div>

            <div class="text-end">
              <div class="cert-signature">Automated Evaluation Engine</div>
              <div class="cert-meta-row" style="margin-top:0.3rem;">Type Master Verification Authority</div>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <span class="cert-meta-row"><i class="bi bi-info-circle me-1"></i>Print or save this certificate as PDF.</span>
        <div class="d-flex gap-2 flex-wrap">
          <button type="button" class="px-btn" data-bs-dismiss="modal">Close</button>
          <button type="button" class="px-btn px-btn-primary" onclick="window.print()">
            <i class="bi bi-printer-fill"></i> Print / PDF
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

  let isBackspaceDisabled = false;

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
    onFinish: (result) => { displayResultsModal(result); }
  });

  engine.loadText(initialPassage.content);

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
    onFinish: (result) => { displayResultsModal(result); }
  });

  window.activateTypingFocus = function() {
    const prompt = document.getElementById('typing-focus-prompt');
    if (prompt) {
      prompt.classList.add('hidden');
      prompt.style.display = 'none';
      prompt.style.pointerEvents = 'none';
    }
    if (engine && engine.input) {
      engine.input.focus();
    }
  };

  const promptEl = document.getElementById('typing-focus-prompt');
  if (promptEl) {
    promptEl.addEventListener('click', window.activateTypingFocus);
  }
  const surfaceEl = document.getElementById('typing-box-wrapper');
  if (surfaceEl) {
    surfaceEl.addEventListener('click', window.activateTypingFocus);
  }

  // Global keydown: starts live timer and word counter on first keystroke from anywhere!
  window.addEventListener('keydown', (e) => {
    if (['TEXTAREA', 'SELECT'].includes(document.activeElement.tagName)) {
      if (document.activeElement.id !== 'free-typing-textarea') return;
    }
    if (document.activeElement.tagName === 'INPUT' && document.activeElement.id !== 'hidden-typing-input') {
      return;
    }
    if (document.querySelector('.modal.show')) return;

    if (e.key === ' ' && document.activeElement.id !== 'free-typing-textarea') {
      e.preventDefault();
    }

    window.activateTypingFocus();
  });

  setTimeout(() => {
    const skel = document.getElementById('practice-skeleton-view');
    const content = document.getElementById('practice-content-view');
    if (skel) skel.style.display = 'none';
    if (content) {
      content.style.display = 'block';
      window.activateTypingFocus();
    }
  }, 220);

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

  document.getElementById('free-timer-mode-select').addEventListener('change', (e) => {
    const val = e.target.value;
    if (val === 'custom') {
      const targetFree = document.getElementById('targetFree');
      if (targetFree) targetFree.checked = true;
      new bootstrap.Modal(document.getElementById('customDurationModal')).show();
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

  document.getElementById('btn-free-finish').addEventListener('click', () => freeEngine.finish());

  document.getElementById('btn-free-clear').addEventListener('click', () => {
    if (confirm('Clear the canvas and reset the word counter?')) freeEngine.clear();
  });

  document.getElementById('btn-free-copy').addEventListener('click', () => {
    const text = document.getElementById('free-typing-textarea').value;
    navigator.clipboard.writeText(text).then(() => showToast('success', 'Text copied!'));
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

  document.getElementById('btn-insert-css-sample').addEventListener('click', () => {
    const textarea = document.getElementById('free-typing-textarea');
    textarea.value = cssSampleSnippet;
    freeEngine.handleInput();
    textarea.focus();
    showToast('info', 'CSS snippet loaded!');
  });

  document.getElementById('btn-free-to-passage').addEventListener('click', () => {
    const text = document.getElementById('free-typing-textarea').value.trim();
    if (text.length < 10) { alert('Please write at least 10 characters.'); return; }
    const words = text.split(/\s+/).filter(w => w.length > 0);
    initialPassage = {
      id: null, title: 'Converted Free Writing Drill', content: text,
      difficulty: 'custom', word_count: words.length, char_count: text.length
    };
    document.getElementById('passage-title-display').textContent = initialPassage.title;
    document.getElementById('passage-word-count-badge').textContent = words.length;
    engine.options.passageId = null;
    engine.options.difficulty = 'custom';
    engine.loadText(text);
    btnModePassage.click();
    showToast('success', 'Converted to Passage Drill!');
  });

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
      if (toggleText) toggleText.innerHTML = 'Backspace: <strong>Disabled</strong>';
      if (touchBtn) { touchBtn.disabled = true; }
      if (touchBtnText) touchBtnText.textContent = 'Backspace (Disabled)';
      if (freeToggleText) freeToggleText.innerHTML = 'Backspace: <strong>Disabled</strong>';
      if (freeTouchBtn) { freeTouchBtn.disabled = true; }
      if (freeTouchBtnText) freeTouchBtnText.textContent = 'Backspace (Disabled)';
    } else {
      if (toggleText) toggleText.innerHTML = 'Backspace: <strong>Enabled</strong>';
      if (touchBtn) { touchBtn.disabled = false; }
      if (touchBtnText) touchBtnText.textContent = 'Backspace';
      if (freeToggleText) freeToggleText.innerHTML = 'Backspace: <strong>Enabled</strong>';
      if (freeTouchBtn) { freeTouchBtn.disabled = false; }
      if (freeTouchBtnText) freeTouchBtnText.textContent = 'Backspace';
    }
  }

  setBackspaceMode(false);

  const toggleBackspaceBtn = document.getElementById('btn-toggle-backspace');
  if (toggleBackspaceBtn) {
    toggleBackspaceBtn.addEventListener('click', () => {
      setBackspaceMode(!isBackspaceDisabled);
      showToast(isBackspaceDisabled ? 'warning' : 'info', isBackspaceDisabled ? 'Strict Mode ON' : 'Standard Mode ON');
    });
  }

  const freeToggleBackspaceBtn = document.getElementById('btn-free-toggle-backspace');
  if (freeToggleBackspaceBtn) {
    freeToggleBackspaceBtn.addEventListener('click', () => {
      setBackspaceMode(!isBackspaceDisabled);
      showToast(isBackspaceDisabled ? 'warning' : 'info', isBackspaceDisabled ? 'Strict Mode ON' : 'Standard Mode ON');
    });
  }

  const touchBackspaceBtn = document.getElementById('btn-touch-backspace');
  if (touchBackspaceBtn) touchBackspaceBtn.addEventListener('click', (e) => { e.preventDefault(); engine.manualBackspace(); });

  const freeTouchBackspaceBtn = document.getElementById('btn-free-backspace-touch');
  if (freeTouchBackspaceBtn) freeTouchBackspaceBtn.addEventListener('click', (e) => { e.preventDefault(); freeEngine.manualBackspace(); });

  const btnPauseTest = document.getElementById('btn-pause-test');
  if (btnPauseTest) btnPauseTest.addEventListener('click', () => engine.togglePause());

  const btnResumeOverlay = document.getElementById('btn-resume-overlay');
  if (btnResumeOverlay) btnResumeOverlay.addEventListener('click', () => engine.resumeTest());

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
      if (timeEl) timeEl.textContent = `(${lastResult.elapsed_seconds || 0}s)`;

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

  let tabPressed = false;
  window.addEventListener('keydown', (e) => {
    if (e.key === 'Tab') { tabPressed = true; setTimeout(() => { tabPressed = false; }, 800); }
    else if (e.key === 'Enter' && tabPressed) {
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

  document.getElementById('btn-restart-test').addEventListener('click', () => engine.loadText(initialPassage.content));
  document.getElementById('btn-next-passage').addEventListener('click', () => loadNewRandomPassage(currentDifficulty));

  const soundBtn = document.getElementById('btn-sound-toggle');
  const soundIcon = document.getElementById('sound-icon');
  soundBtn.addEventListener('click', () => {
    isSoundEnabled = !isSoundEnabled;
    engine.options.soundEnabled = isSoundEnabled;
    freeEngine.options.soundEnabled = isSoundEnabled;
    soundIcon.className = isSoundEnabled ? 'bi bi-volume-up-fill' : 'bi bi-volume-mute-fill';
    showToast('info', isSoundEnabled ? 'Sound ON' : 'Sound muted');
  });

  document.querySelectorAll('.duration-selector-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.duration-selector-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      currentDuration = parseInt(btn.dataset.duration);
      engine.options.duration = currentDuration;
      engine.loadText(initialPassage.content);
    });
  });

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
    document.getElementById('custom-text-title').value = 'Fibonacci';
    customTextArea.value = `function fibonacci(n) {\n    if (n <= 1) return n;\n    return fibonacci(n - 1) + fibonacci(n - 2);\n}`;
    updateCustomCounter();
  });

  document.getElementById('btn-apply-custom-text').addEventListener('click', () => {
    const customContent = customTextArea.value.trim();
    const customTitle = document.getElementById('custom-text-title').value.trim() || 'Custom Text Drill';
    if (customContent.length < 5) { alert('Please enter some text.'); return; }
    const words = customContent.split(/\s+/).filter(w => w.length > 0);
    initialPassage = { id: null, title: customTitle, content: customContent, difficulty: 'custom', word_count: words.length, char_count: customContent.length };
    document.getElementById('passage-title-display').textContent = customTitle;
    document.getElementById('passage-word-count-badge').textContent = words.length;
    engine.options.passageId = null;
    engine.options.difficulty = 'custom';
    engine.loadText(customContent);
    bootstrap.Modal.getInstance(document.getElementById('customTextModal')).hide();
    btnModePassage.click();
    showToast('success', 'Custom drill loaded!');
  });

  document.getElementById('btn-res-retry').addEventListener('click', () => {
    resultsModal.hide();
    if (!passageView.classList.contains('d-none')) engine.loadText(initialPassage.content);
    else freeEngine.clear();
  });

  document.getElementById('btn-res-next').addEventListener('click', () => {
    resultsModal.hide();
    if (!passageView.classList.contains('d-none')) loadNewRandomPassage(currentDifficulty);
    else freeEngine.clear();
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
      .catch(() => engine.loadText(initialPassage.content));
  }

  let latestResultData = null;

  function displayResultsModal(res) {
    latestResultData = res;
    const netWpm = res.net_wpm ?? 0;
    const elNetWpm = document.getElementById('res-net-wpm');
    if (elNetWpm) elNetWpm.textContent = netWpm;

    const greetIcon = document.getElementById('res-greeting-icon');
    const greetTitle = document.getElementById('res-greeting-title');
    const greetSub = document.getElementById('res-greeting-subtitle');
    if (greetIcon && greetTitle && greetSub) {
      if (netWpm >= 80) {
        greetIcon.textContent = '🌟';
        greetTitle.textContent = 'Godspeed Typist!';
        greetSub.textContent = `At ${netWpm} Net WPM you are in the top 1% globally.`;
      } else if (netWpm >= 60) {
        greetIcon.textContent = '🚀';
        greetTitle.textContent = 'Masterful Run!';
        greetSub.textContent = `You clocked ${netWpm} Net WPM with stellar consistency.`;
      } else if (netWpm >= 40) {
        greetIcon.textContent = '⚡';
        greetTitle.textContent = 'Great Job! Solid Rhythm!';
        greetSub.textContent = `${netWpm} Net WPM demonstrates solid control.`;
      } else {
        greetIcon.textContent = '🎯';
        greetTitle.textContent = 'Practice Complete!';
        greetSub.textContent = `Completed at ${netWpm} Net WPM. Consistent training escalates speed!`;
      }
    }

    const ratingBadge = document.getElementById('res-rating-badge');
    if (ratingBadge) {
      const rating = getSpeedRating(netWpm);
      ratingBadge.innerHTML = `<span class="sc-rating-badge"><i class="bi ${rating.icon}"></i> ${rating.label}</span>`;
    }

    const strictNote = document.getElementById('res-strict-mode-note');
    if (strictNote) {
      if (res.backspace_disabled || isBackspaceDisabled) {
        strictNote.innerHTML = '<i class="bi bi-slash-circle-fill" style="color:#dc2626;"></i> Strict Exam Rules: Backspace disabled';
      } else {
        strictNote.innerHTML = '<i class="bi bi-check-circle-fill"></i> Standard Mode: Backspace enabled';
      }
    }

    const elapsedSec = Math.round(res.elapsed_seconds || 0);
    const minutes = Math.floor(elapsedSec / 60);
    const seconds = elapsedSec % 60;
    const timerStr = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;

    document.getElementById('res-elapsed-timer').textContent = timerStr;
    document.getElementById('res-time-elapsed').textContent = `${res.elapsed_seconds || 0}s duration`;
    document.getElementById('res-words-typed').textContent = res.words_typed ?? 0;
    document.getElementById('res-accuracy').textContent = `${res.accuracy ?? 100}%`;
    document.getElementById('res-gross-wpm').textContent = res.gross_wpm ?? 0;

    const totalChars = res.total_characters ?? res.total_keystrokes ?? ((res.words_typed ?? 0) * 5);
    const cpmVal = res.cpm || Math.round(totalChars / Math.max(0.1, (res.elapsed_seconds || 1) / 60));
    document.getElementById('res-cpm').textContent = cpmVal;
    document.getElementById('res-chars-count').textContent = totalChars;
    document.getElementById('res-lines-count').textContent = res.total_lines || Math.max(1, Math.ceil((res.words_typed || 1) / 12));

    const tot = res.total_keystrokes || totalChars || 1;
    const cor = res.correct_keystrokes ?? tot;
    const qScore = Math.min(100, Math.max(0, Math.round((cor / Math.max(1, tot)) * 100)));
    document.getElementById('res-quality-score').textContent = `${qScore}%`;

    document.getElementById('res-total-keys').textContent = res.total_keystrokes ?? 0;
    document.getElementById('res-correct-keys').textContent = res.correct_keystrokes ?? 0;
    document.getElementById('res-error-keys').textContent = res.error_keystrokes ?? 0;
    document.getElementById('res-backspaces').textContent = res.backspace_count ?? 0;

    renderSessionChart(res.speed_history || []);
    resultsModal.show();
  }

  function openCertificateModal() {
    const res = latestResultData || {
      net_wpm: document.getElementById('res-net-wpm')?.textContent || 0,
      gross_wpm: document.getElementById('res-gross-wpm')?.textContent || 0,
      accuracy: (document.getElementById('res-accuracy')?.textContent || '100%').replace('%', ''),
      elapsed_seconds: currentDuration || 60
    };
    document.getElementById('certNetWpm').textContent = res.net_wpm || 0;
    document.getElementById('certGrossWpm').textContent = res.gross_wpm || res.net_wpm || 0;
    document.getElementById('certAccuracy').textContent = `${res.accuracy || 100}%`;
    const durSec = Math.round(res.elapsed_seconds || currentDuration || 60);
    document.getElementById('certDuration').textContent = durSec < 60 ? `${durSec}s` : `${Math.round(durSec / 60)} min`;
    new bootstrap.Modal(document.getElementById('certificateModal')).show();
  }

  const btnGenCert = document.getElementById('btn-generate-cert');
  if (btnGenCert) btnGenCert.addEventListener('click', openCertificateModal);
  const btnQuickCert = document.getElementById('btn-quick-cert-view');
  if (btnQuickCert) btnQuickCert.addEventListener('click', openCertificateModal);

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
    });
  });

  const btnApplyCustomDuration = document.getElementById('btn-apply-custom-duration');
  if (btnApplyCustomDuration) {
    btnApplyCustomDuration.addEventListener('click', () => {
      const val = parseInt(document.getElementById('custom-time-val').value) || 60;
      const unit = document.getElementById('custom-time-unit').value;
      const totalSeconds = unit === 'min' ? val * 60 : val;
      const targetMode = document.querySelector('input[name="customTimeTarget"]:checked')?.value || 'passage';

      if (totalSeconds < 5) { alert('Please enter at least 5 seconds.'); return; }

      if (targetMode === 'passage') {
        currentDuration = totalSeconds;
        engine.options.duration = totalSeconds;
        document.getElementById('live-timer').textContent = `${totalSeconds}s`;
        engine.loadText(initialPassage.content);
        btnModePassage.click();
      } else {
        freeEngine.options.mode = 'timed';
        freeEngine.options.duration = totalSeconds;
        const liveFreeTimer = document.getElementById('live-free-timer');
        const m = Math.floor(totalSeconds / 60);
        const s = totalSeconds % 60;
        liveFreeTimer.textContent = `${m}:${s < 10 ? '0' : ''}${s}`;
        btnModeFree.click();
        freeEngine.clear();
      }

      bootstrap.Modal.getInstance(document.getElementById('customDurationModal'))?.hide();
      showToast('success', `Custom time: ${totalSeconds}s for ${targetMode === 'passage' ? 'Passage' : 'Free'}!`);
    });
  }

  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.get('mode') === 'custom') {
    new bootstrap.Modal(document.getElementById('customDurationModal')).show();
  }

  function getSpeedRating(wpm) {
    if (wpm >= 100) return { label: 'Typing God', icon: 'bi-trophy-fill' };
    if (wpm >= 80) return { label: 'Grandmaster', icon: 'bi-award-fill' };
    if (wpm >= 60) return { label: 'Professional', icon: 'bi-star-fill' };
    if (wpm >= 40) return { label: 'Intermediate', icon: 'bi-check-circle-fill' };
    if (wpm >= 25) return { label: 'Developing', icon: 'bi-graph-up-arrow' };
    return { label: 'Beginner', icon: 'bi-seedling' };
  }

  function renderSessionChart(history) {
    const ctx = document.getElementById('sessionSpeedChart');
    if (!ctx) return;
    if (sessionChartInstance) sessionChartInstance.destroy();

    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const lineColor = isDark ? '#ffffff' : '#000000';
    const gridColor = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.08)';
    const tickColor = isDark ? '#999999' : '#666666';

    const labels = (history && history.length > 0) ? history.map(h => `${h.second}s`) : ['0s', '1s'];
    const dataPoints = (history && history.length > 0) ? history.map(h => h.wpm) : [0, 0];

    sessionChartInstance = new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'WPM',
          data: dataPoints,
          borderColor: lineColor,
          backgroundColor: isDark ? 'rgba(255,255,255,0.12)' : 'rgba(0,0,0,0.08)',
          fill: true,
          tension: 0.35,
          borderWidth: 3,
          pointRadius: 2.5,
          pointBackgroundColor: lineColor,
          pointHoverRadius: 5
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: isDark ? '#000000' : '#ffffff',
            borderColor: lineColor,
            borderWidth: 2,
            titleColor: isDark ? '#ffffff' : '#000000',
            bodyColor: isDark ? '#ffffff' : '#000000',
            displayColors: false,
            padding: 10,
            titleFont: { family: 'Inter', weight: '700', size: 12 },
            bodyFont: { family: 'JetBrains Mono', size: 12 }
          }
        },
        scales: {
          x: {
            grid: { color: gridColor },
            ticks: { color: tickColor, font: { size: 10, family: 'Inter' } }
          },
          y: {
            beginAtZero: true,
            grid: { color: gridColor },
            ticks: { color: tickColor, font: { size: 10, family: 'JetBrains Mono' } }
          }
        }
      }
    });
  }
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>