<?php
// api/model/complaint_form.php
// author: Aldrin Danila

class ComplaintForm
{
    private $conn;
    private $table = 'complaints';

    // Table columns as properties
    public $brngy_case_no;
    public $case_type;
    public $complainant_fullname;
    public $complainant_address;
    public $complainant_cellphone;
    public $respondent_fullname;
    public $respondent_address;
    public $respondent_cellphone;
    public $complaint_description;
    public $attachment_file;
    public $date_of_incident;
    public $submitted_date;
    public $status;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Read all complaints
    public function read()
    {
        $query = "SELECT * FROM " . $this->table . " ORDER BY submitted_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Read single complaint by brngy_case_no
    public function read_single()
    {
        $query = "SELECT * FROM " . $this->table . " WHERE brngy_case_no = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->brngy_case_no);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->brngy_case_no = $row['brngy_case_no'];
            $this->case_type = $row['case_type'];
            $this->complainant_fullname = $row['complainant_fullname'];
            $this->complainant_address = $row['complainant_address'];
            $this->complainant_cellphone = $row['complainant_cellphone'];
            $this->respondent_fullname = $row['respondent_fullname'];
            $this->respondent_address = $row['respondent_address'];
            $this->respondent_cellphone = $row['respondent_cellphone'];
            $this->complaint_description = $row['complaint_description'];
            $this->attachment_file = $row['attachment_file'];
            $this->date_of_incident = $row['date_of_incident'];
            $this->submitted_date = $row['submitted_date'];
            $this->status = $row['status'];
        }
    }

    // Create a new complaint
    public function create()
    {
        $query = "INSERT INTO " . $this->table . "
            SET
                brngy_case_no = :brngy_case_no,
                case_type = :case_type,
                complainant_fullname = :complainant_fullname,
                complainant_address = :complainant_address,
                complainant_cellphone = :complainant_cellphone,
                respondent_fullname = :respondent_fullname,
                respondent_address = :respondent_address,
                respondent_cellphone = :respondent_cellphone,
                complaint_description = :complaint_description,
                attachment_file = :attachment_file,
                date_of_incident = :date_of_incident";

        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->brngy_case_no = htmlspecialchars(strip_tags($this->brngy_case_no));
        $this->case_type = htmlspecialchars(strip_tags($this->case_type));
        $this->complainant_fullname = htmlspecialchars(strip_tags($this->complainant_fullname));
        $this->complainant_address = htmlspecialchars(strip_tags($this->complainant_address));
        $this->complainant_cellphone = htmlspecialchars(strip_tags($this->complainant_cellphone));
        $this->respondent_fullname = htmlspecialchars(strip_tags($this->respondent_fullname));        $this->respondent_address = htmlspecialchars(strip_tags($this->respondent_address));
        $this->respondent_cellphone = htmlspecialchars(strip_tags($this->respondent_cellphone));
        $this->complaint_description = htmlspecialchars(strip_tags($this->complaint_description));
        $this->attachment_file = $this->attachment_file ? htmlspecialchars(strip_tags($this->attachment_file)) : null;
        $this->date_of_incident = htmlspecialchars(strip_tags($this->date_of_incident));

        // Bind data
        $stmt->bindParam(':brngy_case_no', $this->brngy_case_no);
        $stmt->bindParam(':case_type', $this->case_type);
        $stmt->bindParam(':complainant_fullname', $this->complainant_fullname);
        $stmt->bindParam(':complainant_address', $this->complainant_address);
        $stmt->bindParam(':complainant_cellphone', $this->complainant_cellphone);
        $stmt->bindParam(':respondent_fullname', $this->respondent_fullname);
        $stmt->bindParam(':respondent_address', $this->respondent_address);
        $stmt->bindParam(':respondent_cellphone', $this->respondent_cellphone);
        $stmt->bindParam(':complaint_description', $this->complaint_description);
        $stmt->bindParam(':attachment_file', $this->attachment_file);
        $stmt->bindParam(':date_of_incident', $this->date_of_incident);

        if ($stmt->execute()) {
            return true;
        }

        printf("Error: %s.\n", $stmt->errorInfo()[2]);
        return false;
    }

    // Update existing complaint by brngy_case_no
   public function updatePartial($data) {
    // Fetch existing complaint first
    $this->brngy_case_no = $data['brngy_case_no'];
    $this->read_single();

    if ($this->complainant_fullname === null) {
        // No record found
        return false;
    }

    // Use new values if provided, else keep old
    $case_type = $data['case_type'] ?? $this->case_type;
    $complainant_fullname = $data['complainant_fullname'] ?? $this->complainant_fullname;
    $complainant_address = $data['complainant_address'] ?? $this->complainant_address;
    $complainant_cellphone = $data['complainant_cellphone'] ?? $this->complainant_cellphone;
    $respondent_fullname = $data['respondent_fullname'] ?? $this->respondent_fullname;
    $respondent_address = $data['respondent_address'] ?? $this->respondent_address;
    $respondent_cellphone = $data['respondent_cellphone'] ?? $this->respondent_cellphone;
    $complaint_description = $data['complaint_description'] ?? $this->complaint_description;
    $attachment_file = $data['attachment_file'] ?? $this->attachment_file;
    $date_of_incident = $data['date_of_incident'] ?? $this->date_of_incident;
    $status = $data['status'] ?? $this->status;  // <-- Added status

    $query = "UPDATE " . $this->table . " SET
        case_type = :case_type,
        complainant_fullname = :complainant_fullname,
        complainant_address = :complainant_address,
        complainant_cellphone = :complainant_cellphone,
        respondent_fullname = :respondent_fullname,
        respondent_address = :respondent_address,
        respondent_cellphone = :respondent_cellphone,
        complaint_description = :complaint_description,
        attachment_file = :attachment_file,
        date_of_incident = :date_of_incident,
        status = :status
        WHERE brngy_case_no = :brngy_case_no";

    $stmt = $this->conn->prepare($query);

    // Bind parameters
    $stmt->bindParam(':case_type', $case_type);
    $stmt->bindParam(':complainant_fullname', $complainant_fullname);
    $stmt->bindParam(':complainant_address', $complainant_address);
    $stmt->bindParam(':complainant_cellphone', $complainant_cellphone);
    $stmt->bindParam(':respondent_fullname', $respondent_fullname);
    $stmt->bindParam(':respondent_address', $respondent_address);
    $stmt->bindParam(':respondent_cellphone', $respondent_cellphone);
    $stmt->bindParam(':complaint_description', $complaint_description);
    $stmt->bindParam(':attachment_file', $attachment_file);
    $stmt->bindParam(':date_of_incident', $date_of_incident);
    $stmt->bindParam(':status', $status);  // <-- Bind status
    $stmt->bindParam(':brngy_case_no', $this->brngy_case_no);

    return $stmt->execute();
}


    // Delete complaint by brngy_case_no
    public function delete()
    {
        $query = "DELETE FROM " . $this->table . " WHERE brngy_case_no = :brngy_case_no";
        $stmt = $this->conn->prepare($query);

        $this->brngy_case_no = htmlspecialchars(strip_tags($this->brngy_case_no));
        $stmt->bindParam(':brngy_case_no', $this->brngy_case_no);

        if ($stmt->execute()) {
            return true;
        }

        printf("Error: %s.\n", $stmt->errorInfo()[2]);
        return false;
    }
    
    //GENERATE NEXT CASE NUMUBER
    public function generateBrngyCaseNo() {
    $year = date('Y'); // current year, e.g., 2025

    // Find the last case number for the current year
    $query = "SELECT brngy_case_no FROM " . $this->table . " 
              WHERE brngy_case_no LIKE :year_pattern 
              ORDER BY brngy_case_no DESC LIMIT 1";

    $stmt = $this->conn->prepare($query);
    $year_pattern = "brgy-$year-%";
    $stmt->bindParam(':year_pattern', $year_pattern);
    $stmt->execute();

    $lastCaseNo = $stmt->fetchColumn();

    if ($lastCaseNo) {
        // Extract the last sequence number (e.g. from 'brgy-2025-07' get 07)
        $parts = explode('-', $lastCaseNo);
        $lastSeq = intval(end($parts));
        $newSeq = $lastSeq + 1;
    } else {
        // No case for this year yet, start at 1
        $newSeq = 1;
    }

    // Format sequence with leading zero (e.g., 01, 02)
    $newSeqFormatted = str_pad($newSeq, 2, '0', STR_PAD_LEFT);

    return "brgy-$year-$newSeqFormatted";
}

}