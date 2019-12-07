<?php
require_once './static/data/pokedex.php';

$html = "

<div class='card text-center p-1 m-3'>
	<div class='tab'>";
		if (!empty($config['ui']['pages']['shinys']['today']) && $config['ui']['pages']['shinys']['today'] !== false) {
			$html .="
			<button id='TodayButton' class='tablinks heading text-light' onclick='openCity(event,\"Today\")' style='float:left;width:50%'><b>Heute</b></button>";
		}
		if (!empty ($config['ui']['pages']['shinys']['alltime']) && $config['ui']['pages']['shinys']['alltime'] !== false) {
			$html .="
			<button id='AlltimeButton' class='tablinks heading text-light' onclick='openCity(event,\"ShinysTotal\")' style='width:50%'><b>Gesamt</b></button>";
		}
	$html .="
	</div>";
	
	if (!empty($config['ui']['pages']['shinys']['today']) && $config['ui']['pages']['shinys']['today'] !== false) {
		$html .="
		<div id='Today' class='tabcontent active'>
			<div id='Today-Loading' class='loading'>
				<img src='static/images/loading.gif' Alt='Loading...' width='150' height='150'/>
			</div>
			<div id='Today-Info' class='container' style='height: 150px;'>
				<p>
					<u>Sortieren:</u>
					<form id='sort-order' method='POST'>
						<div>
							<button type='submit' onclick='javascript:setOrder(0, \"shinyToday\", \"shiny-rates\", 0);return false;' class='btn btn-dark mx-3 my-1'>Pokemon ID (↑)</button>
							<button type='submit' onclick='javascript:setOrder(2, \"shinyToday\", \"shiny-rates\", 0);return false;' class='btn btn-dark mx-3 my-1'>Shiny Rate (↑)</button>
						</div>
						<div>
							<button type='submit' onclick='javascript:setOrder(1, \"shinyToday\", \"shiny-rates\", 0);return false;' class='btn btn-dark mx-3 my-1'>Pokemon ID (↓)</button>
							<button type='submit' onclick='javascript:setOrder(3, \"shinyToday\", \"shiny-rates\", 0);return false;' class='btn btn-dark mx-3 my-1'>Shiny Rate (↓)</button>
						</div>
					</form>
				</p>
			</div>
			<div class='container mt-3'>
				<div id='shiny-rates' class=''></div>
			</div>
		</div>";
	}
	if (!empty($config['ui']['pages']['shinys']['alltime']) && $config['ui']['pages']['shinys']['alltime'] !== false) {
		$html .="
		<div id='ShinysTotal' class='tabcontent'>
			<div id='Alltime-Info' class='container'>
				<center>
					<div style='max-width:1000px;padding-bottom:10px;text-align:left;'>
						Bei dieser Statistik fließen nur Daten von Tagen ein, bei denen von der jeweiligen Spezies mindestens ein Shiny gefunden wurde. Dies verringert die Ungenauigkeit durch neuere Shiny-Releases<br>
						(Bei einer zu geringeren gefunden Shiny-Anzahl kann die Rate sehr von der tatsächlichen Shiny-Rate abweichen)
					</div>
				</center>
				<div id='alltime-sort' style='height: 150px;'>
					<p>
						<u>Sortieren:</u>
						<form id='sort-order' method='POST'>
							<div>
								<button type='submit' onclick='javascript:setOrder(0, \"shinyAlltime\", \"shiny-rates-total\", 2);return false;' class='btn btn-dark mx-3 my-1'>Pokemon ID (↑)</button>
								<button type='submit' onclick='javascript:setOrder(2, \"shinyAlltime\", \"shiny-rates-total\", 2);return false;' class='btn btn-dark mx-3 my-1'>Shiny Rate (↑)</button>
							</div>
							<div>
								<button type='submit' onclick='javascript:setOrder(1, \"shinyAlltime\", \"shiny-rates-total\", 2);return false;' class='btn btn-dark mx-3 my-1'>Pokemon ID (↓)</button>
								<button type='submit' onclick='javascript:setOrder(3, \"shinyAlltime\", \"shiny-rates-total\", 2);return false;' class='btn btn-dark mx-3 my-1'>Shiny Rate (↓)</button>
							</div>
						</form>
					</p>
				</div>
			</div>
			<div id='Alltime-Loading' class='loading'>
				<img src='static/images/loading.gif' Alt='Loading...' width='150' height='150' />
			</div>
			<div class='container mt-3'>
				<div id='shiny-rates-total' class=''></div>
			</div>
		</div>";
	}
	$html .="
</div>";
echo $html;
?>
<link rel="stylesheet" href="./static/css/dashboard.css"/>
<link rel="stylesheet" href="./static/css/footerfix.css"/>
<script type="text/javascript" src="static/js/dashboard.js"></script>
<script type='text/javascript' src='./static/js/datepicker.js'></script>
<script type='text/javascript' src='./static/js/pokedex.js'></script>
<script type="text/javascript" src="./static/js/utils.js"></script>

<script type='text/javascript'>
var debug = <?=$config['core']['showDebug'] !== false ? '1' : '0'?>;
var shinyStats = <?=(!empty($config['ui']['pages']['shinys']['today']) && $config['ui']['pages']['shinys']['today']) !== false ? '1' : '0'?>;
var shinyStatsAlltime = <?= (!empty($config['ui']['pages']['shinys']['alltime']) && $config['ui']['pages']['shinys']['alltime'] !== false) ? '1' : '0'?>;
var shinyStatsAlltimeMode = <?= (!empty($config['ui']['pages']['shinys']['customMode']) && $config['ui']['pages']['shinys']['customMode'] !== false) ? '1' : '0'?>;

// Presets for Styling
if(shinyStats){
	document.getElementById('Today-Loading').style.display = "block";
	document.getElementById('Today-Info').style.display = "none";
}
if (shinyStatsAlltime){
	document.getElementById('Alltime-Loading').style.display = "block";
	document.getElementById('Alltime-Info').style.display = "none";
	document.getElementById('alltime-sort').style.display = "none";
}

//If ShinystatsToday is enabled, show Today-Tab from first load, if its disabled, show alltime
if(shinyStats){
	document.getElementById("TodayButton").classList.add('active');
	document.getElementById('Today').style.display = "block";
} else if (shinyStatsAlltime){
	document.getElementById("AlltimeButton").className += " active";
	document.getElementById('ShinysTotal').style.display = "block";
}

getStats();

function getStats() {
  if(shinyStatsAlltime){
	if (shinyStatsAlltimeMode){
		getContent(2, "shinyAlltimeCustom", "shiny-rates-total", 1);
	}else{
		getContent(2, "shinyAlltime", "shiny-rates-total", 2);
	}
  }
  if(shinyStats){
	getContent(2, "shinyToday", "shiny-rates", 0);
  }
}

function getContent(order, stat, container, dataset) {
		if (stat == "shinyAlltimeCustom" || stat == "shinyAlltime") {
			document.getElementById('Alltime-Loading').style.display = "block";
			document.getElementById('alltime-sort').style.display = "none";
		} else if (stat == "shinyToday") {
			document.getElementById('Today-Loading').style.display = "block";
			document.getElementById('Today-Info').style.display = "none";
		}
	tmp = createToken();
	sendRequest({ "order": order,  "type": "shinys", "stat": stat, "token": tmp }, function(data, status) {
		tmp = null;
		if (debug) {
			if (data === 0) {
				console.log("Failed to get data for shinypage.");
				return;
			} else {
				console.log("Shinys:", data);
			}
		}
		var obj = JSON.parse(data);
		switch (dataset) {
			case 0:
				dataset = obj.shiny_rates
			break;
			case 1:
				dataset = obj.shiny_rates_total_custom
			break;
			case 2:
				dataset = obj.shiny_rates_total
			break;
		}

		var html = "";
		var count = 0;
		$.each(dataset, function(key, value) {
			if (count == 0) {
				html += "<div class='row justify-content-center'>";
			}
			var name = pokedex[value.pokeid];
			var pkmnImage = sprintf("<?=$config['urls']['images']['pokemon']?>", value.pokeid);
			
			if (value.pokeform != null && value.pokeform !== '0') {
				pkmnImage = pkmnImage.toString().replace("00.png", value.pokeform + ".png");
			}
			html += "<div class='col-md-2" + (count == 0 ? " col-md-offset-1" : "") + "'>";
			html += "<img src='" + pkmnImage + "' width='64' height='64'><p><span class='text-nowrap'>" + name + ": " + numberWithCommas(value.count) + "</span>";
			html += "<span class='text-nowrap'><br>(" + value.count + " : " + value.total + ")<br><b>Ø 1 : " + Math.round(value.total/value.count) + "</b></span></p></br>";
			html += "</div>";
			if (count == 4) {
				html += "</div>";
				count = 0;
			} else {
				count++;
			}
		});
		$('#' + container).html(html);
		if (stat == "shinyAlltimeCustom" || stat == "shinyAlltime") {
			document.getElementById('Alltime-Loading').style.display = "none";
			document.getElementById('Alltime-Info').style.display = "block";
			document.getElementById('alltime-sort').style.display = "block";
		} else if (stat == "shinyToday") {
			document.getElementById('Today-Loading').style.display = "none";
			document.getElementById('Today-Info').style.display = "block";
			
		}
	});
}

function setOrder(order, stat, container, dataset) {
		if (debug) {
			console.log("Sort in order mode: " + order);
			console.log("Api Call stat: " + stat);
			console.log("html container to write in: " + container);
			console.log("Process api data: " + dataset);
		}
	if (shinyStatsAlltimeMode && dataset == 2){
		dataset--;
		stat = "shinyAlltimeCustom";
		if (debug) {
			console.log("Detected custom alltime-table, changing dataset to: " + dataset);
			console.log("Detected custom alltime-table, changing stat to: " + stat);
		}
	}
getContent(order, stat, container, dataset);
}


function createToken() {
  //TODO: Secure
  <?php $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(16)); ?>
  return "<?php echo $_SESSION['token']; ?>";
}
</script>