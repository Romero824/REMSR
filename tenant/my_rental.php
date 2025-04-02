<?php
session_start();

// Check if user is logged in and is a tenant
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["user_type"] !== "tenant"){
    header("location: ../login.php");
    exit;
}

require_once "../config/database.php";
require_once "../models/Property.php";
require_once "../models/Lease.php";
require_once "../models/Payment.php";

$database = new Database();
$db = $database->getConnection();

$property = new Property($db);
$lease = new Lease($db);
$payment = new Payment($db);

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

// Get property details if tenant has an active lease
$property_details = null;
if($current_lease) {
    $property_details = $property->readOne($current_lease['property_id']);
}

// Get recent payments for the current lease
$recent_payments = null;
if($current_lease) {
    $recent_payments = $payment->readByLease($current_lease['lease_id']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Rental - REMSR</title>
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
                        <a class="nav-link active" href="my_rental.php">My Rental</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="payments.php">Payments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="maintenance.php">Maintenance</a>
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
        <h2>My Rental Property</h2>

        <?php if($current_lease && $property_details): ?>
            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($property_details['title']); ?></h5>
                            <?php if($property_details['image_path']): ?>
                                <img src="<?php echo htmlspecialchars($property_details['image_path']); ?>" 
                                     class="img-fluid mb-3" alt="Property Image">
                            <?php endif; ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Property Type:</strong> <?php echo htmlspecialchars($property_details['property_type']); ?></p>
                                    <p><strong>Address:</strong> <?php echo htmlspecialchars($property_details['address']); ?></p>
                                    <p><strong>City:</strong> <?php echo htmlspecialchars($property_details['city']); ?></p>
                                    <p><strong>State:</strong> <?php echo htmlspecialchars($property_details['state']); ?></p>
                                    <p><strong>ZIP Code:</strong> <?php echo htmlspecialchars($property_details['zip_code']); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Bedrooms:</strong> <?php echo htmlspecialchars($property_details['bedrooms']); ?></p>
                                    <p><strong>Bathrooms:</strong> <?php echo htmlspecialchars($property_details['bathrooms']); ?></p>
                                    <p><strong>Square Feet:</strong> <?php echo htmlspecialchars($property_details['square_feet']); ?></p>
                                    <p><strong>Monthly Rent:</strong> $<?php echo number_format($current_lease['monthly_rent'], 2); ?></p>
                                </div>
                            </div>
                            <p class="mt-3"><strong>Description:</strong></p>
                            <p><?php echo nl2br(htmlspecialchars($property_details['description'])); ?></p>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Lease Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Start Date:</strong> <?php echo date('F d, Y', strtotime($current_lease['start_date'])); ?></p>
                                    <p><strong>End Date:</strong> <?php echo date('F d, Y', strtotime($current_lease['end_date'])); ?></p>
                                    <p><strong>Security Deposit:</strong> $<?php echo number_format($current_lease['security_deposit'], 2); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Status:</strong> <span class="badge bg-success">Active</span></p>
                                    <p><strong>Days Remaining:</strong> <?php 
                                        $end_date = strtotime($current_lease['end_date']);
                                        $today = time();
                                        $days_remaining = ceil(($end_date - $today) / (60 * 60 * 24));
                                        echo $days_remaining;
                                    ?> days</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="payments.php" class="btn btn-primary">
                                    <i class="bi bi-cash"></i> Make Payment
                                </a>
                                <a href="maintenance.php" class="btn btn-warning">
                                    <i class="bi bi-tools"></i> Submit Maintenance Request
                                </a>
                                <a href="renew_lease.php" class="btn btn-info">
                                    <i class="bi bi-calendar-check"></i> Renew Lease
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Recent Payments</h5>
                        </div>
                        <div class="card-body">
                            <?php if($recent_payments && $recent_payments->rowCount() > 0): ?>
                                <div class="list-group">
                                    <?php while($payment_row = $recent_payments->fetch(PDO::FETCH_ASSOC)): ?>
                                        <div class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-1">$<?php echo number_format($payment_row['amount'], 2); ?></h6>
                                                    <small class="text-muted">
                                                        <?php echo date('M d, Y', strtotime($payment_row['payment_date'])); ?>
                                                    </small>
                                                </div>
                                                <span class="badge bg-<?php 
                                                    echo $payment_row['status'] == 'completed' ? 'success' : 
                                                        ($payment_row['status'] == 'pending' ? 'warning' : 'danger'); 
                                                ?>">
                                                    <?php echo ucfirst($payment_row['status']); ?>
                                                </span>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">No recent payments found.</p>
                            <?php endif; ?>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 