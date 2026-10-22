<?php
/**
 * Custom Type Master Pro
 * Admin: User Management
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/functions.php';

require_admin();

$pdo = getDBConnection();
$currentUser = current_user();

// Handle role change or user deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verify_csrf_token()) {
        $action = $_POST['action'] ?? '';
        $targetUserId = (int)($_POST['target_user_id'] ?? 0);

        if ($targetUserId && $pdo) {
            // Prevent modifying own superuser account in this action
            if ($targetUserId === $currentUser['id']) {
                set_flash('danger', 'Action rejected: You cannot change role or delete your own account while logged in.');
            } else {
                if ($action === 'change_role') {
                    $newRole = ($_POST['role'] === 'admin') ? 'admin' : 'user';
                    $stmt = $pdo->prepare("UPDATE users SET role = :role WHERE id = :id");
                    $stmt->execute([':role' => $newRole, ':id' => $targetUserId]);
                    set_flash('success', 'User role updated successfully.');
                } elseif ($action === 'delete') {
                    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
                    $stmt->execute([':id' => $targetUserId]);
                    set_flash('success', 'User account and associated records deleted successfully.');
                }
            }
            header('Location: ' . BASE_URL . '/admin/users.php');
            exit;
        }
    }
}

$search = trim($_GET['search'] ?? '');
$params = [];
$whereSql = "1=1";

if (!empty($search)) {
    $whereSql = "(username LIKE :s OR email LIKE :s OR full_name LIKE :s)";
    $params[':s'] = "%$search%";
}

$users = [];
if ($pdo) {
    try {
        $stmt = $pdo->prepare("
            SELECT u.*, 
                   COUNT(r.id) AS total_tests, 
                   COALESCE(MAX(r.net_wpm), 0) AS best_wpm 
            FROM users u
            LEFT JOIN typing_results r ON u.id = r.user_id
            WHERE $whereSql
            GROUP BY u.id
            ORDER BY u.created_at DESC
        ");
        $stmt->execute($params);
        $users = $stmt->fetchAll();
    } catch (Exception $e) {
        error_log('Error loading users: ' . $e->getMessage());
    }
}

$pageTitle = 'User Management';
include __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid px-md-4 py-3">

  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
      <h2 class="fw-bold text-light mb-1"><i class="bi bi-people-fill text-info me-2"></i>User Management</h2>
      <p class="text-secondary small mb-0">Manage registered typists, role assignments, and review activity.</p>
    </div>
    <a href="<?= BASE_URL ?>/admin/index.php" class="btn btn-outline-secondary btn-sm">
      <i class="bi bi-arrow-left me-1"></i> Admin Overview
    </a>
  </div>

  <!-- Search & Filter Card -->
  <div class="glass-card p-3 mb-4">
    <form method="GET" action="<?= BASE_URL ?>/admin/users.php" class="row g-2 align-items-center">
      <div class="col-md-6">
        <div class="input-group input-group-sm">
          <span class="input-group-text bg-secondary border-secondary text-secondary"><i class="bi bi-search"></i></span>
          <input type="text" name="search" class="form-control bg-dark text-light border-secondary" placeholder="Search by username, full name, or email..." value="<?= e($search) ?>">
        </div>
      </div>
      <div class="col-md-2">
        <button type="submit" class="btn btn-sm btn-primary w-100 fw-semibold">Search</button>
      </div>
      <?php if (!empty($search)): ?>
        <div class="col-md-2">
          <a href="<?= BASE_URL ?>/admin/users.php" class="btn btn-sm btn-outline-secondary">Reset</a>
        </div>
      <?php endif; ?>
    </form>
  </div>

  <!-- Users Table -->
  <div class="glass-card p-4">
    <div class="table-responsive">
      <table class="custom-table mb-0">
        <thead>
          <tr>
            <th>ID</th>
            <th>Typist Profile</th>
            <th>Email</th>
            <th>Role</th>
            <th>Tests Taken</th>
            <th>Best WPM</th>
            <th>Joined</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $u): ?>
            <tr>
              <td class="font-monospace text-muted small">#<?= (int)$u['id'] ?></td>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <div class="user-avatar" style="width: 32px; height: 32px; font-size: 0.85rem;">
                    <?= strtoupper(substr($u['username'], 0, 1)) ?>
                  </div>
                  <div>
                    <div class="fw-bold text-light"><?= e($u['full_name'] ?: $u['username']) ?></div>
                    <div class="text-muted small">@<?= e($u['username']) ?></div>
                  </div>
                </div>
              </td>
              <td class="font-monospace text-secondary small"><?= e($u['email']) ?></td>
              <td>
                <span class="badge <?= ($u['role'] === 'admin') ? 'bg-danger' : 'bg-secondary' ?>">
                  <?= ucfirst($u['role']) ?>
                </span>
              </td>
              <td class="font-monospace text-info fw-bold"><?= (int)$u['total_tests'] ?></td>
              <td class="font-monospace text-success fw-bold"><?= (float)$u['best_wpm'] ?></td>
              <td class="text-muted small"><?= date('M j, Y', strtotime($u['created_at'])) ?></td>
              <td class="text-end">
                <?php if ($u['id'] !== $currentUser['id']): ?>
                  <div class="d-inline-flex gap-1">
                    <!-- Toggle Role -->
                    <form method="POST" action="<?= BASE_URL ?>/admin/users.php" class="d-inline">
                      <?= csrf_field() ?>
                      <input type="hidden" name="action" value="change_role">
                      <input type="hidden" name="target_user_id" value="<?= (int)$u['id'] ?>">
                      <input type="hidden" name="role" value="<?= ($u['role'] === 'admin') ? 'user' : 'admin' ?>">
                      <button type="submit" class="btn btn-sm <?= ($u['role'] === 'admin') ? 'btn-outline-warning' : 'btn-outline-primary' ?>" title="<?= ($u['role'] === 'admin') ? 'Demote to regular user' : 'Promote to admin' ?>">
                        <i class="bi <?= ($u['role'] === 'admin') ? 'bi-shield-slash' : 'bi-shield-check' ?>"></i>
                      </button>
                    </form>

                    <!-- Delete User -->
                    <form method="POST" action="<?= BASE_URL ?>/admin/users.php" class="d-inline" onsubmit="return confirm('Are you sure you want to permanently delete user @<?= e($u['username']) ?>? This cannot be undone.');">
                      <?= csrf_field() ?>
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="target_user_id" value="<?= (int)$u['id'] ?>">
                      <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete User">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>
                  </div>
                <?php else: ?>
                  <span class="badge bg-secondary-subtle text-secondary">Current Session</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
