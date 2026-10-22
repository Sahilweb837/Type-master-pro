<?php
/**
 * Custom Type Master Pro
 * Authentication & Authorization Utilities
 */

require_once __DIR__ . '/../config/app.php';

/**
 * Check if a user is currently logged in.
 *
 * @return bool
 */
function is_logged_in(): bool {
    return !empty($_SESSION['user_id']) && !empty($_SESSION['username']);
}

/**
 * Check if the currently logged in user is an administrator.
 *
 * @return bool
 */
function is_admin(): bool {
    return is_logged_in() && ($_SESSION['user_role'] ?? 'user') === 'admin';
}

/**
 * Get the current user data from the session or database.
 *
 * @return array|null
 */
function current_user(): ?array {
    if (!is_logged_in()) {
        return null;
    }

    return [
        'id'        => (int)$_SESSION['user_id'],
        'username'  => $_SESSION['username'],
        'email'     => $_SESSION['user_email'] ?? '',
        'full_name' => $_SESSION['user_full_name'] ?? $_SESSION['username'],
        'role'      => $_SESSION['user_role'] ?? 'user',
        'avatar'    => $_SESSION['user_avatar'] ?? 'default-avatar.png'
    ];
}

/**
 * Guard page: Enforces user authentication.
 */
function require_login(): void {
    if (!is_logged_in()) {
        $returnUrl = urlencode($_SERVER['REQUEST_URI'] ?? 'dashboard.php');
        header('Location: ' . BASE_URL . '/login.php?redirect=' . $returnUrl);
        exit;
    }
}

/**
 * Guard page: Enforces administrator role.
 */
function require_admin(): void {
    require_login();
    if (!is_admin()) {
        http_response_code(403);
        include __DIR__ . '/../includes/header.php';
        echo '<div class="container py-5 text-center">
                <div class="card p-5 border-danger shadow-sm mx-auto" style="max-width: 500px;">
                    <i class="bi bi-shield-slash-fill text-danger display-1 mb-3"></i>
                    <h2 class="text-danger fw-bold">Access Forbidden</h2>
                    <p class="text-muted">You do not possess the required administrator privileges to view this page.</p>
                    <a href="' . BASE_URL . '/dashboard.php" class="btn btn-primary mt-3">Return to Dashboard</a>
                </div>
              </div>';
        include __DIR__ . '/../includes/footer.php';
        exit;
    }
}

/**
 * Log in a user by populating session variables and regenerating session ID.
 *
 * @param array $user
 */
function login_user(array $user): void {
    session_regenerate_id(true);

    $_SESSION['user_id']        = (int)$user['id'];
    $_SESSION['username']       = $user['username'];
    $_SESSION['user_email']     = $user['email'];
    $_SESSION['user_full_name'] = $user['full_name'] ?? $user['username'];
    $_SESSION['user_role']      = $user['role'] ?? 'user';
    $_SESSION['user_avatar']    = $user['avatar'] ?? 'default-avatar.png';
    $_SESSION['last_login']     = time();
}

/**
 * Safely log out the user and clear session cookie.
 */
function logout_user(): void {
    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    session_destroy();
}

/**
 * Fetch or initialize settings for a given user.
 *
 * @param int $userId
 * @return array
 */
function get_user_settings(int $userId): array {
    $pdo = getDBConnection();
    $defaultSettings = [
        'theme'               => 'light',
        'font_family'         => 'JetBrains Mono',
        'font_size'           => 22,
        'line_height'         => 1.8,
        'caret_style'         => 'line',
        'caret_color'         => '#38bdf8',
        'correct_color'       => '#4ade80',
        'incorrect_color'     => '#f87171',
        'typing_area_width'   => '920px',
        'sound_enabled'       => 1,
        'sound_theme'         => 'mechanical',
        'default_duration'    => 60,
        'default_difficulty'  => 'intermediate',
        'show_live_wpm'       => 1,
        'show_live_accuracy'  => 1,
        'show_live_word_count'=> 1
    ];

    if (!$pdo) {
        return $defaultSettings;
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM user_settings WHERE user_id = :uid LIMIT 1");
        $stmt->execute([':uid' => $userId]);
        $row = $stmt->fetch();

        if ($row) {
            return array_merge($defaultSettings, $row);
        }

        // Initialize default row for new user
        $insert = $pdo->prepare("
            INSERT INTO user_settings (user_id) 
            VALUES (:uid) 
            ON DUPLICATE KEY UPDATE updated_at = CURRENT_TIMESTAMP
        ");
        $insert->execute([':uid' => $userId]);
        return $defaultSettings;
    } catch (Exception $e) {
        error_log('Failed to fetch settings: ' . $e->getMessage());
        return $defaultSettings;
    }
}
