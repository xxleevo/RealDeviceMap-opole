<?php
session_start();

include './config.php';
include './includes/DbConnector.php';

/*
if (isset($_SERVER['HTTP_ORIGIN'])) {
  $address = 'https://' . $_SERVER['SERVER_NAME'];
  if (strpos($address, $_SERVER['HTTP_ORIGIN']) !== 0) {
    die("A");
  }
} else {
  die("B");
}

if (isset($_SERVER['HTTP_REFERER'])) {
  $address = 'https://' . $_SERVER['SERVER_NAME'];
  if (strpos($address, $_SERVER['HTTP_REFERER']) !== 0) {
    die("C");
  }
} else {
  die("D");
}
*/

if (!(isset($_SESSION['token']) && !empty($_SESSION['token']))) {
  die();
}
if (!(isset($_GET['token']) && !empty($_GET['token']))) {
  die();
}
if ($_SESSION['token'] !== $_GET['token']) {
  die();
}
if (!(isset($_GET['table']) && !empty($_GET['table']))) {
  die();
}
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER["HTTP_X_REQUESTED_WITH"] == "XMLHttpRequest")) {
  die();
}

//TODO: Sanitize user input

$table = $_GET['table'];
$limit = isset($_GET['limit']) ? $_GET['limit'] : '99999';
$db = new DbConnector($config['db']);
$pdo = $db->getConnection();
$sql = "SELECT * FROM " . $config['db']['dbname'] . ".$table LIMIT $limit";
$result = $pdo->query($sql);
if ($result->rowCount() > 0) {
  $data = $result->fetchAll();
  echo json_encode($data);
}
unset($pdo);
unset($db);
?>