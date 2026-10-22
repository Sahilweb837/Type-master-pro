/**
 * =====================================================================
 * Custom Type Master Pro - Advanced Real-Time Typing Engine
 * Features:
 * - Real-time word count (words completed, remaining, total)
 * - Real-time Gross WPM & Net WPM formulas
 * - Keystroke precision accuracy & error tracking
 * - Smooth dynamic caret positioning (line, block, underline)
 * - Web Audio API synthesizer for tactile mechanical & click sound effects
 * - Countdown timer & passage completion detector
 * - Automatic result submission via secure AJAX
 * =====================================================================
 */

class TypingEngine {
  constructor(options = {}) {
    this.options = Object.assign({
      containerId: 'typing-box',
      hiddenInputId: 'hidden-typing-input',
      timerDisplayId: 'live-timer',
      wpmDisplayId: 'live-wpm',
      netWpmDisplayId: 'live-net-wpm',
      accuracyDisplayId: 'live-accuracy',
      wordsDisplayId: 'live-words-count',
      cpmDisplayId: 'live-cpm',
      charsDisplayId: 'live-chars-count',
      linesDisplayId: 'live-lines-count',
      errorsDisplayId: 'live-errors-count',
      progressBarId: 'typing-progress-bar',
      caretStyle: 'line', // 'line', 'block', 'underline'
      soundEnabled: true,
      soundTheme: 'mechanical', // 'mechanical', 'click', 'beep', 'silent'
      duration: 60, // seconds
      mode: 'timed', // 'timed' or 'passage'
      passageId: null,
      difficulty: 'intermediate',
      csrfToken: '',
      backspaceDisabled: true, // Strict Exam Mode: Backspace/Delete Disabled by default
      onFinish: null
    }, options);

    // Core DOM Elements
    this.container = document.getElementById(this.options.containerId);
    this.input = document.getElementById(this.options.hiddenInputId);
    this.timerDisplay = document.getElementById(this.options.timerDisplayId);
    this.wpmDisplay = document.getElementById(this.options.wpmDisplayId);
    this.netWpmDisplay = document.getElementById(this.options.netWpmDisplayId);
    this.accuracyDisplay = document.getElementById(this.options.accuracyDisplayId);
    this.wordsDisplay = document.getElementById(this.options.wordsDisplayId);
    this.cpmDisplay = document.getElementById(this.options.cpmDisplayId);
    this.charsDisplay = document.getElementById(this.options.charsDisplayId);
    this.linesDisplay = document.getElementById(this.options.linesDisplayId);
    this.errorsDisplay = document.getElementById(this.options.errorsDisplayId);
    this.progressBar = document.getElementById(this.options.progressBarId);

    // Caret element
    this.caret = null;

    // Test State Variables
    this.targetText = '';
    this.wordsArray = [];
    this.charElements = [];
    this.wordElements = [];

    this.charIndex = 0;
    this.currentWordIndex = 0;
    this.totalKeystrokes = 0;
    this.correctKeystrokes = 0;
    this.errorKeystrokes = 0;
    this.backspaceCount = 0;
    this.activeErrorsCount = 0;
    this.wordsCompletedCount = 0;

    this.timerInterval = null;
    this.timeRemaining = this.options.duration;
    this.elapsedSeconds = 0;
    this.isRunning = false;
    this.isPaused = false;
    this.pauseStartTime = null;
    this.totalPausedDuration = 0;
    this.lastKeystrokeTime = null;
    this.isFinished = false;
    this.startTime = null;

    // Chart tracking series for session
    this.speedHistory = [];

    // Audio Context
    this.audioCtx = null;

    this.init();
  }

  init() {
    if (!this.container || !this.input) {
      console.error('Typing Engine: Required container or input not found.');
      return;
    }

    this.createCaret();
    this.setupEventListeners();
  }

  createCaret() {
    let existing = this.container.querySelector('.typing-caret');
    if (existing) existing.remove();

    this.caret = document.createElement('div');
    this.caret.className = `typing-caret caret-${this.options.caretStyle}`;
    this.container.appendChild(this.caret);
  }

  setupEventListeners() {
    this.handledByKeydown = false;

    // Click on container focuses the hidden input
    this.container.addEventListener('click', () => {
      this.input.focus();
      const prompt = document.getElementById('typing-focus-prompt');
      if (prompt) prompt.classList.add('hidden');
    });

    this.input.addEventListener('focus', () => {
      this.container.classList.add('focused');
      const prompt = document.getElementById('typing-focus-prompt');
      if (prompt) prompt.classList.add('hidden');
    });

    this.input.addEventListener('blur', () => {
      this.container.classList.remove('focused');
      if (!this.isFinished && this.targetText.length > 0) {
        const prompt = document.getElementById('typing-focus-prompt');
        if (prompt) prompt.classList.remove('hidden');
      }
    });

    // Handle keydown for input
    this.input.addEventListener('keydown', (e) => this.handleKeyDown(e));

    // Support mobile virtual keyboards (which emit beforeinput instead of keydown for backspace)
    this.input.addEventListener('beforeinput', (e) => {
      if (e.inputType === 'deleteContentBackward' || e.inputType === 'deleteContentForward') {
        e.preventDefault();
        if (this.options.backspaceDisabled) {
          this.playSound('error');
          if (typeof showToast === 'function') {
            showToast('warning', 'Backspace & Delete are disabled in Strict Mode!', 1200);
          }
          return;
        }
        this.handleBackspace(false);
      } else if (e.data && e.data.length === 1 && !this.handledByKeydown) {
        this.handleCharacterInput(e.data);
      }
    });

    // Keep input buffer empty so backspace never gets stuck
    this.input.addEventListener('input', () => {
      this.input.value = '';
    });
  }

  reset() {
    clearInterval(this.timerInterval);
    this.isRunning = false;
    this.isPaused = false;
    this.isFinished = false;
    this.startTime = null;
    this.pauseStartTime = null;
    this.totalPausedDuration = 0;
    this.lastKeystrokeTime = null;
    this.charIndex = 0;
    this.currentWordIndex = 0;
    this.totalKeystrokes = 0;
    this.correctKeystrokes = 0;
    this.errorKeystrokes = 0;
    this.backspaceCount = 0;
    this.activeErrorsCount = 0;
    this.wordsCompletedCount = 0;
    this.elapsedSeconds = 0;
    this.speedHistory = [];
    this.timeRemaining = this.options.duration;

    if (this.timerDisplay) {
      this.timerDisplay.textContent = this.options.mode === 'timed' ? `${this.options.duration}s` : '0:00';
    }
    if (this.wpmDisplay) this.wpmDisplay.textContent = '0';
    if (this.netWpmDisplay) this.netWpmDisplay.textContent = '0';
    if (this.accuracyDisplay) this.accuracyDisplay.textContent = '100%';
    if (this.wordsDisplay) this.wordsDisplay.textContent = '0';
    if (this.cpmDisplay) this.cpmDisplay.textContent = '0';
    if (this.charsDisplay) this.charsDisplay.textContent = '0';
    if (this.linesDisplay) this.linesDisplay.textContent = '1';
    if (this.errorsDisplay) this.errorsDisplay.textContent = '0';
    if (this.progressBar) this.progressBar.style.width = '0%';

    const pauseOverlay = document.getElementById('typing-pause-overlay');
    if (pauseOverlay) pauseOverlay.classList.add('hidden');
    const pauseBtnText = document.getElementById('pause-btn-text');
    if (pauseBtnText) pauseBtnText.innerHTML = 'Pause (<kbd class="kbd-pro">Esc</kbd>)';
    const pauseBtnIcon = document.querySelector('#btn-pause-test i');
    if (pauseBtnIcon) pauseBtnIcon.className = 'bi bi-pause-fill';
  }

  loadText(text) {
    this.reset();
    this.targetText = text.trim().replace(/\s+/g, ' ');
    this.renderText();
    this.updateLiveStats();
    this.updateCaretPosition();
    this.input.value = '';
    this.input.focus();
  }

  renderText() {
    // Clean container while keeping caret
    const caret = this.caret;
    this.container.innerHTML = '';
    this.container.appendChild(caret);

    this.charElements = [];
    this.wordElements = [];
    this.wordsArray = this.targetText.split(' ');

    let charCounter = 0;

    this.wordsArray.forEach((wordStr, wIndex) => {
      const wordSpan = document.createElement('span');
      wordSpan.className = 'word';
      wordSpan.dataset.wordIndex = wIndex;

      for (let i = 0; i < wordStr.length; i++) {
        const charSpan = document.createElement('span');
        charSpan.className = 'char untyped';
        charSpan.textContent = wordStr[i];
        charSpan.dataset.charIndex = charCounter++;
        wordSpan.appendChild(charSpan);
        this.charElements.push(charSpan);
      }

      // Add trailing space for all words except the last one
      if (wIndex < this.wordsArray.length - 1) {
        const spaceSpan = document.createElement('span');
        spaceSpan.className = 'char untyped';
        spaceSpan.textContent = ' ';
        spaceSpan.dataset.charIndex = charCounter++;
        wordSpan.appendChild(spaceSpan);
        this.charElements.push(spaceSpan);
      }

      this.container.appendChild(wordSpan);
      this.wordElements.push(wordSpan);
    });

    if (this.wordElements.length > 0) {
      this.wordElements[0].classList.add('active-word');
    }
  }

  startTest() {
    if (this.isRunning && !this.isPaused) return;
    this.isRunning = true;
    this.isPaused = false;
    this.isFinished = false;
    this.startTime = Date.now();
    this.totalPausedDuration = 0;
    this.lastKeystrokeTime = Date.now();

    clearInterval(this.timerInterval);
    this.timerInterval = setInterval(() => {
      this.tick();
    }, 200);

    const pauseOverlay = document.getElementById('typing-pause-overlay');
    if (pauseOverlay) pauseOverlay.classList.add('hidden');
    const pauseBtnText = document.getElementById('pause-btn-text');
    if (pauseBtnText) pauseBtnText.innerHTML = 'Pause (<kbd class="kbd-pro">Esc</kbd>)';
    const pauseBtnIcon = document.querySelector('#btn-pause-test i');
    if (pauseBtnIcon) pauseBtnIcon.className = 'bi bi-pause-fill';
  }

  pauseTest() {
    if (!this.isRunning || this.isPaused || this.isFinished) return;
    this.isPaused = true;
    this.pauseStartTime = Date.now();
    clearInterval(this.timerInterval);

    const pauseOverlay = document.getElementById('typing-pause-overlay');
    if (pauseOverlay) pauseOverlay.classList.remove('hidden');

    const pauseBtnText = document.getElementById('pause-btn-text');
    if (pauseBtnText) pauseBtnText.innerHTML = 'Resume (<kbd class="kbd-pro">Esc</kbd>)';
    const pauseBtnIcon = document.querySelector('#btn-pause-test i');
    if (pauseBtnIcon) pauseBtnIcon.className = 'bi bi-play-fill';

    if (typeof showToast === 'function') {
      showToast('info', 'Practice paused. Timer frozen.');
    }
  }

  resumeTest() {
    if (!this.isPaused || this.isFinished) return;
    if (this.pauseStartTime) {
      this.totalPausedDuration += (Date.now() - this.pauseStartTime);
      this.pauseStartTime = null;
    }
    this.isPaused = false;
    this.lastKeystrokeTime = Date.now();

    this.timerInterval = setInterval(() => {
      this.tick();
    }, 200);

    const pauseOverlay = document.getElementById('typing-pause-overlay');
    if (pauseOverlay) pauseOverlay.classList.add('hidden');

    const pauseBtnText = document.getElementById('pause-btn-text');
    if (pauseBtnText) pauseBtnText.innerHTML = 'Pause (<kbd class="kbd-pro">Esc</kbd>)';
    const pauseBtnIcon = document.querySelector('#btn-pause-test i');
    if (pauseBtnIcon) pauseBtnIcon.className = 'bi bi-pause-fill';

    if (this.input) this.input.focus();
    if (typeof showToast === 'function') {
      showToast('success', 'Practice resumed!');
    }
  }

  togglePause() {
    if (!this.isRunning && !this.isPaused) return;
    if (this.isPaused) {
      this.resumeTest();
    } else {
      this.pauseTest();
    }
  }

  stopTest() {
    if (!this.isRunning && !this.isPaused) return;
    this.finishTest();
  }

  tick() {
    if (!this.isRunning || this.isPaused || this.isFinished) return;

    const now = Date.now();
    this.elapsedSeconds = Math.max(0.1, (now - this.startTime - this.totalPausedDuration) / 1000.0);

    // Auto-pause if user stopped typing for > 8 seconds during an active test
    if (this.lastKeystrokeTime && (now - this.lastKeystrokeTime > 8000) && this.totalKeystrokes > 3) {
      this.pauseTest();
      return;
    }

    if (this.options.mode === 'timed') {
      this.timeRemaining = Math.max(0, this.options.duration - this.elapsedSeconds);
      if (this.timerDisplay) {
        this.timerDisplay.textContent = Math.ceil(this.timeRemaining) + 's';
      }

      if (this.timeRemaining <= 0) {
        this.finishTest();
        return;
      }
    } else {
      // Free passage mode (count up)
      if (this.timerDisplay) {
        const m = Math.floor(this.elapsedSeconds / 60);
        const s = Math.floor(this.elapsedSeconds % 60);
        this.timerDisplay.textContent = `${m}:${s < 10 ? '0' : ''}${s}`;
      }
    }

    this.updateLiveStats();

    // Record data point for progress graph every second
    if (Math.floor(this.elapsedSeconds) > this.speedHistory.length) {
      const liveNetWpm = this.calcNetWPM();
      this.speedHistory.push({
        second: Math.floor(this.elapsedSeconds),
        wpm: liveNetWpm
      });
    }
  }

  handleKeyDown(e) {
    if (this.isFinished) return;

    // Esc toggles pause
    if (e.key === 'Escape') {
      e.preventDefault();
      this.togglePause();
      return;
    }

    // Space or Enter resumes when paused
    if (this.isPaused) {
      if (e.key === ' ' || e.key === 'Enter') {
        e.preventDefault();
        this.resumeTest();
      }
      return;
    }

    this.handledByKeydown = true;
    setTimeout(() => { this.handledByKeydown = false; }, 50);

    // Support both Backspace and Delete keys (Disable if backspaceDisabled mode is active)
    if (e.key === 'Backspace' || e.key === 'Delete') {
      e.preventDefault();
      if (this.options.backspaceDisabled) {
        this.playSound('error');
        if (typeof showToast === 'function') {
          showToast('warning', 'Backspace & Delete are disabled in Strict Mode!', 1200);
        }
        return;
      }
      this.lastKeystrokeTime = Date.now();
      this.handleBackspace(e.ctrlKey || e.metaKey || e.altKey);
      return;
    }

    // Ignore modifier keys and arrows
    if (['Shift', 'Control', 'Alt', 'Meta', 'CapsLock', 'Tab', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown'].includes(e.key)) {
      return;
    }

    // Start timer on first printable keystroke
    if (!this.isRunning && e.key.length === 1 && !e.ctrlKey && !e.metaKey && !e.altKey) {
      this.startTest();
    }

    if (e.key.length === 1 && !e.ctrlKey && !e.metaKey) {
      e.preventDefault();
      this.lastKeystrokeTime = Date.now();
      this.handleCharacterInput(e.key);
    }
  }

  handleCharacterInput(key) {
    if (this.charIndex >= this.charElements.length) {
      this.finishTest();
      return;
    }

    const targetCharEl = this.charElements[this.charIndex];
    const expectedChar = targetCharEl.textContent;

    this.totalKeystrokes++;

    if (key === expectedChar) {
      targetCharEl.className = 'char correct';
      this.correctKeystrokes++;
      this.playSound('correct');
    } else {
      targetCharEl.className = 'char incorrect';
      this.errorKeystrokes++;
      this.activeErrorsCount++;
      this.playSound('error');
    }

    this.charIndex++;
    this.updateWordTracking();
    this.updateCaretPosition();
    this.updateLiveStats();

    // Check if test completed by finishing the entire passage
    if (this.charIndex >= this.charElements.length) {
      this.finishTest();
    }
  }

  handleBackspace(ctrlKey) {
    if (this.charIndex <= 0) return;

    this.backspaceCount++;
    this.playSound('backspace');

    const removeOne = () => {
      if (this.charIndex <= 0) return;
      this.charIndex--;
      const charEl = this.charElements[this.charIndex];
      if (charEl.classList.contains('incorrect')) {
        this.activeErrorsCount = Math.max(0, this.activeErrorsCount - 1);
      } else if (charEl.classList.contains('correct')) {
        this.correctKeystrokes = Math.max(0, this.correctKeystrokes - 1);
      }
      charEl.className = 'char untyped';
    };

    if (ctrlKey) {
      // Remove whole previous word
      removeOne();
      while (this.charIndex > 0 && this.charElements[this.charIndex - 1].textContent !== ' ') {
        removeOne();
      }
    } else {
      removeOne();
    }

    if (this.input) this.input.value = '';
    this.updateWordTracking();
    this.updateCaretPosition();
    this.updateLiveStats();
  }

  manualBackspace() {
    if (this.options.backspaceDisabled) {
      if (typeof showToast === 'function') {
        showToast('warning', 'Backspace & Delete are disabled in Strict Mode!', 1200);
      }
      return;
    }
    this.handleBackspace(false);
    if (this.input) this.input.focus();
  }

  /**
   * Advanced Real-Time Word Count Tracking
   * Accurately determines current word index, words completed, words remaining
   */
  updateWordTracking() {
    let currentIdx = 0;
    let countedWords = 0;

    for (let i = 0; i < this.wordElements.length; i++) {
      const wordEl = this.wordElements[i];
      const chars = wordEl.querySelectorAll('.char');
      const firstCharIdx = parseInt(chars[0].dataset.charIndex);
      const lastCharIdx = parseInt(chars[chars.length - 1].dataset.charIndex);

      if (this.charIndex >= firstCharIdx && this.charIndex <= lastCharIdx) {
        currentIdx = i;
        wordEl.classList.add('active-word');
      } else {
        wordEl.classList.remove('active-word');
      }

      // Check if word is fully typed
      if (this.charIndex > lastCharIdx) {
        countedWords++;
      }
    }

    this.currentWordIndex = currentIdx;
    this.wordsCompletedCount = countedWords;
  }

  updateCaretPosition() {
    if (!this.caret) return;

    if (this.charIndex < this.charElements.length) {
      const targetChar = this.charElements[this.charIndex];
      const charRect = targetChar.getBoundingClientRect();
      const containerRect = this.container.getBoundingClientRect();

      const top = charRect.top - containerRect.top + this.container.scrollTop;
      const left = charRect.left - containerRect.left;

      this.caret.style.top = `${top}px`;
      this.caret.style.left = `${left}px`;
      this.caret.style.height = `${charRect.height}px`;

      // Auto scroll smoothly to keep caret in view
      const scrollOffset = top - (this.container.clientHeight / 2);
      if (scrollOffset > 0) {
        this.container.scrollTo({ top: scrollOffset, behavior: 'smooth' });
      }
    } else if (this.charElements.length > 0) {
      const lastChar = this.charElements[this.charElements.length - 1];
      const charRect = lastChar.getBoundingClientRect();
      const containerRect = this.container.getBoundingClientRect();

      this.caret.style.top = `${charRect.top - containerRect.top + this.container.scrollTop}px`;
      this.caret.style.left = `${charRect.right - containerRect.left}px`;
    }
  }

  calcGrossWPM() {
    if (this.elapsedSeconds <= 0.5) return 0;
    const elapsedMinutes = this.elapsedSeconds / 60.0;
    const gross = (this.charIndex / 5.0) / elapsedMinutes;
    return Math.round(gross);
  }

  calcNetWPM() {
    if (this.elapsedSeconds <= 0.5) return 0;
    const elapsedMinutes = this.elapsedSeconds / 60.0;
    const gross = this.calcGrossWPM();
    const errorRate = this.activeErrorsCount / elapsedMinutes;
    const net = Math.max(0, gross - errorRate);
    return Math.round(net);
  }

  calcAccuracy() {
    if (this.totalKeystrokes === 0) return 100;
    const acc = (this.correctKeystrokes / this.totalKeystrokes) * 100.0;
    return Math.min(100, Math.max(0, Math.round(acc)));
  }

  /**
   * Updates all real-time visual counters
   */
  updateLiveStats() {
    const grossWpm = this.calcGrossWPM();
    const netWpm = this.calcNetWPM();
    const accuracy = this.calcAccuracy();
    const totalWords = this.wordsArray.length;

    if (this.wpmDisplay) {
      this.wpmDisplay.textContent = grossWpm;
    }
    if (this.netWpmDisplay) {
      this.netWpmDisplay.textContent = netWpm;
    }
    if (this.accuracyDisplay) {
      this.accuracyDisplay.textContent = `${accuracy}%`;
    }
    if (this.errorsDisplay) {
      this.errorsDisplay.textContent = this.activeErrorsCount;
    }

    // Real-Time Word Count
    if (this.wordsDisplay) {
      this.wordsDisplay.textContent = this.wordsCompletedCount;
    }

    // Live CPM (Characters Per Minute Speed)
    if (this.cpmDisplay) {
      const elapsedMin = Math.max(0.016, (this.elapsedSeconds || 1) / 60.0);
      const cpmVal = Math.round((this.totalKeystrokes || this.charIndex) / elapsedMin);
      this.cpmDisplay.textContent = this.elapsedSeconds > 0.5 ? cpmVal : 0;
    }

    // Live Characters Count
    if (this.charsDisplay) {
      this.charsDisplay.textContent = this.charIndex;
    }

    // Live Lines Count
    if (this.linesDisplay) {
      const currentLines = Math.max(1, Math.ceil((this.wordsCompletedCount + 1) / 10));
      this.linesDisplay.textContent = currentLines;
    }

    // Progress Bar
    if (this.progressBar && this.charElements.length > 0) {
      const progressPercent = Math.min(100, Math.round((this.charIndex / this.charElements.length) * 100));
      this.progressBar.style.width = `${progressPercent}%`;
    }
  }

  finishTest() {
    if (this.isFinished) return;
    this.isFinished = true;
    this.isRunning = false;
    clearInterval(this.timerInterval);

    // Final calculations
    const finalElapsed = Math.max(1, (Date.now() - this.startTime - this.totalPausedDuration) / 1000.0);
    this.elapsedSeconds = finalElapsed;

    const grossWpm = this.calcGrossWPM();
    const netWpm = this.calcNetWPM();
    const accuracy = this.calcAccuracy();
    const wordsTyped = this.wordsCompletedCount;
    const elapsedMinutes = finalElapsed / 60.0;
    const cpm = Math.round((this.totalKeystrokes || this.charIndex) / elapsedMinutes);
    const totalChars = this.charIndex || this.totalKeystrokes;
    const totalLines = Math.max(1, Math.ceil(this.wordsCompletedCount / 12));

    this.playSound('finish');

    const resultData = {
      passage_id: this.options.passageId,
      gross_wpm: grossWpm,
      net_wpm: netWpm,
      cpm: cpm,
      accuracy: accuracy,
      duration_seconds: this.options.duration,
      elapsed_seconds: Math.round(finalElapsed * 10) / 10,
      words_typed: wordsTyped,
      total_characters: totalChars,
      total_lines: totalLines,
      total_keystrokes: this.totalKeystrokes,
      correct_keystrokes: this.correctKeystrokes,
      error_keystrokes: this.errorKeystrokes,
      backspace_count: this.backspaceCount,
      backspace_disabled: !!this.options.backspaceDisabled,
      difficulty: this.options.difficulty,
      test_mode: this.options.mode,
      speed_history: this.speedHistory
    };

    try {
      localStorage.setItem('typemaster_last_result', JSON.stringify(resultData));
      if (typeof window.updateLastCapturedBanner === 'function') {
        window.updateLastCapturedBanner(resultData);
      }
    } catch (e) {}

    // Trigger callback
    if (typeof this.options.onFinish === 'function') {
      this.options.onFinish(resultData);
    }

    // Auto submit to backend API if CSRF token present
    this.saveResultToDatabase(resultData);
  }

  saveResultToDatabase(resultData) {
    const postPayload = {
      csrf_token: this.options.csrfToken,
      ...resultData
    };

    fetch('api/save-result.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': this.options.csrfToken
      },
      body: JSON.stringify(postPayload)
    })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          if (typeof showToast === 'function') {
            showToast('success', 'Typing result saved to your history!');
          }
        } else {
          console.warn('API Notice:', data.message);
        }
      })
      .catch(err => {
        console.error('Error saving typing result:', err);
      });
  }

  reset() {
    clearInterval(this.timerInterval);
    this.isRunning = false;
    this.isFinished = false;
    this.charIndex = 0;
    this.currentWordIndex = 0;
    this.totalKeystrokes = 0;
    this.correctKeystrokes = 0;
    this.errorKeystrokes = 0;
    this.backspaceCount = 0;
    this.activeErrorsCount = 0;
    this.wordsCompletedCount = 0;
    this.elapsedSeconds = 0;
    this.timeRemaining = this.options.duration;
    this.speedHistory = [];

    if (this.timerDisplay) {
      this.timerDisplay.textContent = this.options.mode === 'timed' ? `${this.options.duration}s` : '0:00';
    }
    if (this.wpmDisplay) this.wpmDisplay.textContent = '0';
    if (this.netWpmDisplay) this.netWpmDisplay.textContent = '0';
    if (this.accuracyDisplay) this.accuracyDisplay.textContent = '100%';
    if (this.errorsDisplay) this.errorsDisplay.textContent = '0';
    if (this.wordsDisplay) this.wordsDisplay.textContent = '0 / 0';
    if (this.progressBar) this.progressBar.style.width = '0%';

    const prompt = document.getElementById('typing-focus-prompt');
    if (prompt) prompt.classList.remove('hidden');
  }

  /**
   * Pure Web Audio API Sound Synthesizer
   * Creates crisp mechanical clicks, beeps, or chimes with zero external assets
   */
  playSound(type) {
    if (!this.options.soundEnabled || this.options.soundTheme === 'silent') {
      return;
    }

    try {
      if (!this.audioCtx) {
        const AudioContextClass = window.AudioContext || window.webkitAudioContext;
        if (!AudioContextClass) return;
        this.audioCtx = new AudioContextClass();
      }

      if (this.audioCtx.state === 'suspended') {
        this.audioCtx.resume();
      }

      const now = this.audioCtx.currentTime;

      if (type === 'correct') {
        if (this.options.soundTheme === 'mechanical') {
          // Deep mechanical clack
          const osc = this.audioCtx.createOscillator();
          const gain = this.audioCtx.createGain();
          osc.type = 'triangle';
          osc.frequency.setValueAtTime(140, now);
          osc.frequency.exponentialRampToValueAtTime(40, now + 0.04);
          gain.gain.setValueAtTime(0.18, now);
          gain.gain.exponentialRampToValueAtTime(0.01, now + 0.04);
          osc.connect(gain);
          gain.connect(this.audioCtx.destination);
          osc.start(now);
          osc.stop(now + 0.04);
        } else {
          // Tactile soft click
          const osc = this.audioCtx.createOscillator();
          const gain = this.audioCtx.createGain();
          osc.type = 'sine';
          osc.frequency.setValueAtTime(600, now);
          gain.gain.setValueAtTime(0.1, now);
          gain.gain.exponentialRampToValueAtTime(0.001, now + 0.02);
          osc.connect(gain);
          gain.connect(this.audioCtx.destination);
          osc.start(now);
          osc.stop(now + 0.02);
        }
      } else if (type === 'error') {
        // Crisp error thud/buzz
        const osc = this.audioCtx.createOscillator();
        const gain = this.audioCtx.createGain();
        osc.type = 'sawtooth';
        osc.frequency.setValueAtTime(110, now);
        osc.frequency.linearRampToValueAtTime(80, now + 0.06);
        gain.gain.setValueAtTime(0.15, now);
        gain.gain.exponentialRampToValueAtTime(0.01, now + 0.06);
        osc.connect(gain);
        gain.connect(this.audioCtx.destination);
        osc.start(now);
        osc.stop(now + 0.06);
      } else if (type === 'finish') {
        // Victorious two-tone chime
        [523.25, 659.25, 783.99].forEach((freq, idx) => {
          const osc = this.audioCtx.createOscillator();
          const gain = this.audioCtx.createGain();
          const startTone = now + (idx * 0.1);
          osc.type = 'sine';
          osc.frequency.setValueAtTime(freq, startTone);
          gain.gain.setValueAtTime(0.15, startTone);
          gain.gain.exponentialRampToValueAtTime(0.001, startTone + 0.35);
          osc.connect(gain);
          gain.connect(this.audioCtx.destination);
          osc.start(startTone);
          osc.stop(startTone + 0.35);
        });
      }
    } catch (e) {
      // Audio autoplay policy fallback
    }
  }
}

/**
 * =====================================================================
 * FreeWritingEngine - Blank Canvas "Write Anything" Engine
 * Starts with ZERO pre-existing text.
 * Counts words, characters, lines, WPM, CPM, and keystrokes dynamically.
 * Supports code indentation (Tab key) and tactile audio feedback.
 * =====================================================================
 */
class FreeWritingEngine {
  constructor(options = {}) {
    this.options = Object.assign({
      textareaId: 'free-typing-textarea',
      wordsDisplayId: 'live-free-words',
      charsDisplayId: 'live-free-chars',
      charsNoSpaceDisplayId: 'live-free-chars-nospace',
      accuracyDisplayId: 'live-free-accuracy',
      wpmDisplayId: 'live-free-wpm',
      cpmDisplayId: 'live-free-cpm',
      timerDisplayId: 'live-free-timer',
      linesDisplayId: 'live-free-lines',
      soundEnabled: true,
      soundTheme: 'mechanical',
      duration: 60,
      mode: 'stopwatch', // 'stopwatch' (count up) or 'timed' (countdown)
      csrfToken: '',
      backspaceDisabled: true, // Strict Exam Mode: Backspace/Delete Disabled by default
      onFinish: null
    }, options);

    this.textarea = document.getElementById(this.options.textareaId);
    this.wordsDisplay = document.getElementById(this.options.wordsDisplayId);
    this.charsDisplay = document.getElementById(this.options.charsDisplayId);
    this.charsNoSpaceDisplay = document.getElementById(this.options.charsNoSpaceDisplayId);
    this.accuracyDisplay = document.getElementById(this.options.accuracyDisplayId);
    this.wpmDisplay = document.getElementById(this.options.wpmDisplayId);
    this.cpmDisplay = document.getElementById(this.options.cpmDisplayId);
    this.timerDisplay = document.getElementById(this.options.timerDisplayId);
    this.linesDisplay = document.getElementById(this.options.linesDisplayId);

    this.isRunning = false;
    this.isFinished = false;
    this.startTime = null;
    this.timerInterval = null;
    this.elapsedSeconds = 0;
    this.totalKeystrokes = 0;
    this.backspaceCount = 0;
    this.audioCtx = null;
    this.speedHistory = [];

    this.init();
  }

  init() {
    if (!this.textarea) return;

    this.textarea.addEventListener('input', () => this.handleInput());
    this.textarea.addEventListener('keydown', (e) => this.handleKeyDown(e));
  }

  handleKeyDown(e) {
    if (this.isFinished) return;

    // Start timer on first printable keypress
    if (!this.isRunning && e.key.length === 1 && !e.ctrlKey && !e.metaKey) {
      this.start();
    }

    // Handle Tab key for coding indentation
    if (e.key === 'Tab') {
      e.preventDefault();
      const start = this.textarea.selectionStart;
      const end = this.textarea.selectionEnd;
      const spaces = '    '; // 4 spaces
      this.textarea.value = this.textarea.value.substring(0, start) + spaces + this.textarea.value.substring(end);
      this.textarea.selectionStart = this.textarea.selectionEnd = start + spaces.length;
      this.totalKeystrokes += 4;
      this.playSound('correct');
      this.handleInput();
      return;
    }

    if (e.key === 'Backspace' || e.key === 'Delete') {
      if (this.options.backspaceDisabled) {
        e.preventDefault();
        this.playSound('error');
        if (typeof showToast === 'function') {
          showToast('warning', 'Backspace & Delete are disabled in Strict Mode!', 1200);
        }
        return;
      }
      this.backspaceCount++;
      this.playSound('backspace');
      setTimeout(() => this.handleInput(), 10);
    } else if (e.key.length === 1 && !e.ctrlKey && !e.metaKey) {
      this.totalKeystrokes++;
      this.playSound('correct');
    }
  }

  calcAccuracy() {
    if (this.totalKeystrokes === 0) return 100;
    const effectiveCorrect = Math.max(0, this.totalKeystrokes - this.backspaceCount);
    const acc = (effectiveCorrect / this.totalKeystrokes) * 100.0;
    return Math.min(100, Math.max(0, Math.round(acc)));
  }

  manualBackspace() {
    if (!this.textarea) return;
    if (this.options.backspaceDisabled) {
      if (typeof showToast === 'function') {
        showToast('warning', 'Backspace & Delete are disabled in Strict Mode!', 1200);
      }
      return;
    }
    const start = this.textarea.selectionStart;
    const end = this.textarea.selectionEnd;
    if (start > 0 || start !== end) {
      if (start === end) {
        this.textarea.value = this.textarea.value.substring(0, start - 1) + this.textarea.value.substring(end);
        this.textarea.selectionStart = this.textarea.selectionEnd = start - 1;
      } else {
        this.textarea.value = this.textarea.value.substring(0, start) + this.textarea.value.substring(end);
        this.textarea.selectionStart = this.textarea.selectionEnd = start;
      }
      this.backspaceCount++;
      this.playSound('backspace');
      this.handleInput();
    }
    this.textarea.focus();
  }

  handleInput() {
    const text = this.textarea.value;

    // Advanced Word Count Tokenization
    const trimmed = text.trim();
    const words = trimmed ? trimmed.split(/\s+/).filter(w => w.length > 0) : [];
    const wordCount = words.length;

    const charCount = text.length;
    const charNoSpaceCount = text.replace(/\s/g, '').length;
    const lineCount = text.length > 0 ? text.split(/\r\n|\r|\n/).length : 0;
    const accuracy = this.calcAccuracy();

    // Update Word & Character HUD Displays
    if (this.wordsDisplay) this.wordsDisplay.textContent = wordCount;
    if (this.charsDisplay) this.charsDisplay.textContent = charCount;
    if (this.charsNoSpaceDisplay) this.charsNoSpaceDisplay.textContent = charNoSpaceCount;
    if (this.linesDisplay) this.linesDisplay.textContent = lineCount;
    if (this.accuracyDisplay) this.accuracyDisplay.textContent = `${accuracy}%`;

    // Update Live WPM & CPM
    this.updateSpeedMetrics(charCount);
  }

  start() {
    if (this.isRunning) return;
    this.isRunning = true;
    this.isFinished = false;
    this.startTime = Date.now();

    this.timerInterval = setInterval(() => {
      this.tick();
    }, 250);
  }

  tick() {
    if (!this.isRunning || this.isFinished) return;

    this.elapsedSeconds = (Date.now() - this.startTime) / 1000.0;

    if (this.options.mode === 'timed') {
      const remaining = Math.max(0, this.options.duration - this.elapsedSeconds);
      if (this.timerDisplay) {
        this.timerDisplay.textContent = Math.ceil(remaining) + 's';
      }
      if (remaining <= 0) {
        this.finish();
        return;
      }
    } else {
      // Stopwatch mode
      if (this.timerDisplay) {
        const m = Math.floor(this.elapsedSeconds / 60);
        const s = Math.floor(this.elapsedSeconds % 60);
        this.timerDisplay.textContent = `${m}:${s < 10 ? '0' : ''}${s}`;
      }
    }

    this.updateSpeedMetrics(this.textarea.value.length);

    if (Math.floor(this.elapsedSeconds) > this.speedHistory.length) {
      this.speedHistory.push({
        second: Math.floor(this.elapsedSeconds),
        wpm: this.calcGrossWPM(this.textarea.value.length)
      });
    }
  }

  calcGrossWPM(charCount) {
    if (this.elapsedSeconds <= 0.5) return 0;
    const elapsedMinutes = this.elapsedSeconds / 60.0;
    const gross = (charCount / 5.0) / elapsedMinutes;
    return Math.round(gross);
  }

  calcCPM(charCount) {
    if (this.elapsedSeconds <= 0.5) return 0;
    const elapsedMinutes = this.elapsedSeconds / 60.0;
    return Math.round(charCount / elapsedMinutes);
  }

  updateSpeedMetrics(charCount) {
    const grossWpm = this.calcGrossWPM(charCount);
    const cpm = this.calcCPM(charCount);

    if (this.wpmDisplay) this.wpmDisplay.textContent = grossWpm;
    if (this.cpmDisplay) this.cpmDisplay.textContent = cpm;
  }

  finish() {
    if (this.isFinished) return;
    this.isFinished = true;
    this.isRunning = false;
    clearInterval(this.timerInterval);

    const text = this.textarea.value;
    const trimmed = text.trim();
    const words = trimmed ? trimmed.split(/\s+/).filter(w => w.length > 0) : [];
    const finalElapsed = Math.max(1, (Date.now() - (this.startTime || Date.now())) / 1000.0);
    this.elapsedSeconds = finalElapsed;

    const grossWpm = this.calcGrossWPM(text.length);
    const elapsedMinutes = finalElapsed / 60.0;
    const errorsPerMin = elapsedMinutes > 0 ? (this.backspaceCount / elapsedMinutes) : 0;
    const netWpm = Math.max(0, Math.round(grossWpm - errorsPerMin));
    const wordsTyped = words.length;

    const cpm = this.calcCPM(text.length);
    const totalLines = text.length > 0 ? text.split(/\r\n|\r|\n/).length : 0;

    const resultData = {
      passage_id: null,
      gross_wpm: grossWpm,
      net_wpm: netWpm,
      cpm: cpm,
      accuracy: this.calcAccuracy(),
      duration_seconds: Math.round(finalElapsed),
      elapsed_seconds: Math.round(finalElapsed * 10) / 10,
      words_typed: wordsTyped,
      total_characters: text.length,
      total_lines: totalLines,
      total_keystrokes: this.totalKeystrokes || text.length,
      correct_keystrokes: Math.max(0, (this.totalKeystrokes || text.length) - this.backspaceCount),
      error_keystrokes: this.backspaceCount,
      backspace_count: this.backspaceCount,
      backspace_disabled: !!this.options.backspaceDisabled,
      difficulty: 'custom',
      test_mode: 'custom',
      speed_history: this.speedHistory
    };

    try {
      localStorage.setItem('typemaster_last_result', JSON.stringify(resultData));
      if (typeof window.updateLastCapturedBanner === 'function') {
        window.updateLastCapturedBanner(resultData);
      }
    } catch (e) {}

    if (typeof this.options.onFinish === 'function') {
      this.options.onFinish(resultData);
    }

    this.saveResultToDatabase(resultData);
  }

  saveResultToDatabase(resultData) {
    if (!this.options.csrfToken) return;

    fetch('api/save-result.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': this.options.csrfToken
      },
      body: JSON.stringify({
        csrf_token: this.options.csrfToken,
        ...resultData
      })
    })
      .then(res => res.json())
      .then(data => {
        if (data.success && typeof showToast === 'function') {
          showToast('success', 'Free writing session saved to your history!');
        }
      })
      .catch(() => { });
  }

  clear() {
    clearInterval(this.timerInterval);
    this.isRunning = false;
    this.isFinished = false;
    this.startTime = null;
    this.elapsedSeconds = 0;
    this.totalKeystrokes = 0;
    this.backspaceCount = 0;
    this.speedHistory = [];
    if (this.textarea) this.textarea.value = '';
    if (this.wordsDisplay) this.wordsDisplay.textContent = '0';
    if (this.charsDisplay) this.charsDisplay.textContent = '0';
    if (this.charsNoSpaceDisplay) this.charsNoSpaceDisplay.textContent = '0';
    if (this.linesDisplay) this.linesDisplay.textContent = '0';
    if (this.wpmDisplay) this.wpmDisplay.textContent = '0';
    if (this.cpmDisplay) this.cpmDisplay.textContent = '0';
    if (this.timerDisplay) this.timerDisplay.textContent = this.options.mode === 'timed' ? `${this.options.duration}s` : '0:00';
    this.textarea.focus();
  }

  playSound(type) {
    if (!this.options.soundEnabled || this.options.soundTheme === 'silent') return;

    try {
      if (!this.audioCtx) {
        const AudioContextClass = window.AudioContext || window.webkitAudioContext;
        if (!AudioContextClass) return;
        this.audioCtx = new AudioContextClass();
      }
      if (this.audioCtx.state === 'suspended') {
        this.audioCtx.resume();
      }
      const now = this.audioCtx.currentTime;

      if (type === 'correct') {
        const osc = this.audioCtx.createOscillator();
        const gain = this.audioCtx.createGain();
        osc.type = 'triangle';
        osc.frequency.setValueAtTime(140, now);
        osc.frequency.exponentialRampToValueAtTime(45, now + 0.035);
        gain.gain.setValueAtTime(0.15, now);
        gain.gain.exponentialRampToValueAtTime(0.01, now + 0.035);
        osc.connect(gain);
        gain.connect(this.audioCtx.destination);
        osc.start(now);
        osc.stop(now + 0.035);
      } else if (type === 'backspace') {
        const osc = this.audioCtx.createOscillator();
        const gain = this.audioCtx.createGain();
        osc.type = 'sawtooth';
        osc.frequency.setValueAtTime(90, now);
        gain.gain.setValueAtTime(0.1, now);
        gain.gain.exponentialRampToValueAtTime(0.01, now + 0.03);
        osc.connect(gain);
        gain.connect(this.audioCtx.destination);
        osc.start(now);
        osc.stop(now + 0.03);
      } else if (type === 'finish') {
        [523.25, 659.25, 783.99].forEach((freq, idx) => {
          const osc = this.audioCtx.createOscillator();
          const gain = this.audioCtx.createGain();
          const startTone = now + (idx * 0.1);
          osc.type = 'sine';
          osc.frequency.setValueAtTime(freq, startTone);
          gain.gain.setValueAtTime(0.12, startTone);
          gain.gain.exponentialRampToValueAtTime(0.001, startTone + 0.3);
          osc.connect(gain);
          gain.connect(this.audioCtx.destination);
          osc.start(startTone);
          osc.stop(startTone + 0.3);
        });
      }
    } catch (e) { }
  }
}

