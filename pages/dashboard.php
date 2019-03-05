<?php
include './config.php';
include './includes/DbConnector.php';
include './includes/utils.php';

$html = "
<div class='container'>
<h2 class='page-header text-center'>Overview</h2>
<div class='card text-center p-1 m-3'>
  <div class='card-header bg-dark text-light'><b>Overview</b></div>
  <div class='card-body'>
    <div class='container'>
      iv class='row'>
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
    div>
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
</div>
";
echo $html;
?>
<link rel="stylesheet" href="./static/css/dashboard.css"/>
<script type="text/javascript" src="./static/js/utils.js"></script>
<script type="text/javascript">
var tmp = createToken();
sendRequest({ "type": "dashboard", "token": tmp }, function(data) {
  tmp = null;
  if (<?=$config['core']['showDebug']?>) {
    console.log("Dashboard:",data);
  }
  var obj = JSON.parse(data);
  if (obj === 0) {
    console.log("Failed to get data for dashboard.");
    return;
  }

  // Animate the element's value from x to y:
  $({ pokemonValue: 0, gymsValue: 0, raidsValue: 0, neutralValue: 0, mysticValue: 0, valorValue: 0, instinctValue: 0, pokestopsValue: 0, luredValue: 0, questsValue: 0 }).animate({ pokemonValue: obj.active_pokemon, gymsValue: obj.gyms, raidsValue: obj.raids, neutralValue: obj.neutral, mysticValue: obj.mystic, valorValue: obj.valor, instinctValue: obj.instinct, pokestopsValue: obj.pokestops, luredValue: obj.lured, questsValue: obj.quests }, {
    duration: 3000,
    easing: 'swing', // can be anything
    step: function() { // called on every step
      // Update the element's text with rounded-up value:
      $('.pokemon-count').text(numberWithCommas(Math.round(this.pokemonValue)));
      $('.gym-count').text(numberWithCommas(Math.round(this.gymsValue)));
      $('.raids-count').text(numberWithCommas(Math.round(this.raidsValue)));
      $('.neutral-gyms-count').text(numberWithCommas(Math.round(this.neutralValue)));
      $('.valor-gyms-count').text(numberWithCommas(Math.round(this.valorValue)));
      $('.mystic-gyms-count').text(numberWithCommas(Math.round(this.mysticValue)));
      $('.instinct-gyms-count').text(numberWithCommas(Math.round(this.instinctValue)));
      $('.pokestop-count').text(numberWithCommas(Math.round(this.pokestopsValue)));
      $('.lured-pokestop-count').text(numberWithCommas(Math.round(this.luredValue)));
      $('.quest-pokestop-count').text(numberWithCommas(Math.round(this.questsValue)));
    }
  });
});

function createToken() {
  //TODO: Secure
  <?php $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(16)); ?>
  return "<?php echo $_SESSION['token']; ?>";
}
</script>