document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("form");
    const errorContainer = document.createElement("div");
    errorContainer.style.color = "red";
    form.prepend(errorContainer);
  
    form.addEventListener("submit", (event) => {
      errorContainer.innerHTML = ""; // Clear previous errors
  
      const username = form.username.value.trim();
      const password = form.password.value;
  
      const errors = [];
  
      if (username.length < 3 || !/^[a-zA-Z0-9]+$/.test(username)) {
        errors.push("Username must be at least 3 characters and contain only letters and numbers.");
      }
  
      if (password.length === 0) {
        errors.push("Please enter your password.");
      }
  
      if (errors.length > 0) {
        event.preventDefault();
        errorContainer.innerHTML = errors.map(e => `<p>${e}</p>`).join("");
      }
    });
  });
  