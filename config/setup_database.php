<?php
require_once 'database.php';

try {
    // Read SQL file
    $sql = file_get_contents('database.sql');
    
    // Split SQL file into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    // Execute each statement
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $pdo->exec($statement);
        }
    }
    
    echo "Database tables created successfully!";
} catch (PDOException $e) {
    die("Error creating database tables: " . $e->getMessage());
}
?> 