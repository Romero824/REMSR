<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    exit();
}

$inquiry_id = $_GET['inquiry_id'] ?? 0;

// Get inquiry details
$stmt = $pdo->prepare("
    SELECT i.*, p.title as property_title, u.name as buyer_name
    FROM inquiries i
    JOIN properties p ON i.property_id = p.id
    JOIN users u ON i.user_id = u.id
    WHERE i.id = ?
");
$stmt->execute([$inquiry_id]);
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
        CASE 
            WHEN r.is_buyer_reply = 1 THEN u.name
            ELSE a.name
        END as sender_name
    FROM inquiry_replies r
    LEFT JOIN users u ON r.user_id = u.id
    LEFT JOIN users a ON r.admin_id = a.id
    WHERE r.inquiry_id = ?
    ORDER BY r.created_at ASC
");
$stmt->execute([$inquiry_id]);
$replies = $stmt->fetchAll();

// Add the original inquiry as first message
$messages = array_merge(
    [[
        'message' => $inquiry['message'],
        'created_at' => $inquiry['created_at'],
        'is_buyer_reply' => true,
        'sender_name' => $inquiry['buyer_name']
    ]],
    $replies
);

// Update status to read if it's new
if ($inquiry['status'] === 'new') {
    $stmt = $pdo->prepare("UPDATE inquiries SET status = 'read' WHERE id = ?");
    $stmt->execute([$inquiry_id]);
}

echo json_encode([
    'success' => true,
    'property_title' => $inquiry['property_title'],
    'buyer_name' => $inquiry['buyer_name'],
    'messages' => $messages
]);