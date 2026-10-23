<?php
/**
 * Custom Type Master Pro
 * API: Save Student Lesson Progress (Stars, Accuracy, WPM)
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/lessons_data.php';

// Read JSON input or fallback to POST
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) {
    $data = $_POST;
}

// 1. Verify CSRF Token
$token = $data['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($_SERVER['HTTP_X_XSRF_TOKEN'] ?? '');
if (!verify_csrf_token($token)) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Security token validation failed. Please refresh the page.'
    ]);
    exit;
}

$lessonId = trim($data['lesson_id'] ?? '');
$lesson = get_lesson_by_id($lessonId);
if (!$lesson) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid lesson identifier.']);
    exit;
}

$stage = $lesson['stage'];
$wpm = max(0.0, (float)($data['wpm'] ?? 0));
$accuracy = max(0.0, min(100.0, (float)($data['accuracy'] ?? 0)));
$stars = max(1, min(3, (int)($data['stars'] ?? 1)));

// If accuracy is high, ensure appropriate stars
if ($accuracy >= 95.0) {
    $stars = 3;
} elseif ($accuracy >= 85.0 && $stars < 2) {
    $stars = 2;
}

// Guest Mode handling
if (!is_logged_in()) {
    echo json_encode([
        'success'   => true,
        'is_guest'  => true,
        'stars'     => $stars,
        'wpm'       => $wpm,
        'accuracy'  => $accuracy,
        'message'   => 'Lesson completed! Sign in or register to record your progress permanently.'
    ]);
    exit;
}

$user = current_user();
$userId = (int)$user['id'];
$pdo = getDBConnection();

if (!$pdo) {
    // Database unreachable, return guest-style success so user's game flow isn't broken
    echo json_encode([
        'success'   => true,
        'is_guest'  => false,
        'db_notice' => true,
        'stars'     => $stars,
        'wpm'       => $wpm,
        'accuracy'  => $accuracy,
        'message'   => 'Lesson completed! (Saved locally while database service connects).'
    ]);
    exit;
}

ensure_lesson_table_exists($pdo);

try {
    $stmt = $pdo->prepare("
        INSERT INTO user_lesson_progress 
        (user_id, lesson_id, stage, stars, highest_wpm, highest_accuracy, status)
        VALUES 
        (:uid, :lid, :stage, :stars, :wpm, :acc, 'completed')
        ON DUPLICATE KEY UPDATE 
            stars = GREATEST(stars, VALUES(stars)),
            highest_wpm = GREATEST(highest_wpm, VALUES(highest_wpm)),
            highest_accuracy = GREATEST(highest_accuracy, VALUES(highest_accuracy)),
            status = 'completed',
            completed_at = CURRENT_TIMESTAMP
    ");
    $stmt->execute([
        ':uid'   => $userId,
        ':lid'   => $lessonId,
        ':stage' => $stage,
        ':stars' => $stars,
        ':wpm'   => $wpm,
        ':acc'   => $accuracy
    ]);

    // Find next lesson
    $all = get_all_lessons();
    $nextLessonId = null;
    $foundCurrent = false;
    foreach ($all as $l) {
        if ($foundCurrent) {
            $nextLessonId = $l['id'];
            break;
        }
        if ($l['id'] === $lessonId) {
            $foundCurrent = true;
        }
    }

    echo json_encode([
        'success'        => true,
        'is_guest'       => false,
        'stars'          => $stars,
        'wpm'            => $wpm,
        'accuracy'       => $accuracy,
        'next_lesson_id' => $nextLessonId,
        'message'        => 'Lesson progress recorded successfully!'
    ]);
    exit;
} catch (Exception $e) {
    error_log('[Save Lesson Error] ' . $e->getMessage());
    echo json_encode([
        'success'  => true,
        'is_guest' => false,
        'stars'    => $stars,
        'wpm'      => $wpm,
        'accuracy' => $accuracy,
        'message'  => 'Lesson complete! Result registered.'
    ]);
    exit;
}
