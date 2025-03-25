<?php
session_start();

require_once '../sql/db_connect.php';


$username = $password = $email = $confirmPassword = "";
$usernameError = $passwordError = $emailError = $confirmPasswordError = "";
$formSubmitted = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $formSubmitted = true;
    //VALIDATE USERNAME
    if (empty($_POST["username"])) {
        $usernameError = "Username is required!";
    } else {
        //sanitize input
        $username = test_input($_POST["username"]);

        //validate input
        if (strlen($username) < 3) {
            $usernameError = "Username needs to be 3 characters or longer";
        } else {
            try {
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
                $stmt->execute([':username' => $username]);
                $count = $stmt->fetchColumn();
                if ($count > 0) {
                    $usernameError = "This username is already taken";
                }
            } catch(PDOException $e) {
                die("Error: " . $e->getMessage());
            }
        }
    }
    //VALIDATE EMAIL
    if (empty($_POST["email"])) {
        $emailError = "Email is required!";
    } else {
        //sanitize input
        $email = test_input($_POST["email"]);

        //validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailError = "Invalid email format";
        }
    }
    //VALIDATE PASSWORD
    if (empty($_POST["password"])) {
        $passwError = "Password is required!";
    } else {
        $password = $_POST["password"];
        //matches against regex for validation (8 characters, 1 uppercase, 1 number, 1 symbol)
        if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
            $passwordError = "Password should be at least 8 characters with at least one uppercase letter, one number, and one symbol";
        }
    }
    //VALIDATE CONFIRM PASSWORD
    if (empty($_POST["confirm-password"])) {
        $confirmPasswordError = "Please Confirm Your Password!";
    } else {
        $confirmPassword = $_POST["confirm-password"];
        if ($confirmPassword !== $password){
            $confirmPasswordError = "Passwords do not match!";
        }
    }
    //if no errors, proceed
    if (empty($usernameError) && empty($emailError) && empty($passwordError) && empty($confirmPasswordError)) {
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare(
                "INSERT INTO users (username, first_name, last_name, email, password, bio, pfp, role) 
                 VALUES (:username, :first_name, :last_name, :email, :password, :bio, :pfp, :role)"
            );            
            $stmt->execute([
                ':username' => $username,
                ':first_name' => '',
                ':last_name' => '',
                ':email' => $email,
                ':password' => $hashedPassword,
                ':bio' => '',
                ':pfp' => '',
                ':role' => 'user'
            ]);            

        } catch(PDOException $e) {
            die("Error: " . $e->getMessage());
        }
        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['username'] = $username;
        $_SESSION['logged_in'] = true;
        header("Location: feed.php");
            exit;
    }
}
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="../styles/main.css">
    <link rel="stylesheet" href="../styles/register.css">
</head>
<body>
    <div id="navbar"></div>
    <div id="topnav"></div>
    <div id="content" class="center-content">
        <div class="register-container">
            <h1>Register</h1>

            <div class="profile-pic-container">
                <label for="profile-pic" class="profile-pic-label">
                    <img src="../assets/profile-icon.png" alt="Profile Picture" class="profile-pic">
                    <div class="edit-icon">
                        <img src="../assets/edit-icon.png" alt="Edit">
                    </div>
                </label>
                <input type="file" id="profile-pic" name="profile-pic" accept="image/png, image/jpeg, image/jpg" hidden>
                <span class="error-message" id="profilePicError"></span>
            </div>

            <form class="register-form" id="registerForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" novalidate>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Enter your username" value="<?php echo $username; ?>" required>
                    <span class="error-message" id="usernameError"><?php echo $usernameError; ?></span>
                </div>
            
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" value="<?php echo $email; ?>" required>
                    <span class="error-message" id="emailError"><?php echo $emailError; ?></span>
                </div>
            
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    <span class="error-message" id="passwordError"><?php echo $passwordError; ?></span>
                </div>
            
                <div class="form-group">
                    <label for="confirm-password">Confirm Password</label>
                    <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm your password" required>
                    <span class="error-message" id="confirmPasswordError"><?php echo $confirmPasswordError; ?></span>
                </div>

                <button type="submit" class="btn submit-btn">Register</button>
            </form>

            <p class="login-link">Already have an account? <a href="login.html">Login here</a></p>
        </div>
    </div>
    <script src="../scripts/router.js" defer></script>

    <script>
        document.getElementById("profile-pic").addEventListener("change", function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.querySelector(".profile-pic").src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>

</body>
</html>
