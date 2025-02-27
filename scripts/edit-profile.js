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

document.addEventListener("DOMContentLoaded", function () {
    console.log("Edit Profile JS Loaded"); 

    document.getElementById("editProfileForm").addEventListener("submit", function (e) {
        e.preventDefault();
        validateEditProfileForm();
    });
});

function validateEditProfileForm() {
    const username = document.getElementById("username");
    const email = document.getElementById("email");
    const newPassword = document.getElementById("new-password");
    const confirmPassword = document.getElementById("confirm-password");
    const phone = document.getElementById("phone");
    const bio = document.getElementById("bio");

    const usernameError = document.getElementById("usernameError");
    const emailError = document.getElementById("emailError");
    const passwordError = document.getElementById("passwordError");
    const confirmPasswordError = document.getElementById("confirmPasswordError");
    const phoneError = document.getElementById("phoneError");
    const bioError = document.getElementById("bioError");

    if (!usernameError || !emailError || !passwordError || !confirmPasswordError || !phoneError || !bioError) {
        console.error("One or more error elements are missing from the DOM!");
        return; 
    }

    [username, email, newPassword, confirmPassword, phone, bio].forEach(input => {
        if (input) input.classList.remove("invalid", "valid");
    });

    [usernameError, emailError, passwordError, confirmPasswordError, phoneError, bioError].forEach(error => {
        if (error) error.textContent = "";
    });

    let isValid = true;

    if (username.value.trim().length < 3) {
        username.classList.add("invalid");
        usernameError.textContent = "Username must be at least 3 characters long.";
        isValid = false;
    } else {
        username.classList.add("valid");
    }

    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email.value.trim())) {
        email.classList.add("invalid");
        emailError.textContent = "Please enter a valid email address.";
        isValid = false;
    } else {
        email.classList.add("valid");
    }

    const passwordPattern = /^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
    if (!passwordPattern.test(newPassword.value)) {
        newPassword.classList.add("invalid");
        passwordError.textContent = "Password must be at least 8 characters long, contain an uppercase letter, a number, and a symbol.";
        isValid = false;
    } else {
        password.classList.add("valid");
    }

    //passwords need to match
    if (confirmPassword.value !== newPassword.value || confirmPassword.value === "") {
        confirmPassword.classList.add("invalid");
        confirmPasswordError.textContent = "Passwords do not match.";
        isValid = false;
    } else {
        confirmPassword.classList.add("valid");
    }

    const phonePattern = /^\d{7,15}$/; // allows 7-15 digit numbers
    if (phone.value.trim() !== "" && !phonePattern.test(phone.value.trim())) {
        phone.classList.add("invalid");
        phoneError.textContent = "Please enter a valid phone number (digits only, 7-15 characters).";
        isValid = false;
    } else if (phone.value.trim() !== "") {
        phone.classList.add("valid");
    }

    // bio validation (optional but must be under 200 characters)
    if (bio.value.trim().length > 200) {
        bio.classList.add("invalid");
        bioError.textContent = "Bio must be under 200 characters.";
        isValid = false;
    } else if (bio.value.trim() !== "") {
        bio.classList.add("valid");
    }

    if (isValid) {
        alert("Profile updated successfully!");
        window.location.href = "profile.html"; // redirect back to profile page
    }
}
