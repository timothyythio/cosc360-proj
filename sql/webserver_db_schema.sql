
USE timnthio;

DROP TABLE IF EXISTS `Comments`;
DROP TABLE IF EXISTS `comments`;

DROP TABLE IF EXISTS `Drafts`;
DROP TABLE IF EXISTS `Likes`;
DROP TABLE IF EXISTS `Saved`;
DROP TABLE IF EXISTS `Admin`;
DROP TABLE IF EXISTS `Posts`;
DROP TABLE IF EXISTS `Topic_Followers`;

DROP TABLE IF EXISTS `Topics`;
DROP TABLE IF EXISTS `Users`;

-- Set character encoding
SET NAMES utf8mb4;
SET character_set_client = utf8mb4;
SET character_set_connection = utf8mb4;
SET character_set_results = utf8mb4;

-- Create Users table
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
    `topic_img` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`topic_id`),
    UNIQUE KEY `topic_name` (`topic_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `Topic_Followers` (
    `follow_id` INT NOT NULL AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `topic_id` INT NOT NULL,
    `followed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`follow_id`),
    FOREIGN KEY (`user_id`) REFERENCES `Users`(`user_id`) ON DELETE CASCADE,
    FOREIGN KEY (`topic_id`) REFERENCES `Topics`(`topic_id`) ON DELETE CASCADE,
    UNIQUE KEY `user_topic_unique` (`user_id`, `topic_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- update topics table for member count
ALTER TABLE `Topics` ADD COLUMN `members_count_updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

-- Create posts table
CREATE TABLE `Posts` (
    `post_id` INT NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `content` TEXT NOT NULL,
    `image` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `user_id` INT DEFAULT NULL,
    `topic_id` INT DEFAULT NULL,
    `status` enum('draft','posted') DEFAULT 'posted',
    `image_path` varchar(255) DEFAULT NULL,
    `username` varchar(255) NOT NULL,
    `likes` INT DEFAULT 0,
    PRIMARY KEY (`post_id`),
    FOREIGN KEY (`user_id`) REFERENCES `Users`(`user_id`) ON DELETE SET NULL,
    FOREIGN KEY (`topic_id`) REFERENCES `Topics`(`topic_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `Drafts` (
    `draft_id` INT NOT NULL AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `username` VARCHAR(255) NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `content` TEXT,
    `saved_content` TEXT,
    `image_path` VARCHAR(255),
    `topic_id` INT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`draft_id`),
    FOREIGN KEY (`user_id`) REFERENCES `Users`(`user_id`) ON DELETE CASCADE,
    FOREIGN KEY (`topic_id`) REFERENCES `Topics`(`topic_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `Saved` (
    `save_id` INT NOT NULL AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `post_id` INT NOT NULL,
    `saved_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`save_id`),
    FOREIGN KEY (`user_id`) REFERENCES `Users`(`user_id`) ON DELETE CASCADE,
    FOREIGN KEY (`post_id`) REFERENCES `Posts`(`post_id`) ON DELETE CASCADE,
    UNIQUE KEY `user_post_unique` (`user_id`, `post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `Likes` (
    `like_id` INT NOT NULL AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `post_id` INT NOT NULL,
    `liked_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`like_id`),
    FOREIGN KEY (`user_id`) REFERENCES `Users`(`user_id`) ON DELETE CASCADE,
    FOREIGN KEY (`post_id`) REFERENCES `Posts`(`post_id`) ON DELETE CASCADE,
    UNIQUE KEY `user_post_unique_like` (`user_id`, `post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `Comments` (
    `comment_id` INT NOT NULL AUTO_INCREMENT,
    `content` TEXT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `user_id` INT DEFAULT NULL,
    `post_id` INT,
    `parent_comment_id` INT DEFAULT NULL,
    PRIMARY KEY (`comment_id`),
    FOREIGN KEY (`user_id`) REFERENCES `Users`(`user_id`) ON DELETE SET NULL,
    FOREIGN KEY (`post_id`) REFERENCES `Posts`(`post_id`) ON DELETE CASCADE,
    FOREIGN KEY (`parent_comment_id`) REFERENCES `Comments`(`comment_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample Users
INSERT INTO `Users` (`username`, `first_name`, `last_name`, `email`, `password`, `bio`, `role`, `pfp`) VALUES
('admin', 'Admin', 'User', 'admin@bloggit.com', '$2y$10$74OUtU4vAHIHwM4AqFChsuTUMsm5cNqqCiAVQ7XTazylNILIvkVG6', 'Administrator', 'admin', '../assets/profile-icon.png'),
('exampleuser123', 'Example', 'User', 'user@example.com', '$2y$10$o4Vg./VSTfktdQrSXC9AeOpigfRsPAp3AmjKhd8HPerUJmpcvOKWi', 'I am a 3rd year computer science student in UBC.', 'user', '../assets/profile-icon.png'),
('janedoe', 'Jane', 'Doe', 'jane@gmail.com', '$2y$10$o4Vg./VSTfktdQrSXC9AeOpigfRsPAp3AmjKhd8HPerUJmpcvOKWi', 'I am a 2nd year computer science student in UBC.', 'user', '../assets/profile-icon.png'),
('johndoe', 'John', 'Doe', 'johndoe@gmail.com', '$2y$10$o4Vg./VSTfktdQrSXC9AeOpigfRsPAp3AmjKhd8HPerUJmpcvOKWi', 'I am also a 3rd year computer science student in UBC.', 'user', '../assets/profile-icon.png');

-- insert admin 
INSERT INTO `Admin` (`user_id`, `country`, `city`)
VALUES (
    (SELECT user_id FROM Users WHERE username = 'admin'),
    'United States',
    'New York'
);

-- Insert sample topics
INSERT INTO `Topics` (`topic_id`, `topic_name`) VALUES
(1, 'Programming'),
(2, 'Boba'),
(3, 'Gaming'),
(4, 'Music'),
(5, 'Cats'),
(6, 'Drawing'),
(7, 'Spoons');

UPDATE `Topics` 
SET 
    `description` = CASE 
        WHEN `topic_id` = 1 THEN 'Share programming tips, resources, and questions'
        WHEN `topic_id` = 2 THEN 'Discuss bubble tea flavors, shops, and recipes'
        WHEN `topic_id` = 3 THEN 'All about video games, from casual to competitive'
        WHEN `topic_id` = 4 THEN 'Share your favorite songs, artists, and genres'
        WHEN `topic_id` = 5 THEN 'For cat lovers and cat memes'
        WHEN `topic_id` = 6 THEN 'Share your artwork and drawing tips'
        WHEN `topic_id` = 7 THEN 'All things spoon-related'
    END,
    `topic_img` = CASE 
        WHEN `topic_id` = 1 THEN 'programming.jpg'
        WHEN `topic_id` = 2 THEN 'boba.jpg'
        WHEN `topic_id` = 3 THEN 'gaming.png'
        WHEN `topic_id` = 4 THEN 'music.jpg'
        WHEN `topic_id` = 5 THEN 'cat.jpg'
        WHEN `topic_id` = 6 THEN 'drawing.jpg'
        WHEN `topic_id` = 7 THEN 'spoon.jpg'
    END
WHERE `topic_id` BETWEEN 1 AND 7;

-- Insert sample posts

INSERT INTO `Posts` (`title`, `content`, `image`, `user_id`, `topic_id`, `status`, `image_path`, `username`, `likes`) VALUES
('Why I Love Programming', 'Programming helps me solve real-world problems.', NULL, (SELECT user_id FROM Users WHERE username = 'exampleuser123'), 1, 'posted', NULL, 'exampleuser123', 3),
('Best Boba Spots in Town', 'I tried 5 shops—here’s my verdict.', NULL, (SELECT user_id FROM Users WHERE username = 'janedoe'), 2, 'posted', NULL, 'janedoe', 5),
('Speedrunning Skyrim', 'My personal record is under 1 hour!', NULL, (SELECT user_id FROM Users WHERE username = 'johndoe'), 3, 'posted', NULL, 'johndoe', 8);

-- Insert sample drafts

INSERT INTO `Drafts` (`user_id`, `username`, `title`, `content`, `saved_content`, `image_path`, `topic_id`)
VALUES
((SELECT user_id FROM Users WHERE username = 'exampleuser123'), 'exampleuser123', 'The Joy of Debugging', '', 'Debugging can be satisfying when you finally squash that bug.', NULL, 1),
((SELECT user_id FROM Users WHERE username = 'exampleuser123'), 'exampleuser123', 'Top 5 Cat Memes', '', 'Here are the best memes from this week.', NULL, 5);

-- Insert sample comments

INSERT INTO `Comments` (`content`, `user_id`, `post_id`, `parent_comment_id`)
VALUES
('Great post! Really inspiring.', (SELECT user_id FROM Users WHERE username = 'exampleuser123'), (SELECT post_id FROM Posts WHERE title = 'Why I Love Programming'), NULL),
('Which boba spot was your favorite?', (SELECT user_id FROM Users WHERE username = 'exampleuser123'), (SELECT post_id FROM Posts WHERE title = 'Best Boba Spots in Town'), NULL),
('I liked T4 the best!', (SELECT user_id FROM Users WHERE username = 'exampleuser123'), (SELECT post_id FROM Posts WHERE title = 'Best Boba Spots in Town'), LAST_INSERT_ID());

-- Insert sample saved Posts

INSERT INTO `Saved` (`user_id`, `post_id`)
VALUES
((SELECT user_id FROM Users WHERE username = 'exampleuser123'), (SELECT post_id FROM Posts WHERE title = 'Why I Love Programming')),
((SELECT user_id FROM Users WHERE username = 'exampleuser123'), (SELECT post_id FROM Posts WHERE title = 'Best Boba Spots in Town'));

-- exampleuser likes janedoe's post "Best Boba Spots in Town"
INSERT INTO `Likes` (`user_id`, `post_id`)
VALUES (
    (SELECT user_id FROM Users WHERE username = 'exampleuser123'),
    (SELECT post_id FROM Posts WHERE title = 'Best Boba Spots in Town')
);

-- exampleuser likes johndoe's post "Speedrunning Skyrim"
INSERT INTO `Likes` (`user_id`, `post_id`)
VALUES (
    (SELECT user_id FROM Users WHERE username = 'exampleuser123'),
    (SELECT post_id FROM Posts WHERE title = 'Speedrunning Skyrim')
);

-- johndoe likes exampleuser's post "Why I Love Programming"
INSERT INTO `Likes` (`user_id`, `post_id`)
VALUES (
    (SELECT user_id FROM Users WHERE username = 'johndoe'),
    (SELECT post_id FROM Posts WHERE title = 'Why I Love Programming')
);


