<?php
require_once 'db.php';
$logged_in_user_id = $_SESSION['user_id'] ?? null;
$profile_id = $_GET['id'] ?? $logged_in_user_id;

if (!$profile_id) {
    header("Location: login.php");
    exit;
}

// Fetch Profile User
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$profile_id]);
$user = $stmt->fetch();

if (!$user) {
    die("User not found");
}

// Fetch Stats
$stmt = $pdo->prepare("SELECT COUNT(*) FROM follows WHERE following_id = ?");
$stmt->execute([$profile_id]);
$followers_count = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM follows WHERE follower_id = ?");
$stmt->execute([$profile_id]);
$following_count = $stmt->fetchColumn();

// Check if following
$is_following = false;
if ($logged_in_user_id && $logged_in_user_id != $profile_id) {
    $stmt = $pdo->prepare("SELECT 1 FROM follows WHERE follower_id = ? AND following_id = ?");
    $stmt->execute([$logged_in_user_id, $profile_id]);
    $is_following = $stmt->fetchColumn();
}

// Fetch User's Tweets
$stmt = $pdo->prepare("
    SELECT t.*, u.username, u.full_name, u.avatar,
    (SELECT COUNT(*) FROM likes WHERE tweet_id = t.id) as like_count,
    (SELECT COUNT(*) FROM likes WHERE tweet_id = t.id AND user_id = :uid) as user_liked
    FROM tweets t
    JOIN users u ON t.user_id = u.id
    WHERE t.user_id = :pid
    ORDER BY t.created_at DESC
");
$stmt->execute([':uid' => $logged_in_user_id, ':pid' => $profile_id]);
$tweets = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($user['full_name']) ?> (@<?= htmlspecialchars($user['username']) ?>) / Twitter Clone</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <!-- Sidebar Left -->
        <div class="sidebar-left">
            <div class="logo">X</div>
            <a href="index.php" class="nav-link">
                <span>🏠</span> <span>Home</span>
            </a>
            <?php if ($logged_in_user_id): ?>
            <a href="profile.php?id=<?= $logged_in_user_id ?>" class="nav-link" style="font-weight: bold;">
                <span>👤</span> <span>Profile</span>
            </a>
            <a href="logout.php" class="nav-link">
                <span>🚪</span> <span>Logout</span>
            </a>
            <?php else: ?>
            <a href="login.php" class="nav-link">
                <span>🔐</span> <span>Login</span>
            </a>
            <?php endif; ?>
        </div>

        <div class="main-content">
            <div class="header">
                <h2><?= htmlspecialchars($user['full_name']) ?></h2>
                <div style="font-size: 0.8rem; color: var(--text-secondary);"><?= count($tweets) ?> posts</div>
            </div>

            <div class="profile-header">
                <!-- Cover Image -->
                <?php $coverUrl = !empty($user['cover_photo']) ? htmlspecialchars($user['cover_photo']) : ''; ?>
                <div style="height: 200px; background-color: #333639; background-image: url('<?= $coverUrl ?>'); background-size: cover; background-position: center;"></div>
                
                <div style="padding: 15px; position: relative;">
                    <img src="<?= htmlspecialchars($user['avatar']) ?>" onerror="this.src='assets/default_avatar.svg'" alt="Avatar" class="avatar" style="width: 134px; height: 134px; border: 4px solid black; position: absolute; top: -67px;">
                    
                    <div style="display: flex; justify-content: flex-end;">
                        <?php if ($logged_in_user_id == $profile_id): ?>
                            <a href="edit_profile.php" class="btn-primary" style="width: auto; background: transparent; border: 1px solid var(--border-color); color: var(--text-primary);">Edit profile</a>
                        <?php elseif ($logged_in_user_id): ?>
                            <button id="followBtn" class="btn-primary <?= $is_following ? '' : 'btn-accent' ?>" style="width: auto;" data-id="<?= $profile_id ?>">
                                <?= $is_following ? 'Following' : 'Follow' ?>
                            </button>
                        <?php endif; ?>
                    </div>

                    <div class="profile-info" style="margin-top: 50px;">
                        <h2 style="margin: 0;"><?= htmlspecialchars($user['full_name']) ?></h2>
                        <div style="color: var(--text-secondary);">@<?= htmlspecialchars($user['username']) ?></div>
                        <div style="margin-top: 10px;"><?= nl2br(htmlspecialchars($user['bio'] ?? '')) ?></div>
                        <div style="margin-top: 10px; color: var(--text-secondary);">Joined <?= date('F Y', strtotime($user['created_at'])) ?></div>
                        
                        <div class="counts">
                            <span class="count"><strong><?= $following_count ?></strong> Following</span>
                            <span class="count"><strong><?= $followers_count ?></strong> Followers</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Tweets -->
            <div id="feed">
                <?php foreach ($tweets as $tweet): ?>
                <div class="tweet" data-id="<?= $tweet['id'] ?>">
                    <img src="<?= htmlspecialchars($tweet['avatar']) ?>" onerror="this.src='assets/default_avatar.svg'" alt="Avatar" class="avatar" style="margin-right: 15px;">
                    <div class="tweet-content-wrapper" style="flex: 1;">
                        <div class="tweet-header">
                            <span class="username"><?= htmlspecialchars($tweet['full_name']) ?></span>
                            <span class="handle">@<?= htmlspecialchars($tweet['username']) ?></span>
                            <span class="time">· <?= date('M d', strtotime($tweet['created_at'])) ?></span>
                            <?php if ($logged_in_user_id == $tweet['user_id']): ?>
                                <button class="delete-btn" style="margin-left: auto; background: none; border: none; color: #71767b; cursor: pointer;">🗑️</button>
                            <?php endif; ?>
                        </div>
                        <div class="tweet-content">
                            <?= nl2br(htmlspecialchars($tweet['content'])) ?>
                            <?php if (!empty($tweet['image'])): ?>
                            <div class="tweet-image" style="margin-top: 10px;">
                                <img src="<?= htmlspecialchars($tweet['image']) ?>" alt="Tweet Image" style="width: 100%; border-radius: 15px; border: 1px solid var(--border-color);">
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="tweet-footer">
                            <div class="tweet-action like-btn <?= $tweet['user_liked'] ? 'liked' : '' ?>" data-id="<?= $tweet['id'] ?>">
                                <span>❤</span> <span class="like-count"><?= $tweet['like_count'] ?></span>
                            </div>
                            <div class="tweet-action">
                                <span>💬</span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="sidebar-right"></div>
    </div>

    <script src="assets/js/main.js"></script>
    <script>
        const followBtn = document.getElementById('followBtn');
        if (followBtn) {
            followBtn.addEventListener('click', async () => {
                const userId = followBtn.dataset.id;
                const formData = new FormData();
                formData.append('action', 'toggle_follow');
                formData.append('user_id', userId);

                try {
                    const res = await fetch('actions/follow.php', { method: 'POST', body: formData });
                    const data = await res.json();
                    
                    if (data.success) {
                        if (data.following) {
                            followBtn.textContent = 'Following';
                            followBtn.classList.remove('btn-accent');
                        } else {
                            followBtn.textContent = 'Follow';
                            followBtn.classList.add('btn-accent');
                        }
                        // Ideally update follower count here too
                        location.reload(); // Quickest way to update counts
                    }
                } catch (err) {
                    console.error(err);
                }
            });
        }
    </script>
</body>
</html>
