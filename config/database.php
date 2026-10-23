    <?php
    /**
     * Custom Type Master Pro
     * Database Connection Configuration (PDO)
     */

    define('DB_HOST', '127.0.0.1');
    define('DB_PORT', '3306');
    define('DB_NAME', 'jgryfuqubz');
    define('DB_USER', 'jgryfuqubz');
    define('DB_PASS', 'jgryfuqubzxD3F6P4ZzS');
    define('DB_CHARSET', 'utf8mb4');

    /**
     * Returns a singleton PDO database connection instance.
     *
     * @return PDO|null
     */
    function getDBConnection(): ?PDO {
        static $pdo = null;

        if ($pdo === null) {
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
            ];

            // 1. Primary Attempt with configured credentials
            try {
                $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', DB_HOST, DB_PORT, DB_NAME, DB_CHARSET);
                $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
                return $pdo;
            } catch (PDOException $e) {
                error_log('Primary DB Connection failed (' . DB_USER . '@' . DB_HOST . '): ' . $e->getMessage());
            }

            // 2. Secondary Attempt: Try localhost fallback if running in local environment
            try {
                $dsnFallback = sprintf('mysql:host=127.0.0.1;port=%s;dbname=%s;charset=%s', DB_PORT, DB_NAME, DB_CHARSET);
                $pdo = new PDO($dsnFallback, 'root', '', $options);
                return $pdo;
            } catch (PDOException $e2) {
                try {
                    $dsnOld = sprintf('mysql:host=127.0.0.1;port=%s;dbname=custom_type_master_pro;charset=%s', DB_PORT, DB_CHARSET);
                    $pdo = new PDO($dsnOld, 'root', '', $options);
                    return $pdo;
                } catch (PDOException $e3) {
                    error_log('Database Connection Final Error: ' . $e3->getMessage());
                    return null;
                }
            }
        }

        return $pdo;
    }
