const pathPrefix = window.location.pathname.includes('/pages/') ? '../' : './';

document.getElementById('navbar').innerHTML = `
    <nav>
        <a href="${pathPrefix}index.html">Landing Page</a>
        <a href="${pathPrefix}pages/home.html">Home</a>
        <a href="${pathPrefix}pages/login.html">Login</a>
        <a href="${pathPrefix}pages/register.html">Register</a>
        <a href="${pathPrefix}pages/profile.html">Profile</a>
        <a href="${pathPrefix}pages/admin.html">Admin</a>
    </nav>
`;


document.getElementById('footer').innerHTML = `
    <footer>
        <p>&copy; 2025 Blogging Platform. All rights reserved.</p>
    </footer>
`;
