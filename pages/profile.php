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
$bio = "";
$pfp = "../assets/profile-icon.png"; 

try {
    $stmt = $pdo->prepare("SELECT bio, pfp FROM users WHERE user_id = :user_id");
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
                <div class="profile-image-container">
                    <img src="<?php echo $pfp; ?>" alt="Profile Picture" class="profile-image">
                </div>

                <h1 class="username"><?php echo htmlspecialchars($username); ?></h1>

                <div class="profile-buttons">
                    <a href="edit-profile.php" class="edit-profile-link">
                        <button class="btn edit-btn">Edit Profile</button>
                    </a>
                </div>

                <div class="profile-bio">
                    <p><?php echo nl2br(htmlspecialchars($bio)); ?></p>
                </div>

                <div class="profile-tabs">
                    <button class="tab-btn" onclick="showTab('liked-posts')">Liked Posts</button>
                    <button class="tab-btn" onclick="showTab('posts')">Posts</button>
                    <button class="tab-btn" onclick="showTab('comments')">Comments</button>
                    <button class="tab-btn" onclick="showTab('saved-posts')">Saved Posts</button>
                </div>

                <div id="posts" class="tab-content">
                    <h2>Your Posts</h2>
                    <!-- TODO: Loop user's posts here from DB -->
                    <p>Post fetching from DB coming soon!</p>
                </div>

                <div id="liked-posts" class="tab-content" style="display: none;">
                    <h2>Your Liked Posts</h2>
                    <p>Liked posts fetching from DB coming soon!</p>
                </div>

                <div id="saved-posts" class="tab-content" style="display: none;">
                    <h2>Your Saved Posts</h2>
                    <p>Saved posts fetching from DB coming soon!</p>
                </div>

                <div id="comments" class="tab-content" style="display: none;">
                    <h2>Your Comments</h2>
                    <p>Comments fetching from DB coming soon!</p>
                </div>
            </div>
        </main>

        <div id="footer"></div>
    </div>

    <script src="../scripts/router.js" defer></script>
    <script src="../scripts/auth.js" defer></script>
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