<?php
include './vendor/autoload.php';
include './config.php';
include './pokedex.php';
include './geofence_service.php';

$geofence_srvc = new GeofenceService();

$filters = "
<div class='panel panel-default'>
  <div class='form-group row'>
    <div class='col-md-4'>
      <div class='input-group'>
        Search by team:&nbsp;
        <select id='filter-team' class='form-control' style='display:initial !important;' onchange='filter_gyms()'>
          <option disabled selected>Select</option>
          <option value='all'>All</option>
          <option value='Neutral'>Neutral</option>
          <option value='Mystic'>Mystic</option>
          <option value='Valor'>Valor</option>
          <option value='Instinct'>Instinct</option>
        </select>
      </div>
    </div>
    <div class='col-md-4'>
      <div class='input-group'>
        Search by slots:&nbsp;
        <select id='filter-slots' class='form-control' style='display:initial !important;' onchange='filter_gyms()'>
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
    </div>
  </div>
  <div class='form-group row'>
    <div class='col-md-4'>
      <div class='input-group'>
        Search by battle status:&nbsp;
        <select id='filter-battle' class='form-control' style='display:initial !important;' onchange='filter_gyms()'>
     	   <option disabled selected>Select</option>
     	   <option value='all'>All</option>
     	   <option value='Under Attack!'>Yes</option>
     	   <option value='Safe'>No</option>
        </select>
      </div>
    </div>
    <div class='col-md-4'> 
      <div class='input-group'>
        Search by city:&nbsp;
        <select id='filter-city' class='form-control' style='display:initial !important;' onchange='filter_gyms()'>
          <option disabled selected>Select</option>
          <option value='all'>All</option>";
          $count = count($geofence_srvc->geofences);
          for ($i = 0; $i < $count; $i++) {
            $geofence = $geofence_srvc->geofences[$i];
            $filters .= "<option value='".$geofence->name."'>".$geofence->name."</option>";
          }
          $filters .= "
        </select>
      </div>
    </div>
  </div>
</div>
";

// Establish connection to database
try {
  $pdo = new PDO("mysql:host=$dbhost;dbname=$dbname;port=$dbPort", $dbuser, $dbpass);
  // Set the PDO error mode to exception
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
  die("ERROR: Could not connect. " . $e->getMessage());
}
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
  name
FROM 
  " . $dbname . ".gym
WHERE
  name IS NOT NULL &&
  enabled=1;
";

  $result = $pdo->query($sql);
  if ($result->rowCount() > 0) {
    echo $filters;
    echo "<table class='table table-".$table_style." ".($table_striped ? 'table-striped' : null)."' border='1' id='quest-table';>";
    echo "<thead class='thead-".$table_header_style."'>";
    echo "<tr>";
        echo "<th>Team</th>";
        echo "<th>Available Slots</th>";
        echo "<th>Guarding Pokemon</th>";
        echo "<th>In Battle</th>";
        echo "<th>City</th>";
        echo "<th>Gym</th>";
    echo "</tr>";
    echo "</thead>";
    while ($row = $result->fetch()) {	
      $geofence = $geofence_srvc->get_geofence($row['lat'], $row['lon']);
      $city = ($geofence == null ? $unknown_value : $geofence->name);
      $map_link = sprintf($googleMapsLink, $row["lat"], $row["lon"]);

	  $team = get_team($row['team_id']);
	  $available_slots = $row['availble_slots'];
	  $guarding_pokemon_id = $row['guarding_pokemon_id'];
	  $in_battle = $row['in_battle'];

      echo "<tr>";
        echo "<td scope='row'><a title='Remove' data-toggle='tooltip' class='delete'>X&nbsp;</a><img src='./static/images/teams/" . strtolower($team) . ".png' height=32 width=32 />&nbsp;" . $team . "</td>";
        echo "<td>" . ($available_slots == 0 ? "Full" : $available_slots) . "</td>";
        echo "<td>" . $pokedex[$guarding_pokemon_id] . "</td>";
        echo "<td>" . ($in_battle ? "Under Attack!" : "Safe") . "</td>";
        echo "<td>" . $city . "</td>";
        echo "<td><a href='" . $map_link . "' target='_blank'>" . $row['name'] . "</a></td>";
      echo "</tr>";
    }
    echo "</table>";
		
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

function get_team($team_id) {
  switch ($team_id) {
    case "1": return "Mystic";
    case "2": return "Valor";
    case "3": return "Instinct";
    default:  return "Neutral";
  }
}

?>

<script type="text/javascript">
$(document).on("click", ".delete", function(){
  $(this).parents("tr").remove();
  $(".add-new").removeAttr("disabled");
});

function filter_gyms() {
  var team_filter = document.getElementById("filter-team").value.toUpperCase();
  var slots_filter = document.getElementById("filter-slots").value.toUpperCase();
  var battle_filter = document.getElementById("filter-battle").value.toUpperCase();
  var city_filter = document.getElementById("filter-city").value.toUpperCase();
  
  console.log("Team:", team_filter, "Slots:", slots_filter, "In Battle:", battle_filter, "City:", city_filter);
  
  if (team_filter.toLowerCase().indexOf("all") === 0 ||
    team_filter.toLowerCase().indexOf("select") === 0) {
    team_filter = "";
    console.log("Team filter cleared");
  }
  
  if (slots_filter.toLowerCase().indexOf("all") === 0 ||
    slots_filter.toLowerCase().indexOf("select") === 0) {
    slots_filter = "ALL";
    console.log("Available slots filter cleared");
  }
  
  if (battle_filter.toLowerCase().indexOf("all") === 0 ||
    battle_filter.toLowerCase().indexOf("select") === 0) {
    battle_filter = "";
    console.log("Battle filter cleared");
  }
  
  if (city_filter.toLowerCase().indexOf("all") === 0 ||
    city_filter.toLowerCase().indexOf("select") === 0) {
    city_filter = "";
    console.log("City filter cleared");
  }
 
  var table = document.getElementById("quest-table");
  var tr = table.getElementsByTagName("tr");
  for (var i = 0; i < tr.length; i++) {
    if (i == 0)
      continue;
  
    var team_value = table.rows[i].cells[0].innerHTML.toUpperCase();
    var slots_value = table.rows[i].cells[1].innerHTML.toUpperCase();
    var battle_value = table.rows[i].cells[3].innerHTML.toUpperCase();
    var city_value = table.rows[i].cells[4].innerHTML.toUpperCase();

    if (team_value.indexOf(team_filter) > -1 && 
        ((slots_value >= slots_filter && slots_value.indexOf("FULL") == -1) || (slots_value == slots_filter && slots_filter.indexOf("FULL") >= -1) || slots_filter.indexOf("ALL") > -1) &&
        battle_value.indexOf(battle_filter) > -1 &&
        city_value.indexOf(city_filter) > -1) {
      tr[i].style.display = "";
    } else {
      tr[i].style.display = "none";
    }     
  }
}
</script>