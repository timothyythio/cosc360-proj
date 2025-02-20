const pathPrefix = window.location.pathname.includes('/pages/') ? '../' : './';
let currentPageName = window.location.pathname.split('/').pop().split('.').shift();
currentPageName = currentPageName.at(0).toUpperCase() + currentPageName.slice(1);

document.getElementById('navbar').innerHTML = `
        <aside class="navbar">
            <div class="navbar-links">
                <a href="${pathPrefix}pages/new-post.html" class="nav-item">
                    <div class="nav-icon-container">
                        <img src="${pathPrefix}assets/plus-icon.png" alt="New Post Icon" class="nav-icon">
                    </div>
                    <span class="nav-text">New Post</span>
                </a>
                <a href="${pathPrefix}pages/feed.html" class="nav-item">
                    <div class="nav-icon-container">
                        <img src="${pathPrefix}assets/newspaper-icon.png" alt="Feed Icon" class="nav-icon">
                    </div>
                    <span class="nav-text">Feed</span>
                </a>
                <a href="${pathPrefix}pages/search.html" class="nav-item">
                    <div class="nav-icon-container">
                        <img src="${pathPrefix}assets/search-icon.png" alt="Search Icon" class="nav-icon">
                    </div>
                    <span class="nav-text">Search</span>
                </a>
                <a href="${pathPrefix}pages/topics.html" class="nav-item">
                    <div class="nav-icon-container">
                        <img src="${pathPrefix}assets/topic-icon.png" alt="Topic Icon" class="nav-icon">
                    </div>
                    <span class="nav-text">Topics</span>
                </a>
                <a href="${pathPrefix}pages/profile.html" class="nav-item">
                    <div class="nav-icon-container">
                        <img src="${pathPrefix}assets/profile-icon.png" alt="Profile Icon" class="nav-icon">
                    </div>
                    <span class="nav-text">Profile</span>
                </a>
            </div>
        </aside>
    `;
 document.getElementById('topnav').innerHTML = `
    <div class="topnav">
        <div class="topnav-left">
            <h1 id="page-label">${currentPageName}</h1>
        </div>
            <div class="topnav-center">
            <input type="text" class="search-bar" placeholder="Search Bloggit">
            <span class="search-icon">🔍</span>
        </div>
        <div class="topnav-right">
            <img src="${pathPrefix}assets/profile-icon.png" alt="User Icon" class="user-icon">
        </div>
    </div>
    `;

document.getElementById('footer').innerHTML = `
    <footer>
        <p>&copy; 2025 COSC360 Blogging Platform</p>
    </footer>
`;
