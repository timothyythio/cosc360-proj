<!DOCTYPE html>
<html lang="en">

<?php 
session_start();
require_once '../sql/db_connect.php';

//$pageStyles = ["../styles/feed.css"];  Make sure this is correct path
$pageStyles = [
    "../styles/main.css",
    "../styles/feed.css"
];


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

include('header.php');
?>

<script>
    // send post data to JS
    const postsData = <?php echo json_encode($posts); ?>;
</script>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bloggit</title>
    <link rel="stylesheet" href="<?php echo $pathPrefix; ?>styles/main.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo $pathPrefix; ?>styles/feed.css?v=<?php echo time(); ?>">
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