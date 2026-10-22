<?php
/**
 * Custom Type Master Pro
 * API: Save Completed Typing Test Session
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

// Allow guest tests to proceed with notice
if (!is_logged_in()) {
    echo json_encode([
        'success' => false,
        'guest'   => true,
        'message' => 'Test completed in guest mode. Register or login to record permanent stats!'
    ]);
    exit;
}

$user = current_user();

// Read JSON input
$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON payload.']);
    exit;
}

// CSRF check
$token = $data['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
if (!verify_csrf_token($token)) {
    echo json_encode(['success' => false, 'message' => 'CSRF verification failed.']);
    exit;
}

$pdo = getDBConnection();
if (!$pdo) {
    echo json_encode(['success' => false, 'message' => 'Database connection unavailable.']);
    exit;
}

try {
    $passageId = !empty($data['passage_id']) ? (int)$data['passage_id'] : null;
    $grossWpm = max(0.0, (float)($data['gross_wpm'] ?? 0));
    $netWpm = max(0.0, (float)($data['net_wpm'] ?? 0));
    $accuracy = max(0.0, min(100.0, (float)($data['accuracy'] ?? 100)));
    $durationSeconds = max(1, (int)($data['duration_seconds'] ?? 60));
    $elapsedSeconds = max(0.1, (float)($data['elapsed_seconds'] ?? 60));
    $wordsTyped = max(0, (int)($data['words_typed'] ?? 0));
    $totalKeystrokes = max(0, (int)($data['total_keystrokes'] ?? 0));
    $correctKeystrokes = max(0, (int)($data['correct_keystrokes'] ?? 0));
    $errorKeystrokes = max(0, (int)($data['error_keystrokes'] ?? 0));
    $backspaceCount = max(0, (int)($data['backspace_count'] ?? 0));
    $difficulty = in_array($data['difficulty'] ?? '', ['beginner', 'intermediate', 'advanced', 'custom']) 
                ? $data['difficulty'] 
                : 'intermediate';
    $testMode = in_array($data['test_mode'] ?? '', ['timed', 'passage', 'custom']) 
              ? $data['test_mode'] 
              : 'timed';

    $stmt = $pdo->prepare("
        INSERT INTO typing_results 
        (user_id, passage_id, gross_wpm, net_wpm, accuracy, duration_seconds, elapsed_seconds, words_typed, total_keystrokes, correct_keystrokes, error_keystrokes, backspace_count, difficulty, test_mode)
        VALUES
        (:uid, :pid, :gwpm, :nwpm, :acc, :dur, :elap, :wtyped, :tkeys, :ckeys, :ekeys, :bkeys, :diff, :mode)
    ");

    $stmt->execute([
        ':uid'    => $user['id'],
        ':pid'    => $passageId,
        ':gwpm'   => $grossWpm,
        ':nwpm'   => $netWpm,
        ':acc'    => $accuracy,
        ':dur'    => $durationSeconds,
        ':elap'   => $elapsedSeconds,
        ':wtyped' => $wordsTyped,
        ':tkeys'  => $totalKeystrokes,
        ':ckeys'  => $correctKeystrokes,
        ':ekeys'  => $errorKeystrokes,
        ':bkeys'  => $backspaceCount,
        ':diff'   => $difficulty,
        ':mode'   => $testMode
    ]);

    $insertId = (int)$pdo->lastInsertId();

    echo json_encode([
        'success'   => true,
        'result_id' => $insertId,
        'message'   => 'Session recorded successfully.'
    ]);
} catch (Exception $e) {
    error_log('API save result error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while saving the typing record.'
    ]);
}
