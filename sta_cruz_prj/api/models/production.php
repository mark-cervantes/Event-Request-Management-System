<?php
// api/models/production.php
// author: Nathaniel Jr. S. Alcantara
// date: 2025-03-16

class Production {
    private $conn;
    private $table = 'production';

    // Production Properties
    public $productionNo;
    public $workerID;
    public $productCode;
    public $quantity;
    public $productionDate;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Read all productions
    public function read() {
        $query = 'SELECT * FROM ' . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Read a single production
    public function read_single() {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE productionNo = ? LIMIT 0,1';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->productionNo);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->productionNo = $row['productionNo'];
            $this->workerID = $row['workerID'];
            $this->productCode = $row['productCode'];
            $this->quantity = $row['quantity'];
            $this->productionDate = $row['productionDate'];
            return true;
        }

        return false;
    }

    // Create production
    public function create() {
        $query = 'INSERT INTO ' . $this->table . '
                  SET
                    productionNo = :productionNo,
                    workerID = :workerID,
                    productCode = :productCode,
                    quantity = :quantity,
                    productionDate = :productionDate';

        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->productionNo = htmlspecialchars(strip_tags($this->productionNo));
        $this->workerID = htmlspecialchars(strip_tags($this->workerID));
        $this->productCode = htmlspecialchars(strip_tags($this->productCode));
        $this->quantity = htmlspecialchars(strip_tags($this->quantity));
        $this->productionDate = htmlspecialchars(strip_tags($this->productionDate));

        // Bind data
        $stmt->bindParam(':productionNo', $this->productionNo);
        $stmt->bindParam(':workerID', $this->workerID);
        $stmt->bindParam(':productCode', $this->productCode);
        $stmt->bindParam(':quantity', $this->quantity);
        $stmt->bindParam(':productionDate', $this->productionDate);

        if ($stmt->execute()) {
            return true;
        }

        printf("Error: %s.\n", $stmt->errorInfo()[2]);
        return false;
    }

    // Update production
    public function update() {
        $query = 'UPDATE ' . $this->table . '
                  SET
                    workerID = :workerID,
                    productCode = :productCode,
                    quantity = :quantity,
                    productionDate = :productionDate
                  WHERE
                    productionNo = :productionNo';

        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->productionNo = htmlspecialchars(strip_tags($this->productionNo));
        $this->workerID = htmlspecialchars(strip_tags($this->workerID));
        $this->productCode = htmlspecialchars(strip_tags($this->productCode));
        $this->quantity = htmlspecialchars(strip_tags($this->quantity));
        $this->productionDate = htmlspecialchars(strip_tags($this->productionDate));

        // Bind data
        $stmt->bindParam(':productionNo', $this->productionNo);
        $stmt->bindParam(':workerID', $this->workerID);
        $stmt->bindParam(':productCode', $this->productCode);
        $stmt->bindParam(':quantity', $this->quantity);
        $stmt->bindParam(':productionDate', $this->productionDate);

        if ($stmt->execute()) {
            return true;
        }

        printf("Error: %s.\n", $stmt->errorInfo()[2]);
        return false;
    }

    // Delete production
    public function delete() {
        $query = 'DELETE FROM ' . $this->table . ' WHERE productionNo = :productionNo';
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->productionNo = htmlspecialchars(strip_tags($this->productionNo));

        // Bind data
        $stmt->bindParam(':productionNo', $this->productionNo);

        if ($stmt->execute()) {
            return true;
        }

        printf("Error: %s.\n", $stmt->errorInfo()[2]);
        return false;
    }
}
?>




