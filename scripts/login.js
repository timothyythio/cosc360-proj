document.getElementById("loginForm").addEventListener("submit", function (e) {
    e.preventDefault();
    validateLoginForm();
});

function validateLoginForm() {
    const username = document.getElementById("username");
    const password = document.getElementById("password");

    const usernameError = document.getElementById("usernameError");
    const passwordError = document.getElementById("passwordError");

    
    [username, password].forEach(input => input.classList.remove("invalid", "valid"));
    [usernameError, passwordError].forEach(error => error.textContent = "");

    let isValid = true;

    //username needs to be > 3
    if (username.value.trim().length < 3) {
        username.classList.add("invalid");
        usernameError.textContent = "Username must be at least 3 wewe long.";
        isValid = false;
    } else {
        username.classList.add("valid");
    }

    //password cannot be empty
    if (password.value.trim() === "") {
        password.classList.add("invalid");
        passwordError.textContent = "Password cannot be empty.";
        isValid = false;
    } else {
        password.classList.add("valid");
    }

    //goes to feed if valid
    if (isValid) {
        usernameValue = username.value.trim();
        document.getElementById("loginForm").reset();
        [username, password].forEach(input => input.classList.remove("valid"));

        if (usernameValue === "admin") {
            alert("Admin login successful! Redirecting to the admin page...");
            loginAdmin(usernameValue);
        } else {
            alert("Login successful! Redirecting to the feed page...");
            loginUser(usernameValue);
        }
        
    }
}
