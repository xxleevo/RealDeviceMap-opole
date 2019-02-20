<?
include './config.php';
include './includes/DbConnector.php';

if (isset($_GET['table'])) {
  $db = new DbConnector($config['db']);
  $pdo = $db->getConnection();
  $table = $_GET['table']; //TODO: Check
  if (empty($table)) {
    die("Table is required");
  }

  $limit = isset($_GET['limit']) ? $_GET['limit'] : '99999';
  $sql = "SELECT * FROM " . $config['db']['dbname'] . ".$table LIMIT $limit";
  $result = $pdo->query($sql);
  if ($result->rowCount() > 0) {
    $data = $result->fetchAll();
    echo json_encode($data);
  }
  unset($pdo);
  unset($db);
}
?>