<?php
require_once './vendor/autoload.php';
require_once './config.php';
require_once './includes/GeofenceService.php';
require_once './includes/utils.php';
require_once './static/data/pokedex.php';
require_once './static/data/movesets.php';

$geofenceSrvc = new GeofenceService();
$mobile = ($config['ui']['table']['forceRaidCards'] || (!$config['ui']['table']['forceRaidCards'] && is_mobile())) !== false ? '1' : '0';

$filters = "
<div class='container'>
  <div class='row'>
    <div class='input-group mb-3'>
      <div class='input-group-prepend'>
        <label class='input-group-text' for='search-input' data-i18n='raids_filter_pokemon'>Pokemon</label>
      </div>
      <input type='text' id='search-input' class='form-control input-lg' onkeyup='filter_raids($mobile)' placeholder='Search by name..' title='Type in a name'>
    </div>
    <div class='input-group mb-3'>
      <div class='input-group-prepend'>
        <label class='input-group-text' for='filter-gym' data-i18n='raids_filter_gym'>Gym</label>
      </div>
      <input type='text' id='filter-gym' class='form-control input-lg' onkeyup='filter_raids($mobile)' placeholder='Search by gym..' title='Type in a gym name'>
    </div>
    <div class='input-group mb-3'>
      <div class='input-group-prepend'>
        <label class='input-group-text' for='filter-city' data-i18n='raids_filter_city'>City</label>
      </div>
      <select multiple id='filter-city' class='custom-select' onchange='filter_raids($mobile)'>
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
    <div class='input-group mb-3'>
      <div class='input-group-prepend'>
        <label class='input-group-text' for='filter-level' data-i18n='raids_filter_level'>Raid Level</label>
      </div>
      <select id='filter-level' class='custom-select' onchange='filter_raids($mobile)'>
        <option value='all' selected>All</option>
        <option value='1'>1</option>
        <option value='2'>2</option>
        <option value='3'>3</option>
        <option value='4'>4</option>
        <option value='5'>5</option>
      </select>
    </div>
    <div class='input-group mb-3'>
      <div class='input-group-prepend'>
        <label class='input-group-text' for='filter-team' data-i18n='raids_filter_team'>Team</label>
      </div>
      <select id='filter-team' class='custom-select' onchange='filter_raids($mobile)'>
        <option value='all' selected>All</option>
        <option value='Neutral'>Neutral</option>
        <option value='Mystic'>Mystic</option>
        <option value='Valor'>Valor</option>
        <option value='Instinct'>Instinct</option>
      </select>
    </div>
    <div class='input-group mb-3'>
      <div class='input-group-prepend'>
        <label class='input-group-text' for='search-input' data-i18n='raids_filter_ex'>Ex-Eligible</label>
      </div>
      <select id='filter-ex' class='custom-select' onchange='filter_raids($mobile)'>
        <option value='all' selected>All</option>
        <option value='yes'>Yes</option>
        <option value='no'>No</option>
      </select>
    </div>
  </div>
</div>
";

$modal = "
<h2 class='page-header text-center' data-i18n='raids_title'>Ongoing raid battles</h2>
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
        <h5 class='modal-title' id='filtersModalLabel' data-i18n='raids_filters_title'>Raid Filters</h5>
        <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
          <span aria-hidden='true'>&times;</span>
        </button>
      </div>
      <div class='modal-body'>" . $filters . "</div>
      <div class='modal-footer'>
        <button type='button' class='btn btn-danger' id='reset-filters' data-i18n='raids_modal_reset_filters'>Reset Filters</button>
        <button type='button' class='btn btn-primary' data-dismiss='modal' data-i18n='raids_modal_close'>Close</button>
      </div>
    </div>
  </div>
</div>
<div class='modal fade' id='columnsModal' tabIndex='-1' role='dialog' aria-labelledby='columnsModalLabel' aria-hidden='true'>
  <div class='modal-dialog' role='document'>
    <div class='modal-content'>
      <div class='modal-header'>
        <h5 class='modal-title' id='columnsModalLabel' data-i18n='raids_modal_show_columns'>Show Columns</h5>
        <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
          <span aria-hidden='true'>&times;</span>
        </button>
      </div>    
      <div class='modal-body'>
        <div id='chkColumns'>
          <p><input type='checkbox' name='starts' data-i18n='raids_column_raid_starts' />&nbsp;Raid Starts</p>
          <p><input type='checkbox' name='ends' data-i18n='raids_column_raid_ends' />&nbsp;Raid Ends</p>
          <p><input type='checkbox' name='level' data-i18n='raids_column_raid_level' />&nbsp;Raid Level</p>
          <p><input type='checkbox' name='boss' data-i18n='raids_column_raid_boss' />&nbsp;Raid Boss</p>
          <p><input type='checkbox' name='moveset' data-i18n='raids_column_moveset' />&nbsp;Moveset</p>
          <p><input type='checkbox' name='city' data-i18n='raids_column_city' />&nbsp;City</p>
          <p><input type='checkbox' name='team' data-i18n='raids_column_team' />&nbsp;Team</p>
          <p><input type='checkbox' name='ex' data-i18n='raids_column_ex' />&nbsp;Ex-Eligible</p>
          <p><input type='checkbox' name='updated' data-i18n='raids_column_updated' />&nbsp;Updated</p>
        </div>
      </div>
      <div class='modal-footer'>
        <button type='button' class='btn btn-primary' data-dismiss='modal' data-i18n='raids_modal_close'>Close</button>
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
var refresh_rate = <?=$config['ui']['table']['refreshRateS']?>;
var refresher = setInterval(filter_raids, refresh_rate * 1000);
setTimeout(function() { clearInterval(refresher); }, 1800000);

$(document).on("click", ".delete", function(){
  $(this).parents("tr").remove();
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

if (get("raids-search-input") !== false) {
  $('#search-input').val(get("raids-search-input"));
}
if (get("raids-filter-gym") !== false) {
  $('#filter-gym').val(get("raids-filter-gym"));
}
if (get("raids-filter-city") !== false) {
  $('#filter-city').val(JSON.parse(get("raids-filter-city")));
}
if (get("raids-filter-level") !== false) {
  $('#filter-level').val(get("raids-filter-level"));
}
if (get("raids-filter-team") !== false) {
  $('#filter-team').val(get("raids-filter-team"));
}
if (get("raids-filter-ex") !== false) {
  $('#filter-ex').val(get("raids-filter-ex"));
}


var isMobile = <?=$mobile?>;
filter_raids(isMobile);

$('#reset-filters').on('click', function() {
  if (confirm($.i18n('raids_filters_reset_confirm'))) {
    $('#search-input').val('');
    $('#filter-gym').val('');
    $('#filter-city').val('');
    $('#filter-level').val('All');
    $('#filter-team').val('All');
    $('#filter-ex').val('All');
    filter_raids(isMobile);
  }
});
</script>