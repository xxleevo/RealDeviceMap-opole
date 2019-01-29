<?php
include './vendor/autoload.php';
include './config.php';
include './pokedex.php';
include './movesets.php';
include './geofence_service.php';

$googleMapsLink = "https://maps.google.com/maps?q=%s,%s";
$appleMapsLink = "https://maps.apple.com/maps?daddr=%s,%s";

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

if ($google_adsense_id != "") {
  echo "
<script async src='//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js'></script>
<script>
  (adsbygoogle = window.adsbygoogle || []).push({
    google_ad_client: '" . $google_adsense_id . "',
    enable_page_level_ads: true
  });
</script>";
}
?>
<html>
<link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css' integrity='sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS' crossorigin='anonymous'>
<script type="text/javascript" src='https://code.jquery.com/jquery-3.3.1.slim.min.js' integrity='sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo' crossorigin='anonymous'></script>
<script type="text/javascript" src='https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js' integrity='sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut' crossorigin='anonymous'></script>
<script type="text/javascript" src='https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js' integrity='sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k' crossorigin='anonymous'></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>

<script type="text/javascript">
var refresher = setInterval(filter_raids, 5000);
setTimeout(function() {
  clearInterval(refresher);
}, 1800000);

$(document).on("click", ".delete", function(){
	$(this).parents("tr").remove();
	$(".add-new").removeAttr("disabled");
});

function filter_raids() {
  var table = $("table-refresh");
  table.load("data_fetcher.php");
	
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
  
  console.log("Rows:", table.rows[i]);
	
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