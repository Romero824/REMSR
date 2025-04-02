<?php
class MaintenanceRequest {
    // Database connection and table name
    private $conn;
    private $table_name = "maintenance_requests";

    // Object properties
    public $request_id;
    public $property_id;
    public $reported_by;
    public $title;
    public $description;
    public $status;
    public $priority;
    public $created_at;
    public $updated_at;

    // Constructor with $db as database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    // Read all maintenance requests
    public function read() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Create maintenance request
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    property_id = :property_id,
                    reported_by = :reported_by,
                    title = :title,
                    description = :description,
                    status = :status,
                    priority = :priority,
                    created_at = :created_at,
                    updated_at = :updated_at";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->property_id = htmlspecialchars(strip_tags($this->property_id));
        $this->reported_by = htmlspecialchars(strip_tags($this->reported_by));
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->priority = htmlspecialchars(strip_tags($this->priority));
        $this->created_at = date('Y-m-d H:i:s');
        $this->updated_at = date('Y-m-d H:i:s');

        // Bind
        $stmt->bindParam(":property_id", $this->property_id);
        $stmt->bindParam(":reported_by", $this->reported_by);
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":priority", $this->priority);
        $stmt->bindParam(":created_at", $this->created_at);
        $stmt->bindParam(":updated_at", $this->updated_at);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Read single maintenance request
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE request_id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->request_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->property_id = $row['property_id'];
            $this->reported_by = $row['reported_by'];
            $this->title = $row['title'];
            $this->description = $row['description'];
            $this->status = $row['status'];
            $this->priority = $row['priority'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            return true;
        }
        return false;
    }

    // Update maintenance request
    public function update() {
        $query = "UPDATE " . $this->table_name . "
                SET
                    status = :status,
                    updated_at = :updated_at
                WHERE
                    request_id = :request_id";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->updated_at = date('Y-m-d H:i:s');
        $this->request_id = htmlspecialchars(strip_tags($this->request_id));

        // Bind
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":updated_at", $this->updated_at);
        $stmt->bindParam(":request_id", $this->request_id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Delete maintenance request
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE request_id = ?";
        $stmt = $this->conn->prepare($query);
        $this->request_id = htmlspecialchars(strip_tags($this->request_id));
        $stmt->bindParam(1, $this->request_id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Read maintenance requests by property
    public function readByProperty($property_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE property_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $property_id);
        $stmt->execute();
        return $stmt;
    }

    // Read maintenance requests by reporter
    public function readByReporter($reporter_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE reported_by = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $reporter_id);
        $stmt->execute();
        return $stmt;
    }

    // Get maintenance requests by property owner
    public function getByProperty($owner_id) {
        $query = "SELECT mr.*, p.title as property_title, u.full_name as reporter_name 
                 FROM " . $this->table_name . " mr
                 LEFT JOIN properties p ON mr.property_id = p.property_id
                 LEFT JOIN users u ON mr.reported_by = u.user_id
                 WHERE p.owner_id = ?
                 ORDER BY mr.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $owner_id);
        $stmt->execute();

        return $stmt;
    }
}
?> 