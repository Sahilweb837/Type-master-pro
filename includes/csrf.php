<?php
/**
 * Custom Type Master Pro
 * CSRF Protection Utilities
 */

/**
 * Generates or retrieves the active session CSRF token.
 *
 * @return string
 */
function get_csrf_token(): string {
    if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
        @session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verifies a CSRF token from POST or headers against the session token.
 *
 * @param string|null $token
 * @return bool
 */
function verify_csrf_token(?string $token = null): bool {
    if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
        @session_start();
    }

    if ($token === null) {
        $token = $_POST['csrf_token'] 
              ?? $_SERVER['HTTP_X_CSRF_TOKEN'] 
              ?? ($_SERVER['HTTP_X_XSRF_TOKEN'] ?? '');
    }

    $sessionToken = $_SESSION['csrf_token'] ?? '';
    if (empty($token) || empty($sessionToken)) {
        error_log(sprintf(
            '[CSRF Warning] Validation failed: received_token=%s, session_token_present=%s, session_id=%s',
            empty($token) ? 'EMPTY' : 'PROVIDED',
            empty($sessionToken) ? 'NO' : 'YES',
            session_id() ?: 'NONE'
        ));
        return false;
    }

    $isValid = hash_equals($sessionToken, $token);
    if (!$isValid) {
        error_log('[CSRF Warning] Token mismatch detected.');
    }
    return $isValid;
}

/**
 * Helper to render a hidden HTML input for forms.
 *
 * @return string
 */
function csrf_field(): string {
    $token = htmlspecialchars(get_csrf_token(), ENT_QUOTES, 'UTF-8');
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}
