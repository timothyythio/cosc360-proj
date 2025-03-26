<?php
require_once '../sql/db_connect.php';
session_start();

//helps tell content JSON
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'])) {
    $postId = intval($_POST['post_id']);

    try {
        $stmt = $pdo->prepare("UPDATE posts SET likes = likes + 1 WHERE post_id = :post_id");
        $stmt->execute([':post_id' => $postId]);
        $getLikes = $pdo->prepare("SELECT likes FROM posts WHERE post_id = :post_id");
        $getLikes->execute([':post_id' => $postId]);
        $likes = $getLikes->fetchColumn();

        echo json_encode(['success' => true, 'likes' => $likes]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
