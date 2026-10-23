<?php
/**
 * Custom Type Master Pro
 * User Login
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
$redirect = $_GET['redirect'] ?? 'dashboard.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!verify_csrf_token()) {
        $error = 'Security validation failed (invalid CSRF token). Please try again.';
    } else {
        $loginInput = trim($_POST['login_input'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($loginInput) || empty($password)) {
            $error = 'Please enter both your username/email and your password.';
        } else {
            $pdo = getDBConnection();
            if (!$pdo) {
                $error = 'Database service is currently offline. Please verify that the database server is running.';
            } else {
                try {
                    $stmt = $pdo->prepare("
                        SELECT id, username, email, password_hash, full_name, role, avatar 
                        FROM users 
                        WHERE username = :input OR email = :input 
                        LIMIT 1
                    ");
                    $stmt->execute([':input' => $loginInput]);
                    $user = $stmt->fetch();

                    if ($user && password_verify($password, $user['password_hash'])) {
                        login_user($user);
                        set_flash('success', 'Welcome back, ' . htmlspecialchars($user['full_name'] ?: $user['username']) . '!');
                        
                        $safeRedirect = (strpos($redirect, 'http') === false) ? $redirect : 'dashboard.php';
                        header('Location: ' . BASE_URL . '/' . ltrim($safeRedirect, '/'));
                        exit;
                    } else {
                        $error = 'Invalid username/email or password.';
                    }
                } catch (Exception $e) {
                    error_log('Login error: ' . $e->getMessage());
                    $error = 'An error occurred while signing in. Please try again.';
                }
            }
        }
    }
}

$pageTitle = 'Sign In';
include __DIR__ . '/includes/header.php';
?>

<div class="container py-5 my-md-4">
  <div class="row justify-content-center">
    <div class="col-md-7 col-lg-5">
      <div class="soft-card p-4 p-md-5">
        <div class="text-center mb-4">
          <div class="brand-logo-badge mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: #0a0a0a; border-radius: 12px; box-shadow: var(--shadow-md);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <rect x="2" y="2" width="20" height="20" rx="4" fill="#0a0a0a" stroke="#ffffff" stroke-width="1.8"/>
              <path d="M7 9H17M12 9V17" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round"/>
            </svg>
          </div>
          <h2 class="fw-bold mb-1" style="color: var(--text-primary); letter-spacing: -0.5px;">Welcome Back</h2>
          <p class="text-secondary small mb-0">Sign in to your TypeMaster PRO workspace</p>
        </div>

        <?php if (!empty($error)): ?>
          <div class="alert alert-danger d-flex align-items-center gap-2 mb-4 rounded-3 border-0 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
            <div class="small fw-semibold"><?= e($error) ?></div>
          </div>
        <?php endif; ?>

        <form method="POST" action="login.php?redirect=<?= urlencode($redirect) ?>">
          <?= csrf_field() ?>

          <div class="mb-3">
            <label class="form-label small fw-bold text-secondary">Username or Email</label>
            <div class="input-group">
              <span class="input-group-text border-end-0" style="background: var(--bg-primary); border-color: var(--border-color); color: var(--second-color);">
                <i class="bi bi-person"></i>
              </span>
              <input type="text" name="login_input" id="loginInput" class="form-control border-start-0 ps-0" style="background: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);" placeholder="e.g. admin or demouser" required autofocus value="<?= e($_POST['login_input'] ?? '') ?>">
            </div>
          </div>

          <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <label class="form-label small fw-bold text-secondary mb-0">Password</label>
            </div>
            <div class="input-group">
              <span class="input-group-text border-end-0" style="background: var(--bg-primary); border-color: var(--border-color); color: var(--second-color);">
                <i class="bi bi-lock"></i>
              </span>
              <input type="password" name="password" id="loginPassword" class="form-control border-start-0 border-end-0 ps-0" style="background: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);" placeholder="••••••••" required>
              <button class="btn btn-outline-secondary" type="button" style="border-color: var(--border-color);" onclick="togglePasswordVisibility('loginPassword', this)">
                <i class="bi bi-eye"></i>
              </button>
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center mb-4 small">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="rememberMe" checked>
              <label class="form-check-label text-secondary" for="rememberMe">Remember Session</label>
            </div>
            <a href="<?= BASE_URL ?>/practice.php" class="text-secondary text-decoration-none hover-coral">Guest Arena</a>
          </div>

          <button type="submit" class="soft-btn soft-btn-primary w-100 py-3 fw-bold fs-6">
            <i class="bi bi-box-arrow-in-right me-1"></i> Sign In to TypeMaster
          </button>
        </form>

        <!-- Quick 1-Click Demo Fill Box -->
        <div class="mt-4 p-3 rounded-3" style="background: var(--bg-primary); border: 1px solid var(--border-color); box-shadow: var(--soft-inset);">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="small fw-bold text-secondary"><i class="bi bi-key-fill text-warning me-1"></i> Quick 1-Click Demo Fill:</span>
          </div>
          <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm flex-fill" style="background: var(--bg-card); color: var(--text-primary); border: 1px solid var(--border-color); box-shadow: var(--soft-flat);" onclick="fillCredentials('admin', 'Admin@123456')">
              <i class="bi bi-shield-lock-fill text-danger me-1"></i> Admin Demo
            </button>
            <button type="button" class="btn btn-sm flex-fill" style="background: var(--bg-card); color: var(--text-primary); border: 1px solid var(--border-color); box-shadow: var(--soft-flat);" onclick="fillCredentials('demouser', 'Admin@123456')">
              <i class="bi bi-person-badge-fill text-info me-1"></i> User Demo
            </button>
          </div>
        </div>

        <div class="text-center mt-4 text-secondary small">
          Don't have an account yet? 
          <a href="<?= BASE_URL ?>/register.php" class="fw-bold ms-1" style="color: var(--first-color); text-decoration: none;">Create Free Account</a>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function togglePasswordVisibility(fieldId, btn) {
  const f = document.getElementById(fieldId);
  const icon = btn.querySelector('i');
  if (f.type === 'password') {
    f.type = 'text';
    icon.classList.replace('bi-eye', 'bi-eye-slash');
  } else {
    f.type = 'password';
    icon.classList.replace('bi-eye-slash', 'bi-eye');
  }
}

function fillCredentials(u, p) {
  const userField = document.getElementById('loginInput');
  const passField = document.getElementById('loginPassword');
  if (userField && passField) {
    userField.value = u;
    passField.value = p;
    userField.focus();
    if (typeof showToast === 'function') {
      showToast('info', `Filled credentials for ${u}`);
    }
  }
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
