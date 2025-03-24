<?php
require_once '../sql/db_connect.php';
if (!isset($_GET['id'])) {
    echo "Post not found.";
    exit;
}

$postId = intval($_GET['id']);
$stmt = $pdo->prepare("SELECT * FROM posts WHERE post_id = :id");
$stmt->execute(['id' => $postId]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    echo "Post not found.";
    exit;
}
$commentStmt = $pdo->prepare("SELECT c.content, c.created_at, u.username FROM comments c LEFT JOIN users u ON c.user_id = u.user_id WHERE c.post_id = :post_id ORDER BY c.created_at ASC");
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
        <div id="post-content">
            <h2><?= htmlspecialchars($post['title']) ?>
                <div id="userspfp-post">
                    <img src="../assets/profile-icon.png" alt="User Profile">
                </div>
            </h2>
            <?php if ($post['image_path']): ?>
                <img src="<?= htmlspecialchars($post['image_path']) ?>" alt="Post Image">
            <?php endif; ?>
            <div id="like-count">
                <img src="../assets/heart-circle-svgrepo-com.svg" alt="Likes">
                <p><?= intval($post['likes']) ?> likes</p>
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
            <button class="save">
                <img src="../assets/bookmark.svg" alt="save">
            </button>
        </div>

        <hr class="comment-divider">

        <div id="comment-section">
            <?php if (count($comments) === 0): ?>
                <p style="text-align:center; color: #555;">No comments yet. Be the first to comment!</p>
            <?php else: ?>
                <?php foreach ($comments as $comment): ?>
                    <div id="comment-container">
                        <img src="../assets/profile-icon.png">
                        <h3><?= htmlspecialchars($comment['username'] ?? 'Anonymous') ?></h3>
                        <div id="comment-content">
                            <p><?= nl2br(htmlspecialchars($comment['content'])) ?></p>
                        </div>
                    </div>
                    <hr class="comment-divider">
                <?php endforeach; ?>
            <?php endif; ?>
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
</script>
<script src="../scripts/router.js"></script>
<script src="../scripts/auth.js" defer></script>
</body>
</html>
