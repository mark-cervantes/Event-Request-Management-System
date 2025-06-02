<?php
// api/models/worker.php
// author: Nathaniel Jr. S. Alcantara
// date: 2025-03-16

class Worker {
    private $conn;
    private $table = 'worker';

    // Worker Properties
    public $workerID;
    public $firstName;
    public $middleName;
    public $lastName;
    public $birthDate;
    public $gender;
    public $maritalStatus;
    public $address;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Read all workers
    public function read() {
        $query = 'SELECT * FROM ' . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Read a single worker
    public function read_single() {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE workerID = ? LIMIT 0,1';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->workerID);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->workerID = $row['workerID'];
            $this->firstName = $row['firstName'];
            $this->middleName = $row['middleName'];
            $this->lastName = $row['lastName'];
            $this->birthDate = $row['birthDate'];
            $this->gender = $row['gender'];
            $this->maritalStatus = $row['maritalStatus'];
            $this->address = $row['address'];
        }
    }

    // Create Worker
    public function create() {
        // Create query
        $query = 'INSERT INTO ' . $this->table . '
                  SET
                    workerID = :workerID,
                    firstName = :firstName,
                    middleName = :middleName,
                    lastName = :lastName,
                    birthDate = :birthDate,
                    gender = :gender,
                    maritalStatus = :maritalStatus,
                    address = :address';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->workerID = htmlspecialchars(strip_tags($this->workerID));
        $this->firstName = htmlspecialchars(strip_tags($this->firstName));
        $this->middleName = htmlspecialchars(strip_tags($this->middleName));
        $this->lastName = htmlspecialchars(strip_tags($this->lastName));
        $this->birthDate = htmlspecialchars(strip_tags($this->birthDate));
        $this->gender = htmlspecialchars(strip_tags($this->gender));
        $this->maritalStatus = htmlspecialchars(strip_tags($this->maritalStatus));
        $this->address = htmlspecialchars(strip_tags($this->address));

        // Bind data
        $stmt->bindParam(':workerID', $this->workerID);
        $stmt->bindParam(':firstName', $this->firstName);
        $stmt->bindParam(':middleName', $this->middleName);
        $stmt->bindParam(':lastName', $this->lastName);
        $stmt->bindParam(':birthDate', $this->birthDate);
        $stmt->bindParam(':gender', $this->gender);
        $stmt->bindParam(':maritalStatus', $this->maritalStatus);
        $stmt->bindParam(':address', $this->address);

        // Execute query
        if ($stmt->execute()) {
            return true;
        }

        // Print error if something goes wrong
        printf("Error: %s.\n", $stmt->errorInfo());
        return false;
    }

    // Update Worker
    public function update() {
        $query = 'UPDATE ' . $this->table . '
                  SET
                    firstName = :firstName,
                    middleName = :middleName,
                    lastName = :lastName,
                    birthDate = :birthDate,
                    gender = :gender,
                    maritalStatus = :maritalStatus,
                    address = :address
                  WHERE
                   workerID = :workerID';

        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->firstName = htmlspecialchars(strip_tags($this->firstName));
        $this->middleName = htmlspecialchars(strip_tags($this->middleName));
        $this->lastName = htmlspecialchars(strip_tags($this->lastName));
        $this->birthDate = htmlspecialchars(strip_tags($this->birthDate));
        $this->gender = htmlspecialchars(strip_tags($this->gender));
        $this->maritalStatus = htmlspecialchars(strip_tags($this->maritalStatus));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->workerID = htmlspecialchars(strip_tags($this->workerID));

        // Bind data
        $stmt->bindParam(':workerID', $this->workerID);
        $stmt->bindParam(':firstName', $this->firstName);
        $stmt->bindParam(':middleName', $this->middleName);
        $stmt->bindParam(':lastName', $this->lastName);
        $stmt->bindParam(':birthDate', $this->birthDate);
        $stmt->bindParam(':gender', $this->gender);
        $stmt->bindParam(':maritalStatus', $this->maritalStatus);
        $stmt->bindParam(':address', $this->address);

        // Execute query
        if ($stmt->execute()) {
            return true;
        }

        // Print error if something goes wrong
        printf("Error: %s.\n", $stmt->errorInfo());
        return false;
    }

    //Delete Worker
    public function delete() {
        $query = 'DELETE FROM ' . $this->table . ' WHERE workerID = :workerID';

        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->workerID = htmlspecialchars(strip_tags($this->workerID));

        // Bind data
        $stmt->bindParam(':workerID', $this->workerID);

        // Execute query
        if ($stmt->execute()) {
            return true;
        }

        // Print error if something goes wrong
        printf("Error: %s.\n", $stmt->errorInfo());
        return false;

    }
}
?>



