<?php
// api/models/official.php

class Official {
    // DB stuff
    private $conn;
    private $table = 'officials';

    // Official Properties
    public $id;
    public $first_name;
    public $middle_name;
    public $last_name;
    public $gender;
    public $birth_date;
    public $position_title;
    public $email;
    public $contact_number;
    public $start_term;
    public $end_term;
    public $created_at;
    public $updated_at;

    // Constructor with DB connection
    public function __construct($db) {
        $this->conn = $db;
    }

    // Read all officials
    public function read() {
        $query = 'SELECT * FROM ' . $this->table . ' ORDER BY created_at DESC';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Read single official
    public function read_single() {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE id = :id LIMIT 1';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->id = $row['id'];
            $this->first_name = $row['first_name'];
            $this->middle_name = $row['middle_name'];
            $this->last_name = $row['last_name'];
            $this->gender = $row['gender'];
            $this->birth_date = $row['birth_date'];
            $this->position_title = $row['position_title'];
            $this->email = $row['email'];
            $this->contact_number = $row['contact_number'];
            $this->start_term = $row['start_term'];
            $this->end_term = $row['end_term'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            return true;
        }
        return false;
    }

    // Create official
    public function create() {
        $query = 'INSERT INTO ' . $this->table . ' 
                  (first_name, middle_name, last_name, gender, birth_date, 
                   position_title, email, contact_number, start_term, end_term)
                  VALUES (:first_name, :middle_name, :last_name, :gender, :birth_date,
                          :position_title, :email, :contact_number, :start_term, :end_term)';

        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->first_name = htmlspecialchars(strip_tags($this->first_name));
        $this->middle_name = htmlspecialchars(strip_tags($this->middle_name));
        $this->last_name = htmlspecialchars(strip_tags($this->last_name));
        $this->gender = htmlspecialchars(strip_tags($this->gender));
        $this->birth_date = htmlspecialchars(strip_tags($this->birth_date));
        $this->position_title = htmlspecialchars(strip_tags($this->position_title));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->contact_number = htmlspecialchars(strip_tags($this->contact_number));
        $this->start_term = htmlspecialchars(strip_tags($this->start_term));
        $this->end_term = htmlspecialchars(strip_tags($this->end_term));

        // Bind data
        $stmt->bindParam(':first_name', $this->first_name);
        $stmt->bindParam(':middle_name', $this->middle_name);
        $stmt->bindParam(':last_name', $this->last_name);
        $stmt->bindParam(':gender', $this->gender);
        $stmt->bindParam(':birth_date', $this->birth_date);
        $stmt->bindParam(':position_title', $this->position_title);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':contact_number', $this->contact_number);
        $stmt->bindParam(':start_term', $this->start_term);
        $stmt->bindParam(':end_term', $this->end_term);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Update official
    public function update() {
        $query = 'UPDATE ' . $this->table . ' 
                  SET first_name = :first_name,
                      middle_name = :middle_name,
                      last_name = :last_name,
                      gender = :gender,
                      birth_date = :birth_date,
                      position_title = :position_title,
                      email = :email,
                      contact_number = :contact_number,
                      start_term = :start_term,
                      end_term = :end_term
                  WHERE id = :id';

        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->first_name = htmlspecialchars(strip_tags($this->first_name));
        $this->middle_name = htmlspecialchars(strip_tags($this->middle_name));
        $this->last_name = htmlspecialchars(strip_tags($this->last_name));
        $this->gender = htmlspecialchars(strip_tags($this->gender));
        $this->birth_date = htmlspecialchars(strip_tags($this->birth_date));
        $this->position_title = htmlspecialchars(strip_tags($this->position_title));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->contact_number = htmlspecialchars(strip_tags($this->contact_number));
        $this->start_term = htmlspecialchars(strip_tags($this->start_term));
        $this->end_term = htmlspecialchars(strip_tags($this->end_term));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Bind data
        $stmt->bindParam(':first_name', $this->first_name);
        $stmt->bindParam(':middle_name', $this->middle_name);
        $stmt->bindParam(':last_name', $this->last_name);
        $stmt->bindParam(':gender', $this->gender);
        $stmt->bindParam(':birth_date', $this->birth_date);
        $stmt->bindParam(':position_title', $this->position_title);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':contact_number', $this->contact_number);
        $stmt->bindParam(':start_term', $this->start_term);
        $stmt->bindParam(':end_term', $this->end_term);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Delete official
    public function delete() {
        $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';
        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Search officials by name or position
    public function search($keywords) {
        $query = 'SELECT * FROM ' . $this->table . ' 
                  WHERE first_name LIKE :keywords 
                     OR last_name LIKE :keywords 
                     OR position_title LIKE :keywords
                  ORDER BY created_at DESC';

        $stmt = $this->conn->prepare($query);
        $keywords = "%{$keywords}%";
        $stmt->bindParam(':keywords', $keywords);
        $stmt->execute();

        return $stmt;
    }

    // Get active officials (current term)
    public function getActiveOfficials() {
        $query = 'SELECT * FROM ' . $this->table . ' 
                  WHERE start_term <= CURDATE() 
                    AND (end_term IS NULL OR end_term >= CURDATE())
                  ORDER BY position_title ASC';

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>
