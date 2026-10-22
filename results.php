<?php
/**
 * Custom Type Master Pro
 * Individual Test Result Detail View
 */

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/functions.php';

require_login();

$user = current_user();
$resultId = (int)($_GET['id'] ?? 0);

if (!$resultId) {
    set_flash('danger', 'Invalid session result ID provided.');
    header('Location: ' . BASE_URL . '/history.php');
    exit;
}

$pdo = getDBConnection();
$result = null;

if ($pdo) {
    try {
        $stmt = $pdo->prepare("
            SELECT r.*, p.title AS passage_title, p.content AS passage_content 
            FROM typing_results r
            LEFT JOIN typing_passages p ON r.passage_id = p.id
            WHERE r.id = :id AND (r.user_id = :uid OR :is_admin = 1)
            LIMIT 1
        ");
        $stmt->execute([
            ':id'       => $resultId,
            ':uid'      => $user['id'],
            ':is_admin' => is_admin() ? 1 : 0
        ]);
        $result = $stmt->fetch();
    } catch (Exception $e) {
        error_log('Error loading result: ' . $e->getMessage());
    }
}

if (!$result) {
    set_flash('danger', 'Session record not found or access denied.');
    header('Location: ' . BASE_URL . '/history.php');
    exit;
}

$rating = get_speed_rating((float)$result['net_wpm']);

$pageTitle = 'Session Report #' . $result['id'];
include __DIR__ . '/includes/header.php';
?>

<div class="container py-4">

  <!-- =====================================================================
       LAZY SKELETON LOADING SHIMMER WRAPPER (RESULTS REPORT)
       ===================================================================== -->
  <div id="results-skeleton-view" class="skeleton-wrapper">
    <!-- Skeleton Action Bar -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div class="skeleton" style="width: 140px; height: 34px; border-radius: 6px;"></div>
      <div class="d-flex gap-2">
        <div class="skeleton" style="width: 160px; height: 34px; border-radius: 6px;"></div>
        <div class="skeleton" style="width: 130px; height: 34px; border-radius: 6px;"></div>
      </div>
    </div>
    <!-- Skeleton Hero Cards -->
    <div class="glass-card p-4 p-md-5 mb-4 skeleton-card">
      <div class="skeleton skeleton-title mx-auto mb-3" style="width: 35%;"></div>
      <div class="skeleton skeleton-text mx-auto mb-4" style="width: 25%;"></div>
      <div class="row g-3 mb-4">
        <?php for ($i = 0; $i < 3; $i++): ?>
          <div class="col-md-4">
            <div class="skeleton" style="height: 120px; border-radius: 14px;"></div>
          </div>
        <?php endfor; ?>
      </div>
      <!-- Skeleton 7 Core Metric Cards -->
      <div class="row g-3">
        <?php for ($i = 0; $i < 7; $i++): ?>
          <div class="col-sm-6 col-lg-3">
            <div class="skeleton" style="height: 90px; border-radius: 12px;"></div>
          </div>
        <?php endfor; ?>
      </div>
    </div>
  </div>

  <!-- Real Results Content View -->
  <div id="results-content-view" style="display: none;">
    <div class="d-flex align-items-center justify-content-between mb-4">
      <a href="<?= BASE_URL ?>/history.php" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-1">
        <i class="bi bi-arrow-left"></i>
        <span>Back to History</span>
      </a>
      <div class="d-flex gap-2 flex-wrap">
        <button type="button" class="btn soft-btn-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#resultsCertificateModal">
          <i class="bi bi-award-fill me-1"></i> Get Certificate
        </button>
        <?php if (!empty($result['passage_id'])): ?>
          <a href="<?= BASE_URL ?>/practice.php?passage_id=<?= (int)$result['passage_id'] ?>" class="btn btn-primary btn-sm fw-bold">
            <i class="bi bi-play-circle-fill me-1"></i> Retype This Passage
          </a>
        <?php endif; ?>
        <button type="button" class="btn btn-outline-info btn-sm" onclick="copyShareText()">
          <i class="bi bi-share-fill me-1"></i> Copy Summary
        </button>
      </div>
    </div>

    <div class="glass-card p-4 p-md-5 mb-4">
    <div class="text-center mb-4">
      <div class="small text-muted text-uppercase fw-bold letter-spacing-1">Session Report</div>
      <h2 class="fw-bold text-light mb-2"><?= e($result['passage_title'] ?? 'Custom Text Session') ?></h2>
      <div class="d-flex justify-content-center align-items-center gap-2">
        <?= get_difficulty_badge($result['difficulty']) ?>
        <span class="badge px-3 py-1" style="background-color: <?= $rating['color'] ?>;">
          <i class="bi <?= $rating['icon'] ?> me-1"></i><?= $rating['label'] ?>
        </span>
        <span class="text-muted small">• Completed on <?= date('M j, Y \a\t g:i A', strtotime($result['created_at'])) ?></span>
      </div>
    </div>

    <!-- Hero Speed Metric -->
    <div class="row g-4 mb-4 justify-content-center">
      <div class="col-md-4">
        <div class="result-hero-metric">
          <div class="result-hero-val"><?= (float)$result['net_wpm'] ?></div>
          <div class="result-hero-lbl">Net Words Per Minute</div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="result-hero-metric">
          <div class="result-hero-val text-info" style="background: none; -webkit-text-fill-color: initial;"><?= (float)$result['gross_wpm'] ?></div>
          <div class="result-hero-lbl">Gross Words Per Minute</div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="result-hero-metric">
          <div class="result-hero-val text-success" style="background: none; -webkit-text-fill-color: initial;"><?= (float)$result['accuracy'] ?>%</div>
          <div class="result-hero-lbl">Accuracy Percentage</div>
        </div>
      </div>
    </div>

    <!-- The 7 Core Metrics Showing Cards Grid (Symmetrical 4x2 Luxury Layout) -->
    <?php
      $elapsedSec = (int)round((float)$result['elapsed_seconds']);
      $elMin = floor($elapsedSec / 60);
      $elSec = $elapsedSec % 60;
      $timerFormatted = sprintf('%d:%02d', $elMin, $elSec);
      $totalCharacters = !empty($result['total_keystrokes']) ? (int)$result['total_keystrokes'] : ((int)$result['words_typed'] * 5);
      $cpmSpeed = round($totalCharacters / max(0.1, ($elapsedSec / 60.0)));
      $totalLines = max(1, (int)ceil((int)$result['words_typed'] / 12));
      $totKeys = (int)($result['total_keystrokes'] ?? $totalCharacters);
      $corKeys = (int)($result['correct_keystrokes'] ?? $totKeys);
      $qualityScore = min(100, max(0, round(($corKeys / max(1, $totKeys)) * 100)));
    ?>
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
          <div class="showing-card-num text-warning font-monospace"><?= $timerFormatted ?></div>
          <div class="showing-card-sub"><?= $elapsedSec ?>s duration</div>
        </div>
      </div>

      <!-- 2. Real-Time Words -->
      <div class="col-6 col-md-3">
        <div class="stat-showing-card p-3 text-center h-100 border-start border-3 border-success">
          <div class="showing-card-icon mx-auto bg-success-subtle text-success">
            <i class="bi bi-fonts"></i>
          </div>
          <div class="showing-card-lbl">2. Real-Time Words</div>
          <div class="showing-card-num text-success font-monospace"><?= (int)$result['words_typed'] ?></div>
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
          <div class="showing-card-num text-info font-monospace"><?= (float)$result['accuracy'] ?>%</div>
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
          <div class="showing-card-num text-light font-monospace"><?= (float)$result['gross_wpm'] ?></div>
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
          <div class="showing-card-num text-warning font-monospace"><?= $cpmSpeed ?></div>
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
          <div class="showing-card-num text-light font-monospace"><?= $totalCharacters ?></div>
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
          <div class="showing-card-num text-warning font-monospace"><?= $totalLines ?></div>
          <div class="showing-card-sub">Paragraph volume</div>
        </div>
      </div>

      <!-- 8. Keystroke Quality -->
      <div class="col-6 col-md-3">
        <div class="stat-showing-card p-3 text-center h-100 border-start border-3 border-success">
          <div class="showing-card-icon mx-auto bg-success-subtle text-success">
            <i class="bi bi-shield-check"></i>
          </div>
          <div class="showing-card-lbl">Keystroke Quality</div>
          <div class="showing-card-num text-success font-monospace"><?= $qualityScore ?>%</div>
          <div class="showing-card-sub">Error-free execution</div>
        </div>
      </div>
    </div>

    <!-- Keystroke Detailed Diagnostic Bar -->
    <div class="showing-diagnostics-bar p-3 d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
      <div class="d-flex align-items-center gap-2">
        <i class="bi bi-cpu-fill text-warning fs-5"></i>
        <span class="small fw-bold text-light">Keystroke Precision Diagnostics:</span>
      </div>
      <div class="d-flex align-items-center gap-4 text-muted small font-monospace flex-wrap">
        <span>Total Keys: <strong class="text-light fs-6"><?= (int)$result['total_keystrokes'] ?></strong></span>
        <span>Correct: <strong class="text-success fs-6"><?= (int)$result['correct_keystrokes'] ?></strong></span>
        <span>Errors: <strong class="text-danger fs-6"><?= (int)$result['error_keystrokes'] ?></strong></span>
        <span>Backspaces: <strong class="text-warning fs-6"><?= (int)$result['backspace_count'] ?></strong></span>
        <span class="badge border border-danger text-danger bg-danger-subtle"><i class="bi bi-slash-circle-fill me-1"></i>Exam Rules Enforced</span>
      </div>
    </div>

    <!-- Original Passage Preview -->
    <?php if (!empty($result['passage_content'])): ?>
      <div class="mt-4 pt-4 border-top border-secondary">
        <h6 class="fw-bold text-light mb-2"><i class="bi bi-file-text text-info me-2"></i>Passage Text:</h6>
        <div class="p-3 rounded bg-dark border border-secondary text-secondary font-monospace" style="line-height: 1.8;">
          <?= nl2br(e($result['passage_content'])) ?>
        </div>
      </div>
    <?php endif; ?>
  </div> <!-- closes glass-card -->

  <!-- =====================================================================
       Official Typing Proficiency Certificate Modal (Session Report)
       ===================================================================== -->
  <div class="modal fade" id="resultsCertificateModal" tabindex="-1" aria-labelledby="resultsCertificateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
      <div class="modal-content glass-card p-0 border-secondary overflow-hidden">
        <div class="modal-header border-secondary p-3 px-4">
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-award-fill text-warning fs-4"></i>
            <h5 class="modal-title fw-bold text-light mb-0" id="resultsCertificateModalLabel">Official Typing Proficiency Certificate</h5>
          </div>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body p-4 bg-dark">
          <!-- Printable Certificate Box -->
          <div class="certificate-preview-box" id="printable-certificate-report">
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
              <div class="cert-candidate-name">
                <?= e($user['username'] ?? 'Candidate Typist') ?>
              </div>
              <p class="text-secondary small mt-1 mb-4" style="max-width: 620px; margin: 0 auto;">
                has demonstrated verified typing velocity, accuracy, and keystroke integrity on standardized examination drills with the following official metrics:
              </p>

              <!-- 4 Core Achievement Badges -->
              <div class="row g-3 justify-content-center mb-4">
                <div class="col-6 col-md-3">
                  <div class="cert-metric-badge">
                    <div class="cert-metric-val"><?= (float)$result['net_wpm'] ?></div>
                    <div class="cert-metric-lbl">Net Speed (WPM)</div>
                  </div>
                </div>
                <div class="col-6 col-md-3">
                  <div class="cert-metric-badge">
                    <div class="cert-metric-val"><?= (float)$result['gross_wpm'] ?></div>
                    <div class="cert-metric-lbl">Gross Speed (WPM)</div>
                  </div>
                </div>
                <div class="col-6 col-md-3">
                  <div class="cert-metric-badge">
                    <div class="cert-metric-val"><?= (float)$result['accuracy'] ?>%</div>
                    <div class="cert-metric-lbl">Accuracy</div>
                  </div>
                </div>
                <div class="col-6 col-md-3">
                  <div class="cert-metric-badge">
                    <div class="cert-metric-val"><?= $timerFormatted ?></div>
                    <div class="cert-metric-lbl">Duration</div>
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
                    Credential ID: <strong class="text-secondary font-monospace">TM-<?= strtoupper(substr(md5('cert_' . $result['id'] . '_' . $result['created_at']), 0, 10)) ?></strong>
                  </div>
                  <div class="text-muted" style="font-size: 0.72rem;">
                    Issue Date: <strong class="text-secondary"><?= date('F j, Y', strtotime($result['created_at'])) ?></strong>
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
            <i class="bi bi-info-circle me-1"></i>Official verified certificate issued by Type Master.
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

  </div> <!-- closes #results-content-view -->
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  setTimeout(() => {
    const skel = document.getElementById('results-skeleton-view');
    const content = document.getElementById('results-content-view');
    if (skel) skel.style.display = 'none';
    if (content) content.style.display = 'block';
  }, 200);
});

function copyShareText() {
  const text = `🏆 I achieved ${<?= (float)$result['net_wpm'] ?>} Net WPM with ${<?= (float)$result['accuracy'] ?>}% accuracy on Custom Type Master Pro!`;
  navigator.clipboard.writeText(text).then(() => {
    showToast('success', 'Summary copied to clipboard!');
  }).catch(() => {
    alert(text);
  });
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
