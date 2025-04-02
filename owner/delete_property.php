<?php
require_once "../config/database.php";
require_once "../models/Property.php";
require_once "../models/Lease.php";

$database = new Database();
$db = $database->getConnection();
$property = new Property($db);
$lease = new Lease($db);

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

// Check if property has active lease
$active_lease = $lease->getActiveLeaseByProperty($property_id);
if ($active_lease) {
    $_SESSION["error"] = "Cannot delete property with active lease.";
    header("location: view_property.php?id=" . $property_id);
    exit();
}

// Delete property image if exists
if (!empty($property_data["image_path"])) {
    $image_file = "../" . $property_data["image_path"];
    if (file_exists($image_file)) {
        unlink($image_file);
    }
}

// Delete property
if ($property->delete($property_id)) {
    $_SESSION["success"] = "Property deleted successfully.";
} else {
    $_SESSION["error"] = "Unable to delete property.";
}

header("location: properties.php");
exit(); 