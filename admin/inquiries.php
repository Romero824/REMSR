<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Get admin information
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'admin'");
$stmt->execute([$_SESSION['user_id']]);
$admin = $stmt->fetch();

// Get all inquiries with buyer information
$stmt = $pdo->prepare("
    SELECT 
        i.*,
        p.title as property_title,
        u.name as buyer_name,
        (SELECT COUNT(*) FROM inquiry_replies WHERE inquiry_id = i.id) as reply_count
    FROM inquiries i
    JOIN properties p ON i.property_id = p.id
    JOIN users u ON i.user_id = u.id
    ORDER BY 
        CASE 
            WHEN i.status = 'replied_by_buyer' THEN 1
            WHEN i.status = 'new' THEN 2
            ELSE 3
        END,
        i.created_at DESC
");
$stmt->execute();
$inquiries = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Inquiries - REMSR Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .chat-area {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
        }
        .message {
            margin-bottom: 1rem;
            padding: 1rem;
            border-radius: 8px;
        }
        .buyer-message {
            background: #e3f2fd;
            margin-right: 20%;
        }
        .admin-message {
            background: #f5f5f5;
            margin-left: 20%;
        }
        .inquiry-item {
            border-left: 3px solid transparent;
        }
        .inquiry-item.active {
            border-left-color: #0d6efd;
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Property Inquiries</h1>
                    <a href="dashboard.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
                </div>

                <div class="row">
                    <!-- Inquiries List -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush">
                                    <?php foreach ($inquiries as $inquiry): ?>
                                        <a href="#" class="list-group-item list-group-item-action inquiry-item" 
                                           data-inquiry-id="<?php echo $inquiry['id']; ?>"
                                           onclick="loadChat(<?php echo $inquiry['id']; ?>)">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1"><?php echo htmlspecialchars($inquiry['buyer_name']); ?></h6>
                                                <small class="text-muted">
                                                    <?php echo date('M d', strtotime($inquiry['created_at'])); ?>
                                                </small>
                                            </div>
                                            <p class="mb-1 text-truncate"><?php echo htmlspecialchars($inquiry['property_title']); ?></p>
                                            <small>
                                                <span class="badge bg-<?php 
                                                    echo $inquiry['status'] === 'new' ? 'danger' : 
                                                        ($inquiry['status'] === 'replied_by_buyer' ? 'warning' : 'success'); 
                                                ?>">
                                                    <?php echo ucfirst(str_replace('_', ' ', $inquiry['status'])); ?>
                                                </span>
                                                <?php if ($inquiry['reply_count'] > 0): ?>
                                                    <span class="badge bg-info ms-1"><?php echo $inquiry['reply_count']; ?> replies</span>
                                                <?php endif; ?>
                                            </small>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Chat Area -->
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <div id="chatArea" class="chat-area mb-3" style="height: 400px; overflow-y: auto;">
                                    <div class="text-center text-muted">
                                        <i class="fas fa-comments fa-3x mb-3"></i>
                                        <p>Select an inquiry to view the conversation</p>
                                    </div>
                                </div>
                                <form id="replyForm" class="d-none">
                                    <input type="hidden" name="inquiry_id" id="inquiry_id">
                                    <div class="input-group">
                                        <textarea class="form-control" name="message" rows="2" 
                                            placeholder="Type your reply..." required></textarea>
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fas fa-paper-plane me-1"></i> Send
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/chat.js"></script>
</body>
</html>