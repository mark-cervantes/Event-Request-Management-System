    <?php
    // api/model/users.php
    // author: Aldrin Danila

    class users
    {
        private $conn;
        private $table = 'users';

        // User Table Properties
        public $user_id;
        public $username;
        public $password;
        public $email;
        public $created_at;
        public $updated_at;
        public $role;
        public $status;
        public $resident_id;
        public $start_term;
        public $end_term;
        public $position_title;

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
            $query = 'SELECT * FROM ' . $this->table . ' WHERE user_id = ? LIMIT 0,1';
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $this->user_id);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                $this->user_id = $row['user_id'];
                $this->username = $row['username'];
                $this->password = $row['password'];
                $this->email = $row['email'];
                $this->created_at = $row['created_at'];
                $this->updated_at = $row['updated_at'];
                $this->role = $row['role'];
                $this->status = $row['status'];
                $this->resident_id = $row['resident_id'];
                $this->start_term = $row['start_term'];
                $this->end_term = $row['end_term'];
                $this->position_title = $row['position_title'];


            }
        }

        public function create()
        {
            $query = 'INSERT INTO ' . $this->table . '
                SET
                    user_id = :user_id,
                    username = :username,
                    password = :password,
                    email = :email,
                    created_at = :created_at,
                    updated_at = :updated_at,
                    role = :role,
                    status = :status,
                    resident_id = :resident_id,
                    start_term = :start_term,
                    end_term = :end_term,
                    position_title = :position_title';
                    

            $stmt = $this->conn->prepare($query);

            // Clean data
            $this->user_id = htmlspecialchars(strip_tags($this->user_id));
            $this->username = htmlspecialchars(strip_tags($this->username));
            $this->password = htmlspecialchars(strip_tags($this->password));
            $this->email = htmlspecialchars(strip_tags($this->email));
            $this->created_at = date('Y-m-d H:i:s');
            $this->updated_at = date('Y-m-d H:i:s');
            $this->role = htmlspecialchars(strip_tags($this->role));
            $this->status = htmlspecialchars(strip_tags($this->status));
            $this->resident_id = htmlspecialchars(strip_tags($this->resident_id));
            $this->start_term = htmlspecialchars(strip_tags($this->start_term));
            $this->end_term = htmlspecialchars(strip_tags($this->end_term));
            $this->position_title = htmlspecialchars(strip_tags($this->position_title));
      

            // Bind data
            $stmt->bindParam(':user_id', $this->user_id);
            $stmt->bindParam(':username', $this->username);
            $stmt->bindParam(':password', $this->password);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':created_at', $this->created_at);
            $stmt->bindParam(':updated_at', $this->updated_at);
            $stmt->bindParam(':role', $this->role);
            $stmt->bindParam(':status', $this->status);
            $stmt->bindParam(':resident_id', $this->resident_id);
            $stmt->bindParam(':start_term', $this->start_term);
            $stmt->bindParam(':end_term', $this->end_term);
            $stmt->bindParam(':position_title', $this->position_title);

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
                        username = :username,
                        email = :email,
                        password = :password,
                        updated_at = :updated_at,
                        role = :role,
                        status = :status,
                        resident_id = :resident_id,
                        start_term = :start_term,
                        end_term = :end_term,
                        position_title = :position_title
                    WHERE
                        user_id = :user_id';

            $stmt = $this->conn->prepare($query);

            // Clean data
            $this->user_id = htmlspecialchars(strip_tags($this->user_id));
            $this->username = htmlspecialchars(strip_tags($this->username));
            $this->password = htmlspecialchars(strip_tags($this->password));
            $this->email = htmlspecialchars(strip_tags($this->email));
            $this->updated_at = date('Y-m-d H:i:s');
            $this->role = htmlspecialchars(strip_tags($this->role));
            $this->status = htmlspecialchars(strip_tags($this->status));
            $this->resident_id = htmlspecialchars(strip_tags($this->resident_id));
            $this->start_term = htmlspecialchars(strip_tags($this->start_term));
            $this->end_term = htmlspecialchars(strip_tags($this->end_term));
            $this->position_title = htmlspecialchars(strip_tags($this->position_title));

            // Bind values
            $stmt->bindParam(':user_id', $this->user_id);
            $stmt->bindParam(':username', $this->username);
            $stmt->bindParam(':password', $this->password);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':updated_at', $this->updated_at);
            $stmt->bindParam(':role', $this->role);
            $stmt->bindParam(':status', $this->status);
            $stmt->bindParam(':resident_id', $this->resident_id);
            $stmt->bindParam(':start_term', $this->start_term);
            $stmt->bindParam(':end_term', $this->end_term);
            $stmt->bindParam(':position_title', $this->position_title);

            if ($stmt->execute()) {
                return true;
            }

            printf("Error: %s.\n", $stmt->error);
            return false;
        }

        public function delete()
        {
            $query = 'DELETE FROM ' . $this->table . ' WHERE user_id = :user_id';
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $this->user_id);

            if ($stmt->execute()) {
                return true;
            }

            printf("Error: %s.\n", $stmt->error);
            return false;
        }

        public function existsForResident($rid)
        {
            $sql = "SELECT COUNT(*) as cnt FROM users WHERE resident_id = :rid";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':rid', $rid);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['cnt'] > 0;
        }

    
        public function archive()
        {
            $query = "UPDATE " . $this->table . " SET status = 'archived' WHERE user_id = :user_id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $this->user_id);

            return $stmt->execute();
        }

    

    }
