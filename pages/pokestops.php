<?php
include_once './config.php';
include_once './includes/DbConnector.php';

$html = "
<div class='container'>
    <h2 class='page-header'>Pokestops</h2>
    <div class='row'>
        <div class='col-md-3'>
            <a class='list-group-item'>
                <h3 class='pull-right'>
                    <img src='./static/images/pokestop.png' width='64' height='64'/>
                </h3>
                <h4 class='list-group-item-heading pokestop-count'>
                    0
                </h4>
                <p class='list-group-item-text'>
                    Pokestops
                </p>
            </a>
        </div>
        <div class='col-md-3'>
            <a class='list-group-item'>
                <h3 class='pull-right'>
                    <img src='./static/images/lure-module.png' width='64' height='64'/>
                </h3>
                <h4 class='list-group-item-heading lured-pokestop-count'>
                    0
                </h4>
                <p class='list-group-item-text'>
                    Lured Pokestops
                </p>
            </a>
        </div>
        <div class='col-md-3'>
            <a class='list-group-item'>
                <h3 class='pull-right'>
                    <img src='./static/images/quests/0.png' width='64' height='64'/>
                </h3>
                <h4 class='list-group-item-heading quest-pokestop-count'>
                    0
                </h4>
                <p class='list-group-item-text'>
                    Field Research
                </p>
            </a>
        </div>
    </div>
</div>";

echo $html;

$data = get_pokestop_stats();
$pokestops = $data["total"];
$lured = $data["lured"];
$quests = $data["quests"];

function get_pokestop_stats() {
  global $dbhost, $dbPort, $dbuser, $dbpass, $dbname;
  $db = new DbConnector($dbhost, $dbPort, $dbuser, $dbpass, $dbname);
  $pdo = $db->getConnection();
  $sql = "
SELECT 
  COUNT(id) total,
  SUM(CASE WHEN lure_expire_timestamp > 0 THEN 1 ELSE 0 END) lured,
  SUM(CASE WHEN quest_reward_type THEN 1 ELSE 0 END) quests
FROM
  " . $dbname . ".pokestop
";
  $result = $pdo->query($sql);
  if ($result->rowCount() > 0) {
    $data = $result->fetchAll()[0];
  }
  unset($pdo);
  unset($db);
  
  return $data;
}
?>
<link rel="stylesheet" href="./static/css/dashboard.css"/>
<!--<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>-->
<script type="text/javascript">
var pokestops = "<?=$pokestops?>";
var lured = "<?=$lured?>";
var quests = "<?=$quests?>";

// Animate the element's value from x to y:
$({ pokestopsValue: 0, luredValue: 0, questsValue: 0 }).animate({ pokestopsValue: pokestops, luredValue: lured, questsValue: quests }, {
    duration: 3000,
    easing: 'swing', // can be anything
    step: function () { // called on every step
        // Update the element's text with rounded-up value:
        $('.pokestop-count').text(commaSeparateNumber(pokestops));
        $('.lured-pokestop-count').text(commaSeparateNumber(lured));
        $('.quest-pokestop-count').text(commaSeparateNumber(quests));
    }
});

function commaSeparateNumber(val) {
    while (/(\d+)(\d{3})/.test(val.toString())) {
        val = val.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
    }
    return val;
}
</script>