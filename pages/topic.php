<?php 
session_start();
require_once '../sql/db_connect.php';

// get topic ID 
$topic_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM topics WHERE topic_id = ?");
$stmt->execute([$topic_id]);
$topic = $stmt->fetch();

if (!$topic) {
    // not found, redirect to topics page
    header('Location: topics.php');
    exit;
}

$pageTitle = htmlspecialchars($topic['topic_name']) . ' - Bloggit';
$pageStyles = ['main.css', 'topic.css'];

include('header.php');
?>

<main id="topicPageBody">
<div id="topicMain">
    <div id="topicImgContainer">
        <img src="<?php echo $topic['topic_img'] ? '../assets/'.$topic['topic_img'] : '../assets/siteicon.png'; ?>" id="topicImg">
    </div>
    <div id="topicDescContainer">
        <h1 id="topicName"><?php echo htmlspecialchars($topic['topic_name']); ?></h1>
        <p id="topicFollows"><?php echo $topic['members']; ?> Followers</p>
        <p id="topicDesc"><?php echo htmlspecialchars($topic['description'] ?? 'No description available.'); ?></p>
        <button id="topicFollowBtn">Follow Topic</button>
    </div>
</div>
<div id="sortbar"></div>
<div id="topicPosts">
    <!-- topic posts will appear here -->
    <div class="loading">Loading posts...</div>
</div>
</main>

<script>
    const topicData = {
        id: <?php echo $topic_id; ?>,
        name: "<?php echo addslashes($topic['topic_name']); ?>",
        description: "<?php echo addslashes($topic['description'] ?? ''); ?>",
        members: <?php echo $topic['members']; ?>,
        image: "<?php echo $topic['topic_img'] ? $topic['topic_img'] : 'siteicon.png'; ?>"
    };
</script>
<script src="../scripts/topic.js?v=<?php echo time(); ?>" defer></script>