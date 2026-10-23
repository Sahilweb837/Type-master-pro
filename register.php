<?php
/**
 * Custom Type Master Pro
 * User Registration
 */

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/functions.php';

if (is_logged_in()) {
    header('Location: ' . BASE_URL . '/dashboard.php');
    exit;
}

$error = '';
$username = '';
$email = '';
$fullName = '';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!verify_csrf_token()) {
        $error = 'Security validation failed (invalid CSRF token). Please try again.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $fullName = trim($_POST['full_name'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validation rules
        if (empty($username) || empty($email) || empty($password)) {
            $error = 'Please fill in all required fields.';
        } elseif (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username)) {
            $error = 'Username must be between 3 and 30 characters and contain only letters, numbers, and underscores.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please provide a valid email address.';
        } elseif (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters long.';
        } elseif ($password !== $confirmPassword) {
            $error = 'Passwords do not match.';
        } else {
            $pdo = getDBConnection();
            if (!$pdo) {
                $error = 'Database service is currently offline. Please verify that the database server is running.';
            } else {
                try {
                    // Check if username or email already taken
                    $stmt = $pdo->prepare("SELECT id, username, email FROM users WHERE username = :u OR email = :e LIMIT 1");
                    $stmt->execute([':u' => $username, ':e' => $email]);
                    $existing = $stmt->fetch();

                    if ($existing) {
                        if (strtolower($existing['username']) === strtolower($username)) {
                            $error = 'This username is already taken. Please choose another.';
                        } else {
                            $error = 'An account with this email address already exists.';
                        }
                    } else {
                        // Create user
                        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
                        $insertStmt = $pdo->prepare("
                            INSERT INTO users (username, email, password_hash, full_name, role) 
                            VALUES (:u, :e, :p, :fn, 'user')
                        ");
                        $insertStmt->execute([
                            ':u'  => $username,
                            ':e'  => $email,
                            ':p'  => $passwordHash,
                            ':fn' => !empty($fullName) ? $fullName : $username
                        ]);

                        $newUserId = (int)$pdo->lastInsertId();

                        // Provision default user settings
                        $settingsStmt = $pdo->prepare("
                            INSERT INTO user_settings (user_id) VALUES (:uid)
                        ");
                        $settingsStmt->execute([':uid' => $newUserId]);

                        // Automatically log in newly created user
                        $newUser = [
                            'id'        => $newUserId,
                            'username'  => $username,
                            'email'     => $email,
                            'full_name' => !empty($fullName) ? $fullName : $username,
                            'role'      => 'user',
                            'avatar'    => 'default-avatar.png'
                        ];

                        login_user($newUser);
                        set_flash('success', 'Your account has been created successfully! Welcome to Custom Type Master Pro.');
                        header('Location: ' . BASE_URL . '/dashboard.php');
                        exit;
                    }
                } catch (Exception $e) {
                    error_log('Registration error: ' . $e->getMessage());
                    $error = 'An error occurred during registration. Please try again.';
                }
            }
        }
    }
}

$pageTitle = 'Create Account';
include __DIR__ . '/includes/header.php';
?>

<div class="container py-5 my-md-4">
  <div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
      <div class="soft-card p-4 p-md-5">
        <div class="text-center mb-4">
          <div class="brand-logo-badge mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: #0a0a0a; border-radius: 12px; box-shadow: var(--shadow-md);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <rect x="2" y="2" width="20" height="20" rx="4" fill="#0a0a0a" stroke="#ffffff" stroke-width="1.8"/>
              <path d="M7 9H17M12 9V17" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round"/>
            </svg>
          </div>
          <h2 class="fw-bold mb-1" style="color: var(--text-primary); letter-spacing: -0.5px;">Create Account</h2>
          <p class="text-secondary small mb-0">Join TypeMaster PRO to track your speed and precision</p>
        </div>

        <?php if (!empty($error)): ?>
          <div class="alert alert-danger d-flex align-items-center gap-2 mb-4 rounded-3 border-0 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
            <div class="small fw-semibold"><?= e($error) ?></div>
          </div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>/register.php">
          <?= csrf_field() ?>

          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label small fw-bold text-secondary">Username <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text border-end-0" style="background: var(--bg-primary); border-color: var(--border-color); color: var(--second-color);"><i class="bi bi-at"></i></span>
                <input type="text" name="username" class="form-control border-start-0 ps-0" style="background: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);" placeholder="speed_typist" required value="<?= e($username) ?>">
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-secondary">Full Name</label>
              <div class="input-group">
                <span class="input-group-text border-end-0" style="background: var(--bg-primary); border-color: var(--border-color); color: var(--second-color);"><i class="bi bi-person"></i></span>
                <input type="text" name="full_name" class="form-control border-start-0 ps-0" style="background: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);" placeholder="Alex Morgan" value="<?= e($fullName) ?>">
              </div>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label small fw-bold text-secondary">Email Address <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text border-end-0" style="background: var(--bg-primary); border-color: var(--border-color); color: var(--second-color);"><i class="bi bi-envelope"></i></span>
              <input type="email" name="email" class="form-control border-start-0 ps-0" style="background: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);" placeholder="alex@example.com" required value="<?= e($email) ?>">
            </div>
          </div>

          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <label class="form-label small fw-bold text-secondary">Password <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text border-end-0" style="background: var(--bg-primary); border-color: var(--border-color); color: var(--second-color);"><i class="bi bi-lock"></i></span>
                <input type="password" name="password" id="regPassword" class="form-control border-start-0 ps-0" style="background: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);" placeholder="Min. 6 chars" required>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-secondary">Confirm Password <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text border-end-0" style="background: var(--bg-primary); border-color: var(--border-color); color: var(--second-color);"><i class="bi bi-shield-lock"></i></span>
                <input type="password" name="confirm_password" id="regConfirmPassword" class="form-control border-start-0 ps-0" style="background: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);" placeholder="Repeat password" required>
              </div>
            </div>
          </div>

          <button type="submit" class="soft-btn soft-btn-primary w-100 py-3 fw-bold fs-6">
            <i class="bi bi-check2-circle me-1"></i> Register Free Account
          </button>
        </form>

        <div class="text-center mt-4 text-secondary small">
          Already have an account? 
          <a href="<?= BASE_URL ?>/login.php" class="fw-bold ms-1" style="color: var(--first-color); text-decoration: none;">Sign In</a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
