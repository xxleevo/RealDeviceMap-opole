<?php
require_once './vendor/autoload.php';
require_once './config.php';
require_once './includes/DbConnector.php';
require_once './includes/GeofenceService.php';
require_once './includes/utils.php';
require_once './static/data/movesets.php';
require_once './static/data/pokedex.php';

if ($config['discord']['enabled'] && !isset($_SESSION['user']))
  die("No access");

$geofenceSrvc = new GeofenceService();

// Establish connection to database
$db = new DbConnector($config['db']);
$pdo = $db->getConnection();

// Query Database and Build Raid Billboard
try {
  $sql = "
SELECT 
  TIME_FORMAT(CONVERT_TZ(FROM_UNIXTIME(raid_battle_timestamp), 'UTC', '" . $config['core']['timeZone'] . "'), '%h:%i:%s %p')
    AS starts, 
  TIME_FORMAT(CONVERT_TZ(FROM_UNIXTIME(raid_end_timestamp), 'UTC', '" . $config['core']['timeZone'] . "'),'%h:%i:%s %p')
    AS ends, 
  lat, 
  lon,
  raid_level,
  raid_pokemon_id, 
  raid_pokemon_move_1,
  raid_pokemon_move_2,
  name,
  team_id,
  ex_raid_eligible,
  updated
FROM 
  " . $config['db']['dbname'] . ".gym
WHERE
  raid_pokemon_id IS NOT NULL && 
  name IS NOT NULL &&
  raid_end_timestamp >= UNIX_TIMESTAMP()
ORDER BY 
  raid_end_timestamp;
";

  $result = $pdo->query($sql);
  if ($result->rowCount() > 0) {
    echo $modal;
    echo "<div class='table-responsive'>";
    echo "<table id='gym-table' class='table table-".$config['ui']['table']['style']." ".($config['ui']['table']['striped'] ? 'table-striped' : null)."' border='1'>";
    echo "<thead class='thead-".$config['ui']['table']['headerStyle']."'>";
    echo "<tr class='text-nowrap'>";
      echo "<th>Remove</th>";
      echo "<th onclick='sort_table(\"gym-table\",1)'>Raid Starts</th>";
      echo "<th onclick='sort_table(\"gym-table\",2)'>Raid Ends</th>";
      echo "<th onclick='sort_table(\"gym-table\",3)'>Raid Level</th>";
      echo "<th onclick='sort_table(\"gym-table\",4)'>Raid Boss</th>";
      echo "<th onclick='sort_table(\"gym-table\",5)'>Moveset</th>";
      echo "<th onclick='sort_table(\"gym-table\",6)'>City</th>";
      echo "<th onclick='sort_table(\"gym-table\",7)'>Team</th>";
      echo "<th onclick='sort_table(\"gym-table\",8)'>Ex-Eligible</th>";
      echo "<th onclick='sort_table(\"gym-table\",9)'>Gym</th>";
      echo "<th onclick='sort_table(\"gym-table\",10)'>Updated</th>";
    echo "</tr>";
    echo "</thead>";
    while ($row = $result->fetch()) {	
      $geofence = $geofenceSrvc->get_geofence($row['lat'], $row['lon']);
      $city = ($geofence == null ? $config['ui']['unknownValue'] : $geofence->name);
      $map_link = sprintf($config['google']['maps'], $row["lat"], $row["lon"]);
      $pokemon_id = $row['raid_pokemon_id'];
      $pokemon = $pokedex[$pokemon_id];
      $level = $row['raid_level'];
      $raid_image = get_raid_image($pokemon_id, $level);
      $fast_move = $quick_moves[$row['raid_pokemon_move_1']];
      $charge_move = $charge_moves[$row['raid_pokemon_move_2']];
      $moveset = (($fast_move == $config['ui']['unknownValue'] && $charge_move == $config['ui']['unknownValue']) ? $config['ui']['unknownValue'] : $fast_move . "/" . $charge_move);
      echo "<tr class='text-nowrap'>";
        echo "<td scope='row' class='text-center'><a title='Remove' data-toggle='tooltip' class='delete'><i class='fa fa-times'></i></a></td>";
        echo "<td>" . $row['starts'] . "</td>";
        echo "<td>" . $row['ends'] . "</td>";
        echo "<td>" . $level . "</td>";
        echo "<td><img src='$raid_image' height=32 width=32 />&nbsp;" . $pokemon . "</td>";
        echo "<td>" . $moveset . "</td>";
        echo "<td>" . $city . "</td>";
        echo "<td>" . get_team($row['team_id']) . "</td>";
        echo "<td>" . ($row['ex_raid_eligible'] ? "Yes" : "No") . "</td>";
        echo "<td><a href='" . $map_link . "' target='_blank'>" . $row['name'] . "</a></td>";
        echo "<td>" . date($config['core']['dateTimeFormat'], $row['updated']) . "</td>";
      echo "</tr>";
    }
    echo "</table>";
    echo "</div>";
		
  // Free result set
  unset($result);
  } else{
    echo "
  <div class='alert alert-primary' role='alert'>
    <b>No raids available</b>, come back tomorrow after 5am.
  </div>";
  }
} catch(PDOException $e){
  die("ERROR: Could not able to execute $sql. " . $e->getMessage());
}
// Close connection
unset($pdo);
unset($db);

?>