<?php
require_once './vendor/autoload.php';
require_once './config.php';
require_once './pokedex.php';
require_once './movesets.php';
require_once './geofence_service.php';

$geofence_srvc = new GeofenceService();

$filters = "
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

echo "<div id='table-refresh'>";
include_once("data_fetcher.php");
echo "</div>";
?>