<?php
// api/models/product.php

class Product {
    // DB stuff
    private $conn;
    private $table = 'product';

    // Product Properties
    public $productCode;
    public $productName;
    public $description;
    public $price;

    // Constructor with DB connection
    public function __construct($db) {
        $this->conn = $db;
    }

    // Read all products
    public function read() {
        $query = 'SELECT * FROM ' . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Create product
    public function create() {
        $query = 'INSERT INTO ' . $this->table . ' 
                  (productCode, productName, description, price)
                  VALUES (:productCode, :productName, :description, :price)';

        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->productCode = htmlspecialchars(strip_tags($this->productCode));
        $this->productName = htmlspecialchars(strip_tags($this->productName));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->price = htmlspecialchars(strip_tags($this->price));

        // Bind data
        $stmt->bindParam(':productCode', $this->productCode);
        $stmt->bindParam(':productName', $this->productName);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':price', $this->price);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}
?>



