<?php
include_once './config.php';
include_once './includes/DbConnector.php';
include_once './includes/utils.php';

$html = "
<div class='container'>
  <h2 class='page-header'>Pokestops</h2>
  <div class='row'>
    <div class='col-md-3'>
      <a class='list-group-item'>
        <h3 class='pull-right'><img src='./static/images/pokestop.png' width='64' height='64'/></h3>
        <h4 class='list-group-item-heading pokestop-count'>0</h4>
        <p class='list-group-item-text'>Pokestops</p>
      </a>
    </div>
    <div class='col-md-3'>
      <a class='list-group-item'>
        <h3 class='pull-right'><img src='./static/images/lure-module.png' width='64' height='64'/></h3>
        <h4 class='list-group-item-heading lured-pokestop-count'>0</h4>
        <p class='list-group-item-text'>Lured Pokestops</p>
      </a>
    </div>
    <div class='col-md-3'>
      <a class='list-group-item'>
        <h3 class='pull-right'><img src='./static/images/quests/0.png' width='64' height='64'/></h3>
        <h4 class='list-group-item-heading quest-pokestop-count'>0</h4>
        <p class='list-group-item-text'>Field Research</p>
      </a>
    </div>
  </div>
</div>
<div class='container m-3'>
  <div id='mapid' style='width: 100%; height: 600px;'></div>
</div>
";

echo $html;

//TODO: Get pokestop objects with location, parse in javascript

$data = get_pokestop_stats();
$pokestops = $data["total"];
$lured = $data["lured"];
$quests = $data["quests"];

$pokestop_objects = get_pokestop_objects();

?>

<link rel="stylesheet" href="./static/css/dashboard.css"/>
<script type='text/javascript'>
var mymap = L.map('mapid').setView([51.505, -0.09], 11);

L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
  maxZoom: 18,
  attribution: '',
  id: 'mapbox.streets'
}).addTo(mymap);

var popup = L.popup();
function onMapClick(e) {
  popup
    .setLatLng(e.latlng)
    .setContent("You clicked the map at " + e.latlng.toString())
    .openOn(mymap);
}

mymap.on('click', onMapClick);

var pokestops = "<?=$pokestops?>";
var lured = "<?=$lured?>";
var quests = "<?=$quests?>";

// Animate the element's value from x to y:
$({ pokestopsValue: 0, luredValue: 0, questsValue: 0 }).animate({ pokestopsValue: pokestops, luredValue: lured, questsValue: quests }, {
  duration: 3000,
  easing: 'swing', // can be anything
  step: function () { // called on every step
    // Update the element's text with rounded-up value:
    $('.pokestop-count').text(commaSeparateNumber(Math.round(pokestops)));
    $('.lured-pokestop-count').text(commaSeparateNumber(Math.round(lured)));
    $('.quest-pokestop-count').text(commaSeparateNumber(Math.round(quests)));
  }
});

var pokestopObjects = <?=json_encode($pokestop_objects)?>;
pokestopObjects.forEach(pokestop => {
  L.marker([pokestop.lat, pokestop.lon]).addTo(mymap);
});

function commaSeparateNumber(val) {
  while (/(\d+)(\d{3})/.test(val.toString())) {
    val = val.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
  }
  return val;
}
</script>