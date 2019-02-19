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

    var level_value = table.rows[i].cells[3].innerHTML;
    var pkmn_value = table.rows[i].cells[4].innerHTML.toUpperCase();
    var city_value = table.rows[i].cells[6].innerHTML.toUpperCase();
    var team_value = table.rows[i].cells[7].innerHTML.toUpperCase();
    var ex_value = table.rows[i].cells[8].innerHTML.toUpperCase();
    
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

function filter_gyms() {
  var team_filter = document.getElementById("filter-team").value.toUpperCase();
  var slots_filter = document.getElementById("filter-slots").value.toUpperCase();
  var battle_filter = document.getElementById("filter-battle").value.toUpperCase();
  var city_filter = document.getElementById("filter-city").value.toUpperCase();

  console.log("Team:", team_filter, "Slots:", slots_filter, "In Battle:", battle_filter, "City:", city_filter);

  if (team_filter.toLowerCase().indexOf("all") === 0 ||
    team_filter.toLowerCase().indexOf("select") === 0) {
    team_filter = "";
    console.log("Team filter cleared");
  }

  if (slots_filter.toLowerCase().indexOf("all") === 0 ||
    slots_filter.toLowerCase().indexOf("select") === 0) {
    slots_filter = "ALL";
    console.log("Available slots filter cleared");
  }

  if (battle_filter.toLowerCase().indexOf("all") === 0 ||
    battle_filter.toLowerCase().indexOf("select") === 0) {
    battle_filter = "";
    console.log("Battle filter cleared");
  }

  if (city_filter.toLowerCase().indexOf("all") === 0 ||
    city_filter.toLowerCase().indexOf("select") === 0) {
    city_filter = "";
    console.log("City filter cleared");
  }

  var table = document.getElementById("gym-table");
  var tr = table.getElementsByTagName("tr");
  for (var i = 0; i < tr.length; i++) {
    if (i == 0)
      continue;

    var team_value = table.rows[i].cells[1].innerHTML.toUpperCase();
    var slots_value = table.rows[i].cells[2].innerHTML.toUpperCase();
    var battle_value = table.rows[i].cells[4].innerHTML.toUpperCase();
    var city_value = table.rows[i].cells[5].innerHTML.toUpperCase();

    if (team_value.indexOf(team_filter) > -1 && 
      ((slots_value >= slots_filter && slots_value.indexOf("FULL") == -1) || (slots_value == slots_filter && slots_filter.indexOf("FULL") >= -1) || slots_filter.indexOf("ALL") > -1) &&
      battle_value.indexOf(battle_filter) > -1 &&
      city_value.indexOf(city_filter) > -1) {
      tr[i].style.display = "";
    } else {
      tr[i].style.display = "none";
    }     
  }
}

function filter_quests() {
  var search_filter = document.getElementById("search-input").value.toUpperCase();
  var city_filter = document.getElementById("filter-city").value.toUpperCase();

  console.log("Quest:", search_filter, "City:", city_filter);

  if (city_filter.toLowerCase().indexOf("all") === 0 ||
    city_filter.toLowerCase().indexOf("select") === 0) {
    city_filter = "";
    console.log("City filter cleared");
  }

  var table = document.getElementById("quest-table");
  var tr = table.getElementsByTagName("tr");
  for (var i = 0; i < tr.length; i++) {
    if (i == 0)
      continue;

    var reward_value = table.rows[i].cells[1].innerHTML.toUpperCase();
    var city_value = table.rows[i].cells[4].innerHTML.toUpperCase();
    
    if (reward_value.indexOf(search_filter) > -1 && city_value.indexOf(city_filter) > -1) {
      tr[i].style.display = "";
    } else {
      tr[i].style.display = "none";
    }     
  }
}