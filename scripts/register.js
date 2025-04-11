document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("form");
    const errorContainer = document.createElement("div");
    errorContainer.style.color = "red";
    form.prepend(errorContainer);
  
    form.addEventListener("submit", (event) => {
      errorContainer.innerHTML = ""; 
  
      const username = form.username.value.trim();
      const email = form.email.value.trim();
      const password = form.password.value;
      const confirmPassword = form.confirm_password.value;
  
      const errors = [];
  
      if (username.length < 3 || !/^[a-zA-Z0-9]+$/.test(username)) {
        errors.push("Username must be at least 3 characters and contain only letters and numbers.");
      }
  
      const emailPattern = /^[^@\s]+@[^@\s]+\.[^@\s]+$/;
      if (!emailPattern.test(email)) {
        errors.push("Please enter a valid email address.");
      }
  
      const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/;
      if (!passwordPattern.test(password)) {
        errors.push("Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character.");
      }
  
      if (password !== confirmPassword) {
        errors.push("Passwords do not match.");
      }
  
      if (errors.length > 0) {
        event.preventDefault();
        errorContainer.innerHTML = errors.map(e => `<p>${e}</p>`).join("");
      }
    });
  });
  