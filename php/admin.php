<?php
require_once '../sql/db_connect.php';
$postnum = $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();
$usernum = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$commentnum = $pdo->query("SELECT COUNT(*) FROM comments")->fetchColumn();
$topicnum = $pdo->query("SELECT COUNT(*) FROM topics")->fetchColumn();


$roles = $pdo->query("SELECT role, COUNT(*) AS count FROM users GROUP BY role")
              ->fetchAll(PDO::FETCH_KEY_PAIR);
$userList = $pdo->query("SELECT username, role FROM users")->fetchAll();
$topicList = $pdo->query("SELECT topic_name FROM topics")->fetchAll(PDO::FETCH_COLUMN);
$commentData = $pdo->query("SELECT comment_id, user_id, post_id, created_at FROM comments ORDER BY created_at DESC LIMIT 10")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Bloggit</title>
  <link rel="stylesheet" href="../styles/main.css" />
  <link rel="stylesheet" href="../styles/admin.css" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    #dashboardChart, #usersChart, #topicsChart {
      max-width: 500px;
      max-height: 400px;
      margin: auto;
    }

.user-table {
  width: 100%;
  margin: 25px 0;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  border-collapse: separate;
  border-spacing: 0;
  background: white;
}

.user-table thead {
  background: #667eea;
}

.user-table th {
  color: white;
  padding: 15px;
  font-weight: 600;
  text-align: center; 
  letter-spacing: 0.5px;
  text-transform: uppercase;
  font-size: 0.85em;
}

.user-table td {
  padding: 15px;
  border-bottom: 1px solid #edf2f7;
  color: #2d3748;
  text-align: center; 
}

.user-table td:first-child {
  text-align: left;
}

.user-table tr:last-child td {
  border-bottom: none;
}

.user-actions {
  display: flex;
  gap: 8px;
  justify-content: center; 
}

    .action-btn {
      padding: 6px 12px;
      border: none;
      border-radius: 4px;
      font-size: 0.8em;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s;
    }
    
    .edit-btn {
      background-color: #ebf8ff;
      color: #3182ce;
    }
    
    .edit-btn:hover {
      background-color: #bee3f8;
    }
    
    .delete-btn {
      background-color: #fef0f0;
      color: #f56565;
    }
    
    .delete-btn:hover {
      background-color: #fed7d7;
    }
    
    .comment-table {
      width: 100%;
      border-collapse: collapse;
      margin: 25px 0;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    
    .comment-table thead {
      background-color: #667eea;
    }
    
    .comment-table th {
      color: white;
      padding: 12px 15px;
      text-align: left;
    }
    
    .comment-table td {
      padding: 12px 15px;
      border-bottom: 1px solid #e2e8f0;
    }
    
    .comment-table tbody tr:nth-child(even) {
      background-color: #f7fafc;
    }
    
    .comment-id {
      font-weight: 600;
      color: #4a5568;
    }
    
    .topics-list {
      list-style-type: none;
      padding: 0;
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 15px;
    }
    
    .topics-list li {
      background-color: #f8f9fa;
      padding: 12px;
      border-radius: 6px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      text-align: center;
      transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .topics-list li:hover {
      transform: translateY(-3px);
      box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
  </style>
</head>
<body>
  <div id="app">
    <div id="topnav"></div>
    <div id="navbar"></div>
    <main id="content">
      <div id="admin-board">
        <div id="userspfp-post">
          <img src="../assets/profile-icon.png" alt="User Profile" />
        </div>
        <h2>Admin Dashboard</h2>
      </div>

      <section class="admin-menu">
        <div class="button-grid">
          <button class="tab-btn active" data-tab="dashboard">Dashboard</button>
          <button class="tab-btn" data-tab="users">Users</button>
          <button class="tab-btn" data-tab="posts">Posts</button>
          <button class="tab-btn" data-tab="comments">Comments</button>
          <button class="tab-btn" data-tab="topics">Topics</button>
          <button class="tab-btn" data-tab="settings">Settings</button>
        </div>
      </section>

      <div id="dashboard" class="tab-content">
        <h2>Dashboard Overview</h2>
        <div class="stat-box">Total Users: <strong><?= $usernum ?></strong></div>
        <div class="stat-box">Total Posts: <strong><?= $postnum ?></strong></div>
        <div class="stat-box">Total Comments: <strong><?= $commentnum ?></strong></div>
        <div class="chart-container">
          <canvas id="dashboardChart"></canvas>
        </div>
      </div>

      <div id="users" class="tab-content" style="display: none;">
        <h2>User Management</h2>
        <div class="stat-box">Total Users: <strong><?= $usernum ?></strong></div>
        
        <div class="chart-container">
          <canvas id="usersChart"></canvas>
        </div>
        
        <h3>User List</h3>
        <table class="user-table">
          <thead>
            <tr>
              <th>Username</th>
              <th>Role</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($userList as $user): ?>
            <?php 
              $roleClass = '';
              switch(strtolower($user['role'])) {
                case 'admin': $roleClass = 'role-admin'; break;
                case 'moderator': $roleClass = 'role-moderator'; break;
                case 'guest': $roleClass = 'role-guest'; break;
                default: $roleClass = 'role-user';
              }
            ?>
            <tr>
              <td><?= htmlspecialchars($user['username']) ?></td>
              <td><span class="user-role <?= $roleClass ?>"><?= htmlspecialchars($user['role']) ?></span></td>
              <td>
                <div class="user-actions">
                  <button class="action-btn edit-btn">Edit</button>
                  <button class="action-btn delete-btn">Delete</button>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div id="posts" class="tab-content" style="display: none;">
        <h2>Post Analytics</h2>
        <div class="stat-box">Total Posts: <strong><?= $postnum ?></strong></div>
        <div class="stat-box">Posts Today: <strong>3</strong></div>
        <div class="stat-box">Most Liked Post: <strong>1</strong></div>
        <div class="chart-container">
          <canvas id="postsChart"></canvas>
        </div>
      </div>

      <div id="comments" class="tab-content" style="display: none;">
        <h2>Comment Moderation</h2>
        <div class="stat-box">Total Comments: <strong><?= $commentnum ?></strong></div>
        
        <h3>Recent Comments</h3>
        <table class="comment-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>User ID</th>
              <th>Post ID</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($commentData as $comment): ?>
            <tr>
              <td class="comment-id">#<?= htmlspecialchars($comment['comment_id']) ?></td>
              <td><?= htmlspecialchars($comment['user_id']) ?></td>
              <td><?= htmlspecialchars($comment['post_id']) ?></td>
              <td><?= htmlspecialchars($comment['created_at']) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div id="topics" class="tab-content" style="display: none;">
        <h2>Topic Overview</h2>
        <div class="stat-box">Total Topics: <strong><?= $topicnum ?></strong></div>
        
        <div class="chart-container">
          <canvas id="topicsChart"></canvas>
        </div>
        
        <h3>Topic List</h3>
        <ul class="topics-list">
          <?php foreach ($topicList as $topic): ?>
            <li><?= htmlspecialchars($topic) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div id="settings" class="tab-content" style="display: none;">
        <h2>Admin Settings</h2>
        <div class="admin-info">
          <p><strong>Name:</strong> John Doe</p>
          <p><strong>Admin ID:</strong> #123456</p>
          <p><strong>Email:</strong> johndoe@example.com</p>
          <p><strong>Country:</strong> United States</p>
          <p><strong>City:</strong> New York</p>
          <p><strong>Password:</strong> ********</p>
          <a href="edit-admin.html" class="edit-admin-link">
            <button class="prof-btn">Edit Account Info</button>
          </a>
        </div>
      </div>
    </main>
    <div id="footer"></div>
  </div>

  <script>
    const dashboardData = [<?= $usernum ?>, <?= $postnum ?>, <?= $commentnum ?>];
    const userRoleLabels = <?= json_encode(array_keys($roles)) ?>;
    const userRoleData = <?= json_encode(array_values($roles)) ?>;
    const topicLabels = <?= json_encode($topicList) ?>;
    const topicData = Array(<?= json_encode(count($topicList)) ?>).fill(1); // Just for visualization

    let chartInstances = {};

    function renderChart(canvasId, labels, data, type = 'pie') {
      const canvas = document.getElementById(canvasId);
      if (!canvas) return;

      if (chartInstances[canvasId]) {
        chartInstances[canvasId].destroy();
      }

      const ctx = canvas.getContext('2d');
      chartInstances[canvasId] = new Chart(ctx, {
        type: type,
        data: {
          labels: labels,
          datasets: [{
            label: 'Statistics',
            data: data,
            backgroundColor: [
              'rgba(75, 192, 192, 0.7)',
              'rgba(54, 162, 235, 0.7)',
              'rgba(255, 206, 86, 0.7)',
              'rgba(255, 99, 132, 0.7)',
              'rgba(153, 102, 255, 0.7)',
              'rgba(255, 159, 64, 0.7)',
              'rgba(199, 199, 199, 0.7)',
              'rgba(83, 102, 255, 0.7)',
              'rgba(170, 159, 64, 0.7)',
              'rgba(140, 199, 199, 0.7)'
            ],
            borderColor: [
              'rgba(75, 192, 192, 1)',
              'rgba(54, 162, 235, 1)',
              'rgba(255, 206, 86, 1)',
              'rgba(255, 99, 132, 1)',
              'rgba(153, 102, 255, 1)',
              'rgba(255, 159, 64, 1)',
              'rgba(199, 199, 199, 1)',
              'rgba(83, 102, 255, 1)',
              'rgba(170, 159, 64, 1)',
              'rgba(140, 199, 199, 1)'
            ],
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'top'
            }
          }
        }
      });
    }

    function showTab(tabId) {
      document.querySelectorAll('.tab-content').forEach(content => content.style.display = 'none');
      document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
      document.getElementById(tabId).style.display = 'block';
      document.querySelector(`.tab-btn[data-tab="${tabId}"]`).classList.add('active');
      localStorage.setItem('adminActiveTab', tabId);

      switch (tabId) {
        case 'dashboard':
          renderChart('dashboardChart', ['Users', 'Posts', 'Comments'], dashboardData);
          break;
        case 'users':
          renderChart('usersChart', userRoleLabels, userRoleData, 'pie');
          break;
        case 'posts':
          renderChart('postsChart', ['Total Posts', 'Posts Today', 'Most Liked Post'], [<?= $postnum ?>, 3, 1]);
          break;
        case 'topics':
          renderChart('topicsChart', ['Topics'], [<?= $topicnum ?>]);
          break;
      }
    }

    document.querySelectorAll('.tab-btn').forEach(button => {
      button.addEventListener('click', function() {
        const tabId = this.getAttribute('data-tab');
        showTab(tabId);
      });
    });

    window.onload = () => {
      const activeTab = localStorage.getItem('adminActiveTab') || 'dashboard';
      showTab(activeTab);
    };

    document.addEventListener("DOMContentLoaded", function () {
      if (typeof checkUserLogin === "function") {
        checkAdmin();
      }
    });
  </script>
  <script src="../scripts/router.js"></script>
  <script src="../scripts/auth.js" defer></script>
</body>
</html>