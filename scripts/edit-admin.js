document.addEventListener("DOMContentLoaded", function () {
    const editAdminForm = document.getElementById("editAdminForm");


    const adminData = {
        id: "#123456", 
        name: "John Doe",
        email: "johndoe@example.com",
        country: "United States",
        city: "New York",
        password: "",  
    };

    document.getElementById("admin-id").value = adminData.id;
    document.getElementById("admin-name").value = adminData.name;
    document.getElementById("admin-email").value = adminData.email;
    document.getElementById("admin-country").value = adminData.country;
    document.getElementById("admin-city").value = adminData.city;

    editAdminForm.addEventListener("submit", function (event) {
        event.preventDefault(); 

        const updatedAdminData = {
            name: document.getElementById("admin-name").value.trim(),
            email: document.getElementById("admin-email").value.trim(),
            country: document.getElementById("admin-country").value.trim(),
            city: document.getElementById("admin-city").value.trim(),
            password: document.getElementById("admin-password").value,
            confirmPassword: document.getElementById("confirm-password").value,
        };

        if (!updatedAdminData.name || !updatedAdminData.email || !updatedAdminData.country || !updatedAdminData.city) {
            alert("All fields except password are required!");
            return;
        }

        if (updatedAdminData.password !== updatedAdminData.confirmPassword) {
            alert("Passwords do not match!");
            return;
        }

        console.log("Updated Admin Data:", updatedAdminData);
        alert("Profile updated successfully!");
        window.location.href = "admin.html";
    });

    document.querySelector(".cancel-btn").addEventListener("click", function () {
        window.location.href = "admin.html";
    });
});
