<?php
include './vendor/autoload.php';
include './config.php';
include './includes/DbConnector.php';
include './includes/GeofenceService.php';
include './includes/utils.php';
include './static/data/pokedex.php';

$geofenceSrvc = new GeofenceService();

$filters = "
<div class='container'>
  <div class='row'>
    <div class='input-group mb-3'>
      <div class='input-group-prepend'>
        <label class='input-group-text' for='filter-team'>Team</label>
      </div>
      <select id='filter-team' class='custom-select' onchange='filter_gyms()'>
        <option selected>Select</option>
        <option value='all'>All</option>
        <option value='Neutral'>Neutral</option>
        <option value='Mystic'>Mystic</option>
        <option value='Valor'>Valor</option>
        <option value='Instinct'>Instinct</option>
      </select>
    </div>
    <div class='input-group mb-3'>
      <div class='input-group-prepend'>
        <label class='input-group-text' for='filter-slots'>Available Slots</label>
      </div>
      <select id='filter-slots' class='custom-select' onchange='filter_gyms()'>
        <option disabled selected>Select</option>
        <option value='all'>All</option>
        <option value='full'>Full</option>
        <option value='1'>1</option>
        <option value='2'>2</option>
        <option value='3'>3</option>
        <option value='4'>4</option>
        <option value='5'>5</option>
      </select>
    </div>
    <div class='input-group mb-3'>
      <div class='input-group-prepend'>
        <label class='input-group-text' for='filter-battle'>In Battle Status</label>
      </div>
      <select id='filter-battle' class='custom-select' onchange='filter_gyms()'>
  	    <option disabled selected>Select</option>
   	    <option value='all'>All</option>
   	    <option value='Under Attack!'>Yes</option>
   	    <option value='Safe'>No</option>
      </select>
    </div>
    <div class='input-group mb-3'>
      <div class='input-group-prepend'>
        <label class='input-group-text' for='filter-city'>City</label>
      </div>
      <select id='filter-city' class='custom-select' onchange='filter_gyms()'>
        <option disabled selected>Select</option>
        <option value='all'>All</option>
        <option value='" . $config['ui']['unknownValue'] . "'>" . $config['ui']['unknownValue'] . "</option>";
        $count = count($geofenceSrvc->geofences);
        for ($i = 0; $i < $count; $i++) {
          $geofence = $geofenceSrvc->geofences[$i];
          $filters .= "<option value='".$geofence->name."'>".$geofence->name."</option>";
        }
        $filters .= "
      </select>
    </div>
  </div>
</div>
";

$modal = "
<h2 class='page-header text-center'>Team gyms</h2>
<button type='button' class='btn btn-dark float-right' data-toggle='modal' data-target='#filtersModal'>
  <i class='fa fa-fw fa-filter' aria-hidden='true'></i>
</button>
<p>&nbsp;</p>
<div class='modal fade' id='filtersModal' tabindex='-1' role='dialog' aria-labelledby='filtersModalLabel' aria-hidden='true'>
  <div class='modal-dialog' role='document'>
    <div class='modal-content'>
      <div class='modal-header'>
        <h5 class='modal-title' id='filtersModalLabel'>Gym Filters</h5>
        <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
          <span aria-hidden='true'>&times;</span>
        </button>
      </div>
      <div class='modal-body'>" . $filters . "</div>
      <div class='modal-footer'>
        <button type='button' class='btn btn-primary' data-dismiss='modal'>Close</button>
      </div>
    </div>
  </div>
</div>
";

// Establish connection to database
$db = new DbConnector($config['db']);
$pdo = $db->getConnection();

// Query Database and Build Raid Billboard
try {
  $sql = "
SELECT 
  lat, 
  lon,
  guarding_pokemon_id,
  availble_slots,
  team_id,
  in_battle,
  name,
  updated
FROM 
  " . $config['db']['dbname'] . ".gym
WHERE
  name IS NOT NULL &&
  enabled=1;
";

  $result = $pdo->query($sql);
  if ($result->rowCount() > 0) {
    echo $modal;
    echo "<div id='no-more-tables'>";
    echo "<table id='gym-table' class='table table-".$config['ui']['table']['style']." ".($config['ui']['table']['striped'] ? 'table-striped' : null)."' border='1'>";
    echo "<thead class='thead-".$config['ui']['table']['headerStyle']."'>";
    echo "<tr class='text-nowrap'>";
      echo "<th>Remove</th>";
      echo "<th>Team</th>";
      echo "<th>Available Slots</th>";
      echo "<th>Guarding Pokemon</th>";
      echo "<th>In Battle</th>";
      echo "<th>City</th>";
      echo "<th>Gym</th>";
      echo "<th>Updated</th>";
    echo "</tr>";
    echo "</thead>";
    while ($row = $result->fetch()) {	
      $geofence = $geofenceSrvc->get_geofence($row['lat'], $row['lon']);
      $city = ($geofence == null ? $config['ui']['unknownValue'] : $geofence->name);
      $map_link = sprintf($config['google']['maps'], $row["lat"], $row["lon"]);

      $team = get_team($row['team_id']);
      $available_slots = $row['availble_slots'];
      $guarding_pokemon_id = $row['guarding_pokemon_id'];
      $in_battle = $row['in_battle'];

      echo "<tr class='text-nowrap'>";
        echo "<td scope='row' class='text-center' data-title='Remove'><a title='Remove' data-toggle='tooltip' class='delete'><i class='fa fa-times'></i></a></td>";
        echo "<td data-title='Team'><img src='./static/images/teams/" . strtolower($team) . ".png' height=32 width=32 />&nbsp;" . $team . "</td>";
        echo "<td data-title='Available Slots'>" . ($available_slots == 0 ? "Full" : $available_slots) . "</td>";
        echo "<td data-title='Guarding Pokemon'>" . $pokedex[$guarding_pokemon_id] . "</td>";
        echo "<td data-title='In Battle'>" . ($in_battle ? "Under Attack!" : "Safe") . "</td>";
        echo "<td data-title='City'>" . $city . "</td>";
        echo "<td data-title='Gym'><a href='" . $map_link . "' target='_blank'>" . $row['name'] . "</a></td>";
        echo "<td data-title='Updated'>" . date($config['core']['dateTimeFormat'], $row['updated']) . "</td>";
      echo "</tr>";
    }
    echo "</table>";
    echo "</div>";
		
    // Free result set
    unset($result);
  } else{
    echo "<p>No gyms found.</p>";
  }
} catch(PDOException $e){
  die("ERROR: Could not able to execute $sql. " . $e->getMessage());
}
// Close connection
unset($pdo);
unset($db);

?>
<style>
@media only screen and (max-width: 800px) {
	#unseen table td:nth-child(2), 
	#unseen table th:nth-child(2) {display: none;}
}
 
@media only screen and (max-width: 640px) {
	#unseen table td:nth-child(4),
	#unseen table th:nth-child(4),
	#unseen table td:nth-child(7),
	#unseen table th:nth-child(7),
	#unseen table td:nth-child(8),
	#unseen table th:nth-child(8){display: none;}
}
</style>
<script type="text/javascript">
$(document).on("click", ".delete", function(){
  $(this).parents("tr").remove();
  $(".add-new").removeAttr("disabled");
});
</script>