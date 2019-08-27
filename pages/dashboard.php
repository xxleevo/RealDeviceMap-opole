<?php
$onlineDevices = get_table_count_noId("device where uuid LIKE '%SE%' OR uuid LIKE '%PL10%' AND last_seen > UNIX_TIMESTAMP()-120");
$onlineDevicesQuests = get_table_count_noId("device where uuid LIKE '%SE%' AND instance_name LIKE 'Quests%' OR uuid LIKE '%PL10%' AND instance_name LIKE 'Quests%' and last_seen > UNIX_TIMESTAMP()-120");
$maxDevices = get_table_count_noId("device where uuid LIKE '%SE%' OR uuid LIKE '%PL10%'");
$maxDevicesQuests = $maxDevices;
$percentOnline = (($onlineDevices/$maxDevices) *100);
$percentOnlineQuests = (($onlineDevicesQuests/$maxDevicesQuests)*100);

$activeWhite = get_table_count("gym where updated > (UNIX_TIMESTAMP()-14400) AND team_id = 0");
$activeBlue = get_table_count("gym where updated > (UNIX_TIMESTAMP()-14400) AND team_id = 1");
$activeRed = get_table_count("gym where updated > (UNIX_TIMESTAMP()-14400) AND team_id = 2");
$activeYellow = get_table_count("gym where updated > (UNIX_TIMESTAMP()-14400) AND team_id = 3");
$activeTotal = ($activeWhite + $activeBlue + $activeRed + $activeYellow);

$activeWhitePercent = (($activeWhite/$activeTotal)*100);
$activeBluePercent = (($activeBlue/$activeTotal)*100);
$activeRedPercent = (($activeRed/$activeTotal)*100);
$activeYellowPercent = (($activeYellow/$activeTotal)*100); 

$totalhundos = get_table_count_noId("pokemon where iv > 95");

$html = "
<div style='max-width:1280px;margin: 0 auto !important;float: none !important;'>

<h2 class='page-header text-center'>Übersicht</h2>
<div class='card p-1 m-3'>
Willkommen auf unserer Seite!<br>
Finde Monster ,Raids, Stops ,Arenen, Monster mit IV, Quests und vieles mehr!<br>
Auf dieser Seite findest du eine Übersicht darüber, welche Daten auf unserer Map zu finden sind.<br>
</div>
<div class='card text-center p-1 m-3'>
	<div class='card-header heading text-light'><b>Allgemein</b></div>
	<div class='card-body'>
		<div class='container'>
			<div class='row'>
				<div class='col-md-4'>
					<div class='list-group'>
						<a class='list-group-item'>
							<h3 class='pull-right'><img src='./static/images/quests/1.png' width='48' height='48'/></h3>
							<h4 class='list-group-item-heading pokemon-count'>0</h4>
							<p class='list-group-item-text'>Aktive Monster</p>
						</a>
					</div>
				</div>
				<div class='col-md-4'>
					<div class='list-group'>
						<a class='list-group-item'>
							<h3 class='pull-right'><img src='./static/images/iv.png' width='48' height='48'/></h3>
							<h4 class='list-group-item-heading active-iv-count'>0</h4>
							<p class='list-group-item-text'>Monster mit IV</p>
						</a>
					</div>
				</div>
				<div class='col-md-4'>
					<div class='list-group'>
						<a class='list-group-item'>
							<h3 class='pull-right'><img src='./static/images/battle.png'  width='48' height='48'/></h3>
							<h4 class='list-group-item-heading raids-count'>0</h4>
							<p class='list-group-item-text'>Aktive Raids & Eier</p>
						</a>
					</div>
				</div>
				<div class='col-md-4'>
					<div class='list-group'>
						<a class='list-group-item'>
							<h3 class='pull-right'><img src='./static/images/quests/1.png' width='48' height='48'/></h3>
							<h4 class='list-group-item-heading total-pokemon-count'>0</h4>
							<p class='list-group-item-text'>Monster heute Gesamt</p>
						</a>
					</div>
				</div>

				<div class='col-md-4'>
					<div class='list-group'>
						<a class='list-group-item'>
							<h3 class='pull-right'><img src='./static/images/iv.png' width='48' height='48'/></h3>
							<h4 class='list-group-item-heading total-iv-count'>0</h4>
							<p class='list-group-item-text'>IV Monster heute Gesamt</p>
						</a>
					</div>
				</div>
				<div class='col-md-4'>
					<div class='list-group'>
						<a class='list-group-item'>
							<h3 class='pull-right'><img src='./static/images/teams/teams.png' width='auto' height='48'/></h3>
							<h4 class='list-group-item-heading gym-count'>0</h4>
							<p class='list-group-item-text'>Arenen Gesamt</p>
						</a>
					</div>
				</div>
			</div>
		<div>
	</div>
</div>
</div>
</div>

<!--
<div class='card text-center p-1 m-3'>
	<div class='card-header heading text-light'><b data-i18n='dashboard_pokemon_top10'>Top 10 Monster (Heute)</b></div>
	<div class='card-body'>
		<div class='container'>
			<div id='top-10-pokemon' class='row justify-content-center'></div>
		</div>
	</div>
</div>
-->

<div class='card text-center p-1 m-3'>
	<div class='tab'>
		<button class='tablinks heading text-light active' onclick='openCity(event,\"Top10MonTod\")'><b>Top 10 Heute</b></button>
		<button class='tablinks heading text-light' onclick='openCity(event,\"Top10MonLife\")'><b>Top 10 Gesamt</b></button>
		<button class='tablinks heading text-light' onclick='openCity(event,\"Top10MonIV\")'><b>Top 10 IV Heute</b></button>
		<button class='tablinks heading text-light' onclick='openCity(event,\"Top10MonIV95\")'><b>96+ Heute</b></button>
		<button class='tablinks heading text-light' onclick='openCity(event,\"Top10MonIV100\")'><b>100er Heute</b></button>
	</div>
	

	<div id='Top10MonTod' class='tabcontent'>
		<div class='container'>
			<div id='top-10-pokemon' class='row justify-content-center active'></div>
		</div>
	</div>
	
	<div id='Top10MonLife' class='tabcontent'>
		<div class='container'>
			<div id='top-10-pokemon-lifetime' class='row justify-content-center'></div>
		</div>
	</div>
	
	<div id='Top10MonIV' class='tabcontent'>
		<div class='container'>
			<div id='top-10-pokemon-iv' class='row justify-content-center'></div>
		</div>
	</div>
	
	<div id='Top10MonIV95' class='tabcontent'>
		<div class='container'>
			<div id='top-10-pokemon-iv95' class='row justify-content-center'></div>
		</div>
		<div class='row justify-content-around'>
				<center>
					<a class='list-group-item' style='width:200px;'>
						<h3 class='pull-right'><img src='./static/images/iv.png' width='48' height='48'/></h3>
						<h4 class='list-group-item-heading total-iv95'>0</h4>
						<p class='list-group-item-text'>96+ Heute</p>
					</a>
				</center>
				<center>
					<a class='list-group-item' style='width:200px;'>
						<h3 class='pull-right'><img src='./static/images/iv.png' width='48' height='48'/></h3>
						<h4 class='list-group-item-heading active-iv95'>0</h4>
						<p class='list-group-item-text'>96+ Live</p>
					</a>
				</center>
		</div>
	</div>
	
	<div id='Top10MonIV100' class='tabcontent'>
		<div class='container'>
			<div id='top-10-pokemon-iv100' class='row justify-content-center'></div>
		</div>
		<div class='row justify-content-around'>
				<center>
					<a class='list-group-item' style='width:200px;'>
						<h3 class='pull-right'><img src='./static/images/iv.png' width='48' height='48'/></h3>
						<h4 class='list-group-item-heading total-iv100'>0</h4>
						<p class='list-group-item-text'>100er Heute</p>
					</a>
				</center>
				<center>
					<a class='list-group-item' style='width:200px;'>
						<h3 class='pull-right'><img src='./static/images/iv.png' width='48' height='48'/></h3>
						<h4 class='list-group-item-heading active-iv100'>0</h4>
						<p class='list-group-item-text'>100er Live</p>
					</a>
				</center>
		</div>
	</div>
	
</div>

<div class='card text-center p-1 m-3'>
  <div class='card-header heading text-light'><b>Teams (aktive Arenen)</b></div>
  <div class='card-body'>
    <div class='container'>
      <div class='row'>
        <div class='col-md-3'>
          <div class='list-group'>
            <a class='list-group-item neutral'>
              <h3 class='pull-right'><img src='./static/images/teams/neutral.png' width='64' height='64'/></h3>
              <h4 class='list-group-item-heading gym-white-active'>0</h4>
              <p class='list-group-item-text'>Neutral (". round($activeWhitePercent,1) ."%) </p>
            </a>
          </div>
        </div>
        <div class='col-md-3'>
          <div class='list-group'>
            <a class='list-group-item valor'>
              <h3 class='pull-right'><img src='./static/images/teams/valor.png' width='64' height='64'/></h3>
              <h4 class='list-group-item-heading gym-red-active'>0</h4>
              <p class='list-group-item-text'>Valor (". round($activeRedPercent,1) ."%) </p>
            </a>
          </div>
        </div>
        <div class='col-md-3'>
          <div class='list-group'>
            <a class='list-group-item mystic'>
              <h3 class='pull-right'><img src='./static/images/teams/mystic.png' width='64' height='64'/></h3>
              <h4 class='list-group-item-heading gym-blue-active'>0</h4>
              <p class='list-group-item-text'>Mystic (". round($activeBluePercent,1) ."%) </p>
            </a>
          </div>
        </div>
        <div class='col-md-3'>
          <div class='list-group'>
            <a class='list-group-item instinct'>
              <h3 class='pull-right'><img src='./static/images/teams/instinct.png' width='64' height='64'/></h3>
              <h4 class='list-group-item-heading gym-yellow-active'>0</h4>
              <p class='list-group-item-text'>Instinct (". round($activeYellowPercent,1) ."%) </p>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class='card text-center p-1 m-3'>
  <div class='card-header heading text-light'><b data-i18n='dashboard_pokestops_header'>Stops</b></div>
  <div class='card-body'>
    <div class='container'>
      <div class='row'>
        <div class='col-md-3'>
          <a class='list-group-item'>
            <h3 class='pull-right'><img src='./static/images/stop.png' width='48' height='48'/></h3>
            <h4 class='list-group-item-heading pokestop-count'>0</h4>
            <p class='list-group-item-text' data-i18n='dashboard_pokestops_total'>Stops</p>
          </a>
        </div>
        <div class='col-md-3'>
          <a class='list-group-item'>
            <h3 class='pull-right'><img src='./static/images/module.png' width='48' height='48'/></h3>
            <h4 class='list-group-item-heading lured-pokestop-count'>0</h4>
            <p class='list-group-item-text' data-i18n='dashboard_pokestops_lured'>Aktive Module</p>
          </a>
        </div>
        <div class='col-md-3'>
          <a class='list-group-item'>
            <h3 class='pull-right'><img src='./static/images/quests/0.png' width='48' height='48'/></h3>
            <h4 class='list-group-item-heading quest-pokestop-count'>0</h4>
            <p class='list-group-item-text' data-i18n='dashboard_pokestops_quests'>Quests</p>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class='card text-center p-1 m-3'>
  <div class='card-header heading text-light'><b>Spawnpunkte</b></div>
  <div class='card-body'>
    <div class='container'>
      <div class='row'>
        <div class='col-md-3'>
          <a class='list-group-item'>
            <h3 class='pull-right'><img src='./static/images/spawns.png' width='64' height='64'/></h3>
            <h4 class='list-group-item-heading spawnpoint-count'>0</h4>
            <p class='list-group-item-text'>Spawnpunkte Gesamt</p>
          </a>
        </div>
        <div class='col-md-3'>
          <a class='list-group-item'>
            <h3 class='pull-right'><img src='./static/images/spawns_verified.png' width='64' height='64'/></h3>
            <h4 class='list-group-item-heading found-spawnpoint-count'>0</h4>
            <p class='list-group-item-text'>Verifizierte Despawnzeiten</p>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>";
// Show this Raid Stats only from 4:00 to 23:00
if (Date('H') >= 4 && Date('H') < 23) {
$html .="
<div class='card text-center p-1 m-3'>
  <div class='card-header heading text-light'><b>Raids & Eier</b></div>
  <div class='card-body'>
    <div class='container'>
      <div class='row'>
        <div class='col-md-4'>
          <a class='list-group-item'>
            <h3 class='pull-right'><img src='./static/egg-icons/eggs.png' width='auto' height='64'/></h3>
            <h4 class='list-group-item-heading egg-total-count'>0</h4>
            <p class='list-group-item-text'>Raideier Gesamt</p>
          </a>
        </div>
        <div class='col-md-4'>
          <a class='list-group-item'>
            <h3 class='pull-right'><img src='./static/egg-icons/eggs_normal.png' width='auto' height='64'/></h3>
            <h4 class='list-group-item-heading egg-normal-count'>0</h4>
            <p class='list-group-item-text'>Normale Raideier</p>
          </a>
        </div>
        <div class='col-md-4'>
          <a class='list-group-item'>
            <h3 class='pull-right'><img src='./static/egg-icons/5.png' width='auto' height='64'/></h3>
            <h4 class='list-group-item-heading egg-legendary-count'>0</h4>
            <p class='list-group-item-text'>Legendäre Raideier</p>
          </a>
        </div>
        <div class='col-md-4'>
          <a class='list-group-item'>
            <h3 class='pull-right'><img src='./static/egg-icons/raids_hatched.png' width='auto' height='64'/></h3>
            <h4 class='list-group-item-heading raids-hatched-total'>0</h4>
            <p class='list-group-item-text'>Raids Gesamt</p>
          </a>
        </div>
        <div class='col-md-4'>
          <a class='list-group-item'>
            <h3 class='pull-right'><img src='./static/egg-icons/raids_hatched_normal.png' width='auto' height='64'/></h3>
            <h4 class='list-group-item-heading raids-hatched-normal'>0</h4>
            <p class='list-group-item-text'>Normale Raids</p>
          </a>
        </div>
        <div class='col-md-4'>
          <a class='list-group-item'>
            <h3 class='pull-right'><img src='./static/egg-icons/5-open.png' width='auto' height='64'/></h3>
            <h4 class='list-group-item-heading raids-hatched-legendary'>0</h4>
            <p class='list-group-item-text'>Legendäre Raids</p>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>";
}
$html .="


<div class='card text-center p-1 m-3'>
  <div class='card-header heading text-light'><b>Status</b></div>
  <div class='card-body'>
    <div class='container'>
      <div class='row'>
        <div class='col-md-4'>
          <a class='list-group-item'>";
            
			if($onlineDevices >= $maxDevices){
			$html .="
					<h3 class='pull-right'><img src='./static/images/online.png' width='48' height='48'/></h3>
					<h4 class='list-group-item-heading'>Aktiv</h4>";
			}else if($onlineDevices > 0 && $onlineDevices < $maxDevices){
			$html .="
					<h3 class='pull-right'><img src='./static/images/unstable.png' width='48' height='48'/></h3>
					<h4 class='list-group-item-heading'>Unstable (". round($percentOnline) ."%)</h4>";
			}else{
			$html .="
					<h3 class='pull-right'><img src='./static/images/offline.png' width='48' height='48'/></h3>
					<h4 class='list-group-item-heading'>Inaktiv</h4>";
			}
			$html .="
            <p class='list-group-item-text'>Scan</p>
          </a>
        </div>
        <div class='col-md-4'>
          <a class='list-group-item'>";
            
			if($onlineDevicesQuests >= $maxDevicesQuests){
			$html .="
					<h3 class='pull-right'><img src='./static/images/online.png' width='48' height='48'/></h3>
					<h4 class='list-group-item-heading'>Aktiv</h4>";
			}else if($onlineDevicesQuests >= 1 && $onlineDevicesQuests < $maxDevicesQuests){
			$html .="
					<h3 class='pull-right'><img src='./static/images/unstable.png' width='48' height='48'/></h3>
					<h4 class='list-group-item-heading'>Unstable (". round($percentOnlineQuests) ."%)</h4>";
			}else{
			$html .="
					<h3 class='pull-right'><img src='./static/images/offline.png' width='48' height='48'/></h3>
					<h4 class='list-group-item-heading'>Inaktiv</h4>";
			}
			$html .="
            <p class='list-group-item-text'>Questscan</p>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class ='p-1 m-3'>
<div class='row'>
<div class='card text-center p-1 m-3 col-md-3'>
	<div class='card-header heading text-light'><b>Map</b></div>
	<div class='card-body'>
		<div class='container'>
          <a href='https://map.rocketmapdo.de/' class='link'>
			<center>
			<img src='./static/images/map.png' width='128' height='auto'/></h3>
            <p> > Zur Map < </p>
			</center>
          </a>
		</div>
	</div>
</div>
<div class='card text-center p-1 m-3 col-md-3'>
	<div class='card-header heading text-light'><b>Discord</b></div>
	<div class='card-body'>
		<div class='container'>
          <a href='https://discord.gg/zwsGCUS' class='link'>
			<center>
			<img src='./static/images/discord.png' width='128' height='auto'/></h3>
            <p> > Zum Discord Server < </p>
			</center>
          </a>
		</div>
	</div>
</div>
<div class='card text-center p-1 m-3 col-md-3'>
	<div class='card-header heading text-light'><b>Patreon</b></div>
	<div class='card-body'>
		<div class='container'>
          <a href='https://www.patreon.com/rocketmapdo' class='link'>
			<center>
			<img src='./static/images/patreon.png' width='128' height='auto'/></h3>
            <p> > Zu Patreon (Spenden) < </p>
			</center>
          </a>
		</div>
	</div>
</div>

</div>
";
echo $html;
?>
<link rel="stylesheet" href="./static/css/dashboard.css"/>
<link rel="stylesheet" href="./static/css/footerfix.css"/>
<script type="text/javascript" src="./static/js/pokedex.js"></script>
<script type="text/javascript" src="./static/js/utils.js"></script>
<script type="text/javascript" src="static/js/dashboard.js"></script>
<script type="text/javascript">
var debug = <?=$config['core']['showDebug'] !== false ? '1' : '0'?>;
getStats();

function getStats() {
  var tmp = createToken();
  sendRequest({ "type": "dashboard", "stat": "pokemon", "token": tmp }, function(data, status) {
    tmp = null;
    if (debug) {
      if (data === 0) {
        console.log("Failed to get data for dashboard.");
        return;
      } else {
        console.log("Dashboard:", data);
      }
    }
    var obj = JSON.parse(data);
    updateCounter(".pokemon-count", obj.active_pokemon);
    updateCounter(".total-pokemon-count", obj.pokemon);
    updateCounter(".total-iv-count", obj.iv_total);
    updateCounter(".active-iv-count", obj.iv_active);
    updateCounter(".total-iv95", obj.iv_95_total);
    updateCounter(".active-iv95", obj.iv_95_active);
    updateCounter(".total-iv100", obj.iv_100_total);
    updateCounter(".active-iv100", obj.iv_100_active);
  });

  tmp = createToken();
  sendRequest({ "type": "dashboard", "stat": "gyms", "token": tmp }, function(data, status) {
    tmp = null;
    if (debug) {
      if (data === 0) {
        console.log("Failed to get data for dashboard.");
        return;
      } else {
        console.log("Dashboard:", data);
      }
    }
    var obj = JSON.parse(data);
    updateCounter(".gym-count", obj.gyms);
    updateCounter(".raids-count", obj.raids);
    updateCounter(".neutral-gyms-count", obj.neutral);
    updateCounter(".valor-gyms-count", obj.valor);
    updateCounter(".mystic-gyms-count", obj.mystic);
    updateCounter(".instinct-gyms-count", obj.instinct);
    updateCounter(".gym-white-active", obj.neutralActive);
    updateCounter(".gym-blue-active", obj.mysticActive);
    updateCounter(".gym-red-active", obj.valorActive);
    updateCounter(".gym-yellow-active", obj.instinctActive);
    updateCounter(".raids-hatched-total", obj.hatchedRaids);
    updateCounter(".raids-hatched-normal", obj.hatchedNormalRaids);
    updateCounter(".raids-hatched-legendary", obj.hatchedLegendaryRaids);
    updateCounter(".egg-total-count", obj.eggs);
    updateCounter(".egg-normal-count", obj.eggsNormal);
    updateCounter(".egg-legendary-count", obj.eggsLegendary);
  });

  tmp = createToken();
  sendRequest({ "type": "dashboard", "stat": "pokestops", "token": tmp }, function(data, status) {
    tmp = null;
    if (debug) {
      if (data === 0) {
        console.log("Failed to get data for dashboard.");
        return;
      } else {
        console.log("Dashboard:", data);
      }
    }
    var obj = JSON.parse(data);
    updateCounter(".pokestop-count", obj.pokestops);
    updateCounter(".lured-pokestop-count", obj.lured);
    updateCounter(".quest-pokestop-count", obj.quests);
  });

  tmp = createToken();
  sendRequest({ "type": "dashboard", "stat": "tth", "token": tmp }, function(data, status) {
    tmp = null;
    if (debug) {
      if (data === 0) {
        console.log("Failed to get data for dashboard.");
        return;
      } else {
        console.log("Dashboard:", data);
      }
    }
    var obj = JSON.parse(data);
    var percentage = Math.round(((obj.tth_found / obj.tth_total) * 100), 2);
    updateCounter(".spawnpoint-count", obj.tth_total);
    updateCounter(".found-spawnpoint-count", obj.tth_found);
    updateCounter(".missing-spawnpoint-count", obj.tth_missing);
    updateCounter(".percentage-spawnpoint-count", percentage);
  });

  tmp = createToken();
  sendRequest({ "type": "dashboard", "stat": "top", "token": tmp }, function(data, status) {
    tmp = null;
    if (debug) {
      if (data === 0) {
        console.log("Failed to get data for dashboard.");
        return;
      } else {
        console.log("Dashboard:", data);
      }
    }
    var obj = JSON.parse(data);
    var html = "";
    var count = 0;
    $.each(obj.top10_pokemon, function(key, value) {
      if (count == 0) {
        html += "<div class='row justify-content-center'>";
      }
      var name = pokedex[value.pokemon_id];
      var pkmnImage = sprintf("<?=$config['urls']['images']['pokemon']?>", value.pokemon_id);
      html += "<div class='col-md-2" + (count == 0 ? " col-md-offset-1" : "") + "'>";
      html += "<img src='" + pkmnImage + "' width='48' height='48'><span class='text-nowrap'>" + name + ": " + numberWithCommas(value.count) + "</span></br>";
      html += "</div>";
      if (count == 4) {
        html += "</div>";
        count = 0;
      } else {
        count++;
      }
    });
    $('#top-10-pokemon').html(html);
	
	var obj = JSON.parse(data);
    var html = "";
    var count = 0;
    $.each(obj.top10_pokemon_iv, function(key, value) {
      if (count == 0) {
        html += "<div class='row justify-content-center'>";
      }
      var name = pokedex[value.pokemon_id];
      var pkmnImage = sprintf("<?=$config['urls']['images']['pokemon']?>", value.pokemon_id);
      html += "<div class='col-md-2" + (count == 0 ? " col-md-offset-1" : "") + "'>";
      html += "<img src='" + pkmnImage + "' width='48' height='48'><span class='text-nowrap'>" + name + ": " + numberWithCommas(value.count) + "</span></br>";
      html += "</div>";
      if (count == 4) {
        html += "</div>";
        count = 0;
      } else {
        count++;
      }
    });
    $('#top-10-pokemon-iv').html(html);
	
	var obj = JSON.parse(data);
    var html = "";
    var count = 0;
    $.each(obj.top10_pokemon_iv95, function(key, value) {
      if (count == 0) {
        html += "<div class='row justify-content-center'>";
      }
      var name = pokedex[value.pokemon_id];
      var pkmnImage = sprintf("<?=$config['urls']['images']['pokemon']?>", value.pokemon_id);
      html += "<div class='col-md-2" + (count == 0 ? " col-md-offset-1" : "") + "'>";
      html += "<img src='" + pkmnImage + "' width='48' height='48'><span class='text-nowrap'>" + name + ": " + numberWithCommas(value.count) + "</span></br>";
      html += "</div>";
      if (count == 4) {
        html += "</div>";
        count = 0;
      } else {
        count++;
      }
    });
    $('#top-10-pokemon-iv95').html(html);
	
	var obj = JSON.parse(data);
    var html = "";
    var count = 0;
    $.each(obj.top10_pokemon_iv100, function(key, value) {
      if (count == 0) {
        html += "<div class='row justify-content-center'>";
      }
      var name = pokedex[value.pokemon_id];
      var pkmnImage = sprintf("<?=$config['urls']['images']['pokemon']?>", value.pokemon_id);
      html += "<div class='col-md-2" + (count == 0 ? " col-md-offset-1" : "") + "'>";
      html += "<img src='" + pkmnImage + "' width='48' height='48'><span class='text-nowrap'>" + name + ": " + numberWithCommas(value.count) + "</span></br>";
      html += "</div>";
      if (count == 4) {
        html += "</div>";
        count = 0;
      } else {
        count++;
      }
    });
    $('#top-10-pokemon-iv100').html(html);

	var obj = JSON.parse(data);
    var html = "";
    var count = 0;
    $.each(obj.top10_pokemon_lifetime, function(key, value) {
      if (count == 0) {
        html += "<div class='row justify-content-center'>";
      }
      var name = pokedex[value.pokemon_id];
      var pkmnImage = sprintf("<?=$config['urls']['images']['pokemon']?>", value.pokemon_id);
      html += "<div class='col-md-2" + (count == 0 ? " col-md-offset-1" : "") + "'>";
      html += "<img src='" + pkmnImage + "' width='48' height='48'><span class='text-nowrap'>" + name + ": " + numberWithCommas(value.count) + "</span></br>";
      html += "</div>";
      if (count == 4) {
        html += "</div>";
        count = 0;
      } else {
        count++;
      }
    });
    $('#top-10-pokemon-lifetime').html(html);
  });
}

/*
var tmp = createToken();
sendRequest({ "type": "dashboard", "token": tmp }, function(data, status) {
  tmp = null;
  if (debug) {
    if (data === 0) {
      console.log("Failed to get data for dashboard.");
      return;
    } else {
      console.log("Dashboard:", data);
    }
  }
  var obj = JSON.parse(data);
  updateCounter(".pokemon-count", obj.active_pokemon);
  updateCounter(".gym-count", obj.gyms);
  updateCounter(".raids-count", obj.raids);
  updateCounter(".total-pokemon-count", obj.pokemon);
  updateCounter(".total-iv-count", obj.iv_total);
  updateCounter(".active-iv-count", obj.iv_active);
  updateCounter(".neutral-gyms-count", obj.neutral);
  updateCounter(".valor-gyms-count", obj.valor);
  updateCounter(".mystic-gyms-count", obj.mystic);
  updateCounter(".instinct-gyms-count", obj.instinct);
  updateCounter(".pokestop-count", obj.pokestops);
  updateCounter(".lured-pokestop-count", obj.lured);
  updateCounter(".quest-pokestop-count", obj.quests);
  updateCounter(".spawnpoint-count", obj.tth_total);
  updateCounter(".found-spawnpoint-count", obj.tth_found);
  updateCounter(".missing-spawnpoint-count", obj.tth_missing);
  updateCounter(".percentage-spawnpoint-count", obj.tth_percentage);

  var html = "";
  var count = 0;
  $.each(obj.top10_pokemon, function(key, value) {
    if (count == 0) {
      html += "<div class='row justify-content-center'>";
    }
    var name = pokedex[value.pokemon_id];
    var pkmnImage = sprintf("<?=$config['urls']['images']['pokemon']?>", value.pokemon_id);
    html += "<div class='col-md-2" + (count == 0 ? " col-md-offset-1" : "") + "'>";
    html += "<img src='" + pkmnImage + "' width='48' height='48'><span class='text-nowrap'>" + name + ": " + numberWithCommas(value.count) + "</span></br>";
    html += "</div>";
    if (count == 4) {
      html += "</div>";
      count = 0;
    } else {
      count++;
    }
  });
  $('#top-10-pokemon').html(html);
});
*/

function createToken() {
  //TODO: Secure
  <?php $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(16)); ?>
  return "<?php echo $_SESSION['token']; ?>";
}

  //first tab should be auto-open
  document.getElementById('Top10MonTod').style.display = "block";

</script>
