<?php
/**
 * Custom Type Master Pro
 * Admin API & Real-time Controller
 * Namespace: /nickadmin005#
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

// Strict Admin Gatekeeper
if (!is_admin()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Forbidden: Admin authorization required.']);
    exit;
}

$pdo = getDBConnection();
$action = $_GET['action'] ?? $_POST['action'] ?? '';

if (!$pdo) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database connection unavailable.']);
    exit;
}

try {
    switch ($action) {
        case 'stats':
            // Optional delay simulation for skeleton loading demo
            $delay = (int)($_GET['simulate_delay'] ?? 0);
            if ($delay > 0 && $delay <= 3000) {
                usleep($delay * 1000);
            }

            $totalUsers = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
            $totalTests = (int)$pdo->query("SELECT COUNT(*) FROM typing_results")->fetchColumn();
            $totalPassages = (int)$pdo->query("SELECT COUNT(*) FROM typing_passages")->fetchColumn();
            $systemAvgWpm = round((float)$pdo->query("SELECT COALESCE(AVG(net_wpm), 0) FROM typing_results")->fetchColumn(), 1);
            $systemAvgAcc = round((float)$pdo->query("SELECT COALESCE(AVG(accuracy), 0) FROM typing_results")->fetchColumn(), 1);

            // Tests in last 24h
            $testsToday = (int)$pdo->query("SELECT COUNT(*) FROM typing_results WHERE created_at >= NOW() - INTERVAL 1 DAY")->fetchColumn();

            // Speed tiers
            $tierBeginner = (int)$pdo->query("SELECT COUNT(*) FROM typing_results WHERE net_wpm < 40")->fetchColumn();
            $tierInter    = (int)$pdo->query("SELECT COUNT(*) FROM typing_results WHERE net_wpm >= 40 AND net_wpm < 70")->fetchColumn();
            $tierPro      = (int)$pdo->query("SELECT COUNT(*) FROM typing_results WHERE net_wpm >= 70")->fetchColumn();

            echo json_encode([
                'success' => true,
                'data' => [
                    'totalUsers'    => $totalUsers,
                    'totalTests'    => $totalTests,
                    'totalPassages' => $totalPassages,
                    'systemAvgWpm'  => $systemAvgWpm,
                    'systemAvgAcc'  => $systemAvgAcc,
                    'testsToday'    => $testsToday,
                    'tiers'         => [
                        'beginner'     => $tierBeginner,
                        'intermediate' => $tierInter,
                        'pro'          => $tierPro
                    ]
                ]
            ]);
            exit;

        case 'change_role':
            if (!verify_csrf_token()) {
                echo json_encode(['success' => false, 'error' => 'Invalid CSRF security token.']);
                exit;
            }
            $targetUserId = (int)($_POST['target_user_id'] ?? 0);
            $newRole = ($_POST['role'] ?? '') === 'admin' ? 'admin' : 'user';

            if ($targetUserId === (int)$_SESSION['user_id']) {
                echo json_encode(['success' => false, 'error' => 'Cannot modify your own administrator role.']);
                exit;
            }

            $stmt = $pdo->prepare("UPDATE users SET role = :r WHERE id = :id");
            $stmt->execute([':r' => $newRole, ':id' => $targetUserId]);
            echo json_encode(['success' => true, 'new_role' => $newRole, 'message' => "User role updated to {$newRole}."]);
            exit;

        case 'delete_user':
            if (!verify_csrf_token()) {
                echo json_encode(['success' => false, 'error' => 'Invalid CSRF security token.']);
                exit;
            }
            $targetUserId = (int)($_POST['target_user_id'] ?? 0);
            if ($targetUserId === (int)$_SESSION['user_id']) {
                echo json_encode(['success' => false, 'error' => 'Cannot delete your own active administrator account.']);
                exit;
            }

            $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
            $stmt->execute([':id' => $targetUserId]);
            echo json_encode(['success' => true, 'message' => 'User and associated records removed successfully.']);
            exit;

        case 'toggle_passage':
            if (!verify_csrf_token()) {
                echo json_encode(['success' => false, 'error' => 'Invalid CSRF security token.']);
                exit;
            }
            $passageId = (int)($_POST['passage_id'] ?? 0);
            $stmt = $pdo->prepare("SELECT status FROM typing_passages WHERE id = :id");
            $stmt->execute([':id' => $passageId]);
            $current = $stmt->fetchColumn();

            $newStatus = ($current === 'active') ? 'inactive' : 'active';
            $upd = $pdo->prepare("UPDATE typing_passages SET status = :s WHERE id = :id");
            $upd->execute([':s' => $newStatus, ':id' => $passageId]);

            echo json_encode(['success' => true, 'new_status' => $newStatus, 'message' => "Passage status is now {$newStatus}."]);
            exit;

        case 'create_passage':
            if (!verify_csrf_token()) {
                echo json_encode(['success' => false, 'error' => 'Invalid CSRF security token.']);
                exit;
            }
            $title = trim($_POST['title'] ?? '');
            $content = trim($_POST['content'] ?? '');
            $difficulty = trim($_POST['difficulty'] ?? 'intermediate');
            $category = trim($_POST['category'] ?? 'General');

            if (empty($title) || empty($content)) {
                echo json_encode(['success' => false, 'error' => 'Please provide both title and content.']);
                exit;
            }

            $words = preg_split('/\s+/', $content, -1, PREG_SPLIT_NO_EMPTY);
            $wordCount = count($words);
            $charCount = mb_strlen($content);

            $stmt = $pdo->prepare("
                INSERT INTO typing_passages 
                (title, content, difficulty, category, word_count, char_count, created_by, status) 
                VALUES 
                (:t, :c, :d, :cat, :wc, :cc, :cb, 'active')
            ");
            $stmt->execute([
                ':t'   => $title,
                ':c'   => $content,
                ':d'   => $difficulty,
                ':cat' => $category,
                ':wc'  => $wordCount,
                ':cc'  => $charCount,
                ':cb'  => (int)$_SESSION['user_id']
            ]);

            echo json_encode([
                'success' => true,
                'id' => $pdo->lastInsertId(),
                'word_count' => $wordCount,
                'message' => "Passage '{$title}' created successfully ({$wordCount} words)."
            ]);
            exit;

        case 'optimize_db':
            $pdo->query("OPTIMIZE TABLE users, typing_passages, typing_results, user_settings");
            echo json_encode(['success' => true, 'message' => 'All database tables optimized and indices recalculated.']);
            exit;

        default:
            echo json_encode(['success' => false, 'error' => 'Invalid action requested.']);
            exit;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
}
