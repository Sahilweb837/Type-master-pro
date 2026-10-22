<?php
/**
 * Custom Type Master Pro
 * User Practice History & Session Logs
 */

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/functions.php';

require_login();

$user = current_user();
$userId = $user['id'];
$pdo = getDBConnection();

// Handle deletion of single result
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    if (verify_csrf_token()) {
        $resultId = (int)($_POST['result_id'] ?? 0);
        if ($resultId && $pdo) {
            $delStmt = $pdo->prepare("DELETE FROM typing_results WHERE id = :id AND user_id = :uid");
            $delStmt->execute([':id' => $resultId, ':uid' => $userId]);
            set_flash('success', 'Practice session record deleted successfully.');
            header('Location: ' . BASE_URL . '/history.php');
            exit;
        }
    }
}

// Search, Filter & Pagination Parameters
$search = trim($_GET['search'] ?? '');
$filterDiff = trim($_GET['difficulty'] ?? '');
$sortBy = trim($_GET['sort'] ?? 'created_at');
$sortOrder = strtoupper(trim($_GET['order'] ?? 'DESC')) === 'ASC' ? 'ASC' : 'DESC';

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 15;
$offset = ($page - 1) * $perPage;

$allowedSort = [
    'created_at' => 'r.created_at',
    'net_wpm'    => 'r.net_wpm',
    'gross_wpm'  => 'r.gross_wpm',
    'accuracy'   => 'r.accuracy',
    'duration'   => 'r.elapsed_seconds'
];
$sortColumn = $allowedSort[$sortBy] ?? 'r.created_at';

// Build SQL
$whereClauses = ["r.user_id = :uid"];
$params = [':uid' => $userId];

if (!empty($search)) {
    $whereClauses[] = "(p.title LIKE :s OR r.difficulty LIKE :s)";
    $params[':s'] = "%$search%";
}

if (!empty($filterDiff)) {
    $whereClauses[] = "r.difficulty = :diff";
    $params[':diff'] = $filterDiff;
}

$whereSql = implode(' AND ', $whereClauses);

$totalRecords = 0;
$results = [];

if ($pdo) {
    try {
        // Count total matching records
        $countSql = "
            SELECT COUNT(*) AS total 
            FROM typing_results r
            LEFT JOIN typing_passages p ON r.passage_id = p.id
            WHERE $whereSql
        ";
        $countStmt = $pdo->prepare($countSql);
        $countStmt->execute($params);
        $totalRecords = (int)$countStmt->fetchColumn();

        // Fetch paginated rows
        $dataSql = "
            SELECT r.*, p.title AS passage_title 
            FROM typing_results r
            LEFT JOIN typing_passages p ON r.passage_id = p.id
            WHERE $whereSql
            ORDER BY $sortColumn $sortOrder
            LIMIT $perPage OFFSET $offset
        ";
        $dataStmt = $pdo->prepare($dataSql);
        $dataStmt->execute($params);
        $results = $dataStmt->fetchAll();
    } catch (Exception $e) {
        error_log('Error fetching history: ' . $e->getMessage());
    }
}

$totalPages = max(1, ceil($totalRecords / $perPage));

$pageTitle = 'Practice History & Results';
include __DIR__ . '/includes/header.php';
?>

<div class="container-fluid px-md-4 py-3">

  <!-- =====================================================================
       LAZY SKELETON LOADING SHIMMER WRAPPER (HISTORY PAGE)
       ===================================================================== -->
  <div id="history-skeleton-view" class="skeleton-wrapper">
    <!-- Skeleton Header -->
    <div class="glass-card p-4 mb-4 skeleton-card d-flex justify-content-between align-items-center">
      <div style="width: 40%;">
        <div class="skeleton skeleton-title" style="width: 50%;"></div>
        <div class="skeleton skeleton-text" style="width: 80%;"></div>
      </div>
      <div class="d-flex gap-2">
        <div class="skeleton" style="width: 100px; height: 34px; border-radius: 6px;"></div>
        <div class="skeleton" style="width: 100px; height: 34px; border-radius: 6px;"></div>
      </div>
    </div>
    <!-- Skeleton Filter Bar -->
    <div class="glass-card p-3 mb-4 skeleton-card">
      <div class="row g-2">
        <div class="col-md-4"><div class="skeleton" style="height: 34px; border-radius: 6px;"></div></div>
        <div class="col-md-3"><div class="skeleton" style="height: 34px; border-radius: 6px;"></div></div>
        <div class="col-md-3"><div class="skeleton" style="height: 34px; border-radius: 6px;"></div></div>
        <div class="col-md-2"><div class="skeleton" style="height: 34px; border-radius: 6px;"></div></div>
      </div>
    </div>
    <!-- Skeleton Table Rows -->
    <div class="glass-card p-3 skeleton-card">
      <?php for ($i = 0; $i < 6; $i++): ?>
        <div class="skeleton mb-3" style="height: 48px; border-radius: 8px;"></div>
      <?php endfor; ?>
    </div>
  </div>

  <!-- Real Content View -->
  <div id="history-content-view" style="display: none;">
    <!-- Header Title & Export Tools -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
      <div>
        <h2 class="fw-bold text-light mb-1"><i class="bi bi-clock-history text-info me-2"></i>Practice History</h2>
        <p class="text-secondary small mb-0">Complete historical records of every typing test you've completed.</p>
      </div>
      <div class="d-flex align-items-center gap-2">
        <a href="<?= BASE_URL ?>/export.php?format=csv" class="btn btn-outline-success btn-sm d-inline-flex align-items-center gap-1">
        <i class="bi bi-file-earmark-spreadsheet-fill"></i>
        <span>Export CSV</span>
      </a>
      <a href="<?= BASE_URL ?>/export.php?format=json" class="btn btn-outline-info btn-sm d-inline-flex align-items-center gap-1">
        <i class="bi bi-filetype-json"></i>
        <span>Export JSON</span>
      </a>
      <a href="<?= BASE_URL ?>/practice.php" class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1 fw-bold">
        <i class="bi bi-play-circle-fill"></i>
        <span>New Test</span>
      </a>
    </div>
  </div>

  <!-- Filters & Search Bar -->
  <div class="glass-card p-3 mb-4">
    <form method="GET" action="<?= BASE_URL ?>/history.php" class="row g-2 align-items-center">
      <div class="col-md-4">
        <div class="input-group input-group-sm">
          <span class="input-group-text bg-secondary border-secondary text-secondary"><i class="bi bi-search"></i></span>
          <input type="text" name="search" class="form-control bg-dark text-light border-secondary" placeholder="Search passage or difficulty..." value="<?= e($search) ?>">
        </div>
      </div>

      <div class="col-sm-6 col-md-3">
        <select name="difficulty" class="form-select form-select-sm bg-dark text-light border-secondary">
          <option value="">All Difficulties</option>
          <option value="beginner" <?= ($filterDiff === 'beginner') ? 'selected' : '' ?>>Beginner</option>
          <option value="intermediate" <?= ($filterDiff === 'intermediate') ? 'selected' : '' ?>>Intermediate</option>
          <option value="advanced" <?= ($filterDiff === 'advanced') ? 'selected' : '' ?>>Advanced</option>
          <option value="custom" <?= ($filterDiff === 'custom') ? 'selected' : '' ?>>Custom</option>
        </select>
      </div>

      <div class="col-sm-6 col-md-3">
        <select name="sort" class="form-select form-select-sm bg-dark text-light border-secondary">
          <option value="created_at" <?= ($sortBy === 'created_at') ? 'selected' : '' ?>>Sort: Most Recent</option>
          <option value="net_wpm" <?= ($sortBy === 'net_wpm') ? 'selected' : '' ?>>Sort: Highest Net WPM</option>
          <option value="accuracy" <?= ($sortBy === 'accuracy') ? 'selected' : '' ?>>Sort: Highest Accuracy</option>
          <option value="duration" <?= ($sortBy === 'duration') ? 'selected' : '' ?>>Sort: Duration</option>
        </select>
      </div>

      <div class="col-md-2 d-flex gap-2">
        <button type="submit" class="btn btn-sm btn-primary w-100 fw-semibold">Filter</button>
        <?php if (!empty($search) || !empty($filterDiff) || $sortBy !== 'created_at'): ?>
          <a href="<?= BASE_URL ?>/history.php" class="btn btn-sm btn-outline-secondary" title="Reset Filters"><i class="bi bi-x-lg"></i></a>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <!-- Records Table -->
  <div class="glass-card p-4">
    <?php if (!empty($results)): ?>
      <div class="table-responsive">
        <table class="custom-table mb-0">
          <thead>
            <tr>
              <th>ID</th>
              <th>Passage Title</th>
              <th>Difficulty</th>
              <th>Net WPM</th>
              <th>Gross WPM</th>
              <th>Accuracy</th>
              <th>Keystrokes (C / E)</th>
              <th>Duration</th>
              <th>Date & Time</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($results as $row): ?>
              <tr>
                <td class="font-monospace text-muted small">#<?= (int)$row['id'] ?></td>
                <td>
                  <div class="fw-bold text-light text-truncate" style="max-width: 220px;" title="<?= e($row['passage_title'] ?? 'Custom Text') ?>">
                    <?= e($row['passage_title'] ?? 'Custom Text Drill') ?>
                  </div>
                  <div class="small text-muted font-monospace"><?= (int)$row['words_typed'] ?> words typed</div>
                </td>
                <td><?= get_difficulty_badge($row['difficulty']) ?></td>
                <td>
                  <span class="fw-bold text-info fs-5 font-monospace"><?= (float)$row['net_wpm'] ?></span>
                </td>
                <td class="font-monospace text-secondary"><?= (float)$row['gross_wpm'] ?></td>
                <td>
                  <span class="font-monospace fw-semibold <?= ((float)$row['accuracy'] >= 95) ? 'text-success' : 'text-warning' ?>">
                    <?= (float)$row['accuracy'] ?>%
                  </span>
                </td>
                <td class="font-monospace small">
                  <span class="text-success fw-bold"><?= (int)$row['correct_keystrokes'] ?></span> / 
                  <span class="text-danger fw-bold"><?= (int)$row['error_keystrokes'] ?></span>
                </td>
                <td class="font-monospace text-secondary"><?= (float)$row['elapsed_seconds'] ?>s</td>
                <td>
                  <div class="small text-light"><?= date('M j, Y', strtotime($row['created_at'])) ?></div>
                  <div class="small text-muted font-monospace"><?= date('H:i:s', strtotime($row['created_at'])) ?></div>
                </td>
                <td class="text-end">
                  <div class="d-inline-flex gap-1">
                    <a href="<?= BASE_URL ?>/results.php?id=<?= (int)$row['id'] ?>" class="btn btn-sm btn-outline-info" title="View Full Report">
                      <i class="bi bi-eye"></i>
                    </a>
                    <form method="POST" action="<?= BASE_URL ?>/history.php" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this session record?');">
                      <?= csrf_field() ?>
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="result_id" value="<?= (int)$row['id'] ?>">
                      <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Record">
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

      <!-- Pagination Controls -->
      <?php if ($totalPages > 1): ?>
        <nav class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top border-secondary">
          <div class="text-secondary small">
            Showing <?= $offset + 1 ?> to <?= min($offset + $perPage, $totalRecords) ?> of <?= $totalRecords ?> total sessions
          </div>
          <ul class="pagination pagination-sm mb-0">
            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
              <a class="page-link bg-dark text-light border-secondary" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&difficulty=<?= urlencode($filterDiff) ?>&sort=<?= urlencode($sortBy) ?>">Previous</a>
            </li>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
              <?php if ($i == 1 || $i == $totalPages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                <li class="page-item <?= ($i === $page) ? 'active' : '' ?>">
                  <a class="page-link <?= ($i === $page) ? 'bg-primary text-white border-primary' : 'bg-dark text-light border-secondary' ?>" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&difficulty=<?= urlencode($filterDiff) ?>&sort=<?= urlencode($sortBy) ?>"><?= $i ?></a>
                </li>
              <?php endif; ?>
            <?php endfor; ?>
            <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
              <a class="page-link bg-dark text-light border-secondary" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&difficulty=<?= urlencode($filterDiff) ?>&sort=<?= urlencode($sortBy) ?>">Next</a>
            </li>
          </ul>
        </nav>
      <?php endif; ?>

    <?php else: ?>
      <div class="text-center py-5 text-muted">
        <i class="bi bi-inbox display-4 d-block mb-3 opacity-25"></i>
        <h5 class="text-light fw-bold">No typing records found</h5>
        <p class="text-secondary small mb-3">Try adjusting your filters or complete a new practice session.</p>
        <a href="<?= BASE_URL ?>/practice.php" class="btn btn-primary fw-bold px-4 py-2">Start Practicing</a>
      </div>
    <?php endif; ?>
  </div>

  </div> <!-- closes #history-content-view -->
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  setTimeout(() => {
    const skel = document.getElementById('history-skeleton-view');
    const content = document.getElementById('history-content-view');
    if (skel) skel.style.display = 'none';
    if (content) content.style.display = 'block';
  }, 200);
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
