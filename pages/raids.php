<?php
require_once './vendor/autoload.php';
require_once './config.php';
require_once './includes/GeofenceService.php';
require_once './static/data/pokedex.php';
require_once './static/data/movesets.php';

$geofence_srvc = new GeofenceService();

$filters = "
<div class='container'>
  <div class='row'>
    <div class='input-group mb-3'>
      <div class='input-group-prepend'>
        <label class='input-group-text' for='search-input'>Search Pokemon</label>
      </div>
      <input type='text' id='search-input' class='form-control input-lg' onkeyup='filter_raids()' placeholder='Search by name..' title='Type in a name'>
    </div>
    <div class='input-group mb-3'>
      <div class='input-group-prepend'>
        <label class='input-group-text' for='filter-city'>City</label>
      </div>
      <select id='filter-city' class='custom-select' onchange='filter_raids()'>
        <option disabled selected>Select</option>
        <option value='all'>All</option>
        <option value='" . $unknown_value . "'>" . $unknown_value . "</option>";
        $count = count($geofence_srvc->geofences);
        for ($i = 0; $i < $count; $i++) {
          $geofence = $geofence_srvc->geofences[$i];
          $filters .= "<option value='".$geofence->name."'>".$geofence->name."</option>";
        }
        $filters .= "
      </select>
    </div>
    <div class='input-group mb-3'>
      <div class='input-group-prepend'>
        <label class='input-group-text' for='filter-level'>Raid Level</label>
      </div>
      <select id='filter-level' class='custom-select' onchange='filter_raids()'>
        <option disabled selected>Select</option>
        <option value='all'>All</option>
        <option value='1'>1</option>
        <option value='2'>2</option>
        <option value='3'>3</option>
        <option value='4'>4</option>
        <option value='5'>5</option>
      </select>
    </div>
    <div class='input-group mb-3'>
      <div class='input-group-prepend'>
        <label class='input-group-text' for='filter-team'>Team</label>
      </div>
      <select id='filter-team' class='custom-select' onchange='filter_raids()'>
        <option disabled selected>Select</option>
        <option value='all'>All</option>
        <option value='Neutral'>Neutral</option>
        <option value='Mystic'>Mystic</option>
        <option value='Valor'>Valor</option>
        <option value='Instinct'>Instinct</option>
      </select>
    </div>
    <div class='input-group mb-3'>
      <div class='input-group-prepend'>
        <label class='input-group-text' for='search-input'>Ex-Eligible</label>
      </div>
      <select id='filter-ex' class='custom-select' onchange='filter_raids()'>
        <option disabled selected>Select</option>
        <option value='all'>All</option>
        <option value='yes'>Yes</option>
        <option value='no'>No</option>
      </select>
    </div>
  </div>
</div>
";

$modal = "
<button type='button' class='btn btn-dark float-right' data-toggle='modal' data-target='#filtersModal'>
  Filters
</button>
<div class='modal fade' id='filtersModal' tabindex='-1' role='dialog' aria-labelledby='filtersModalLabel' aria-hidden='true'>
  <div class='modal-dialog' role='document'>
    <div class='modal-content'>
      <div class='modal-header'>
        <h5 class='modal-title' id='filtersModalLabel'>Raid Filters</h5>
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

echo "<div id='table-refresh'>";
include_once("./includes/data_fetcher.php");
echo "</div>";
?>
<script type="text/javascript">
var refresh_rate = <?=$table_refresh_s?>;
var refresher = setInterval(filter_raids, refresh_rate * 1000);
setTimeout(function() { clearInterval(refresher); }, 1800000);

$(document).on("click", ".delete", function(){
  $(this).parents("tr").remove();
  $(".add-new").removeAttr("disabled");
});
</script>