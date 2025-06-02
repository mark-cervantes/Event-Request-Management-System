<?php
// api/model/resident.php
// author: Aldrin Danila

class Resident
{
    private $conn;
    private $table = 'resident';

    // Worker (Resident) Properties
    public $resident_id;
    public $first_name;
    public $middle_name;
    public $last_name;
    public $contact_number;
    public $address;
    public $birth_date;
    public $gender;
    public $civil_status;
    public $household_no;
    public $religion;
    public $status;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function read()
    {
        $query = 'SELECT * FROM ' . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }


    public function read_single()
    {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE resident_id = ? LIMIT 1';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->resident_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->resident_id = $row['resident_id'];
            $this->first_name = $row['first_name'];
            $this->middle_name = $row['middle_name'];
            $this->last_name = $row['last_name'];
            $this->contact_number = $row['contact_number'];
            $this->address = $row['address'];
            $this->birth_date = $row['birth_date'];
            $this->gender = $row['gender'];
            $this->civil_status = $row['civil_status'];
            $this->household_no = $row['household_no'];
            $this->religion = $row['religion'];
            $this->status = $row['status'];
            return $row;
       }
       return false;
    }

    public function create()
    {
        $query = 'INSERT INTO ' . $this->table . '
        SET
            resident_id = :resident_id,
            first_name = :first_name,
            middle_name = :middle_name,
            last_name = :last_name,
            contact_number = :contact_number,
            address = :address,
            birth_date = :birth_date,
            gender = :gender,
            civil_status = :civil_status,
            household_no = :household_no,
            religion = :religion,
            status =:status';

        $stmt = $this->conn->prepare($query);

        // Clean only expected fields
        $this->resident_id = htmlspecialchars(strip_tags($this->resident_id));
        $this->first_name = htmlspecialchars(strip_tags($this->first_name));
        $this->middle_name = htmlspecialchars(strip_tags($this->middle_name));
        $this->last_name = htmlspecialchars(strip_tags($this->last_name));
        $this->contact_number = htmlspecialchars(strip_tags($this->contact_number));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->birth_date = htmlspecialchars(strip_tags($this->birth_date));
        $this->gender = htmlspecialchars(strip_tags($this->gender));
        $this->civil_status = htmlspecialchars(strip_tags($this->civil_status));
        $this->household_no = htmlspecialchars(strip_tags($this->household_no));
        $this->religion = htmlspecialchars(strip_tags($this->religion));
        $this->status = htmlspecialchars(strip_tags($this->status));

        // Bind data
        $stmt->bindParam(':resident_id', $this->resident_id);
        $stmt->bindParam(':first_name', $this->first_name);
        $stmt->bindParam(':middle_name', $this->middle_name);
        $stmt->bindParam(':last_name', $this->last_name);
        $stmt->bindParam(':contact_number', $this->contact_number);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':birth_date', $this->birth_date);
        $stmt->bindParam(':gender', $this->gender);
        $stmt->bindParam(':civil_status', $this->civil_status);
        $stmt->bindParam(':household_no', $this->household_no);
        $stmt->bindParam(':religion', $this->religion);
        $stmt->bindParam(':status', $this->status);
        

        if ($stmt->execute()) {
            return true;
        }

        printf("Error: %s.\n", $stmt->error);
        return false;
    }

    public function update()
    {
        $query = 'UPDATE ' . $this->table . '
        SET
            first_name = :first_name,
            middle_name = :middle_name,
            last_name = :last_name,
            contact_number = :contact_number,
            address = :address,
            birth_date = :birth_date,
            gender = :gender,
            civil_status = :civil_status,
            household_no = :household_no,
            religion = :religion,
            status =:status
        WHERE
            resident_id = :resident_id';

        $stmt = $this->conn->prepare($query);

        // Clean only expected fields (avoid $this->conn!)
        $this->resident_id = htmlspecialchars(strip_tags($this->resident_id));
        $this->first_name = htmlspecialchars(strip_tags($this->first_name));
        $this->middle_name = htmlspecialchars(strip_tags($this->middle_name));
        $this->last_name = htmlspecialchars(strip_tags($this->last_name));
        $this->contact_number = htmlspecialchars(strip_tags($this->contact_number));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->birth_date = htmlspecialchars(strip_tags($this->birth_date));
        $this->gender = htmlspecialchars(strip_tags($this->gender));
        $this->civil_status = htmlspecialchars(strip_tags($this->civil_status));
        $this->household_no = htmlspecialchars(strip_tags($this->household_no));
        $this->religion = htmlspecialchars(strip_tags($this->religion));
        $this->status = htmlspecialchars(strip_tags($this->status));

        // Bind values
        $stmt->bindParam(':resident_id', $this->resident_id);
        $stmt->bindParam(':first_name', $this->first_name);
        $stmt->bindParam(':middle_name', $this->middle_name);
        $stmt->bindParam(':last_name', $this->last_name);
        $stmt->bindParam(':contact_number', $this->contact_number);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':birth_date', $this->birth_date);
        $stmt->bindParam(':gender', $this->gender);
        $stmt->bindParam(':civil_status', $this->civil_status);
        $stmt->bindParam(':household_no', $this->household_no);
        $stmt->bindParam(':religion', $this->religion);
        $stmt->bindParam(':status', $this->status);

        if ($stmt->execute()) {
            return true;
        }

        printf("Error: %s.\n", $stmt->error);
        return false;
    }

    public function delete()
    {
        $query = 'DELETE FROM ' . $this->table . ' WHERE resident_id = :resident_id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':resident_id', $this->resident_id);

        if ($stmt->execute()) {
            return true;
        }

        printf("Error: %s.\n", $stmt->error);
        return false;
    }
    public function archive()
    {
        $query = "UPDATE " . $this->table . " SET status = 'archived' WHERE resident_id = :resident_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':resident_id', $this->resident_id);

        return $stmt->execute();
    }
}
