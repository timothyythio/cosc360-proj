<?php
session_start();
require_once '../sql/db_connect.php';

$topic_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'popular';

if (!$topic_id) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Missing topic ID']);
    exit;
}

try {
    $query = "
        SELECT p.*, u.username, COUNT(l.like_id) as like_count
        FROM Posts p
        LEFT JOIN Users u ON p.user_id = u.user_id
        LEFT JOIN Likes l ON p.post_id = l.post_id
        WHERE p.topic_id = ? AND p.status = 'posted'
        GROUP BY p.post_id
    ";
    
    switch ($sort) {
        case 'hot':
            // posts with most likes in the last 24h
            $query .= " HAVING p.created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR) ORDER BY like_count DESC";
            break;
        case 'new':
            $query .= " ORDER BY p.created_at DESC";
            break;
        case 'popular':
        default:
            $query .= " ORDER BY like_count DESC";
            break;
    }
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$topic_id]);
    $posts = $stmt->fetchAll();

    $response = [];
    foreach ($posts as $post) {
        $response[] = [
            'post_id' => $post['post_id'],
            'title' => $post['title'],
            'content' => $post['content'],
            'created_at' => $post['created_at'],
            'username' => $post['username'],
            'image_path' => $post['image_path'],
            'likes' => $post['like_count']
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}