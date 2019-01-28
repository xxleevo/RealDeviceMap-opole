<?php
include 'pokedex.php';
include 'movesets.php';
include 'geofence_service.php';

// RealDeviceMap Database
$dbhost = "127.0.0.1";        // Database host name or IP address
$dbPort = 3306;               // Database port. (default: 3306)
$dbuser = "user";             // Database username.
$dbpass = "password";         // Database user password.
$dbname = "rdmdb";            // Database name.

// Table Style
$table_style = "light";       // light/dark
$table_header_style = "dark"; // light/dark
$table_striped = true;        // true/false

// StaticMap Lite/Google Maps Url 
$staticImage = "http://staticmaplite-url-here/staticmap.php?center=%s,%s&markers=%s,%s,red-pushpin&zoom=14&size=300x175&maptype=mapnik"; //https://github.com/dfacts/staticmaplite

$unknown_value = "Unknown";

$googleMapsLink = "https://maps.google.com/maps?q=%s,%s";
$appleMapsLink = "https://maps.apple.com/maps?daddr=%s,%s";

$geofence_srvc = new GeofenceService();
$page = $_SERVER['PHP_SELF'];
$sec = "60";
//style='display:inline-block' 
echo "
<p>
<span>
	Search:&nbsp;
	<input type='text' id='search-input' class='form-control input-lg' onkeyup='search()' placeholder='Search for names..' title='Type in a name'>
<select id='filter-city' class='form-control' onchange='filter_cities()'>
    <option disabled selected>Select</option>
	<option value='all'>All</option>
    <option value='Rancho'>Rancho</option>
    <option value='Upland'>Upland</option>
	<option value='Ontario'>Ontario</option>
	<option value='Pomona'>Pomona</option>
	<option value='Claremont'>Claremont</option>
    <option value='Montclair'>Montclair</option>
</select>
</span>
</p>";

// Establish connection to database
try{
    $pdo = new PDO("mysql:host=$dbhost;dbname=$dbname;port=$dbPort", $dbuser, $dbpass);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e){
    die("ERROR: Could not connect. " . $e->getMessage());
}
// Query Database and Build Raid Billboard
try 
{
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
FROM rdmdb.gym
WHERE
	raid_pokemon_id IS NOT NULL && 
	name IS NOT NULL && 
	raid_end_timestamp >= UNIX_TIMESTAMP()
ORDER BY 
	raid_end_timestamp;
";

	$result = $pdo->query($sql);
	if($result->rowCount() > 0){
		echo "<link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css' integrity='sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS' crossorigin='anonymous'>";
		echo "<table class='table table-".$table_style." ".($table_striped ? 'table-striped' : null)."' border='1' id='gym-table';>";
		echo "<thead class='thead-".$table_header_style."'>";
			echo "<tr>";
				echo "<th>Raid Starts</th>";
				echo "<th>Raid Ends</th>";
				echo "<th>Raid Level</th>";
				echo "<th>Raid Boss</th>";
				echo "<th>Moveset</th>";
				echo "<th>City</th>";
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
			$moveset = ($fast_move == $unknown_value && $charge_move == $unknown_value ? $unknown_value : $fast_move . "/" . $charge_move);
			echo "<tr id='content'>";
				echo "<td scope='row'>" . $row['starts'] . "</td>";
				echo "<td>" . $row['ends'] . "</td>";
				echo "<td>" . $row['raid_level'] . "</td>";
				echo "<td>" . $pokemon . "</td>";
				echo "<td>" . $moveset . "</td>";
				echo "<td>" . $city . "</td>";
				echo "<td>" . get_team($row['team_id']) . "</td>";
				echo "<td>" . ($row['ex_raid_eligible'] ? "Yes" : "No") . "</td>";
				echo "<td><a href='" . $map_link . "' target='_blank'>" . $row['name'] . "</a></td>";
				//echo "<img src='" . sprintf($staticImage, $row["lat"], $row["lon"], $row["lat"], $row["lon"]) . "' height='128' width='256' /></td>";
			echo "</tr>";
		}
		echo "</table>";
		echo "<script src='https://code.jquery.com/jquery-3.3.1.slim.min.js' integrity='sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo' crossorigin='anonymous'></script>";
		echo "<script src='https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js' integrity='sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut' crossorigin='anonymous'></script>";
		echo "<script src='https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js' integrity='sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k' crossorigin='anonymous'></script>";
		// Free result set
		unset($result);
	} else{
		echo "No records matching your query were found.";
	}
} catch(PDOException $e){
    die("ERROR: Could not able to execute $sql. " . $e->getMessage());
}
// Close connection
unset($pdo);

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
function search() {
  var input = document.getElementById("search-input");
  var filter = input.value.toUpperCase();
  var table = document.getElementById("gym-table");
  var tr = table.getElementsByTagName("tr");
  for (var i = 0; i < tr.length; i++) {
	  if (i == 0)
		  continue;
    value = tr[i].innerText.toUpperCase();
    if (value.toUpperCase().indexOf(filter) > -1) {
	  tr[i].style.display = "";
    } else {
	  tr[i].style.display = "none";
    }     
  }
}
function filter_cities()
{  
	var rex = new RegExp($('#filter-city').val());
	if(rex =="/all/"){clearFilter()}else{
		$('.content').hide();
		$('.content').filter(function() {
		return rex.test($(this).text());
		}).show();
	}
}
function clear_filter()
{
	$('.filter-city').val('');
	$('.content').show();
}
</script>
</html>