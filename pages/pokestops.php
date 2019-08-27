<?php
include_once './config.php';
include_once './includes/DbConnector.php';
include_once './includes/utils.php';

$html = "
<div class='container'>
  <h2 class='page-header text-center' data-i18n='pokestops_title'>Pokestops</h2>
  <div class='row justify-content-center'>
    <div class='col-md-3'>
      <a class='list-group-item'>
        <h3 class='pull-right'><img src='./static/images/pokestop.png' width='64' height='64'/></h3>
        <h4 class='list-group-item-heading pokestop-count'>0</h4>
        <p class='list-group-item-text' data-i18n='pokestops_total'>Pokestops</p>
      </a>
    </div>
    <div class='col-md-3'>
      <a class='list-group-item'>
        <h3 class='pull-right'><img src='./static/images/lure-module.png' width='64' height='64'/></h3>
        <h4 class='list-group-item-heading lured-pokestop-count'>0</h4>
        <p class='list-group-item-text' data-i18n='pokestops_lured'>Lured Pokestops</p>
      </a>
    </div>
    <div class='col-md-3'>
      <a class='list-group-item'>
        <h3 class='pull-right'><img src='./static/images/quests/0.png' width='64' height='64'/></h3>
        <h4 class='list-group-item-heading quest-pokestop-count'>0</h4>
        <p class='list-group-item-text' data-i18n='pokestops_quests'>Field Research</p>
      </a>
    </div>
  </div>
  <div class='container m-3'>
    <div id='mapid' style='width: 100%; height: 600px;'></div>
  </div>
</div>
";

echo $html;

$data = get_pokestop_stats();
$pokestops = $data["total"];
$lured = $data["lured"];
$quests = $data["quests"];
?>

<link rel="stylesheet" href="./static/css/footerfix.css"/>
<link rel="stylesheet" href="./static/css/dashboard.css"/>
<script type='text/javascript'>
var pokestops = "<?=$pokestops?>";
var lured = "<?=$lured?>";
var quests = "<?=$quests?>";

updateCounter(".pokestop-count", pokestops);
updateCounter(".lured-pokestop-count", lured);
updateCounter(".quest-pokestop-count", quests);

var mymap = L.map('mapid').setView(<?=json_encode($config['core']['startupLocation'])?>, <?=$config['core']['startupZoom']?>);
L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
  maxZoom: 18,
  attribution: '',
  id: 'mapbox.streets'
}).addTo(mymap);

var popup = L.popup();
function onMapClick(e) {
/*
  popup
    .setLatLng(e.latlng)
    .setContent("You clicked the map at " + e.latlng.toString())
    .openOn(mymap);
*/
}

mymap.on('click', onMapClick);

var debug = <?=$config['core']['showDebug'] !== false ? '1' : '0'?>;
var tmp = createToken();
sendRequest({ "table": "pokestop", "limit": 1000, "token": tmp }, function(data) {
  tmp = null;
  if (debug) {
    if (data === 0) {
      console.log("Failed to get data for pokestops.");
      return;
    } else {
      console.log("Pokestops:", data);
    }
  }
  var pokestops = JSON.parse(data);
  pokestops.forEach(pokestop => {
    L.marker([pokestop.lat, pokestop.lon]).addTo(mymap);
  });
});

function createToken() {
  //TODO: Secure
  <?php $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(16)); ?>
  return "<?=$_SESSION['token']?>";
}
</script>