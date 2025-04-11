<?php
session_start();
require_once '../sql/db_connect.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$pageTitle = 'Topics - Bloggit';
$pageStyles = ['main.css', 'topics.css'];

include('header.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
</head>

<body>
    <div id="app">
        <div id="sortbar">
        </div>
        <main id="topicContent">
            <!-- topics will appear here -->
            <div class="loading">Loading topics...</div>
        </main>
        <div id="footer">
        </div>
    </div>


    <script src="../scripts/topics.js" defer></script>

</body>
</html>
