<?php
session_start();
require_once '../sql/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $topicName = $_POST['topic_name'] ?? '';
    $description = $_POST['description'] ?? '';

    if (empty($topicName) || empty($description)) {
        $error = "Topic name and description are required.";
    } else {
        try {
            $imagePath = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = '../uploads/topics/';
                $filename = basename($_FILES['image']['name']);
                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                $safeName = uniqid('topic_', true) . '.' . $ext;
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
                $stmt = $pdo->prepare("INSERT INTO Topics (topic_name, description, topic_img, created_at) 
                                      VALUES (:topic_name, :description, :topic_img, NOW())");
                
                $stmt->execute([
                    ':topic_name' => $topicName,
                    ':description' => $description,
                    ':topic_img' => $imagePath ?? 'default-topic.png'
                ]);

                header("Location: topics.php");
                exit;
            }
        } catch (PDOException $e) {
            $error = "Database error: " . htmlspecialchars($e->getMessage());
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
    <title>Create New Topic - Bloggit</title>
    <link rel="stylesheet" href="../styles/main.css" />
    <link rel="stylesheet" href="../styles/newPost.css" />
    <link rel="stylesheet" href="../styles/new-topic.css" />
</head>
<body>
    <div id="app">
        <div id="topnav"></div>
        <div id="navbar"></div>
        <main id="content">
            <div id="create-container">
                <img src="<?= htmlspecialchars($_SESSION['user_pfp'] ?? '../assets/profile-icon.png') ?>" alt="Profile Image" class="profile-pic" />
                <h2>Create New Topic</h2>

                <?php if (isset($error)): ?>
                    <p style="color: red;"><?= $error ?></p>
                <?php endif; ?>

                <form action="new-topic.php" method="POST" id="topicForm" enctype="multipart/form-data">
                    <input type="text" id="topicName" name="topic_name" placeholder="Topic Name" required />

                    <h3>Add Topic Image</h3>
                    <div class="img-upload">
                        <label class="upload-container">
                            <img src="../assets/plus-icon.png" alt="Upload Icon" class="upload-icon" id="upload-icon">
                            <input type="file" name="image" accept="image/*" class="file-input" id="new-topic-image" required>
                            <img id="new-topic-preview" src="#" alt="Preview" class="image-preview" style="display: none;">
                        </label>
                    </div>

                    <textarea id="topicDescription" name="description" placeholder="Description of your topic..." required></textarea>

                    <div class="button-cont">
                        <button type="submit" name="action" value="post" class="post">Create Topic</button>
                    </div>
                </form>
            </div>
        </main>
        <div id="footer"></div>
    </div>

    <script src="../scripts/router.js"></script>
    <script src="../scripts/new-topic.js"></script>
    <script src="../scripts/auth.js" defer></script>
</body>
</html>