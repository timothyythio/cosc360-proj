<?php
session_start();
require_once '../sql/db_connect.php';

header('Content-Type: application/json');

$sort = isset($_GET['sort']) ? $_GET['sort'] : 'popular';

try {
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    
    $query = "SELECT 
                t.topic_id AS id, 
                t.topic_name AS name, 
                t.description, 
                t.members AS follower_count,
                t.topic_img AS image_path,
                (SELECT COUNT(*) FROM Posts WHERE topic_id = t.topic_id) AS post_count,
                " . ($user_id ? "(SELECT COUNT(*) FROM Topic_Followers WHERE topic_id = t.topic_id AND user_id = ?) > 0" : "FALSE") . " AS is_followed";
    
    // count posts in the last 24 hours for hot sort option
    if ($sort === 'hot') {
        $query .= ", (SELECT COUNT(*) FROM Posts WHERE topic_id = t.topic_id AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)) AS recent_posts";
    }
    
    $query .= " FROM Topics t";
    
    switch ($sort) {
        case 'hot':
            // most posts in last 24h
            $query .= " ORDER BY recent_posts DESC, t.members DESC";
            break;
        case 'new':
            $query .= " ORDER BY t.created_at DESC";
            break;
        case 'popular':
        default:
            $query .= " ORDER BY t.members DESC";
            break;
    }
    
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
