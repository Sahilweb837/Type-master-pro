<?php
/**
 * Custom Type Master Pro
 * Arcade & Games Data & Persistence Helper
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';

/**
 * Ensures user_game_scores table exists in database.
 * Auto-creates so it works instantly on live servers without manual SQL import.
 */
function ensure_game_table_exists(?PDO $pdo = null): void {
    if (!$pdo) {
        $pdo = getDBConnection();
    }
    if (!$pdo) {
        return;
    }

    try {
        $sql = "
            CREATE TABLE IF NOT EXISTS `user_game_scores` (
                `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `user_id` INT UNSIGNED NOT NULL,
                `game_slug` VARCHAR(50) NOT NULL,
                `score` INT UNSIGNED NOT NULL DEFAULT 0,
                `highest_streak` INT UNSIGNED NOT NULL DEFAULT 0,
                `accuracy` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
                `difficulty` ENUM('easy', 'medium', 'hard') NOT NULL DEFAULT 'medium',
                `mode` VARCHAR(50) NOT NULL DEFAULT 'all',
                `played_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX `idx_user_game` (`user_id`, `game_slug`),
                INDEX `idx_game_score` (`game_slug`, `score` DESC)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        $pdo->exec($sql);
    } catch (Exception $e) {
        error_log('[Game Table Check] ' . $e->getMessage());
    }
}

/**
 * Returns available typing games catalog.
 *
 * @return array
 */
function get_all_games(): array {
    return [
        [
            'slug'        => 'ninja',
            'title'       => 'Keyboard Ninja',
            'badge'       => 'Most Popular',
            'icon'        => 'bi-slash-circle-fill',
            'color'       => '#ff4d00',
            'description' => 'Slash flying fruit targets mid-air with lightning katana strikes. Dodge explosive bombs and isolate keyboard rows to build reflex muscle memory.',
            'url'         => 'game-ninja.php',
            'modes'       => ['Home Row', 'Top Row', 'Bottom Row', 'Numbers', 'All Letters'],
            'difficulties'=> ['Easy', 'Medium', 'Hard'],
            'features'    => ['Fruit Slicing Physics', 'Sword Sound Effects', 'Combo Streaks', 'Bomb Hazards']
        ],
        [
            'slug'        => 'space',
            'title'       => 'Space Word Defender',
            'badge'       => 'Arcade Shooter',
            'icon'        => 'bi-rocket-takeoff-fill',
            'color'       => '#38bdf8',
            'description' => 'Defend your planetary base from waves of invading alien spacecraft. Type full vocabulary words to fire laser cannons and obliterate targets.',
            'url'         => 'game-space.php',
            'modes'       => ['Short Words', 'Common Vocabulary', 'Tech & Code Terms'],
            'difficulties'=> ['Cadet', 'Commander', 'Elite'],
            'features'    => ['Laser Particle Beams', 'Progressive Waves', 'Shield HP System', 'Wave Multipliers']
        ]
    ];
}

/**
 * Returns personal best high score for a specific user and game.
 *
 * @param int $userId
 * @param string $gameSlug
 * @return array
 */
function get_user_game_high_score(int $userId, string $gameSlug): array {
    $default = ['score' => 0, 'highest_streak' => 0, 'accuracy' => 0.0, 'played_at' => null];
    if ($userId <= 0) {
        return $default;
    }

    $pdo = getDBConnection();
    if (!$pdo) {
        return $default;
    }

    ensure_game_table_exists($pdo);

    try {
        $stmt = $pdo->prepare("
            SELECT MAX(score) AS score, MAX(highest_streak) AS highest_streak, MAX(accuracy) AS accuracy, MAX(played_at) AS played_at
            FROM user_game_scores
            WHERE user_id = :uid AND game_slug = :slug
        ");
        $stmt->execute([':uid' => $userId, ':slug' => $gameSlug]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row && $row['score'] !== null) {
            return [
                'score'          => (int)$row['score'],
                'highest_streak' => (int)$row['highest_streak'],
                'accuracy'       => (float)$row['accuracy'],
                'played_at'      => $row['played_at']
            ];
        }
    } catch (Exception $e) {
        error_log('[Get Game Score] ' . $e->getMessage());
    }

    return $default;
}

/**
 * Returns top leaderboard entries for a given game.
 *
 * @param string $gameSlug
 * @param int $limit
 * @return array
 */
function get_game_leaderboard(string $gameSlug, int $limit = 5): array {
    $pdo = getDBConnection();
    if (!$pdo) {
        return [];
    }

    ensure_game_table_exists($pdo);

    try {
        $stmt = $pdo->prepare("
            SELECT g.score, g.highest_streak, g.accuracy, g.difficulty, g.played_at, u.username, u.full_name
            FROM user_game_scores g
            JOIN users u ON g.user_id = u.id
            WHERE g.game_slug = :slug
            ORDER BY g.score DESC, g.accuracy DESC
            LIMIT :lim
        ");
        $stmt->bindValue(':slug', $gameSlug, PDO::PARAM_STR);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log('[Get Leaderboard] ' . $e->getMessage());
        return [];
    }
}
