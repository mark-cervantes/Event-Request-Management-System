<?php
// api/models/reservation.php

class Reservation {
    // DB stuff
    private $conn;
    private $table = 'reservations';

    // Reservation Properties
    public $id;
    public $user_id;
    public $official_id;
    public $facility;
    public $event_name;
    public $start_datetime;
    public $end_datetime;
    public $date_time_requested;
    public $status;
    public $requested_by;
    public $contact_number;
    public $purpose;
    public $expected_attendees;
    public $created_at;
    public $updated_at;

    // Constructor with DB connection
    public function __construct($db) {
        $this->conn = $db;
    }

    // Read all reservations
    public function read() {
        $query = 'SELECT r.*, 
                         CONCAT(o.first_name, " ", o.last_name) as official_name,
                         o.position_title as official_position,
                         u.username as created_by_username,
                         CONCAT(res.first_name, " ", res.last_name) as created_by_name
                  FROM ' . $this->table . ' r 
                  LEFT JOIN officials o ON r.official_id = o.id 
                  LEFT JOIN users u ON r.user_id = u.user_id
                  LEFT JOIN resident res ON u.resident_id = res.resident_id
                  ORDER BY r.created_at DESC';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Read single reservation
    public function read_single() {
        $query = 'SELECT r.*, 
                         CONCAT(o.first_name, " ", o.last_name) as official_name,
                         o.position_title as official_position,
                         u.username as created_by_username,
                         CONCAT(res.first_name, " ", res.last_name) as created_by_name
                  FROM ' . $this->table . ' r 
                  LEFT JOIN officials o ON r.official_id = o.id 
                  LEFT JOIN users u ON r.user_id = u.user_id
                  LEFT JOIN resident res ON u.resident_id = res.resident_id
                  WHERE r.id = :id LIMIT 1';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->id = $row['id'];
            $this->user_id = $row['user_id'];
            $this->official_id = $row['official_id'];
            $this->facility = $row['facility'];
            $this->event_name = $row['event_name'];
            $this->start_datetime = $row['start_datetime'];
            $this->end_datetime = $row['end_datetime'];
            $this->date_time_requested = $row['date_time_requested'];
            $this->status = $row['status'];
            $this->requested_by = $row['requested_by'];
            $this->contact_number = $row['contact_number'];
            $this->purpose = $row['purpose'];
            $this->expected_attendees = $row['expected_attendees'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            return true;
        }
        return false;
    }

    // Create reservation
    public function create() {
        $query = 'INSERT INTO ' . $this->table . ' 
                  (user_id, official_id, facility, event_name, start_datetime, end_datetime, 
                   status, requested_by, contact_number, purpose, expected_attendees)
                  VALUES (:user_id, :official_id, :facility, :event_name, :start_datetime, :end_datetime,
                          :status, :requested_by, :contact_number, :purpose, :expected_attendees)';

        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->user_id = $this->user_id ? htmlspecialchars(strip_tags($this->user_id)) : null;
        $this->official_id = $this->official_id ? htmlspecialchars(strip_tags($this->official_id)) : null;
        $this->facility = htmlspecialchars(strip_tags($this->facility));
        $this->event_name = htmlspecialchars(strip_tags($this->event_name));
        $this->start_datetime = htmlspecialchars(strip_tags($this->start_datetime));
        $this->end_datetime = htmlspecialchars(strip_tags($this->end_datetime));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->requested_by = htmlspecialchars(strip_tags($this->requested_by));
        $this->contact_number = htmlspecialchars(strip_tags($this->contact_number));
        $this->purpose = htmlspecialchars(strip_tags($this->purpose));
        $this->expected_attendees = htmlspecialchars(strip_tags($this->expected_attendees));

        // Bind data
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':official_id', $this->official_id);
        $stmt->bindParam(':facility', $this->facility);
        $stmt->bindParam(':event_name', $this->event_name);
        $stmt->bindParam(':start_datetime', $this->start_datetime);
        $stmt->bindParam(':end_datetime', $this->end_datetime);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':requested_by', $this->requested_by);
        $stmt->bindParam(':contact_number', $this->contact_number);
        $stmt->bindParam(':purpose', $this->purpose);
        $stmt->bindParam(':expected_attendees', $this->expected_attendees);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Update reservation
    public function update() {
        $query = 'UPDATE ' . $this->table . ' 
                  SET user_id = :user_id,
                      official_id = :official_id,
                      facility = :facility,
                      event_name = :event_name,
                      start_datetime = :start_datetime,
                      end_datetime = :end_datetime,
                      status = :status,
                      requested_by = :requested_by,
                      contact_number = :contact_number,
                      purpose = :purpose,
                      expected_attendees = :expected_attendees
                  WHERE id = :id';

        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->official_id = htmlspecialchars(strip_tags($this->official_id));
        $this->facility = htmlspecialchars(strip_tags($this->facility));
        $this->event_name = htmlspecialchars(strip_tags($this->event_name));
        $this->start_datetime = htmlspecialchars(strip_tags($this->start_datetime));
        $this->end_datetime = htmlspecialchars(strip_tags($this->end_datetime));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->requested_by = htmlspecialchars(strip_tags($this->requested_by));
        $this->contact_number = htmlspecialchars(strip_tags($this->contact_number));
        $this->purpose = htmlspecialchars(strip_tags($this->purpose));
        $this->expected_attendees = htmlspecialchars(strip_tags($this->expected_attendees));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Bind data
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':official_id', $this->official_id);
        $stmt->bindParam(':facility', $this->facility);
        $stmt->bindParam(':event_name', $this->event_name);
        $stmt->bindParam(':start_datetime', $this->start_datetime);
        $stmt->bindParam(':end_datetime', $this->end_datetime);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':requested_by', $this->requested_by);
        $stmt->bindParam(':contact_number', $this->contact_number);
        $stmt->bindParam(':purpose', $this->purpose);
        $stmt->bindParam(':expected_attendees', $this->expected_attendees);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Delete reservation
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

    // Update reservation status
    public function updateStatus() {
        $query = 'UPDATE ' . $this->table . ' 
                  SET status = :status,
                      official_id = :official_id
                  WHERE id = :id';

        $stmt = $this->conn->prepare($query);

        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->official_id = htmlspecialchars(strip_tags($this->official_id));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':official_id', $this->official_id);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Search reservations
    public function search($keywords) {
        $query = 'SELECT r.*, 
                         CONCAT(o.first_name, " ", o.last_name) as official_name,
                         o.position_title as official_position,
                         u.username as created_by_username,
                         CONCAT(res.first_name, " ", res.last_name) as created_by_name
                  FROM ' . $this->table . ' r 
                  LEFT JOIN officials o ON r.official_id = o.id 
                  LEFT JOIN users u ON r.user_id = u.user_id
                  LEFT JOIN resident res ON u.resident_id = res.resident_id
                  WHERE r.event_name LIKE :keywords 
                     OR r.requested_by LIKE :keywords 
                     OR r.facility LIKE :keywords
                  ORDER BY r.created_at DESC';

        $stmt = $this->conn->prepare($query);
        $keywords = "%{$keywords}%";
        $stmt->bindParam(':keywords', $keywords);
        $stmt->execute();

        return $stmt;
    }

    // Get reservations by status
    public function getByStatus($status) {
        $query = 'SELECT r.*, 
                         CONCAT(o.first_name, " ", o.last_name) as official_name,
                         o.position_title as official_position,
                         u.username as created_by_username,
                         CONCAT(res.first_name, " ", res.last_name) as created_by_name
                  FROM ' . $this->table . ' r 
                  LEFT JOIN officials o ON r.official_id = o.id 
                  LEFT JOIN users u ON r.user_id = u.user_id
                  LEFT JOIN resident res ON u.resident_id = res.resident_id
                  WHERE r.status = :status
                  ORDER BY r.created_at DESC';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        return $stmt;
    }

    // Get reservations by facility
    public function getByFacility($facility) {
        $query = 'SELECT r.*, 
                         CONCAT(o.first_name, " ", o.last_name) as official_name,
                         o.position_title as official_position,
                         u.username as created_by_username,
                         CONCAT(res.first_name, " ", res.last_name) as created_by_name
                  FROM ' . $this->table . ' r 
                  LEFT JOIN officials o ON r.official_id = o.id 
                  LEFT JOIN users u ON r.user_id = u.user_id
                  LEFT JOIN resident res ON u.resident_id = res.resident_id
                  WHERE r.facility = :facility
                  ORDER BY r.start_datetime ASC';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':facility', $facility);
        $stmt->execute();
        return $stmt;
    }

    // Get upcoming reservations
    public function getUpcoming() {
        $query = 'SELECT r.*, 
                         CONCAT(o.first_name, " ", o.last_name) as official_name,
                         o.position_title as official_position,
                         u.username as created_by_username,
                         CONCAT(res.first_name, " ", res.last_name) as created_by_name
                  FROM ' . $this->table . ' r 
                  LEFT JOIN officials o ON r.official_id = o.id 
                  LEFT JOIN users u ON r.user_id = u.user_id
                  LEFT JOIN resident res ON u.resident_id = res.resident_id
                  WHERE r.start_datetime >= NOW() 
                    AND r.status IN ("Approved", "Pending")
                  ORDER BY r.start_datetime ASC';

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Check for conflicting reservations
    public function checkConflict() {
        $query = 'SELECT COUNT(*) as conflict_count
                  FROM ' . $this->table . ' 
                  WHERE facility = :facility 
                    AND status IN ("Approved", "Pending")
                    AND (
                        (start_datetime <= :start_datetime AND end_datetime > :start_datetime) OR
                        (start_datetime < :end_datetime AND end_datetime >= :end_datetime) OR
                        (start_datetime >= :start_datetime AND end_datetime <= :end_datetime)
                    )';
        
        if (!empty($this->id)) {
            $query .= ' AND id != :id';
        }

        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':facility', $this->facility);
        $stmt->bindParam(':start_datetime', $this->start_datetime);
        $stmt->bindParam(':end_datetime', $this->end_datetime);
        
        if (!empty($this->id)) {
            $stmt->bindParam(':id', $this->id);
        }

        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row['conflict_count'] > 0;
    }

    // Validate if user exists
    public function validateUser($user_id) {
        if (empty($user_id)) return true; // Allow null user_id
        
        $query = 'SELECT COUNT(*) as count FROM users WHERE user_id = :user_id AND status = "active"';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'] > 0;
    }

    // Validate if official exists
    public function validateOfficial($official_id) {
        if (empty($official_id)) return true; // Allow null official_id
        
        $query = 'SELECT COUNT(*) as count FROM officials WHERE id = :official_id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':official_id', $official_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'] > 0;
    }

    // Get reservations by user
    public function getByUser($user_id) {
        $query = 'SELECT r.*, 
                         CONCAT(o.first_name, " ", o.last_name) as official_name,
                         o.position_title as official_position,
                         u.username as created_by_username,
                         CONCAT(res.first_name, " ", res.last_name) as created_by_name
                  FROM ' . $this->table . ' r 
                  LEFT JOIN officials o ON r.official_id = o.id 
                  LEFT JOIN users u ON r.user_id = u.user_id
                  LEFT JOIN resident res ON u.resident_id = res.resident_id
                  WHERE r.user_id = :user_id
                  ORDER BY r.created_at DESC';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt;
    }

    // Get reservations by official
    public function getByOfficial($official_id) {
        $query = 'SELECT r.*, 
                         CONCAT(o.first_name, " ", o.last_name) as official_name,
                         o.position_title as official_position,
                         u.username as created_by_username,
                         CONCAT(res.first_name, " ", res.last_name) as created_by_name
                  FROM ' . $this->table . ' r 
                  LEFT JOIN officials o ON r.official_id = o.id 
                  LEFT JOIN users u ON r.user_id = u.user_id
                  LEFT JOIN resident res ON u.resident_id = res.resident_id
                  WHERE r.official_id = :official_id
                  ORDER BY r.created_at DESC';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':official_id', $official_id);
        $stmt->execute();
        return $stmt;
    }
}
?>
