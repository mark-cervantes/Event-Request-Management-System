<?php
// api/models/admin.php
// author: Aldrin Danila

class Admin {
    private $conn;
    private $table = 'admin';

    // Admin Properties
    public $admin_id;
    public $username;
    public $password;
    public $email;
    public $created_at;
    public $updated_at;
    public $official_id;

    public function __construct($db){
        $this->conn = $db;
    }

    public function read(){
        $query = 'SELECT * FROM ' . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function read_single(){
        $query = 'SELECT * FROM ' . $this->table . ' WHERE admin_id = ? LIMIT 0,1';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->admin_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->admin_id = $row['admin_id'];
            $this->username = $row['username'];
            $this->password = $row['password']; 
            $this->email = $row['email'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            $this->official_id = $row['official_id'];
        }
    }

    public function create(){
        $query = 'INSERT INTO ' . $this->table . '
            SET
                admin_id = :admin_id,
                username = :username,
                password = :password,
                email = :email,
                created_at = :created_at,
                updated_at = :updated_at,
                official_id = :official_id';

        $stmt = $this->conn->prepare($query);

        $this->admin_id = htmlspecialchars(strip_tags($this->admin_id));
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->created_at = htmlspecialchars(strip_tags($this->created_at));
        $this->updated_at = htmlspecialchars(strip_tags($this->updated_at));
        $this->official_id = htmlspecialchars(strip_tags($this->official_id));

        $stmt->bindParam(':admin_id', $this->admin_id);
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':created_at', $this->created_at);
        $stmt->bindParam(':updated_at', $this->updated_at);
        $stmt->bindParam(':official_id', $this->official_id);

        if ($stmt->execute()) {
            return true;
        }

        printf("Error: %s.\n", $stmt->error);
        return false;
    }

    public function update(){
        $query = 'UPDATE ' . $this->table . '
            SET
                username = :username,
                password = :password,
                email = :email,
                created_at = :created_at,
                updated_at = :updated_at,
                official_id = :official_id
            WHERE
                admin_id = :admin_id';

        $stmt = $this->conn->prepare($query);

        $this->admin_id = htmlspecialchars(strip_tags($this->admin_id));
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->created_at = htmlspecialchars(strip_tags($this->created_at));
        $this->updated_at = htmlspecialchars(strip_tags($this->updated_at));
        $this->official_id = htmlspecialchars(strip_tags($this->official_id));

        $stmt->bindParam(':admin_id', $this->admin_id);
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':created_at', $this->created_at);
        $stmt->bindParam(':updated_at', $this->updated_at);
        $stmt->bindParam(':official_id', $this->official_id);

        if ($stmt->execute()) {
            return true;
        }

        printf("Error: %s.\n", $stmt->error);
        return false;
    }

    public function delete(){
        $query = 'DELETE FROM ' . $this->table . ' WHERE admin_id = :admin_id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':admin_id', $this->admin_id);

        if ($stmt->execute()) {
            return true;
        }

        printf("Error: %s.\n", $stmt->error);
        return false;
    }
}
?>
