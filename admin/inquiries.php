<?php
session_start();
require_once '../config/database.php';
require_once 'send_email.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Get admin information
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'admin'");
$stmt->execute([$_SESSION['user_id']]);
$admin = $stmt->fetch();

// If admin not found, redirect to login
if (!$admin) {
    session_destroy();
    header('Location: login.php');
    exit();
}

$page_title = 'Inquiries';
$current_page = 'inquiries';
require_once 'includes/header.php';

// Get all inquiries with property and buyer information
$stmt = $pdo->query("
    SELECT i.*, p.title as property_title, u.name as buyer_name, u.email as buyer_email 
    FROM inquiries i 
    LEFT JOIN properties p ON i.property_id = p.id 
    LEFT JOIN users u ON i.user_id = u.id 
    ORDER BY i.created_at DESC
");
$inquiries = $stmt->fetchAll();
?>

<!-- Main content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Inquiries</h1>
    </div>

    <!-- Inquiries Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Property</th>
                            <th>Buyer</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($inquiries as $inquiry): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($inquiry['id']); ?></td>
                                <td><?php echo htmlspecialchars($inquiry['property_title']); ?></td>
                                <td>
                                    <?php echo htmlspecialchars($inquiry['buyer_name']); ?><br>
                                    <small class="text-muted"><?php echo htmlspecialchars($inquiry['buyer_email']); ?></small>
                                </td>
                                <td><?php echo nl2br(htmlspecialchars($inquiry['message'])); ?></td>
                                <td>
                                    <span class="badge bg-<?php 
                                        echo $inquiry['status'] === 'new' ? 'danger' : 
                                            ($inquiry['status'] === 'read' ? 'warning' : 'success'); 
                                    ?>">
                                        <?php echo ucfirst($inquiry['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y H:i', strtotime($inquiry['created_at'])); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary me-2" onclick="openReplyModal('<?php echo $inquiry['id']; ?>', '<?php echo htmlspecialchars($inquiry['buyer_email']); ?>', '<?php echo htmlspecialchars($inquiry['property_title']); ?>')">
                                        <i class="fas fa-reply"></i>
                                    </button>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="inquiry_id" value="<?php echo $inquiry['id']; ?>">
                                        <input type="hidden" name="status" value="<?php echo $inquiry['status'] === 'new' ? 'read' : 'new'; ?>">
                                        <button type="submit" name="update_status" class="btn btn-sm btn-outline-<?php echo $inquiry['status'] === 'new' ? 'success' : 'warning'; ?>">
                                            <i class="fas fa-<?php echo $inquiry['status'] === 'new' ? 'check' : 'undo'; ?>"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Reply Modal -->
<div class="modal fade" id="replyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reply to Inquiry</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="replyForm" method="POST">
                    <input type="hidden" name="inquiry_id" id="inquiry_id">
                    <input type="hidden" name="buyer_email" id="buyer_email">
                    <input type="hidden" name="property_title" id="property_title">
                    <div class="mb-3">
                        <label class="form-label">To:</label>
                        <div id="recipient_info"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message:</label>
                        <textarea class="form-control" name="reply_message" rows="5" required></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="send_reply" class="btn btn-primary">Send Reply</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function openReplyModal(inquiryId, buyerEmail, propertyTitle) {
    document.getElementById('inquiry_id').value = inquiryId;
    document.getElementById('buyer_email').value = buyerEmail;
    document.getElementById('property_title').value = propertyTitle;
    document.getElementById('recipient_info').textContent = buyerEmail;
    
    // Pre-fill the reply message template
    const replyTemplate = `Dear Valued Client,

Thank you for your inquiry about ${propertyTitle}. 

[Your response here]

Best regards,
${<?php echo json_encode($admin['name']); ?>}
REMSR Team`;
    
    document.querySelector('textarea[name="reply_message"]').value = replyTemplate;
    
    new bootstrap.Modal(document.getElementById('replyModal')).show();
}
</script>
</body>
</html> 