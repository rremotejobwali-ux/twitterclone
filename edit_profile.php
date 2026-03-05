<?php
require_once 'db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle Form Submit
// Handle Form Submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $bio = trim($_POST['bio']);
    
    // Handle Avatar Upload
    $avatar_path = $user['avatar']; // Keep existing
    if (!empty($_FILES['avatar']['name'])) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $filename = 'avatar_' . $user_id . '_' . time() . '.' . $ext;
            $target_dir = __DIR__ . '/assets/uploads/avatars/';
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target_dir . $filename)) {
                $avatar_path = 'assets/uploads/avatars/' . $filename;
            }
        }
    }

    // Handle Cover Upload
    $cover_path = $user['cover_photo']; // Keep existing
    if (!empty($_FILES['cover_photo']['name'])) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($_FILES['cover_photo']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $filename = 'cover_' . $user_id . '_' . time() . '.' . $ext;
            $target_dir = __DIR__ . '/assets/uploads/covers/';
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
            if (move_uploaded_file($_FILES['cover_photo']['tmp_name'], $target_dir . $filename)) {
                $cover_path = 'assets/uploads/covers/' . $filename;
            }
        }
    }

    if ($full_name) {
        $stmt = $pdo->prepare("UPDATE users SET full_name = ?, bio = ?, avatar = ?, cover_photo = ? WHERE id = ?");
        $stmt->execute([$full_name, $bio, $avatar_path, $cover_path, $user_id]);
        
        // Update session avatar if needed
        $_SESSION['avatar'] = $avatar_path;
        
        header("Location: profile.php");
        exit;
    }
}

// Fetch Current Data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile / Twitter Clone</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <h1 class="auth-title">Edit Profile</h1>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label style="color: var(--text-secondary);">Full Name</label>
                    <input type="text" name="full_name" class="form-input" value="<?= htmlspecialchars($user['full_name']) ?>" required>
                </div>
                <div class="form-group">
                    <label style="color: var(--text-secondary);">Bio</label>
                    <textarea name="bio" class="form-input" style="resize: none; height: 100px;"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label style="color: var(--text-secondary);">Avatar</label>
                    <input type="file" name="avatar" class="form-input" accept="image/*">
                </div>
                <div class="form-group">
                     <label style="color: var(--text-secondary);">Cover Photo</label>
                     <input type="file" name="cover_photo" class="form-input" accept="image/*">
                </div>
                <button type="submit" class="btn-primary">Save</button>
            </form>
            <a href="profile.php" class="auth-link">Cancel</a>
        </div>
    </div>
</body>
</html>
