<?php
require_once '../sql/db_connect.php';

header('Content-Type: application/json');

try {
    $query = "SELECT 
                t.topic_id AS id, 
                t.topic_name AS name, 
                t.description, 
                t.members AS follower_count,
                t.topic_img AS image_path,
                (SELECT COUNT(*) FROM Posts WHERE topic_id = t.topic_id) AS post_count 
            FROM Topics t
            ORDER BY t.members DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    
    $topics = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($topics);
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>