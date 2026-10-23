<?php
/**
 * Custom Type Master Pro
 * CSRF Protection Utilities
 */

/**
 * Generates or retrieves the active session CSRF token.
 * Automatically synchronizes with an XSRF-TOKEN cookie for double-submit reliability.
 *
 * @return string
 */
function get_csrf_token(): string {
    if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
        @session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        // If an existing valid cookie token exists, reuse it to maintain continuity
        if (!empty($_COOKIE['XSRF-TOKEN']) && preg_match('/^[a-f0-9]{64}$/i', $_COOKIE['XSRF-TOKEN'])) {
            $_SESSION['csrf_token'] = $_COOKIE['XSRF-TOKEN'];
        } else {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    // Set or refresh the client-accessible XSRF-TOKEN cookie
    if (!headers_sent()) {
        $isHttps = (
            (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off')
            || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
            || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')
        );
        @setcookie('XSRF-TOKEN', $_SESSION['csrf_token'], [
            'expires'  => time() + 86400 * 7,
            'path'     => '/',
            'secure'   => $isHttps,
            'httponly' => false,
            'samesite' => 'Lax'
        ]);
    }

    return $_SESSION['csrf_token'];
}

/**
 * Verifies a CSRF token from POST, headers, or JSON body.
 * Implements primary session check with double-submit cookie fallback for live servers.
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

        // If still empty and request has a JSON body, inspect JSON payload
        if (empty($token) && !empty($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
            $rawInput = file_get_contents('php://input');
            if (!empty($rawInput)) {
                $decoded = json_decode($rawInput, true);
                if (is_array($decoded) && !empty($decoded['csrf_token'])) {
                    $token = $decoded['csrf_token'];
                }
            }
        }
    }

    if (empty($token)) {
        error_log(sprintf(
            '[CSRF Warning] Validation failed: token is empty. Session ID: %s',
            session_id() ?: 'NONE'
        ));
        return false;
    }

    // 1. Primary Check: Validate against active session token
    $sessionToken = $_SESSION['csrf_token'] ?? '';
    if (!empty($sessionToken) && hash_equals($sessionToken, $token)) {
        return true;
    }

    // 2. Secondary Fallback: Double-Submit Cookie verification (resilient against session storage glitches)
    $cookieToken = $_COOKIE['XSRF-TOKEN'] ?? '';
    if (!empty($cookieToken) && hash_equals($cookieToken, $token)) {
        // Restore session token so subsequent operations remain synchronized
        $_SESSION['csrf_token'] = $cookieToken;
        return true;
    }

    error_log(sprintf(
        '[CSRF Warning] Token mismatch detected. Received token length: %d, session token exists: %s, cookie token exists: %s',
        strlen($token),
        !empty($sessionToken) ? 'YES' : 'NO',
        !empty($cookieToken) ? 'YES' : 'NO'
    ));
    return false;
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
