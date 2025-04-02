<?php
class Property {
    // Database connection and table name
    private $conn;
    private $table_name = "properties";

    // Object properties
    public $property_id;
    public $owner_id;
    public $title;
    public $description;
    public $property_type;
    public $price;
    public $address;
    public $city;
    public $state;
    public $zip_code;
    public $bedrooms;
    public $bathrooms;
    public $square_feet;
    public $status;
    public $image_path;
    public $created_at;
    public $updated_at;

    // Constructor with $db as database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    // Read all properties
    public function read() {
        $query = "SELECT p.*, u.name as owner_name 
                 FROM " . $this->table_name . " p
                 LEFT JOIN users u ON p.owner_id = u.user_id
                 ORDER BY p.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    // Read single property
    public function readOne($property_id) {
        $query = "SELECT p.*, u.name as owner_name 
                 FROM " . $this->table_name . " p
                 LEFT JOIN users u ON p.owner_id = u.user_id
                 WHERE p.property_id = ?
                 LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $property_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->property_id = $row["property_id"];
            $this->owner_id = $row["owner_id"];
            $this->title = $row["title"];
            $this->description = $row["description"];
            $this->property_type = $row["property_type"];
            $this->price = $row["price"];
            $this->address = $row["address"];
            $this->city = $row["city"];
            $this->state = $row["state"];
            $this->zip_code = $row["zip_code"];
            $this->bedrooms = $row["bedrooms"];
            $this->bathrooms = $row["bathrooms"];
            $this->square_feet = $row["square_feet"];
            $this->status = $row["status"];
            $this->image_path = $row["image_path"];
            $this->created_at = $row["created_at"];
            $this->updated_at = $row["updated_at"];
            $this->owner_name = $row["owner_name"];
        }

        return $row;
    }

    // Get properties by owner
    public function getByOwner($owner_id) {
        $query = "SELECT * FROM " . $this->table_name . "
                 WHERE owner_id = ?
                 ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $owner_id);
        $stmt->execute();

        return $stmt;
    }

    // Create property
    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . "
                 (owner_id, title, description, property_type, price, 
                  address, city, state, zip_code, bedrooms, bathrooms, 
                  square_feet, status, image_path, created_at, updated_at)
                 VALUES
                 (:owner_id, :title, :description, :property_type, :price,
                  :address, :city, :state, :zip_code, :bedrooms, :bathrooms,
                  :square_feet, :status, :image_path, NOW(), NOW())";

        $stmt = $this->conn->prepare($query);

        // Sanitize input
        $this->owner_id = htmlspecialchars(strip_tags($data["owner_id"]));
        $this->title = htmlspecialchars(strip_tags($data["title"]));
        $this->description = htmlspecialchars(strip_tags($data["description"]));
        $this->property_type = htmlspecialchars(strip_tags($data["property_type"]));
        $this->price = htmlspecialchars(strip_tags($data["price"]));
        $this->address = htmlspecialchars(strip_tags($data["address"]));
        $this->city = htmlspecialchars(strip_tags($data["city"]));
        $this->state = htmlspecialchars(strip_tags($data["state"]));
        $this->zip_code = htmlspecialchars(strip_tags($data["zip_code"]));
        $this->bedrooms = htmlspecialchars(strip_tags($data["bedrooms"]));
        $this->bathrooms = htmlspecialchars(strip_tags($data["bathrooms"]));
        $this->square_feet = htmlspecialchars(strip_tags($data["square_feet"]));
        $this->status = htmlspecialchars(strip_tags($data["status"]));
        $this->image_path = htmlspecialchars(strip_tags($data["image_path"]));

        // Bind values
        $stmt->bindParam(":owner_id", $this->owner_id);
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":property_type", $this->property_type);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":address", $this->address);
        $stmt->bindParam(":city", $this->city);
        $stmt->bindParam(":state", $this->state);
        $stmt->bindParam(":zip_code", $this->zip_code);
        $stmt->bindParam(":bedrooms", $this->bedrooms);
        $stmt->bindParam(":bathrooms", $this->bathrooms);
        $stmt->bindParam(":square_feet", $this->square_feet);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":image_path", $this->image_path);

        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Update property
    public function update($property_id, $data) {
        $query = "UPDATE " . $this->table_name . "
                 SET title = :title,
                     description = :description,
                     property_type = :property_type,
                     price = :price,
                     address = :address,
                     city = :city,
                     state = :state,
                     zip_code = :zip_code,
                     bedrooms = :bedrooms,
                     bathrooms = :bathrooms,
                     square_feet = :square_feet,
                     status = :status,
                     image_path = :image_path,
                     updated_at = NOW()
                 WHERE property_id = :property_id";

        $stmt = $this->conn->prepare($query);

        // Sanitize input
        $this->property_id = htmlspecialchars(strip_tags($property_id));
        $this->title = htmlspecialchars(strip_tags($data["title"]));
        $this->description = htmlspecialchars(strip_tags($data["description"]));
        $this->property_type = htmlspecialchars(strip_tags($data["property_type"]));
        $this->price = htmlspecialchars(strip_tags($data["price"]));
        $this->address = htmlspecialchars(strip_tags($data["address"]));
        $this->city = htmlspecialchars(strip_tags($data["city"]));
        $this->state = htmlspecialchars(strip_tags($data["state"]));
        $this->zip_code = htmlspecialchars(strip_tags($data["zip_code"]));
        $this->bedrooms = htmlspecialchars(strip_tags($data["bedrooms"]));
        $this->bathrooms = htmlspecialchars(strip_tags($data["bathrooms"]));
        $this->square_feet = htmlspecialchars(strip_tags($data["square_feet"]));
        $this->status = htmlspecialchars(strip_tags($data["status"]));
        $this->image_path = htmlspecialchars(strip_tags($data["image_path"]));

        // Bind values
        $stmt->bindParam(":property_id", $this->property_id);
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":property_type", $this->property_type);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":address", $this->address);
        $stmt->bindParam(":city", $this->city);
        $stmt->bindParam(":state", $this->state);
        $stmt->bindParam(":zip_code", $this->zip_code);
        $stmt->bindParam(":bedrooms", $this->bedrooms);
        $stmt->bindParam(":bathrooms", $this->bathrooms);
        $stmt->bindParam(":square_feet", $this->square_feet);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":image_path", $this->image_path);

        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Delete property
    public function delete($property_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE property_id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $property_id);

        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Search properties
    public function search($keywords) {
        $query = "SELECT p.*, u.name as owner_name 
                 FROM " . $this->table_name . " p
                 LEFT JOIN users u ON p.owner_id = u.user_id
                 WHERE p.title LIKE ? OR p.description LIKE ? OR p.address LIKE ?
                 ORDER BY p.created_at DESC";

        $keywords = "%{$keywords}%";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $keywords);
        $stmt->bindParam(2, $keywords);
        $stmt->bindParam(3, $keywords);
        $stmt->execute();

        return $stmt;
    }

    // Get available properties
    public function getAvailable() {
        $query = "SELECT p.*, u.name as owner_name 
                 FROM " . $this->table_name . " p
                 LEFT JOIN users u ON p.owner_id = u.user_id
                 WHERE p.status = 'available'
                 ORDER BY p.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }
}
?> 