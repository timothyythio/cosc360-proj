<?php
session_start();
require_once '../sql/db_connect.php';

$username = $password = "";
$usernameError = $passwordError = $loginError = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (empty($_POST["username"])) {
        $usernameError = "Username is required!";
    } else {
        $username = htmlspecialchars(trim($_POST["username"]));
    }

    if (empty($_POST["password"])) {
        $passwordError = "Password is required!";
    } else {
        $password = $_POST["password"];
    }

    if (empty($usernameError) && empty($passwordError)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->execute([':username' => $username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Login successful, set session
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['logged_in'] = true;

                header("Location: feed.php");  
                exit;
            } else {
                $loginError = "Invalid username or password.";
            }
        } catch (PDOException $e) {
            $loginError = "Login failed: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="../styles/main.css">
    <link rel="stylesheet" href="../styles/login.css">
</head>
<body>
<div id="navbar"></div>
<div id="topnav"></div>

<div id="content" class="center-content">
    <div class="login-container">
        <h1>Login</h1>

        <?php if (!empty($loginError)): ?>
            <p class="error-message"><?php echo $loginError; ?></p>
        <?php endif; ?>

        <form class="login-form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" novalidate>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" value="<?php echo htmlspecialchars($username); ?>" required>
                <span class="error-message"><?php echo $usernameError; ?></span>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
                <span class="error-message"><?php echo $passwordError; ?></span>
            </div>

            <button type="submit" class="btn submit-btn">Login</button>
        </form>

        <p class="register-link">Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</div>

<div id="footer">© 2025 COSC360 Blogging Platform</div>
<script src="../scripts/router.js" defer></script>
<script src="../scripts/auth.js" defer></script>
</body>
</html>
