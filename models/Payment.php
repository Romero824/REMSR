<?php
class Payment {
    // Database connection and table name
    private $conn;
    private $table_name = "payments";

    // Object properties
    public $payment_id;
    public $lease_id;
    public $amount;
    public $payment_date;
    public $payment_method;
    public $status;
    public $created_at;
    public $updated_at;

    // Constructor with $db as database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    // Read all payments
    public function read() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Create payment
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    lease_id = :lease_id,
                    amount = :amount,
                    payment_date = :payment_date,
                    payment_method = :payment_method,
                    status = :status,
                    created_at = :created_at,
                    updated_at = :updated_at";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->lease_id = htmlspecialchars(strip_tags($this->lease_id));
        $this->amount = htmlspecialchars(strip_tags($this->amount));
        $this->payment_date = htmlspecialchars(strip_tags($this->payment_date));
        $this->payment_method = htmlspecialchars(strip_tags($this->payment_method));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->created_at = date('Y-m-d H:i:s');
        $this->updated_at = date('Y-m-d H:i:s');

        // Bind
        $stmt->bindParam(":lease_id", $this->lease_id);
        $stmt->bindParam(":amount", $this->amount);
        $stmt->bindParam(":payment_date", $this->payment_date);
        $stmt->bindParam(":payment_method", $this->payment_method);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":created_at", $this->created_at);
        $stmt->bindParam(":updated_at", $this->updated_at);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Read single payment
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE payment_id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->payment_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->lease_id = $row['lease_id'];
            $this->amount = $row['amount'];
            $this->payment_date = $row['payment_date'];
            $this->payment_method = $row['payment_method'];
            $this->status = $row['status'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            return true;
        }
        return false;
    }

    // Update payment
    public function update() {
        $query = "UPDATE " . $this->table_name . "
                SET
                    status = :status,
                    updated_at = :updated_at
                WHERE
                    payment_id = :payment_id";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->updated_at = date('Y-m-d H:i:s');
        $this->payment_id = htmlspecialchars(strip_tags($this->payment_id));

        // Bind
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":updated_at", $this->updated_at);
        $stmt->bindParam(":payment_id", $this->payment_id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Delete payment
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE payment_id = ?";
        $stmt = $this->conn->prepare($query);
        $this->payment_id = htmlspecialchars(strip_tags($this->payment_id));
        $stmt->bindParam(1, $this->payment_id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Read payments by lease
    public function readByLease($lease_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE lease_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $lease_id);
        $stmt->execute();
        return $stmt;
    }

    // Get payments by property owner
    public function getByLease($owner_id) {
        $query = "SELECT p.*, l.property_id, pr.title as property_title, u.full_name as tenant_name 
                 FROM " . $this->table_name . " p
                 LEFT JOIN leases l ON p.lease_id = l.lease_id
                 LEFT JOIN properties pr ON l.property_id = pr.property_id
                 LEFT JOIN users u ON l.tenant_id = u.user_id
                 WHERE pr.owner_id = ?
                 ORDER BY p.payment_date DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $owner_id);
        $stmt->execute();

        return $stmt;
    }
}
?> 