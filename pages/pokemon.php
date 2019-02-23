<?
require_once './static/data/pokedex.php';

$html = "
<div class='container'>
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
  if ($id === 0) {
    continue;
  }
  $html .= "
<div id='pkmn-$id' class='col text-center p-2'>
  <img src='" . sprintf($config['urls']['images']['pokemon'], $id) . "' width='48' height='48'>
  <div class='card-body'>
    <b>$name</b>
    <p id='pkmn-seen-$id' class='card-text'>
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
<script type='text/javascript'>
$("[data-toggle='datepicker']").datepicker({
  autoHide: true,
  yearFirst: true,
  format: "yyyy-mm-dd",
  zIndex: 2048,
});
$("#filter-date").change(filterPokemon);
$("#filter-date").datepicker("setDate", new Date());

function filterPokemon() {
  var tmp = createToken();
  sendRequest({ "table": "pokemon_stats", "token": tmp }, function(data) {
    this.tmp = null;
    var obj = JSON.parse(data);
    var dateFilter = document.getElementById("filter-date").value;
    var pokeFilter = document.getElementById("filter-pokemon").value;
    //TODO: Loop 1-490, index the stats obj, and run filter check.
    //for (const [pokemon_id, count] of Object.entries(obj)) {
    obj.forEach(stat => {
      if (stat.date === dateFilter) {
        $("#pkmn-seen-" + stat.pokemon_id).text("Seen: " + stat.count);
        if (pokeFilter === "") {
          $("#pkmn-" + stat.pokemon_id).show();
        } else {
          if (pokedex[stat.pokemon_id].toLowerCase().includes(pokeFilter.toLowerCase())) {
            $("#pkmn-" + stat.pokemon_id).show();
          } else {
            $("#pkmn-" + stat.pokemon_id).hide();
          }
        }
      }
    });
  });
}

function sendRequest(options, successCallback) {
  $.ajax({
    url: "api.php",
    method: "POST",
    data: options,
    success: successCallback,
    error: function(data) {
      console.log(data);
    }
  });
}

function createToken() {
  //TODO: Secure
  <?php $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(16)); ?>
  return "<?php echo $_SESSION['token']; ?>";
}
</script>