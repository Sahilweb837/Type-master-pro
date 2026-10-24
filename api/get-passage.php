<?php
/**
 * TypoPrac — API: Fetch & Shuffle 195 Typing Passages
 * File: api/get-passage.php
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/passages_data.php';

$difficulty = trim($_GET['difficulty'] ?? 'all');
$category = trim($_GET['category'] ?? 'all');
$passageId = isset($_GET['id']) ? (int)$_GET['id'] : null;
$shuffle = isset($_GET['shuffle']) || isset($_GET['random']);

$passage = null;
$pdo = getDBConnection();

if ($pdo) {
    try {
        if ($passageId && !$shuffle) {
            $stmt = $pdo->prepare("SELECT * FROM typing_passages WHERE id = :id AND status = 'active' LIMIT 1");
            $stmt->execute([':id' => $passageId]);
            $passage = $stmt->fetch();
        } else {
            $sql = "SELECT * FROM typing_passages WHERE status = 'active'";
            $params = [];
            if ($difficulty && $difficulty !== 'all') {
                $sql .= " AND difficulty = :diff";
                $params[':diff'] = $difficulty;
            }
            if ($category && $category !== 'all') {
                $sql .= " AND category = :cat";
                $params[':cat'] = $category;
            }
            $sql .= " ORDER BY RAND() LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $passage = $stmt->fetch();

            if (!$passage) {
                $passage = $pdo->query("SELECT * FROM typing_passages WHERE status = 'active' ORDER BY RAND() LIMIT 1")->fetch();
            }
        }
    } catch (Exception $e) {
        error_log('API get passage DB error: ' . $e->getMessage());
    }
}

// Resilient Fallback to offline 195 passages library
if (!$passage) {
    if ($passageId && !$shuffle) {
        $passage = get_passage_by_id($passageId);
    }
    if (!$passage) {
        $passage = get_random_passage($difficulty !== 'all' ? $difficulty : null, $category !== 'all' ? $category : null);
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
    echo json_encode([
        'success' => false,
        'message' => 'No passages available.'
    ]);
}
