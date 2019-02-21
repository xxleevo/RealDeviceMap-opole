<?
include './config.php';
include './includes/DbConnector.php';

if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER["HTTP_X_REQUESTED_WITH"] == "XMLHttpRequest"))
  header("Location: index.php");

if (isset($_GET['table'])) {
  $db = new DbConnector($config['db']);
  $pdo = $db->getConnection();
  $table = $_GET['table'];
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