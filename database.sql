-- Database Name: rsk0_04
-- Database User: rsk0_04
-- Password: 123456

CREATE DATABASE IF NOT EXISTS `rsk0_04` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `rsk0_04`;

-- Users table
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) UNIQUE NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `bio` TEXT,
    `avatar` VARCHAR(255) DEFAULT 'assets/default_avatar.png',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tweets table
CREATE TABLE IF NOT EXISTS `tweets` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `content` TEXT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Follows table
CREATE TABLE IF NOT EXISTS `follows` (
    `follower_id` INT NOT NULL,
    `following_id` INT NOT NULL,
    PRIMARY KEY (`follower_id`, `following_id`),
    FOREIGN KEY (`follower_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`following_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Likes table
CREATE TABLE IF NOT EXISTS `likes` (
    `user_id` INT NOT NULL,
    `tweet_id` INT NOT NULL,
    PRIMARY KEY (`user_id`, `tweet_id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`tweet_id`) REFERENCES `tweets`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Sample Data
INSERT INTO `users` (`username`, `password_hash`, `full_name`, `bio`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin User', 'The architect of this clone.');

INSERT INTO `tweets` (`user_id`, `content`) VALUES
(1, 'Welcome to the Twitter Clone! Feel free to post your thoughts.');
