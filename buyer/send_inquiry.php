<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in and is a buyer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'buyer') {
    die(json_encode(['success' => false, 'message' => 'Please login to send an inquiry']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate required fields
        $required_fields = ['property_id', 'name', 'email', 'message'];
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("$field is required");
            }
        }

        // Validate email
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }

        // Insert inquiry into database
        $sql = "INSERT INTO inquiries (property_id, user_id, name, email, phone, message, status) 
                VALUES (?, ?, ?, ?, ?, ?, 'new')";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $_POST['property_id'],
            $_SESSION['user_id'],
            $_POST['name'],
            $_POST['email'],
            $_POST['phone'] ?? null,
            $_POST['message']
        ]);

        echo json_encode([
            'success' => true,
            'message' => 'Your inquiry has been sent successfully. The agent will contact you soon.'
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?> 