<?php
/**
 * Custom Type Master Pro
 * Application Configuration & Session Bootstrap
 */

// Production error reporting - log to server, avoid exposing traces to end users
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

// Set default timezone
date_default_timezone_set('UTC');

// Secure Session Configuration
if (session_status() === PHP_SESSION_NONE) {
    // Only set cookie params if headers not sent
    if (!headers_sent()) {
        $cookieParams = [
            'lifetime' => 86400 * 7, // 7 days
            'path'     => '/',
            'domain'   => '',
            'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
            'httponly' => true,
            'samesite' => 'Lax'
        ];
        session_set_cookie_params($cookieParams);
    }
    session_start();
}

// App Constants
define('APP_NAME', 'Custom Type Master Pro');
define('APP_VERSION', '2.0.0');
define('APP_AUTHOR', 'Full-Stack Team');

// Determine Base URL dynamically
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));

// Normalize base path
$basePath = preg_replace('#/(api|admin|includes|config|assets)(/.*)?$#', '', $scriptDir);
if ($basePath === '/' || $basePath === '\\') {
    $basePath = '';
}
define('BASE_URL', rtrim($protocol . $host . $basePath, '/'));

// Include Database connection
require_once __DIR__ . '/database.php';
