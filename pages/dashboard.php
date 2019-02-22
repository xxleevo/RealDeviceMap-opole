<?php
include './config.php';
include './includes/DbConnector.php';
include './includes/utils.php';

$html = "
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
              <p class='list-group-item-text'>Pokemon</p>
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
";
echo $html;

$data = get_gym_stats();
$neutral = $data == null ? 0 : $data[0]; //Neutral gyms
$mystic = $data == null ? 0 : $data[1]; //Mystic gyms
$valor = $data == null ? 0 : $data[2]; //Valor gyms
$instinct = $data == null ? 0 : $data[3]; //Instinct gyms

$data = get_pokestop_stats();
$pokestops = $data["total"];
$lured = $data["lured"];
$quests = $data["quests"];

$pokemon = get_table_count("pokemon");
$gyms = get_table_count("gym");
$raids = get_raid_stats();

?>
<link rel="stylesheet" href="./static/css/dashboard.css"/>
<script type="text/javascript">
var neutral = "<?=$neutral?>";
var mystic = "<?=$mystic?>";
var valor = "<?=$valor?>";
var instinct = "<?=$instinct?>";
var pokestops = "<?=$pokestops?>";
var lured = "<?=$lured?>";
var quests = "<?=$quests?>";
var pokemon = "<?=$pokemon?>";
var gyms = "<?=$gyms?>";
var raids = "<?=$raids?>";

// Animate the element's value from x to y:
$({ pokemonValue: 0, gymsValue: 0, raidsValue: 0, neutralValue: 0, mysticValue: 0, valorValue: 0, instinctValue: 0, pokestopsValue: 0, luredValue: 0, questsValue: 0 }).animate(
    { pokemonValue: pokemon, gymsValue: gyms, raidsValue: raids, neutralValue: neutral, mysticValue: mystic, valorValue: valor, instinctValue: instinct, pokestopsValue: pokestops, luredValue: lured, questsValue: quests }, {
    duration: 3000,
    easing: 'swing', // can be anything
    step: function () { // called on every step
        // Update the element's text with rounded-up value:
        $('.pokemon-count').text(commaSeparateNumber(Math.round(this.pokemonValue)));
        $('.gym-count').text(commaSeparateNumber(Math.round(this.gymsValue)));
        $('.raids-count').text(commaSeparateNumber(Math.round(this.raidsValue)));
        $('.neutral-gyms-count').text(commaSeparateNumber(Math.round(this.neutralValue)));
        $('.valor-gyms-count').text(commaSeparateNumber(Math.round(this.valorValue)));
        $('.mystic-gyms-count').text(commaSeparateNumber(Math.round(this.mysticValue)));
        $('.instinct-gyms-count').text(commaSeparateNumber(Math.round(this.instinctValue)));
        $('.pokestop-count').text(commaSeparateNumber(Math.round(this.pokestopsValue)));
        $('.lured-pokestop-count').text(commaSeparateNumber(Math.round(this.luredValue)));
        $('.quest-pokestop-count').text(commaSeparateNumber(Math.round(this.questsValue)));
    }
});

function commaSeparateNumber(val) {
    while (/(\d+)(\d{3})/.test(val.toString())) {
        val = val.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
    }
    return val;
}
</script>