<?php
require 'db.php';
try {
    $pdo->exec("UPDATE users SET avatar = 'assets/default_avatar.svg' WHERE avatar = 'assets/default_avatar.png'");
    echo "Updated existing users avatars.\n";
} catch (Exception $e) {
    echo "Error updating avatars: " . $e->getMessage() . "\n";
}
?>
