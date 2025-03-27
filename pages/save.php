<?php
session_start();
require_once '../sql/db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}

$userId = $_SESSION['user_id'];
$postId = $_POST['post_id'] ?? null;

if (!$postId) {
    echo json_encode(['success' => false, 'message' => 'Post ID is missing.']);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM saved WHERE user_id = ? AND post_id = ?");
$stmt->execute([$userId, $postId]);
$saved = $stmt->fetch();

//if alr saved remove
if ($saved) {
    $deleteStmt = $pdo->prepare("DELETE FROM saved WHERE user_id = ? AND post_id = ?");
    $deleteStmt->execute([$userId, $postId]);
    echo json_encode(['success' => true, 'saved' => false]);
} else {
//add
    $insertStmt = $pdo->prepare("INSERT INTO saved (user_id, post_id) VALUES (?, ?)");
    $insertStmt->execute([$userId, $postId]);
    echo json_encode(['success' => true, 'saved' => true]);
}
?>
