function filter_raids() {
  var table = $("table-refresh");
  table.load("data_fetcher.php");

  var searchFilter = document.getElementById("search-input").value.toUpperCase();
  var gymFilter = document.getElementById("filter-gym").value.toUpperCase();
  var cityFilter = document.getElementById("filter-city").value.toUpperCase();
  var levelFilter = document.getElementById("filter-level").value.toUpperCase();
  var teamFilter = document.getElementById("filter-team").value.toUpperCase();
  var exFilter = document.getElementById("filter-ex").value.toUpperCase();

  console.log("Pokemon:", searchFilter, "City:", cityFilter, "Level:", levelFilter, "Team:", teamFilter, "Ex:", exFilter, "Gym:", gymFilter);

  if (cityFilter.toLowerCase().indexOf("all") === 0 ||
    cityFilter.toLowerCase().indexOf("select") === 0) {
    cityFilter = "";
    console.log("City filter cleared");
  }

  if (levelFilter.toLowerCase().indexOf("all") === 0 ||
    levelFilter.toLowerCase().indexOf("select") === 0) {
    levelFilter = "";
    console.log("Level filter cleared");
  }

  if (teamFilter.toLowerCase().indexOf("all") === 0 ||
    teamFilter.toLowerCase().indexOf("select") === 0) {
    teamFilter = "";
    console.log("Team filter cleared");
  }

  if (exFilter.toLowerCase().indexOf("all") === 0 ||
    exFilter.toLowerCase().indexOf("select") === 0) {
    exFilter = "";
    console.log("Ex filter cleared");
  }

  if (gymFilter.toLowerCase().indexOf("all") === 0 ||
    gymFilter.toLowerCase().indexOf("select") === 0) {
    gymFilter = "";
    console.log("Gym filter cleared");
  }

  var isMobile = true;//isMobile();
  var table = document.getElementById("gym-table");
  var tr = isMobile ? table.getElementsByClassName("mobile-row") : table.getElementsByTagName("tr");
  for (var i = 0; i < tr.length; i++) {
    if (!isMobile && i == 0)
      continue;

    var pkmnValue, cityValue, levelValue, teamValue, exValue, gymValue;
    if (isMobile) {
      var cells = tr[i].getElementsByClassName("mobile");
      pkmnValue = cells[0].innerHTML.toUpperCase();
      levelValue = cells[1].innerHTML;
      gymValue = cells[4].innerHTML.toUpperCase();
      cityValue = cells[5].innerHTML.toUpperCase();
      teamValue = cells[6].innerHTML.toUpperCase();
      exValue = cells[8].innerHTML.toUpperCase();
    } else {
      levelValue = table.rows[i].cells[3].innerHTML;
      pkmnValue = table.rows[i].cells[4].innerHTML.toUpperCase();
      gymValue = table.rows[i].cells[6].innerHTML.toUpperCase();
      cityValue = table.rows[i].cells[7].innerHTML.toUpperCase();
      teamValue = table.rows[i].cells[8].innerHTML.toUpperCase();
      exValue = table.rows[i].cells[9].innerHTML.toUpperCase();
    }
    
    if (pkmnValue.indexOf(searchFilter) > -1 &&
      cityValue.indexOf(cityFilter) > -1 &&
      levelValue.indexOf(levelFilter) > -1 &&
      teamValue.indexOf(teamFilter) > -1 &&
      exValue.indexOf(exFilter) > -1 &&
      gymValue.indexOf(gymFilter) > -1) {
      tr[i].style.display = "";
    } else {
      tr[i].style.display = "none";
    }     
  }
}

function filter_gyms() {
  var teamFilter = document.getElementById("filter-team").value.toUpperCase();
  var slotsFilter = document.getElementById("filter-slots").value.toUpperCase();
  var battleFilter = document.getElementById("filter-battle").value.toUpperCase();
  var cityFilter = document.getElementById("filter-city").value.toUpperCase();
  var gymFilter = document.getElementById("filter-gym").value.toUpperCase();

  console.log("Team:", teamFilter, "Slots:", slotsFilter, "In Battle:", battleFilter, "City:", cityFilter, "Gym:", gymFilter);

  if (teamFilter.toLowerCase().indexOf("all") === 0 ||
    teamFilter.toLowerCase().indexOf("select") === 0) {
    teamFilter = "";
    console.log("Team filter cleared");
  }

  if (slotsFilter.toLowerCase().indexOf("all") === 0 ||
    slotsFilter.toLowerCase().indexOf("select") === 0) {
    slotsFilter = "ALL";
    console.log("Available slots filter cleared");
  }

  if (battleFilter.toLowerCase().indexOf("all") === 0 ||
    battleFilter.toLowerCase().indexOf("select") === 0) {
    battleFilter = "";
    console.log("Battle filter cleared");
  }

  if (cityFilter.toLowerCase().indexOf("all") === 0 ||
    cityFilter.toLowerCase().indexOf("select") === 0) {
    cityFilter = "";
    console.log("City filter cleared");
  }

  if (gymFilter.toLowerCase().indexOf("all") === 0 ||
    gymFilter.toLowerCase().indexOf("select") === 0) {
    gymFilter = "";
    console.log("Gym filter cleared");
  }

  var table = document.getElementById("gym-table");
  var tr = table.getElementsByTagName("tr");
  for (var i = 0; i < tr.length; i++) {
    if (i == 0)
      continue;

    var gymValue = table.rows[i].cells[1].innerHTML.toUpperCase();
    var teamValue = table.rows[i].cells[2].innerHTML.toUpperCase();
    var slotsValue = table.rows[i].cells[3].innerHTML.toUpperCase();
    var battleValue = table.rows[i].cells[5].innerHTML.toUpperCase();
    var cityValue = table.rows[i].cells[6].innerHTML.toUpperCase();

    if (teamValue.indexOf(teamFilter) > -1 && 
      ((slotsValue >= slotsFilter && slotsValue.indexOf("FULL") == -1) || (slotsValue == slotsFilter && slotsFilter.indexOf("FULL") >= -1) || slotsFilter.indexOf("ALL") > -1) &&
      battleValue.indexOf(battleFilter) > -1 &&
      cityValue.indexOf(cityFilter) > -1 &&
      gymValue.indexOf(gymFilter) > -1) {
      tr[i].style.display = "";
    } else {
      tr[i].style.display = "none";
    }     
  }
}

function filter_quests() {
  var searchFilter = document.getElementById("search-input").value.toUpperCase();
  var cityFilter = document.getElementById("filter-city").value.toUpperCase();
  var pokestopFilter = document.getElementById("filter-pokestop").value.toUpperCase();

  console.log("Quest:", searchFilter, "City:", cityFilter, "Pokestop:", pokestopFilter);

  if (cityFilter.toLowerCase().indexOf("all") === 0 ||
    cityFilter.toLowerCase().indexOf("select") === 0) {
    cityFilter = "";
    console.log("City filter cleared");
  }

  if (pokestopFilter.toLowerCase().indexOf("all") === 0 ||
    pokestopFilter.toLowerCase().indexOf("select") === 0) {
    pokestopFilter = "";
    console.log("Pokestop filter cleared");
  }

  var table = document.getElementById("quest-table");
  var tr = table.getElementsByTagName("tr");
  for (var i = 0; i < tr.length; i++) {
    if (i == 0)
      continue;

    var rewardValue = table.rows[i].cells[1].innerHTML.toUpperCase();
    var cityValue = table.rows[i].cells[4].innerHTML.toUpperCase();
    var pokestopValue = table.rows[i].cells[5].innerHTML.toUpperCase();
    
    if (rewardValue.indexOf(searchFilter) > -1 && 
      cityValue.indexOf(cityFilter) > -1 &&
      pokestopValue.indexOf(pokestopFilter) > -1) {
      tr[i].style.display = "";
    } else {
      tr[i].style.display = "none";
    }     
  }
}