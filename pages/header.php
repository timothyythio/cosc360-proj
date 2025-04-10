<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../sql/db_connect.php';
$pageTitle = $pageTitle ?? 'Bloggit';
$pfpPath = '../assets/default-profile.png';

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    $stmt = $pdo->prepare("SELECT pfp FROM users WHERE user_id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && !empty($user['pfp'])) {
        $pfpPath = htmlspecialchars($user['pfp']);
    }
}


// User state
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$loggedInUser = $isLoggedIn ? $_SESSION['username'] : '';
$isAdmin = $isLoggedIn && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Get current page name for active navigation highlighting
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="../styles/header.css">
    <link rel="stylesheet" href="../styles/main.css">
    <?php if (isset($pageStyles) && is_array($pageStyles)): ?>
        <?php foreach ($pageStyles as $style): ?>
            <link rel="stylesheet" href="<?php echo "../styles/" . $style; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    <script>
        // Pass PHP variables to JavaScript
        const isLoggedIn = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;
        const loggedInUser = "<?php echo htmlspecialchars($loggedInUser, ENT_QUOTES); ?>";
        const isAdmin = <?php echo $isAdmin ? 'true' : 'false'; ?>;
    </script>
</head>
<body>
    <div id="app">
        <!-- Top Navigation Bar -->
        <div class="topnav">
            <div class="topnav-left">
                <a href="../pages/feed.php" class="site-button">
                    <img src="../assets/siteicon.png" alt="Site Logo" class="site-icon">
                </a>
            </div>
            
            <!-- breadcrumbs here -->
            <div class="topnav-center">
                <nav class="breadcrumbs-inline">
                    <?php
                    $breadcrumbs = [];
                    $uri = $_SERVER['REQUEST_URI'];
                    $path = parse_url($uri, PHP_URL_PATH);
                    $segments = explode('/', trim($path, '/'));

                    $currentPage = end($segments);
                    if ($currentPage === 'new-post.php') {
                        $breadcrumbs[] = ['label' => 'Create Post', 'link' => ''];
                    } elseif ($currentPage === 'topics.php') {
                        $breadcrumbs[] = ['label' => 'Topics', 'link' => ''];
                    } elseif ($currentPage === 'search.php') {
                        $breadcrumbs[] = ['label' => 'Search', 'link' => ''];
                    } elseif ($currentPage === 'feed.php') {
                        $breadcrumbs[] = ['label' => 'Home', 'link' => ''];
                    } elseif ($currentPage === 'profile.php') {
                        $breadcrumbs[] = ['label' => 'Profile', 'link' => ''];
                    }else {
                        $breadcrumbs[] = ['label' => 'Home', 'link' => '/cosc360-proj/pages/feed.php'];
                    }

                    
                    if (in_array('post.php', $segments)) {
                        $breadcrumbs[] = ['label' => 'Post', 'link' => ''];
                    } elseif (in_array('draft.php', $segments)) {
                        $breadcrumbs[] = ['label' => 'My Drafts', 'link' => ''];
                    } 
                    // elseif (in_array('new-post.php', $segments)) {
                    //     $breadcrumbs[] = ['label' => 'Create Post', 'link' => ''];
                    // }

                    $last = count($breadcrumbs) - 1;
                    foreach ($breadcrumbs as $i => $crumb) {
                        if ($i !== $last) {
                            echo '<a href="' . $crumb['link'] . '">' . $crumb['label'] . '</a> &gt; ';
                        } else {
                            echo '<span>' . $crumb['label'] . '</span>';
                        }
                    }
                    ?>
                </nav>
                <form action="search.php" method="GET" class="header-search-form">
                    <input 
                        type="text" 
                        name="q" 
                        class="search-bar" 
                        placeholder="Search Bloggit"
                        value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>"
                    >
                    <button type="submit" class="search-icon-button">
                        <img src="../assets/search-icon.png" alt="Search">
                    </button>
                </form>
            </div>

            <div class="topnav-right">
                <?php if ($isLoggedIn): ?>
                    <div class="notification-wrapper">
                        <button id="notification-btn">
                            <img src="../assets/mail_icon.png" alt="Notifications" class="notification-icon">
                            <span id="notification-dot" class="notification-dot"></span>
                        </button>

                        <div class="notification-dropdown" id="notification-dropdown">
                            <p class="dropdown-header">Notifications</p>
                            <ul class="notification-list" id="notification-list">
                            </ul>
                        </div>
                    </div>
                    <?php if ($isAdmin && $isLoggedIn): ?>
                        <a href="../pages/admin.php" class="login-btn">Admin</a>
                    <?php endif; ?>
                    <a href="../pages/profile.php" class="profile-icon-link">
                        <img src="<?= $pfpPath ?>" alt="Profile" class="user-icon">
                    </a>
                    <a href="logout.php" class="logout-icon-link">
                        <img src="../assets/logout-icon.svg" class="logout-icon" alt="Logout">
                    </a>
                <?php else: ?>
                    <a href="login.php" class="login-btn">Login</a>
                    <a href="register.php" class="register-btn">Register</a>
                <?php endif; ?>
            </div>

        </div>
        
        <!-- Side Navigation Bar -->
        <aside class="navbar">
            <div class="navbar-links">
                <a href="../pages/new-post.php" class="nav-item <?php echo $currentPage === 'new-post' ? 'active' : ''; ?>">
                    <div class="nav-icon-container">
                        <img src="../assets/plus-icon.png" alt="New Post Icon" class="nav-icon">
                    </div>
                    <span class="nav-text">New Post</span>
                </a>
                <a href="../pages/feed.php" class="nav-item <?php echo $currentPage === 'feed' ? 'active' : ''; ?>">
                    <div class="nav-icon-container">
                        <img src="../assets/newspaper-icon.png" alt="Feed Icon" class="nav-icon">
                    </div>
                    <span class="nav-text">Feed</span>
                </a>
                <a href="../pages/search.php" class="nav-item <?php echo $currentPage === 'search' ? 'active' : ''; ?>">
                    <div class="nav-icon-container">
                        <img src="../assets/search-icon.png" alt="Search Icon" class="nav-icon">
                    </div>
                    <span class="nav-text">Search</span>
                </a>
                <a href="../pages/topics.php" class="nav-item <?php echo $currentPage === 'topics' ? 'active' : ''; ?>">
                    <div class="nav-icon-container">
                        <img src="../assets/topic-icon.png" alt="Topic Icon" class="nav-icon">
                    </div>
                    <span class="nav-text">Topics</span>
                </a>
            </div>
        </aside>
    </div>  
    <main id="content"></main>
</body>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const notifBtn = document.getElementById("notification-btn");
    const notifDropdown = document.getElementById("notification-dropdown");
    const notifDot = document.getElementById("notification-dot");

    //for future if ever we want to add unread dot
    // if (localStorage.getItem("notificationsRead") === "true") {
    //     notifDot.style.display = "none";
    // }

    if (notifBtn && notifDropdown) {
    notifBtn.addEventListener("click", () => {
        console.log("button clicked");
        // localStorage.setItem("notificationsRead", "true");
        notifDot.style.display = "none";
        notifDropdown.style.display =
        notifDropdown.style.display === "block" ? "none" : "block";
    });
    } else {
    console.log("❌ Elements not found!");
    }
    //close dropdown when clicking outside
    document.addEventListener("click", (e) => {
        if (!notifBtn.contains(e.target) && !notifDropdown.contains(e.target)) {
        notifDropdown.style.display = "none";
        }
    });
});
</script>

<script>
function fetchNotifications() {
  fetch("../php/get_notifications.php") 
    .then(res => res.json())
    .then(data => {
        const list = document.getElementById("notification-list");
        list.innerHTML = "";

        //for future if ever we want to add unread dot
        // if (data.length > 0) {
        //     localStorage.setItem("notificationsRead", "false");
        //     document.getElementById("notification-dot").style.display = "inline-block";
        // }

        data.forEach(notif => {
            const li = document.createElement("li");
            li.className = "notification-item";
            li.innerHTML = `
            <span>${notif.message}</span>
            <span class="timestamp">${notif.created_at}</span>
            `;
            list.appendChild(li);
      });
    });
}

document.addEventListener("DOMContentLoaded", () => {
  fetchNotifications();
  setInterval(fetchNotifications, 5000); // every 5 sec
});
</script>

