<?php
$html = "
<div class='container'>
<h2 class='page-header text-center'>Overview</h2>
<div class='card text-center p-1 m-3'>
  <div class='card-header bg-dark text-light'><b>Overview</b></div>
  <div class='card-body'>
    <div class='container'>
      <div class='row'>
        <div class='col-md-3'>
          <div class='list-group'>
            <a class='list-group-item'>
              <h3 class='pull-right'><img src='./static/images/quests/1.png' width='64' height='64'/></h3>
              <h4 class='list-group-item-heading pokemon-count'>0</h4>
              <p class='list-group-item-text'>Active Pokemon</p>
            </a>
          </div>
        </div>
        <div class='col-md-3'>
          <div class='list-group'>
            <a class='list-group-item'>
              <h3 class='pull-right'><img src='./static/images/teams/neutral.png' width='64' height='64'/></h3>
              <h4 class='list-group-item-heading gym-count'>0</h4>
              <p class='list-group-item-text'>Total Gyms</p>
            </a>
          </div>
        </div>
        <div class='col-md-3'>
          <div class='list-group'>
            <a class='list-group-item'>
              <h3 class='pull-right'><img src='./static/images/quests/1402.png' width='64' height='64'/></h3>
              <h4 class='list-group-item-heading raids-count'>0</h4>
              <p class='list-group-item-text'>Active Raids</p>
            </a>
          </div>
        </div>
        <div class='col-md-3'>
          <div class='list-group'>
            <a class='list-group-item'>
              <h3 class='pull-right'><img src='./static/images/pokestop.png' width='64' height='64'/></h3>
              <h4 class='list-group-item-heading pokestop-count'>0</h4>
              <p class='list-group-item-text'>Pokestops</p>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class='card text-center p-1 m-3'>
  <div class='card-header bg-dark text-light'><b>Pokemon</b></div>
  <div class='card-body'>
    <div class='container'>
      <div class='row'>
        <div class='col-md-3'>
          <div class='list-group'>
            <a class='list-group-item'>
              <h3 class='pull-right'><img src='./static/images/quests/1.png' width='64' height='64'/></h3>
              <h4 class='list-group-item-heading total-pokemon-count'>0</h4>
              <p class='list-group-item-text'>Total Pokemon</p>
            </a>
          </div>
        </div>
        <div class='col-md-3'>
          <div class='list-group'>
            <a class='list-group-item'>
              <h3 class='pull-right'><img src='./static/images/quests/1.png' width='64' height='64'/></h3>
              <h4 class='list-group-item-heading pokemon-count'>0</h4>
              <p class='list-group-item-text'>Active Pokemon</p>
            </a>
          </div>
        </div>
        <div class='col-md-3'>
          <div class='list-group'>
            <a class='list-group-item'>
              <h3 class='pull-right'><img src='./static/images/100.png' width='64' height='64'/></h3>
              <h4 class='list-group-item-heading total-iv-count'>0</h4>
              <p class='list-group-item-text'>Total IVs</p>
            </a>
          </div>
        </div>
        <div class='col-md-3'>
          <div class='list-group'>
            <a class='list-group-item'>
              <h3 class='pull-right'><img src='./static/images/100.png' width='64' height='64'/></h3>
              <h4 class='list-group-item-heading active-iv-count'>0</h4>
              <p class='list-group-item-text'>Active with IVs</p>
            </a>
          </div>
        </div>
      </div>
      <div class='card text-center p-1 m-3'>
        <div class='card-header bg-dark text-light'><b>Top 10 Pokemon (Lifetime)</b></div>
        <div class='card-body'>
          <div class='container'>
            <div id='top-10-pokemon' class='row justify-content-center'></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class='card text-center p-1 m-3'>
  <div class='card-header bg-dark text-light'><b>Teams</b></div>
  <div class='card-body'>
    <div class='container'>
      <div class='row'>
        <div class='col-md-3'>
          <div class='list-group'>
            <a class='list-group-item neutral'>
              <h3 class='pull-right'><img src='./static/images/teams/neutral.png' width='64' height='64'/></h3>
              <h4 class='list-group-item-heading neutral-gyms-count'>0</h4>
              <p class='list-group-item-text'>Neutral Gyms</p>
            </a>
          </div>
        </div>
        <div class='col-md-3'>
          <div class='list-group'>
            <a class='list-group-item valor'>
              <h3 class='pull-right'><img src='./static/images/teams/valor.png' width='64' height='64'/></h3>
              <h4 class='list-group-item-heading valor-gyms-count'>0</h4>
              <p class='list-group-item-text'>Valor Gyms</p>
            </a>
          </div>
        </div>
        <div class='col-md-3'>
          <div class='list-group'>
            <a class='list-group-item mystic'>
              <h3 class='pull-right'><img src='./static/images/teams/mystic.png' width='64' height='64'/></h3>
              <h4 class='list-group-item-heading mystic-gyms-count'>0</h4>
              <p class='list-group-item-text'>Mystic Gyms</p>
            </a>
          </div>
        </div>
        <div class='col-md-3'>
          <div class='list-group'>
            <a class='list-group-item instinct'>
              <h3 class='pull-right'><img src='./static/images/teams/instinct.png' width='64' height='64'/></h3>
              <h4 class='list-group-item-heading instinct-gyms-count'>0</h4>
              <p class='list-group-item-text'>Instinct Gyms</p>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class='card text-center p-1 m-3'>
  <div class='card-header bg-dark text-light'><b>Pokestops</b></div>
  <div class='card-body'>
    <div class='container'>
      <div class='row'>
        <div class='col-md-3'>
          <a class='list-group-item'>
            <h3 class='pull-right'><img src='./static/images/pokestop.png' width='64' height='64'/></h3>
            <h4 class='list-group-item-heading pokestop-count'>0</h4>
            <p class='list-group-item-text'>Pokestops</p>
          </a>
        </div>
        <div class='col-md-3'>
          <a class='list-group-item'>
            <h3 class='pull-right'><img src='./static/images/lure-module.png' width='64' height='64'/></h3>
            <h4 class='list-group-item-heading lured-pokestop-count'>0</h4>
            <p class='list-group-item-text'>Lured Pokestops</p>
          </a>
        </div>
        <div class='col-md-3'>
          <a class='list-group-item'>
            <h3 class='pull-right'><img src='./static/images/quests/0.png' width='64' height='64'/></h3>
            <h4 class='list-group-item-heading quest-pokestop-count'>0</h4>
            <p class='list-group-item-text'>Field Research</p>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class='card text-center p-1 m-3'>
  <div class='card-header bg-dark text-light'><b>Spawnpoint Timers</b></div>
  <div class='card-body'>
    <div class='container'>
      <div class='row'>
        <div class='col-md-3'>
          <a class='list-group-item'>
            <h3 class='pull-right'><img src='./static/images/spawnpoint.png' width='64' height='64'/></h3>
            <h4 class='list-group-item-heading spawnpoint-count'>0</h4>
            <p class='list-group-item-text'>Total</p>
          </a>
        </div>
        <div class='col-md-3'>
          <a class='list-group-item'>
            <h3 class='pull-right'><img src='./static/images/found.png' width='64' height='64'/></h3>
            <h4 class='list-group-item-heading found-spawnpoint-count'>0</h4>
            <p class='list-group-item-text'>Found</p>
          </a>
        </div>
        <div class='col-md-3'>
          <a class='list-group-item'>
            <h3 class='pull-right'><img src='./static/images/missing.png' width='64' height='64'/></h3>
            <h4 class='list-group-item-heading missing-spawnpoint-count'>0</h4>
            <p class='list-group-item-text'>Missing</p>
          </a>
        </div>
        <div class='col-md-3'>
          <a class='list-group-item'>
            <h3 class='pull-right'><img src='./static/images/percentage.png' width='64' height='64'/></h3>
            <h4 class='list-group-item-heading percentage-spawnpoint-count'>0%</h4>
            <p class='list-group-item-text'>Percentage</p>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
";
echo $html;
?>
<link rel="stylesheet" href="./static/css/dashboard.css"/>
<script type="text/javascript" src="./static/js/pokedex.js"></script>
<script type="text/javascript" src="./static/js/utils.js"></script>
<script type="text/javascript">
var tmp = createToken();
sendRequest({ "type": "dashboard", "token": tmp }, function(data, status) {
  tmp = null;
  if (<?=$config['core']['showDebug']?>) {
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
    html += "<img src='" + pkmnImage + "' width='64' height='64'><span class='text-nowrap'>" + name + ": " + numberWithCommas(value.count) + "</span></br>";
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

function createToken() {
  //TODO: Secure
  <?php $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(16)); ?>
  return "<?php echo $_SESSION['token']; ?>";
}
</script>