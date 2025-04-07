<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'buyer') {
    header('HTTP/1.1 403 Forbidden');
    exit();
}

$inquiry_id = $_GET['inquiry_id'] ?? 0;

// Verify the inquiry belongs to the user
$stmt = $pdo->prepare("
    SELECT i.*, p.title as property_title 
    FROM inquiries i 
    JOIN properties p ON i.property_id = p.id 
    WHERE i.id = ? AND i.user_id = ?
");
$stmt->execute([$inquiry_id, $_SESSION['user_id']]);
$inquiry = $stmt->fetch();

if (!$inquiry) {
    echo json_encode(['error' => 'Inquiry not found']);
    exit();
}

// Get all messages including replies
$stmt = $pdo->prepare("
    SELECT 
        r.message,
        r.created_at,
        r.is_buyer_reply,
        u.name as sender_name,
        'reply' as type
    FROM inquiry_replies r
    LEFT JOIN users u ON CASE 
        WHEN r.is_buyer_reply = 1 THEN r.user_id = u.id
        ELSE r.admin_id = u.id
    END
    WHERE r.inquiry_id = ?
    ORDER BY r.created_at ASC
");
$stmt->execute([$inquiry_id]);
$messages = $stmt->fetchAll();

// Add the original inquiry as first message
array_unshift($messages, [
    'message' => $inquiry['message'],
    'created_at' => $inquiry['created_at'],
    'is_buyer_reply' => true,
    'sender_name' => $_SESSION['name'],
    'type' => 'inquiry'
]);

echo json_encode([
    'success' => true,
    'property_title' => $inquiry['property_title'],
    'messages' => $messages
]);