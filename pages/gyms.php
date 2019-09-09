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
        <label class='input-group-text' for='filter-gym' data-i18n='gyms_filter_gym'>Arena</label>
      </div>
      <input type='text' id='filter-gym' class='form-control input-lg' onkeyup='filter_gyms()' placeholder='Nach Namen suchen...' title='Namen suchen'></input>
    </div>
    <div class='input-group mb-3'>
      <div class='input-group-prepend'>
        <label class='input-group-text' for='filter-team' data-i18n='gyms_filter_team'>Team</label>
      </div>
      <select id='filter-team' class='custom-select' onchange='filter_gyms()'>
        <!--<option value='all' selected>All</option>-->
        <option value='Neutral'>Neutral</option>
        <option value='Mystic'>Mystic</option>
        <option value='Valor'>Valor</option>
        <option value='Instinct'>Instinct</option>
      </select>
    </div>
    <div class='input-group mb-3'>
      <div class='input-group-prepend'>
        <label class='input-group-text' for='filter-slots' data-i18n='gyms_filter_available_slots'>Freie Plätze</label>
      </div>
      <select id='filter-slots' class='custom-select' onchange='filter_gyms()'>
        <!--<option value='all' selected>All</option>-->
        <option value='full'>Voll</option>
        <option value='1'>1</option>
        <option value='2'>2</option>
        <option value='3'>3</option>
        <option value='4'>4</option>
        <option value='5'>5</option>
        <option value='6'>Leer</option>
      </select>
    </div>
    <div class='input-group mb-3'>
      <div class='input-group-prepend'>
        <label class='input-group-text' for='filter-battle' data-i18n='gyms_filter_battle'>Kampfstatus</label>
      </div>
      <select id='filter-battle' class='custom-select' onchange='filter_gyms()'>
        <!--<option value='all' selected>All</option>-->
        <option value='Angriff!'>In Angriff</option>
        <option value='Sicher'>Sicher</option>
      </select>
    </div>
    <div class='input-group mb-3'>
      <div class='input-group-prepend'>
        <label class='input-group-text' for='filter-city' data-i18n='gyms_filter_city'>City</label>
      </div>
      <select multiple id='filter-city' class='custom-select' onchange='filter_gyms()'>
        <option value='' selected>All</option>";
        $count = count($geofenceSrvc->geofences);
        for ($i = 0; $i < $count; $i++) {
            $geofence = $geofenceSrvc->geofences[$i];
            $filters .= "<option value='".$geofence->name."'>".$geofence->name."</option>";
        }
        $filters .= "
        <option value='" . $config['ui']['unknownValue'] . "'>" . $config['ui']['unknownValue'] . "</option>
      </select>
    </div>
  </div>
</div>
";

$modal = "
<h2 class='page-header text-center' data-i18n='gyms_title'>Arenenübersicht</h2>
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
        <h5 class='modal-title' id='filtersModalLabel' data-i18n='gyms_filters_title'>Arenen filtern</h5>
        <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
          <span aria-hidden='true'>&times;</span>
        </button>
      </div>
      <div class='modal-body'>" . $filters . "</div>
      <div class='modal-footer'>
        <button type='button' class='btn btn-danger' id='reset-filters' data-i18n='gyms_modal_reset_filters'>Filter zurücksetzen</button>
        <button type='button' class='btn btn-primary' data-dismiss='modal' data-i18n='gyms_modal_close'>Schließen</button>
      </div>
    </div>
  </div>
</div>
<div class='modal fade' id='columnsModal' tabIndex='-1' role='dialog' aria-labelledby='columnsModalLabel' aria-hidden='true'>
  <div class='modal-dialog' role='document'>
    <div class='modal-content'>
      <div class='modal-header'>
        <h5 class='modal-title' id='columnsModalLabel' data-i18n='gyms_modal_show_columns'>Spaltenanzeige</h5>
        <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
          <span aria-hidden='true'>&times;</span>
        </button>
      </div>    
      <div class='modal-body'>
        <div id='chkColumns'>
          <p><input type='checkbox' name='team'/>&nbsp;Team</p>
          <p><input type='checkbox' name='slots'/>&nbsp;Freie Plätze</p>
          <p><input type='checkbox' name='guard'/>&nbsp;Verteidiger</p>
          <p><input type='checkbox' name='battle'/>&nbsp;Kampfstatus</p>
          <!--<p><input type='checkbox' name='city'/>&nbsp;Stadt</p>-->
          <!--<p><input type='checkbox' name='updated'/>&nbsp;Aktualisiert</p>-->
        </div>
      </div>
      <div class='modal-footer'>
        <button type='button' class='btn btn-primary' data-dismiss='modal' data-i18n='gyms_modal_close'>Schließen</button>
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
            echo "<th class='remove' data-i18n='gyms_column_remove'>Entfernen</th>";
            echo "<th class='gym' data-i18n='gyms_column_gym'>Arena</th>";
            echo "<th class='team' data-i18n='gyms_column_team'>Team</th>";
            echo "<th class='slots' data-i18n='gyms_column_available_slots'>Freie Plätze</th>";
            echo "<th class='guard' data-i18n='gyms_column_guarding_pokemon'>Verteidiger</th>";
            echo "<th class='battle data-i18n='gyms_column_battle'>Kampfstatus</th>";
            echo "<th class='city' data-i18n='gyms_column_city'>Stadt</th>";
            //echo "<th class='updated' data-i18n='gyms_column_updated'>Updated</th>";
        echo "</tr>";
        echo "</thead>";
        while ($row = $result->fetch()) {	
            $geofence = $geofenceSrvc->get_geofence($row['lat'], $row['lon']);
            if ($geofence == null && $config['ui']['pages']['gyms']['ignoreUnknown'] !== false) {
                continue;
            }
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
                echo "<td data-title='In Battle'>" . ($in_battle ? "Angriff! <img src='static/images/swords.png' width='32' height='auto' />" : "Sicher") . "</td>";
                echo "<td data-title='City'>" . $city . "</td>";
                //echo "<td data-title='Updated'>" . date($config['core']['dateTimeFormat'], $row['updated']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "</div>";
	  	
        // Free result set
        unset($result);
    } else {
        echo "<p>Keine Arenen gefunden.</p>";
    }
} catch (PDOException $e) {
    die("ERROR: Could not able to execute $sql. " . $e->getMessage());
}
// Close connection
unset($pdo);
unset($db);
?>

<link rel="stylesheet" href="./static/css/footerfix.css"/>
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
  $('#filter-city').val(JSON.parse(get("gyms-filter-city")));
}
if (get("gyms-filter-gym") !== false) {
  $('#filter-gym').val(get("gyms-filter-gym"));
}

filter_gyms();

$('#reset-filters').on('click', function() {
  if (confirm($.i18n('Zurücksetzen?'))) {
    $('#filter-team').val('All');
    $('#filter-slots').val('All');
    $('#filter-battle').val('All');
    $('#filter-city').val('');
    $('#filter-gym').val('');
    filter_gyms();
  }
});
</script>