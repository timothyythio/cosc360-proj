<?php
require_once '../sql/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $caption = $_POST['caption'] ?? '';
    $category = $_POST['category'] ?? ''; // topic name
    $username = $_POST['username'] ?? '';
    $status = ($_POST['action'] === 'draft') ? 'draft' : 'posted';

    if (empty($title) || empty($caption) || empty($category) || empty($username)) {
        $error = "All fields are required.";
    } else {
        $topicQuery = $pdo->prepare("SELECT topic_id FROM Topics WHERE topic_name = :name");
        $topicQuery->execute([':name' => $category]);
        $topicRow = $topicQuery->fetch();

        if (!$topicRow) {
            $error = "Selected topic not found.";
        } else {
            $topic_id = $topicRow['topic_id'];
            $imagePath = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = '../uploads/';
                $filename = basename($_FILES['image']['name']);
                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                $safeName = uniqid('img_', true) . '.' . $ext;
                $fullPath = $uploadDir . $safeName;

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                if (move_uploaded_file($_FILES['image']['tmp_name'], $fullPath)) {
                    $imagePath = $fullPath;
                } else {
                    $error = "Image upload failed.";
                }
            }

            if (!isset($error)) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO posts (username, title, content, topic_id, status, image_path, created_at)
                                           VALUES (:username, :title, :caption, :topic_id, :status, :image_path, NOW())");

                    $stmt->execute([
                        ':username' => $username,
                        ':title' => $title,
                        ':caption' => $caption,
                        ':topic_id' => $topic_id,
                        ':status' => $status,
                        ':image_path' => $imagePath
                    ]);

                    $success = "Your post was " . ($status === 'draft' ? "saved as a draft" : "published") . " successfully!";
                } catch (PDOException $e) {
                    $error = "Database error: " . htmlspecialchars($e->getMessage());
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Bloggit</title>
    <link rel="stylesheet" href="../styles/main.css" />
    <link rel="stylesheet" href="../styles/newPost.css" />
</head>
<body>
    <div id="app">
        <div id="topnav"></div>
        <div id="navbar"></div>
        <main id="content">
            <div id="create-container">
                <img src="../assets/profile-icon.png" alt="Profile Image" class="profile-pic" />
                <h2>Create Post</h2>

                <?php if (isset($error)): ?>
                    <p style="color: red;"><?= $error ?></p>
                <?php elseif (isset($success)): ?>
                    <p style="color: green;"><?= $success ?></p>
                    <a href="../pages/feed.html">Go to Feed</a>
                <?php endif; ?>

                <form action="new-post.php" method="POST" id="postForm" enctype="multipart/form-data">
                    <select name="category" required>
                        <option value="">Select a Topic</option>
                        <?php
                        $topics = $pdo->query("SELECT topic_name FROM Topics");
                        while ($row = $topics->fetch()) {
                            $topic = htmlspecialchars($row['topic_name']);
                            echo "<option value=\"$topic\">$topic</option>";
                        }
                        ?>
                    </select>

                    <input type="text" id="postTitle" name="title" placeholder="Title" required />

                    <h3>Add Photos...</h3>
                    <div class="img-upload">
                        <label class="upload-container">
                            <img src="../assets/plus-icon.png" alt="Upload Image" />
                            <input type="file" name="image" accept="image/*" class="file-input" />
                        </label>
                    </div>

                    <textarea id="postCaption" name="caption" placeholder="Caption..." required></textarea>
                    <input type="hidden" name="username" id="loggedInUser" />

                    <div class="button-cont">
                        <button type="submit" name="action" value="draft" class="save">Save Draft</button>
                        <button type="submit" name="action" value="post" class="post">Post</button>
                    </div>
                </form>
            </div>
        </main>
        <div id="footer"></div>
    </div>

    <script src="../scripts/router.js"></script>
    <script src="../scripts/new-post.js"></script>
    <script src="../scripts/auth.js" defer></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            checkUserLogin(); // redirect guests
            const loggedInUser = localStorage.getItem("loggedInUser");
            document.getElementById("loggedInUser").value = loggedInUser;
        });
    </script>
</body>
</html>
