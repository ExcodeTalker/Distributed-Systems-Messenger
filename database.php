<?php
class Database {
private $host = "172.16.11.22:3306";
private $db_name = "fosd1_23_messenger";
private $username = "fosd1_23_bingus";
private $password = "AzurL4ne!";
public $conn;
public function getConnection() {
$this->conn = null;
try {
$this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
$this->conn->exec("set names utf8");
} catch(PDOException $exception) {
echo "Connection error: " . $exception->getMessage();
}
return $this->conn;
}
}
?>