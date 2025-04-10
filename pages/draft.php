<?php
session_start();
require_once '../sql/db_connect.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    header('Content-Type: application/json');
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        exit;
    }
    
    if (!isset($_POST['draft_id']) || empty($_POST['draft_id'])) {
        echo json_encode(['success' => false, 'message' => 'No draft ID provided']);
        exit;
    }
    
    $draftId = $_POST['draft_id'];
    $userId = $_SESSION['user_id']; 
    
    try {
        $deleteStmt = $pdo->prepare("DELETE FROM Drafts WHERE draft_id = ? AND user_id = ?");
        $result = $deleteStmt->execute([$draftId, $userId]);
        
        if ($result && $deleteStmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Draft deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Draft not found or already deleted']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
    
    exit;
}

$userId = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? '';
$stmt = $pdo->prepare("SELECT d.*, t.topic_name FROM Drafts d 
                       LEFT JOIN Topics t ON d.topic_id = t.topic_id 
                       WHERE d.user_id = ? 
                       ORDER BY d.updated_at DESC");
$stmt->execute([$userId]);
$drafts = $stmt->fetchAll(PDO::FETCH_ASSOC);

include('header.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>My Drafts - Bloggit</title>
    <link rel="stylesheet" href="../styles/main.css" />
    <link rel="stylesheet" href="../styles/newPost.css" />
    <link rel="stylesheet" href="../styles/drafts.css" />
</head>
<body>
    <div id="app">
        <div id="topnav"></div>
        <div id="navbar"></div>
        <main id="content">
            <div id="create-container">
                <div class="page-header">
                    <a href="new-post.php" class="back-button">
                        <img src="../assets/back-button.svg" alt="Back" />
                        <p>Back</p>
                    </a>
                    <img src="../assets/profile-icon.png" alt="Profile Image" class="profile-pic" />
                    <h2>My Drafts</h2>
                </div>
                
                <div class="drafts-container">
                    <?php if (empty($drafts)): ?>
                        <p class="no-drafts">Currently no drafts</p>
                    <?php else: ?>
                        <?php foreach ($drafts as $draft): ?>
                            <div class="draft-card" data-draft-id="<?= htmlspecialchars($draft['draft_id']) ?>"
                                 data-title="<?= htmlspecialchars($draft['title']) ?>"
                                 data-content="<?= htmlspecialchars($draft['content']) ?>"
                                 data-topic="<?= htmlspecialchars($draft['topic_name']) ?>">
                                <div class="draft-header">
                                    <h3><?= htmlspecialchars($draft['title']) ?></h3>
                                    <span class="draft-date"><?= date('M d, Y', strtotime($draft['updated_at'])) ?></span>
                                </div>
                                <p class="draft-preview"><?= htmlspecialchars(substr($draft['content'], 0, 100)) ?>...</p>
                                <div class="draft-footer">
                                    <span class="draft-topic"><?= htmlspecialchars($draft['topic_name']) ?></span>
                                    <button class="delete-draft" data-draft-id="<?= htmlspecialchars($draft['draft_id']) ?>">Delete</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </main>
        <div id="footer"></div>
    </div>

    <script src="../scripts/drafts.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            checkUserLogin(); // redirect guests
        });
    </script>
</body>
</html>