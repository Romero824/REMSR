<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'buyer') {
    header('Location: ../login.php');
    exit();
}

// Get buyer information
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'buyer'");
$stmt->execute([$_SESSION['user_id']]);
$buyer = $stmt->fetch();

// Get all inquiries for this buyer
$stmt = $pdo->prepare("
    SELECT 
        i.*,
        p.title as property_title,
        p.image_path as property_image
    FROM inquiries i
    LEFT JOIN properties p ON i.property_id = p.id
    WHERE i.user_id = ?
    ORDER BY i.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$inquiries = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Inquiries - REMSR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Add the same chat styles as admin page */
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
            .message-content {
                margin-bottom: 0.5rem;
                white-space: pre-wrap;
            }
        </style>
    </style>
</head>
<body>
    <!-- Include your existing navbar here -->
    
    <div class="container-fluid">
        <div class="row">
            <!-- Include your existing sidebar here -->
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">My Inquiries</h1>
                    <a href="dashboard.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
                </div>

                <div class="row">
                    <!-- Properties List -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush">
                                    <?php foreach ($inquiries as $inquiry): ?>
                                        <a href="#" class="list-group-item list-group-item-action inquiry-item" 
                                           data-inquiry-id="<?php echo $inquiry['id']; ?>"
                                           onclick="loadChat(<?php echo $inquiry['id']; ?>)">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1"><?php echo htmlspecialchars($inquiry['property_title']); ?></h6>
                                                <small class="text-muted">
                                                    <?php echo date('M d', strtotime($inquiry['created_at'])); ?>
                                                </small>
                                            </div>
                                            <small class="text-muted">
                                                <span class="badge bg-<?php 
                                                    echo $inquiry['status'] === 'new' ? 'primary' : 
                                                        ($inquiry['status'] === 'replied' ? 'success' : 'secondary'); 
                                                ?>">
                                                    <?php echo ucfirst($inquiry['status']); ?>
                                                </span>
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
                                            placeholder="Type your message..." required></textarea>
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
    <script>
        // Add the same JavaScript as admin page with slight modifications for buyer side
    </script>
    
    <!-- Add this before closing body tag -->
    <script src="js/chat.js"></script>
</body>
</html>