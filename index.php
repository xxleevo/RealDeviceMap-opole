<?php
include './config.php';
include './pokedex.php';
include './movesets.php';
include './geofence_service.php';

$googleMapsLink = "https://maps.google.com/maps?q=%s,%s";
$appleMapsLink = "https://maps.apple.com/maps?daddr=%s,%s";

$geofence_srvc = new GeofenceService();
$page = $_SERVER['PHP_SELF'];
$sec = "60";

$filters = "
<link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css' integrity='sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS' crossorigin='anonymous'>
<div class='panel panel-default'>
<div class='form-group row'>
  <div class='col-md-4'> 
    <div class='input-group'>
    Search Pokemon:&nbsp;
    <input type='text' id='search-input' class='form-control input-lg' style='display:initial !important;' onkeyup='filter_raids()' placeholder='Search by name..' title='Type in a name'>
  </div>
</div>
<div class='col-md-4'> 
  <div class='input-group'>
    Search by city:&nbsp;
    <select id='filter-city' class='form-control' style='display:initial !important;' onchange='filter_raids()'>
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
<div class='form-group row'>
  <div class='col-md-2'> 
    <div class='input-group'>
    Search by level:&nbsp;
    <select id='filter-level' class='form-control' style='display:initial !important;' onchange='filter_raids()'>
        <option disabled selected>Select</option>
        <option value='all'>All</option>
        <option value='1'>1</option>
        <option value='2'>2</option>
        <option value='3'>3</option>
        <option value='4'>4</option>
        <option value='5'>5</option>
      </select>
    </div>
  </div>
  <div class='col-md-2'> 
    <div class='input-group'>
      Search by team:&nbsp;
      <select id='filter-team' class='form-control' style='display:initial !important;' onchange='filter_raids()'>
        <option disabled selected>Select</option>
        <option value='all'>All</option>
        <option value='Neutral'>Neutral</option>
        <option value='Mystic'>Mystic</option>
        <option value='Valor'>Valor</option>
        <option value='Instinct'>Instinct</option>
      </select>
    </div>
  </div>
  <div class='col-md-2'> 
    <div class='input-group'>
      Search by Ex-Eligibility:&nbsp;
      <select id='filter-ex' class='form-control' style='display:initial !important;' onchange='filter_raids()'>
        <option disabled selected>Select</option>
        <option value='all'>All</option>
        <option value='yes'>Yes</option>
        <option value='no'>No</option>
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
} catch(PDOException $e){
  die("ERROR: Could not connect. " . $e->getMessage());
}
// Query Database and Build Raid Billboard
try {
  $sql = "
SELECT 
    time_format(from_unixtime(raid_battle_timestamp), '%h:%i:%s %p')
        AS starts, 
    time_format(from_unixtime(raid_end_timestamp),'%h:%i:%s %p')
        AS ends, 
    lat, 
    lon,
    raid_level,
    raid_pokemon_id, 
    raid_pokemon_move_1,
    raid_pokemon_move_2,
    name,
    team_id,
    ex_raid_eligible
FROM 
    rdmdb.gym
WHERE
    raid_pokemon_id IS NOT NULL && 
    name IS NOT NULL && 
    raid_end_timestamp >= UNIX_TIMESTAMP()
ORDER BY 
    raid_end_timestamp;
";

  $result = $pdo->query($sql);
  if($result->rowCount() > 0){
    echo $filters;
    echo "<table class='table table-".$table_style." ".($table_striped ? 'table-striped' : null)."' border='1' id='gym-table';>";
    echo "<thead class='thead-".$table_header_style."'>";
    echo "<tr>";
        echo "<th>Raid Starts</th>";
        echo "<th>Raid Ends</th>";
        echo "<th>Raid Level</th>";
        echo "<th>Raid Boss</th>";
        echo "<th>Moveset</th>";
        echo "<th headers='city'>City</th>";
        echo "<th>Team</th>";
        echo "<th>Ex-Eligible</th>";
        echo "<th>Gym</th>";
    echo "</tr>";
    echo "</thead>";
    while($row = $result->fetch()){	
      $geofence = $geofence_srvc->get_geofence($row['lat'], $row['lon']);
      $city = ($geofence == null ? $unknown_value : $geofence->name);
      $map_link = sprintf($googleMapsLink, $row["lat"], $row["lon"]);
      $pokemon = $pokedex[$row['raid_pokemon_id']];
      $fast_move = $quick_moves[$row['raid_pokemon_move_1']];
      $charge_move = $charge_moves[$row['raid_pokemon_move_2']];
      $moveset = (($fast_move == $unknown_value && $charge_move == $unknown_value) ? $unknown_value : $fast_move . "/" . $charge_move);
      echo "<tr>";
        echo "<td scope='row'>" . $row['starts'] . "</td>";
        echo "<td>" . $row['ends'] . "</td>";
        echo "<td>" . $row['raid_level'] . "</td>";
        echo "<td>" . $pokemon . "</td>";
        echo "<td>" . $moveset . "</td>";
        echo "<td>" . $city . "</td>";
        echo "<td>" . get_team($row['team_id']) . "</td>";
        echo "<td>" . ($row['ex_raid_eligible'] ? "Yes" : "No") . "</td>";
        echo "<td><a href='" . $map_link . "' target='_blank'>" . $row['name'] . "</a></td>";
      echo "</tr>";
    }
    echo "</table>";
    echo "<script src='https://code.jquery.com/jquery-3.3.1.slim.min.js' integrity='sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo' crossorigin='anonymous'></script>";
    echo "<script src='https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js' integrity='sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut' crossorigin='anonymous'></script>";
    echo "<script src='https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js' integrity='sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k' crossorigin='anonymous'></script>";
		
  // Free result set
  unset($result);
  } else{
    echo "<p>No records matching your query were found.</p>";
  }
} catch(PDOException $e){
  die("ERROR: Could not able to execute $sql. " . $e->getMessage());
}
// Close connection
unset($pdo);

if ($google_analytics_id != "") {
  echo "
<!-- Google Analytics -->
<script>
  window.ga=window.ga||function(){(ga.q=ga.q||[]).push(arguments)};ga.l=+new Date;
  ga('create', '" . $google_analytics_id . "', 'auto');
  ga('send', 'pageview');
</script>
<script async src='https://www.google-analytics.com/analytics.js'></script>
<!-- End Google Analytics -->'";
}

if (google_adsense_id != "") {
  echo "
<script async src='//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js'></script>
<script>
  (adsbygoogle = window.adsbygoogle || []).push({
    google_ad_client: '" . $google_adsense_id . "',
    enable_page_level_ads: true
  });
</script>";
}

function get_team($team_id) {
  switch ($team_id) {
    case "1":
      return "Mystic";
    case "2":
      return "Valor";
    case "3":
      return "Instinct";
    default:
      return "Neutral";
  }
}

?>
<html>
  <head>
    <meta http-equiv="refresh" content="<?php echo $sec?>;URL='<?php echo $page?>'">
  </head>
<script>
function filter_raids() {
  var search_filter = document.getElementById("search-input").value.toUpperCase();
  var city_filter = document.getElementById("filter-city").value.toUpperCase();
  var level_filter = document.getElementById("filter-level").value.toUpperCase();
  var team_filter = document.getElementById("filter-team").value.toUpperCase();
  var ex_filter = document.getElementById("filter-ex").value.toUpperCase();
  
  console.log("Pokemon:", search_filter, "City:", city_filter, "Level:", level_filter, "Team:", team_filter, "Ex:", ex_filter);
  
  if (city_filter.toLowerCase().indexOf("all") === 0 ||
    city_filter.toLowerCase().indexOf("select") === 0) {
    city_filter = "";
    console.log("City filter cleared");
  }
  
  if (level_filter.toLowerCase().indexOf("all") === 0 ||
    level_filter.toLowerCase().indexOf("select") === 0) {
    level_filter = "";
    console.log("Level filter cleared");
  }
  
  if (team_filter.toLowerCase().indexOf("all") === 0 ||
    team_filter.toLowerCase().indexOf("select") === 0) {
    team_filter = "";
    console.log("Team filter cleared");
  }
  
  if (ex_filter.toLowerCase().indexOf("all") === 0 ||
    ex_filter.toLowerCase().indexOf("select") === 0) {
    ex_filter = "";
    console.log("Ex filter cleared");
  }
 
  var table = document.getElementById("gym-table");
  var tr = table.getElementsByTagName("tr");
  for (var i = 0; i < tr.length; i++) {
    if (i == 0)
      continue;
	
    var level_value = table.rows[i].cells[2].innerHTML;
    var pkmn_value = table.rows[i].cells[3].innerHTML.toUpperCase();
    var city_value = table.rows[i].cells[5].innerHTML.toUpperCase();
    var team_value = table.rows[i].cells[6].innerHTML.toUpperCase();
    var ex_value = table.rows[i].cells[7].innerHTML.toUpperCase();
	
    if (pkmn_value.indexOf(search_filter) > -1 &&
      city_value.indexOf(city_filter) > -1 &&
      level_value.indexOf(level_filter) > -1 &&
      team_value.indexOf(team_filter) > -1 &&
      ex_value.indexOf(ex_filter) > -1) {
      tr[i].style.display = "";
    } else {
      tr[i].style.display = "none";
    }     
  }
}
</script>
</html>