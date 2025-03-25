<?php
session_start();
require_once '../sql/db_connect.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$successMessage = $errorMessage = "";

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $bio = trim($_POST['bio']);
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    try {
        // Validate password match
        if (!empty($newPassword) && $newPassword !== $confirmPassword) {
            throw new Exception("Passwords do not match.");
        }

        // Build the SQL
        $query = "UPDATE users SET username = :username, email = :email, bio = :bio";
        $params = [
            ':username' => $username,
            ':email' => $email,
            ':bio' => $bio,
            ':user_id' => $user_id
        ];

        // Optional password update
        if (!empty($newPassword)) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $query .= ", password = :password";
            $params[':password'] = $hashedPassword;
        }

        $query .= " WHERE user_id = :user_id";

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);

        $_SESSION['username'] = $username;
        $successMessage = "Profile updated successfully!";
    } catch (Exception $e) {
        $errorMessage = $e->getMessage();
    }
}

// Fetch Current User Info
$stmt = $pdo->prepare("SELECT username, email, bio FROM users WHERE user_id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="../styles/main.css">
    <link rel="stylesheet" href="../styles/edit-profile.css">
</head>
<body>

<div id="navbar"></div>
<div id="content">
    <div id="topnav"></div>

    <div class="back-container">
        <a href="profile.php" class="back-link">
            <img src="../assets/back-button.svg" class="back-icon"> Back to Profile
        </a>
    </div>

    <div class="edit-profile-container">
        <h2>Edit Profile</h2>

        <?php if ($successMessage): ?>
            <p class="success-message"><?php echo $successMessage; ?></p>
        <?php elseif ($errorMessage): ?>
            <p class="error-message"><?php echo $errorMessage; ?></p>
        <?php endif; ?>

        <form method="POST" class="profile-form">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" required value="<?php echo htmlspecialchars($userData['username']); ?>">
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required value="<?php echo htmlspecialchars($userData['email']); ?>">
            </div>

            <div class="form-group">
                <label for="new_password">New Password (leave blank to keep current)</label>
                <input type="password" name="new_password" id="new_password">
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" name="confirm_password" id="confirm_password">
            </div>

            <div class="form-group">
                <label for="bio">Bio</label>
                <textarea name="bio" id="bio" placeholder="Tell us about yourself"><?php echo htmlspecialchars($userData['bio']); ?></textarea>
            </div>

            <div class="button-group">
                <a href="profile.php" class="btn cancel-btn">Cancel</a>
                <button type="submit" class="btn save-btn">Save Changes</button>
            </div>
        </form>
    </div>
</div>
<div id="footer"></div>

<script src="../scripts/router.js"></script>
<script src="../scripts/auth.js" defer></script>
<script>
    document.addEventListener("DOMContentLoaded", checkUserLogin);
</script>

</body>
</html>
