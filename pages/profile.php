<?php
session_start();
require_once '../sql/db_connect.php'; 

// Redirect if not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}


$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

$viewingOwnProfile = true;
if (isset($_GET['user'])) {
    $requestedId = $_GET['user'];
    if ($requestedId != $_SESSION['user_id']) {
        $viewingOwnProfile = false;
        $user_id = $requestedId;
    }
}
$bio = "";
$pfp = "../assets/profile-icon.png"; 

try {
    $stmt = $pdo->prepare("SELECT username, bio, pfp FROM Users WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($userData) {
        $bio = $userData['bio'] ?: "No bio yet.";
        if (!empty($userData['pfp'])) {
            $pfp = "../uploads/" . htmlspecialchars($userData['pfp']); // Assuming uploaded pics stored in /uploads/
        }
    }
} catch (PDOException $e) {
    $bio = "Failed to load bio.";
}

include('header.php');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($username); ?>'s Profile - Bloggit</title>
    <link rel="stylesheet" href="../styles/main.css">
    <link rel="stylesheet" href="../styles/profile.css">
</head>
<body>
    <div id="app">
        <div id="topnav"></div>
        <div id="navbar"></div>

        <main id="content">
            <div class="profile-container">
                <div class="profile-header">
                    <div class="profile-image-container">
                        <img src="<?= $userData['pfp'] ?>" class="profile-image" alt="Profile Picture">
                    </div>
                    <div class="profile-info">
                        <h2 class="profile-username"><?= htmlspecialchars($userData['username']) ?></h2>
                        <?php if ($viewingOwnProfile): ?>
                            <a href="edit-profile.php" class="btn">Edit Profile</a>
                        <?php endif; ?>
                    </div>
                    <div class="profile-bio">
                        <p><?php echo nl2br(htmlspecialchars($bio)); ?></p>
                    </div>
                </div>
                
                <div class="profile-tabs">
                    <button class="tab-btn" onclick="showTab('liked-posts')">Liked Posts</button>
                    <button class="tab-btn" onclick="showTab('posts')">Posts</button>
                    <button class="tab-btn" onclick="showTab('comments')">Comments</button>
                    <button class="tab-btn" onclick="showTab('saved-posts')">Saved Posts</button>
                </div>
                <div class="post-tabs">
                    <div id="posts" class="tab-content">
                        <h2>Your Posts</h2>
                        <?php
                        try {
                            $stmt = $pdo->prepare(" SELECT 
                                    posts.post_id, 
                                    posts.title, 
                                    posts.content, 
                                    posts.image_path, 
                                    posts.created_at, 
                                    posts.likes, 
                                    users.username,
                                    (SELECT COUNT(*) FROM Likes WHERE likes.post_id = posts.post_id) AS like_count
                                FROM Posts
                                JOIN Users ON posts.user_id = users.user_id
                                WHERE posts.user_id = :user_id
                                ORDER BY posts.created_at DESC
                            ");
                            $stmt->execute(['user_id' => $user_id]);
                            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            if ($posts) {
                                echo "<div class='post-grid'>";
                                foreach ($posts as $post) {
                                    echo "<div class='post-card'>";
                                    if (!empty($post['image_path'])) {
                                        echo "<img src='../uploads/" . htmlspecialchars($post['image_path']) . "' class='post-image-small' alt='Post image'>";
                                    }
                                    echo "<h3 class='post-title'>" . htmlspecialchars($post['title']) . "</h3>";
                                    echo "<p class='post-content'>" . nl2br(htmlspecialchars(substr($post['content'], 0, 150))) . "...</p>";
                                    echo "<div class='post-meta'>";
                                    echo "<span class='timestamp'>" . htmlspecialchars($post['created_at']) . "</span>";
                                    echo "</div>";
                                    echo "<div class='post-actions'>";
                                    echo "<span>❤️ " . htmlspecialchars($post['like_count']) . " likes</span>";
                                    echo "<a href='post.php?id=" . urlencode($post['post_id']) . "' class='view-btn'>View</a>";
                                    echo "</div>";
                                    echo "</div>";
                                }
                                echo "</div>";
                            } else {
                                echo "<p>No posts yet.</p>";
                            }
                        } catch (PDOException $e) {
                            echo "<p>Error: " . $e->getMessage() . "</p>";
                        }
                        ?>
                    </div>

                    <!-- Liked Posts Tab -->
                    <div id="liked-posts" class="tab-content" style="display: none;">
                        <h2>Your Liked Posts</h2>
                        <?php
                        try {
                            $stmt = $pdo->prepare("
                                SELECT p.title, p.created_at 
                                FROM Likes l
                                JOIN Posts p ON l.post_id = p.post_id
                                WHERE l.user_id = :user_id
                                ORDER BY l.liked_at DESC
                            ");
                            $stmt->execute(['user_id' => $user_id]);
                            $likedPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            if ($likedPosts) {
                                echo "<ul class='post-list'>";
                                foreach ($likedPosts as $post) {
                                    echo "<li><strong>" . htmlspecialchars($post['title']) . "</strong> <em>(" . $post['created_at'] . ")</em></li>";
                                }
                                echo "</ul>";
                            } else {
                                echo "<p>No liked posts yet.</p>";
                            }
                        } catch (PDOException $e) {
                            echo "<p>Error fetching liked posts.</p>";
                        }
                        ?>
                    </div>

                    <div id="saved-posts" class="tab-content" style="display: none;">
                        <h2>Your Saved Posts</h2>
                        <?php
                        try {
                            $stmt = $pdo->prepare("
                                SELECT p.title, p.created_at 
                                FROM Saved s
                                JOIN Posts p ON s.post_id = p.post_id
                                WHERE s.user_id = :user_id
                                ORDER BY s.saved_at DESC
                            ");
                            $stmt->execute(['user_id' => $user_id]);
                            $savedPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            if ($savedPosts) {
                                echo "<ul class='post-list'>";
                                foreach ($savedPosts as $post) {
                                    echo "<li><strong>" . htmlspecialchars($post['title']) . "</strong> <em>(" . $post['created_at'] . ")</em></li>";
                                }
                                echo "</ul>";
                            } else {
                                echo "<p>No saved posts yet.</p>";
                            }
                        } catch (PDOException $e) {
                            echo "<p>Error fetching saved posts.</p>";
                        }
                        ?>
                    </div>


                    <div id="comments" class="tab-content" style="display: none;">
                        <h2>Your Comments</h2>
                        <?php
                        try {
                            $stmt = $pdo->prepare("
                                SELECT c.content, c.created_at, p.title 
                                FROM Comments c
                                LEFT JOIN Posts p ON c.post_id = p.post_id
                                WHERE c.user_id = :user_id
                                ORDER BY c.created_at DESC
                            ");
                            $stmt->execute(['user_id' => $user_id]);
                            $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            if ($comments) {
                                echo "<ul class='comment-list'>";
                                foreach ($comments as $comment) {
                                    echo "<li><em>On \"" . htmlspecialchars($comment['title']) . "\"</em>: " . htmlspecialchars($comment['content']) . " <small>(" . $comment['created_at'] . ")</small></li>";
                                }
                                echo "</ul>";
                            } else {
                                echo "<p>No comments yet.</p>";
                            }
                        } catch (PDOException $e) {
                            echo "<p>Error fetching comments.</p>";
                        }
                        ?>
                    </div>
                </div>
            </div>
                
        </main>

        <div id="footer"></div>
    </div>


    <script>
        function showTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(content => content.style.display = 'none');
            document.getElementById(tabId).style.display = 'block';
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelector(`[onclick="showTab('${tabId}')"]`).classList.add('active');
            localStorage.setItem('activeTab', tabId);
        }

        window.onload = function() {
            const activeTab = localStorage.getItem('activeTab') || 'posts';
            showTab(activeTab);
        };
    </script>
</body>
</html>




                



