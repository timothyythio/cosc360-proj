<?php
session_start();
require_once '../sql/db_connect.php';

header('Content-Type: application/json');

try {
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    
    $query = "SELECT 
                t.topic_id AS id, 
                t.topic_name AS name, 
                t.description, 
                t.members AS follower_count,
                t.topic_img AS image_path,
                (SELECT COUNT(*) FROM Posts WHERE topic_id = t.topic_id) AS post_count,
                " . ($user_id ? "(SELECT COUNT(*) FROM Topic_Followers WHERE topic_id = t.topic_id AND user_id = ?) > 0" : "FALSE") . " AS is_followed
            FROM Topics t
            ORDER BY t.members DESC";
    
    $stmt = $pdo->prepare($query);
    
    if ($user_id) {
        $stmt->execute([$user_id]);
    } else {
        $stmt->execute();
    }
    
    $topics = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($topics);
    
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}