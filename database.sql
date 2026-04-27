-- FounderBrain Database | Run in phpMyAdmin
CREATE DATABASE IF NOT EXISTS `founderbrain` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `founderbrain`;

CREATE TABLE IF NOT EXISTS `users` (
  `id`         INT AUTO_INCREMENT PRIMARY KEY,
  `username`   VARCHAR(100) NOT NULL UNIQUE,
  `password`   VARCHAR(255) NOT NULL,
  `name`       VARCHAR(150) NOT NULL DEFAULT 'Huzaifa',
  `email`      VARCHAR(200) DEFAULT '',
  `google_id`  VARCHAR(200) DEFAULT NULL,
  `picture`    TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Default login: huzaifa / 12345678
INSERT IGNORE INTO `users` (`username`,`password`,`name`,`email`) VALUES
('huzaifa','$2y$10$TZx2vCRASZkiS5npCqm96OocJ5qxccJmBrA02A0JEAucKycA9k6OW','Huzaifa','huzaifa@founderbrain.chat');

CREATE TABLE IF NOT EXISTS `oauth_tokens` (
  `id`            INT AUTO_INCREMENT PRIMARY KEY,
  `user_id`       INT NOT NULL UNIQUE,
  `access_token`  TEXT NOT NULL,
  `refresh_token` TEXT,
  `expires_at`    INT,
  `google_email`  VARCHAR(200),
  `updated_at`    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `tasks` (
  `id`         INT AUTO_INCREMENT PRIMARY KEY,
  `user_id`    INT NOT NULL,
  `title`      VARCHAR(500) NOT NULL,
  `action`     TEXT,
  `category`   VARCHAR(50) DEFAULT 'admin',
  `priority`   ENUM('high','medium','low') DEFAULT 'medium',
  `deadline`   VARCHAR(100) DEFAULT 'no-deadline',
  `done`       TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `briefings` (
  `id`         INT AUTO_INCREMENT PRIMARY KEY,
  `user_id`    INT NOT NULL,
  `content`    TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `followup_log` (
  `id`           INT AUTO_INCREMENT PRIMARY KEY,
  `user_id`      INT NOT NULL,
  `email_id`     VARCHAR(200),
  `to_email`     VARCHAR(200),
  `subject`      VARCHAR(500),
  `body`         TEXT,
  `sent_at`      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `activity_log` (
  `id`         INT AUTO_INCREMENT PRIMARY KEY,
  `user_id`    INT NOT NULL,
  `action`     VARCHAR(200) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;
