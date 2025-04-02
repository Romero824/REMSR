<?php
require_once "../config/database.php";
require_once "../models/Property.php";
require_once "../includes/header.php";

$database = new Database();
$db = $database->getConnection();
$property = new Property($db);

$message = "";

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate input
    $title = trim($_POST["title"]);
    $description = trim($_POST["description"]);
    $property_type = trim($_POST["property_type"]);
    $price = floatval($_POST["price"]);
    $address = trim($_POST["address"]);
    $city = trim($_POST["city"]);
    $state = trim($_POST["state"]);
    $zip_code = trim($_POST["zip_code"]);
    $bedrooms = intval($_POST["bedrooms"]);
    $bathrooms = floatval($_POST["bathrooms"]);
    $square_feet = floatval($_POST["square_feet"]);
    $status = trim($_POST["status"]);

    // Handle image upload
    $image_path = $property_data["image_path"];
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $target_dir = "../uploads/properties/";
        $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid() . "." . $file_extension;
        $target_file = $target_dir . $new_filename;

        // Check if image file is actual image or fake image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            // Allow certain file formats
            if ($file_extension == "jpg" || $file_extension == "png" || $file_extension == "jpeg" || $file_extension == "gif") {
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    // Delete old image if exists
                    if (!empty($property_data["image_path"])) {
                        $old_file = "../" . $property_data["image_path"];
                        if (file_exists($old_file)) {
                            unlink($old_file);
                        }
                    }
                    $image_path = "uploads/properties/" . $new_filename;
                }
            }
        }
    }

    // Update property
    if ($property->update($property_id, [
        "title" => $title,
        "description" => $description,
        "property_type" => $property_type,
        "price" => $price,
        "address" => $address,
        "city" => $city,
        "state" => $state,
        "zip_code" => $zip_code,
        "bedrooms" => $bedrooms,
        "bathrooms" => $bathrooms,
        "square_feet" => $square_feet,
        "status" => $status,
        "image_path" => $image_path
    ])) {
        header("location: view_property.php?id=" . $property_id);
        exit();
    } else {
        $message = "Unable to update property.";
    }
}
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Property</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($message)): ?>
                        <div class="alert alert-danger"><?php echo $message; ?></div>
                    <?php endif; ?>

                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=" . $property_id; ?>" 
                          method="post" 
                          enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="title" class="form-label">Property Title</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="title" 
                                   name="title" 
                                   value="<?php echo htmlspecialchars($property_data["title"]); ?>" 
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" 
                                      id="description" 
                                      name="description" 
                                      rows="3" 
                                      required><?php echo htmlspecialchars($property_data["description"]); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="property_type" class="form-label">Property Type</label>
                            <select class="form-select" id="property_type" name="property_type" required>
                                <option value="">Select Property Type</option>
                                <option value="house" <?php echo $property_data["property_type"] == "house" ? "selected" : ""; ?>>House</option>
                                <option value="apartment" <?php echo $property_data["property_type"] == "apartment" ? "selected" : ""; ?>>Apartment</option>
                                <option value="condo" <?php echo $property_data["property_type"] == "condo" ? "selected" : ""; ?>>Condo</option>
                                <option value="townhouse" <?php echo $property_data["property_type"] == "townhouse" ? "selected" : ""; ?>>Townhouse</option>
                                <option value="land" <?php echo $property_data["property_type"] == "land" ? "selected" : ""; ?>>Land</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label">Price</label>
                            <input type="number" 
                                   class="form-control" 
                                   id="price" 
                                   name="price" 
                                   step="0.01" 
                                   value="<?php echo $property_data["price"]; ?>" 
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="address" 
                                   name="address" 
                                   value="<?php echo htmlspecialchars($property_data["address"]); ?>" 
                                   required>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="city" class="form-label">City</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="city" 
                                           name="city" 
                                           value="<?php echo htmlspecialchars($property_data["city"]); ?>" 
                                           required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="state" class="form-label">State</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="state" 
                                           name="state" 
                                           value="<?php echo htmlspecialchars($property_data["state"]); ?>" 
                                           required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="zip_code" class="form-label">ZIP Code</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="zip_code" 
                                           name="zip_code" 
                                           value="<?php echo htmlspecialchars($property_data["zip_code"]); ?>" 
                                           required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="bedrooms" class="form-label">Bedrooms</label>
                                    <input type="number" 
                                           class="form-control" 
                                           id="bedrooms" 
                                           name="bedrooms" 
                                           min="0" 
                                           value="<?php echo $property_data["bedrooms"]; ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="bathrooms" class="form-label">Bathrooms</label>
                                    <input type="number" 
                                           class="form-control" 
                                           id="bathrooms" 
                                           name="bathrooms" 
                                           min="0" 
                                           step="0.5" 
                                           value="<?php echo $property_data["bathrooms"]; ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="square_feet" class="form-label">Square Feet</label>
                                    <input type="number" 
                                           class="form-control" 
                                           id="square_feet" 
                                           name="square_feet" 
                                           min="0" 
                                           value="<?php echo $property_data["square_feet"]; ?>">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="available" <?php echo $property_data["status"] == "available" ? "selected" : ""; ?>>Available</option>
                                <option value="rented" <?php echo $property_data["status"] == "rented" ? "selected" : ""; ?>>Rented</option>
                                <option value="maintenance" <?php echo $property_data["status"] == "maintenance" ? "selected" : ""; ?>>Maintenance</option>
                                <option value="sold" <?php echo $property_data["status"] == "sold" ? "selected" : ""; ?>>Sold</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">Property Image</label>
                            <?php if (!empty($property_data["image_path"])): ?>
                                <div class="mb-2">
                                    <img src="../<?php echo htmlspecialchars($property_data["image_path"]); ?>" 
                                         class="img-thumbnail" 
                                         style="max-height: 200px;" 
                                         alt="Current property image">
                                </div>
                            <?php endif; ?>
                            <input type="file" 
                                   class="form-control" 
                                   id="image" 
                                   name="image" 
                                   accept="image/*">
                            <div class="form-text">Leave empty to keep the current image.</div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Update Property</button>
                            <a href="view_property.php?id=<?php echo $property_id; ?>" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once "../includes/footer.php"; ?> 