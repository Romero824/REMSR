<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die(json_encode(['success' => false, 'message' => 'Unauthorized access']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate required fields
        $required_fields = ['title', 'type', 'price', 'location'];
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("$field is required");
            }
        }

        // Handle image upload
        $image_path = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../uploads/properties/';
            
            // Create directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // Validate file type
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($_FILES['image']['type'], $allowed_types)) {
                throw new Exception("Invalid file type. Only JPG, PNG and GIF are allowed.");
            }

            // Generate unique filename
            $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $filename = uniqid() . '.' . $file_extension;
            $target_path = $upload_dir . $filename;

            // Move uploaded file
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                $image_path = 'uploads/properties/' . $filename;
            } else {
                throw new Exception("Failed to upload image");
            }
        }

        // Set default values for status and featured
        $status = 'for_sale';
        $is_featured = 1;

        // Prepare and execute SQL statement
        $sql = "INSERT INTO properties (title, type, price, location, description, image_path, bedrooms, bathrooms, area, status, is_featured, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $_POST['title'],
            $_POST['type'],
            $_POST['price'],
            $_POST['location'],
            $_POST['description'] ?? null,
            $image_path,
            $_POST['bedrooms'] ?? null,
            $_POST['bathrooms'] ?? null,
            $_POST['area'] ?? null,
            $status,
            $is_featured
        ]);

        $property_id = $pdo->lastInsertId();

        echo json_encode([
            'success' => true,
            'message' => 'Property added successfully and is now available in the buyer dashboard',
            'property_id' => $property_id
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