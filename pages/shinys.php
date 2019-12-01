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
			<div class='container'>
				<div id='shiny-rates' class=''></div>
			</div>
		</div>";
	}
	if (!empty($config['ui']['pages']['shinys']['alltime']) && $config['ui']['pages']['shinys']['alltime'] !== false) {
		$html .="
		<div id='ShinysTotal' class='tabcontent'>
		<div id='Alltime-Loading' class='loading'>
			<img src='static/images/loading.gif' Alt='Loading...' width='150' height='150'/>
		</div>
	
			<div id='Alltime-Info'class='container'>
			<center>
				<div style='max-width:1000px;padding-bottom:10px;text-align:left;'>
					Bei dieser Statistik fließen nur Daten von Tagen ein, bei denen von der jeweiligen Spezies mindestens ein Shiny gefunden wurde. Dies verringert die Ungenauigkeit durch neuere Shiny-Releases<br>
					(Bei einer zu geringeren gefunden Shiny-Anzahl kann die Rate sehr von der tatsächlichen Shiny-Rate abweichen)
				</div>
			</center>
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
}
if (shinyStatsAlltime){
	document.getElementById('Alltime-Loading').style.display = "block";
	document.getElementById('Alltime-Info').style.display = "none";
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
  if(shinyStats){
	tmp = createToken();
	sendRequest({ "type": "shinys", "stat": "shinyToday", "token": tmp }, function(data, status) {
		tmp = null;
		if (debug) {
		if (data === 0) {
			console.log("Failed to get data for shinypage.");
			return;
		} else {
			console.log("Shinys Today:", data);
		}
		}
		var obj = JSON.parse(data);
		var html = "";
		var count = 0;
		$.each(obj.shiny_rates, function(key, value) {
			if (count == 0) {
				html += "<div class='row justify-content-center'>";
			}
			var name = pokedex[value.pokeid];
			var pkmnImage = sprintf("<?=$config['urls']['images']['pokemon']?>", value.pokeid);
			if(value.form !== '0'){
				pkmnImage = pkmnImage.toString().replace("00.png", value.form + ".png");
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
		$('#shiny-rates').html(html);
		document.getElementById('Today-Loading').style.display = "none";
	});
  }
  
  if(shinyStatsAlltime){
	tmp = createToken();
	if (shinyStatsAlltimeMode){
		sendRequest({ "type": "shinys", "stat": "shinyAlltimeCustom", "token": tmp }, function(data, status) {
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
			var html = "";
			var count = 0;
			$.each(obj.shiny_rates_total_custom, function(key, value) {
			if (count == 0) {
				html += "<div class='row justify-content-center'>";
			}
			var name = pokedex[value.pokeid];
			var pkmnImage = sprintf("<?=$config['urls']['images']['pokemon']?>", value.pokeid);
			if(value.form !== '0'){
				pkmnImage = pkmnImage.toString().replace("00.png", value.form + ".png");
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
			$('#shiny-rates-total').html(html);
			document.getElementById('Alltime-Loading').style.display = "none";
			document.getElementById('Alltime-Info').style.display = "block";
		});
	}else{
		sendRequest({ "type": "shinys", "stat": "shinyAlltime", "token": tmp }, function(data, status) {
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
			var html = "";
			var count = 0;
			$.each(obj.shiny_rates_total, function(key, value) {
			if (count == 0) {
				html += "<div class='row justify-content-center'>";
			}
			var name = pokedex[value.pokeid];
			var pkmnImage = sprintf("<?=$config['urls']['images']['pokemon']?>", value.pokeid);
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
			$('#shiny-rates-total').html(html);
			document.getElementById('Alltime-Loading').style.display = "none";
			document.getElementById('Alltime-Info').style.display = "block";
		});
	}
  }
  
}

function createToken() {
  //TODO: Secure
  <?php $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(16)); ?>
  return "<?php echo $_SESSION['token']; ?>";
}
</script>