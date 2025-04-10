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
$topic_id = $_POST['topic_id'] ?? null;

if (!$topic_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing topic ID']);
    exit;
}

try {
    $pdo->beginTransaction();
    
    // check if user already follows this topic
    $check = $pdo->prepare("SELECT * FROM Topic_Followers WHERE user_id = ? AND topic_id = ?");
    $check->execute([$user_id, $topic_id]);
    
    if ($check->fetch()) {
        // unfollow
        $delete = $pdo->prepare("DELETE FROM Topic_Followers WHERE user_id = ? AND topic_id = ?");
        $delete->execute([$user_id, $topic_id]);
        
        // update follower count
        $updateCount = $pdo->prepare("UPDATE Topics SET members = (SELECT COUNT(*) FROM Topic_Followers WHERE topic_id = ?), members_count_updated = CURRENT_TIMESTAMP WHERE topic_id = ?");
        $updateCount->execute([$topic_id, $topic_id]);
        
        // get updated count
        $getCount = $pdo->prepare("SELECT members FROM Topics WHERE topic_id = ?");
        $getCount->execute([$topic_id]);
        $followers = $getCount->fetchColumn();
        
        $pdo->commit();
        
        echo json_encode([
            'status' => 'unfollowed',
            'followers' => $followers
        ]);
    } else {
        // follow topic
        $insert = $pdo->prepare("INSERT INTO Topic_Followers (user_id, topic_id) VALUES (?, ?)");
        $insert->execute([$user_id, $topic_id]);
        
        $updateCount = $pdo->prepare("UPDATE Topics SET members = (SELECT COUNT(*) FROM Topic_Followers WHERE topic_id = ?), members_count_updated = CURRENT_TIMESTAMP WHERE topic_id = ?");
        $updateCount->execute([$topic_id, $topic_id]);
        
        $getCount = $pdo->prepare("SELECT members FROM Topics WHERE topic_id = ?");
        $getCount->execute([$topic_id]);
        $followers = $getCount->fetchColumn();
        
        $pdo->commit();
        
        echo json_encode([
            'status' => 'followed',
            'followers' => $followers
        ]);
    }
} catch (PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}