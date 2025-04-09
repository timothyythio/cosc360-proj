<?php
// Start session and include necessary files
session_start();
require_once '../sql/db_connect.php';

// Set page title for header
$pageTitle = 'Topics - Bloggit';
$pageStyles = ['main.css', 'topics.css'];

// Include header
include('header.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <!-- CSS files are included in header.php -->
</head>

<body>
    <div id="app">
        <!-- Navigation elements are included in header.php -->
        <div id="sortbar">
            <!-- Sort bar will be populated by JavaScript -->
        </div>
        <main id="topicContent">
            <!-- Topics will be loaded here dynamically -->
            <div class="loading">Loading topics...</div>
        </main>
        <div id="footer">
            <!-- Footer content -->
        </div>
    </div>

    <script src="../scripts/topics.js?v=<?php echo time(); ?>"></script>
</body>
</html>
