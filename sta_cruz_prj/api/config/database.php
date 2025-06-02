<?php

class Database
{    private $host =  "mysql";
    private $dbname = 'sta_cruz_db';
    private $username = 'root';
    private $password = 'rootpassword';
    public $conn;

    public function connect()
    {
        $this->conn = null;
        try {
            $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname . ';charset=utf8';

            //set PDO attributes
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
       
        } catch (PDOException $exception) {
            echo 'Connection Error: ' . $exception->getMessage();
        }

       // echo 'Connected successfully';
        return $this->conn;
    }
}

?>
