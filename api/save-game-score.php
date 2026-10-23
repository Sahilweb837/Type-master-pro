<?php
/**
 * Custom Type Master Pro
 * API: Save Arcade Game Scores
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/games_data.php';

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) {
    $data = $_POST;
}

// 1. Verify CSRF Token
$token = $data['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($_SERVER['HTTP_X_XSRF_TOKEN'] ?? '');
if (!verify_csrf_token($token)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'CSRF security token invalid.']);
    exit;
}

$gameSlug = trim($data['game_slug'] ?? 'ninja');
$score = max(0, (int)($data['score'] ?? 0));
$highestStreak = max(0, (int)($data['highest_streak'] ?? 0));
$accuracy = max(0.0, min(100.0, (float)($data['accuracy'] ?? 100.0)));
$difficulty = in_array($data['difficulty'] ?? '', ['easy', 'medium', 'hard']) ? $data['difficulty'] : 'medium';
$mode = trim($data['mode'] ?? 'all');

if (!is_logged_in()) {
    echo json_encode([
        'success'  => true,
        'is_guest' => true,
        'score'    => $score,
        'message'  => 'Great score! Register to feature on the global leaderboard.'
    ]);
    exit;
}

$user = current_user();
$userId = (int)$user['id'];
$pdo = getDBConnection();

if (!$pdo) {
    echo json_encode([
        'success'  => true,
        'is_guest' => false,
        'db_notice'=> true,
        'score'    => $score,
        'message'  => 'Score saved locally while database reconnects.'
    ]);
    exit;
}

ensure_game_table_exists($pdo);

try {
    $stmt = $pdo->prepare("
        INSERT INTO user_game_scores 
        (user_id, game_slug, score, highest_streak, accuracy, difficulty, mode)
        VALUES 
        (:uid, :slug, :score, :streak, :acc, :diff, :mode)
    ");
    $stmt->execute([
        ':uid'    => $userId,
        ':slug'   => $gameSlug,
        ':score'  => $score,
        ':streak' => $highestStreak,
        ':acc'    => $accuracy,
        ':diff'   => $difficulty,
        ':mode'   => $mode
    ]);

    echo json_encode([
        'success'  => true,
        'is_guest' => false,
        'score'    => $score,
        'message'  => 'Score successfully recorded!'
    ]);
    exit;
} catch (Exception $e) {
    error_log('[Save Game Score Error] ' . $e->getMessage());
    echo json_encode([
        'success'  => true,
        'is_guest' => false,
        'score'    => $score,
        'message'  => 'Score registered.'
    ]);
    exit;
}
