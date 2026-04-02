<?php
class Database {
    private $host;
    private $username;
    private $password;
    private $dbname;
    private $conn;
    private static $instance = null;
    
    private function __construct() {
        // Check the environment and set appropriate credentials
        if (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], '593900.klas4s24.mid-ica.nl') !== false) {
            // Production environment
            $this->host = "localhost";
            $this->username = "klas4s24_593900";
            $this->password = "tmIePSag";
            $this->dbname = "klas4s24_593900";
        } else {
            // Local development environment
            $this->host = "localhost";
            $this->username = "gastenboek_niels";
            $this->password = "Bruin_813!";
            $this->dbname = "gastenboek_niels";
        }
        
        $this->connect();
    }
    
    // Singleton pattern to ensure only one database connection
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    private function connect() {
        // Create connection
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->dbname);
        
        // Check connection
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }
    
    public function getConnection() {
        return $this->conn;
    }
    
    public function prepare($sql) {
        return $this->conn->prepare($sql);
    }
    
    public function query($sql) {
        return $this->conn->query($sql);
    }
    
    public function beginTransaction() {
        return $this->conn->begin_transaction();
    }
    
    public function commit() {
        return $this->conn->commit();
    }
    
    public function rollback() {
        return $this->conn->rollback();
    }
    
    public function close() {
        return $this->conn->close();
    }
}