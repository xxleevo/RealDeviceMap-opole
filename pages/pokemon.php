<?php
require_once './static/data/pokedex.php';

$html = "
<div class='container'>
  <h2 class='page-header text-center' data-i18n='pokemon_title'>Gesichtete wilde Pokemon!</h2>
  <div class='row'>
    <div class='input-group mb-3'>
      <div class='input-group-prepend'>
        <label class='input-group-text' for='filter-date' data-i18n='pokemon_filter_date'>Date</label>
      </div>
      <input id='filter-date' type='text' class='form-control' data-toggle='datepicker'>
    </div>
    <div class='input-group mb-3'>
      <div class='input-group-prepend'>
        <label class='input-group-text' for='search-input' data-i18n='pokemon_filter_pokemon'>Search Pokemon</label>
      </div>
      <input type='text' id='filter-pokemon' class='form-control input-lg' onkeyup='filterPokemon()' placeholder='Search by name..' title='Type in a name'>
    </div>
    <div class='input-group-text mb-3' style ='display:none'>
      <input id='foundOnly' for='filter-found' type='checkbox' onclick='foundOnly()'aria-label='Checkbox for following text input'>&nbsp;Nur gefundene zeigen
    </div>
  </div>";
$count = 0;
//<div class='row'>";
foreach ($pokedex as $id => $name) {
    if ($id <= 0 || $id > 807) {
        continue;
    }
    if ($count == 0) {
      $html .= "<div class='row justify-content-center'>";
    }
    $html .= "
<div id='pkmn-$id' class='col-sm-2" . ($count == 0 ? " col-sd-offset-1" : "") . " text-center'>
  <img src='" . sprintf($config['urls']['images']['pokemon'], $id) . "' width='48' height='48'>
  <div class='card-body'>
    <span class='text-nowrap'><b>$name</b> #$id</span>
    <p class='card-text text-nowrap'>
      Seen:
      <span id='pkmn-seen-$id'>0</span>
    </p>
  </div>
</div>";
    if ($count == 5) {
      $html .= "</div>";
      $count = 0;
    } else {
      $count++;
    }
}
$html .= "</div>";
$html .= "<div style='margin-bottom:70px;'></div>";
echo $html;
?>

<script type='text/javascript' src='./static/js/datepicker.js'></script>
<script type='text/javascript' src='./static/js/pokedex.js'></script>
<script type="text/javascript" src="./static/js/utils.js"></script>
<script type='text/javascript'>
$("[data-toggle='datepicker']").datepicker({
  autoHide: true,
  yearFirst: true,
  format: "yyyy-mm-dd",
  zIndex: 2048,
});
$("#filter-date").change(filterPokemon);
$("#filter-date").datepicker("setDate", new Date());

var debug = <?=$config['core']['showDebug'] !== false ? '1' : '0'?>;
//Cache retrieved pokemon stats data
var obj = [];
function filterPokemon() {
  var dateFilter = document.getElementById("filter-date").value;
  var pokeFilter = document.getElementById("filter-pokemon").value;
  var checkbox = document.getElementById("foundOnly");
  //console.log("Date:", dateFilter, "Pokemon:", pokeFilter, "Checkbox:", checkbox.checked);
  
  if (obj != null && obj.length > 0) {
    filterPokemonElements(obj, dateFilter, pokeFilter, checkbox.checked);
  } else {
    var tmp = createToken();
    sendRequest({ "table": "pokemon_stats", "token": tmp }, function(data, status) {
      tmp = null;
      if (debug) {
        if (data === 0) {
          conosle.log("Failed to get pokemon stats data.");
        } else {
          console.log("Pokemon:", data);
        }
      }
      obj = JSON.parse(data);
      filterPokemonElements(obj, dateFilter, pokeFilter, checkbox.checked);
    });
  }
}
function foundOnly(){
	filterPokemon();
}

function filterPokemonElements(elements, dateFilter, pokeFilter, checkboxChecked) {
	//console.log("Filter elements, checkbox:", checkboxChecked);
  elements.map(stat => {
    if (stat.date === dateFilter) {
		$("#pkmn-seen-" + stat.pokemon_id).text(numberWithCommas(stat.count));
      if (pokeFilter === "" && !checkboxChecked) {
        $("#pkmn-" + stat.pokemon_id).show();
	  }
       else {
        var maxPokemon = <?=$config['core']['maxPokemon']?>;
        for (var i = 0; i <= maxPokemon; i++) {
          var pokemonId = (i + 1);
		  if(pokeFilter !== ""){ // For Poke Filter
			if (pokedex[pokemonId].toLowerCase().includes(pokeFilter.toLowerCase())) {
				$("#pkmn-" + pokemonId).show();
			} else {
				$("#pkmn-" + pokemonId).hide();
			}
		  }
		  //if(checkboxChecked){
		  //  $("#pkmn-" + pokemonId).hide();
		  //}
        }
		//if(checkboxChecked){
		//	$("#pkmn-" + stat.pokemon_id).show();
		//}
      }
    }
  });
}

function createToken() {
  //TODO: Secure
  <?php $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(16)); ?>
  return "<?php echo $_SESSION['token']; ?>";
}
</script>