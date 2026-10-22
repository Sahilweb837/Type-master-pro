<?php
/**
 * Custom Type Master Pro
 * API: Save Settings Asynchronously
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
    exit;
}

$user = current_user();
$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid data.']);
    exit;
}

$token = $data['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
if (!verify_csrf_token($token)) {
    echo json_encode(['success' => false, 'message' => 'CSRF verification failed.']);
    exit;
}

$pdo = getDBConnection();
if (!$pdo) {
    echo json_encode(['success' => false, 'message' => 'Database offline.']);
    exit;
}

try {
    // If partial theme update
    if (isset($data['theme'])) {
        $allowedThemes = ['orange', 'dark', 'light', 'cyberpunk', 'nord', 'white-orange'];
        if (in_array($data['theme'], $allowedThemes)) {
            $stmt = $pdo->prepare("UPDATE user_settings SET theme = :theme WHERE user_id = :uid");
            $stmt->execute([':theme' => $data['theme'], ':uid' => $user['id']]);
            echo json_encode(['success' => true]);
            exit;
        }
    }

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    error_log('API save settings error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to save settings.']);
}
