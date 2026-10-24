<?php
/**
 * Custom Type Master Pro
 * Database Connection Configuration (PDO)
 * File: config/database.php
 *
 * App URL : https://phpstack-1264585-6742270.cloudwaysapps.com/
 * Hosting : Cloudways (single server)
 * DB Host : 127.0.0.1 (Cloudways same-server standard)
 * DB Name : jgryfuqubz
 * DB User : jgryfuqubz
 * DB Pass : xD3F6P4ZzS
 * DB Port : 3306
 */

// 1. Lightweight .env loader (safe if putenv() is disabled on Cloudways)
(function() {
    $envFile = __DIR__ . '/../.env';
    if (@file_exists($envFile) && @is_readable($envFile)) {
        $lines = @file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines !== false) {
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '' || $line[0] === '#') {
                    continue;
                }
                if (strpos($line, '=') !== false) {
                    list($key, $val) = explode('=', $line, 2);
                    $key = trim($key);
                    $val = trim($val);
                    $len = strlen($val);
                    if ($len >= 2) {
                        if (($val[0] === '"' && $val[$len - 1] === '"') || ($val[0] === "'" && $val[$len - 1] === "'")) {
                            $val = substr($val, 1, -1);
                        }
                    }
                    if (!isset($_SERVER[$key]) && !isset($_ENV[$key])) {
                        if (function_exists('putenv')) {
                            @putenv("{$key}={$val}");
                        }
                        $_ENV[$key] = $val;
                        $_SERVER[$key] = $val;
                    }
                }
            }
        }
    }
})();

// 2. Parse Cloud Database URLs (optional, overrides defaults if present)
$cloudDbUrl = getenv('DATABASE_URL') ?: (getenv('MYSQL_URL') ?: (getenv('JAWSDB_URL') ?: getenv('CLEARDB_DATABASE_URL')));
$parsedUrl = [];
if ($cloudDbUrl) {
    $parts = parse_url($cloudDbUrl);
    if (!empty($parts['host'])) {
        $parsedUrl['host'] = $parts['host'];
        $parsedUrl['port'] = $parts['port'] ?? 3306;
        $parsedUrl['user'] = $parts['user'] ?? '';
        $parsedUrl['pass'] = $parts['pass'] ?? '';
        $parsedUrl['name'] = isset($parts['path']) ? ltrim($parts['path'], '/') : '';
    }
}

// 3. Resolve Database Credentials
// Cloudways defaults (hardcoded as last fallback)
$dbHost = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? ($_SERVER['DB_HOST'] ?? ($parsedUrl['host'] ?? '127.0.0.1')));
$dbPort = getenv('DB_PORT') ?: ($_ENV['DB_PORT'] ?? ($_SERVER['DB_PORT'] ?? ($parsedUrl['port'] ?? '3306')));
$dbName = getenv('DB_NAME') ?: ($_ENV['DB_NAME'] ?? ($_SERVER['DB_NAME'] ?? ($parsedUrl['name'] ?? 'jgryfuqubz')));
$dbUser = getenv('DB_USER') ?: ($_ENV['DB_USER'] ?? ($_SERVER['DB_USER'] ?? ($parsedUrl['user'] ?? 'jgryfuqubz')));
$dbPass = getenv('DB_PASS') ?: ($_ENV['DB_PASS'] ?? ($_SERVER['DB_PASS'] ?? ($parsedUrl['pass'] ?? 'xD3F6P4ZzS')));
$dbCharset = getenv('DB_CHARSET') ?: ($_ENV['DB_CHARSET'] ?? ($_SERVER['DB_CHARSET'] ?? 'utf8mb4'));

// Cloudways fix: localhost / ::1 ko 127.0.0.1 me force karo
if ($dbHost === 'localhost' || $dbHost === '::1') {
    $dbHost = '127.0.0.1';
}

if (!defined('DB_HOST')) define('DB_HOST', $dbHost);
if (!defined('DB_PORT')) define('DB_PORT', $dbPort);
if (!defined('DB_NAME')) define('DB_NAME', $dbName);
if (!defined('DB_USER')) define('DB_USER', $dbUser);
if (!defined('DB_PASS')) define('DB_PASS', $dbPass);
if (!defined('DB_CHARSET')) define('DB_CHARSET', $dbCharset);

/**
 * Returns the last technical database connection error message for diagnostics.
 */
function getDBLastError(): string {
    global $g_dbLastError;
    return $g_dbLastError ?? '';
}

/**
 * Returns a singleton PDO database connection instance.
 */
function getDBConnection(): ?PDO {
    static $pdo = null;
    global $g_dbLastError;

    if ($pdo !== null) {
        return $pdo;
    }

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET,
        PDO::ATTR_TIMEOUT            => 5
    ];

    $attempts = [];

    // Attempt 1: 127.0.0.1 TCP (Cloudways standard)
    $attempts[] = [
        'dsn'  => sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', DB_HOST, DB_PORT, DB_NAME, DB_CHARSET),
        'user' => DB_USER,
        'pass' => DB_PASS,
        'desc' => 'Cloudways primary (' . DB_USER . '@' . DB_HOST . ':' . DB_PORT . '/' . DB_NAME . ')'
    ];

    // Attempt 2: localhost fallback (in case Cloudways uses socket for localhost)
    $attempts[] = [
        'dsn'  => sprintf('mysql:host=localhost;port=%s;dbname=%s;charset=%s', DB_PORT, DB_NAME, DB_CHARSET),
        'user' => DB_USER,
        'pass' => DB_PASS,
        'desc' => 'Fallback localhost'
    ];

    // Attempt 3: Known UNIX sockets (Cloudways / cPanel)
    $knownSockets = [
        '/var/run/mysqld/mysqld.sock',
        '/tmp/mysql.sock',
        '/var/lib/mysql/mysql.sock',
        '/run/mysqld/mysqld.sock'
    ];
    foreach ($knownSockets as $sock) {
        if (@file_exists($sock)) {
            $attempts[] = [
                'dsn'  => sprintf('mysql:unix_socket=%s;dbname=%s;charset=%s', $sock, DB_NAME, DB_CHARSET),
                'user' => DB_USER,
                'pass' => DB_PASS,
                'desc' => 'UNIX Socket (' . $sock . ')'
            ];
            break;
        }
    }

    // Attempt 4: Local development fallback (XAMPP / WAMP root with empty password)
    $localDatabases = [DB_NAME, 'custom_type_master_pro', 'type_master'];
    foreach (array_unique($localDatabases) as $fallbackDb) {
        $attempts[] = [
            'dsn'  => sprintf('mysql:host=127.0.0.1;port=%s;dbname=%s;charset=%s', DB_PORT, $fallbackDb, DB_CHARSET),
            'user' => 'root',
            'pass' => '',
            'desc' => "Local dev fallback (root@127.0.0.1 / db={$fallbackDb})"
        ];
    }

    $errors = [];
    foreach ($attempts as $attempt) {
        try {
            $pdo = new PDO($attempt['dsn'], $attempt['user'], $attempt['pass'], $options);
            return $pdo;
        } catch (PDOException $e) {
            $errors[] = $attempt['desc'] . ': ' . $e->getMessage();
        }
    }

    $g_dbLastError = !empty($errors) ? end($errors) : 'Unknown database connection error';
    error_log('[TypeMaster DB Connection Error] ' . implode(' | ', $errors));

    return null;
}