(function () {
    const pathPrefix = window.location.pathname.includes('/pages/') ? '../' : './';

    // Format the current page name
    let page = window.location.pathname.split('/').pop().split('.')[0];
    let pageName = page.split('-')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');

    console.log("Is the user logged in?", localStorage.getItem("isLoggedIn") ? "Yes" : "No");
    console.log("Username:", localStorage.getItem("loggedInUser"));
    console.log("Is the user an admin?", localStorage.getItem("isAdmin") ? "Yes" : "No");

    const navbar = document.getElementById('navbar');
    const topnav = document.getElementById('topnav');

    if (navbar) {
        navbar.innerHTML = `
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
                </div>
            </aside>
        `;
    }

    if (topnav) {
        topnav.innerHTML = `
            <div class="topnav">
                <div class="topnav-left">
                    <a href="${pathPrefix}pages/home.html" class="site-button">
                        <img src="${pathPrefix}assets/siteicon.png" alt="Site Logo" class="site-icon">
                    </a>
                </div>
                <div class="topnav-center">
                    <h1 id="page-label">${pageName}</h1>s
                    <input type="text" class="search-bar" placeholder="Search Bloggit">
                </div>
                <div class="topnav-right">
                    <!-- Optional user controls -->
                </div>
            </div>
        `;
    }
})();
