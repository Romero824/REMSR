<?php
try {
    $host = 'localhost';
    $dbname = 'remsr_db';
    $username = 'root';
    $password = '';

    // Create PDO connection
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");
    $pdo->exec("USE $dbname");

    // Create tables if they don't exist
    $tables = [
        "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            role ENUM('admin', 'buyer') NOT NULL DEFAULT 'buyer',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",

        "CREATE TABLE IF NOT EXISTS properties (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            type ENUM('house', 'apartment', 'condo') NOT NULL,
            price DECIMAL(10,2) NOT NULL,
            location VARCHAR(255) NOT NULL,
            description TEXT,
            image_path VARCHAR(255),
            bedrooms INT,
            bathrooms INT,
            area DECIMAL(10,2),
            status ENUM('for_sale', 'sold') NOT NULL DEFAULT 'for_sale',
            is_featured TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )",

        "CREATE TABLE IF NOT EXISTS favorites (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            property_id INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
            UNIQUE KEY unique_favorite (user_id, property_id)
        )"
    ];

    // Execute each table creation query
    foreach ($tables as $sql) {
        $pdo->exec($sql);
    }

    // Create default admin user if it doesn't exist
    $checkAdmin = $pdo->query("SELECT id FROM users WHERE role = 'admin' LIMIT 1");
    if (!$checkAdmin->fetch()) {
        $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute(['Administrator', 'admin@gmail.com', $adminPassword, 'admin']);
    }

} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?> 