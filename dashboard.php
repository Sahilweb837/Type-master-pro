<?php
/**
 * TypoPrac — Professional Typing Practice Software
 * User Dashboard — Recreated to match the exact TypoPrac design mockup
 */

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/passages_data.php';

require_login();

$user = current_user();
$allPassagesList = get_all_passages();
$userId = $user['id'];
$pdo = getDBConnection();

$totalTests = 0; $avgWpm = 0.0; $bestWpm = 0.0; $avgAccuracy = 0.0;
$totalSeconds = 0; $totalWords = 0; $recentTests = []; $weeklyChartData = [];

if ($pdo) {
    try {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) AS total_tests,
                COALESCE(AVG(net_wpm), 0) AS avg_wpm,
                COALESCE(MAX(net_wpm), 0) AS best_wpm,
                COALESCE(AVG(accuracy), 0) AS avg_accuracy,
                COALESCE(SUM(elapsed_seconds), 0) AS total_seconds,
                COALESCE(SUM(words_typed), 0) AS total_words
            FROM typing_results WHERE user_id = :uid
        ");
        $stmt->execute([':uid' => $userId]);
        $stats = $stmt->fetch();
        if ($stats) {
            $totalTests   = (int)$stats['total_tests'];
            $avgWpm       = round((float)$stats['avg_wpm'], 1);
            $bestWpm      = round((float)$stats['best_wpm'], 1);
            $avgAccuracy  = round((float)$stats['avg_accuracy'], 1);
            $totalSeconds = (int)$stats['total_seconds'];
            $totalWords   = (int)$stats['total_words'];
        }

        $recentStmt = $pdo->prepare("
            SELECT r.*, p.title AS passage_title
            FROM typing_results r
            LEFT JOIN typing_passages p ON r.passage_id = p.id
            WHERE r.user_id = :uid
            ORDER BY r.created_at DESC LIMIT 6
        ");
        $recentStmt->execute([':uid' => $userId]);
        $recentTests = $recentStmt->fetchAll();

        $chartStmt = $pdo->prepare("
            SELECT DATE_FORMAT(created_at, '%b %d') AS test_day, net_wpm, accuracy
            FROM typing_results WHERE user_id = :uid
            ORDER BY created_at ASC LIMIT 14
        ");
        $chartStmt->execute([':uid' => $userId]);
        $weeklyChartData = $chartStmt->fetchAll();
    } catch (Exception $e) {
        error_log('Dashboard data fetch error: ' . $e->getMessage());
    }
}

$userLevel = min(50, max(1, (int)floor($bestWpm / 15) + (int)floor($totalTests / 4) + 1));
$displayWpm = $bestWpm > 0 ? (int)$bestWpm : 52;
$displayAcc = $avgAccuracy > 0 ? (int)$avgAccuracy : 98;
$currentXp = max(350, $totalWords > 0 ? $totalWords : 350);
$nextLevelXp = max(1000, $currentXp + 650);
$xpPercent = min(100, max(15, round(($currentXp / $nextLevelXp) * 100)));

$pageTitle = 'Dashboard';
include __DIR__ . '/includes/header.php';
?>

<style>
/* =====================================================================
   TypoPrac Dashboard Theme — Exact Mockup Match
   ===================================================================== */
:root {
  --tp-bg-dark: #0b0f17;
  --tp-card-bg: #131a29;
  --tp-card-hover: #192338;
  --tp-border: rgba(255, 255, 255, 0.08);
  --tp-orange: #ff5722;
  --tp-orange-glow: rgba(255, 87, 34, 0.35);
  --tp-green: #00e676;
  --tp-text-dim: #94a3b8;
}

.typoprac-dash-wrap {
  max-width: 1440px;
  margin: 0 auto;
  padding: 0.5rem 0 3rem;
  font-family: 'Inter', -apple-system, sans-serif;
  color: #ffffff;
}

/* Hero Panoramic Banner */
.dash-hero-banner {
  position: relative;
  border-radius: 18px;
  overflow: hidden;
  background: url('<?= BASE_URL ?>/assets/images/mountain-banner.jpg') center 35% / cover no-repeat;
  border: 1px solid rgba(255, 255, 255, 0.1);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
  min-height: 220px;
}
.dash-hero-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(90deg, rgba(11, 15, 23, 0.95) 0%, rgba(11, 15, 23, 0.8) 50%, rgba(11, 15, 23, 0.35) 100%);
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 2.2rem 2.5rem;
}
@media (max-width: 991px) {
  .dash-hero-overlay {
    flex-direction: column;
    align-items: flex-start;
    gap: 1.5rem;
    padding: 1.5rem;
    background: linear-gradient(180deg, rgba(11, 15, 23, 0.9) 0%, rgba(11, 15, 23, 0.98) 100%);
  }
}

/* XP Level Card on Hero */
.dash-level-card {
  background: rgba(19, 26, 42, 0.85);
  backdrop-filter: blur(14px);
  border: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: 14px;
  padding: 1.1rem 1.4rem;
  min-width: 270px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
}
.dash-level-bar {
  height: 6px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 99px;
  overflow: hidden;
  margin: 0.6rem 0 0.4rem;
}
.dash-level-bar-fill {
  height: 100%;
  background: linear-gradient(90deg, #ff5500, #ff8a00);
  border-radius: 99px;
  box-shadow: 0 0 10px rgba(255, 87, 34, 0.8);
}

/* 4 Mode Selector Cards */
.mode-row {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1rem;
}
@media (max-width: 991px) {
  .mode-row { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 575px) {
  .mode-row { grid-template-columns: 1fr; }
}

.mode-card {
  background: var(--tp-card-bg);
  border: 1px solid var(--tp-border);
  border-radius: 14px;
  padding: 1rem 1.25rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  text-decoration: none !important;
  color: #ffffff;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
}
.mode-card:hover {
  transform: translateY(-2px);
  border-color: rgba(255, 87, 34, 0.4);
  box-shadow: 0 6px 20px rgba(255, 87, 34, 0.15);
  color: #ffffff;
}
.mode-card.active {
  border-color: #ff5722 !important;
  box-shadow: 0 0 22px rgba(255, 87, 34, 0.28) !important;
  background: rgba(255, 87, 34, 0.05);
}
.mode-card-icon {
  width: 44px;
  height: 44px;
  border-radius: 10px;
  background: rgba(255, 87, 34, 0.12);
  color: #ff5722;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.3rem;
  flex-shrink: 0;
}

/* Practice Live Container */
.dash-practice-box {
  background: var(--tp-card-bg);
  border: 1px solid var(--tp-border);
  border-radius: 16px;
  padding: 1.5rem;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.35);
  position: relative;
}
.dash-practice-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid var(--tp-border);
  margin-bottom: 1.25rem;
}
.practice-select-pill {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid var(--tp-border);
  border-radius: 8px;
  color: #ffffff;
  padding: 0.4rem 0.85rem;
  font-size: 0.84rem;
  font-weight: 600;
  cursor: pointer;
  outline: none;
}
.practice-select-pill option {
  background: #111726;
  color: #ffffff;
}
.timer-pill {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid var(--tp-border);
  border-radius: 8px;
  padding: 0.4rem 0.9rem;
  font-family: 'JetBrains Mono', monospace;
  font-size: 0.95rem;
  font-weight: 700;
  color: #ffffff;
}

/* Interactive Text Display */
.dash-typing-text {
  font-family: 'JetBrains Mono', monospace;
  font-size: 1.25rem;
  line-height: 1.85;
  color: #94a3b8;
  letter-spacing: 0.01em;
  margin-bottom: 1.5rem;
  min-height: 110px;
  outline: none;
  position: relative;
  cursor: text;
}
.dash-char.correct {
  color: #ffffff;
}
.dash-char.incorrect {
  color: #ff5252;
  text-decoration: underline;
  background: rgba(255, 82, 82, 0.2);
}
.dash-caret {
  display: inline-block;
  width: 2px;
  height: 1.25rem;
  background: #ff5722;
  margin-left: 1px;
  vertical-align: text-bottom;
  animation: tpCaretBlink 1s infinite;
  box-shadow: 0 0 8px rgba(255, 87, 34, 0.9);
}
@keyframes tpCaretBlink {
  0%, 49% { opacity: 1; }
  50%, 100% { opacity: 0; }
}

/* Virtual Backlit Keyboard & Posture */
.dash-keyboard-container {
  background: #0d121c;
  border: 1px solid rgba(255, 255, 255, 0.06);
  border-radius: 14px;
  padding: 1.2rem;
  position: relative;
  overflow: hidden;
}
.kb-row {
  display: flex;
  justify-content: center;
  gap: 6px;
  margin-bottom: 6px;
}
.kb-key {
  min-width: 44px;
  height: 42px;
  border-radius: 7px;
  background: #172030;
  border: 1px solid rgba(255, 255, 255, 0.06);
  color: #cbd5e1;
  font-family: 'JetBrains Mono', monospace;
  font-size: 0.85rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.4);
  transition: all 0.12s ease;
  user-select: none;
  padding: 0 0.5rem;
}
.kb-key.home-marker {
  background: rgba(255, 87, 34, 0.18);
  border-color: #ff5722;
  color: #ff5722;
  box-shadow: 0 0 12px rgba(255, 87, 34, 0.4);
}
.kb-key.pressed {
  background: #ff5722 !important;
  color: #ffffff !important;
  box-shadow: 0 0 16px rgba(255, 87, 34, 0.85) !important;
  transform: translateY(2px);
}

/* Hands Posture Guide */
.hands-guide-visual {
  position: absolute;
  bottom: -5px;
  left: 50%;
  transform: translateX(-50%);
  pointer-events: none;
  opacity: 0.4;
  transition: opacity 0.3s ease;
  width: 580px;
  max-width: 90%;
}
.dash-keyboard-container:hover .hands-guide-visual {
  opacity: 0.65;
}

/* Keyboard Color Legend */
.kb-legend {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1.5rem;
  margin-top: 1rem;
  font-size: 0.78rem;
  color: var(--tp-text-dim);
}
.kb-dot {
  width: 9px;
  height: 9px;
  border-radius: 50%;
  display: inline-block;
}

/* Right Column Widgets */
.stat-mini-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 0.75rem;
  margin-bottom: 1.25rem;
}
.stat-mini-card {
  background: var(--tp-card-bg);
  border: 1px solid var(--tp-border);
  border-radius: 12px;
  padding: 0.9rem 0.75rem;
  text-align: center;
}
.stat-mini-val {
  font-family: 'JetBrains Mono', monospace;
  font-size: 1.6rem;
  font-weight: 800;
  line-height: 1.1;
  color: #ffffff;
}
.delta-badge {
  font-size: 0.68rem;
  font-weight: 700;
  color: #00e676;
  display: inline-flex;
  align-items: center;
  gap: 2px;
  margin-top: 2px;
}

/* Live Progress Bar Chart */
.live-bars-wrap {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 8px;
  height: 110px;
  padding-top: 1rem;
}
.live-bar-col {
  flex: 1;
  background: linear-gradient(180deg, #ff7a00 0%, #ff5500 100%);
  border-radius: 4px 4px 0 0;
  transition: height 0.5s ease;
  min-height: 15px;
  box-shadow: 0 0 8px rgba(255, 87, 34, 0.4);
}

/* Typing Tips Checkmarks */
.tip-item {
  display: flex;
  align-items: flex-start;
  gap: 0.65rem;
  font-size: 0.84rem;
  color: #cbd5e1;
  margin-bottom: 0.65rem;
  line-height: 1.4;
}
.tip-item i {
  color: #00e676;
  font-size: 0.95rem;
  margin-top: 1px;
}

/* Today's Practice Donut */
.donut-center-val {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  text-align: center;
  font-family: 'JetBrains Mono', monospace;
  font-size: 1.2rem;
  font-weight: 800;
  color: #ffffff;
  line-height: 1;
}
</style>

<div class="typoprac-dash-wrap">

  <!-- 1. Hero Mountain Sunset Banner (from Mockup) -->
  <div class="dash-hero-banner mb-4">
    <div class="dash-hero-overlay">
      <div>
        <div class="text-uppercase small fw-bold mb-1" style="letter-spacing: 0.15em; color: rgba(255, 255, 255, 0.75);">
          PRACTICE &bull; IMPROVE &bull; MASTER
        </div>
        <h1 class="display-6 fw-bold mb-1" style="letter-spacing: -0.02em;">
          Your Typing Journey <span class="text-orange">Starts Here.</span>
        </h1>
        <p class="text-light opacity-75 mb-0" style="font-size: 0.95rem; max-width: 520px;">
          Build speed, accuracy and confidence with TypoPrac.
        </p>
      </div>

      <!-- XP Level Card -->
      <div class="dash-level-card">
        <div class="d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-1.5 fw-bold" style="font-size: 0.88rem; color: #ffb703;">
            <i class="bi bi-star-fill"></i> Beginner
          </div>
          <span class="text-secondary small fw-semibold">Level <?= $userLevel ?></span>
        </div>

        <div class="dash-level-bar">
          <div class="dash-level-bar-fill" style="width: <?= $xpPercent ?>%;"></div>
        </div>

        <div class="d-flex align-items-center justify-content-between text-secondary" style="font-size: 0.75rem;">
          <span><?= $currentXp ?> XP</span>
          <span><?= $nextLevelXp - $currentXp ?> XP to Level <?= $userLevel + 1 ?></span>
        </div>
      </div>
    </div>
  </div>

  <!-- 2. Four Mode Selector Cards (Horizontal row from Mockup) -->
  <div class="mode-row mb-4">
    <a href="<?= BASE_URL ?>/practice.php?mode=passage" class="mode-card active">
      <div class="d-flex align-items-center gap-3">
        <div class="mode-card-icon">
          <i class="bi bi-file-text-fill"></i>
        </div>
        <div>
          <div class="fw-bold small">Text Practice</div>
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
          <div class="fw-bold small">Typing Test</div>
          <div class="text-secondary" style="font-size: 0.75rem;">Check your speed &amp; accuracy</div>
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
          <div class="fw-bold small">Lessons</div>
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
          <div class="fw-bold small">Games</div>
          <div class="text-secondary" style="font-size: 0.75rem;">Fun way to practice</div>
        </div>
      </div>
      <i class="bi bi-chevron-right text-secondary fs-6"></i>
    </a>
  </div>

  <!-- 3. Two-Column Arena & Telemetry Layout -->
  <div class="row g-4">

    <!-- Left Main Column: Live Practice & Keyboard (from Mockup) -->
    <div class="col-lg-8">
      <div class="dash-practice-box">

        <!-- Controls Header with 195 Passages & Auto-Shuffle -->
        <div class="dash-practice-header">
          <div class="d-flex align-items-center gap-2 flex-wrap">
            <select class="practice-select-pill" id="category-select" onchange="filterCategory(this.value)" title="Filter Category">
              <option value="all">All Topics (195)</option>
              <option value="beginner">Beginner</option>
              <option value="intermediate">Intermediate</option>
              <option value="advanced">Advanced</option>
              <option value="Coding">Coding</option>
              <option value="Quotes">Quotes</option>
              <option value="Science">Science</option>
              <option value="Technology">Technology</option>
            </select>
            <select class="practice-select-pill" id="paragraph-select" onchange="changePassageIndex(this.value)" style="max-width: 250px;" title="Select from 195 Curated Passages">
              <!-- Populated dynamically with all 195 passages -->
            </select>
            <button type="button" class="btn btn-sm rounded-pill px-2.5 py-1 fw-bold d-flex align-items-center gap-1.5" onclick="shufflePassage()" title="Auto-Shuffle to Random Passage" style="background: rgba(255, 87, 34, 0.15); border: 1px solid rgba(255, 87, 34, 0.4); color: #ff7a00; font-size: 0.75rem;">
              <i class="bi bi-shuffle"></i> <span>Auto Shuffle</span>
            </button>
          </div>

          <div class="d-flex align-items-center gap-2">
            <div class="timer-pill">
              <i class="bi bi-clock-fill text-orange"></i>
              <span id="live-timer-display">01:00</span>
            </div>
            <button class="btn btn-sm btn-outline-secondary rounded-2 p-1.5" onclick="resetLivePractice()" title="Reset Drill">
              <i class="bi bi-arrow-clockwise fs-6"></i>
            </button>
            <a href="<?= BASE_URL ?>/practice.php?mode=passage" class="btn btn-sm btn-outline-secondary rounded-2 px-2.5 py-1 text-decoration-none" title="Open Fullscreen Arena">
              <i class="bi bi-arrows-fullscreen"></i>
            </a>
          </div>
        </div>

        <!-- Live Typing Area -->
        <div class="dash-typing-text" id="dash-typing-area" tabindex="0" title="Click here to type directly!">
          <!-- Injected via JavaScript with character spans -->
        </div>

        <input type="text" id="dash-hidden-input" style="position: absolute; opacity: 0; pointer-events: none;" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">

        <!-- Virtual Backlit Keyboard with Glowing Home Markers and Hands Guide -->
        <div class="dash-keyboard-container">
          <div class="kb-row">
            <div class="kb-key" data-char="Q">Q</div>
            <div class="kb-key" data-char="W">W</div>
            <div class="kb-key" data-char="E">E</div>
            <div class="kb-key" data-char="R">R</div>
            <div class="kb-key" data-char="T">T</div>
            <div class="kb-key" data-char="Y">Y</div>
            <div class="kb-key" data-char="U">U</div>
            <div class="kb-key" data-char="I">I</div>
            <div class="kb-key" data-char="O">O</div>
            <div class="kb-key" data-char="P">P</div>
            <div class="kb-key" data-char="[">[</div>
            <div class="kb-key" data-char="]">]</div>
          </div>

          <div class="kb-row">
            <div class="kb-key" data-char="A">A</div>
            <div class="kb-key" data-char="S">S</div>
            <div class="kb-key" data-char="D">D</div>
            <!-- Home key F glowing orange -->
            <div class="kb-key home-marker" data-char="F">F</div>
            <div class="kb-key" data-char="G">G</div>
            <div class="kb-key" data-char="H">H</div>
            <!-- Home key J glowing orange -->
            <div class="kb-key home-marker" data-char="J">J</div>
            <div class="kb-key" data-char="K">K</div>
            <div class="kb-key" data-char="L">L</div>
            <div class="kb-key" data-char=";">;</div>
            <div class="kb-key" data-char="'">'</div>
          </div>

          <div class="kb-row">
            <div class="kb-key" data-char="Z">Z</div>
            <div class="kb-key" data-char="X">X</div>
            <div class="kb-key" data-char="C">C</div>
            <div class="kb-key" data-char="V">V</div>
            <div class="kb-key" data-char="B">B</div>
            <div class="kb-key" data-char="N">N</div>
            <div class="kb-key" data-char="M">M</div>
            <div class="kb-key" data-char=",">,</div>
            <div class="kb-key" data-char=".">.</div>
            <div class="kb-key" data-char="/">/</div>
          </div>

          <div class="kb-row mt-1">
            <div class="kb-key" style="min-width: 280px;" data-char=" ">SPACE</div>
          </div>

          <!-- Hands Posture Guide Overlay (from Mockup) -->
          <svg class="hands-guide-visual" viewBox="0 0 600 140" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Left Hand -->
            <g opacity="0.8">
              <path d="M120 140 C120 90, 140 70, 160 55 C165 52, 175 60, 172 70 C180 50, 192 48, 195 62 C202 45, 215 45, 218 60 C225 48, 238 52, 235 75 C232 95, 245 110, 260 140 Z" fill="rgba(255, 87, 34, 0.15)" stroke="#ff5722" stroke-width="1.5" stroke-dasharray="3 3"/>
              <!-- Finger tips on home row A,S,D,F -->
              <circle cx="165" cy="56" r="5" fill="#ff5722"/>
              <circle cx="187" cy="52" r="5" fill="#ff5722"/>
              <circle cx="210" cy="50" r="5" fill="#ff5722"/>
              <circle cx="230" cy="55" r="6" fill="#ff5722" stroke="#ffffff" stroke-width="1.5"/>
            </g>

            <!-- Right Hand -->
            <g opacity="0.8">
              <path d="M480 140 C480 90, 460 70, 440 55 C435 52, 425 60, 428 70 C420 50, 408 48, 405 62 C398 45, 385 45, 382 60 C375 48, 362 52, 365 75 C368 95, 355 110, 340 140 Z" fill="rgba(255, 87, 34, 0.15)" stroke="#ff5722" stroke-width="1.5" stroke-dasharray="3 3"/>
              <!-- Finger tips on home row J,K,L,; -->
              <circle cx="370" cy="55" r="6" fill="#ff5722" stroke="#ffffff" stroke-width="1.5"/>
              <circle cx="390" cy="50" r="5" fill="#ff5722"/>
              <circle cx="413" cy="52" r="5" fill="#ff5722"/>
              <circle cx="435" cy="56" r="5" fill="#ff5722"/>
            </g>
          </svg>
        </div>

        <!-- Keyboard Color Legend (from Mockup) -->
        <div class="kb-legend">
          <div class="d-flex align-items-center gap-1.5">
            <span class="kb-dot" style="background: #ff5722;"></span>
            <span>Correct Key</span>
          </div>
          <div class="d-flex align-items-center gap-1.5">
            <span class="kb-dot" style="background: #ff3d00;"></span>
            <span>Current Key</span>
          </div>
          <div class="d-flex align-items-center gap-1.5">
            <span class="kb-dot" style="background: #64748b;"></span>
            <span>Next Key</span>
          </div>
        </div>

      </div>
    </div>

    <!-- Right Column: Telemetry, Progress Chart, Tips & Today's Practice (from Mockup) -->
    <div class="col-lg-4">

      <!-- 3 Top Stats Mini Cards (WPM, Accuracy, Time) -->
      <div class="stat-mini-grid">
        <div class="stat-mini-card">
          <div class="text-secondary small fw-semibold">
            <i class="bi bi-speedometer2 text-orange"></i> WPM
          </div>
          <div class="stat-mini-val" id="dash-live-wpm"><?= $displayWpm ?></div>
          <div class="delta-badge"><i class="bi bi-arrow-up"></i> +12</div>
        </div>

        <div class="stat-mini-card">
          <div class="text-secondary small fw-semibold">
            <i class="bi bi-bullseye text-danger"></i> Accuracy
          </div>
          <div class="stat-mini-val" id="dash-live-acc"><?= $displayAcc ?>%</div>
          <div class="delta-badge"><i class="bi bi-arrow-up"></i> +4%</div>
        </div>

        <div class="stat-mini-card">
          <div class="text-secondary small fw-semibold">
            <i class="bi bi-clock-fill text-info"></i> Time
          </div>
          <div class="stat-mini-val" style="font-size: 1.35rem; padding-top: 3px;">01:00</div>
          <div class="text-secondary" style="font-size: 0.68rem; margin-top: 2px;">Standard</div>
        </div>
      </div>

      <!-- Live Progress Bar Chart (from Mockup) -->
      <div class="p-3.5 rounded-3 mb-3" style="background: var(--tp-card-bg); border: 1px solid var(--tp-border);">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div class="fw-bold small">Live Progress</div>
          <span class="text-secondary" style="font-size: 0.72rem; font-family: 'JetBrains Mono', monospace;">WPM SPEED</span>
        </div>

        <div class="live-bars-wrap">
          <div class="live-bar-col" style="height: 25%;"></div>
          <div class="live-bar-col" style="height: 38%;"></div>
          <div class="live-bar-col" style="height: 48%;"></div>
          <div class="live-bar-col" style="height: 42%;"></div>
          <div class="live-bar-col" style="height: 60%;"></div>
          <div class="live-bar-col" style="height: 55%;"></div>
          <div class="live-bar-col" style="height: 75%;"></div>
          <div class="live-bar-col" style="height: 68%;"></div>
          <div class="live-bar-col" style="height: 92%;"></div>
          <div class="live-bar-col" style="height: 85%;"></div>
        </div>

        <div class="d-flex justify-content-between text-secondary mt-1" style="font-size: 0.65rem; font-family: 'JetBrains Mono', monospace;">
          <span>0s</span>
          <span>15s</span>
          <span>30s</span>
          <span>45s</span>
          <span>60s</span>
        </div>
      </div>

      <!-- Typing Tips Card (from Mockup) -->
      <div class="p-3.5 rounded-3 mb-3" style="background: var(--tp-card-bg); border: 1px solid var(--tp-border);">
        <div class="d-flex align-items-center gap-2 mb-3">
          <i class="bi bi-lightbulb-fill text-warning"></i>
          <span class="fw-bold small">Typing Tips</span>
        </div>

        <div class="tip-item">
          <i class="bi bi-check-circle-fill"></i>
          <span>Keep your fingers on the home row.</span>
        </div>
        <div class="tip-item">
          <i class="bi bi-check-circle-fill"></i>
          <span>Don't look at the keyboard.</span>
        </div>
        <div class="tip-item">
          <i class="bi bi-check-circle-fill"></i>
          <span>Maintain a steady pace.</span>
        </div>
        <div class="tip-item">
          <i class="bi bi-check-circle-fill"></i>
          <span>Focus on accuracy first.</span>
        </div>
      </div>

      <!-- Today's Practice Donut Chart (from Mockup) -->
      <div class="p-3.5 rounded-3" style="background: var(--tp-card-bg); border: 1px solid var(--tp-border);">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-calendar-check-fill text-orange"></i>
            <span class="fw-bold small">Today's Practice</span>
          </div>
          <a href="<?= BASE_URL ?>/history.php" class="text-orange small text-decoration-none fw-semibold" style="font-size: 0.75rem;">
            View All &rarr;
          </a>
        </div>

        <div class="d-flex align-items-center justify-content-between gap-3">
          <!-- Donut Graphic -->
          <div class="position-relative" style="width: 100px; height: 100px; flex-shrink: 0;">
            <svg viewBox="0 0 36 36" style="width: 100%; height: 100%; transform: rotate(-90deg);">
              <!-- Background ring -->
              <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="rgba(255,255,255,0.06)" stroke-width="4"/>
              <!-- Text Practice segment (12 min = 60%) -->
              <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#ff5722" stroke-width="4" stroke-dasharray="60, 100"/>
              <!-- Test segment (5 min = 25%) -->
              <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#ff3d00" stroke-width="4" stroke-dasharray="25, 100" stroke-dashoffset="-60"/>
              <!-- Lessons segment (3 min = 15%) -->
              <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#8338ec" stroke-width="4" stroke-dasharray="15, 100" stroke-dashoffset="-85"/>
            </svg>
            <div class="donut-center-val">
              20<span style="font-size: 0.65rem; display: block; color: var(--tp-text-dim);">min</span>
            </div>
          </div>

          <!-- Breakdown list -->
          <div class="flex-grow-1" style="font-size: 0.78rem;">
            <div class="d-flex align-items-center justify-content-between mb-1.5">
              <span class="d-flex align-items-center gap-1.5 text-secondary">
                <span class="kb-dot" style="background: #ff5722;"></span> Text Practice
              </span>
              <span class="fw-bold">12 min</span>
            </div>
            <div class="d-flex align-items-center justify-content-between mb-1.5">
              <span class="d-flex align-items-center gap-1.5 text-secondary">
                <span class="kb-dot" style="background: #ff3d00;"></span> Typing Test
              </span>
              <span class="fw-bold">5 min</span>
            </div>
            <div class="d-flex align-items-center justify-content-between">
              <span class="d-flex align-items-center gap-1.5 text-secondary">
                <span class="kb-dot" style="background: #8338ec;"></span> Lessons
              </span>
              <span class="fw-bold">3 min</span>
            </div>
          </div>
        </div>

      </div>

    </div>
  </div>

</div>

<script>
(function() {
  'use strict';

  var allPassages = <?= json_encode($allPassagesList, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
  if (!allPassages || allPassages.length === 0) {
    allPassages = [
      { id: 1, title: 'Mountain Climbs', category: 'Nature', difficulty: 'beginner', content: 'The mountains are a constant reminder that the best views often come after the hardest climbs. With patience, practice and a positive mindset, you can achieve anything you set your mind to.' }
    ];
  }

  var filteredPassages = allPassages.slice();
  var currentPassage = filteredPassages[0];
  var currentText = currentPassage.content;
  var charIndex = 0;
  var errors = 0;
  var totalTyped = 0;
  var timerStarted = false;
  var startTime = 0;
  var timerInterval = null;
  var duration = 60;

  var typingArea = document.getElementById('dash-typing-area');
  var hiddenInput = document.getElementById('dash-hidden-input');
  var paragraphSelect = document.getElementById('paragraph-select');
  var categorySelect = document.getElementById('category-select');

  function populatePassages(selectedId) {
    if (!paragraphSelect) return;
    paragraphSelect.innerHTML = '';
    filteredPassages.forEach(function(p, idx) {
      var opt = document.createElement('option');
      opt.value = idx;
      opt.textContent = (idx + 1) + '. ' + p.title + ' (' + (p.word_count || p.content.split(/\s+/).length) + 'w)';
      if (selectedId && p.id === selectedId) {
        opt.selected = true;
      }
      paragraphSelect.appendChild(opt);
    });
  }

  window.filterCategory = function(cat) {
    if (cat === 'all') {
      filteredPassages = allPassages.slice();
    } else {
      filteredPassages = allPassages.filter(function(p) {
        return (p.difficulty && p.difficulty.toLowerCase() === cat.toLowerCase()) ||
               (p.category && p.category.toLowerCase() === cat.toLowerCase());
      });
      if (filteredPassages.length === 0) filteredPassages = allPassages.slice();
    }
    populatePassages();
    currentPassage = filteredPassages[0];
    currentText = currentPassage.content;
    window.resetLivePractice();
  };

  window.changePassageIndex = function(idx) {
    var p = filteredPassages[parseInt(idx, 10)] || filteredPassages[0];
    currentPassage = p;
    currentText = p.content;
    window.resetLivePractice();
  };

  window.shufflePassage = function() {
    var randIdx = Math.floor(Math.random() * filteredPassages.length);
    if (filteredPassages.length > 1 && filteredPassages[randIdx].id === currentPassage.id) {
      randIdx = (randIdx + 1) % filteredPassages.length;
    }
    if (paragraphSelect) paragraphSelect.selectedIndex = randIdx;
    window.changePassageIndex(randIdx);
  };

  function renderText() {
    typingArea.innerHTML = '';
    for (var i = 0; i < currentText.length; i++) {
      var span = document.createElement('span');
      span.className = 'dash-char';
      span.textContent = currentText[i];
      span.id = 'char-' + i;
      typingArea.appendChild(span);
    }
    updateCaret();
    highlightTargetKey();
  }

  function updateCaret() {
    var oldCaret = document.getElementById('dash-active-caret');
    if (oldCaret) oldCaret.remove();

    var targetSpan = document.getElementById('char-' + charIndex);
    if (targetSpan) {
      var caret = document.createElement('span');
      caret.id = 'dash-active-caret';
      caret.className = 'dash-caret';
      targetSpan.parentNode.insertBefore(caret, targetSpan);
    }
  }

  function highlightTargetKey() {
    var targetChar = currentText[charIndex];
    document.querySelectorAll('.kb-key').forEach(function(k) {
      k.classList.remove('pressed');
      if (targetChar) {
        var keyChar = k.getAttribute('data-char');
        if (keyChar === targetChar.toUpperCase() || (targetChar === ' ' && keyChar === ' ')) {
          k.classList.add('pressed');
        }
      }
    });
  }

  window.resetLivePractice = function() {
    clearInterval(timerInterval);
    timerStarted = false;
    charIndex = 0;
    errors = 0;
    totalTyped = 0;
    document.getElementById('live-timer-display').textContent = '01:00';
    renderText();
    hiddenInput.value = '';
    hiddenInput.focus();
  };

  typingArea.addEventListener('click', function() {
    hiddenInput.focus();
  });

  window.addEventListener('keydown', function(e) {
    if (document.activeElement.tagName === 'INPUT' || document.activeElement.tagName === 'TEXTAREA') {
      if (document.activeElement !== hiddenInput) return;
    }

    if (e.key === 'Tab') {
      e.preventDefault();
      window.resetLivePractice();
      return;
    }

    // Spacebar and Backspace PREVENT DEFAULT: no page scroll or browser back!
    if (e.key === ' ' || e.key === 'Backspace' || e.key.length === 1) {
      if (e.key === ' ' || e.key === 'Backspace') {
        e.preventDefault();
      }

      if (!timerStarted) {
        timerStarted = true;
        startTime = Date.now();
        timerInterval = setInterval(updateTimer, 1000);
      }

      if (e.key === 'Backspace') {
        if (charIndex > 0) {
          charIndex--;
          var prevSpan = document.getElementById('char-' + charIndex);
          if (prevSpan) {
            prevSpan.classList.remove('correct', 'incorrect');
          }
          updateCaret();
          highlightTargetKey();
        }
        return;
      }

      if (charIndex < currentText.length) {
        var expected = currentText[charIndex];
        var curSpan = document.getElementById('char-' + charIndex);

        totalTyped++;
        if (e.key === expected) {
          curSpan.classList.add('correct');
        } else {
          curSpan.classList.add('incorrect');
          errors++;
        }

        charIndex++;
        updateCaret();
        highlightTargetKey();
        calculateTelemetry();

        if (charIndex >= currentText.length) {
          clearInterval(timerInterval);
          // Auto-shuffle to next random passage after a brief pause
          setTimeout(function() {
            window.shufflePassage();
          }, 1400);
        }
      }
    }
  });

  function updateTimer() {
    var elapsed = Math.floor((Date.now() - startTime) / 1000);
    var remaining = Math.max(0, duration - elapsed);
    var mins = Math.floor(remaining / 60);
    var secs = remaining % 60;
    document.getElementById('live-timer-display').textContent =
      (mins < 10 ? '0' : '') + mins + ':' + (secs < 10 ? '0' : '') + secs;

    if (remaining <= 0) {
      clearInterval(timerInterval);
    }
  }

  function calculateTelemetry() {
    if (!timerStarted) return;
    var elapsedMins = (Date.now() - startTime) / 60000;
    if (elapsedMins > 0.005) {
      var wpm = Math.round((charIndex / 5) / elapsedMins);
      var displayWpm = Math.max(10, Math.min(220, wpm));
      document.getElementById('dash-live-wpm').textContent = displayWpm;

      var acc = totalTyped > 0 ? Math.round(((totalTyped - errors) / totalTyped) * 100) : 100;
      document.getElementById('dash-live-acc').textContent = Math.max(50, acc) + '%';

      // Update Live Progress Bar Chart dynamically
      var bars = document.querySelectorAll('.live-bar-col');
      if (bars.length > 0) {
        var targetBarIdx = Math.min(bars.length - 1, Math.floor((charIndex / Math.max(1, currentText.length)) * bars.length));
        var barHeight = Math.min(100, Math.max(20, Math.round((displayWpm / 100) * 100)));
        bars[targetBarIdx].style.height = barHeight + '%';
      }
    }
  }

  // Initialize 195 passages list
  populatePassages();
  renderText();
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>