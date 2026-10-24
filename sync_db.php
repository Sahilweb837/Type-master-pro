<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/passages_data.php';

$pdo = getDBConnection();
if (!$pdo) {
    echo "MySQL connection is offline. Fallback to includes/passages_data.php will be used seamlessly.\n";
    exit;
}

try {
    $pass = get_all_passages();
    $stmt = $pdo->prepare("
        INSERT INTO typing_passages 
        (id, title, content, difficulty, category, word_count, char_count, created_by, status) 
        VALUES (:id, :title, :content, :difficulty, :category, :word_count, :char_count, 1, 'active')
        ON DUPLICATE KEY UPDATE 
            title = VALUES(title),
            content = VALUES(content),
            difficulty = VALUES(difficulty),
            category = VALUES(category),
            word_count = VALUES(word_count),
            char_count = VALUES(char_count),
            status = 'active'
    ");
    
    $cnt = 0;
    foreach ($pass as $p) {
        $stmt->execute([
            ':id'         => $p['id'],
            ':title'      => $p['title'],
            ':content'    => $p['content'],
            ':difficulty' => $p['difficulty'],
            ':category'   => $p['category'],
            ':word_count' => $p['word_count'],
            ':char_count' => $p['char_count']
        ]);
        $cnt++;
    }
    echo "SUCCESS: Synced {$cnt} passages into MySQL typing_passages table.\n";
} catch (Exception $e) {
    echo "Error syncing: " . $e->getMessage() . "\n";
}
