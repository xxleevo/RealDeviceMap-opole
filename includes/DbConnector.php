<?php
class DbConnector {
  private $host;
  private $port;
  private $user;
  private $pass;
  private $db;
      
  function __construct($dbOptions) {
    $this->host = $dbOptions['host'];
    $this->port = $dbOptions['port'];
    $this->user = $dbOptions['user'];
    $this->pass = $dbOptions['pass'];
    $this->db = $dbOptions['dbname'];
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