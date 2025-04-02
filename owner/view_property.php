<?php
require_once "../config/database.php";
require_once "../models/Property.php";
require_once "../models/Lease.php";
require_once "../models/MaintenanceRequest.php";
require_once "../includes/header.php";

$database = new Database();
$db = $database->getConnection();
$property = new Property($db);
$lease = new Lease($db);
$maintenance = new MaintenanceRequest($db);

// Check if property ID is provided
if (!isset($_GET["id"])) {
    header("location: properties.php");
    exit();
}

$property_id = $_GET["id"];

// Get property details
$property_data = $property->readOne($property_id);

// Check if property exists and belongs to the owner
if (!$property_data || $property_data["owner_id"] != $_SESSION["user_id"]) {
    header("location: properties.php");
    exit();
}

// Get active lease for the property
$active_lease = $lease->getActiveLeaseByProperty($property_id);

// Get recent maintenance requests
$maintenance_requests = $maintenance->getByProperty($property_id);
?>

<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0"><?php echo htmlspecialchars($property_data["title"]); ?></h3>
                    <span class="badge bg-<?php 
                        echo $property_data["status"] == "available" ? "success" : 
                            ($property_data["status"] == "rented" ? "info" : 
                            ($property_data["status"] == "maintenance" ? "warning" : "secondary")); 
                    ?>">
                        <?php echo ucfirst(htmlspecialchars($property_data["status"])); ?>
                    </span>
                </div>
                <div class="card-body">
                    <?php if (!empty($property_data["image_path"])): ?>
                        <img src="../<?php echo htmlspecialchars($property_data["image_path"]); ?>" 
                             class="img-fluid rounded mb-3" 
                             alt="<?php echo htmlspecialchars($property_data["title"]); ?>">
                    <?php endif; ?>

                    <p class="card-text"><?php echo nl2br(htmlspecialchars($property_data["description"])); ?></p>

                    <div class="row">
                        <div class="col-md-6">
                            <h5>Property Details</h5>
                            <ul class="list-unstyled">
                                <li><strong>Type:</strong> <?php echo ucfirst(htmlspecialchars($property_data["property_type"])); ?></li>
                                <li><strong>Price:</strong> $<?php echo number_format($property_data["price"], 2); ?></li>
                                <li><strong>Bedrooms:</strong> <?php echo $property_data["bedrooms"]; ?></li>
                                <li><strong>Bathrooms:</strong> <?php echo $property_data["bathrooms"]; ?></li>
                                <li><strong>Square Feet:</strong> <?php echo number_format($property_data["square_feet"]); ?></li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>Location</h5>
                            <ul class="list-unstyled">
                                <li><?php echo htmlspecialchars($property_data["address"]); ?></li>
                                <li><?php echo htmlspecialchars($property_data["city"] . ", " . $property_data["state"] . " " . $property_data["zip_code"]); ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Lease Section -->
            <?php if ($active_lease): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="card-title mb-0">Active Lease</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Tenant Information</h5>
                            <ul class="list-unstyled">
                                <li><strong>Name:</strong> <?php echo htmlspecialchars($active_lease["tenant_name"]); ?></li>
                                <li><strong>Email:</strong> <?php echo htmlspecialchars($active_lease["tenant_email"]); ?></li>
                                <li><strong>Phone:</strong> <?php echo htmlspecialchars($active_lease["tenant_phone"]); ?></li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>Lease Details</h5>
                            <ul class="list-unstyled">
                                <li><strong>Start Date:</strong> <?php echo date("F j, Y", strtotime($active_lease["start_date"])); ?></li>
                                <li><strong>End Date:</strong> <?php echo date("F j, Y", strtotime($active_lease["end_date"])); ?></li>
                                <li><strong>Monthly Rent:</strong> $<?php echo number_format($active_lease["monthly_rent"], 2); ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="col-md-4">
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="card-title mb-0">Quick Actions</h4>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="edit_property.php?id=<?php echo $property_id; ?>" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Edit Property
                        </a>
                        <?php if ($property_data["status"] == "available"): ?>
                            <a href="add_lease.php?property_id=<?php echo $property_id; ?>" class="btn btn-success">
                                <i class="bi bi-file-earmark-text"></i> Add New Lease
                            </a>
                        <?php endif; ?>
                        <a href="maintenance_requests.php?property_id=<?php echo $property_id; ?>" class="btn btn-info">
                            <i class="bi bi-tools"></i> View Maintenance Requests
                        </a>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="bi bi-trash"></i> Delete Property
                        </button>
                    </div>
                </div>
            </div>

            <!-- Recent Maintenance Requests -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Recent Maintenance Requests</h4>
                </div>
                <div class="card-body">
                    <?php if ($maintenance_requests->rowCount() > 0): ?>
                        <div class="list-group">
                            <?php while ($request = $maintenance_requests->fetch(PDO::FETCH_ASSOC)): ?>
                                <a href="view_maintenance_request.php?id=<?php echo $request["request_id"]; ?>" 
                                   class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($request["title"]); ?></h6>
                                        <small><?php echo date("M j, Y", strtotime($request["created_at"])); ?></small>
                                    </div>
                                    <p class="mb-1"><?php echo htmlspecialchars(substr($request["description"], 0, 100)) . "..."; ?></p>
                                    <small class="text-<?php 
                                        echo $request["status"] == "pending" ? "warning" : 
                                            ($request["status"] == "in_progress" ? "info" : 
                                            ($request["status"] == "completed" ? "success" : "secondary")); 
                                    ?>">
                                        <?php echo ucfirst(htmlspecialchars($request["status"])); ?>
                                    </small>
                                </a>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No maintenance requests found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Property</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this property? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="delete_property.php?id=<?php echo $property_id; ?>" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>

<?php require_once "../includes/footer.php"; ?> 