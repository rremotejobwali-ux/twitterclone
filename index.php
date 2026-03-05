<?php
require_once 'db.php';
$user_id = $_SESSION['user_id'] ?? null;

// Fetch Tweets (Global Feed for now)
$stmt = $pdo->prepare("
    SELECT t.*, u.username, u.full_name, u.avatar,
    (SELECT COUNT(*) FROM likes WHERE tweet_id = t.id) as like_count,
    (SELECT COUNT(*) FROM likes WHERE tweet_id = t.id AND user_id = :uid) as user_liked
    FROM tweets t
    JOIN users u ON t.user_id = u.id
    ORDER BY t.created_at DESC
    LIMIT 50
");
$stmt->execute([':uid' => $user_id]);
$tweets = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home / Twitter Clone</title>
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
            <?php if ($user_id): ?>
            <a href="profile.php?id=<?= $user_id ?>" class="nav-link">
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

        <!-- Main Content (Feed) -->
        <div class="main-content">
            <div class="header">
                <h2>Home</h2>
            </div>
            
            <?php if ($user_id): ?>
            <!-- Tweet Box -->
            <div class="tweet-box">
                <img src="<?= $_SESSION['avatar'] ?? 'assets/default_avatar.svg' ?>" onerror="this.src='assets/default_avatar.svg'" alt="Avatar" class="avatar">
                <div class="tweet-box-content">
                    <form id="tweetForm" enctype="multipart/form-data">
                        <textarea name="content" class="tweet-textarea" placeholder="What is happening?!" required maxlength="280"></textarea>
                        
                        <div id="mediaPreview" style="display: none; position: relative; margin-bottom: 10px;">
                            <img id="previewImg" src="" style="width: 100%; border-radius: 15px; max-height: 300px; object-fit: cover;">
                            <button type="button" id="removeMedia" style="position: absolute; top: 10px; right: 10px; background: rgba(0,0,0,0.7); color: white; border: none; border-radius: 50%; width: 30px; height: 30px; cursor: pointer;">&times;</button>
                        </div>

                        <div class="tweet-actions" style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <label for="tweetImage" style="cursor: pointer; font-size: 1.2rem; color: var(--accent-color); padding: 5px;">
                                    📷
                                </label>
                                <input type="file" id="tweetImage" name="image" accept="image/*" style="display: none;">
                            </div>
                            <button type="submit" class="btn-primary btn-accent" style="width: auto;">Post</button>
                        </div>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <!-- Feed -->
            <div id="feed">
                <?php foreach ($tweets as $tweet): ?>
                <div class="tweet" data-id="<?= $tweet['id'] ?>">
                    <img src="<?= htmlspecialchars($tweet['avatar']) ?>" onerror="this.src='assets/default_avatar.svg'" alt="Avatar" class="avatar" style="margin-right: 15px;">
                    <div class="tweet-content-wrapper" style="flex: 1;">
                        <div class="tweet-header">
                            <span class="username"><?= htmlspecialchars($tweet['full_name']) ?></span>
                            <span class="handle">@<?= htmlspecialchars($tweet['username']) ?></span>
                            <span class="time">· <?= date('M d', strtotime($tweet['created_at'])) ?></span>
                            <?php if ($user_id == $tweet['user_id']): ?>
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

        <!-- Sidebar Right -->
        <div class="sidebar-right">
            <!-- Search or Trends could go here -->
        </div>
    </div>

    <?php if ($user_id): ?>
    <script src="assets/js/main.js"></script>
    <script>
        // Pass user info to JS if needed
        const currentUser = {
            id: <?= $user_id ?>,
            username: "<?= $_SESSION['username'] ?? '' ?>"
        };
    </script>
    <?php endif; ?>
</body>
</html>
