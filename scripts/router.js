(function () {
    const pathPrefix = window.location.pathname.includes('/pages/') ? '../' : './';

// Get current page name, separate each word, capitalize
let currentPageName = window.location.pathname.split('/').pop().split('.').shift();
currentPageName = currentPageName.split('-')
    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ');

// Logging session data
console.log("ROUTER: Is the user logged in?: ", isLoggedIn ? "yes" : "no");
console.log("ROUTER: Username: ", loggedInUser);
console.log("ROUTER: Is the user an admin: ", isAdmin === 'true' ? "yes" : "no");

// Sidebar navigation
document.getElementById('navbar').innerHTML = `
    <aside class="navbar">
        <div class="navbar-links">
            <a href="${pathPrefix}pages/new-post.php" class="nav-item">
                <div class="nav-icon-container">
                    <img src="${pathPrefix}assets/plus-icon.png" alt="New Post Icon" class="nav-icon">
                </div>
                <span class="nav-text">New Post</span>
            </a>
            <a href="${pathPrefix}pages/feed.php" class="nav-item">
                <div class="nav-icon-container">
                    <img src="${pathPrefix}assets/newspaper-icon.png" alt="Feed Icon" class="nav-icon">
                </div>
                <span class="nav-text">Feed</span>
            </a>
            <a href="${pathPrefix}pages/search.php" class="nav-item">
                <div class="nav-icon-container">
                    <img src="${pathPrefix}assets/search-icon.png" alt="Search Icon" class="nav-icon">
                </div>
                <span class="nav-text">Search</span>
            </a>
            <a href="${pathPrefix}pages/topics.php" class="nav-item">
                <div class="nav-icon-container">
                    <img src="${pathPrefix}assets/topic-icon.png" alt="Topic Icon" class="nav-icon">
                </div>
                <span class="nav-text">Topics</span>
            </a>
        </div>
    </aside>
`;

// Top navigation bar
document.getElementById('topnav').innerHTML = `
    <div class="topnav">
        <div class="topnav-left">
            <a href="${pathPrefix}pages/feed.php" class="site-button">
                <img src="${pathPrefix}assets/siteicon.png" alt="Site Logo" class="site-icon">
            </a>
        </div>
        <div class="topnav-center">
            <h1 id="page-label">${currentPageName}</h1>
            <input type="text" class="search-bar" placeholder="Search Bloggit">
        </div>
        <div class="topnav-right" id="dynamicNav"></div>
    </div>
`;

// Conditional rendering of top right nav based on PHP session data
let topNavContent = '';

if (isLoggedIn) {
    topNavContent = `
        <a href="${pathPrefix}pages/profile.php" class="profile-link">${loggedInUser}</a>
        <a href="${pathPrefix}scripts/logout.php" class="logout-btn">Logout</a>
    `;

    if (isAdmin === 'true') {
        topNavContent += `
            <a href="${pathPrefix}pages/admin.php" class="admin-btn">Admin Panel</a>
        `;
    }
} else {
    topNavContent = `
        <a href="${pathPrefix}pages/login.php" class="login-btn">Login</a>
        <a href="${pathPrefix}pages/register.php" class="register-btn">LOL</a>
    `;
}

document.getElementById('dynamicNav').innerHTML = topNavContent;
