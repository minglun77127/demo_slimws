<?php

class Database{
    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "demo";
    private $conn = null;
    private static $instance = null;

    private function __construct(){
        $this->conn = new mysqli($this->servername,$this->username,$this->password,$this->dbname);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public static function getInstance(){
        return self::$instance ? self::$instance : self::$instance = new Database();
    }

    public function query($sql){
        return $this->conn->query($sql);
    }

    public function execStmt($sql, $typeStr, ...$params){
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($typeStr, ...$params);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function __destruct(){
        $this->conn->close();
    }
}