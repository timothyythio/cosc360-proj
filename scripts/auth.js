console.log("auth.js loaded!")

function loginUser(username) {
    localStorage.setItem("loggedInUser", username);
    localStorage.setItem("isLoggedIn", "true");
    window.location.href = "feed.html"; 
}

function loginAdmin (username) {
    localStorage.setItem("loggedInUser", username);
    localStorage.setItem("isLoggedIn", "true");
    localStorage.setItem("isAdmin", "true");
    window.location.href = "admin.html"; 
}

function logoutUser() {
    localStorage.removeItem("loggedInUser");
    localStorage.removeItem("isLoggedIn");
    localStorage.removeItem("isAdmin");
}

function checkUserLogin() {
    if (!localStorage.getItem("isLoggedIn")) {
        window.location.href = "login.html";

    }
}
function checkAdmin() {
    if (!localStorage.getItem("isAdmin")) {
        window.location.href = "feed.html";
    }

}

function updateNavLinks() {
    const topNavRight = document.querySelector(".topnav-right");
    if (localStorage.getItem("isLoggedIn") && localStorage.getItem("isAdmin")) {
        topNavRight.innerHTML = `
            <a href="profile.html">Profile</a>
            <a href="admin.html">Admin</a>
            <a href="feed.html" onclick="logoutUser()" class="logout-btn">Logout</a>
        `;
    } else if (localStorage.getItem("isLoggedIn")) {
        // If logged in, show profile & logout
        topNavRight.innerHTML = `
            <a href="profile.html">Profile</a>
            <a href="feed.html" onclick="logoutUser()" class="logout-btn">Logout</a>
        `;
    }
    
    else {
        // If logged out, show Register & Login links
        topNavRight.innerHTML = `
            <a href="register.html" class="register-btn">Register</a>
            <a href="login.html" class="login-btn">Login</a>
        `;
    }
}

document.addEventListener("DOMContentLoaded", updateNavLinks);
