<?php
/**
 * Custom Type Master Pro
 * Admin: Passage Management
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/functions.php';

require_admin();

$pdo = getDBConnection();
$currentUser = current_user();

// Handle Create / Update / Delete / Toggle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verify_csrf_token()) {
        $action = $_POST['action'] ?? '';

        if ($action === 'create' || $action === 'update') {
            $title = trim($_POST['title'] ?? '');
            $content = trim($_POST['content'] ?? '');
            $difficulty = trim($_POST['difficulty'] ?? 'intermediate');
            $category = trim($_POST['category'] ?? 'General');
            $status = ($_POST['status'] ?? 'active') === 'active' ? 'active' : 'inactive';

            if (empty($title) || empty($content)) {
                set_flash('danger', 'Please provide both title and passage content.');
            } else {
                // Compute word count and char count automatically
                $words = preg_split('/\s+/', $content, -1, PREG_SPLIT_NO_EMPTY);
                $wordCount = count($words);
                $charCount = mb_strlen($content);

                if ($action === 'create') {
                    $stmt = $pdo->prepare("
                        INSERT INTO typing_passages 
                        (title, content, difficulty, category, word_count, char_count, created_by, status) 
                        VALUES 
                        (:t, :c, :d, :cat, :wc, :cc, :cb, :st)
                    ");
                    $stmt->execute([
                        ':t'   => $title,
                        ':c'   => $content,
                        ':d'   => $difficulty,
                        ':cat' => $category,
                        ':wc'  => $wordCount,
                        ':cc'  => $charCount,
                        ':cb'  => $currentUser['id'],
                        ':st'  => $status
                    ]);
                    set_flash('success', 'New passage created successfully with ' . $wordCount . ' words.');
                } else {
                    $passageId = (int)($_POST['passage_id'] ?? 0);
                    $stmt = $pdo->prepare("
                        UPDATE typing_passages SET
                            title = :t,
                            content = :c,
                            difficulty = :d,
                            category = :cat,
                            word_count = :wc,
                            char_count = :cc,
                            status = :st
                        WHERE id = :id
                    ");
                    $stmt->execute([
                        ':t'   => $title,
                        ':c'   => $content,
                        ':d'   => $difficulty,
                        ':cat' => $category,
                        ':wc'  => $wordCount,
                        ':cc'  => $charCount,
                        ':st'  => $status,
                        ':id'  => $passageId
                    ]);
                    set_flash('success', 'Passage updated successfully.');
                }
            }
        } elseif ($action === 'toggle_status') {
            $passageId = (int)($_POST['passage_id'] ?? 0);
            $newStatus = ($_POST['status'] === 'active') ? 'active' : 'inactive';
            $stmt = $pdo->prepare("UPDATE typing_passages SET status = :st WHERE id = :id");
            $stmt->execute([':st' => $newStatus, ':id' => $passageId]);
            set_flash('info', 'Passage status updated to ' . $newStatus . '.');
        } elseif ($action === 'delete') {
            $passageId = (int)($_POST['passage_id'] ?? 0);
            $stmt = $pdo->prepare("DELETE FROM typing_passages WHERE id = :id");
            $stmt->execute([':id' => $passageId]);
            set_flash('success', 'Passage deleted.');
        }

        header('Location: ' . BASE_URL . '/admin/passages.php');
        exit;
    }
}

$passages = [];
if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM typing_passages ORDER BY id DESC");
        $passages = $stmt->fetchAll();
    } catch (Exception $e) {
        error_log('Error loading passages: ' . $e->getMessage());
    }
}

$pageTitle = 'Passage Management';
include __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid px-md-4 py-3">

  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
      <h2 class="fw-bold text-light mb-1"><i class="bi bi-journal-text text-info me-2"></i>Passage Management</h2>
      <p class="text-secondary small mb-0">Create, organize, and inspect practice drill content for all skill levels.</p>
    </div>
    <div class="d-flex gap-2">
      <button type="button" class="btn btn-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#passageModal" onclick="prepareCreatePassage()">
        <i class="bi bi-plus-circle me-1"></i> Add New Passage
      </button>
      <a href="<?= BASE_URL ?>/admin/index.php" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Overview
      </a>
    </div>
  </div>

  <!-- Passages Table -->
  <div class="glass-card p-4">
    <div class="table-responsive">
      <table class="custom-table mb-0">
        <thead>
          <tr>
            <th>ID</th>
            <th>Title & Category</th>
            <th>Difficulty</th>
            <th>Word Count</th>
            <th>Char Count</th>
            <th>Status</th>
            <th>Created</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($passages as $p): ?>
            <tr>
              <td class="font-monospace text-muted small">#<?= (int)$p['id'] ?></td>
              <td>
                <div class="fw-bold text-light"><?= e($p['title']) ?></div>
                <div class="text-muted small"><i class="bi bi-tag me-1"></i><?= e($p['category']) ?></div>
              </td>
              <td><?= get_difficulty_badge($p['difficulty']) ?></td>
              <td class="font-monospace text-info fw-bold"><?= (int)$p['word_count'] ?> words</td>
              <td class="font-monospace text-secondary"><?= (int)$p['char_count'] ?></td>
              <td>
                <span class="badge <?= ($p['status'] === 'active') ? 'bg-success-subtle text-success border border-success' : 'bg-secondary' ?>">
                  <?= ucfirst($p['status']) ?>
                </span>
              </td>
              <td class="text-muted small"><?= date('M j, Y', strtotime($p['created_at'])) ?></td>
              <td class="text-end">
                <div class="d-inline-flex gap-1">
                  <!-- Try in Practice -->
                  <a href="<?= BASE_URL ?>/practice.php?passage_id=<?= (int)$p['id'] ?>" class="btn btn-sm btn-outline-info" title="Test in Arena">
                    <i class="bi bi-play-fill"></i>
                  </a>

                  <!-- Edit Button -->
                  <button type="button" class="btn btn-sm btn-outline-secondary" onclick='prepareEditPassage(<?= json_encode($p) ?>)' title="Edit Passage">
                    <i class="bi bi-pencil"></i>
                  </button>

                  <!-- Toggle Status Form -->
                  <form method="POST" action="<?= BASE_URL ?>/admin/passages.php" class="d-inline">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="toggle_status">
                    <input type="hidden" name="passage_id" value="<?= (int)$p['id'] ?>">
                    <input type="hidden" name="status" value="<?= ($p['status'] === 'active') ? 'inactive' : 'active' ?>">
                    <button type="submit" class="btn btn-sm <?= ($p['status'] === 'active') ? 'btn-outline-warning' : 'btn-outline-success' ?>" title="<?= ($p['status'] === 'active') ? 'Deactivate' : 'Activate' ?>">
                      <i class="bi <?= ($p['status'] === 'active') ? 'bi-pause-circle' : 'bi-check-circle' ?>"></i>
                    </button>
                  </form>

                  <!-- Delete Form -->
                  <form method="POST" action="<?= BASE_URL ?>/admin/passages.php" class="d-inline" onsubmit="return confirm('Delete passage #<?= $p['id'] ?>?');">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="passage_id" value="<?= (int)$p['id'] ?>">
                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<!-- Create / Edit Passage Modal -->
<div class="modal fade" id="passageModal" tabindex="-1" aria-labelledby="passageModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content glass-card border-secondary">
      <form method="POST" action="<?= BASE_URL ?>/admin/passages.php" id="passageForm">
        <?= csrf_field() ?>
        <input type="hidden" name="action" id="formAction" value="create">
        <input type="hidden" name="passage_id" id="formPassageId" value="0">

        <div class="modal-header border-secondary">
          <h5 class="modal-title text-light fw-bold" id="passageModalLabel">
            <i class="bi bi-journal-plus text-info me-2"></i>Add Practice Passage
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body p-4">
          <div class="row g-3 mb-3">
            <div class="col-md-8">
              <label class="form-label text-secondary small fw-bold">Passage Title <span class="text-danger">*</span></label>
              <input type="text" name="title" id="pTitle" class="form-control bg-dark text-light border-secondary" required placeholder="e.g. Distributed Database Consistency">
            </div>
            <div class="col-md-4">
              <label class="form-label text-secondary small fw-bold">Category</label>
              <input type="text" name="category" id="pCategory" class="form-control bg-dark text-light border-secondary" placeholder="e.g. Technology" value="Technology">
            </div>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label text-secondary small fw-bold">Difficulty Level <span class="text-danger">*</span></label>
              <select name="difficulty" id="pDifficulty" class="form-select bg-dark text-light border-secondary">
                <option value="beginner">Beginner (Simple flow, low punctuation)</option>
                <option value="intermediate" selected>Intermediate (Standard prose, natural punctuation)</option>
                <option value="advanced">Advanced (Code snippets, symbols, numbers)</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label text-secondary small fw-bold">Status</label>
              <select name="status" id="pStatus" class="form-select bg-dark text-light border-secondary">
                <option value="active" selected>Active (Available in practice arena)</option>
                <option value="inactive">Inactive (Hidden)</option>
              </select>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label text-secondary small fw-bold">Passage Text Content <span class="text-danger">*</span></label>
            <textarea name="content" id="pContent" rows="6" class="form-control bg-dark text-light border-secondary font-monospace" required placeholder="Type or paste the passage text here..."></textarea>
            <div class="form-text text-muted d-flex justify-content-between">
              <span id="pWordCountDisplay">0 words</span>
              <span id="pCharCountDisplay">0 characters</span>
            </div>
          </div>
        </div>

        <div class="modal-footer border-secondary">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary fw-bold" id="pSubmitBtn">
            <i class="bi bi-save2 me-1"></i> Save Passage
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
const passageModal = new bootstrap.Modal(document.getElementById('passageModal'));
const pTitle = document.getElementById('pTitle');
const pCategory = document.getElementById('pCategory');
const pDifficulty = document.getElementById('pDifficulty');
const pStatus = document.getElementById('pStatus');
const pContent = document.getElementById('pContent');
const formAction = document.getElementById('formAction');
const formPassageId = document.getElementById('formPassageId');
const modalLabel = document.getElementById('passageModalLabel');

function updateCounters() {
  const words = pContent.value.trim().split(/\s+/).filter(w => w.length > 0);
  document.getElementById('pWordCountDisplay').textContent = `${words.length} words`;
  document.getElementById('pCharCountDisplay').textContent = `${pContent.value.length} characters`;
}

pContent.addEventListener('input', updateCounters);

function prepareCreatePassage() {
  formAction.value = 'create';
  formPassageId.value = '0';
  modalLabel.innerHTML = '<i class="bi bi-journal-plus text-info me-2"></i>Add Practice Passage';
  pTitle.value = '';
  pCategory.value = 'General';
  pDifficulty.value = 'intermediate';
  pStatus.value = 'active';
  pContent.value = '';
  updateCounters();
}

function prepareEditPassage(passage) {
  formAction.value = 'update';
  formPassageId.value = passage.id;
  modalLabel.innerHTML = `<i class="bi bi-pencil-square text-info me-2"></i>Edit Passage #${passage.id}`;
  pTitle.value = passage.title;
  pCategory.value = passage.category;
  pDifficulty.value = passage.difficulty;
  pStatus.value = passage.status;
  pContent.value = passage.content;
  updateCounters();
  passageModal.show();
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
