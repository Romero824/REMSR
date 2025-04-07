<nav class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link text-white" href="dashboard.php">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="properties.php">
                    <i class="fas fa-home me-2"></i> Properties
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active text-white" href="inquiries.php">
                    <i class="fas fa-comments me-2"></i> Inquiries
                    <?php
                    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM inquiries WHERE status = 'new' OR status = 'replied_by_buyer'");
                    $stmt->execute();
                    $unread = $stmt->fetch();
                    if ($unread['count'] > 0): ?>
                        <span class="badge bg-danger rounded-pill ms-2"><?php echo $unread['count']; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="users.php">
                    <i class="fas fa-users me-2"></i> Users
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="settings.php">
                    <i class="fas fa-cog me-2"></i> Settings
                </a>
            </li>
        </ul>
    </div>
</nav>