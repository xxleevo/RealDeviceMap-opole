<?php
require_once './config.php';
include_once './static/data/pokedex.php';

$html = "
<h2 class='page-header text-center'>Statistics</h2>
<ul class='nav nav-pills mb-3 justify-content-center' role='tablist'>
  <li class='nav-item'><a class='nav-link active' role='tab' aria-controls='pokemon' aria-selected='true' data-toggle='pill' href='#pokemon'>Pokemon</a></li>
  <li class='nav-item'><a class='nav-link' role='tab' aria-controls='raids' aria-selected='false' data-toggle='pill' href='#raids'>Raids</a></li>
  <li class='nav-item'><a class='nav-link' role='tab' aria-controls='quests' aria-selected='false' data-toggle='pill' href='#quests'>Quests</a></li>
</ul>

<div class='container'>
  <div class='tab-content'>
    <div id='pokemon' class='tab-pane fade show active' role='tabpanel'>
      <div class='container'>
        <div class='row'>
          <div class='input-group mb-3'>
            <div class='input-group-prepend'>
              <label class='input-group-text' for='filter-date'>Date</label>
            </div>
            <input id='filter-date' type='text' class='form-control' data-toggle='datepicker'>
            <div class='input-group-prepend'>
              <label class='input-group-text' for='filter-pokemon'>Pokemon</label>
            </div>
            <select id='filter-pokemon' class='custom-select'>
              <option disabled selected>Select</option>
              <option value='all'>All</option>";
              foreach ($pokedex as $pokemon_id => $name) {
  	  	        if ($pokemon_id <= 0)
                      continue;
  	  	        $html .= "<option value='$pokemon_id'>$name</option>";
  	  	    }
  	  	    $html .= "
            </select>
          </div>
        </div>
      </div>
      <canvas id='pokemon-stats'></canvas>
      <progress id='pokemon-animation' max='1' value='0' style='width: 100%'></progress>
    </div>
    <div id='raids' class='tab-pane fade' role='tabpanel'>
      <div class='container'>
        <div class='row'>
          <div class='input-group mb-3'>
            <div class='input-group-prepend'>
              <label class='input-group-text' for='filter-raid-date'>Date</label>
            </div>
            <input id='filter-raid-date' type='text' class='form-control' data-toggle='datepicker'>
            <div class='input-group-prepend'>
              <label class='input-group-text' for='filter-raid-type'>Filter By</label>
            </div>
            <label class='radio-inline'><input type='radio' class='btn' name='filter-raid-type' value='0' checked>Pokemon</label>
            <label class='radio-inline'><input type='radio' class='btn' name='filter-raid-type' value='1'>Level</label>
          </div>
        </div>
      </div>
      <canvas id='raid-stats'></canvas>
      <progress id='raid-animation' max='1' value='0' style='width: 100%'></progress>
    </div>
    <div id='quests' class='tab-pane fade' role='tabpanel'>
      <canvas id='quest-stats'></canvas>
    </div>
  </div>
</div>
";
echo $html;
?>

<script type='text/javascript' src='https://www.chartjs.org/dist/latest/Chart.bundle.js'></script>
<script type='text/javascript' src='./static/js/datepicker.js'></script>
<script type='text/javascript' src='./static/js/pokedex.js'></script>
<script type='text/javascript' src='./static/js/utils.js'></script>
<script type='text/javascript'>
var pkmnProgress = document.getElementById("pokemon-animation");
var pkmnCtx = $("#pokemon-stats");
pkmnChart = new Chart(pkmnCtx, {
  type: 'bar',
  //data: createChartData("Seen", pokemon, amounts),
  options: createChartOptions("Pokemon Spawn Statistics", "Pokemon", "Amount Seen", pkmnProgress, "pokemon-stats")
});
$("#pokemon-stats").hide();

var raidProgress = document.getElementById("raid-animation");
var raidCtx = $("#raid-stats");
raidChart = new Chart(raidCtx, {
  type: 'bar',
  //data: createChartData("Seen", pokemon, amounts),
  options: createChartOptions("Raid Boss Statistics", "Pokemon", "Amount Seen", raidProgress, "raid-stats")
});
$("#raid-stats").hide();

$("#filter-raid-level").prop("disabled", true);
$("[data-toggle='datepicker']").datepicker({
  autoHide: true,
  yearFirst: true,
  format: "yyyy-mm-dd",
  zIndex: 2048,
});
$("#filter-date").datepicker("setDate", new Date());
$("#filter-raid-date").datepicker("setDate", new Date());

$("#filter-date").change(filterPokemonChart);
$("#filter-pokemon").change(filterPokemonChart);
filterPokemonChart();

//$("[name='filter-raid-type']").change(filterRaidChart);
$("#filter-raid-date").change(filterRaidChart);
filterRaidChart();

function filterPokemonChart() {
  var date_filter = document.getElementById("filter-date").value;
  var pokemon_filter = document.getElementById("filter-pokemon").value;
  if (pokemon_filter.toLowerCase().indexOf("select") === 0) {
    pokemon_filter = "all";
  }
  if (date_filter != null) {
    console.log("Updating pokemon chart...");
    updatePokemonChart(pkmnChart, date_filter, pokemon_filter);
  }
}

function filterRaidChart() {
  var date_filter = document.getElementById("filter-raid-date").value;
  var type_filter = $("[name='filter-raid-type']:checked").val();
  if (date_filter != null) {
    console.log("Updating raid chart...");
    updateRaidChart(raidChart, date_filter, type_filter);
  }
}

function updatePokemonChart(chart, dateFilter, pokeFilter) {
  console.log("Date:", dateFilter, "Pokemon:", pokeFilter);
  var tmp = createToken();
  sendRequest({ "table": "pokemon_stats", "token": tmp }, function(data, success) {
    tmp = null;
    if (<?=$config['core']['showDebug']?>) {
      if (data !== false) {
        console.log("Pokemon:", data);
      } else {
        conosle.log("Failed to get pokemon stats data.");        
      }
    }
    var pokemon = [];
    var amounts = [];
    var obj = JSON.parse(data);
    obj.forEach(stat => {
      if (stat.date === dateFilter && (pokeFilter === stat.pokemon_id || pokeFilter === "all")) {
        pokemon.push(pokedex[stat.pokemon_id]);
        amounts.push(stat.count);
      }
    });

    clearChartData(chart);
    chart.data = createChartData("Seen", pokemon, amounts);
    chart.update();
    console.log("Pokemon chart updated");
  });
}

function updateRaidChart(chart, dateFilter, typeFilter) {
  console.log("Date:", dateFilter, "Type:", typeFilter);
  var tmp = createToken();
  sendRequest({ "table": "raid_stats", "token": tmp }, function(data, success) {
    tmp = null;
    if (<?=$config['core']['showDebug']?>) {
      if (data !== false) {
        console.log("Raids:", data);
      } else {
        conosle.log("Failed to get raid stats data.");
      }
    }
    var pokemon = [];
    var amounts = [];
    var obj = JSON.parse(data);
    obj.forEach(stat => {
      if (stat.date === dateFilter) {
        pokemon.push(pokedex[stat.pokemon_id] + " (Level " + stat.level + ")");
        amounts.push(stat.count);
      }
    });

    clearChartData(chart);
    chart.data = createChartData("Seen", pokemon, amounts);
    chart.update();
    console.log("Raid chart updated");
  });
}

function createChartOptions(title, xAxesLabel, yAxesLabel, progress, canvasId) {
  var chartOptions = {
    responsive: true,
    title: { display: true, text: title, fontSize: 18, fontColor: "#111" },
    tooltips: { mode: "index", intersect: false, },
    hover: { mode: "nearest", intersect: true },
    scales: {
      xAxes: [{
        display: true,
        scaleLabel: { display: true, labelString: xAxesLabel }
      }],
      yAxes: [{
        display: true,
        scaleLabel: { display: true, labelString: yAxesLabel },
        ticks: { precision: 0, beginAtZero: true }
      }]
    },
    animation: {
      duration: 2000,
      onProgress: function(animation) {
        progress.value = animation.currentStep / animation.numSteps;
      },
      onComplete: function() {
        window.setTimeout(function() {
          progress.value = 0;
          progress.style.display = "none";
          $("#" + canvasId).show();
        }, 2000);
      }
    }
  };
  return chartOptions;
}

function createChartData(title, labels, data) {
  var colors = [];
  for (var i = 0; i < labels.length; i++) {
    colors.push(getRandomColor());
  }
  var chartData = {
    labels: labels,
    datasets : [{
      label: title,
      strokeColor: createArrayOfValue("<?=$config['ui']['charts']['colors']['stroke']?>", labels.length),
      highlightFill: createArrayOfValue("<?=$config['ui']['charts']['colors']['highlightFill']?>", labels.length),
      highlightStroke: createArrayOfValue("<?=$config['ui']['charts']['colors']['highlightStroke']?>", labels.length),
      backgroundColor: colors,
      borderColor: createArrayOfValue("<?=$config['ui']['charts']['colors']['border']?>", labels.length),
      hoverBackgroundColor: createArrayOfValue("<?=$config['ui']['charts']['colors']['hoverBackground']?>", labels.length),
      hoverBorderColor: createArrayOfValue("<?=$config['ui']['charts']['colors']['hoverBorder']?>", labels.length),
      data: data
    }]
  };
  return chartData;
}

function clearChartData(chart) {
  chart.data.labels.pop();
  chart.data.datasets.forEach((dataset) => {
    dataset.data.pop();
  });
}

function createToken() {
  //TODO: Secure
  <?php $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(16)); ?>
  return "<?=$_SESSION['token']?>";
}
</script>