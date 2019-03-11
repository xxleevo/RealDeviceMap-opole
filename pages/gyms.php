<?php
include './vendor/autoload.php';
include './includes/GeofenceService.php';
include './static/data/pokedex.php';

$geofenceSrvc = new GeofenceService();

$filters = "
<div class='container'>
  <div class='row'>
    <div class='input-group mb-3'>
      <div class='input-group-prepend'>
        <label class='input-group-text' for='filter-gym'>Gym</label>
      </div>
      <input type='text' id='filter-gym' class='form-control input-lg' onkeyup='filter_gyms()' placeholder='Search by gym...' title='Type in a gym name'></input>
    </div>
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
<div class='btn-group btn-group-sm float-right'>
  <button type='button' class='btn btn-dark' data-toggle='modal' data-target='#filtersModal'>
    <i class='fa fa-fw fa-filter' aria-hidden='true'></i>
  </button>
  <button type='button' class='btn btn-dark' data-toggle='modal' data-target='#columnsModal'>
    <i class='fa fa-fw fa-columns' aria-hidden='true'></i>
  </button>
</div>
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
        <button type='button' class='btn btn-danger' id='reset-filters'>Reset Filters</button>
        <button type='button' class='btn btn-primary' data-dismiss='modal'>Close</button>
      </div>
    </div>
  </div>
</div>
<div class='modal fade' id='columnsModal' tabIndex='-1' role='dialog' aria-labelledby='columnsModalLabel' aria-hidden='true'>
  <div class='modal-dialog' role='document'>
    <div class='modal-content'>
      <div class='modal-header'>
        <h5 class='modal-title' id='columnsModalLabel'>Show Columns</h5>
        <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
          <span aria-hidden='true'>&times;</span>
        </button>
      </div>    
      <div class='modal-body'>
        <div id='chkColumns'>
          <p><input type='checkbox' name='team'/>&nbsp;Team</p>
          <p><input type='checkbox' name='slots'/>&nbsp;Available Slots</p>
          <p><input type='checkbox' name='guard'/>&nbsp;Guarding Pokemon</p>
          <p><input type='checkbox' name='battle'/>&nbsp;In Battle</p>
          <p><input type='checkbox' name='city'/>&nbsp;City</p>
          <p><input type='checkbox' name='updated'/>&nbsp;Updated</p>
        </div>
      </div>
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
  gym
WHERE
  name IS NOT NULL
  AND enabled = 1;
";

    $result = $pdo->query($sql);
    if ($result->rowCount() > 0) {
        echo $modal;
        echo "<div id='no-more-tables'>";
        echo "<table id='gym-table' class='table table-".$config['ui']['table']['style']." ".($config['ui']['table']['striped'] ? 'table-striped' : null)."' border='1'>";
        echo "<thead class='thead-".$config['ui']['table']['headerStyle']."'>";
        echo "<tr class='text-nowrap'>";
            echo "<th class='remove'>Remove</th>";
            echo "<th class='gym'>Gym</th>";
            echo "<th class='team'>Team</th>";
            echo "<th class='slots'>Available Slots</th>";
            echo "<th class='guard'>Guarding Pokemon</th>";
            echo "<th class='battle'>In Battle</th>";
            echo "<th class='city'>City</th>";
            echo "<th class='updated'>Updated</th>";
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
                echo "<td data-title='Gym'><a href='" . $map_link . "' target='_blank'>" . $row['name'] . "</a></td>";
                echo "<td data-title='Team'><img src='./static/images/teams/" . strtolower($team) . ".png' height=32 width=32 />&nbsp;" . $team . "</td>";
                echo "<td data-title='Available Slots'>" . ($available_slots == 0 ? "Full" : $available_slots) . "</td>";
                echo "<td data-title='Guarding Pokemon'>" . $pokedex[$guarding_pokemon_id] . "</td>";
                echo "<td data-title='In Battle'>" . ($in_battle ? "Under Attack!" : "Safe") . "</td>";
                echo "<td data-title='City'>" . $city . "</td>";
                echo "<td data-title='Updated'>" . date($config['core']['dateTimeFormat'], $row['updated']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "</div>";
	  	
        // Free result set
        unset($result);
    } else {
        echo "<p>No gyms found.</p>";
    }
} catch (PDOException $e) {
    die("ERROR: Could not able to execute $sql. " . $e->getMessage());
}
// Close connection
unset($pdo);
unset($db);
?>

<script type="text/javascript">
/*
$("#gym-table").DataTable({
  "paging": true,
  "pagingType": "simple_numbers",
  "pageLength": 25,
  "orderMulti": true,
  "info": true,
  "searching": true,
  "ajax": ''
});
*/

$(document).on("click", ".delete", function(){
  $(this).parents("tr").remove();
  $(".add-new").removeAttr("disabled");
});

var checkbox = $("#chkColumns input:checkbox"); 
var tbl = $("#gym-table");
var tblHead = $("#gym-table th");
checkbox.prop('checked', true); 
checkbox.click(function () {
  var colToHide = tblHead.filter("." + $(this).attr("name"));
  var index = $(colToHide).index();
  tbl.find('tr :nth-child(' + (index + 1) + ')').toggle();
});

if (get("gyms-filter-team") !== false) {
  $('#filter-team').val(get("gyms-team"));
}
if (get("gyms-filter-slots") !== false) {
  $('#filter-slots').val(get("gyms-filter-slots"));
}
if (get("gyms-filter-battle") !== false) {
  $('#filter-battle').val(get("gyms-filter-battle"));
}
if (get("gyms-filter-city") !== false) {
  $('#filter-city').val(get("gyms-filter-city"));
}
if (get("gyms-filter-gym") !== false) {
  $('#filter-gym').val(get("gyms-filter-gym"));
}

filter_gyms();

$('#reset-filters').on('click', function() {
  if (confirm("Are you sure you want to reset the gym filters?")) {
    $('#filter-team').val('All');
    $('#filter-slots').val('All');
    $('#filter-battle').val('All');
    $('#filter-city').val('All');
    $('#filter-gym').val('');
    filter_gyms();
  }
});
</script>