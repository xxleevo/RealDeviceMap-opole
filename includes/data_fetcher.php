<?php
require_once './vendor/autoload.php';
require_once './config.php';
require_once './includes/DbConnector.php';
require_once './includes/GeofenceService.php';
require_once './includes/utils.php';
require_once './static/data/movesets.php';
require_once './static/data/pokedex.php';

if ($discord_login && !isset($_SESSION['user']))
  die("No access");

$geofence_srvc = new GeofenceService();

// Establish connection to database
$db = new DbConnector($dbhost, $dbPort, $dbuser, $dbpass, $dbname);
$pdo = $db->getConnection();

// Query Database and Build Raid Billboard
try {
  $sql = "
SELECT 
  TIME_FORMAT(CONVERT_TZ(FROM_UNIXTIME(raid_battle_timestamp), 'UTC', '" . $time_zone . "'), '%h:%i:%s %p')
    AS starts, 
  TIME_FORMAT(CONVERT_TZ(FROM_UNIXTIME(raid_end_timestamp), 'UTC', '" . $time_zone . "'),'%h:%i:%s %p')
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
  " . $dbname . ".gym
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
    echo "<table id='gym-table' class='table table-".$table_style." ".($table_striped ? 'table-striped' : null)."' border='1'>";
    echo "<thead class='thead-".$table_header_style."'>";
    echo "<tr class='text-nowrap'>";
      echo "<th>Remove</th>";
      echo "<th onclick='sort_table('gym-table',1)'>Raid Starts</th>";
      echo "<th onclick='sort_table('gym-table',2)'>Raid Ends</th>";
      echo "<th onclick='sort_table('gym-table',3)'>Raid Level</th>";
      echo "<th onclick='sort_table('gym-table',4)'>Raid Boss</th>";
      echo "<th onclick='sort_table('gym-table',5)'>Moveset</th>";
      echo "<th onclick='sort_table('gym-table',6)'>City</th>";
      echo "<th onclick='sort_table('gym-table',7)'>Team</th>";
      echo "<th onclick='sort_table('gym-table',8)'>Ex-Eligible</th>";
      echo "<th onclick='sort_table('gym-table',9)'>Gym</th>";
      echo "<th onclick='sort_table('gym-table',10)'>Updated</th>";
    echo "</tr>";
    echo "</thead>";
    while ($row = $result->fetch()) {	
      $geofence = $geofence_srvc->get_geofence($row['lat'], $row['lon']);
      $city = ($geofence == null ? $unknown_value : $geofence->name);
      $map_link = sprintf($googleMapsLink, $row["lat"], $row["lon"]);
      $pokemon = $pokedex[$row['raid_pokemon_id']];
      $fast_move = $quick_moves[$row['raid_pokemon_move_1']];
      $charge_move = $charge_moves[$row['raid_pokemon_move_2']];
      $moveset = (($fast_move == $unknown_value && $charge_move == $unknown_value) ? $unknown_value : $fast_move . "/" . $charge_move);
      echo "<tr class='text-nowrap'>";
        echo "<td scope='row' class='text-center'><a title='Remove' data-toggle='tooltip' class='delete'><i class='fa fa-times'></i></a></td>";
        echo "<td>" . $row['starts'] . "</td>";
        echo "<td>" . $row['ends'] . "</td>";
        echo "<td>" . $row['raid_level'] . "</td>";
        echo "<td>" . $pokemon . "</td>";
        echo "<td>" . $moveset . "</td>";
        echo "<td>" . $city . "</td>";
        echo "<td>" . get_team($row['team_id']) . "</td>";
        echo "<td>" . ($row['ex_raid_eligible'] ? "Yes" : "No") . "</td>";
        echo "<td><a href='" . $map_link . "' target='_blank'>" . $row['name'] . "</a></td>";
        echo "<td>" . date($date_time_format, $row['updated']) . "</td>";
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