<!DOCTYPE html>
<html lang="en">
<?php 
session_start();
require_once '../sql/db_connect.php';

// grab posts from db
$stmt = $pdo->prepare("
    SELECT p.*, t.topic_name 
    FROM posts p
    LEFT JOIN topics t ON p.topic_id = t.topic_id
    WHERE p.status = 'posted'
    ORDER BY p.created_at DESC
");
$stmt->execute();
$posts = $stmt->fetchAll();
?>

<script>
    const isLoggedIn = <?php echo isset($_SESSION['logged_in']) && $_SESSION['logged_in'] ? 'true' : 'false'; ?>;
    const loggedInUser = "<?php echo isset($_SESSION['username']) ? $_SESSION['username'] : ''; ?>";
    const isAdmin = <?php echo (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ? 'true' : 'false'; ?>;
    
    // send post data to JS
    const postsData = <?php echo json_encode($posts); ?>;
</script>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bloggit</title>
    <link rel="stylesheet" href="../styles/main.css">
    <link rel="stylesheet" href="../styles/feed.css">
</head>

<body>
    <div id="app">
        <div id="topnav"></div>
        <div id="navbar"></div>
        <main id="content">
            <!-- posts will show up here -->
        </main>
        <div id="footer"></div>
    </div>

    <script src="../scripts/router.js?v=<?php echo time(); ?>" defer></script>
    <script src="../scripts/feed.js?v=<?php echo time(); ?>"></script>
    <script src="../scripts/auth.js?v=<?php echo time(); ?>" defer></script>
</body>

</html>