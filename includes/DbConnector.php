<?php
class DbConnector {
  private $host;
  private $port;
  private $user;
  private $pass;
  private $db;
      
  function __construct($host,$port,$user,$pass,$db) {
    $this->host = $host;
    $this->port = $port;
    $this->user = $user;
    $this->pass = $pass;
    $this->db = $db;
  }
 
  public function getConnection() {
    // Establish connection to database
    try {
      $pdo = new PDO("mysql:host=$this->host;dbname=$this->db;port=$this->port", $this->user, $this->pass);
      // Set the PDO error mode to exception
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      return $pdo;
    } catch(PDOException $e) {
      die("ERROR: Could not connect. " . $e->getMessage());
    }
    die("ERROR: Could not establish connection to database.");
  }
}
?>