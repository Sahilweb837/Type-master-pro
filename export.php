<?php
/**
 * Custom Type Master Pro
 * User Practice History Exporter (CSV & JSON)
 */

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/auth.php';

require_login();

$user = current_user();
$userId = $user['id'];
$format = strtolower(trim($_GET['format'] ?? 'csv'));

$pdo = getDBConnection();
$results = [];

if ($pdo) {
    try {
        $stmt = $pdo->prepare("
            SELECT 
                r.id AS session_id,
                p.title AS passage_title,
                r.difficulty,
                r.net_wpm,
                r.gross_wpm,
                r.accuracy,
                r.words_typed,
                r.total_keystrokes,
                r.correct_keystrokes,
                r.error_keystrokes,
                r.backspace_count,
                r.duration_seconds,
                r.elapsed_seconds,
                r.test_mode,
                r.created_at
            FROM typing_results r
            LEFT JOIN typing_passages p ON r.passage_id = p.id
            WHERE r.user_id = :uid
            ORDER BY r.created_at DESC
        ");
        $stmt->execute([':uid' => $userId]);
        $results = $stmt->fetchAll();
    } catch (Exception $e) {
        error_log('Export error: ' . $e->getMessage());
    }
}

$filename = sprintf('typemaster-history-%s-%s', $user['username'], date('Ymd-His'));

if ($format === 'json') {
    header('Content-Type: application/json; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '.json"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $exportData = [
        'app'         => APP_NAME,
        'version'     => APP_VERSION,
        'user'        => [
            'username'  => $user['username'],
            'full_name' => $user['full_name'],
            'email'     => $user['email']
        ],
        'exported_at' => date('Y-m-d H:i:s'),
        'total_count' => count($results),
        'sessions'    => $results
    ];

    echo json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
} else {
    // Default to CSV
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $output = fopen('php://output', 'w');

    // Add UTF-8 BOM for Excel compatibility
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    // CSV Header Row
    fputcsv($output, [
        'Session ID',
        'Passage Title',
        'Difficulty',
        'Net WPM',
        'Gross WPM',
        'Accuracy (%)',
        'Words Typed',
        'Total Keystrokes',
        'Correct Keystrokes',
        'Error Keystrokes',
        'Backspaces',
        'Configured Duration (s)',
        'Elapsed Time (s)',
        'Test Mode',
        'Date & Time'
    ]);

    foreach ($results as $row) {
        fputcsv($output, [
            $row['session_id'],
            $row['passage_title'] ?: 'Custom Text Drill',
            $row['difficulty'],
            $row['net_wpm'],
            $row['gross_wpm'],
            $row['accuracy'],
            $row['words_typed'],
            $row['total_keystrokes'],
            $row['correct_keystrokes'],
            $row['error_keystrokes'],
            $row['backspace_count'],
            $row['duration_seconds'],
            $row['elapsed_seconds'],
            $row['test_mode'],
            $row['created_at']
        ]);
    }

    fclose($output);
    exit;
}
