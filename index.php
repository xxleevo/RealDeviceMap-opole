<?php
session_start();

include './vendor/autoload.php';
include './config.php';

if ($discord_login && !isset($_SESSION['user'])) {
  header("Location: ./discord-login.php");
  die();
}

$googleMapsLink = "https://maps.google.com/maps?q=%s,%s";
$appleMapsLink = "https://maps.apple.com/maps?daddr=%s,%s";

echo "<html>
<link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css' integrity='sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS' crossorigin='anonymous'>
<link rel='stylesheet' href='./static/css/font-awesome.min.css'>
<script type='text/javascript' src='https://code.jquery.com/jquery-3.3.1.slim.min.js' integrity='sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo' crossorigin='anonymous'></script>
<script type='text/javascript' src='https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js' integrity='sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut' crossorigin='anonymous'></script>
<script type='text/javascript' src='https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js' integrity='sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k' crossorigin='anonymous'></script>
<script type='text/javascript' src='https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js'></script>";

include('./templates/header.html');

echo "<div class='container'>";

if (isset($_SESSION['user'])) {
  echo "<h1>Welcome ".$_SESSION['user']."</h1>";
}

$request_method = $_SERVER["REQUEST_METHOD"];
switch($request_method) {
  case "GET":
    if(!empty($_GET["page"])) {
      $page = $_GET["page"];
      switch ($page) {
        case "pokemon":
          echo "Pokemon";
          break;
        case "raids":
          include('./pages/raids.php');
          break;
        case "gyms":
          include('./pages/gyms.php');
          break;
        case "quests":
          include('./pages/quests.php');
          break;
        case "pokestops":
          echo "Pokestops";
          break;
        case "stats":
          echo "Stats";
          break;          
      }
    } else {
      include('./pages/dashboard.php');
    }
    break;
  default:
    // Invalid Request Method
    header("HTTP/1.0 405 Method Not Allowed");
    break;
}

echo "</div>";

if ($google_analytics_id != "") {
  echo "
<!-- Google Analytics -->
<script>
  window.ga=window.ga||function(){(ga.q=ga.q||[]).push(arguments)};ga.l=+new Date;
  ga('create', '" . $google_analytics_id . "', 'auto');
  ga('send', 'pageview');
</script>
<script async src='https://www.google-analytics.com/analytics.js'></script>
<!-- End Google Analytics -->";
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

<script type="text/javascript">
var refresh_rate = <?php echo $table_refresh_s ?>;
var refresher = setInterval(filter_raids, refresh_rate * 1000);
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

function sort_table(index) {
  var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
  table = document.getElementById("gym-table");
  switching = true;
  // Set the sorting direction to ascending:
  dir = "asc"; 
  /* Make a loop that will continue until
  no switching has been done: */
  while (switching) {
    // Start by saying: no switching is done:
    switching = false;
    rows = table.rows;
    /* Loop through all table rows (except the
    first, which contains table headers): */
    for (i = 1; i < (rows.length - 1); i++) {
      // Start by saying there should be no switching:
      shouldSwitch = false;
      /* Get the two elements you want to compare,
      one from current row and one from the next: */
      x = rows[i].getElementsByTagName("TD")[index];
      y = rows[i + 1].getElementsByTagName("TD")[index];
      /* Check if the two rows should switch place,
      based on the direction, asc or desc: */
      if (dir == "asc") {
        if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
          // If so, mark as a switch and break the loop:
          shouldSwitch = true;
          break;
        }
      } else if (dir == "desc") {
        if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
          // If so, mark as a switch and break the loop:
          shouldSwitch = true;
          break;
        }
      }
    }
    if (shouldSwitch) {
      /* If a switch has been marked, make the switch
      and mark that a switch has been done: */
      rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
      switching = true;
      // Each time a switch is done, increase this count by 1:
      switchcount++; 
    } else {
      /* If no switching has been done AND the direction is "asc",
      set the direction to "desc" and run the while loop again. */
      if (switchcount == 0 && dir == "asc") {
        dir = "desc";
        switching = true;
      }
    }
  }
}
</script>
</html>