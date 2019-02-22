<?
session_start();

include './config.php';
include './includes/DbConnector.php';

if (!(isset($_SESSION['token']) && !empty($_SESSION['token']))) {
  die();
}
if (!(isset($_GET['token']) && !empty($_GET['token']))) {
  die();
}
if (strcmp($_SESSION['token'], $_GET['token']) != 0) {
  die();
}
if (!(isset($_GET['table']) && !empty($_GET['table']))) {
  die();
}
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER["HTTP_X_REQUESTED_WITH"] == "XMLHttpRequest")) {
  die();
}

if (isset($_GET['table'])) {
  $db = new DbConnector($config['db']);
  $pdo = $db->getConnection();
  $table = $_GET['table'];
  if (empty($table)) {
    die();
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