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

    -- admin table
    CREATE TABLE `Admin` (
        `admin_id` INT NOT NULL AUTO_INCREMENT,
        `user_id` INT NOT NULL,
        `country` VARCHAR(100),
        `city` VARCHAR(100),
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`admin_id`),
        FOREIGN KEY (`user_id`) REFERENCES `Users`(`user_id`) ON DELETE CASCADE,
        UNIQUE KEY `user_id` (`user_id`)
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
        `title` VARCHAR(255) NOT NULL,
        `content` TEXT NOT NULL,
        `image` VARCHAR(255),
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `user_id` INT,
        `topic_id` INT,
        `status` enum('draft','posted') DEFAULT 'posted',
        `image_path` varchar(255) DEFAULT NULL,
        `username` varchar(255) NOT NULL,
        `likes` INT DEFAULT 0,
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
    ('admin', 'Admin', 'User', 'admin@bloggit.com', '$2y$10$gFQ1xKnrUJ..c.xx8LY8bOeDBVAPYgU6zqnVnXA4Jc7ugXD7pD2jq', 'Administrator', 'admin'),
    ('ExampleUser123', 'Example', 'User', 'user@example.com', '$2y$10$rbq/gVN.5bnKDLmpWco7/.GG3qtu6y/2qS2mYld9IdfRgdXxCxCzC', 'I am a 3rd year computer science student in UBC.', 'user');


    -- insert admin 
    INSERT INTO `Admin` (`user_id`, `country`, `city`)
    VALUES (
        (SELECT user_id FROM users WHERE username = 'admin'),
        'United States',
        'New York'
    );

    INSERT INTO `topics` (`topic_id`, `topic_name`) VALUES
    (1, 'Programming'),
    (2, 'Boba'),
    (3, 'Gaming'),
    (4, 'Music');


    -- Create database user (run as admin)
    DROP USER IF EXISTS 'webuser'@'localhost';
    CREATE USER 'webuser'@'localhost' IDENTIFIED BY 'P@ssw0rd';
    GRANT ALL PRIVILEGES ON bloggit_db.* TO 'webuser'@'localhost';
    FLUSH PRIVILEGES;
