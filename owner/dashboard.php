<?php
require_once "../config/database.php";
require_once "../models/Property.php";
require_once "../models/Lease.php";
require_once "../models/Payment.php";
require_once "../models/MaintenanceRequest.php";
require_once "../includes/header.php";

$database = new Database();
$db = $database->getConnection();

$property = new Property($db);
$lease = new Lease($db);
$payment = new Payment($db);
$maintenance = new MaintenanceRequest($db);

// Get owner's properties
$properties = $property->getByOwner($_SESSION["user_id"]);
$total_properties = $properties->rowCount();

// Get active leases for all owner's properties
$total_active_leases = 0;
$total_income = 0;
$active_leases = array();

while($property_row = $properties->fetch(PDO::FETCH_ASSOC)) {
    $property_leases = $lease->getByProperty($property_row['property_id']);
    while($lease_row = $property_leases->fetch(PDO::FETCH_ASSOC)) {
        if($lease_row['status'] == 'active' && 
           strtotime($lease_row['start_date']) <= time() && 
           strtotime($lease_row['end_date']) >= time()) {
            $total_active_leases++;
            $total_income += $lease_row['monthly_rent'];
            $active_leases[] = $lease_row;
        }
    }
}

// Get pending maintenance requests
$maintenance_requests = $maintenance->getByProperty($_SESSION["user_id"]);
$pending_maintenance = 0;
while($maintenance_row = $maintenance_requests->fetch(PDO::FETCH_ASSOC)) {
    if($maintenance_row['status'] == 'pending') {
        $pending_maintenance++;
    }
}

// Get recent payments
$recent_payments = $payment->getByLease($_SESSION["user_id"]);
?>

<div class="container-fluid">
    <h1 class="h3 mb-4">Owner Dashboard</h1>
    
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Properties</h5>
                    <p class="card-text display-4"><?php echo $total_properties; ?></p>
                    <a href="properties.php" class="btn btn-primary">View Properties</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Active Leases</h5>
                    <p class="card-text display-4"><?php echo $total_active_leases; ?></p>
                    <a href="tenants.php" class="btn btn-primary">View Tenants</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Monthly Income</h5>
                    <p class="card-text display-4">$<?php echo number_format($total_income, 2); ?></p>
                    <a href="payments.php" class="btn btn-primary">View Payments</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Pending Maintenance</h5>
                    <p class="card-text display-4"><?php echo $pending_maintenance; ?></p>
                    <a href="maintenance.php" class="btn btn-primary">View Requests</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Recent Payments</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Property</th>
                                <th>Tenant</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($payment_row = $recent_payments->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo date('M d, Y', strtotime($payment_row['payment_date'])); ?></td>
                                <td><?php echo htmlspecialchars($payment_row['property_title']); ?></td>
                                <td><?php echo htmlspecialchars($payment_row['tenant_name']); ?></td>
                                <td>$<?php echo number_format($payment_row['amount'], 2); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $payment_row['status'] == 'completed' ? 'success' : 'warning'; ?>">
                                        <?php echo ucfirst($payment_row['status']); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="add_property.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Add New Property
                    </a>
                    <a href="maintenance.php" class="btn btn-warning">
                        <i class="bi bi-tools"></i> View Maintenance Requests
                    </a>
                    <a href="reports.php" class="btn btn-info">
                        <i class="bi bi-file-earmark-text"></i> View Reports
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once "../includes/footer.php"; ?> 