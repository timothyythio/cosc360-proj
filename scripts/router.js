const pathPrefix = window.location.pathname.includes('/pages/') ? '../' : './';

//get current page name, separate each word by splitting "-", and capitalizing the first char
let currentPageName = window.location.pathname.split('/').pop().split('.').shift();
currentPageName = currentPageName.split('-') 
    .map(word => word.charAt(0).toUpperCase() + word.slice(1)) 
    .join(' '); 
console.log("Is the user logged in?: ",localStorage.getItem("isLoggedIn")? "yes": "no");
console.log("Username: ", localStorage.getItem("loggedInUser"));
console.log("Is the user an admin?: " , localStorage.getItem("isAdmin")? "yes": "no");



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
            </div>
        </aside>
    `;
document.getElementById('topnav').innerHTML = `
    <div class="topnav">
        <div class="topnav-left">
            <a href="${pathPrefix}pages/home.html" class="site-button">
                <img src="${pathPrefix}assets/siteicon.png" alt="Site Logo" class="site-icon">
            </a>
        </div>
        <div class="topnav-center">
            <h1 id="page-label">${currentPageName}</h1>
            <input type="text" class="search-bar" placeholder="Search Bloggit">
        </div>
        <div class="topnav-right">
            
        </div>
    </div>
`;


// document.getElementById('footer').innerHTML = `
//     <footer>
//         <p>&copy; 2025 COSC360 Blogging Platform</p>
//     </footer>
// `;