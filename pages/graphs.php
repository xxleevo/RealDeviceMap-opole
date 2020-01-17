<?php
include './vendor/autoload.php';
include './config.php';
include './includes/GeofenceService.php';
include './static/data/pokedex.php';
include './static/data/forms.php';

$pokeData = [];
global $config;
// Establish connection to database
$db = new DbConnector($config['db']);
$pdo = $db->getConnection();
try {
	$sql = "
		SELECT
		hour(FROM_UNIXTIME(first_seen_timestamp)) as time,
        count(*) as count,
        SUM(iv is not null) as iv_count
		FROM pokemon
		WHERE first_seen_timestamp > UNIX_TIMESTAMP(CURDATE())
		group by time
        order by time;
	";
	$result = $pdo->query($sql);
	if ($result->rowCount() > 0) {
		$i = 0;
		while ($row = $result->fetch()) {
			$pokeData["Today"]["Total"][$i]["x"] = $row["time"];
			$pokeData["Today"]["Total"][$i]["y"] = $row["count"];
			$pokeData["Today"]["IV"][$i]["x"] = $row["time"];
			$pokeData["Today"]["IV"][$i]["y"] = $row["iv_count"];
			$i++;
		}
		// Free result set
		unset($result);
	}
} catch (PDOException $e) {
	die("ERROR: Could not able to execute $sql. " . $e->getMessage());
}
try {
	$sql = "
		SELECT * FROM (
			SELECT
			UNIX_TIMESTAMP(date) * 1000 as date,
			SUM(count) as count
			FROM pokemon_stats
			WHERE UNIX_TIMESTAMP(date) > UNIX_TIMESTAMP(CURDATE() - INTERVAL 1 MONTH)
			GROUP BY date
			ORDER BY date ASC
		) AS A
		JOIN (
			SELECT
				UNIX_TIMESTAMP(date) * 1000 as date,
				SUM(count) as iv_count
			FROM pokemon_iv_stats
			WHERE UNIX_TIMESTAMP(date) > UNIX_TIMESTAMP(CURDATE() - INTERVAL 1 MONTH)
			GROUP BY date
			ORDER BY date ASC
		) AS B
		on A.date = B.date;
	";
	$result = $pdo->query($sql);
	if ($result->rowCount() > 0) {
		$i = 0;
		while ($row = $result->fetch()) {
			$pokeData["Total"]["Month"]["Total"][$i]["x"] = $row["date"];
			$pokeData["Total"]["Month"]["Total"][$i]["y"] = $row["count"];
			$pokeData["Total"]["Month"]["IV"][$i]["x"] = $row["date"];
			$pokeData["Total"]["Month"]["IV"][$i]["y"] = $row["iv_count"];
			$i++;
		}
		$k = 0;
		for($j=$i-1;$j>=$i-7;$j--){
			$pokeData["Total"]["Week"]["Total"][$k]["x"] = $pokeData["Total"]["Month"]["Total"][$j]["x"];
			$pokeData["Total"]["Week"]["Total"][$k]["y"] = $pokeData["Total"]["Month"]["Total"][$j]["y"];
			$pokeData["Total"]["Week"]["IV"][$k]["x"] = $pokeData["Total"]["Month"]["Total"][$j]["x"];
			$pokeData["Total"]["Week"]["IV"][$k]["y"] = $pokeData["Total"]["Month"]["IV"][$j]["y"];
			$k++;
		}
		// Free result set
		unset($result);
	}
} catch (PDOException $e) {
	die("ERROR: Could not able to execute $sql. " . $e->getMessage());
}

try {
	$sql = "
		SELECT * FROM (
			SELECT
			UNIX_TIMESTAMP(date) * 1000 as L1Date,
			SUM(count) as L1Count
			FROM raid_stats
			WHERE UNIX_TIMESTAMP(date) > UNIX_TIMESTAMP(CURDATE() - INTERVAL 1 MONTH) AND level = 1
			GROUP BY L1Date
			ORDER BY L1Date ASC 
		) AS A
		JOIN (
			SELECT
			UNIX_TIMESTAMP(date) * 1000 as L2Date,
			SUM(count) as L2Count
			FROM raid_stats
			WHERE UNIX_TIMESTAMP(date) > UNIX_TIMESTAMP(CURDATE() - INTERVAL 1 MONTH) AND level = 2
			GROUP BY L2Date
			ORDER BY L2Date ASC 
		) AS B
		JOIN (
			SELECT
			UNIX_TIMESTAMP(date) * 1000 as L3Date,
			SUM(count) as L3Count
			FROM raid_stats
			WHERE UNIX_TIMESTAMP(date) > UNIX_TIMESTAMP(CURDATE() - INTERVAL 1 MONTH) AND level = 3
			GROUP BY L3Date
			ORDER BY L3Date ASC 
		) AS C
		JOIN (
			SELECT
			UNIX_TIMESTAMP(date) * 1000 as L4Date,
			SUM(count) as L4Count
			FROM raid_stats
			WHERE UNIX_TIMESTAMP(date) > UNIX_TIMESTAMP(CURDATE() - INTERVAL 1 MONTH) AND level = 4
			GROUP BY L4Date
			ORDER BY L4Date ASC 
		) AS D
		JOIN (
			SELECT
			UNIX_TIMESTAMP(date) * 1000 as L5Date,
			SUM(count) as L5Count
			FROM raid_stats
			WHERE UNIX_TIMESTAMP(date) > UNIX_TIMESTAMP(CURDATE() - INTERVAL 1 MONTH) AND level = 5
			GROUP BY L5Date
			ORDER BY L5Date ASC 
		) AS E
		JOIN (
			SELECT
			UNIX_TIMESTAMP(date) * 1000 as dateTotal,
			SUM(count) as countTotal
			FROM raid_stats
			WHERE UNIX_TIMESTAMP(date) > UNIX_TIMESTAMP(CURDATE() - INTERVAL 1 MONTH)
			GROUP BY dateTotal
			ORDER BY dateTotal ASC 
		) AS F
		on A.L1Date = B.L2Date AND B.L2Date = C.L3Date AND C.L3Date = D.L4Date AND D.L4Date = E.L5Date and E.L5Date = F.dateTotal;
	";
	$result = $pdo->query($sql);
	if ($result->rowCount() > 0) {
		$i = 0;
		while ($row = $result->fetch()) {
			$raidData["Total"]["Month"]["Total"][$i]["x"] = $row["dateTotal"];
			$raidData["Total"]["Month"]["Total"][$i]["y"] = $row["countTotal"];
			$raidData["Total"]["Month"]["L1"][$i]["x"] = $row["L1Date"];
			$raidData["Total"]["Month"]["L1"][$i]["y"] = $row["L1Count"];
			$raidData["Total"]["Month"]["L2"][$i]["x"] = $row["L2Date"];
			$raidData["Total"]["Month"]["L2"][$i]["y"] = $row["L2Count"];
			$raidData["Total"]["Month"]["L3"][$i]["x"] = $row["L3Date"];
			$raidData["Total"]["Month"]["L3"][$i]["y"] = $row["L3Count"];
			$raidData["Total"]["Month"]["L4"][$i]["x"] = $row["L4Date"];
			$raidData["Total"]["Month"]["L4"][$i]["y"] = $row["L4Count"];
			$raidData["Total"]["Month"]["L5"][$i]["x"] = $row["L5Date"];
			$raidData["Total"]["Month"]["L5"][$i]["y"] = $row["L5Count"];
			$i++;
		}
		$k = 0;
		for($j=$i-1;$j>=$i-7;$j--){
			$raidData["Total"]["Week"]["Total"][$k]["x"] = $raidData["Total"]["Month"]["Total"][$j]["x"];
			$raidData["Total"]["Week"]["Total"][$k]["y"] = $raidData["Total"]["Month"]["Total"][$j]["y"];
			$raidData["Total"]["Week"]["L1"][$k]["x"] = $raidData["Total"]["Month"]["L1"][$j]["x"];
			$raidData["Total"]["Week"]["L1"][$k]["y"] = $raidData["Total"]["Month"]["L1"][$j]["y"];
			$raidData["Total"]["Week"]["L2"][$k]["x"] = $raidData["Total"]["Month"]["L2"][$j]["x"];
			$raidData["Total"]["Week"]["L2"][$k]["y"] = $raidData["Total"]["Month"]["L2"][$j]["y"];
			$raidData["Total"]["Week"]["L3"][$k]["x"] = $raidData["Total"]["Month"]["L3"][$j]["x"];
			$raidData["Total"]["Week"]["L3"][$k]["y"] = $raidData["Total"]["Month"]["L3"][$j]["y"];
			$raidData["Total"]["Week"]["L4"][$k]["x"] = $raidData["Total"]["Month"]["L4"][$j]["x"];
			$raidData["Total"]["Week"]["L4"][$k]["y"] = $raidData["Total"]["Month"]["L4"][$j]["y"];
			$raidData["Total"]["Week"]["L5"][$k]["x"] = $raidData["Total"]["Month"]["L5"][$j]["x"];
			$raidData["Total"]["Week"]["L5"][$k]["y"] = $raidData["Total"]["Month"]["L5"][$j]["y"];
			
			
			$k++;
		}
		// Free result set
		unset($result);
	}
} catch (PDOException $e) {
	die("ERROR: Could not able to execute $sql. " . $e->getMessage());
}
// Close connection
unset($pdo);

//Debug
//echo json_encode($raidData["Total"]["Week"]);

// Write all Data
echo 
"<div style='max-width:1440px;margin: 0 auto !important;float: none !important;'>
	<div class='card text-center m-1'>
		<div class='header'>
			Statistic Graphs for " . $config['ui']['projectname'] . "
		</div>
	</div>
	<!--
	<div class='tab text-center m-2'>
			<button class='tablinks active' onclick='switchContainer(event,\"pokemonContainer\")'><b>Pokemon</b></button>
			<button class='tablinks' onclick='switchContainer(event,\"raidContainer\")'><b>Raids</b></button>
			<button class='tablinks' onclick='switchContainer(event,\"shinyContainer\")'><b>Shinys</b></button>
	</div>
	-->
	<div id='graphContainer' class='m-2'>
		<div class='row'>
			<div class='col-md-12'>
				<center>
					<div class='card text-center chartHeader'>
						<div class='heading " . $config['ui']['style'] . "'>
							Pokemon Today
						</div>
					</div>
				</center>
				<div class='row m-0 pt-3 chartContainer " . $config['ui']['style'] . "'>
					<div class='col-md-12'>
						<div id='pokeAmountToday' style='height: 400px; width: 100%;'></div>
					</div>
				</div>
			</div>
			
			<div class='col-md-6'>
				<center>
					<div class='card text-center chartHeader'>
						<div class='heading " . $config['ui']['style'] . "'>
							Pokemon (Week)
						</div>
					</div>
				</center>
				<div class='row m-0 pt-3 chartContainer " . $config['ui']['style'] . "'>
					<div class='col-md-12'>
						<div id='pokeAmountWeek' style='height: 400px; width: 100%;'></div>
					</div>
				</div>
			</div>
			<div class='col-md-6'>
				<center>
					<div class='card text-center chartHeader'>
						<div class='heading " . $config['ui']['style'] . "'>
							Pokemon (Month)
						</div>
					</div>
				</center>
				<div class='row m-0 pt-3 chartContainer " . $config['ui']['style'] . "'>
					<div class='col-md-12'>
						<div id='pokeAmountMonth' style='height: 400px; width: 100%;'></div>
					</div>
				</div>
			</div>
			<div class='col-md-6'>
				<center>
					<div class='card text-center chartHeader'>
						<div class='heading " . $config['ui']['style'] . "'>
							Raids (Week)
						</div>
					</div>
				</center>
				<div class='row m-0 pt-3 chartContainer " . $config['ui']['style'] . "'>
					<div class='col-md-12'>
						<div id='raidAmountWeek' style='height: 400px; width: 100%;'></div>
					</div>
				</div>
			</div>
			<div class='col-md-6'>
				<center>
					<div class='card text-center chartHeader'>
						<div class='heading " . $config['ui']['style'] . "'>
							Raids (Month)
						</div>
					</div>
				</center>
				<div class='row m-0 pt-3 chartContainer " . $config['ui']['style'] . "'>
					<div class='col-md-12'>
						<div id='raidAmountMonth' style='height: 400px; width: 100%;'></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
</div>";
?>

<script>
var configStyle = '<?php echo $config['ui']['style']?>';
switch(configStyle){
	case "light":
	var style = 'light1';
	break;
	case "dark":
	var style = 'dark1';
	break;
	default:
	var style = 'dark1';
	console.log('No style config found!')
	break;
}
console.log(style);
window.onload = function () {
var chart = new CanvasJS.Chart("pokeAmountToday", {
	animationEnabled: true,
	theme: style,
	title:{
		text: ""
	},
	axisY: {
		title: "Amount of Mon",
		valueFormatString: "#0",
		suffix: "",
		prefix: ""
	},
	axisX: {
		interval: 4,
		intervalType: "hour",
		valueFormatString: "#0':00'",
		minimum: 0,
		maximum: 24
	},
	data: [{
		name: "Total Amount",
		showInLegend: true,
		type: "line",
		markerSize: 5,
		xValueFormatString: "Total Monster Amount",
		yValueFormatString: "#0.",
		dataPoints: <?php echo json_encode($pokeData["Today"]["Total"], JSON_NUMERIC_CHECK); ?>
	},
	{
		name: "IV Amount",
		showInLegend: true,
		type: "line",
		markerSize: 5,
		xValueFormatString: "IV Monster Amount",
		yValueFormatString: "#0.",
		dataPoints: <?php echo json_encode($pokeData["Today"]["IV"], JSON_NUMERIC_CHECK); ?>
	}
	]
});
chart.render();

var chartPokemonWeek = new CanvasJS.Chart("pokeAmountWeek", {
	animationEnabled: true,
	theme: style,
	title:{
		text: ""
	},
	axisX: {
		interval: 1,
		intervalType: "day",    
        valueFormatString: "DD MMM",
        labelAngle: -30
	},
	axisY: {
		title: "Amount of Mon",
		valueFormatString: "#0"
	},
	data: [{
		name: "Total Amount",
		showInLegend: true,
		type: "line",
		markerSize: 5,
		xValueFormatString: "DD.MMM",
		xValueType: "dateTime",
		yValueFormatString: "#0.",
		dataPoints: <?php echo json_encode($pokeData["Total"]["Week"]["Total"], JSON_NUMERIC_CHECK); ?>
	},
	{
		name: "IV Amount",
		showInLegend: true,
		type: "line",
		markerSize: 5,
		xValueFormatString: "DD.MMM",
		xValueType: "dateTime",
		yValueFormatString: "#0.",
		dataPoints: <?php echo json_encode($pokeData["Total"]["Week"]["IV"], JSON_NUMERIC_CHECK); ?>
	}
	]
});
chartPokemonWeek.render();

var chartPokemonMonth = new CanvasJS.Chart("pokeAmountMonth", {
	animationEnabled: true,
	theme: style,
	title:{
		text: ""
	},
	axisX: {
		interval: 3,
		intervalType: "day",    
        valueFormatString: "DD MMM",
        labelAngle: -30
	},
	axisY: {
		title: "Amount of Mon",
		valueFormatString: "#0"
	},
	data: [{
		name: "Total Amount",
		showInLegend: true,
		type: "line",
		markerSize: 5,
		xValueFormatString: "DD.MMM",
		xValueType: "dateTime",
		yValueFormatString: "#0.",
		dataPoints: <?php echo json_encode($pokeData["Total"]["Month"]["Total"], JSON_NUMERIC_CHECK); ?>
	},
	{
		name: "IV Amount",
		showInLegend: true,
		type: "line",
		markerSize: 5,
		xValueFormatString: "DD.MMM",
		xValueType: "dateTime",
		yValueFormatString: "#0.",
		dataPoints: <?php echo json_encode($pokeData["Total"]["Month"]["IV"], JSON_NUMERIC_CHECK); ?>
	}
	]
});
chartPokemonMonth.render();

// Raids (Week)
var chartRaidsWeek = new CanvasJS.Chart("raidAmountWeek", {
	animationEnabled: true,
	theme: style,
	title:{
		text: ""
	},
	axisX: {
		interval: 1,
		intervalType: "day",
        valueFormatString: "DD MMM",
        labelAngle: -30
	},
	axisY: {
		title: "Amount of Raids",
		valueFormatString: "#0"
	},
	data: [{
		name: "Total",
		showInLegend: true,
		type: "line",
		markerSize: 5,
		xValueFormatString: "DD.MMM",
		xValueType: "dateTime",
		yValueFormatString: "#0.",
		dataPoints: <?php echo json_encode($raidData["Total"]["Week"]["Total"], JSON_NUMERIC_CHECK); ?>
	},{
		name: "Level 1",
		showInLegend: true,
		type: "line",
		markerSize: 5,
		xValueFormatString: "DD.MMM",
		xValueType: "dateTime",
		yValueFormatString: "#0.",
		dataPoints: <?php echo json_encode($raidData["Total"]["Week"]["L1"], JSON_NUMERIC_CHECK); ?>
	},{
		name: "Level 2",
		showInLegend: true,
		type: "line",
		markerSize: 5,
		xValueFormatString: "DD.MMM",
		xValueType: "dateTime",
		yValueFormatString: "#0.",
		dataPoints: <?php echo json_encode($raidData["Total"]["Week"]["L2"], JSON_NUMERIC_CHECK); ?>
	},{
		name: "Level 3",
		showInLegend: true,
		type: "line",
		markerSize: 5,
		xValueFormatString: "DD.MMM",
		xValueType: "dateTime",
		yValueFormatString: "#0.",
		dataPoints: <?php echo json_encode($raidData["Total"]["Week"]["L3"], JSON_NUMERIC_CHECK); ?>
	},{
		name: "Level 4",
		showInLegend: true,
		type: "line",
		markerSize: 5,
		xValueFormatString: "DD.MMM",
		xValueType: "dateTime",
		yValueFormatString: "#0.",
		dataPoints: <?php echo json_encode($raidData["Total"]["Week"]["L4"], JSON_NUMERIC_CHECK); ?>
	},{
		name: "Level 5",
		showInLegend: true,
		type: "line",
		markerSize: 5,
		xValueFormatString: "DD.MMM",
		xValueType: "dateTime",
		yValueFormatString: "#0.",
		dataPoints: <?php echo json_encode($raidData["Total"]["Week"]["L5"], JSON_NUMERIC_CHECK); ?>
	},
	]
});
chartRaidsWeek.render();

// Raids (Week)
var chartRaidsMonth = new CanvasJS.Chart("raidAmountMonth", {
	animationEnabled: true,
	theme: style,
	title:{
		text: ""
	},
	axisX: {
		interval: 3,
		intervalType: "day",
        valueFormatString: "DD MMM",
        labelAngle: -30
	},
	axisY: {
		title: "Amount of Raids",
		valueFormatString: "#0"
	},
	data: [{
		name: "Total",
		showInLegend: true,
		type: "line",
		markerSize: 5,
		xValueFormatString: "DD.MMM",
		xValueType: "dateTime",
		yValueFormatString: "#0.",
		dataPoints: <?php echo json_encode($raidData["Total"]["Month"]["Total"], JSON_NUMERIC_CHECK); ?>
	},{
		name: "Level 1",
		showInLegend: true,
		type: "line",
		markerSize: 5,
		xValueFormatString: "DD.MMM",
		xValueType: "dateTime",
		yValueFormatString: "#0.",
		dataPoints: <?php echo json_encode($raidData["Total"]["Month"]["L1"], JSON_NUMERIC_CHECK); ?>
	},{
		name: "Level 2",
		showInLegend: true,
		type: "line",
		markerSize: 5,
		xValueFormatString: "DD.MMM",
		xValueType: "dateTime",
		yValueFormatString: "#0.",
		dataPoints: <?php echo json_encode($raidData["Total"]["Month"]["L2"], JSON_NUMERIC_CHECK); ?>
	},{
		name: "Level 3",
		showInLegend: true,
		type: "line",
		markerSize: 5,
		xValueFormatString: "DD.MMM",
		xValueType: "dateTime",
		yValueFormatString: "#0.",
		dataPoints: <?php echo json_encode($raidData["Total"]["Month"]["L3"], JSON_NUMERIC_CHECK); ?>
	},{
		name: "Level 4",
		showInLegend: true,
		type: "line",
		markerSize: 5,
		xValueFormatString: "DD.MMM",
		xValueType: "dateTime",
		yValueFormatString: "#0.",
		dataPoints: <?php echo json_encode($raidData["Total"]["Month"]["L4"], JSON_NUMERIC_CHECK); ?>
	},{
		name: "Level 5",
		showInLegend: true,
		type: "line",
		markerSize: 5,
		xValueFormatString: "DD.MMM",
		xValueType: "dateTime",
		yValueFormatString: "#0.",
		dataPoints: <?php echo json_encode($raidData["Total"]["Month"]["L5"], JSON_NUMERIC_CHECK); ?>
	},
	]
});
chartRaidsMonth.render();
}
</script>
<link rel="stylesheet" href="./static/css/footerfix.css"/>
<link rel="stylesheet" href="./static/css/graphs.css"/>
<script type="text/javascript" src="static/js/tabs.js"></script>
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
<script type="text/javascript">
  //document.getElementById('pokemonContainer').style.display = "block";
</script>
