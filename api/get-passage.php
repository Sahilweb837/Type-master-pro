<?php
/**
 * Custom Type Master Pro
 * API: Fetch Typing Passage
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/app.php';

$pdo = getDBConnection();
if (!$pdo) {
    echo json_encode(['success' => false, 'message' => 'Database connection offline.']);
    exit;
}

$difficulty = trim($_GET['difficulty'] ?? 'intermediate');
$passageId = isset($_GET['id']) ? (int)$_GET['id'] : null;

try {
    if ($passageId) {
        $stmt = $pdo->prepare("SELECT * FROM typing_passages WHERE id = :id AND status = 'active' LIMIT 1");
        $stmt->execute([':id' => $passageId]);
        $passage = $stmt->fetch();
    } else {
        $stmt = $pdo->prepare("
            SELECT * FROM typing_passages 
            WHERE difficulty = :diff AND status = 'active' 
            ORDER BY RAND() LIMIT 1
        ");
        $stmt->execute([':diff' => $difficulty]);
        $passage = $stmt->fetch();

        // Fallback to any active passage if specific difficulty is empty
        if (!$passage) {
            $passage = $pdo->query("SELECT * FROM typing_passages WHERE status = 'active' ORDER BY RAND() LIMIT 1")->fetch();
        }
    }

    if ($passage) {
        echo json_encode([
            'success' => true,
            'passage' => [
                'id'         => (int)$passage['id'],
                'title'      => $passage['title'],
                'content'    => $passage['content'],
                'difficulty' => $passage['difficulty'],
                'category'   => $passage['category'],
                'word_count' => (int)$passage['word_count'],
                'char_count' => (int)$passage['char_count']
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No passages available.']);
    }
} catch (Exception $e) {
    error_log('API get passage error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to load passage.']);
}
