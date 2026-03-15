-- Create database if it doesn't exist then select it
CREATE DATABASE IF NOT EXISTS freedomboard_db;
USE freedomboard_db;

-- Create users table for authentication
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Create posts table with foreign keys for users and nested replies
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    parent_id INT NULL DEFAULT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE    -- If user is deleted, their posts are deleted
    FOREIGN KEY (parent_id) REFERENCES posts(id) ON DELETE CASCADE  -- If parent post is deleted, all its replies are deleted
);
