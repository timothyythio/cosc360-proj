<?php
session_start();
require_once '../sql/db_connect.php';
include("header.php");

if (!isset($_GET['id'])) {
    echo "Post not found.";
    exit;
}

$postId = intval($_GET['id']);
$commentError = '';

$isSaved = false;
if (isset($_SESSION['user_id'])) {
    $checkSaved = $pdo->prepare("SELECT 1 FROM saved WHERE user_id = ? AND post_id = ?");
    $checkSaved->execute([$_SESSION['user_id'], $postId]);
    $isSaved = $checkSaved->fetchColumn() ? true : false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    if (!isset($_SESSION['user_id'])) {
        $commentError = "You must be logged in to comment.";
    } else {
        $content = trim($_POST['comment']);
        if (empty($content)) {
            $commentError = "Comment cannot be empty.";
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, content, created_at) VALUES (?, ?, ?, NOW())");
                $stmt->execute([$postId, $_SESSION['user_id'], $content]);
                header("Location: post.php?id=" . $postId);
                exit;
            } catch (PDOException $e) {
                $commentError = "Error submitting comment. Please try again.";
            }
        }
    }
}
$stmt = $pdo->prepare("
    SELECT p.*, u.username, u.pfp, u.user_id AS author_id
    FROM posts p
    LEFT JOIN users u ON p.username = u.username
    WHERE p.post_id = :id
");
$stmt->execute(['id' => $postId]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    echo "Post not found.";
    exit;
}

$commentStmt = $pdo->prepare("
    SELECT c.comment_id, c.content, c.created_at, u.username, u.pfp, u.user_id
    FROM comments c 
    LEFT JOIN users u ON c.user_id = u.user_id 
    WHERE c.post_id = :post_id 
    ORDER BY c.created_at ASC
");
$commentStmt->execute(['post_id' => $postId]);
$comments = $commentStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bloggit - <?= htmlspecialchars($post['title']) ?></title>
    <link rel="stylesheet" href="../styles/main.css">
    <link rel="stylesheet" href="../styles/post.css">
</head>
<body>
<div id="app">
    <div id="topnav"></div>
    <div id="navbar"></div>
    <div id="backbutton">
        <a href="feed.php"><img src="../assets/back-button.svg"></a>
        <p>Back</p>
    </div>

    <main id="content">
    <div id="post-container" data-post-id="<?= htmlspecialchars($postId) ?>">

        <div id="post-content">
            <h2><?= htmlspecialchars($post['title']) ?>
                <div id="userspfp-post">
                    <a href="profile.php?user_id=<?= htmlspecialchars($post['author_id'] ?? '') ?>">
                        <img src="<?= htmlspecialchars($post['pfp'] ?? '../assets/profile-icon.png') ?>" alt="Author Profile">
                    </a>
                </div>
            </h2>
            <?php if ($post['image_path']): ?>
                <img src="<?= htmlspecialchars($post['image_path']) ?>" alt="Post Image">
            <?php endif; ?>
            <div id="like-count">
                <button id="like-button" data-post-id="<?= $postId ?>" style="background:none; border:none;">
                    <img id="like-icon" src="../assets/heart-circle-svgrepo-com.svg" alt="Like Button">
                </button>
                <p id="like-count-number"><?= intval($post['likes']) ?> likes</p>
                <p>• Posted on <?= date('F j, Y \a\t g:i A', strtotime($post['created_at'])) ?></p>
            </div>
        </div>

        <div id="text-content">
            <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
        </div>

        <div class="button-cont">
            <button class="share" onclick="copyLink()">
                <img src="../assets/share.svg" alt="share">
            </button>
            <button class="save" id="save-btn">
                <img 
                    src="<?= $isSaved ? '../assets/bookmark-coloured.svg' : '../assets/bookmark.svg' ?>" 
                    alt="save" 
                    id="save-icon" 
                    width="24" height="24"
                >
            </button>
        </div>

        <hr class="comment-divider">

        <div id="comment-section">
            <?php if (count($comments) === 0): ?>
                <p style="text-align:center; color: #555;">No comments yet. Be the first to comment!</p>
            <?php else: ?>
                <?php foreach ($comments as $comment): ?>
                    <div id="comment-container">
                        <a href="profile.php?user_id=<?= htmlspecialchars($comment['user_id'] ?? '') ?>">
                            <img src="<?= htmlspecialchars($comment['pfp'] ?? '../assets/profile-icon.png') ?>" alt="Profile Picture">
                        </a>
                        <h3><?= htmlspecialchars($comment['username'] ?? 'Anonymous') ?></h3>
                        <div id="comment-content">
                            <p><?= nl2br(htmlspecialchars($comment['content'])) ?></p>
                            <small><?= date('F j, Y \a\t g:i A', strtotime($comment['created_at'])) ?></small>
                        </div>
                    </div>
                    <hr class="comment-divider">
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div id="comment-input-section">
            <?php if (isset($_SESSION['user_id'])): ?>
                <form id="comment-form" method="POST" action="">
                    <?php if (!empty($commentError)): ?>
                        <div class="comment-error" style="color: red; margin-bottom: 10px;">
                            <?= htmlspecialchars($commentError) ?>
                        </div>
                    <?php endif; ?>
                    <div class="comment-input-wrapper">
                        <img src="<?= htmlspecialchars($_SESSION['user_pfp'] ?? '../assets/profile-icon.png') ?>" alt="Your Profile">
                        <textarea 
                            name="comment" 
                            id="comment-textarea" 
                            placeholder="Write a comment..." 
                            required
                            maxlength="500"
                        ></textarea>
                        <button type="submit" id="submit-comment">Post</button>
                    </div>
                </form>
            <?php else: ?>
                <p style="text-align:center; color: #555;">
                    <a href="login.php">Log in</a> to leave a comment
                </p>
            <?php endif; ?>
        </div>
    </div>
    </main>
    <div id="footer"></div>
</div>

<script>
function copyLink() {
    const url = window.location.href;
    navigator.clipboard.writeText(url)
        .then(() => alert("Post link copied to clipboard!"))
        .catch(() => alert("Failed to copy the link."));
}

document.getElementById('comment-form')?.addEventListener('submit', function(e) {
    const commentTextarea = document.getElementById('comment-textarea');
    if (commentTextarea.value.trim() === '') {
        e.preventDefault();
        alert('Please enter a comment before submitting.');
    }
});
</script>
<!-- <script src="../scripts/router.js"></script>
<script src="../scripts/auth.js" defer></script> -->
<script src="../scripts/post-likes.js" defer></script>
<script src="../scripts/save-post.js" defer></script>

</body>
</html>