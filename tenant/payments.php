<?php
session_start();

// Check if user is logged in and is a tenant
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["user_type"] !== "tenant"){
    header("location: ../login.php");
    exit;
}

require_once "../config/database.php";
require_once "../models/Lease.php";
require_once "../models/Payment.php";

$database = new Database();
$db = $database->getConnection();

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

// Get all payments for the current lease
$payments = null;
if($current_lease) {
    $payments = $payment->readByLease($current_lease['lease_id']);
}

// Handle payment submission
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['make_payment'])) {
    $amount = floatval($_POST['amount']);
    $payment_date = date('Y-m-d');
    
    $payment->lease_id = $current_lease['lease_id'];
    $payment->amount = $amount;
    $payment->payment_date = $payment_date;
    $payment->status = 'pending';
    
    if($payment->create()) {
        $success_message = "Payment submitted successfully!";
    } else {
        $error_message = "Unable to submit payment. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments - REMSR</title>
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
                        <a class="nav-link active" href="payments.php">Payments</a>
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
        <h2>Payment History</h2>

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
                            <h5 class="card-title mb-0">Payment History</h5>
                        </div>
                        <div class="card-body">
                            <?php if($payments && $payments->rowCount() > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while($payment_row = $payments->fetch(PDO::FETCH_ASSOC)): ?>
                                                <tr>
                                                    <td><?php echo date('M d, Y', strtotime($payment_row['payment_date'])); ?></td>
                                                    <td>$<?php echo number_format($payment_row['amount'], 2); ?></td>
                                                    <td>
                                                        <span class="badge bg-<?php 
                                                            echo $payment_row['status'] == 'completed' ? 'success' : 
                                                                ($payment_row['status'] == 'pending' ? 'warning' : 'danger'); 
                                                        ?>">
                                                            <?php echo ucfirst($payment_row['status']); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php if($payment_row['status'] == 'pending'): ?>
                                                            <button class="btn btn-sm btn-danger" 
                                                                    onclick="cancelPayment(<?php echo $payment_row['payment_id']; ?>)">
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
                                <p class="text-muted">No payment history found.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Make a Payment</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label class="form-label">Amount</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" name="amount" 
                                               step="0.01" min="0" max="<?php echo $current_lease['monthly_rent']; ?>" 
                                               value="<?php echo $current_lease['monthly_rent']; ?>" required>
                                    </div>
                                </div>
                                <button type="submit" name="make_payment" class="btn btn-primary w-100">
                                    <i class="bi bi-cash"></i> Submit Payment
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Payment Summary</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Monthly Rent:</strong> $<?php echo number_format($current_lease['monthly_rent'], 2); ?></p>
                            <p><strong>Due Date:</strong> <?php echo date('F d', strtotime($current_lease['start_date'])); ?> of each month</p>
                            <p><strong>Payment Method:</strong> Online Payment</p>
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
    <script>
        function cancelPayment(paymentId) {
            if(confirm('Are you sure you want to cancel this payment?')) {
                // Add AJAX call to cancel payment
                window.location.href = 'cancel_payment.php?id=' + paymentId;
            }
        }
    </script>
</body>
</html> 