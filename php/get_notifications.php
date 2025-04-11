<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once '../sql/db_connect.php'; 

$userId = $_SESSION['user_id']; // logged-in user

$sql = "
    SELECT l.user_id AS sender_id, l.post_id, l.liked_at AS created_at, 'like' AS type
    FROM Likes l
    JOIN Posts p ON l.post_id = p.post_id
    WHERE p.user_id = ?

    UNION

    SELECT c.user_id AS sender_id, c.post_id, c.created_at, 'comment' AS type
    FROM Comments c
    JOIN Posts p ON c.post_id = p.post_id
    WHERE p.user_id = ?

    UNION

    SELECT s.user_id AS sender_id, s.post_id, s.saved_at AS created_at, 'save' AS type
    FROM Saved s
    JOIN Posts p ON s.post_id = p.post_id
    WHERE p.user_id = ?

    LIMIT 20;
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$userId, $userId, $userId]); 
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$notifications = [];

foreach ($result as $row) {
    // get sender username
    $senderStmt = $pdo->prepare("SELECT username FROM Users WHERE user_id = ?");
    $senderStmt->execute([$row['sender_id']]);
    $sender = $senderStmt->fetch(PDO::FETCH_ASSOC);

    $username = htmlspecialchars($sender['username']);
    $type = $row['type'];
    $postId = $row['post_id'];
    $time = $row['created_at'];

    // custom message
    switch ($type) {
        case 'like':
            $msg = "$username liked your post";
            break;
        case 'comment':
            $msg = "$username commented on your post";
            break;
        case 'save':
            $msg = "$username saved your post";
            break;
        default:
            $msg = "$username interacted with your post";
    }

    $notifications[] = [
        'message' => $msg,
        'post_id' => $postId,
        'created_at' => $time
    ];
}

header('Content-Type: application/json');
echo json_encode($notifications);
