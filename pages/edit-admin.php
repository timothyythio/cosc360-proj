<?php
require_once '../sql/db_connect.php';

// Hardcoded admin user_id for demo purposes — replace with session logic in production
$adminUserId = 1;

// Fetch user and admin details
$stmt = $pdo->prepare("SELECT u.username, u.first_name, u.last_name, u.email, u.password, a.admin_id, a.country, a.city
                        FROM users u
                        JOIN admin a ON u.user_id = a.user_id
                        WHERE u.user_id = ?");
$stmt->execute([$adminUserId]);
$adminData = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['admin-name'] ?? '';
    $email = $_POST['admin-email'] ?? '';
    $country = $_POST['admin-country'] ?? '';
    $city = $_POST['admin-city'] ?? '';
    $password = $_POST['admin-password'] ?? '';
    $confirmPassword = $_POST['confirm-password'] ?? '';

    if ($password !== $confirmPassword) {
        $error = "Passwords do not match.";
    } else {
        $hashedPassword = !empty($password) ? md5($password) : $adminData['password'];

        // Update users table
        $updateUser = $pdo->prepare("UPDATE users SET first_name = ?, email = ?, password = ? WHERE user_id = ?");
        $updateUser->execute([$firstName, $email, $hashedPassword, $adminUserId]);

        // Update admin table
        $updateAdmin = $pdo->prepare("UPDATE admin SET country = ?, city = ? WHERE user_id = ?");
        $updateAdmin->execute([$country, $city, $adminUserId]);

        $success = "Profile updated successfully.";

        // Refresh data
        $stmt->execute([$adminUserId]);
        $adminData = $stmt->fetch();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Admin Profile</title>
  <link rel="stylesheet" href="../styles/main.css">
  <link rel="stylesheet" href="../styles/edit-admin.css">
</head>
<body>
  <div id="navbar"></div>
  <div id="content">
    <div id="topnav"></div>

    <div class="back-container">
      <a href="admin.php" class="back-link">
        <img src="../assets/back-button.svg" class="back-icon"> Back to Admin Dashboard
      </a>
    </div>

    <div id="editInfo-cont">
      <h2>Edit Admin Profile</h2>

      <?php if (isset($error)): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
      <?php elseif (isset($success)): ?>
        <p style="color: green;"><?= htmlspecialchars($success) ?></p>
      <?php endif; ?>

      <form method="POST" id="editAdminForm" class="adminForm">
        <div class="form-group">
          <label for="admin-id">Admin ID</label>
          <input type="text" id="admin-id" value="#<?= htmlspecialchars($adminData['admin_id']) ?>" readonly>
        </div>

        <div class="form-group">
          <label for="admin-name">Name</label>
          <input type="text" name="admin-name" id="admin-name" value="<?= htmlspecialchars($adminData['first_name']) ?>" placeholder="Enter admin name">
        </div>

        <div class="form-group">
          <label for="admin-email">Email</label>
          <input type="email" name="admin-email" id="admin-email" value="<?= htmlspecialchars($adminData['email']) ?>" placeholder="Enter admin email">
        </div>

        <div class="form-group">
          <label for="admin-country">Country</label>
          <input type="text" name="admin-country" id="admin-country" value="<?= htmlspecialchars($adminData['country']) ?>" placeholder="Enter country">
        </div>

        <div class="form-group">
          <label for="admin-city">City</label>
          <input type="text" name="admin-city" id="admin-city" value="<?= htmlspecialchars($adminData['city']) ?>" placeholder="Enter city">
        </div>

        <div class="form-group">
          <label for="admin-password">New Password<span class="required"> *</span></label>
          <input type="password" name="admin-password" id="admin-password" placeholder="Enter new password">
        </div>

        <div class="form-group">
          <label for="confirm-password">Confirm Password<span class="required"> *</span></label>
          <input type="password" name="confirm-password" id="confirm-password" placeholder="Confirm new password">
        </div>

        <div class="button-group">
          <button type="button" class="btn cancel-btn" onclick="window.location.href='admin.php'">Cancel</button>
          <button type="submit" class="btn save-btn">Save Changes</button>
        </div>
      </form>
    </div>
  </div>

  <div id="footer"></div>
  <script src="../scripts/router.js"></script>
</body>
</html>
