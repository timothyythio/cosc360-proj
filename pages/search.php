<?php
session_start();
require_once '../sql/db_connect.php';

// Initialize search parameters
$searchQuery = trim($_GET['q'] ?? '');
$author = trim($_GET['author'] ?? '');
$topic = trim($_GET['topic'] ?? '');
$dateFrom = $_GET['from'] ?? '';
$dateTo = $_GET['to'] ?? '';
$hasImage = isset($_GET['has_image']) ? true : false;
$hasLikes = isset($_GET['min_likes']) ? (int)$_GET['min_likes'] : 0;
$sortBy = $_GET['sort'] ?? 'recent';

$results = [];
$resultCount = 0;
$error = '';
$showAdvanced = isset($_GET['advanced']) || 
                ($author || $topic || $dateFrom || $dateTo || $hasImage || $hasLikes);

// Get all topics for filter dropdown
$topics = [];
try {
    $topicStmt = $pdo->query("SELECT topic_id, topic_name FROM topics ORDER BY topic_name");
    $topics = $topicStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching topics: " . $e->getMessage());
}

// Process search
if (!empty($searchQuery) || $author || $topic || $dateFrom || $dateTo || $hasImage || $hasLikes) {
    try {
        // Build the query
        $sql = "SELECT 
                p.post_id, 
                p.title, 
                p.content, 
                p.created_at, 
                p.image_path,
                p.username as author_username,
                u.pfp,
                t.topic_name,
                (SELECT COUNT(*) FROM likes WHERE post_id = p.post_id) AS like_count
            FROM posts p 
            LEFT JOIN users u ON p.username = u.username
            LEFT JOIN topics t ON p.topic_id = t.topic_id
            WHERE p.status = 'posted'";
        
        $params = [];
        
        // Add search conditions
        if (!empty($searchQuery)) {
            $sql .= " AND (LOWER(p.title) LIKE LOWER(?) OR LOWER(p.content) LIKE LOWER(?))";
            $searchPattern = '%' . $searchQuery . '%';
            $params[] = $searchPattern;
            $params[] = $searchPattern;
        }
        
        if (!empty($author)) {
            $sql .= " AND LOWER(p.username) = LOWER(?)";
            $params[] = $author;
        }
        
        if (!empty($topic)) {
            $sql .= " AND p.topic_id = ?";
            $params[] = $topic;
        }
        
        if (!empty($dateFrom)) {
            $sql .= " AND p.created_at >= ?";
            $params[] = $dateFrom . ' 00:00:00';
        }
        
        if (!empty($dateTo)) {
            $sql .= " AND p.created_at <= ?";
            $params[] = $dateTo . ' 23:59:59';
        }
        
        if ($hasImage) {
            $sql .= " AND p.image_path IS NOT NULL AND p.image_path != ''";
        }
        
        if ($hasLikes > 0) {
            $sql .= " AND (SELECT COUNT(*) FROM likes WHERE post_id = p.post_id) >= ?";
            $params[] = $hasLikes;
        }
        
        // Add sorting
        $sql .= match($sortBy) {
            'likes' => " ORDER BY like_count DESC, p.created_at DESC",
            'oldest' => " ORDER BY p.created_at ASC",
            default => " ORDER BY p.created_at DESC" // 'recent' is default
        };
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $resultCount = count($results);
        
    } catch (PDOException $e) {
        $error = "We encountered an issue with your search.";
        error_log("Search error: " . $e->getMessage());
    }
} else {
    // Default content when no search term is provided
    $stmt = $pdo->prepare("SELECT posts.*, users.username
                       FROM posts
                       JOIN users ON posts.user_id = users.user_id
                       WHERE posts.status = 'posted' AND posts.created_at >= NOW() - INTERVAL 7 DAY
                       ORDER BY posts.likes DESC
                       LIMIT 3");
    $stmt->execute();
    $topicStmt = $pdo->prepare(" SELECT topics.topic_name, COUNT(*) AS post_count
                    FROM posts
                    JOIN topics ON posts.topic_id = topics.topic_id
                    WHERE posts.created_at >= NOW() - INTERVAL 7 DAY
                    GROUP BY topics.topic_name
                    ORDER BY post_count DESC
                    LIMIT 5");
    $topicStmt->execute();
    $hotTopics = $topicStmt->fetchAll();
    $hotPosts = $stmt->fetchAll();
    $showHotPosts = true;
}

// Helper functions
function createExcerpt($text, $maxLength = 200) {
    if (strlen($text) <= $maxLength) return $text;
    $excerpt = substr($text, 0, $maxLength);
    $lastSpace = strrpos($excerpt, ' ');
    return substr($excerpt, 0, $lastSpace) . '...';
}

function formatDate($timestamp) {
    $date = new DateTime($timestamp);
    return $date->format('M j, Y');
}

include("header.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= empty($searchQuery) ? 'Search Posts' : "Search Results for \"$searchQuery\"" ?> - Bloggit</title>
    <link rel="stylesheet" href="../styles/search.css">
</head>
<body>
    <div class="search-container">
        <h1>Search Posts</h1>
        
        <div class="search-header">
            <div class="search-form-container">
                <form class="search-form" method="GET" action="search.php" id="search-form">
                    <div class="search-input-wrapper">
                        <input 
                            type="text" 
                            name="q" 
                            class="search-input" 
                            placeholder="Search for posts..." 
                            value="<?= htmlspecialchars($searchQuery) ?>"
                        >
                        <button type="button" class="filter-toggle" id="toggle-filters" title="Toggle filters">
                            <span>🔍</span>
                        </button>
                    </div>
                    <button type="submit" class="search-button">Search</button>
                </form>
            </div>
        </div>
        
        <div class="advanced-search" id="advanced-search" style="display: <?= $showAdvanced ? 'block' : 'none' ?>">
            <div class="filter-grid">
                <div class="filter-group">
                    <label for="author">Author:</label>
                    <input 
                        type="text" 
                        id="author" 
                        name="author" 
                        placeholder="Username" 
                        value="<?= htmlspecialchars($author) ?>"
                        form="search-form"
                    >
                </div>
                
                <div class="filter-group">
                    <label for="topic">Topic:</label>
                    <select id="topic" name="topic" form="search-form">
                        <option value="">Any topic</option>
                        <?php foreach ($topics as $t): ?>
                            <option value="<?= $t['topic_id'] ?>" <?= $topic == $t['topic_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($t['topic_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="from">From date:</label>
                    <input 
                        type="date" 
                        id="from" 
                        name="from" 
                        value="<?= htmlspecialchars($dateFrom) ?>"
                        form="search-form"
                    >
                </div>
                
                <div class="filter-group">
                    <label for="to">To date:</label>
                    <input 
                        type="date" 
                        id="to" 
                        name="to" 
                        value="<?= htmlspecialchars($dateTo) ?>"
                        form="search-form"
                    >
                </div>
                
                <div class="filter-group">
                    <label for="min_likes">Minimum likes:</label>
                    <input 
                        type="number" 
                        id="min_likes" 
                        name="min_likes" 
                        min="0"
                        value="<?= $hasLikes ?>"
                        placeholder="0"
                        form="search-form"
                    >
                </div>
                
                <div class="filter-group">
                    <div class="checkbox-group">
                        <input 
                            type="checkbox" 
                            id="has_image" 
                            name="has_image"
                            <?= $hasImage ? 'checked' : '' ?>
                            form="search-form"
                        >
                        <label for="has_image">Has image</label>
                    </div>
                </div>
            </div>
            
            <div class="filter-actions">
                <input type="hidden" name="advanced" value="1" form="search-form">
                <button type="reset" class="reset-filters" form="search-form" id="reset-filters">Reset Filters</button>
            </div>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="error-message">
                <p><?= htmlspecialchars($error) ?></p>
            </div>
        <?php endif; ?>
        <div class="hot-section-container">
            <?php if (empty($results)) : ?>
                <div class="hot-posts-column">
                <h3>🔥 Hot Posts This Week</h3>
                    <?php foreach ($hotPosts as $post): ?>
                        <article class="search-result">
                            <div class="result-header">
                                <h3 class="result-title">
                                    <a href="post.php?id=<?= $post['post_id'] ?>">
                                        <?= htmlspecialchars($post['title']) ?>
                                    </a>
                                    <?php if (!empty($post['topic_name'])): ?>
                                        <span class="result-topic"><?= htmlspecialchars($post['topic_name']) ?></span>
                                    <?php endif; ?>
                                </h3>
                            </div>
                            <p class="result-excerpt"><?= nl2br(htmlspecialchars(createExcerpt($post['content']))) ?></p>
                            <div class="result-meta">
                                <img src="<?= htmlspecialchars($post['image_path'] ?? 'default-profile.png') ?>" alt="Author">
                                <span>Posted by <a href="#"><?= htmlspecialchars($post['username']) ?></a> on <?= date('M j, Y', strtotime($post['created_at'])) ?></span>
                                <span><?= $post['likes'] ?> 👍</span>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
                <div class="hot-topics-column">
                    <article class="search-result hot-topics-card">
                        <div class="result-header">
                            <h3 class="result-title">🔥 Trending Topics</h3>
                        </div>
                        <ul class="hot-topics-list">
                            <?php foreach ($hotTopics as $topics): ?>
                            <li>
                                <a href="search.php?topic=<?= urlencode($topics['topic_name']) ?>">
                                <?= htmlspecialchars($topics['topic_name']) ?>
                                </a>
                                (<?= $topics['post_count'] ?> post<?= $topics['post_count'] > 1 ? 's' : '' ?>)
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </article>
                </div>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($searchQuery) || $author || $topic || $dateFrom || $dateTo || $hasImage || $hasLikes): ?>
            <div class="results-header">
                <h2>
                    <?php if (!empty($searchQuery)): ?>
                        Results for: "<?= htmlspecialchars($searchQuery) ?>"
                    <?php else: ?>
                        Search Results
                    <?php endif; ?>
                </h2>
                
                <div class="results-info">
                    <span class="results-count"><?= $resultCount ?> <?= $resultCount == 1 ? 'result' : 'results' ?></span>
                    
                    <div class="sort-options">
                        <label for="sort">Sort by:</label>
                        <select id="sort" name="sort" class="sort-select" form="search-form" onchange="document.getElementById('search-form').submit()">
                            <option value="recent" <?= $sortBy == 'recent' ? 'selected' : '' ?>>Most Recent</option>
                            <option value="likes" <?= $sortBy == 'likes' ? 'selected' : '' ?>>Most Likes</option>
                            <option value="oldest" <?= $sortBy == 'oldest' ? 'selected' : '' ?>>Oldest First</option>
                        </select>
                    </div>
                </div>
            </div>

            <?php if (!empty($results)): ?>
                <div class="search-results">
                    <?php foreach ($results as $post): ?>
                        <article class="search-result">
                            <div class="result-header">
                                <h3 class="result-title">
                                    <a href="post.php?id=<?= $post['post_id'] ?>"><?= htmlspecialchars($post['title']) ?></a>
                                    <?php if (!empty($post['topic_name'])): ?>
                                        <span class="result-topic"><?= htmlspecialchars($post['topic_name']) ?></span>
                                    <?php endif; ?>
                                </h3>
                            </div>
                            
                            <p class="result-excerpt"><?= nl2br(htmlspecialchars(createExcerpt($post['content']))) ?></p>
                            
                            <div class="result-meta">
                                <img 
                                    src="<?= htmlspecialchars($post['pfp'] ?? '../assets/profile-icon.png') ?>" 
                                    alt="Author" 
                                    class="user-avatar"
                                >
                                <div class="user-info">
                                    Posted by <a href="profile.php?username=<?= urlencode($post['author_username']) ?>"><?= htmlspecialchars($post['author_username']) ?></a> 
                                    on <?= formatDate($post['created_at']) ?>
                                </div>
                                
                                <div class="result-stats">
                                    <div class="result-stat">
                                        <img src="../assets/like.png" alt="Likes" class="result-stat-icon">
                                        <span><?= (int)$post['like_count'] ?></span>
                                    </div>
                                    
                                    <?php if (!empty($post['image_path'])): ?>
                                        <div class="result-stat">
                                            <img src="../assets/image-icon.png" alt="Has image" class="result-stat-icon">
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-results">
                    <h3>No results found</h3>
                    <p>Try different keywords or filters</p>
                </div>
                
                <div class="search-tips">
                    <h3>Search Tips</h3>
                    <ul>
                        <li>Check your spelling</li>
                        <li>Try more general keywords</li>
                        <li>Try fewer or different filters</li>
                        <li>Search for related topics</li>
                    </ul>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <a href="feed.php" class="back-link">← Back to Feed</a>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleFilters = document.getElementById('toggle-filters');
            const advancedSearch = document.getElementById('advanced-search');
            const resetFilters = document.getElementById('reset-filters');
            
            toggleFilters.addEventListener('click', function() {
                advancedSearch.style.display = advancedSearch.style.display === 'none' ? 'block' : 'none';
            });
            
            resetFilters.addEventListener('click', function() {
                // Reset all form fields except the search query
                document.querySelectorAll('#search-form input:not([name="q"]), #search-form select').forEach(input => {
                    if (input.type === 'checkbox') {
                        input.checked = false;
                    } else {
                        input.value = '';
                    }
                });
                
                // Submit the form to apply the reset
                document.getElementById('search-form').submit();
            });
        });
    </script>
</body>
</html>