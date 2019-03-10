function getDate() {
  var d = new Date();
  var date = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ("0" + d.getDate()).slice(-2);
  return date;
}

function getRandomColor() {
  var letters = "0123456789ABCDEF";
  var color = '#';
  for (var i = 0; i < 6; i++ ) {
    color += letters[Math.floor(Math.random() * 16)];
  }
  return color;
}

function createArrayOfValue(value, count) {
  var values = [];
  for (var i = 0; i < count; i++) {
    values.push(value);
  }
  return values;
}

function sendRequest(options, successCallback) {
  /*
  $.ajax({
    url: "api.php",
    method: "POST",
    contentType: "application/x-www-form-urlencoded; charset=utf-8",
    data: options,
    success: successCallback,
    error: function(data) {
      console.log(data);
    }
  });
  */
  $.post("api.php", JSON.stringify(options), successCallback);
}

function numberWithCommas(x) {
  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function addDays(date, days) {
  return date.setDate(date.getDate() + days);
}

function updateCounter(name, value) {
  $({ counter: 0 }).animate({ counter: value }, {
    duration: 3000,
    easing: 'swing', // can be anything
    step: function() { // called on every step
      // Update the element's text with rounded-up value:
      $(name).text(numberWithCommas(Math.round(this.counter)));
    }
  });
}

function sprintf(format, args) {
  return format.replace(/{(\d+)}/g, function(match, number) { 
    return typeof args[number] != 'undefined'
      ? args[number]
      : match
    ;
  });
}

function set(name, value) {
  localStorage.setItem(name, value);
}

function get(name) {
  return localStorage.getItem(name);
}

function getNests(p1_lat, p1_lon, p2_lat, p2_lon) {
  const overpassApiEndpoint = 'https://overpass-api.de/api/interpreter';
  var queryBbox = [ // s, e, n, w
    p1_lat,
    p1_lon,
    p2_lat,
    p2_lon,
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
  if (debug !== false) {
    console.log(overPassQuery);
  }
  
  $.ajax({
    //beforeSend: function() {
    //  $("#modalLoading").modal('show');
    //},
    url: overpassApiEndpoint,
    type: 'GET',
    dataType: 'json',
    data: {'data': overPassQuery},
    success: function (result) {
      if (debug !== false) {
        console.log(result);
      }
      var geoJsonFeatures = osmtogeojson(result);
      geoJsonFeatures.features.forEach(function(feature) {
        feature = turf.flip(feature);
        console.log("Feature:", feature);
        console.log("Name:", feature.properties.tags.name);
        console.log("Geometry:", feature.geometry); //feature.geometry.coordinates
      });
    },
    complete: function() {
      //$("#modalLoading").modal('hide');
    }
  });
}

function buildOsmUri(p1_lat, p1_lon, p2_lat, p2_lon) {
  //Generate the OSM uri for the OSM data
  var osmApi = "https://overpass-api.de/api/interpreter";
  var osmDate = "2018-04-09T01:32:00Z";
  var osmBbox = "[bbox:" + p1_lat + "," + p1_lon + "," + p2_lat + "," + p2_lon + "]";
  var osmData = "?data=";
  var osmType = "[out:json]";
  var date = '[date:"' + osmDate + '"];';
  var osmTags = 'way["landuse"="farmland"];way["landuse"="farmyard"];way["landuse"="grass"];way["landuse"="greenfield"];way["landuse"="meadow"];way["landuse"="orchard"];way["landuse"="recreation_ground"];way["landuse"="vineyard"];way["leisure"="garden"];way["leisure"="golf_course"];way["leisure"="park"];way["leisure"="pitch"];way["leisure"="playground"];way["leisure"="recreation_ground"];way["natural"="grassland"];way["natural"="heath"];way["natural"="scrub"];';
  var osmTagData = "(" + osmTags + ");";
  var osmEnd = "out;>;out skel qt;";
  var uri = osmApi + osmData + (osmType + osmBbox + date + osmTagData + osmEnd);
  return uri;
}