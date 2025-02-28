document.getElementById("profile-pic").addEventListener("change", function (event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            document.querySelector(".profile-pic").src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});



document.getElementById("registerForm").addEventListener("submit", function (e) {
    e.preventDefault();
    validateForm();
});

function validateForm() {
    const username = document.getElementById("username");
    const email = document.getElementById("email");
    const password = document.getElementById("password");
    const confirmPassword = document.getElementById("confirm-password");

    const usernameError = document.getElementById("usernameError");
    const emailError = document.getElementById("emailError");
    const passwordError = document.getElementById("passwordError");
    const confirmPasswordError = document.getElementById("confirmPasswordError");

    [username, email, password, confirmPassword].forEach(input => input.classList.remove("invalid", "valid"));
    [usernameError, emailError, passwordError, confirmPasswordError].forEach(error => error.textContent = "");

    let isValid = true;
    //username needs to be more than 3 chars
    if (username.value.trim().length < 3) {
        username.classList.add("invalid");
        usernameError.textContent = "Username must be at least 3 characters long.";
        isValid = false;
    } else {
        username.classList.add("valid");
    }

    //email needs to have valid format (a@example.com)
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email.value.trim())) {
        email.classList.add("invalid");
        emailError.textContent = "Please enter a valid email address.";
        isValid = false;
    } else {
        email.classList.add("valid");
    }

    //password needs to have one uppercase, one number, one symbol
    const passwordPattern = /^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
    if (!passwordPattern.test(password.value)) {
        password.classList.add("invalid");
        passwordError.textContent = "Password must be at least 8 characters long, contain an uppercase letter, a number, and a symbol.";
        isValid = false;
    } else {
        password.classList.add("valid");
    }

    //passwords need to match
    if (confirmPassword.value !== password.value || confirmPassword.value === "") {
        confirmPassword.classList.add("invalid");
        confirmPasswordError.textContent = "Passwords do not match.";
        isValid = false;
    } else {
        confirmPassword.classList.add("valid");
    }

    //submit if all fields are valid. redirects user to feed
    if (isValid) {
        alert("Registration successful!");
        usernameValue = username.value.trim();
        document.getElementById("registerForm").reset();
        loginUser(usernameValue);
    }
}
