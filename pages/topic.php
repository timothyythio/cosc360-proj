<!DOCTYPE html>
<html lang="en">
    <?php session_start(); ?>
<script>
    const isLoggedIn = <?php echo isset($_SESSION['logged_in']) && $_SESSION['logged_in'] ? 'true' : 'false'; ?>;
    const loggedInUser = "<?php echo isset($_SESSION['username']) ? $_SESSION['username'] : ''; ?>";
    const isAdmin = "<?php echo (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ? 'true' : 'false'; ?>";
</script>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bloggit</title>
    <link rel="stylesheet" href="../styles/main.css">
    <link rel="stylesheet" href="../styles/topic.css">
</head>

<body>
    <div id="app">
        <div id="topnav"></div>
        <div id="navbar"></div>
        <main id="content">
            <div id="topicMain">
                <div id="topicImgContainer">
                    <img src="../assets/siteicon.png" id="topicImg">
                </div>
                <div id="topicDescContainer">
                    <h1 id="topicName">Topic Name</h1>
                    <p id="topicFollows">X Followers</p>
                    <p id="topicDesc">Topic Description</p>
                    <button id="topicFollowBtn">Follow Topic</button>
                </div>
            </div>
            <div id="sortbar"></div>
        </main>
        <div id="footer"></div>
    </div>

    <script src="../scripts/router.js"></script>
    <script src="../scripts/auth.js" defer></script>
    <script src="../scripts/topic.js" defer></script>
</body>

</html>