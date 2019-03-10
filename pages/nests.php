<?php require_once './config.php'; ?>

<center>
<div class='container m-2'>
  <h2 class='page-header text-center'>Neighborhood nests</h2>
  <p id="migration" class='text-center'></p>
  <div id='mapid' style='width: 100%; height: 600px;'></div>
  <div class="modal modal-loader" id="modalLoading" data-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-sm">
      <div class="modal-content" style="width: 48px">
        <span class="fa fa-spinner fa-spin fa-3x"></span>
      </div>
    </div>
  </div>
</div>
</center>

<script type='text/javascript' src='https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js'></script>
<script type='text/javascript' src='./static/js/jquery.countdown.min.js'></script>
<script type='text/javascript' src="https://cdn.jsdelivr.net/npm/@turf/turf@5/turf.min.js"></script>
<script type='text/javascript' src="https://cdn.jsdelivr.net/npm/osmtogeojson@3.0.0-beta.3/osmtogeojson.js"></script>
<script type='text/javascript'>
var migrationDate = new Date("<?=$config['core']['lastNestMigration']?>");
while (migrationDate < new Date()) {
  migrationDate = addDays(migrationDate, 14);
}

$("#migration").countdown(migrationDate, function(event) {
  var msg = "The next <b>nest migration</b> occurs in<br/> ";
  var time = event.strftime("%w %!w:<span>week</span>,<span>weeks</span>;, %d %!d:<span>day</span>,<span>days</span>;, %H %!H:<span>hour</span>,<span>hours</span>;, %M %!M:<span>minute</span>,<span>minutes</span>;, and %S %!S:<span>second</span>,<span>seconds</span>;");
  $(this).html(msg + time + "<br/>on<br/><span>" + moment(migrationDate).format("dddd MMMM Do YYYY, h:mm:ss A") + "</span>");
});

var mymap = L.map('mapid').setView(<?=json_encode($config['core']['startupLocation'])?>, 11);
var tileLayer = L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
  maxZoom: 18,
  attribution: '',
  id: 'mapbox.streets'
}).addTo(mymap);

getNests();

$(document).on("click", ".getSpawnReport", function() {
  getSpawnReport(tileLayer);
});

function getNests() {//p1_lat, p1_lon, p2_lat, p2_lon) {
  const bounds = mymap.getBounds();
  const overpassApiEndpoint = 'https://overpass-api.de/api/interpreter';
  var queryBbox = [ // s, e, n, w
    bounds.getSouthWest().lat,
    bounds.getSouthWest().lng,
    bounds.getNorthEast().lat,
    bounds.getNorthEast().lng
  ].join(',');
  var queryDate = "2018-04-09T01:32:00Z";
  var queryOptions = [
    '[out:json]',
    '[bbox:' + queryBbox + ']',
    '[date:"' + queryDate + '"]'
  ].join('');
  var queryNestWays = [
    'way["leisure"="park"];',
    'way["leisure"="recreation_ground"];',
    'way["landuse"="recreation_ground"];'
  ].join('');
  var overPassQuery = queryOptions + ';(' + queryNestWays + ')' + ';out;>;out skel qt;';
  var debug = true;
  if (debug !== false) {
    console.log(overPassQuery);
  }
  
  $.ajax({
    beforeSend: function() {
      $("#modalLoading").show();
    },
    url: overpassApiEndpoint,
    type: 'GET',
    dataType: 'json',
    data: {'data': overPassQuery},
    success: function (result) {
      if (<?=$config['core']['showDebug']?>) {
        if (result === 0) {
          console.log("Failed to get osm nest data.");
          return;
        } else {
          console.log("Nests:", result);
        }
      }
      var geoJsonFeatures = osmtogeojson(result);
      geoJsonFeatures.features.forEach(function(feature) {
        feature = turf.flip(feature);
        var polygon = L.polygon(feature.geometry.coordinates, {
          clickable: false,
          color: "#ff8833",
          fill: true,
          fillColor: null,
          fillOpacity: 0.2,
          opacity: 0.5,
          stroke: true,
          weight: 4
        });
        polygon.tags = {};
        polygon.tags.name = feature.properties.tags.name;
        polygon.addTo(mymap);
        polygon.bindPopup(function (layer) {
          if (typeof layer.tags.name !== 'undefined') {
            var name = '<div class="input-group mb-3 nestName"><span style="padding: .375rem .75rem; width: 100%">Nest: ' + layer.tags.name + '</span></div>';
          }
          var output = name; /*+
                  '<div class="input-group mb-3"><button class="btn btn-secondary btn-sm getSpawnReport" data-layer-container="nestLayer" data-layer-id=' +
                  layer._leaflet_id +
                  ' type="button">Go!</button><div class="input-group-append"><span style="padding: .375rem .75rem;">Get spawn report</span></div></div>' +
                  '<div class="input-group mb-3"><button class="btn btn-secondary btn-sm deleteLayer" data-layer-container="nestLayer" data-layer-id=' +
                  layer._leaflet_id +
                  ' type="button">Go!</button><div class="input-group-append"><span style="padding: .375rem .75rem;">Remove from map</span></div></div>' +
                  '<div class="input-group"><button class="btn btn-secondary btn-sm exportLayer" data-layer-container="nestLayer" data-layer-id=' +
                  layer._leaflet_id +
                  ' type="button">Go!</button><div class="input-group-append"><span style="padding: .375rem .75rem;">Export Polygon</span></div></div>';*/
          return output;
        });
      });
    },
    complete: function() {
      $("#modalLoading").hide();
    }
  });
}

/*
function getSpawnReport(layer) {
  var reportStops = [],
    reportSpawns = [];
  var poly = layer.toGeoJSON();
  var line = turf.polygonToLine(poly);
  pokestops.forEach(function(item) {
    point = turf.point([item.lng, item.lat]);
    if (turf.inside(point, poly)) {
      reportStops.push(item.id);
    }
  });
  spawnpoints.forEach(function(item) {
    point = turf.point([item.lng, item.lat]);
    if (turf.inside(point, poly)) {
      reportSpawns.push(item.id);
    }
  });
  const data = {
    'get_spawndata': true,
    'nest_migration_timestamp': settings.nestMigrationDate,
    'spawn_report_limit': settings.spawnReportLimit,
    'stops': reportStops,
    'spawns': reportSpawns
  };
  const json = JSON.stringify(data);
  if (debug !== false) { console.log(json) }
  
  $.ajax({
    beforeSend: function() {
      $("#modalLoading").show();
    },
    url: this.href,
    type: 'POST',
    dataType: 'json',
    data: {'data': json},
    success: function (result) {
      console.log(result) 
      if (debug !== false) { console.log(result) }
      if (result.spawns !== null) {
        result.spawns.forEach(function(item) {
          if (typeof layer.tags !== 'undefined') {
            $('#modalSpawnReport  .modal-title').text('Spawn Report - ' + layer.tags.name);
          }
          $('#spawnReportTable > tbody:last-child').append('<tr><td>' +pokemon[item.pokemon_id-1] + '</td><td>' + item.count + '</td></tr>');
        });
      } else {
          if (typeof layer.tags !== 'undefined') {
          $('#modalSpawnReport  .modal-title').text('Spawn Report - ' + layer.tags.name);
        }
        $('#spawnReportTable > tbody:last-child').append('<tr><td colspan="2">No data available.</td></tr>');
      }
    },
    complete: function() {
      $("#modalLoading").hide();
      $('#modalSpawnReport').show();
    }
  });
}
*/
</script>
<style>
.modal-loader .modal-dialog{
  display: table;
  position: relative;
  margin: 0 auto;
  top: calc(50% - 24px);
}
.modal-loader .modal-dialog .modal-content{
  background-color: transparent;
  border: none;
}
</style>