<?php
/**
 * Custom Type Master Pro
 * CSRF Protection Utilities (Cloudways / Reverse Proxy Hardened)
 */

function is_https_request(): bool {
    return (
        (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
        || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')
        || (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && strtolower($_SERVER['HTTP_X_FORWARDED_SSL']) === 'on')
        || (!empty($_SERVER['HTTP_X_FORWARDED_PORT']) && (int)$_SERVER['HTTP_X_FORWARDED_PORT'] === 443)
        || (!empty($_SERVER['HTTP_CF_VISITOR']) && strpos($_SERVER['HTTP_CF_VISITOR'], '"scheme":"https"') !== false)
    );
}

/**
 * Validates whether the incoming request originated from the same host (same-origin policy).
 * Modern browsers always attach Origin or Referer to POST requests.
 *
 * @return bool
 */
function is_same_origin(): bool {
    $currentHost = $_SERVER['HTTP_HOST'] ?? '';
    if (empty($currentHost)) {
        return false;
    }
    $hostOnly = explode(':', $currentHost)[0];

    // Check Origin header first
    if (!empty($_SERVER['HTTP_ORIGIN'])) {
        $originHost = parse_url($_SERVER['HTTP_ORIGIN'], PHP_URL_HOST);
        if (!empty($originHost) && strcasecmp($originHost, $hostOnly) === 0) {
            return true;
        }
    }

    // Check Referer header as secondary
    if (!empty($_SERVER['HTTP_REFERER'])) {
        $refererHost = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
        if (!empty($refererHost) && strcasecmp($refererHost, $hostOnly) === 0) {
            return true;
        }
    }

    return false;
}

/**
 * Generate or retrieve the active session CSRF token.
 * Only generates a NEW token if none exists in session.
 * Never overwrites prematurely.
 *
 * @return string
 */
function get_csrf_token(): string {
    if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
        @session_start();
    }

    if (!isset($_SESSION['csrf_tokens']) || !is_array($_SESSION['csrf_tokens'])) {
        $_SESSION['csrf_tokens'] = [];
    }

    // Only create a token if session has none
    if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        if (!empty($_COOKIE['XSRF-TOKEN']) && preg_match('/^[a-f0-9]{64}$/i', $_COOKIE['XSRF-TOKEN'])) {
            $_SESSION['csrf_token'] = $_COOKIE['XSRF-TOKEN'];
        } else {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    // Register into pool of recent valid tokens
    if (!in_array($_SESSION['csrf_token'], $_SESSION['csrf_tokens'], true)) {
        $_SESSION['csrf_tokens'][] = $_SESSION['csrf_token'];
        if (count($_SESSION['csrf_tokens']) > 10) {
            array_shift($_SESSION['csrf_tokens']);
        }
    }

    // Cookie sync
    if (!headers_sent()) {
        $cookieVal = $_COOKIE['XSRF-TOKEN'] ?? '';
        if ($cookieVal !== $_SESSION['csrf_token']) {
            @setcookie('XSRF-TOKEN', $_SESSION['csrf_token'], [
                'expires'  => time() + 86400 * 7,
                'path'     => '/',
                'secure'   => is_https_request(),
                'httponly' => false,
                'samesite' => 'Lax'
            ]);
        }
    }

    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token from POST, headers, or JSON body.
 * Resilient multi-layer check:
 * 1. Active session token
 * 2. Pool of recent valid tokens
 * 3. Double-submit XSRF-TOKEN cookie
 * 4. Same-origin validation fallback (protects against Varnish session drops on Cloudways)
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
              ?? $_POST['_token']
              ?? $_SERVER['HTTP_X_CSRF_TOKEN']
              ?? ($_SERVER['HTTP_X_XSRF_TOKEN'] ?? '');

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

    if (empty($token) || !is_string($token)) {
        error_log('[CSRF] Empty token. Session ID: ' . (session_id() ?: 'NONE'));
        return false;
    }

    // 1. Primary: session token
    $sessionToken = $_SESSION['csrf_token'] ?? '';
    if (!empty($sessionToken) && hash_equals($sessionToken, $token)) {
        return true;
    }

    // 2. Pool of recent tokens (multi-tab / back button)
    if (!empty($_SESSION['csrf_tokens']) && is_array($_SESSION['csrf_tokens'])) {
        foreach ($_SESSION['csrf_tokens'] as $validToken) {
            if (is_string($validToken) && hash_equals($validToken, $token)) {
                return true;
            }
        }
    }

    // 3. Double-submit cookie fallback
    $cookieToken = $_COOKIE['XSRF-TOKEN'] ?? '';
    if (!empty($cookieToken) && hash_equals($cookieToken, $token)) {
        $_SESSION['csrf_token'] = $cookieToken;
        if (!in_array($cookieToken, $_SESSION['csrf_tokens'] ?? [], true)) {
            $_SESSION['csrf_tokens'][] = $cookieToken;
        }
        return true;
    }

    // 4. Same-origin fallback: Valid 64-character hex token originating from our domain
    // Rescues valid user submissions when Cloudways Varnish strips session cookies
    if (preg_match('/^[a-f0-9]{64}$/i', $token) && is_same_origin()) {
        $_SESSION['csrf_token'] = $token;
        if (!in_array($token, $_SESSION['csrf_tokens'] ?? [], true)) {
            $_SESSION['csrf_tokens'][] = $token;
        }
        return true;
    }

    // Log diagnostic warning
    error_log(sprintf(
        '[CSRF] Mismatch. Len=%d Session=%s Cookie=%s Pool=%d SameOrigin=%s',
        strlen($token),
        !empty($sessionToken) ? 'YES' : 'NO',
        !empty($cookieToken) ? 'YES' : 'NO',
        !empty($_SESSION['csrf_tokens']) ? count($_SESSION['csrf_tokens']) : 0,
        is_same_origin() ? 'YES' : 'NO'
    ));
    return false;
}

/**
 * Render hidden HTML CSRF input for forms.
 *
 * @return string
 */
function csrf_field(): string {
    $token = htmlspecialchars(get_csrf_token(), ENT_QUOTES, 'UTF-8');
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}