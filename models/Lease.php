<?php
class Lease {
    // Database connection and table name
    private $conn;
    private $table_name = "leases";

    // Object properties
    public $lease_id;
    public $property_id;
    public $tenant_id;
    public $start_date;
    public $end_date;
    public $monthly_rent;
    public $security_deposit;
    public $status;
    public $created_at;
    public $updated_at;

    // Constructor with $db as database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    // Read all leases
    public function read() {
        $query = "SELECT l.*, p.title as property_title, u.full_name as tenant_name 
                 FROM " . $this->table_name . " l
                 LEFT JOIN properties p ON l.property_id = p.property_id
                 LEFT JOIN users u ON l.tenant_id = u.user_id
                 ORDER BY l.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    // Read single lease
    public function readOne($lease_id) {
        $query = "SELECT l.*, p.title as property_title, u.full_name as tenant_name 
                 FROM " . $this->table_name . " l
                 LEFT JOIN properties p ON l.property_id = p.property_id
                 LEFT JOIN users u ON l.tenant_id = u.user_id
                 WHERE l.lease_id = ?
                 LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $lease_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->lease_id = $row["lease_id"];
            $this->property_id = $row["property_id"];
            $this->tenant_id = $row["tenant_id"];
            $this->start_date = $row["start_date"];
            $this->end_date = $row["end_date"];
            $this->monthly_rent = $row["monthly_rent"];
            $this->security_deposit = $row["security_deposit"];
            $this->status = $row["status"];
            $this->created_at = $row["created_at"];
            $this->updated_at = $row["updated_at"];
            $this->property_title = $row["property_title"];
            $this->tenant_name = $row["tenant_name"];
        }

        return $row;
    }

    // Get active lease by property
    public function getActiveLeaseByProperty($property_id) {
        $query = "SELECT l.*, u.full_name as tenant_name, u.email as tenant_email, u.phone as tenant_phone 
                 FROM " . $this->table_name . " l
                 LEFT JOIN users u ON l.tenant_id = u.user_id
                 WHERE l.property_id = ? AND l.status = 'active'
                 AND l.start_date <= CURDATE() AND l.end_date >= CURDATE()
                 LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $property_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get leases by property
    public function getByProperty($property_id) {
        $query = "SELECT l.*, u.full_name as tenant_name 
                 FROM " . $this->table_name . " l
                 LEFT JOIN users u ON l.tenant_id = u.user_id
                 WHERE l.property_id = ?
                 ORDER BY l.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $property_id);
        $stmt->execute();

        return $stmt;
    }

    // Get leases by tenant
    public function getByTenant($tenant_id) {
        $query = "SELECT l.*, p.title as property_title 
                 FROM " . $this->table_name . " l
                 LEFT JOIN properties p ON l.property_id = p.property_id
                 WHERE l.tenant_id = ?
                 ORDER BY l.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $tenant_id);
        $stmt->execute();

        return $stmt;
    }

    // Create lease
    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . "
                 (property_id, tenant_id, start_date, end_date, monthly_rent,
                  security_deposit, status, created_at, updated_at)
                 VALUES
                 (:property_id, :tenant_id, :start_date, :end_date, :monthly_rent,
                  :security_deposit, :status, NOW(), NOW())";

        $stmt = $this->conn->prepare($query);

        // Sanitize input
        $this->property_id = htmlspecialchars(strip_tags($data["property_id"]));
        $this->tenant_id = htmlspecialchars(strip_tags($data["tenant_id"]));
        $this->start_date = htmlspecialchars(strip_tags($data["start_date"]));
        $this->end_date = htmlspecialchars(strip_tags($data["end_date"]));
        $this->monthly_rent = htmlspecialchars(strip_tags($data["monthly_rent"]));
        $this->security_deposit = htmlspecialchars(strip_tags($data["security_deposit"]));
        $this->status = htmlspecialchars(strip_tags($data["status"]));

        // Bind values
        $stmt->bindParam(":property_id", $this->property_id);
        $stmt->bindParam(":tenant_id", $this->tenant_id);
        $stmt->bindParam(":start_date", $this->start_date);
        $stmt->bindParam(":end_date", $this->end_date);
        $stmt->bindParam(":monthly_rent", $this->monthly_rent);
        $stmt->bindParam(":security_deposit", $this->security_deposit);
        $stmt->bindParam(":status", $this->status);

        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Update lease
    public function update($lease_id, $data) {
        $query = "UPDATE " . $this->table_name . "
                 SET start_date = :start_date,
                     end_date = :end_date,
                     monthly_rent = :monthly_rent,
                     security_deposit = :security_deposit,
                     status = :status,
                     updated_at = NOW()
                 WHERE lease_id = :lease_id";

        $stmt = $this->conn->prepare($query);

        // Sanitize input
        $this->lease_id = htmlspecialchars(strip_tags($lease_id));
        $this->start_date = htmlspecialchars(strip_tags($data["start_date"]));
        $this->end_date = htmlspecialchars(strip_tags($data["end_date"]));
        $this->monthly_rent = htmlspecialchars(strip_tags($data["monthly_rent"]));
        $this->security_deposit = htmlspecialchars(strip_tags($data["security_deposit"]));
        $this->status = htmlspecialchars(strip_tags($data["status"]));

        // Bind values
        $stmt->bindParam(":lease_id", $this->lease_id);
        $stmt->bindParam(":start_date", $this->start_date);
        $stmt->bindParam(":end_date", $this->end_date);
        $stmt->bindParam(":monthly_rent", $this->monthly_rent);
        $stmt->bindParam(":security_deposit", $this->security_deposit);
        $stmt->bindParam(":status", $this->status);

        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Delete lease
    public function delete($lease_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE lease_id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $lease_id);

        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Get active leases
    public function getActive() {
        $query = "SELECT l.*, p.title as property_title, u.full_name as tenant_name 
                 FROM " . $this->table_name . " l
                 LEFT JOIN properties p ON l.property_id = p.property_id
                 LEFT JOIN users u ON l.tenant_id = u.user_id
                 WHERE l.status = 'active'
                 AND l.start_date <= CURDATE() AND l.end_date >= CURDATE()
                 ORDER BY l.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    // Get expired leases
    public function getExpired() {
        $query = "SELECT l.*, p.title as property_title, u.full_name as tenant_name 
                 FROM " . $this->table_name . " l
                 LEFT JOIN properties p ON l.property_id = p.property_id
                 LEFT JOIN users u ON l.tenant_id = u.user_id
                 WHERE l.status = 'active' AND l.end_date < CURDATE()
                 ORDER BY l.end_date DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    // Get upcoming leases
    public function getUpcoming() {
        $query = "SELECT l.*, p.title as property_title, u.full_name as tenant_name 
                 FROM " . $this->table_name . " l
                 LEFT JOIN properties p ON l.property_id = p.property_id
                 LEFT JOIN users u ON l.tenant_id = u.user_id
                 WHERE l.status = 'active' AND l.start_date > CURDATE()
                 ORDER BY l.start_date ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }
}
?> 