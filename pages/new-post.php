<?php
session_start();
require_once '../sql/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}
$draftTitle = '';
$draftContent = '';
$draftTopic = '';
$draftImagePath = '';
$draftId = null;

if (isset($_GET['draft_id'])) {
    $draftId = $_GET['draft_id'];
    $stmt = $pdo->prepare("SELECT d.*, t.topic_name FROM Drafts d 
                           LEFT JOIN Topics t ON d.topic_id = t.topic_id 
                           WHERE d.draft_id = ? AND d.user_id = ?");
    $stmt->execute([$draftId, $_SESSION['user_id']]);
    $draft = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($draft) {
        $draftTitle = htmlspecialchars($draft['title']);
        $draftContent = htmlspecialchars($draft['content']);
        $draftTopic = htmlspecialchars($draft['topic_name']);
        $draftImagePath = $draft['image_path'];
    }
        $draftTitle = htmlspecialchars($draft['title']);
        $draftContent = htmlspecialchars($draft['content']);
        $draftTopic = htmlspecialchars($draft['topic_name']);
    
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $caption = $_POST['caption'] ?? '';
    $category = $_POST['category'] ?? '';
    $username = $_SESSION['username'] ?? '';
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
} else if (!empty($_POST['existing_image_path'])) {
    $imagePath = $_POST['existing_image_path'];
}


            if (!isset($error)) {
                try {
                    if ($status === 'draft') {
                        $stmt = $pdo->prepare("INSERT INTO drafts (user_id, username, title, content, image_path, topic_id, created_at, updated_at)
                                               VALUES (:user_id, :username, :title, :content, :image_path, :topic_id, NOW(), NOW())");

                        $stmt->execute([
                            ':user_id' => $_SESSION['user_id'],
                            ':username' => $username,
                            ':title' => $title,
                            ':content' => $caption,
                            ':image_path' => $imagePath,
                            ':topic_id' => $topic_id
                        ]);

                        header("Location: draft.php");
                        exit;
                    } else {
                        if ($draftId) {
                            $deleteStmt = $pdo->prepare("DELETE FROM Drafts WHERE draft_id = ? AND user_id = ?");
                            $deleteStmt->execute([$draftId, $_SESSION['user_id']]);
                        }

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

                        header("Location: feed.php");
                        exit;
                    }
                } catch (PDOException $e) {
                    $error = "Database error: " . htmlspecialchars($e->getMessage());
                }
            }
        }
    }
}

include('header.php');
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

                <div class="drafts-nav">
                    <a href="draft.php" class="drafts-tab">My Drafts</a>
                </div>

                <?php if (isset($error)): ?>
                    <p style="color: red;"><?= $error ?></p>
                <?php endif; ?>

                <form action="new-post.php<?= $draftId ? '?draft_id=' . $draftId : '' ?>" method="POST" id="postForm" enctype="multipart/form-data">
                    <select name="category" required>
                        <option value="">Select a Topic</option>
                        <?php
                        $topics = $pdo->query("SELECT topic_name FROM Topics");
                        while ($row = $topics->fetch()) {
                            $topic = htmlspecialchars($row['topic_name']);
                            $selected = ($topic === $draftTopic) ? 'selected' : '';
                            echo "<option value=\"$topic\" $selected>$topic</option>";
                        }
                        ?>
                    </select>

                    <input type="text" id="postTitle" name="title" placeholder="Title" required value="<?= $draftTitle ?>" />

                    <h3>Add Photos...</h3>
                    <div class="img-upload">
                        <label class="upload-container">
                            <img src="../assets/plus-icon.png" alt="Upload Image" />
                            <input type="file" name="image" accept="image/*" class="file-input" />
                        </label>
                    </div>
<?php if ($draftImagePath): ?>
                    <div class="existing-image-preview">
                        <p>Draft Image Preview:</p>
                        <img src="<?= htmlspecialchars($draftImagePath) ?>" alt="Draft Image" style="max-width: 100%; height: auto;">
                        <input type="hidden" name="existing_image_path" value="<?= htmlspecialchars($draftImagePath) ?>">
                    </div>
                <?php endif; ?>

                    <textarea id="postCaption" name="caption" placeholder="Caption..." required><?= $draftContent ?></textarea>

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
            checkUserLogin();
        });
    </script>
</body>
</html>
