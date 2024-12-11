<?php

$host = "localhost";
$username = "root";
$password = "";
$dbname = "portfolioDB";



try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    

    // Create Database
    $sql = "CREATE DATABASE IF NOT EXISTS portfolioDB;";
    $pdo->exec($sql);

    // Use Database
    $sql = "USE portfolioDB;";
    $pdo->exec($sql);

    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        public_id VARCHAR(32) NOT NULL,  -- Change to VARCHAR(32)
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );";
    
    $pdo->exec($sql);

    
    $sql = "CREATE TABLE IF NOT EXISTS landingpage (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(255) DEFAULT NULL,
        navbrand VARCHAR(255) DEFAULT NULL,
        name VARCHAR(255) DEFAULT NULL,
        body VARCHAR(255) DEFAULT NULL,
        button VARCHAR(255) DEFAULT NULL,
        image_path VARCHAR(255) DEFAULT NULL,
        birthdate DATE DEFAULT NULL,
        hobbies TEXT DEFAULT NULL,
        aboutme TEXT DEFAULT NULL,
        college VARCHAR(255) DEFAULT NULL,
        degree VARCHAR(255) DEFAULT NULL,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );";
    $pdo->exec($sql);

    // Create skills table
    $sql = "CREATE TABLE IF NOT EXISTS skills (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        skill_name VARCHAR(255) NOT NULL,
        image_skills VARCHAR(255) NOT NULL,
        about_skill TEXT NOT NULL,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );";
    $pdo->exec($sql);

    // Create social table
    $sql = "CREATE TABLE IF NOT EXISTS social (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        fb TEXT DEFAULT NULL,
        github TEXT DEFAULT NULL,
        linked TEXT DEFAULT NULL,
        email VARCHAR(255) DEFAULT NULL,
        tg TEXT DEFAULT NULL,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );";
    $pdo->exec($sql);

    // Create contact_messages table
    $sql = "CREATE TABLE IF NOT EXISTS contact_messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        read_status TINYINT(1) DEFAULT 0,
        is_deleted TINYINT(1) DEFAULT 0,
        user_id INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );";
    $pdo->exec($sql);

    $sql = "ALTER TABLE users MODIFY COLUMN public_id VARCHAR(32);";
    $pdo->exec($sql);



} catch (PDOException $e) {
 
    error_log("Database error: " . $e->getMessage(), 3, 'errors.log');
    die("An error occurred. Please try again later.");
}



?>
