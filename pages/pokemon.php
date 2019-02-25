<?php
require_once './static/data/pokedex.php';

$html = "
<div class='container'>
  <h2 class='page-header text-center'>Pokemon found in the wild!</h2>
  <div class='row'>
    <div class='input-group mb-3'>
      <div class='input-group-prepend'>
        <label class='input-group-text' for='filter-date'>Date</label>
      </div>
      <input id='filter-date' type='text' class='form-control' data-toggle='datepicker'>
    </div>
    <div class='input-group mb-3'>
      <div class='input-group-prepend'>
        <label class='input-group-text' for='search-input'>Search Pokemon</label>
      </div>
      <input type='text' id='filter-pokemon' class='form-control input-lg' onkeyup='filterPokemon()' placeholder='Search by name..' title='Type in a name'>
    </div>
  </div>
  <div class='row'>";
foreach ($pokedex as $id => $name) {
  if ($id <= 0 || $id > 807) {
    continue;
  }
  $html .= "
<div id='pkmn-$id' class='col text-center p-2'>
  <img src='" . sprintf($config['urls']['images']['pokemon'], $id) . "' width='48' height='48'>
  <div class='card-body'>
    <b>$name</b>
    <p id='pkmn-seen-$id' class='card-text text-nowrap'>
      Seen: 0
    </p>
  </div>
</div>";
}
$html .= "</div>
</div>";
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

//Cache retrieved pokemon stats data
var obj = [];
function filterPokemon() {
  var dateFilter = document.getElementById("filter-date").value;
  var pokeFilter = document.getElementById("filter-pokemon").value;
  console.log("Date:",dateFilter,"Pokemon:",pokeFilter);
  
  if (obj != null && obj.length > 0) {
    filterPokemonElements(obj, dateFilter, pokeFilter);
  } else {
    var tmp = createToken();
    sendRequest({ "table": "pokemon_stats", "token": tmp, "limit": 999999 }, function(data) {
      tmp = null;
      obj = JSON.parse(data);
      filterPokemonElements(obj, dateFilter, pokeFilter);
    });
  }
}

function filterPokemonElements(elements, dateFilter, pokeFilter) {
  elements.map(stat => {
    if (stat.date === dateFilter) {
      $("#pkmn-seen-" + stat.pokemon_id).text("Seen: " + numberWithCommas(stat.count));
      if (pokeFilter === "") {
        $("#pkmn-" + stat.pokemon_id).show();
      } else {
        var maxPokemon = <?=$config['core']['maxPokemon']?>;
        for (var i = 0; i <= maxPokemon; i++) {
          var pokemonId = (i + 1);
          //TODO: VVV Fix console error spam
          if (pokedex[pokemonId].toLowerCase().includes(pokeFilter.toLowerCase())) {
            $("#pkmn-" + pokemonId).show();
          } else {
            $("#pkmn-" + pokemonId).hide();
          }
        }
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