<?php
require_once "../config/database.php";
require_once "../models/Property.php";
require_once "../includes/header.php";

$database = new Database();
$db = $database->getConnection();
$property = new Property($db);

// Get all properties for the owner
$properties = $property->getByOwner($_SESSION["user_id"]);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>My Properties</h2>
    <a href="add_property.php" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add New Property
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Price</th>
                        <th>Location</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $properties->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><?php echo ucfirst(htmlspecialchars($row['property_type'])); ?></td>
                        <td>
                            <span class="badge bg-<?php 
                                echo $row['status'] == 'available' ? 'success' : 
                                    ($row['status'] == 'rented' ? 'info' : 
                                    ($row['status'] == 'maintenance' ? 'warning' : 'secondary')); 
                            ?>">
                                <?php echo ucfirst(htmlspecialchars($row['status'])); ?>
                            </span>
                        </td>
                        <td>$<?php echo number_format($row['price'], 2); ?></td>
                        <td><?php echo htmlspecialchars($row['city'] . ', ' . $row['state']); ?></td>
                        <td>
                            <div class="btn-group">
                                <a href="view_property.php?id=<?php echo $row['property_id']; ?>" 
                                   class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="edit_property.php?id=<?php echo $row['property_id']; ?>" 
                                   class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" 
                                        class="btn btn-sm btn-danger" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#deleteModal<?php echo $row['property_id']; ?>">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>

                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteModal<?php echo $row['property_id']; ?>" tabindex="-1">
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
                                            <a href="delete_property.php?id=<?php echo $row['property_id']; ?>" 
                                               class="btn btn-danger">Delete</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once "../includes/footer.php"; ?> 