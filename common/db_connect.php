<?php
class Database {
    private $host = "localhost";
    private $db_name = "sql_pgmitra_in";
    private $username = "sql_pgmitra_in";
    private $password = "86e16fae5d55f8";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);
            
            if ($this->conn->connect_error) {
                die("Connection failed: " . $this->conn->connect_error);
            }
            
            // ✅ FIXED
            $this->conn->set_charset("utf8mb4");
            
        } catch(Exception $e) {
            echo "Connection error: " . $e->getMessage();
        }
        return $this->conn;
    }
}
?>