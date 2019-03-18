<?php
require_once './vendor/autoload.php';
require_once './config.php';
require_once './includes/DbConnector.php';
require_once './includes/GeofenceService.php';
require_once './includes/utils.php';
require_once './static/data/movesets.php';
require_once './static/data/pokedex.php';

define('MAX_GYM_NAME_LENGTH', 20); //TODO: Add to config

if ($config['discord']['enabled'] && !isset($_SESSION['user']))
    die("No access");

$geofenceSrvc = new GeofenceService();

// Establish connection to database
$db = new DbConnector($config['db']);
$pdo = $db->getConnection();

// Query Database and Build Raid Billboard
$sql = "
SELECT 
  CONVERT_TZ(FROM_UNIXTIME(raid_battle_timestamp)," . "'" . $config['core']['fromTimeZoneOffset'] . "', '" . $config['core']['timeZone'] . "')
    AS starts, 
  CONVERT_TZ(FROM_UNIXTIME(raid_end_timestamp)," . "'" . $config['core']['fromTimeZoneOffset'] . "', '" . $config['core']['timeZone'] . "')
    AS ends, 
  raid_battle_timestamp,
  raid_end_timestamp,
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
  gym
WHERE
  raid_pokemon_id IS NOT NULL
  AND name IS NOT NULL 
  AND raid_end_timestamp > UNIX_TIMESTAMP()
ORDER BY
  raid_end_timestamp;
";
echo $modal;
if ($config['ui']['table']['forceRaidCards'] || (!$config['ui']['table']['forceRaidCards'] && is_mobile())) {
    $result = $pdo->query($sql);
    if ($result->rowCount() > 0) {
        echo "<div id='gym-table' class='container' style='display: flex; flex-direction: column;'>";
        while ($row = $result->fetch()) {
            $gymName = strlen($row['name']) > MAX_GYM_NAME_LENGTH
                ? substr($row['name'], 0, min(strlen($row['name']), MAX_GYM_NAME_LENGTH)) . "..." 
                : $row['name'];
            $starts = date($config['core']['dateTimeFormat'], $row['raid_battle_timestamp']);
            $ends = date($config['core']['dateTimeFormat'], $row['raid_end_timestamp']);
            $geofence = $geofenceSrvc->get_geofence($row['lat'], $row['lon']);
            $city = ($geofence == null ? $config['ui']['unknownValue'] : $geofence->name);
            $map_link = sprintf($config['google']['maps'], $row["lat"], $row["lon"]);
            $pokemon_id = $row['raid_pokemon_id'];
            $pokemon = $pokedex[$pokemon_id];
            $level = $row['raid_level'];
            $raid_image = get_raid_image($pokemon_id, $level);
            $fast_move = $quick_moves[$row['raid_pokemon_move_1']];
            $charge_move = $charge_moves[$row['raid_pokemon_move_2']];
            $moveset = (($fast_move == $config['ui']['unknownValue'] && ($charge_move == $config['ui']['unknownValue'])) 
                ? $config['ui']['unknownValue'] 
                : $fast_move . "/" . $charge_move);
            $started = $row['raid_battle_timestamp'] < time();
            $startTime = $started ? "--" : getMinutesLeft($row['raid_battle_timestamp']) . "m";
            $endMinutesLeft = getMinutesLeft($row['raid_end_timestamp']);
            $endTime = $started ? ($endMinutesLeft === '00' ? "Now" : ($endMinutesLeft . "m")) : $ends;
            if ($endMinutesLeft === '00') {
                //Skip raids with under a minute left.
                continue;
            }
            $lastUpdated = getMinutesLeft($row['updated']);
            $updated = ($lastUpdated === '00' ? "Just now" : $lastUpdated . " mins ago");
            echo "<div class='row mobile-row text-nowrap shadow rounded border border-dark' style='background: #cccccc;'>";
                echo "<div class='col w-25 small-header'><b><div class='mobile'>$pokemon</div>&nbsp;<img src='$raid_image' height=32 width=32 /></b></div>";
                echo "<div class='col w-25 small-header'><b>Level</b> <div class='mobile'>$level</div></div>";
                echo "<div class='col w-25 small-header'><b>Starts</b><br><div class='mobile'>$startTime</div></div>";
                echo "<div class='col w-25 small-header'><b>Ends</b><br><div class='mobile'>$endTime</div></div>";
                echo "<div class='w-100'></div>";
                echo "<div class='col w-50 small'><b>Gym</b> <a href='$map_link' target='_blank'><div class='mobile'>$gymName</div></a></div>";
                echo "<div class='col w-25 small'><b>City</b> <div class='mobile'>$city</div></small></div>";
                echo "<div class='col w-25 small'><b>Team</b> <div class='mobile'>" . get_team($row['team_id']) . "</div></div>";
                echo "<div class='w-100'></div>";
                echo "<div class='col w-50 small'><b>Moveset</b> <div class='mobile'>$moveset</div></div>";
                echo "<div class='col w-25 small'><b>Ex-Eligible</b> <div class='mobile'>" . ($row['ex_raid_eligible'] ? "Yes" : "No") . "</div></div>";
                echo "<div class='col w-25 small'><b>Updated</b> <div class='mobile'>$updated</div></div>";
            echo "</div>";
        }
        echo "</div>";
    } else {
        echo "
        <div class='alert alert-primary' role='alert'>
            <i class='fa fa-info'>&nbsp;" . $config['ui']['noRaidsAvailableMessage'] . "</i>
        </div>";
    }
} else {
    try {
        echo "<table id='gym-table' class='table table-".$config['ui']['table']['style']." ".($config['ui']['table']['striped'] ? 'table-striped' : null)."' border='1'>";
        echo "<thead class='thead-".$config['ui']['table']['headerStyle']."'>";
        echo "<tr class='text-nowrap'>";
            echo "<th>Remove</th>";
            echo "<th class='starts' onclick='sort_table(\"gym-table\",1)' data-i18n='raids_column_raid_starts'>Raid Starts</th>";
            echo "<th class='ends' onclick='sort_table(\"gym-table\",2)' data-i18n='raids_column_raid_ends'>Raid Ends</th>";
            echo "<th class='level' onclick='sort_table(\"gym-table\",3)' data-i18n='raids_column_raid_level'>Raid Level</th>";
            echo "<th class='boss' onclick='sort_table(\"gym-table\",4)' data-i18n='raids_column_raid_boss'>Raid Boss</th>";
            echo "<th class='moveset' onclick='sort_table(\"gym-table\",5)' data-i18n='raids_column_moveset'>Moveset</th>";
            echo "<th class='gym' onclick='sort_table(\"gym-table\",6)' data-i18n='raids_column_gym'>Gym</th>";
            echo "<th class='city' onclick='sort_table(\"gym-table\",7)' data-i18n='raids_column_city'>City</th>";
            echo "<th class='team' onclick='sort_table(\"gym-table\",8)' data-i18n='raids_column_team'>Team</th>";
            echo "<th class='ex' onclick='sort_table(\"gym-table\",9)' data-i18n='raids_column_ex'>Ex-Eligible</th>";
            echo "<th class='updated' onclick='sort_table(\"gym-table\",10)' data-i18n='raids_column_updated'>Updated</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
  
        $result = $pdo->query($sql);
        if ($result->rowCount() > 0) {
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
                    echo "<td scope='row' class='text-center' data-title='Remove'><a title='Remove' data-toggle='tooltip' class='delete'><i class='fa fa-times'></i></a></td>";
                    echo "<td data-title='Raid Starts'>" . $row['starts'] . "</td>";
                    echo "<td data-title='Raid Ends'>" . $row['ends'] . "</td>";
                    echo "<td class='numeric' data-title='Raid Level'>" . $level . "</td>";
                    echo "<td data-title='Raid Boss'><img src='$raid_image' height=32 width=32 />&nbsp;$pokemon</td>";
                    echo "<td data-title='Moveset'>$moveset</td>";
                    echo "<td data-title='Gym'><a href='$map_link' target='_blank'>" . $row['name'] . "</a></td>";
                    echo "<td data-title='City'>$city</td>";
                    echo "<td data-title='Team'>" . get_team($row['team_id']) . "</td>";
                    echo "<td data-title='Ex-Eligible'>" . ($row['ex_raid_eligible'] ? "Yes" : "No") . "</td>";
                    echo "<td data-title='Updated'>" . date($config['core']['dateTimeFormat'], $row['updated']) . "</td>";
                echo "</tr>";
            }

            // Free result set
            unset($result);
        } else {
            echo "
            <div class='alert alert-primary' role='alert'>
                <i class='fa fa-info'>&nbsp;" . $config['ui']['noRaidsAvailableMessage'] . "</i>
            </div>";
        }
        echo "</tbody>
        </table>";
    } catch (PDOException $e) {
        die("ERROR: Could not able to execute $sql. " . $e->getMessage());
    }
}
// Close connection
unset($pdo);
unset($db);
?>
