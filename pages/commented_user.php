<?php
session_start();
require 'db_connection.php';


if (!isset($_GET['user_id']) || !is_numeric($_GET['user_id'])) {
    die("Invalid user ID.");
}

$user_id = (int) $_GET['user_id'];
$stmt = $conn->prepare("SELECT username, bio, profile_picture FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("User not found.");
}

$user = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($user['username']); ?>'s Profile</title>
    <link rel="stylesheet" href="profile.css">
</head>
<body>
    <div class="profile-container">
        <div class="profile-image-container">
            <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Image" class="profile-image">
        </div>
        <div class="profile-username">@<?php echo htmlspecialchars($user['username']); ?></div>
        <div class="profile-bio">
            <?php echo nl2br(htmlspecialchars($user['bio'])); ?>
        </div>
    </div>
</body>
</html>
