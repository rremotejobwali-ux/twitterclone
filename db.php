<?php
// db.php - Database Connection & Initialization

$host = 'localhost';
$dbname = 'rsk0_04';
$username = 'rsk0_04';
$password = '123456';

try {
    // Create (or connect to) MySQL database
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password);
    // Set error mode to exceptions
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Fetch results as associative arrays by default
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Create Database if it doesn't exist (if user has permissions)
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$dbname` ");

    // Create Tables if they don't exist
    $commands = [
        "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            full_name VARCHAR(100) NOT NULL,
            bio TEXT,
            avatar VARCHAR(255) DEFAULT 'assets/default_avatar.svg',
            cover_photo VARCHAR(255) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB",
        "CREATE TABLE IF NOT EXISTS tweets (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            content TEXT NOT NULL,
            image VARCHAR(255) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB",
        "CREATE TABLE IF NOT EXISTS follows (
            follower_id INT NOT NULL,
            following_id INT NOT NULL,
            PRIMARY KEY (follower_id, following_id),
            FOREIGN KEY (follower_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (following_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB",
        "CREATE TABLE IF NOT EXISTS likes (
            user_id INT NOT NULL,
            tweet_id INT NOT NULL,
            PRIMARY KEY (user_id, tweet_id),
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (tweet_id) REFERENCES tweets(id) ON DELETE CASCADE
        ) ENGINE=InnoDB"
    ];

    foreach ($commands as $command) {
        $pdo->exec($command);
    }
    
    // Attempt to add columns to existing tables if they were created before
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN cover_photo VARCHAR(255) DEFAULT NULL");
    } catch (Exception $e) { /* Column likely exists */ }
    
    try {
        $pdo->exec("ALTER TABLE tweets ADD COLUMN image VARCHAR(255) DEFAULT NULL");
    } catch (Exception $e) { /* Column likely exists */ }

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Start Session globally
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

