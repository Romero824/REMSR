<?php
session_start();

// Check if user is logged in and is a tenant
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["user_type"] !== "tenant"){
    header("location: ../login.php");
    exit;
}

require_once "../config/database.php";
require_once "../models/Lease.php";
require_once "../models/MaintenanceRequest.php";

$database = new Database();
$db = $database->getConnection();

$lease = new Lease($db);
$maintenance = new MaintenanceRequest($db);

// Get tenant's current lease
$tenant_leases = $lease->getByTenant($_SESSION["user_id"]);
$current_lease = null;

while($lease_row = $tenant_leases->fetch(PDO::FETCH_ASSOC)) {
    if($lease_row['status'] == 'active' && 
       strtotime($lease_row['start_date']) <= time() && 
       strtotime($lease_row['end_date']) >= time()) {
        $current_lease = $lease_row;
        break;
    }
}

// Get maintenance requests for the current lease
$maintenance_requests = null;
if($current_lease) {
    $maintenance_requests = $maintenance->readByTenant($_SESSION["user_id"]);
}

// Handle maintenance request submission
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_request'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $priority = trim($_POST['priority']);
    
    $maintenance->property_id = $current_lease['property_id'];
    $maintenance->reported_by = $_SESSION["user_id"];
    $maintenance->title = $title;
    $maintenance->description = $description;
    $maintenance->priority = $priority;
    $maintenance->status = 'pending';
    
    if($maintenance->create()) {
        $success_message = "Maintenance request submitted successfully!";
    } else {
        $error_message = "Unable to submit maintenance request. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Requests - REMSR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">REMSR - Tenant Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="my_rental.php">My Rental</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="payments.php">Payments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="maintenance.php">Maintenance</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Maintenance Requests</h2>

        <?php if(isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php if(isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if($current_lease): ?>
            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Request History</h5>
                        </div>
                        <div class="card-body">
                            <?php if($maintenance_requests && $maintenance_requests->rowCount() > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Title</th>
                                                <th>Priority</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while($request = $maintenance_requests->fetch(PDO::FETCH_ASSOC)): ?>
                                                <tr>
                                                    <td><?php echo date('M d, Y', strtotime($request['created_at'])); ?></td>
                                                    <td><?php echo htmlspecialchars($request['title']); ?></td>
                                                    <td>
                                                        <span class="badge bg-<?php 
                                                            echo $request['priority'] == 'high' ? 'danger' : 
                                                                ($request['priority'] == 'medium' ? 'warning' : 'info'); 
                                                        ?>">
                                                            <?php echo ucfirst($request['priority']); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-<?php 
                                                            echo $request['status'] == 'completed' ? 'success' : 
                                                                ($request['status'] == 'pending' ? 'warning' : 'info'); 
                                                        ?>">
                                                            <?php echo ucfirst($request['status']); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-info" 
                                                                onclick="viewRequest(<?php echo $request['request_id']; ?>)">
                                                            View
                                                        </button>
                                                        <?php if($request['status'] == 'pending'): ?>
                                                            <button class="btn btn-sm btn-danger" 
                                                                    onclick="cancelRequest(<?php echo $request['request_id']; ?>)">
                                                                Cancel
                                                            </button>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">No maintenance requests found.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Submit New Request</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label class="form-label">Title</label>
                                    <input type="text" class="form-control" name="title" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control" name="description" rows="4" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Priority</label>
                                    <select class="form-select" name="priority" required>
                                        <option value="low">Low</option>
                                        <option value="medium">Medium</option>
                                        <option value="high">High</option>
                                    </select>
                                </div>
                                <button type="submit" name="submit_request" class="btn btn-primary w-100">
                                    <i class="bi bi-tools"></i> Submit Request
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Emergency Contact</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Property Manager:</strong> (555) 123-4567</p>
                            <p><strong>Emergency Maintenance:</strong> (555) 987-6543</p>
                            <p><strong>Hours:</strong> 24/7 Emergency Service</p>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> You currently don't have an active rental property.
            </div>
        <?php endif; ?>
    </div>

    <!-- View Request Modal -->
    <div class="modal fade" id="viewRequestModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Maintenance Request Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="requestDetails">
                    <!-- Content will be loaded dynamically -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function viewRequest(requestId) {
            // Add AJAX call to fetch request details
            fetch('get_request_details.php?id=' + requestId)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('requestDetails').innerHTML = `
                        <p><strong>Title:</strong> ${data.title}</p>
                        <p><strong>Description:</strong> ${data.description}</p>
                        <p><strong>Priority:</strong> ${data.priority}</p>
                        <p><strong>Status:</strong> ${data.status}</p>
                        <p><strong>Submitted:</strong> ${data.created_at}</p>
                        ${data.completed_at ? `<p><strong>Completed:</strong> ${data.completed_at}</p>` : ''}
                    `;
                    new bootstrap.Modal(document.getElementById('viewRequestModal')).show();
                });
        }

        function cancelRequest(requestId) {
            if(confirm('Are you sure you want to cancel this maintenance request?')) {
                // Add AJAX call to cancel request
                window.location.href = 'cancel_request.php?id=' + requestId;
            }
        }
    </script>
</body>
</html> 