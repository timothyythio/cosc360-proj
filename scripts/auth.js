console.log("auth.js loaded");
console.log("AUTH: Is the user logged in?: ", isLoggedIn ? "yes" : "no");
console.log("AUTH: Username: ", loggedInUser);
console.log("AUTH: Is the user an admin: ", isAdmin === 'true' ? "yes" : "no");

function logoutUser() {
    window.location.href = "logout.php"; // Server-side logout destroys the session
}

function checkUserLogin() {
    if (!isLoggedIn) {
        alert("You are not logged in! Redirecting to login page");
        window.location.href = "login.php";
    }
}

function checkAdmin() {
    if (!isAdmin) {
        alert("You are not an admin! Redirecting to feed");
        window.location.href = "feed.php";
    }
}

function updateNavLinks() {
    console.log("Updating nav...");
    const topNavRight = document.querySelector(".topnav-right");

    if (isLoggedIn && isAdmin === 'true') {
        console.log("Admin is logged in");
        topNavRight.innerHTML = `

            <a href="profile.html">Profile</a>
            <a href="admin.php">Admin</a>
            <a href="feed.php" onclick="logoutUser()" class="logout-btn">Logout</a>
            <a href="profile.php">Profile</a>
            <a href="admin.php">Admin</a>
            <a href="#" onclick="logoutUser()" class="logout-btn">Logout</a>
        `;
    } else if (isLoggedIn) {
        console.log("Regular user is logged in");
        topNavRight.innerHTML = `

            <a href="profile.php">Profile</a>
            <a href="#" onclick="logoutUser()" class="logout-btn">Logout</a>
        `;
    } else {
        console.log("User is NOT logged in");
        topNavRight.innerHTML = `
            <a href="register.php" class="register-btn">Register</a>
            <a href="login.php" class="login-btn">Login</a>
        `;
    }
}

document.addEventListener("DOMContentLoaded", updateNavLinks);
