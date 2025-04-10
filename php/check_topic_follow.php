<?php
session_start();
require_once '../sql/db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['follows' => false]);
    exit;
}

$user_id = $_SESSION['user_id'];
$topic_id = $_GET['topic_id'] ?? null;

if (!$topic_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing topic ID']);
    exit;
}

try {
    // check if user follows this topic
    $check = $pdo->prepare("SELECT 1 FROM Topic_Followers WHERE user_id = ? AND topic_id = ?");
    $check->execute([$user_id, $topic_id]);
    
    $follows = $check->fetchColumn() ? true : false;
    
    echo json_encode(['follows' => $follows]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}