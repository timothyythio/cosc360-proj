<?php
session_start();
require_once '../sql/db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}

$userId = $_SESSION['user_id'];
$postId = $_REQUEST['post_id'] ?? null;

if (!$postId) {
    echo json_encode(['success' => false, 'message' => 'Post ID missing.']);
    exit;
}
if (!isset($_SESSION['liked_posts'])) {
    $_SESSION['liked_posts'] = [];
}
$alreadyLiked = in_array($postId, $_SESSION['liked_posts'], true);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $countStmt = $pdo->prepare("SELECT likes FROM posts WHERE post_id = ?");
    $countStmt->execute([$postId]);
    $count = $countStmt->fetchColumn() ?? 0;

    echo json_encode([
        'success' => true,
        'liked' => $alreadyLiked,
        'likes' => intval($count)
    ]);
    exit;
}

try {
    if ($alreadyLiked) {
        $stmt = $pdo->prepare("UPDATE posts SET likes = likes - 1 WHERE post_id = ?");
        $stmt->execute([$postId]);
        $_SESSION['liked_posts'] = array_diff($_SESSION['liked_posts'], [$postId]);

        $newLiked = false;
    } else {
        $stmt = $pdo->prepare("UPDATE posts SET likes = likes + 1 WHERE post_id = ?");
        $stmt->execute([$postId]);
        $_SESSION['liked_posts'][] = $postId;
        $newLiked = true;
    }
    $count = $pdo->prepare("SELECT likes FROM posts WHERE post_id = ?");
    $count->execute([$postId]);
    $likeCount = $count->fetchColumn();

    echo json_encode([
        'success' => true,
        'liked' => $newLiked,
        'likes' => intval($likeCount)
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
