<?php
require_once './config.php';
require_once './includes/DbConnector.php';

function get_team($team_id) {
  switch ($team_id) {
    case "1": return "Mystic";
    case "2": return "Valor";
    case "3": return "Instinct";
    default:  return "Neutral";
  }
}
function get_gym_stats() {
  global $config;
  $db = new DbConnector($config['db']);
  $pdo = $db->getConnection();
  $sql = "
SELECT
  team_id AS team,
  COUNT(id) AS count
FROM
  " . $config['db']['dbname'] . ".gym
GROUP BY
  team
";
  $result = $pdo->query($sql);
  if ($result->rowcount() > 0) {
    $data = $result->fetchAll(PDO::FETCH_KEY_PAIR);
  }
  unset($pdo);
  unset($db);
 
  return $data;
}
  
function get_pokestop_stats() {
  global $config;
  $db = new DbConnector($config['db']);
  $pdo = $db->getConnection();
  $sql = "
SELECT 
  COUNT(id) total,
  SUM(CASE WHEN lure_expire_timestamp > 0 THEN 1 ELSE 0 END) lured,
  SUM(CASE WHEN quest_reward_type THEN 1 ELSE 0 END) quests
FROM
  " . $config['db']['dbname'] . ".pokestop
";
  $result = $pdo->query($sql);
  if ($result->rowCount() > 0) {
    $data = $result->fetchAll()[0];
  }
  unset($pdo);
  unset($db);
  
  return $data;
}
  
function get_raid_stats() {
  global $config;
  $db = new DbConnector($config['db']);
  $pdo = $db->getConnection();
  $sql = "SELECT count(id) FROM " . $config['db']['dbname'] . ".gym WHERE raid_pokemon_id IS NOT NULL && name IS NOT NULL && raid_end_timestamp >= UNIX_TIMESTAMP()";
  $count = $pdo->query($sql)->fetchColumn();
  unset($pdo);
  unset($db);
      
  return $count;
}
  
function get_table_count($table) {
  global $config;
  $db = new DbConnector($config['db']);
  $pdo = $db->getConnection();
  $sql = "SELECT count(id) FROM " . $config['db']['dbname'] . ".$table";
  $count = $pdo->query($sql)->fetchColumn();
  unset($pdo);
  unset($db);
    
  return $count;
}

function get_pokestop_objects() {
  global $config;
  $db = new DbConnector($config['db']);
  $pdo = $db->getConnection();
  $sql = "SELECT id,lat,lon,name FROM ". $config['db']['dbname'] . ".pokestop";
  $result = $pdo->query($sql)->fetchAll();
  unset($pdo);
  unset($db);
  
  return $result;
}

function get_raid_image($pokemonId, $raidLevel) {
  global $config;
  if ($pokemonId === 0) {
    return sprintf($config['urls']['images']['egg'], $raidLevel);
  } else {
    return sprintf($config['urls']['images']['pokemon'], $pokemonId);
  }
}

?>