<?php
$maxDevices = get_devices_count();
$maxDevicesQuests = get_quest_devices_count($config['ui']['pages']['dashboard']['QuestInstance']);

$onlineDevices = get_online_devices($config['ui']['pages']['dashboard']['deviceResponseLimit']);
$onlineDevicesQuests = get_online_quest_devices(($config['ui']['pages']['dashboard']['QuestInstance']),($config['ui']['pages']['dashboard']['deviceResponseLimit']));
$percentOnline = (($onlineDevices/$maxDevices) *100);
$percentOnlineQuests = 0;
if ($onlineDevicesQuests > 0 && $maxDevicesQuests > 0){
(($onlineDevicesQuests/$maxDevicesQuests)*100);
}

$html = "
<div style='max-width:1280px;margin: 0 auto !important;float: none !important;'>

<h2 class='page-header text-center " . $config['ui']['style'] . "'>Übersicht</h2>
<div class='card page-text p-1 m-3 " . $config['ui']['style'] . "'>
Willkommen auf unserer Seite!<br>
Finde Monster ,Raids, Stops ,Arenen, Monster mit IV, Quests und vieles mehr!<br>
Auf dieser Seite findest du eine Übersicht darüber, welche Daten auf unserer Map zu finden sind.
</div>
<div class='card text-center ".$config['ui']['style']." p-1 m-3 " . $config['ui']['style'] . "'>
	<div class='card-header heading text-light'><b>Allgemein</b></div>
	<div class='card-body'>
		<div class='container'>
			<div class='row'>
				<div class='col-md-4'>
					<div class='list-group'>
						<a class='list-group-item ".$config['ui']['style']."'>
							<h3 class='pull-right'><img src='./static/images/encounter.png' width='48' height='48'/></h3>
							<h4 class='list-group-item-heading ".$config['ui']['style']." pokemon-count'>0</h4>
							<p class='list-group-item-text ".$config['ui']['style']."'>Aktive Monster</p>
						</a>
					</div>
				</div>
				<div class='col-md-4'>
					<div class='list-group'>
						<a class='list-group-item " . $config['ui']['style'] . "'>
							<h3 class='pull-right'><img src='./static/images/iv.png' width='48' height='48'/></h3>
							<h4 class='list-group-item-heading ".$config['ui']['style']." active-iv-count'>0</h4>
							<p class='list-group-item-text ".$config['ui']['style']."'>Monster mit IV</p>
						</a>
					</div>
				</div>
				<div class='col-md-4'>
					<div class='list-group'>
						<a class='list-group-item " . $config['ui']['style'] . "'>
							<h3 class='pull-right'><img src='./static/egg-icons/raids_hatched.png' width='auto' height='48'/></h3>
							<h4 class='list-group-item-heading ".$config['ui']['style']." raids-count'>0</h4>
							<p class='list-group-item-text ".$config['ui']['style']."'>Aktive Raids & Eier</p>
						</a>
					</div>
				</div>
				<div class='col-md-4'>
					<div class='list-group'>
						<a class='list-group-item " . $config['ui']['style'] . "'>
							<h3 class='pull-right'><img src='./static/images/encounter.png' width='48' height='48'/></h3>
							<h4 class='list-group-item-heading ".$config['ui']['style']." total-pokemon-count'>0</h4>
							<p class='list-group-item-text ".$config['ui']['style']."'>Monster heute Gesamt</p>
						</a>
					</div>
				</div>

				<div class='col-md-4'>
					<div class='list-group'>
						<a class='list-group-item " . $config['ui']['style'] . "'>
							<h3 class='pull-right'><img src='./static/images/iv.png' width='48' height='48'/></h3>
							<h4 class='list-group-item-heading ".$config['ui']['style']." total-iv-count'>0</h4>
							<p class='list-group-item-text ".$config['ui']['style']."'>IV Monster heute Gesamt</p>
						</a>
					</div>
				</div>
				<div class='col-md-4'>
					<div class='list-group'>
						<a class='list-group-item " . $config['ui']['style'] . "'>
							<h3 class='pull-right'><img src='./static/images/teams/teams.png' width='auto' height='48'/></h3>
							<h4 class='list-group-item-heading ".$config['ui']['style']." gym-count'>0</h4>
							<p class='list-group-item-text ".$config['ui']['style']."'>Arenen Gesamt</p>
						</a>
					</div>
				</div>
			</div>
		<div>
	</div>
</div>
</div>
</div>


<div class='card text-center ".$config['ui']['style']." p-1 m-3'>
	<div class='tab'>
		<button class='tablinks heading text-light active' onclick='openCity(event,\"Top10MonTod\")'><b>Top 10 Heute</b></button>
		<button class='tablinks heading text-light' onclick='openCity(event,\"Top10MonLife\")'><b>Top 10 Gesamt</b></button>
		<button class='tablinks heading text-light' onclick='openCity(event,\"Top10MonIV\")'><b>Top 10 IV Heute</b></button>
		<button class='tablinks heading text-light' onclick='openCity(event,\"Top10MonIV95\")'><b>96+ Heute</b></button>
		<button class='tablinks heading text-light' onclick='openCity(event,\"Top10MonIV100\")'><b>100er Heute</b></button>";
		if (!empty($config['ui']['pages']['dashboard']['shinyStatsToday']) && $config['ui']['pages']['dashboard']['shinyStatsToday'] !== false) {
			$html .="
			<button class='tablinks heading text-light' onclick='openCity(event,\"TopShinys\")'><b>Top Shinys Heute</b></button>";
		}
		if (!empty ($config['ui']['pages']['dashboard']['shinyStatsAlltime']) && $config['ui']['pages']['dashboard']['shinyStatsAlltime'] !== false) {
			$html .="
			<button class='tablinks heading text-light' onclick='openCity(event,\"ShinysTotal\")'><b>Top Shinys Gesamt</b></button>";
		}
		

	$html .="
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
	</div>";
	if (!empty($config['ui']['pages']['dashboard']['shinyStatsToday']) && $config['ui']['pages']['dashboard']['shinyStatsToday'] !== false) {
		$html .="
		<div id='TopShinys' class='tabcontent'>
			<div class='container'>
				<div id='shiny-rates' class=''></div>
			</div>
		</div>";
	}
	if (!empty($config['ui']['pages']['dashboard']['shinyStatsAlltime']) && $config['ui']['pages']['dashboard']['shinyStatsAlltime'] !== false) {
		$html .="
		<div id='ShinysTotal' class='tabcontent'>
	
			<div class='container ".$config['ui']['style']."'>
			<center>
				<div style='max-width:850px;padding-bottom:10px;text-align:left;'>
					Bei dieser Statistik fließen nur Daten von Tagen ein, bei denen von der jeweiligen Spezies mindestens ein Shiny gefunden wurde. Dies verringert die Ungenauigkeit durch neuere Shiny-Releases<br>
					(Bei einer zu geringeren gefunden Shiny-Anzahl kann die Rate sehr von der tatsächlichen Shiny-Rate abweichen)
				</div>
			</center>
				<div id='shiny-rates-total' class=''></div>
			</div>
		</div>";
	}
	
	$html .="
	<div id='Top10MonIV95' class='tabcontent'>
		<div class='container'>
			<div id='top-10-pokemon-iv95' class='row justify-content-center'></div>
		</div>
		<div class='row justify-content-around'>
				<center>
					<a class='list-group-item " . $config['ui']['style'] . "' style='width:200px;'>
						<h3 class='pull-right'><img src='./static/images/iv.png' width='48' height='48'/></h3>
						<h4 class='list-group-item-heading ".$config['ui']['style']." total-iv95'>0</h4>
						<p class='list-group-item-text ".$config['ui']['style']."'>96+ Heute</p>
					</a>
				</center>
				<center>
					<a class='list-group-item " . $config['ui']['style'] . "' style='width:200px;'>
						<h3 class='pull-right'><img src='./static/images/iv.png' width='48' height='48'/></h3>
						<h4 class='list-group-item-heading ".$config['ui']['style']." active-iv95'>0</h4>
						<p class='list-group-item-text ".$config['ui']['style']."'>96+ Live</p>
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
					<a class='list-group-item " . $config['ui']['style'] . "' style='width:200px;'>
						<h3 class='pull-right'><img src='./static/images/iv.png' width='48' height='48'/></h3>
						<h4 class='list-group-item-heading ".$config['ui']['style']." total-iv100'>0</h4>
						<p class='list-group-item-text ".$config['ui']['style']."'>100er Heute</p>
					</a>
				</center>
				<center>
					<a class='list-group-item " . $config['ui']['style'] . "' style='width:200px;'>
						<h3 class='pull-right'><img src='./static/images/iv.png' width='48' height='48'/></h3>
						<h4 class='list-group-item-heading ".$config['ui']['style']." active-iv100'>0</h4>
						<p class='list-group-item-text ".$config['ui']['style']."'>100er Live</p>
					</a>
				</center>
		</div>
	</div>
	
</div>

<div class='card text-center ".$config['ui']['style']." p-1 m-3'>
  <div class='card-header heading text-light'><b>Teams (aktive Arenen)</b></div>
  <div class='card-body'>
    <div class='container'>
      <div class='row'>
        <div class='col-md-3'>
          <div class='list-group'>
            <a class='list-group-item ".$config['ui']['style']." neutral'>
              <h3 class='pull-right'><img src='./static/images/teams/neutral.png' width='64' height='64'/></h3>
              <h4 class='list-group-item-heading ".$config['ui']['style']." gym-white-active'>0</h4>
              <p class='list-group-item-text ".$config['ui']['style']."'>Neutral (<span class='gym-white-active-percent'>0</span>%) </p>
            </a>
          </div>
        </div>
        <div class='col-md-3'>
          <div class='list-group'>
            <a class='list-group-item ".$config['ui']['style']." valor'>
              <h3 class='pull-right'><img src='./static/images/teams/valor.png' width='64' height='64'/></h3>
              <h4 class='list-group-item-heading ".$config['ui']['style']." gym-red-active'>0</h4>
              <p class='list-group-item-text ".$config['ui']['style']."'>Valor (<span class='gym-red-active-percent'>0</span>%) </p>
            </a>
          </div>
        </div>
        <div class='col-md-3'>
          <div class='list-group'>
            <a class='list-group-item ".$config['ui']['style']." mystic'>
              <h3 class='pull-right'><img src='./static/images/teams/mystic.png' width='64' height='64'/></h3>
              <h4 class='list-group-item-heading ".$config['ui']['style']." gym-blue-active'>0</h4>
              <p class='list-group-item-text ".$config['ui']['style']."'>Mystic (<span class='gym-blue-active-percent'>0</span>%) </p>
            </a>
          </div>
        </div>
        <div class='col-md-3'>
          <div class='list-group'>
            <a class='list-group-item ".$config['ui']['style']." instinct'>
              <h3 class='pull-right'><img src='./static/images/teams/instinct.png' width='64' height='64'/></h3>
              <h4 class='list-group-item-heading ".$config['ui']['style']." gym-yellow-active'>0</h4>
              <p class='list-group-item-text ".$config['ui']['style']."'>Instinct (<span class='gym-yellow-active-percent'>0</span>%) </p>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class='card text-center ".$config['ui']['style']." p-1 m-3'>
  <div class='card-header heading text-light'><b data-i18n='dashboard_pokestops_header'>Stops</b></div>
  <div class='card-body'>
    <div class='container'>
      <div class='row'>
        <div class='col-md-3'>
          <a class='list-group-item " . $config['ui']['style'] . "'>
            <h3 class='pull-right'><img src='./static/images/stop.png' width='48' height='48'/></h3>
            <h4 class='list-group-item-heading ".$config['ui']['style']." pokestop-count'>0</h4>
            <p class='list-group-item-text ".$config['ui']['style']."' data-i18n='dashboard_pokestops_total'>Stops</p>
          </a>
        </div>
        <div class='col-md-3'>
          <a class='list-group-item " . $config['ui']['style'] . "'>
            <h3 class='pull-right'><img src='./static/images/rocket-invasion.png' width='48' height='48'/></h3>
            <h4 class='list-group-item-heading ".$config['ui']['style']." invasions-pokestop-count'>0</h4>
            <p class='list-group-item-text ".$config['ui']['style']."' data-i18n='dashboard_pokestops_invasions'>Rocket Invasionen</p>
          </a>
        </div>
        <div class='col-md-3'>
          <a class='list-group-item " . $config['ui']['style'] . "'>
            <h3 class='pull-right'><img src='./static/images/module.png' width='48' height='48'/></h3>
            <h4 class='list-group-item-heading ".$config['ui']['style']." lured-pokestop-count'>0</h4>
            <p class='list-group-item-text ".$config['ui']['style']."' data-i18n='dashboard_pokestops_lured'>Aktive Module</p>
          </a>
        </div>
        <div class='col-md-3'>
          <a class='list-group-item " . $config['ui']['style'] . "'>
            <h3 class='pull-right'><img src='./static/images/quests/0.png' width='48' height='48'/></h3>
            <h4 class='list-group-item-heading ".$config['ui']['style']." quest-pokestop-count'>0</h4>
            <p class='list-group-item-text ".$config['ui']['style']."' data-i18n='dashboard_pokestops_quests'>Quests</p>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>";

// Show this Raid Stats only from 4:00 to 23:00
if (Date('H') >= 4 && Date('H') < 23) {
$html .="
<div class='card text-center ".$config['ui']['style']." p-1 m-3'>
  <div class='card-header heading text-light'><b>Raids & Eier</b></div>
  <div class='card-body'>
    <div class='container'>
      <div class='row'>
        <div class='col-md-4'>
          <a class='list-group-item " . $config['ui']['style'] . "'>
            <h3 class='pull-right'><img src='./static/egg-icons/eggs.png' width='auto' height='64'/></h3>
            <h4 class='list-group-item-heading ".$config['ui']['style']." egg-total-count'>0</h4>
            <p class='list-group-item-text ".$config['ui']['style']."'>Raideier Gesamt</p>
          </a>
        </div>
        <div class='col-md-4'>
          <a class='list-group-item " . $config['ui']['style'] . "'>
            <h3 class='pull-right'><img src='./static/egg-icons/eggs_normal.png' width='auto' height='64'/></h3>
            <h4 class='list-group-item-heading ".$config['ui']['style']." egg-normal-count'>0</h4>
            <p class='list-group-item-text ".$config['ui']['style']."'>Normale Raideier</p>
          </a>
        </div>
        <div class='col-md-4'>
          <a class='list-group-item " . $config['ui']['style'] . "'>
            <h3 class='pull-right'><img src='./static/egg-icons/5.png' width='auto' height='64'/></h3>
            <h4 class='list-group-item-heading ".$config['ui']['style']." egg-legendary-count'>0</h4>
            <p class='list-group-item-text ".$config['ui']['style']."'>Legendäre Raideier</p>
          </a>
        </div>
        <div class='col-md-4'>
          <a class='list-group-item " . $config['ui']['style'] . "'>
            <h3 class='pull-right'><img src='./static/egg-icons/raids_hatched.png' width='auto' height='64'/></h3>
            <h4 class='list-group-item-heading ".$config['ui']['style']." raids-hatched-total'>0</h4>
            <p class='list-group-item-text ".$config['ui']['style']."'>Raids Gesamt</p>
          </a>
        </div>
        <div class='col-md-4'>
          <a class='list-group-item " . $config['ui']['style'] . "'>
            <h3 class='pull-right'><img src='./static/egg-icons/raids_hatched_normal.png' width='auto' height='64'/></h3>
            <h4 class='list-group-item-heading ".$config['ui']['style']." raids-hatched-normal'>0</h4>
            <p class='list-group-item-text ".$config['ui']['style']."'>Normale Raids</p>
          </a>
        </div>
        <div class='col-md-4'>
          <a class='list-group-item " . $config['ui']['style'] . "'>
            <h3 class='pull-right'><img src='./static/egg-icons/5-open.png' width='auto' height='64'/></h3>
            <h4 class='list-group-item-heading ".$config['ui']['style']." raids-hatched-legendary'>0</h4>
            <p class='list-group-item-text ".$config['ui']['style']."'>Legendäre Raids</p>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>";
}
if (isset($config['ui']['pages']['dashboard']['enableNewStopsGyms']) && $config['ui']['pages']['dashboard']['enableNewStopsGyms'] !== false) {
$html .= "
<div class='card text-center ".$config['ui']['style']." p-1 m-3'>
	<div class='tab'>
		<button class='tablinks heading text-light active' onclick='openNew(event,\"NewStops\")'><b>Neue Stops</b><img src='./static/images/stop.png' width='28' height='28' class='ml-1'/></button>
		<button class='tablinks heading text-light' onclick='openNew(event,\"NewGyms\")'><b>Neue Arenen</b><img src='./static/images/swords.png' width='28' height='28' class='ml-1'/></button>
	</div>
	
	<div id='NewStops' class='tabcontent'>
		<div class='container'>
			<div id='new-pokestops' class='row justify-content-center active' ></div>
		</div>
	</div>
	
	<div id='NewGyms' class='tabcontent'>
		<div class='container'>
			<div id='new-gyms' class='row justify-content-center'></div>
		</div>
	</div>
	
</div>";
}

$html .= "
<div class='card text-center ".$config['ui']['style']." p-1 m-3'>
  <div class='card-header heading text-light'><b>Aktives Wetter</b></div>
  <div class='card-body'>
    <div class='container'>
      <div class='row'>
        <div class='col-md-3'>
          <div class='list-group'>
            <a class='list-group-item " . $config['ui']['style'] . "'>
              <h3 class='pull-right'><img src='./static/images/weather/i-0.png' width='64' height='64'/></h3>
              <h4 class='list-group-item-heading ".$config['ui']['style']." weather-pokemon-noBoost'>0</h4>
              <p class='list-group-item-text ".$config['ui']['style']."'>Ohne Boost</p>
            </a>
          </div>
        </div>
        <div class='col-md-3'>
          <div class='list-group'>
            <a class='list-group-item " . $config['ui']['style'] . "'>
              <h3 class='pull-right'><img src='./static/images/weather/i-1.png' width='64' height='64'/></h3>
              <h4 class='list-group-item-heading ".$config['ui']['style']." weather-pokemon-sunny'>0</h4>
              <p class='list-group-item-text ".$config['ui']['style']."'>Sonnig</p>
            </a>
          </div>
        </div>
        <div class='col-md-3'>
          <div class='list-group'>
            <a class='list-group-item " . $config['ui']['style'] . "'>
              <h3 class='pull-right'><img src='./static/images/weather/i-2.png' width='64' height='64'/></h3>
              <h4 class='list-group-item-heading ".$config['ui']['style']." weather-pokemon-rain'>0</h4>
              <p class='list-group-item-text ".$config['ui']['style']."'>Regen</p>
            </a>
          </div>
        </div>
        <div class='col-md-3'>
          <div class='list-group'>
            <a class='list-group-item " . $config['ui']['style'] . "'>
              <h3 class='pull-right'><img src='./static/images/weather/i-3.png' width='64' height='64'/></h3>
              <h4 class='list-group-item-heading ".$config['ui']['style']." weather-pokemon-partlyCloudy'>0</h4>
              <p class='list-group-item-text ".$config['ui']['style']."'>Teilw. Bewölkt</p>
            </a>
          </div>
        </div>
        <div class='col-md-3'>
          <div class='list-group'>
            <a class='list-group-item " . $config['ui']['style'] . "'>
              <h3 class='pull-right'><img src='./static/images/weather/i-4.png' width='64' height='64'/></h3>
              <h4 class='list-group-item-heading ".$config['ui']['style']." weather-pokemon-cloudy'>0</h4>
              <p class='list-group-item-text ".$config['ui']['style']."'>Bewölkt</p>
            </a>
          </div>
        </div>
        <div class='col-md-3'>
          <div class='list-group'>
            <a class='list-group-item " . $config['ui']['style'] . "'>
              <h3 class='pull-right'><img src='./static/images/weather/i-5.png' width='64' height='64'/></h3>
              <h4 class='list-group-item-heading ".$config['ui']['style']." weather-pokemon-windy'>0</h4>
              <p class='list-group-item-text ".$config['ui']['style']."'>Wind</p>
            </a>
          </div>
        </div>
        <div class='col-md-3'>
          <div class='list-group'>
            <a class='list-group-item " . $config['ui']['style'] . "'>
              <h3 class='pull-right'><img src='./static/images/weather/i-6.png' width='64' height='64'/></h3>
              <h4 class='list-group-item-heading ".$config['ui']['style']." weather-pokemon-snow'>0</h4>
              <p class='list-group-item-text ".$config['ui']['style']."'>Schnee</p>
            </a>
          </div>
        </div>
        <div class='col-md-3'>
          <div class='list-group'>
            <a class='list-group-item " . $config['ui']['style'] . "'>
              <h3 class='pull-right'><img src='./static/images/weather/i-7.png' width='64' height='64'/></h3>
              <h4 class='list-group-item-heading ".$config['ui']['style']." weather-pokemon-fog'>0</h4>
              <p class='list-group-item-text ".$config['ui']['style']."'>Nebel</p>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<div class='card text-center ".$config['ui']['style']." p-1 m-3'>
  <div class='card-header heading text-light'><b>Spawnpunkte</b></div>
  <div class='card-body'>
    <div class='container'>
      <div class='row'>
        <div class='col-md-3'>
          <a class='list-group-item " . $config['ui']['style'] . "'>
            <h3 class='pull-right'><img src='./static/images/spawns.png' width='64' height='64'/></h3>
            <h4 class='list-group-item-heading ".$config['ui']['style']." spawnpoint-count'>0</h4>
            <p class='list-group-item-text ".$config['ui']['style']."'>Spawnpunkte Gesamt</p>
          </a>
        </div>
        <div class='col-md-3'>
          <a class='list-group-item " . $config['ui']['style'] . "'>
            <h3 class='pull-right'><img src='./static/images/spawns_verified.png' width='64' height='64'/></h3>
            <h4 class='list-group-item-heading ".$config['ui']['style']." found-spawnpoint-count'>0</h4>
            <p class='list-group-item-text ".$config['ui']['style']."'>Verifizierte Despawnzeiten</p>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>";

if (($config['ui']['pages']['dashboard']['deviceStatus'] !== false) || ($config['ui']['pages']['dashboard']['deviceStatusQuests'] !== false)) {
	$html .="
	<div class='card text-center ".$config['ui']['style']." p-1 m-3'>
		<div class='card-header heading text-light'><b>Status</b></div>
		<div class='card-body'>
			<div class='container'>
			      <div class='row'>";
}
if ($config['ui']['pages']['dashboard']['deviceStatus'] !== false) {
	$html .="
					<div class='col-md-4'>
						<a class='list-group-item " . $config['ui']['style'] . "'>";
            
							if($onlineDevices >= $maxDevices){
							$html .="
									<h3 class='pull-right'><img src='./static/images/online.png' width='48' height='48'/></h3>
									<h4 class='list-group-item-heading ".$config['ui']['style']."'>Aktiv</h4>";
							}else if($onlineDevices > 0 && $onlineDevices < $maxDevices){
							$html .="
									<h3 class='pull-right'><img src='./static/images/unstable.png' width='48' height='48'/></h3>
									<h4 class='list-group-item-heading ".$config['ui']['style']."'>Unstable (". round($percentOnline) ."%)</h4>";
							}else{
							$html .="
									<h3 class='pull-right'><img src='./static/images/offline.png' width='48' height='48'/></h3>
									<h4 class='list-group-item-heading ".$config['ui']['style']."'>Inaktiv</h4>";
							}
							$html .="
							<p class='list-group-item-text ".$config['ui']['style']."'>Scan</p>
						</a>
					</div>";
}
if ($config['ui']['pages']['dashboard']['deviceStatusQuests'] !== false) {
	$html .="
					<div class='col-md-4'>
					<a class='list-group-item " . $config['ui']['style'] . "'>";
						
						if($maxDevicesQuests > 0 && $onlineDevicesQuests == $maxDevicesQuests ){
						$html .="
								<h3 class='pull-right'><img src='./static/images/online.png' width='48' height='48'/></h3>
								<h4 class='list-group-item-heading ".$config['ui']['style']."'>Aktiv</h4>";
						}else if($onlineDevicesQuests > 0 && $onlineDevicesQuests < $maxDevicesQuests){
						$html .="
								<h3 class='pull-right'><img src='./static/images/unstable.png' width='48' height='48'/></h3>
								<h4 class='list-group-item-heading ".$config['ui']['style']."'>Unstable (". round($percentOnlineQuests) ."%)</h4>";
						}else{
						$html .="
								<h3 class='pull-right'><img src='./static/images/offline.png' width='48' height='48'/></h3>
								<h4 class='list-group-item-heading ".$config['ui']['style']."'>Inaktiv</h4>";
						}
						$html .="
						<p class='list-group-item-text ".$config['ui']['style']."'>Questscan</p>
					</a>
					</div>";
}
if (($config['ui']['pages']['dashboard']['deviceStatus'] !== false) || ($config['ui']['pages']['dashboard']['deviceStatusQuests'] !== false)) {
	$html .="
				</div>
			</div>
		</div>
	</div>";
}
$html .="

<div class ='p-1 m-2'>
<div class='row'>";
if (!empty($config['urls']['map'])) {
$html .="<div class='card text-center ".$config['ui']['style']." p-1 m-2 col-md-3'>
	<div class='card-header heading text-light'><b>Map</b></div>
	<div class='card-body'>
		<div class='container " . $config['ui']['style'] . "'>
          <a href='". $config['urls']['map'] ."' class='link'>
			<center>
			<img src='./static/images/map.png' width='128' height='auto'/></h3>
            <p> > Zur Map < </p>
			</center>
          </a>
		</div>
	</div>
</div>";
}
if (!empty($config['discord']['inviteLink'])) {
$html .="<div class='card text-center ".$config['ui']['style']." p-1 m-2 col-md-3'>
	<div class='card-header heading text-light'><b>Discord</b></div>
	<div class='card-body'>
		<div class='container " . $config['ui']['style'] . "'>
          <a href='". $config['discord']['inviteLink'] ."' class='link'>
			<center>
			<img src='./static/images/discord.png' width='128' height='auto'/></h3>
            <p> > Zum Discord Server < </p>
			</center>
          </a>
		</div>
	</div>
</div>";
}
if (!empty($config['urls']['patreon'])) {
$html .="<div class='card text-center ".$config['ui']['style']." p-1 m-2 col-md-3'>
	<div class='card-header heading text-light'><b>Patreon</b></div>
	<div class='card-body'>
		<div class='container " . $config['ui']['style'] . "'>
          <a href='". $config['urls']['patreon'] ."' class='link'>
			<center>
			<img src='./static/images/patreon.png' width='128' height='auto'/></h3>
            <p> > Zu Patreon (Spenden) < </p>
			</center>
          </a>
		</div>
	</div>
</div>";
}
if (!empty($config['urls']['paypal'])) {
$html .="<div class='card text-center ".$config['ui']['style']." p-1 m-2 col-md-3'>
	<div class='card-header heading text-light'><b>Paypal</b></div>
	<div class='card-body'>
		<div class='container " . $config['ui']['style'] . "'>
          <a href='". $config['urls']['paypal'] ."' class='link'>
			<center>
			<img src='./static/images/paypal.png' width='128' height='auto'/></h3>
            <p> > Zu Paypal (Spenden) < </p>
			</center>
          </a>
		</div>
	</div>
</div>";
}
if (!empty($config['urls']['venmo'])) {
$html .="<div class='card text-center ".$config['ui']['style']." p-1 m-2 col-md-3'>
	<div class='card-header heading text-light'><b>Venmo</b></div>
	<div class='card-body'>
		<div class='container " . $config['ui']['style'] . "'>
          <a href='". $config['urls']['venmo'] ."' class='link'>
			<center>
			<img src='./static/images/venmo.png' width='128' height='auto'/></h3>
            <p> > Zu Venmo (Spenden) < </p>
			</center>
          </a>
		</div>
	</div>
</div>";
}

$html .="</div>";
echo $html;
?>
<link rel="stylesheet" href="./static/css/dashboard.css"/>
<link rel="stylesheet" href="./static/css/footerfix.css"/>
<script type="text/javascript" src="./static/js/pokedex.js"></script>
<script type="text/javascript" src="./static/js/utils.js"></script>
<script type="text/javascript" src="static/js/dashboard.js"></script>
<script type="text/javascript">
var debug = <?=$config['core']['showDebug'] !== false ? '1' : '0'?>;
var newPokestopsGyms = <?=(!empty($config['ui']['pages']['dashboard']['enableNewStopsGyms']) && $config['ui']['pages']['dashboard']['enableNewStopsGyms'] !== false) ? '1' : '0'?>;
var shinyStats = <?=(!empty($config['ui']['pages']['dashboard']['shinyStatsToday']) && $config['ui']['pages']['dashboard']['shinyStatsToday']) !== false ? '1' : '0'?>;
var shinyStatsAlltime = <?= (!empty($config['ui']['pages']['dashboard']['shinyStatsAlltime']) && $config['ui']['pages']['dashboard']['shinyStatsAlltime'] !== false) ? '1' : '0'?>;
var shinyStatsAlltimeMode = <?= (!empty($config['ui']['pages']['dashboard']['shinyStatsAlltimeMode']) && $config['ui']['pages']['dashboard']['shinyStatsAlltimeMode'] !== false) ? '1' : '0'?>;
var deviceStats = <?=(!empty($config['ui']['pages']['dashboard']['deviceStatus']) && $config['ui']['pages']['dashboard']['deviceStatus']) !== false ? '1' : '0'?>;
var deviceStatsQuests = <?=(!empty($config['ui']['pages']['dashboard']['deviceStatusQuests']) && $config['ui']['pages']['dashboard']['deviceStatusQuests']) !== false ? '1' : '0'?>;
var timeout = <?=!empty($config['ui']['pages']['dashboard']['deviceResponseLimit']) ? $config['ui']['pages']['dashboard']['deviceResponseLimit'] : 600 ?>;

var maxDevices = <?=$maxDevices?>;
var maxDevicesQuests = <?=$maxDevicesQuests?>;
var onlineDevices = <?=$onlineDevices?>;
var Questing = <?=$onlineDevicesQuests?>;
var percentOnline = <?=$percentOnline?>;

var percentOnlineQuests = <?=$percentOnlineQuests?>;

if(debug){
	console.log('deviceStats: '+ deviceStats + ', deviceStatsQuests: ' + deviceStatsQuests + ', timeout: '+ timeout);
	console.log('Devices: '+ onlineDevices + '/' + maxDevices + '(' + percentOnline + '%) -- QuestDevices: ' + Questing + '/' + maxDevicesQuests + '(' + percentOnlineQuests + '%)');
}
  
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

  var startTimeTemp = new Date();
  tmp = createToken();
  sendRequest({ "type": "dashboard", "stat": "gyms", "token": tmp }, function(data, status) {
    tmp = null;
	var endTimeTemp = new Date();
	console.log('Elapsed: ' + (endTimeTemp - startTimeTemp) + 'ms')
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
    updateCounter(".gym-white-active-percent", obj.neutralActivePercent);
    updateCounter(".gym-blue-active-percent", obj.mysticActivePercent);
    updateCounter(".gym-red-active-percent", obj.valorActivePercent);
    updateCounter(".gym-yellow-active-percent", obj.instinctActivePercent);
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
    updateCounter(".invasions-pokestop-count", obj.invasions);
  });
  
  tmp = createToken();
  sendRequest({ "type": "dashboard", "stat": "weather", "token": tmp }, function(data, status) {
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
    updateCounter(".weather-pokemon-total", obj.total);
    updateCounter(".weather-pokemon-noBoost", obj.noBoost);
    updateCounter(".weather-pokemon-clear", obj.clear);
    updateCounter(".weather-pokemon-rain", obj.rain);
    updateCounter(".weather-pokemon-partlyCloudy", obj.partlyCloudy);
    updateCounter(".weather-pokemon-cloudy", obj.cloudy);
    updateCounter(".weather-pokemon-windy", obj.windy);
    updateCounter(".weather-pokemon-snow", obj.snow);
    updateCounter(".weather-pokemon-fog", obj.fog);
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
	  if(value.form !== '0'){
			pkmnImage = pkmnImage.toString().replace("00.png", value.form + ".png");
	  }
      html += "<div class='col-md-2" + (count == 0 ? " col-md-offset-1" : "") + "'>";
      html += "<img src='" + pkmnImage + "' width='48' height='48'><span class='text-nowrap <?php echo $config['ui']['style']?>'>" + name + ": " + numberWithCommas(value.count) + "</span></br>";
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
	  if(value.form !== '0'){
			pkmnImage = pkmnImage.toString().replace("00.png", value.form + ".png");
	  }
      html += "<div class='col-md-2" + (count == 0 ? " col-md-offset-1" : "") + "'>";
      html += "<img src='" + pkmnImage + "' width='48' height='48'><span class='text-nowrap <?php echo $config['ui']['style']?>'>" + name + ": " + numberWithCommas(value.count) + "</span></br>";
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
	  if(value.form !== '0'){
			pkmnImage = pkmnImage.toString().replace("00.png", value.form + ".png");
	  }
      html += "<div class='col-md-2" + (count == 0 ? " col-md-offset-1" : "") + "'>";
      html += "<img src='" + pkmnImage + "' width='48' height='48'><span class='text-nowrap <?php echo $config['ui']['style']?>'>" + name + ": " + numberWithCommas(value.count) + "</span></br>";
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
	  if(value.form !== '0'){
			pkmnImage = pkmnImage.toString().replace("00.png", value.form + ".png");
	  }
      html += "<div class='col-md-2" + (count == 0 ? " col-md-offset-1" : "") + "'>";
      html += "<img src='" + pkmnImage + "' width='48' height='48'><span class='text-nowrap <?php echo $config['ui']['style']?>'>" + name + ": " + numberWithCommas(value.count) + "</span></br>";
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
      html += "<img src='" + pkmnImage + "' width='48' height='48'><span class='text-nowrap <?php echo $config['ui']['style']?>'>" + name + ": " + numberWithCommas(value.count) + "</span></br>";
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
  
  if(shinyStats){
  
	tmp = createToken();
	if(shinyStatsAlltimeMode){
		sendRequest({ "type": "dashboard", "stat": "shinyTodayMode", "token": tmp }, function(data, status) {
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
			$.each(obj.shiny_rates, function(key, value) {
			if (count == 0) {
				html += "<div class='row justify-content-center'>";
			}
			var name = pokedex[value.pokeid];
			var pkmnImage = sprintf("<?=$config['urls']['images']['pokemon']?>", value.pokeid);
			if(value.pokeform !== '0'){
				pkmnImage = pkmnImage.toString().replace("00.png", value.pokeform + ".png");
			}
			html += "<div class='col-md-2" + (count == 0 ? " col-md-offset-1" : "") + "'>";
			html += "<img src='" + pkmnImage + "' width='64' height='64'><p><span class='text-nowrap <?php echo $config['ui']['style']?>'>" + name + ": " + numberWithCommas(value.count) + "</span>";
			html += "</div>";
			if (count == 4) {
				html += "</div>";
				count = 0;
			} else {
				count++;
			}
			});
			$('#shiny-rates').html(html);
		});
	}else {
		sendRequest({ "type": "dashboard", "stat": "shinyToday", "token": tmp }, function(data, status) {
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
			$.each(obj.shiny_rates, function(key, value) {
			if (count == 0) {
				html += "<div class='row justify-content-center'>";
			}
			var name = pokedex[value.pokeid];
			var pkmnImage = sprintf("<?=$config['urls']['images']['pokemon']?>", value.pokeid);
			html += "<div class='col-md-2" + (count == 0 ? " col-md-offset-1" : "") + "'>";
			html += "<img src='" + pkmnImage + "' width='64' height='64'><p><span class='text-nowrap <?php echo $config['ui']['style']?>'>" + name + ": " + numberWithCommas(value.count) + "</span>";
			html += "</div>";
			if (count == 4) {
				html += "</div>";
				count = 0;
			} else {
				count++;
			}
			});
			$('#shiny-rates').html(html);
		});
	}
  }
  
  if(shinyStatsAlltime){
	tmp = createToken();
	if (shinyStatsAlltimeMode){
		sendRequest({ "type": "dashboard", "stat": "shinyAlltimeCustom", "token": tmp }, function(data, status) {
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
			$.each(obj.shiny_rates_total_custom, function(key, value) {
			if (count == 0) {
				html += "<div class='row justify-content-center'>";
			}
			var name = pokedex[value.pokeid];
			var pkmnImage = sprintf("<?=$config['urls']['images']['pokemon']?>", value.pokeid);
			if(value.pokeform !== '0'){
				pkmnImage = pkmnImage.toString().replace("00.png", value.pokeform + ".png");
			}
			html += "<div class='col-md-2" + (count == 0 ? " col-md-offset-1" : "") + "'>";
	
			html += "<img src='" + pkmnImage + "' width='64' height='64'><p><span class='text-nowrap <?php echo $config['ui']['style']?>'>" + name + ": " + numberWithCommas(value.count) + "</span>";
			html += "<span class='text-nowrap <?php echo $config['ui']['style']?>'><br>(" + value.count + " : " + value.total + ")<br><b>Ø 1 : " + Math.round(value.total/value.count) + "</b></span></p></br>";
			html += "</div>";
			if (count == 4) {
				html += "</div>";
				count = 0;
			} else {
				count++;
			}
			});
			$('#shiny-rates-total').html(html);
		});
	}else{
		sendRequest({ "type": "dashboard", "stat": "shinyAlltime", "token": tmp }, function(data, status) {
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
			$.each(obj.shiny_rates_total, function(key, value) {
			if (count == 0) {
				html += "<div class='row justify-content-center'>";
			}
			var name = pokedex[value.pokeid];
			var pkmnImage = sprintf("<?=$config['urls']['images']['pokemon']?>", value.pokeid);
			html += "<div class='col-md-2" + (count == 0 ? " col-md-offset-1" : "") + "'>";
			html += "<img src='" + pkmnImage + "' width='64' height='64'><p><span class='text-nowrap <?php echo $config['ui']['style']?>'>" + name + ": " + numberWithCommas(value.count) + "</span>";
			html += "<span class='text-nowrap <?php echo $config['ui']['style']?>'><br>(" + value.count + " : " + value.total + ")<br><b>Ø 1 : " + Math.round(value.total/value.count) + "</b></span></p></br>";
			html += "</div>";
			if (count == 4) {
				html += "</div>";
				count = 0;
			} else {
				count++;
			}
			});
			$('#shiny-rates-total').html(html);
		});
	}
  }
  if(newPokestopsGyms){
	tmp = createToken();
	sendRequest({ "type": "dashboard", "stat": "new", "token": tmp }, function(data, status) {
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
		
        var month = new Array();
        month[1] = "Januar"; month[2] = "Februar"; month[3] = "März"; month[4] = "April"; month[5] = "Mai"; month[6] = "Juni"; month[7] = "Juli"; month[8] = "August"; month[9] = "September"; month[10] = "Oktober"; month[11] = "November"; month[12] = "Dezember";
		
		//Create Panel for new Stops
		var html_stops = "";
		html_stops += "<div><ul class='list-group list-group-flush'>";
		html_stops += "<li class='list-group-item <?php echo $config['ui']['style']?>'><h4><img src='./static/images/stop.png' width='32' height='32' class='mx-1'/><b><u>Neue Pokestops im Gebiet</u></b><img src='./static/images/stop.png' width='32' height='32' class='mx-1'/></h4></li>";
		$.each(obj.new_stops, function(key, value) {
			var monthString = month[value.month];
			var yearString = (value.year).toString().substring(2,4);
		
			html_stops += "<li class ='list-group-item <?php echo $config['ui']['style']?>'>";
			html_stops += "<h5 class='list-group-item-heading <?php echo $config['ui']['style']?>'>" + monthString + " '" + yearString +": <b>" + numberWithCommas(value.new) + "</b></h5>";
			html_stops += "</li>";
		});
		html_stops += "</ul></div>";
		$('#new-pokestops').html(html_stops);
		
		//Create Panel for new Gyms
		var html_gyms = "";
		html_gyms += "<div><ul class='list-group list-group-flush'>";
		html_gyms += "<li class='list-group-item <?php echo $config['ui']['style']?>'><h4><img src='./static/images/swords.png' width='32' height='32' class='mx-1'/><b><u>Neue Arenen im Gebiet</u></b><img src='./static/images/swords.png' width='32' height='32' class='mx-1'/></h4></li>";
		$.each(obj.new_gyms, function(key, value) {
			var monthString = month[value.month];
			var yearString = (value.year).toString().substring(2,4);
		
			html_gyms += "<li class ='list-group-item <?php echo $config['ui']['style']?>'>";
			html_gyms += "<h5 class='list-group-item-heading <?php echo $config['ui']['style']?>'>" + monthString + " '" + yearString +": <b>" + numberWithCommas(value.new) + "</b></h5>";
			html_gyms += "</li>";
		});
		html_gyms += "</ul></div>";
		$('#new-gyms').html(html_gyms);
		
	});
  }
  
  

}


function createToken() {
  //TODO: Secure
  <?php $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(16)); ?>
  return "<?php echo $_SESSION['token']; ?>";
}

  //first tab should be auto-open
  document.getElementById('Top10MonTod').style.display = "block";
  document.getElementById('NewStops').style.display = "block";

</script>
