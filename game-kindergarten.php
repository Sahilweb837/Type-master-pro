<?php
/**
 * TypoPrac — Professional Typing Practice Software
 * Kindergarten & Kids Typing Adventure: Animal Letter Jungle
 * Designed for ages 4-8: Letter recognition, phonics, cheerful animations & audio
 */

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/games_data.php';

$currentUser = current_user();
$userId = $currentUser ? (int)$currentUser['id'] : 0;

$pageTitle = 'Kindergarten Typing Games for Kids — Letter Jungle Adventure';
$pageBadgeTitle = 'Kindergarten Games';
$pageIcon = 'bi-stars';

include __DIR__ . '/includes/header.php';
?>

<style>
/* =====================================================================
   TypoPrac Kindergarten & Kids Wonderland Theme
   ===================================================================== */
:root {
  --kids-orange: #ff5722;
  --kids-yellow: #ffb703;
  --kids-green:  #06d6a0;
  --kids-blue:   #118ab2;
  --kids-pink:   #ec4899;
  --kids-purple: #8338ec;
}

.kids-game-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 1rem 0 3rem;
  font-family: 'Inter', -apple-system, sans-serif;
}

/* Hero Title Card */
.kids-hero-card {
  position: relative;
  border-radius: 20px;
  overflow: hidden;
  background: url('<?= BASE_URL ?>/assets/images/kids-typing-banner.jpg') center/cover no-repeat;
  border: 2px solid rgba(255, 87, 34, 0.3);
  box-shadow: 0 12px 36px rgba(0, 0, 0, 0.5);
  margin-bottom: 1.5rem;
  min-height: 220px;
}
.kids-hero-overlay {
  background: linear-gradient(90deg, rgba(10, 14, 23, 0.95) 0%, rgba(10, 14, 23, 0.82) 55%, rgba(10, 14, 23, 0.45) 100%);
  padding: 2rem 2.5rem;
  display: flex;
  flex-direction: column;
  justify-content: center;
  min-height: 220px;
}
@media (max-width: 768px) {
  .kids-hero-overlay {
    background: linear-gradient(180deg, rgba(10, 14, 23, 0.92) 0%, rgba(10, 14, 23, 0.98) 100%);
    padding: 1.5rem;
  }
}

/* Mode Buttons */
.kids-mode-btn {
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: 12px;
  padding: 0.65rem 1.1rem;
  color: #ffffff;
  font-weight: 700;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  transition: all 0.2s ease;
  font-size: 0.88rem;
}
.kids-mode-btn:hover {
  background: rgba(255, 87, 34, 0.15);
  border-color: #ff5722;
  transform: translateY(-2px);
}
.kids-mode-btn.active {
  background: linear-gradient(135deg, #ff5500 0%, #ff8a00 100%);
  border-color: #ff5500;
  box-shadow: 0 4px 15px rgba(255, 87, 34, 0.4);
}

/* Play Stage Arena */
.kids-stage {
  position: relative;
  height: 480px;
  background: linear-gradient(180deg, #0b111e 0%, #0f192c 60%, #16243d 100%);
  border: 2px solid rgba(255, 87, 34, 0.25);
  border-radius: 20px;
  overflow: hidden;
  box-shadow: inset 0 2px 10px rgba(0, 0, 0, 0.6), 0 8px 30px rgba(0, 0, 0, 0.4);
}

/* Floating Bubble */
.letter-bubble {
  position: absolute;
  width: 80px;
  height: 80px;
  border-radius: 50%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: #ffffff;
  font-weight: 900;
  font-size: 2.1rem;
  font-family: 'Outfit', 'Inter', sans-serif;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35), inset 0 -4px 10px rgba(0, 0, 0, 0.2), inset 0 4px 10px rgba(255, 255, 255, 0.6);
  user-select: none;
  cursor: pointer;
  transition: transform 0.15s ease;
  animation: floatWobble 2.5s ease-in-out infinite alternate;
}
.letter-bubble .bubble-hint {
  font-size: 0.62rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  opacity: 0.85;
  margin-top: -4px;
}
@keyframes floatWobble {
  0% { transform: translateY(0px) rotate(0deg); }
  100% { transform: translateY(-8px) rotate(4deg); }
}

/* Mascot Animal Companion */
.kids-companion-bubble {
  position: absolute;
  bottom: 20px;
  left: 25px;
  background: rgba(19, 26, 42, 0.92);
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 87, 34, 0.35);
  border-radius: 16px;
  padding: 0.75rem 1.2rem;
  display: flex;
  align-items: center;
  gap: 0.8rem;
  max-width: 320px;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.5);
  z-index: 10;
}
.companion-avatar {
  font-size: 2.2rem;
  animation: bounceCompanion 1.8s infinite;
}
@keyframes bounceCompanion {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-6px); }
}

/* Kids Big Touch Keyboard */
.kids-keyboard {
  background: #0f1624;
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 16px;
  padding: 1.2rem;
  margin-top: 1.25rem;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
}
.k-row {
  display: flex;
  justify-content: center;
  gap: 6px;
  margin-bottom: 6px;
}
.k-key {
  width: 44px;
  height: 44px;
  border-radius: 8px;
  background: #192336;
  border: 1px solid rgba(255, 255, 255, 0.08);
  color: #e2e8f0;
  font-weight: 800;
  font-size: 1.05rem;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 3px 6px rgba(0, 0, 0, 0.3);
  transition: all 0.1s ease;
  user-select: none;
}
.k-key.highlight {
  background: linear-gradient(135deg, #ff5500 0%, #ff8a00 100%) !important;
  color: #ffffff !important;
  box-shadow: 0 0 18px rgba(255, 87, 34, 0.8) !important;
  transform: scale(1.12);
  z-index: 2;
}
.k-key.correct-pop {
  background: #06d6a0 !important;
  color: #000000 !important;
  transform: scale(1.15);
}
</style>

<div class="kids-game-container">

  <!-- Hero Header -->
  <div class="kids-hero-card">
    <div class="kids-hero-overlay">
      <div class="d-flex align-items-center gap-2 mb-2">
        <span class="badge px-3 py-1.5 rounded-pill" style="background: rgba(255, 87, 34, 0.2); border: 1px solid #ff5722; color: #ff5722; font-weight: 800; font-size: 0.75rem;">
          🧸 AGES 4-8 &bull; 100% FREE &bull; FUN TYPING
        </span>
        <span class="badge px-2.5 py-1 rounded-pill" style="background: rgba(6, 214, 160, 0.2); color: #06d6a0; font-weight: 700; font-size: 0.72rem;">
          ★ NO TYPOS PENALTY
        </span>
      </div>
      <h1 class="display-6 fw-bold text-white mb-1" style="letter-spacing: -0.02em;">
        Kindergarten <span class="text-orange">Letter Jungle</span>
      </h1>
      <p class="text-secondary mb-3" style="max-width: 620px; font-size: 0.95rem;">
        Learn the keyboard through joyful games! Pop floating letter bubbles, hear phonic pronunciations, and earn magical jungle stars.
      </p>

      <!-- Mode Selector -->
      <div class="d-flex flex-wrap gap-2">
        <button class="kids-mode-btn active" data-mode="abc" onclick="switchKidsMode('abc')">
          <i class="bi bi-alphabet-uppercase"></i> ABC Alphabet
        </button>
        <button class="kids-mode-btn" data-mode="homerow" onclick="switchKidsMode('homerow')">
          <i class="bi bi-hand-index-thumb-fill text-warning"></i> Home Row (ASDF JKL)
        </button>
        <button class="kids-mode-btn" data-mode="animals" onclick="switchKidsMode('animals')">
          <i class="bi bi-balloon-heart-fill text-danger"></i> 3-Letter Phonics Animals
        </button>
        <button class="kids-mode-btn" data-mode="numbers" onclick="switchKidsMode('numbers')">
          <i class="bi bi-123 text-info"></i> Numbers 1-10
        </button>
      </div>
    </div>
  </div>

  <!-- HUD Control Row -->
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3 p-3 rounded-3" style="background: var(--bg-card); border: 1px solid var(--border-color);">
    <div class="d-flex align-items-center gap-4">
      <div>
        <span class="text-secondary small d-block">⭐ Stars Collected</span>
        <span id="star-count" class="h4 fw-bold text-warning mb-0">0</span>
      </div>
      <div>
        <span class="text-secondary small d-block">🎯 Letters Popped</span>
        <span id="pop-count" class="h4 fw-bold text-white mb-0">0</span>
      </div>
      <div>
        <span class="text-secondary small d-block">💖 Fun Streak</span>
        <span id="streak-count" class="h4 fw-bold text-orange mb-0">0</span>
      </div>
    </div>

    <div class="d-flex align-items-center gap-2">
      <button id="sound-toggle-btn" class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="toggleKidsSound()">
        <i class="bi bi-volume-up-fill text-orange"></i> Voice &amp; FX: <strong id="sound-status">ON</strong>
      </button>
      <button class="btn btn-sm rounded-pill px-3 fw-bold text-white" style="background: linear-gradient(135deg, #ff5500, #ff7a00);" onclick="resetKidsGame()">
        <i class="bi bi-arrow-clockwise"></i> Restart Jungle
      </button>
      <a href="<?= BASE_URL ?>/games.php" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
        <i class="bi bi-grid-fill"></i> All Games
      </a>
    </div>
  </div>

  <!-- Interactive Play Stage -->
  <div id="kids-stage" class="kids-stage">

    <!-- Active Target Prompt Banner -->
    <div style="position: absolute; top: 20px; left: 50%; transform: translateX(-50%); z-index: 10; background: rgba(19, 26, 42, 0.92); border: 1px solid rgba(255, 87, 34, 0.4); border-radius: 99px; padding: 0.6rem 2rem; display: flex; align-items: center; gap: 0.8rem; box-shadow: 0 4px 20px rgba(0,0,0,0.4);">
      <span style="font-size: 0.82rem; color: #94a3b8; font-weight: 700; text-transform: uppercase;">PRESS KEY:</span>
      <span id="active-target-display" style="font-size: 1.6rem; font-weight: 900; color: #ff5722; font-family: 'Outfit', sans-serif;">A</span>
    </div>

    <!-- Animal Mascot Companion Guidance -->
    <div class="kids-companion-bubble">
      <div class="companion-avatar" id="mascot-emoji">🦊</div>
      <div>
        <div class="fw-bold small text-white" id="mascot-name">Freddy the Fox</div>
        <div class="text-secondary" style="font-size: 0.78rem;" id="mascot-hint">Press the glowing letter on your keyboard!</div>
      </div>
    </div>

    <!-- Floating bubbles container -->
    <div id="bubbles-container"></div>
  </div>

  <!-- Big Kids On-Screen Keyboard -->
  <div class="kids-keyboard">
    <div class="text-center text-secondary small mb-2 fw-semibold">
      <i class="bi bi-keyboard"></i> COLOR-CODED KIDS KEYBOARD &bull; WATCH THE GLOWING KEY
    </div>

    <div class="k-row" id="k-row-1">
      <div class="k-key" data-key="Q">Q</div>
      <div class="k-key" data-key="W">W</div>
      <div class="k-key" data-key="E">E</div>
      <div class="k-key" data-key="R">R</div>
      <div class="k-key" data-key="T">T</div>
      <div class="k-key" data-key="Y">Y</div>
      <div class="k-key" data-key="U">U</div>
      <div class="k-key" data-key="I">I</div>
      <div class="k-key" data-key="O">O</div>
      <div class="k-key" data-key="P">P</div>
    </div>

    <div class="k-row" id="k-row-2">
      <div class="k-key" data-key="A">A</div>
      <div class="k-key" data-key="S">S</div>
      <div class="k-key" data-key="D">D</div>
      <div class="k-key" data-key="F">F</div>
      <div class="k-key" data-key="G">G</div>
      <div class="k-key" data-key="H">H</div>
      <div class="k-key" data-key="J">J</div>
      <div class="k-key" data-key="K">K</div>
      <div class="k-key" data-key="L">L</div>
    </div>

    <div class="k-row" id="k-row-3">
      <div class="k-key" data-key="Z">Z</div>
      <div class="k-key" data-key="X">X</div>
      <div class="k-key" data-key="C">C</div>
      <div class="k-key" data-key="V">V</div>
      <div class="k-key" data-key="B">B</div>
      <div class="k-key" data-key="N">N</div>
      <div class="k-key" data-key="M">M</div>
    </div>

    <div class="k-row mt-1">
      <div class="k-key" style="width: 280px;" data-key="SPACE">SPACEBAR</div>
    </div>
  </div>

</div>

<script>
(function() {
  'use strict';

  var currentMode = 'abc';
  var stars = 0;
  var popped = 0;
  var streak = 0;
  var soundEnabled = true;
  var activeBubbles = [];
  var spawnTimer = null;

  var colors = ['#ff5722', '#06d6a0', '#ffb703', '#118ab2', '#ec4899', '#8338ec'];
  var animalWords = [
    { word: 'CAT', hint: '🐱 Cat' },
    { word: 'DOG', hint: '🐶 Dog' },
    { word: 'FOX', hint: '🦊 Fox' },
    { word: 'OWL', hint: '🦉 Owl' },
    { word: 'BEE', hint: '🐝 Bee' },
    { word: 'PIG', hint: '🐷 Pig' },
    { word: 'COW', hint: '🐮 Cow' },
    { word: 'BAT', hint: '🦇 Bat' },
    { word: 'SUN', hint: '☀️ Sun' }
  ];

  var currentWordIndex = 0;
  var currentWordCharIdx = 0;

  var audioCtx = null;
  function getAudioCtx() {
    if (!audioCtx) {
      audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    }
    if (audioCtx.state === 'suspended') {
      audioCtx.resume();
    }
    return audioCtx;
  }

  function playPopTone() {
    if (!soundEnabled) return;
    try {
      var ctx = getAudioCtx();
      var osc = ctx.createOscillator();
      var gain = ctx.createGain();
      osc.type = 'sine';
      osc.frequency.setValueAtTime(440, ctx.currentTime);
      osc.frequency.exponentialRampToValueAtTime(880, ctx.currentTime + 0.15);
      gain.gain.setValueAtTime(0.3, ctx.currentTime);
      gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.15);
      osc.connect(gain);
      gain.connect(ctx.destination);
      osc.start();
      osc.stop(ctx.currentTime + 0.16);
    } catch(e) {}
  }

  function speakLetter(char) {
    if (!soundEnabled || !window.speechSynthesis) return;
    try {
      window.speechSynthesis.cancel();
      var utter = new SpeechSynthesisUtterance(char.toLowerCase());
      utter.rate = 0.9;
      utter.pitch = 1.2;
      window.speechSynthesis.speak(utter);
    } catch(e) {}
  }

  window.toggleKidsSound = function() {
    soundEnabled = !soundEnabled;
    document.getElementById('sound-status').textContent = soundEnabled ? 'ON' : 'OFF';
  };

  window.switchKidsMode = function(mode) {
    currentMode = mode;
    document.querySelectorAll('.kids-mode-btn').forEach(function(b) {
      b.classList.toggle('active', b.getAttribute('data-mode') === mode);
    });
    resetKidsGame();
  };

  window.resetKidsGame = function() {
    clearInterval(spawnTimer);
    document.getElementById('bubbles-container').innerHTML = '';
    activeBubbles = [];
    currentWordIndex = 0;
    currentWordCharIdx = 0;
    spawnNextBubble();
    spawnTimer = setInterval(updateBubbles, 50);
  };

  function highlightKeyOnKeyboard(char) {
    document.querySelectorAll('.k-key').forEach(function(k) {
      k.classList.remove('highlight');
      if (k.getAttribute('data-key') === char.toUpperCase()) {
        k.classList.add('highlight');
      }
    });
  }

  function spawnNextBubble() {
    var char = 'A';
    var hint = 'Letter A';

    if (currentMode === 'abc') {
      var alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
      char = alphabet[Math.floor(Math.random() * alphabet.length)];
      hint = 'Letter ' + char;
    } else if (currentMode === 'homerow') {
      var homeRow = 'ASDFJKL';
      char = homeRow[Math.floor(Math.random() * homeRow.length)];
      hint = 'Home Row ' + char;
    } else if (currentMode === 'animals') {
      var item = animalWords[currentWordIndex % animalWords.length];
      char = item.word[currentWordCharIdx];
      hint = item.hint;
    } else if (currentMode === 'numbers') {
      var numbers = '1234567890';
      char = numbers[Math.floor(Math.random() * numbers.length)];
      hint = 'Number ' + char;
    }

    document.getElementById('active-target-display').textContent = char;
    highlightKeyOnKeyboard(char);
    speakLetter(char);

    var stage = document.getElementById('kids-stage');
    var maxLeft = stage.clientWidth - 110;
    var leftPos = Math.max(30, Math.floor(Math.random() * maxLeft));

    var bubbleEl = document.createElement('div');
    bubbleEl.className = 'letter-bubble';
    bubbleEl.style.left = leftPos + 'px';
    bubbleEl.style.top = '70px';
    bubbleEl.style.background = colors[Math.floor(Math.random() * colors.length)];
    bubbleEl.innerHTML = char + '<span class="bubble-hint">' + hint + '</span>';

    stage.querySelector('#bubbles-container').appendChild(bubbleEl);

    var bubbleObj = {
      el: bubbleEl,
      char: char.toUpperCase(),
      top: 70,
      left: leftPos,
      speed: 1.2
    };

    activeBubbles.push(bubbleObj);
  }

  function updateBubbles() {
    var stage = document.getElementById('kids-stage');
    var stageHeight = stage.clientHeight;

    for (var i = activeBubbles.length - 1; i >= 0; i--) {
      var b = activeBubbles[i];
      b.top += b.speed;
      b.el.style.top = b.top + 'px';

      if (b.top > stageHeight - 90) {
        b.el.remove();
        activeBubbles.splice(i, 1);
        spawnNextBubble();
      }
    }
  }

  // Handle Keypress
  window.addEventListener('keydown', function(e) {
    if (activeBubbles.length === 0) return;

    var pressedKey = e.key.toUpperCase();
    var currentTarget = activeBubbles[0];

    if (pressedKey === currentTarget.char || (pressedKey === ' ' && currentTarget.char === 'SPACE')) {
      e.preventDefault();
      playPopTone();

      // Confetti burst on bubble
      currentTarget.el.style.transform = 'scale(1.5)';
      currentTarget.el.style.opacity = '0';
      setTimeout(function() {
        if (currentTarget.el.parentNode) currentTarget.el.remove();
      }, 150);

      activeBubbles.shift();

      stars += 5;
      popped += 1;
      streak += 1;

      document.getElementById('star-count').textContent = stars;
      document.getElementById('pop-count').textContent = popped;
      document.getElementById('streak-count').textContent = streak;

      // Update mascot
      var cheers = ['Great Job!', 'Super Fast!', 'Awesome!', 'Keep Going!', 'Star Typer!'];
      document.getElementById('mascot-hint').textContent = cheers[Math.floor(Math.random() * cheers.length)];

      if (currentMode === 'animals') {
        currentWordCharIdx++;
        var currentItem = animalWords[currentWordIndex % animalWords.length];
        if (currentWordCharIdx >= currentItem.word.length) {
          currentWordCharIdx = 0;
          currentWordIndex++;
        }
      }

      setTimeout(spawnNextBubble, 300);
    }
  });

  // Start game on load
  window.resetKidsGame();
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
