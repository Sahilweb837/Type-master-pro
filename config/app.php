<?php
/**
 * Custom Type Master Pro
 * Application Configuration & Session Bootstrap
 */

// Production error reporting - log to server, avoid exposing traces to end users
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

// Output buffering prevents premature header dispatch caused by whitespace or includes
if (ob_get_level() === 0) {
    ob_start();
}

// Set default timezone
date_default_timezone_set('UTC');

// Detect HTTPS behind reverse proxies, CDNs (e.g. Cloudflare), and standard SSL
$isHttps = (
    (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off')
    || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
    || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')
    || (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && strtolower($_SERVER['HTTP_X_FORWARDED_SSL']) === 'on')
    || (!empty($_SERVER['HTTP_CF_VISITOR']) && strpos($_SERVER['HTTP_CF_VISITOR'], '"scheme":"https"') !== false)
);

// Secure Session Configuration
if (session_status() === PHP_SESSION_NONE) {
    // If the server's session save path is misconfigured or unwriteable, fallback to system temp dir
    $savePath = session_save_path();
    if (!empty($savePath) && (!@is_dir($savePath) || !@is_writable($savePath))) {
        $sysTmp = sys_get_temp_dir();
        if (@is_dir($sysTmp) && @is_writable($sysTmp)) {
            @session_save_path($sysTmp);
        }
    }

    if (!headers_sent()) {
        $cookieParams = [
            'lifetime' => 86400 * 7, // 7 days
            'path'     => '/',
            'domain'   => '',
            'secure'   => $isHttps,
            'httponly' => true,
            'samesite' => 'Lax'
        ];
        session_set_cookie_params($cookieParams);
    }
    @session_start();
}

// App Constants
define('APP_NAME', 'Custom Type Master Pro');
define('APP_VERSION', '2.1.0');
define('APP_AUTHOR', 'Full-Stack Team');

// Admin Secure Route Configuration (/nickadmin005#)
define('ADMIN_SLUG', 'nickadmin005');
define('ADMIN_HASH_TAG', 'nickadmin005#');

// Determine Base URL dynamically
$protocol = $isHttps ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));

// Normalize base path (recognizes api, admin, nickadmin005, includes, config, assets)
$basePath = preg_replace('#/(api|admin|nickadmin005|includes|config|assets)(/.*)?$#', '', $scriptDir);
if ($basePath === '/' || $basePath === '\\' || $basePath === '.') {
    $basePath = '';
}
define('BASE_URL', rtrim($protocol . $host . $basePath, '/'));
define('ADMIN_URL', BASE_URL . '/' . ADMIN_SLUG);

// Include Database connection
require_once __DIR__ . '/database.php';
