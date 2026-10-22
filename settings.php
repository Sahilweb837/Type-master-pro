<?php
/**
 * Custom Type Master Pro
 * User Customization & Preferences Settings
 */

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/functions.php';

require_login();

$user = current_user();
$userId = $user['id'];
$pdo = getDBConnection();

$settings = get_user_settings($userId);
$successMsg = '';
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token()) {
        $errorMsg = 'Security validation failed (invalid CSRF token).';
    } else {
        $theme = trim($_POST['theme'] ?? 'light');
        $fontFamily = trim($_POST['font_family'] ?? 'JetBrains Mono');
        $fontSize = max(14, min(36, (int)($_POST['font_size'] ?? 22)));
        $lineHeight = max(1.2, min(2.5, (float)($_POST['line_height'] ?? 1.8)));
        $caretStyle = trim($_POST['caret_style'] ?? 'line');
        $caretColor = trim($_POST['caret_color'] ?? '#38bdf8');
        $correctColor = trim($_POST['correct_color'] ?? '#4ade80');
        $incorrectColor = trim($_POST['incorrect_color'] ?? '#f87171');
        $typingAreaWidth = trim($_POST['typing_area_width'] ?? '920px');
        $soundEnabled = isset($_POST['sound_enabled']) ? 1 : 0;
        $soundTheme = trim($_POST['sound_theme'] ?? 'mechanical');
        $defaultDuration = (int)($_POST['default_duration'] ?? 60);
        $defaultDifficulty = trim($_POST['default_difficulty'] ?? 'intermediate');

        if ($pdo) {
            try {
                $stmt = $pdo->prepare("
                    UPDATE user_settings SET
                        theme = :theme,
                        font_family = :ff,
                        font_size = :fs,
                        line_height = :lh,
                        caret_style = :cs,
                        caret_color = :cc,
                        correct_color = :cor,
                        incorrect_color = :inc,
                        typing_area_width = :w,
                        sound_enabled = :se,
                        sound_theme = :st,
                        default_duration = :dd,
                        default_difficulty = :diff
                    WHERE user_id = :uid
                ");
                $stmt->execute([
                    ':theme' => $theme,
                    ':ff'    => $fontFamily,
                    ':fs'    => $fontSize,
                    ':lh'    => $lineHeight,
                    ':cs'    => $caretStyle,
                    ':cc'    => $caretColor,
                    ':cor'   => $correctColor,
                    ':inc'   => $incorrectColor,
                    ':w'     => $typingAreaWidth,
                    ':se'    => $soundEnabled,
                    ':st'    => $soundTheme,
                    ':dd'    => $defaultDuration,
                    ':diff'  => $defaultDifficulty,
                    ':uid'   => $userId
                ]);

                set_flash('success', 'Your typing preferences have been successfully updated and saved!');
                header('Location: ' . BASE_URL . '/settings.php');
                exit;
            } catch (Exception $e) {
                error_log('Settings update error: ' . $e->getMessage());
                $errorMsg = 'Failed to save settings. Please try again.';
            }
        }
    }
}

$pageTitle = 'Customization Settings';
include __DIR__ . '/includes/header.php';
?>

<div class="container-fluid px-md-4 py-3">

  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
      <h2 class="fw-bold text-light mb-1"><i class="bi bi-sliders text-info me-2"></i>Customization Settings</h2>
      <p class="text-secondary small mb-0">Fine-tune your visual environment, typography, caret behavior, and audio cues.</p>
    </div>
    <a href="<?= BASE_URL ?>/practice.php" class="btn btn-outline-info btn-sm">
      <i class="bi bi-play-circle me-1"></i> Test in Practice Arena
    </a>
  </div>

  <?php if (!empty($errorMsg)): ?>
    <div class="alert alert-danger mb-4"><?= e($errorMsg) ?></div>
  <?php endif; ?>

  <form method="POST" action="<?= BASE_URL ?>/settings.php">
    <?= csrf_field() ?>

    <div class="row g-4">
      <!-- Left Column: Controls -->
      <div class="col-lg-7">
        <!-- Visual Theme -->
        <div class="glass-card p-4 mb-4">
          <h5 class="fw-bold text-light mb-3"><i class="bi bi-palette text-info me-2"></i>Theme & Visual Identity</h5>
          <div class="row g-3">
            <div class="col-sm-6">
              <label class="form-label text-secondary small fw-bold">Active Theme</label>
              <select name="theme" id="settingTheme" class="form-select bg-dark text-light border-secondary">
                <option value="light" <?= ($settings['theme'] === 'light' || $settings['theme'] === 'white-orange') ? 'selected' : '' ?>>Soft UI White (Signature Coral)</option>
                <option value="orange" <?= ($settings['theme'] === 'orange') ? 'selected' : '' ?>>Dark Orange (Signature Brand)</option>
                <option value="dark" <?= ($settings['theme'] === 'dark') ? 'selected' : '' ?>>Dark Obsidian</option>
                <option value="cyberpunk" <?= ($settings['theme'] === 'cyberpunk') ? 'selected' : '' ?>>Cyberpunk Neon</option>
                <option value="nord" <?= ($settings['theme'] === 'nord') ? 'selected' : '' ?>>Nord Arctic</option>
              </select>
            </div>
            <div class="col-sm-6">
              <label class="form-label text-secondary small fw-bold">Typing Arena Width</label>
              <select name="typing_area_width" id="settingWidth" class="form-select bg-dark text-light border-secondary">
                <option value="750px" <?= ($settings['typing_area_width'] === '750px') ? 'selected' : '' ?>>Narrow (750px)</option>
                <option value="850px" <?= ($settings['typing_area_width'] === '850px') ? 'selected' : '' ?>>Standard (850px)</option>
                <option value="920px" <?= ($settings['typing_area_width'] === '920px') ? 'selected' : '' ?>>Comfortable (920px)</option>
                <option value="1100px" <?= ($settings['typing_area_width'] === '1100px') ? 'selected' : '' ?>>Wide (1100px)</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Typography & Caret -->
        <div class="glass-card p-4 mb-4">
          <h5 class="fw-bold text-light mb-3"><i class="bi bi-fonts text-warning me-2"></i>Typography & Caret</h5>
          <div class="row g-3 mb-3">
            <div class="col-sm-6">
              <label class="form-label text-secondary small fw-bold">Font Family</label>
              <select name="font_family" id="settingFont" class="form-select bg-dark text-light border-secondary">
                <option value="JetBrains Mono" <?= ($settings['font_family'] === 'JetBrains Mono') ? 'selected' : '' ?>>JetBrains Mono</option>
                <option value="Fira Code" <?= ($settings['font_family'] === 'Fira Code') ? 'selected' : '' ?>>Fira Code</option>
                <option value="Roboto Mono" <?= ($settings['font_family'] === 'Roboto Mono') ? 'selected' : '' ?>>Roboto Mono</option>
                <option value="Inter" <?= ($settings['font_family'] === 'Inter') ? 'selected' : '' ?>>Inter (Proportional)</option>
              </select>
            </div>
            <div class="col-sm-3 col-6">
              <label class="form-label text-secondary small fw-bold">Font Size (px)</label>
              <input type="number" name="font_size" id="settingFontSize" class="form-control bg-dark text-light border-secondary" min="14" max="36" value="<?= (int)$settings['font_size'] ?>">
            </div>
            <div class="col-sm-3 col-6">
              <label class="form-label text-secondary small fw-bold">Line Height</label>
              <input type="number" step="0.1" name="line_height" id="settingLineHeight" class="form-control bg-dark text-light border-secondary" min="1.2" max="2.5" value="<?= (float)$settings['line_height'] ?>">
            </div>
          </div>

          <div class="row g-3">
            <div class="col-sm-6">
              <label class="form-label text-secondary small fw-bold">Caret Cursor Style</label>
              <select name="caret_style" id="settingCaretStyle" class="form-select bg-dark text-light border-secondary">
                <option value="line" <?= ($settings['caret_style'] === 'line') ? 'selected' : '' ?>>Smooth Line (|)</option>
                <option value="block" <?= ($settings['caret_style'] === 'block') ? 'selected' : '' ?>>Block (█)</option>
                <option value="underline" <?= ($settings['caret_style'] === 'underline') ? 'selected' : '' ?>>Underline (_)</option>
              </select>
            </div>
            <div class="col-sm-6">
              <label class="form-label text-secondary small fw-bold">Caret Color</label>
              <div class="input-group">
                <input type="color" name="caret_color" id="settingCaretColor" class="form-control form-control-color bg-dark border-secondary" value="<?= e($settings['caret_color']) ?>">
                <input type="text" class="form-control bg-dark text-light border-secondary font-monospace" id="settingCaretColorText" value="<?= e($settings['caret_color']) ?>" readonly>
              </div>
            </div>
          </div>
        </div>

        <!-- Character Colors -->
        <div class="glass-card p-4 mb-4">
          <h5 class="fw-bold text-light mb-3"><i class="bi bi-palette2 text-success me-2"></i>Character Feedback Colors</h5>
          <div class="row g-3">
            <div class="col-sm-6">
              <label class="form-label text-secondary small fw-bold">Correct Character Color</label>
              <div class="input-group">
                <input type="color" name="correct_color" id="settingCorrectColor" class="form-control form-control-color bg-dark border-secondary" value="<?= e($settings['correct_color']) ?>">
                <input type="text" class="form-control bg-dark text-light border-secondary font-monospace" id="settingCorrectColorText" value="<?= e($settings['correct_color']) ?>" readonly>
              </div>
            </div>
            <div class="col-sm-6">
              <label class="form-label text-secondary small fw-bold">Incorrect Character Color</label>
              <div class="input-group">
                <input type="color" name="incorrect_color" id="settingIncorrectColor" class="form-control form-control-color bg-dark border-secondary" value="<?= e($settings['incorrect_color']) ?>">
                <input type="text" class="form-control bg-dark text-light border-secondary font-monospace" id="settingIncorrectColorText" value="<?= e($settings['incorrect_color']) ?>" readonly>
              </div>
            </div>
          </div>
        </div>

        <!-- Audio & Defaults -->
        <div class="glass-card p-4 mb-4">
          <h5 class="fw-bold text-light mb-3"><i class="bi bi-volume-up text-primary me-2"></i>Sound & Defaults</h5>
          <div class="row g-3 mb-3">
            <div class="col-sm-6">
              <div class="form-check form-switch pt-2">
                <input class="form-check-input" type="checkbox" role="switch" name="sound_enabled" id="settingSoundEnabled" value="1" <?= (!empty($settings['sound_enabled'])) ? 'checked' : '' ?>>
                <label class="form-check-label text-light fw-semibold" for="settingSoundEnabled">Enable Audio Cues</label>
              </div>
              <div class="form-text text-muted">Tactile keystroke sounds synthesized in real-time.</div>
            </div>
            <div class="col-sm-6">
              <label class="form-label text-secondary small fw-bold">Sound Theme</label>
              <select name="sound_theme" class="form-select bg-dark text-light border-secondary">
                <option value="mechanical" <?= ($settings['sound_theme'] === 'mechanical') ? 'selected' : '' ?>>Mechanical Clack</option>
                <option value="click" <?= ($settings['sound_theme'] === 'click') ? 'selected' : '' ?>>Tactile Click</option>
                <option value="beep" <?= ($settings['sound_theme'] === 'beep') ? 'selected' : '' ?>>Digital Beep</option>
                <option value="silent" <?= ($settings['sound_theme'] === 'silent') ? 'selected' : '' ?>>Silent</option>
              </select>
            </div>
          </div>

          <div class="row g-3">
            <div class="col-sm-6">
              <label class="form-label text-secondary small fw-bold">Default Test Duration</label>
              <select name="default_duration" class="form-select bg-dark text-light border-secondary">
                <option value="15" <?= ($settings['default_duration'] == 15) ? 'selected' : '' ?>>15 Seconds</option>
                <option value="30" <?= ($settings['default_duration'] == 30) ? 'selected' : '' ?>>30 Seconds</option>
                <option value="60" <?= ($settings['default_duration'] == 60) ? 'selected' : '' ?>>60 Seconds (Standard)</option>
                <option value="120" <?= ($settings['default_duration'] == 120) ? 'selected' : '' ?>>120 Seconds</option>
                <option value="300" <?= ($settings['default_duration'] == 300) ? 'selected' : '' ?>>300 Seconds</option>
              </select>
            </div>
            <div class="col-sm-6">
              <label class="form-label text-secondary small fw-bold">Default Difficulty Level</label>
              <select name="default_difficulty" class="form-select bg-dark text-light border-secondary">
                <option value="beginner" <?= ($settings['default_difficulty'] === 'beginner') ? 'selected' : '' ?>>Beginner</option>
                <option value="intermediate" <?= ($settings['default_difficulty'] === 'intermediate') ? 'selected' : '' ?>>Intermediate</option>
                <option value="advanced" <?= ($settings['default_difficulty'] === 'advanced') ? 'selected' : '' ?>>Advanced</option>
              </select>
            </div>
          </div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg fw-bold px-4 py-2 shadow-sm">
          <i class="bi bi-save2-fill me-1"></i> Save All Preferences
        </button>
      </div>

      <!-- Right Column: Live Interactive Preview -->
      <div class="col-lg-5">
        <div class="glass-card p-4 sticky-top" style="top: 90px;">
          <h5 class="fw-bold text-light mb-3"><i class="bi bi-eye text-info me-2"></i>Live Arena Preview</h5>
          <p class="text-secondary small mb-3">Preview updates dynamically as you change your settings.</p>

          <div id="previewTypingBox" class="p-4 rounded border font-monospace mb-3 position-relative" style="background: var(--bg-card); min-height: 180px; overflow: hidden;">
            <span id="previewCorrect" class="text-success">The quick brown fox </span>
            <span id="previewIncorrect" class="text-danger bg-danger-subtle px-1">jumps </span>
            <span id="previewCaret" style="border-left: 2px solid #38bdf8; margin-right: 2px;"></span>
            <span id="previewUntyped" class="text-secondary">over the lazy dog while practicing on Type Master Pro.</span>
          </div>

          <div class="d-flex justify-content-between align-items-center text-muted small">
            <span>Font: <span id="previewFontLabel" class="text-light fw-bold">JetBrains Mono</span></span>
            <span>Size: <span id="previewSizeLabel" class="text-light fw-bold">22px</span></span>
          </div>
        </div>
      </div>
    </div>
  </form>

</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const fontSelect = document.getElementById('settingFont');
  const sizeInput = document.getElementById('settingFontSize');
  const lhInput = document.getElementById('settingLineHeight');
  const caretColorInput = document.getElementById('settingCaretColor');
  const caretColorText = document.getElementById('settingCaretColorText');
  const corColorInput = document.getElementById('settingCorrectColor');
  const corColorText = document.getElementById('settingCorrectColorText');
  const incColorInput = document.getElementById('settingIncorrectColor');
  const incColorText = document.getElementById('settingIncorrectColorText');
  const themeSelect = document.getElementById('settingTheme');

  const previewBox = document.getElementById('previewTypingBox');
  const previewCorrect = document.getElementById('previewCorrect');
  const previewIncorrect = document.getElementById('previewIncorrect');
  const previewCaret = document.getElementById('previewCaret');
  const previewFontLabel = document.getElementById('previewFontLabel');
  const previewSizeLabel = document.getElementById('previewSizeLabel');

  function updatePreview() {
    previewBox.style.fontFamily = fontSelect.value + ', monospace';
    previewBox.style.fontSize = sizeInput.value + 'px';
    previewBox.style.lineHeight = lhInput.value;

    previewCorrect.style.color = corColorInput.value;
    previewIncorrect.style.color = incColorInput.value;
    previewCaret.style.borderLeftColor = caretColorInput.value;

    caretColorText.value = caretColorInput.value;
    corColorText.value = corColorInput.value;
    incColorText.value = incColorInput.value;

    previewFontLabel.textContent = fontSelect.value;
    previewSizeLabel.textContent = sizeInput.value + 'px';
  }

  [fontSelect, sizeInput, lhInput, caretColorInput, corColorInput, incColorInput].forEach(el => {
    el.addEventListener('input', updatePreview);
  });

  themeSelect.addEventListener('change', () => {
    setTheme(themeSelect.value);
  });

  updatePreview();
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
