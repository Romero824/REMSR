<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inquiry_id = $_POST['inquiry_id'] ?? 0;
    $message = $_POST['message'] ?? '';

    if (empty($message)) {
        echo json_encode(['success' => false, 'message' => 'Message cannot be empty']);
        exit();
    }

    try {
        $pdo->beginTransaction();

        // Add admin reply with NULL user_id
        $stmt = $pdo->prepare("
            INSERT INTO inquiry_replies (inquiry_id, user_id, admin_id, message, is_buyer_reply) 
            VALUES (?, NULL, ?, ?, 0)
        ");
        $stmt->execute([$inquiry_id, $_SESSION['user_id'], $message]);

        // Update inquiry status
        $stmt = $pdo->prepare("UPDATE inquiries SET status = 'replied' WHERE id = ?");
        $stmt->execute([$inquiry_id]);

        $pdo->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}