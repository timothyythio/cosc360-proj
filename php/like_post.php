<?php
session_start();
require_once '../sql/db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$post_id = $_POST['post_id'] ?? null;

if (!$post_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing post ID']);
    exit;
}

try {
    // Check if already liked
    $check = $pdo->prepare("SELECT * FROM Likes WHERE user_id = ? AND post_id = ?");
    $check->execute([$user_id, $post_id]);

    if ($check->fetch()) {
        // Unlike
        $delete = $pdo->prepare("DELETE FROM Likes WHERE user_id = ? AND post_id = ?");
        $delete->execute([$user_id, $post_id]);
        echo json_encode(['status' => 'unliked']);
    } else {
        // Like
        $insert = $pdo->prepare("INSERT INTO Likes (user_id, post_id) VALUES (?, ?)");
        $insert->execute([$user_id, $post_id]);
        echo json_encode(['status' => 'liked']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}