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
    <link rel="stylesheet" href="../styles/topics.css">
</head>

<body>
    <div id="app">
        <div id="topnav"></div>
        <div id="navbar"></div>
        <div id="sortbar">
        </div>
        <main id="content">
        </main>
        <div id="footer"></div>
    </div>

    <script src="../scripts/router.js"></script>
    <script src="../scripts/auth.js" defer></script>
    <script src="../scripts/topics.js" defer></script>

</body>

</html>