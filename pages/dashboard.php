<?php
include './config.php';
include './includes/DbConnector.php';
include './includes/utils.php';

$html = "
<div style='max-width:1280px;margin: 0 auto !important;float: none !important;'>

<h2 class='page-header text-center'>Übersicht</h2>
<div class='card text-center p-1 m-4'>
	<div class='card-header heading text-light'><b>Allgemein</b></div>
	<div class='card-body'>
		<div class='container'>
			<div class='row'>
				<div class='col-md-4'>
					<div class='list-group'>
						<a class='list-group-item'>
							<h3 class='pull-right'><img src='./static/images/quests/1.png' width='64' height='64'/></h3>
							<h4 class='list-group-item-heading pokemon-count'>0</h4>
							<p class='list-group-item-text'>Aktive Pokemon</p>
						</a>
					</div>
				</div>
				<div class='col-md-4'>
					<div class='list-group'>
						<a class='list-group-item'>
							<h3 class='pull-right'><img src='./static/images/iv.png' width='48' height='48'/></h3>
							<h4 class='list-group-item-heading pokemon-iv-count'>0</h4>
							<p class='list-group-item-text'>Pokemon mit IV</p>
						</a>
					</div>
				</div>
				<div class='col-md-4'>
					<div class='list-group'>
						<a class='list-group-item'>
							<h3 class='pull-right'><img src='./static/images/iv.png' width='48' height='48'/></h3>
							<h4 class='list-group-item-heading pokemon-total-iv-count'>0</h4>
							<p class='list-group-item-text'>IV Pokemon Heute Gesamt</p>
						</a>
					</div>
				</div>
				<div class='col-md-4'>
					<div class='list-group'>
						<a class='list-group-item'>
							<h3 class='pull-right'><img src='./static/images/teams/neutral.png' width='64' height='64'/></h3>
							<h4 class='list-group-item-heading gym-count'>0</h4>
							<p class='list-group-item-text'>Arenen Gesamt</p>
						</a>
					</div>
				</div>
				<div class='col-md-4'>
					<div class='list-group'>
						<a class='list-group-item'>
							<h3 class='pull-right'><img src='./static/images/quests/1402.png'  width='64' height='64'/></h3>
							<h4 class='list-group-item-heading raids-count'>0</h4>
							<p class='list-group-item-text'>Aktive Raids</p>
						</a>
					</div>
				</div>
				<div class='col-md-4'>
					<div class='list-group'>
						<a class='list-group-item'>
							<h3 class='pull-right'><img src='./static/images/raids/5.png' width='auto' height='64'/></h3>
							<h4 class='list-group-item-heading legendary-raids-count'>0</h4>
							<p class='list-group-item-text'>Aktive Legendäre Raids</p>
						</a>
					</div>
				</div>
			</div>
		<div>
	</div>
</div>
</div>
</div>


<div class='card text-center p-1 m-4'>
  <div class='card-header heading text-light'><b>Teams</b></div>
  <div class='card-body'>
    <div class='container'>
      <div class='row'>
        <div class='col-md-3'>
          <div class='list-group'>
            <a class='list-group-item neutral'>
              <h3 class='pull-right'><img src='./static/images/teams/neutral.png' width='64' height='64'/></h3>
              <h4 class='list-group-item-heading neutral-gyms-count'>0</h4>
              <p class='list-group-item-text'>Neutrale Arenen</p>
            </a>
          </div>
        </div>
        <div class='col-md-3'>
          <div class='list-group'>
            <a class='list-group-item valor'>
              <h3 class='pull-right'><img src='./static/images/teams/valor.png' width='64' height='64'/></h3>
              <h4 class='list-group-item-heading valor-gyms-count'>0</h4>
              <p class='list-group-item-text'>Valor Arenen</p>
            </a>
          </div>
        </div>
        <div class='col-md-3'>
          <div class='list-group'>
            <a class='list-group-item mystic'>
              <h3 class='pull-right'><img src='./static/images/teams/mystic.png' width='64' height='64'/></h3>
              <h4 class='list-group-item-heading mystic-gyms-count'>0</h4>
              <p class='list-group-item-text'>Mystic Arenen</p>
            </a>
          </div>
        </div>
        <div class='col-md-3'>
          <div class='list-group'>
            <a class='list-group-item instinct'>
              <h3 class='pull-right'><img src='./static/images/teams/instinct.png' width='64' height='64'/></h3>
              <h4 class='list-group-item-heading instinct-gyms-count'>0</h4>
              <p class='list-group-item-text'>Instinct Arenen</p>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class='card text-center p-1 m-3'>
  <div class='card-header heading text-light'><b>Pokestops</b></div>
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
            <p class='list-group-item-text'>Lockmodule</p>
          </a>
        </div>
        <div class='col-md-3'>
          <a class='list-group-item'>
            <h3 class='pull-right'><img src='./static/images/quests/0.png' width='64' height='64'/></h3>
            <h4 class='list-group-item-heading quest-pokestop-count'>0</h4>
            <p class='list-group-item-text'>Quests</p>
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
          <a href='https://rocketmapdo.de/' class='link'>
			<center>
			<img src='./static/map.png' width='128' height='auto'/></h3>
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
			<img src='./static/discord.png' width='128' height='auto'/></h3>
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
			<img src='./static/patreon.png' width='128' height='auto'/></h3>
            <p> > Zu Patreon (Spenden) < </p>
			</center>
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
  $({ pokemonValue: 0, pokemonIvValue: 0, pokemonTotalIvValue: 0, gymsValue: 0, raidsValue: 0, legendaryRaidsValue: 0, neutralValue: 0, mysticValue: 0, valorValue: 0, instinctValue: 0, pokestopsValue: 0, luredValue: 0, questsValue: 0 }).animate({ pokemonValue: obj.active_pokemon, pokemonIvValue: obj.iv_pokemon, pokemonTotalIvValue: obj.total_iv_pokemon, gymsValue: obj.gyms, raidsValue: obj.raids, legendaryRaidsValue: obj.legendaryRaids, neutralValue: obj.neutral, mysticValue: obj.mystic, valorValue: obj.valor, instinctValue: obj.instinct, pokestopsValue: obj.pokestops, luredValue: obj.lured, questsValue: obj.quests }, {
    duration: 2000,
    easing: 'swing', // can be anything
    step: function() { // called on every step
      // Update the element's text with rounded-up value:
      $('.pokemon-count').text(numberWithCommas(Math.round(this.pokemonValue)));
      $('.pokemon-iv-count').text(numberWithCommas(Math.round(this.pokemonIvValue)));
      $('.pokemon-total-iv-count').text(numberWithCommas(Math.round(this.pokemonTotalIvValue)));
      $('.gym-count').text(numberWithCommas(Math.round(this.gymsValue)));
      $('.raids-count').text(numberWithCommas(Math.round(this.raidsValue)));
      $('.legendary-raids-count').text(numberWithCommas(Math.round(this.legendaryRaidsValue)));
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