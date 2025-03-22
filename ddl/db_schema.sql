    DROP DATABASE IF EXISTS bloggit_db;
    CREATE DATABASE bloggit_db;
    USE bloggit_db;

    -- Set character encoding
    SET NAMES utf8mb4;
    SET character_set_client = utf8mb4;
    SET character_set_connection = utf8mb4;
    SET character_set_results = utf8mb4;

    -- Create users table
    CREATE TABLE `Users` (
        `user_id` INT NOT NULL AUTO_INCREMENT,
        `username` VARCHAR(50) NOT NULL,
        `first_name` VARCHAR(50) NOT NULL,
        `last_name` VARCHAR(50) NOT NULL,
        `email` VARCHAR(100) NOT NULL,
        `password` VARCHAR(255) NOT NULL,
        `pfp` VARCHAR(255) DEFAULT 'default-profile.png',
        `bio` TEXT,
        `role` ENUM('user', 'admin') DEFAULT 'user',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`user_id`),
        UNIQUE KEY `username` (`username`),
        UNIQUE KEY `email` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


    -- Create topics table
    CREATE TABLE `Topics` (
        `topic_id` INT NOT NULL AUTO_INCREMENT,
        `topic_name` VARCHAR(100) NOT NULL,
        `description` TEXT,
        `members` INT DEFAULT 0,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`topic_id`),
        UNIQUE KEY `topic_name` (`topic_name`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    -- Create posts table
    CREATE TABLE `Posts` (
        `post_id` INT NOT NULL AUTO_INCREMENT,
        `post_name` VARCHAR(255) NOT NULL,
        `content` TEXT NOT NULL,
        `image` VARCHAR(255),
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `user_id` INT,
        `topic_id` INT,
        PRIMARY KEY (`post_id`),
        FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE SET NULL,
        FOREIGN KEY (`topic_id`) REFERENCES `topics`(`topic_id`) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE `comments` (
        `comment_id` INT NOT NULL AUTO_INCREMENT,
        `content` TEXT NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `user_id` INT,
        `post_id` INT,
        `parent_comment_id` INT DEFAULT NULL,
        PRIMARY KEY (`comment_id`),
        FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE SET NULL,
        FOREIGN KEY (`post_id`) REFERENCES `posts`(`post_id`) ON DELETE CASCADE,
        FOREIGN KEY (`parent_comment_id`) REFERENCES `comments`(`comment_id`) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    -- Insert sample data
    INSERT INTO `users` (`username`, `first_name`, `last_name`, `email`, `password`, `bio`, `role`) VALUES
    ('admin', 'Admin', 'User', 'admin@bloggit.com', 'Admin123', 'Administrator', 'admin'),
    ('ExampleUser123', 'Example', 'User', 'user@example.com', 'User123', 'I am a 3rd year computer science student in UBC.', 'user');

    -- Create database user (run as admin)
    DROP USER IF EXISTS 'bloggit_user'@'localhost';
    CREATE USER 'bloggit_user'@'localhost' IDENTIFIED BY 'secure_password_here';
    GRANT SELECT, INSERT, UPDATE, DELETE ON bloggit_db.* TO 'bloggit_user'@'localhost';
    FLUSH PRIVILEGES;