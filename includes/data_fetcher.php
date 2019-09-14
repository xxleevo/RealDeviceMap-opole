<?php
require_once './vendor/autoload.php';
require_once './config.php';
require_once './includes/DbConnector.php';
require_once './includes/GeofenceService.php';
require_once './includes/utils.php';
require_once './static/data/movesets.php';
require_once './static/data/pokedex.php';
require_once './static/data/forms.php';

define('MAX_GYM_NAME_LENGTH', 20); //TODO: Add to config

if ($config['discord']['enabled'] && !isset($_SESSION['user']))
    die("No access");

$geofenceSrvc = new GeofenceService();
$raids = get_raids();
$count = count($raids);
$mobile = $config['ui']['table']['forceRaidCards'] || (!$config['ui']['table']['forceRaidCards'] && is_mobile());

echo $modal;

if ($count == 0) {
    echo "
    <div class='alert alert-primary' role='alert'>
        <i class='fa fa-info'>&nbsp;" . $config['ui']['noRaidsAvailableMessage'] . "</i>
    </div>";
    return;
}

if ($mobile == '0') {
    echo "<table id='gym-table' class='table table-hover table-".$config['ui']['table']['style']." ".($config['ui']['table']['striped'] ? 'table-striped' : null)."' border='1'>";
    echo "<thead class='thead-".$config['ui']['table']['headerStyle']."'>";
    //echo "<thead class='rmdo'>";
    echo "<tr class='text-nowrap'>";
        echo "<th>Remove</th>";
        echo "<th class='starts' onclick='sort_table(\"gym-table\",1)' data-i18n='raids_column_raid_starts'>Raidstart</th>";
        echo "<th class='ends' onclick='sort_table(\"gym-table\",2)' data-i18n='raids_column_raid_ends'>Raidende</th>";
        echo "<th class='level' onclick='sort_table(\"gym-table\",3)' data-i18n='raids_column_raid_level'>Raid Level</th>";
        echo "<th class='boss' onclick='sort_table(\"gym-table\",4)' data-i18n='raids_column_raid_boss'>Raid Boss</th>";
        //echo "<th class='moveset' onclick='sort_table(\"gym-table\",5)' data-i18n='raids_column_moveset'>Moveset</th>";
        echo "<th class='gym' onclick='sort_table(\"gym-table\",5)' data-i18n='raids_column_gym'>Arena</th>";
        echo "<th class='city' onclick='sort_table(\"gym-table\",6)' data-i18n='raids_column_city'>Stadt</th>";
        echo "<th class='team' onclick='sort_table(\"gym-table\",7)' data-i18n='raids_column_team'>Team</th>";
        echo "<th class='ex' onclick='sort_table(\"gym-table\",8)' data-i18n='raids_column_ex'>Ex-Fähig</th>";
        //echo "<th class='updated' onclick='sort_table(\"gym-table\",10)' data-i18n='raids_column_updated'>Updated</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
} else {
    echo "<div id='gym-table' class='container' style='display: flex; flex-direction: column;'>";
}

for ($i = 0; $i < $count; $i++) {
    $raid = $raids[$i];
    $id = 'x' . str_replace('.', '', $raid['id']);
    $gymName = strlen($raid['name']) > MAX_GYM_NAME_LENGTH
        ? substr($raid['name'], 0, min(strlen($raid['name']), MAX_GYM_NAME_LENGTH)) . "..." 
        : $raid['name'];
    $starts = date($config['core']['dateTimeFormat'], $raid['raid_battle_timestamp']);
    $ends = date($config['core']['dateTimeFormat'], $raid['raid_end_timestamp']);
    $geofence = $geofenceSrvc->get_geofence($raid['lat'], $raid['lon']);
    if ($geofence == null && $config['ui']['pages']['raids']['ignoreUnknown'] !== false) {
        continue;
    }
    $city = ($geofence == null ? $config['ui']['unknownValue'] : $geofence->name);
    $map_link = sprintf($config['google']['maps'], $raid["lat"], $raid["lon"]);
    $pokemon_id = $raid['raid_pokemon_id'];
	$pokemon_form = $forms[$raid['raid_pokemon_form']];
    $pokemon = $pokedex[$pokemon_id];
	if($raid['raid_pokemon_form'] != '0'){
		$pokemon .= '(' . $pokemon_form . ')';
		
	}
    $level = $raid['raid_level'];
    $raid_image = get_raid_image($pokemon_id, $level,$raid['raid_pokemon_form']);
    $fast_move = $quick_moves[$raid['raid_pokemon_move_1']];
    $charge_move = $charge_moves[$raid['raid_pokemon_move_2']];
    $moveset = (($fast_move == $config['ui']['unknownValue'] && ($charge_move == $config['ui']['unknownValue'])) 
        ? $config['ui']['unknownValue'] 
        : $fast_move . "/" . $charge_move);
    $team = get_team($raid['team_id']);
    $exEligible = $raid['ex_raid_eligible'] ? "Yes" : "No";
    $started = $raid['raid_battle_timestamp'] < time();
    $startTime = $started ? "--" : getMinutesLeft($raid['raid_battle_timestamp']) . "m";
    $endMinutesLeft = getMinutesLeft($raid['raid_end_timestamp']);
    $endTime = $started ? ($endMinutesLeft === '00' ? "Now" : ($endMinutesLeft . "m")) : $ends;
    if ($endMinutesLeft === '00') {
        //Skip raids with under a minute left.
        continue;
    }
    $lastUpdated = getMinutesLeft($raid['updated']);
    $updated = ($lastUpdated === '00' ? "Just now" : $lastUpdated . " mins ago");

    if ($mobile == '1') {
        echo "<div class='row mobile-row text-nowrap shadow rounded border border-dark' style='background: #cccccc;'>";
            echo "<div class='col w-25 small-header'><b><div class='mobile'>$pokemon</div>&nbsp;<img src='$raid_image' height=32 width=32 /></b></div>";
            echo "<div class='col w-25 small-header'><b>Level</b> <div class='mobile'>$level</div></div>";
            echo "<div class='col w-25 small-header'><b>Starts</b><br><div class='mobile'>$startTime</div></div>";
            echo "<div class='col w-25 small-header'><b>Ends</b><br><div class='mobile'>$endTime</div></div>";
            echo "<div class='w-100'></div>";
            echo "<div class='col w-50 small'><b>Gym</b> <a href='$map_link' target='_blank'><div class='mobile'>$gymName</div></a></div>";
            echo "<div class='col w-25 small'><b>City</b> <div class='mobile'>$city</div></small></div>";
            echo "<div class='col w-25 small'><b>Team</b> <div class='mobile'>$team</div></div>";
            echo "<div class='w-100'></div>";
            echo "<div class='col w-50 small'><b>Moveset</b> <div class='mobile'>$moveset</div></div>";
            echo "<div class='col w-25 small'><b>Ex-Eligible</b> <div class='mobile'>$exEligible</div></div>";
            echo "<div class='col w-25 small'><b>Updated</b> <div class='mobile'>$updated</div></div>";
        echo "</div>";
    } else {
        echo "<tr class='text-nowrap clickable' data-toggle='collapse' href='#$id' data-target='#$id'>";
            echo "<td scope='row' class='text-center' data-title='Remove'><a title='Remove' data-toggle='tooltip' class='delete'><i class='fa fa-times'></i></a></td>";
            echo "<td data-title='Raid Starts'>$startTime</td>";
            echo "<td data-title='Raid Ends'>$endTime</td>";
            echo "<td class='numeric' data-title='Raid Level'>$level</td>";
            echo "<td data-title='Raid Boss'><img src='$raid_image' height=32 width=32 />&nbsp;$pokemon</td>";
            //echo "<td data-title='Moveset'>$moveset</td>";
            echo "<td data-title='Gym'><a href='$map_link' target='_blank'>$gymName</a></td>";
            echo "<td data-title='City'>$city</td>";
            echo "<td data-title='Team'>$team</td>";
            echo "<td data-title='Ex-Eligible'>$exEligible</td>";
            //echo "<td data-title='Updated'>" . date($config['core']['dateTimeFormat'], $row['updated']) . "</td>";
        echo "</tr>";
        echo "<tr class='hiddenRow' id='" . $id . "_row'>";
            echo "<td colspan='100%' class='hiddenRow'>
                    <div id='$id' class='collapse'>
                    <table>
                      <thead></thead>
                      <tbody>
                      <tr><td><span><b>Moveset:</b>&nbsp;$moveset</span></td></tr>
                      <!--<tr><td><span><b>Updated:</b>&nbsp;$updated</span></td></tr>-->
                      </tbody>
                    </table>
                    </div>
                  </td>";
        echo "</tr>";
    }
}

if ($mobile == '0') {
    echo "
  </tbody>
</table>";
} else {
    echo "</div>";
}
?>