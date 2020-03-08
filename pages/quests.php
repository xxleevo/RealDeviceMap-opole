<?php
include './vendor/autoload.php';
include './config.php';
include './includes/GeofenceService.php';
include './static/data/pokedex.php';

$geofenceSrvc = new GeofenceService();

$filters = "
<div class='container'>
  <div class='row'>
    <div class='input-group mb-3'>
      <div class='input-group-prepend'>
        <label class='input-group-text' for='search-input' data-i18n='quests_filter_reward'>Belohnung</label>
      </div>
      <input type='text' id='search-input' class='form-control input-lg' onkeyup='filter_quests()' placeholder='Namen suchen..' title='Namen eingeben..'>
    </div>
    <div class='input-group mb-3'>
      <div class='input-group-prepend'>
        <label class='input-group-text' for='filter-pokestop' data-i18n='quests_filter_pokestop'>Stop</label>
      </div>
      <input type='text' id='filter-pokestop' class='form-control input-lg' onkeyup='filter_quests()' placeholder='Nach Stopnamen suchen..' title='Stop namen eingeben'>
    </div>
    <div class='input-group mb-3'>
      <div class='input-group-prepend'>
        <label class='input-group-text' for='filter-city' data-i18n='quests_filter_city'>Stadt</label>
      </div>
      <select multiple id='filter-city' class='custom-select' onchange='filter_quests()'>
        <option value='' selected>All</option>";
        $count = count($geofenceSrvc->geofences);
        for ($i = 0; $i < $count; $i++) {
            $geofence = $geofenceSrvc->geofences[$i];
            $filters .= "<option value='".$geofence->name."'>".$geofence->name."</option>";
        }
        $filters .= "
        <!--<option value='" . $config['ui']['unknownValue'] . "'>" . $config['ui']['unknownValue'] . "</option>-->
      </select>
    </div>
  </div>
</div>
";

$modal = "
<h2 class='page-header text-center " . $config['ui']['style'] . "' data-i18n='quests_title'>Feldforschungs-Übersicht</h2>
<div class='btn-group btn-group-sm float-right'>
  <button type='button' class='btn btn-dark' data-toggle='modal' data-target='#filtersModal'>
    <i class='fa fa-fw fa-filter' aria-hidden='true'></i>
  </button>
  <button type='button' class='btn btn-dark' data-toggle='modal' data-target='#columnsModal'>
    <i class='fa fa-fw fa-columns' aria-hidden='true'></i>
  </button>
</div>
<p>&nbsp;</p>
<div class='modal fade' id='filtersModal' tabindex='-1' role='dialog' aria-labelledby='filtersModalLabel' aria-hidden='true'>
  <div class='modal-dialog' role='document'>
    <div class='modal-content'>
      <div class='modal-header'>
        <h5 class='modal-title' id='filtersModalLabel' data-i18n='quests_filters_title'>Quests Filtern</h5>
        <button type='button' class='close' data-dismiss='modal' aria-label='Schließen'>
          <span aria-hidden='true'>&times;</span>
        </button>
      </div>
      <div class='modal-body'>" . $filters . "</div>
      <div class='modal-footer'>
        <button type='button' class='btn btn-danger' id='reset-filters' data-i18n='quests_modal_reset_filters'>Filter zurücksetzen</button>
        <button type='button' class='btn btn-primary' data-dismiss='modal' data-i18n='quests_modal_close'>Close</button>
      </div>
    </div>
  </div>
</div>
<div class='modal fade' id='columnsModal' tabIndex='-1' role='dialog' aria-labelledby='columnsModalLabel' aria-hidden='true'>
  <div class='modal-dialog' role='document'>
    <div class='modal-content'>
      <div class='modal-header'>
        <h5 class='modal-title' id='columnsModalLabel' data-i18n='quests_modal_show_columns'>Spaltenanzeige</h5>
        <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
          <span aria-hidden='true'>&times;</span>
        </button>
      </div>    
      <div class='modal-body'>
        <div id='chkColumns'>
          <p><input type='checkbox' name='reward' data-i18n='quests_column_reward'/>&nbsp;Belohnung</p>
          <p><input type='checkbox' name='quest' data-i18n='quests_column_quest' />&nbsp;Quest</p>
          <p><input type='checkbox' name='condition' data-i18n='quests_column_condition' />&nbsp;Vorraussetzungen</p>
          <p><input type='checkbox' name='city' data-i18n='quests_column_city' />&nbsp;Stadt</p>
          <!--<p><input type='checkbox' name='updated' data-i18n='quests_column_updated' />&nbsp;Aktualisiert</p>-->
        </div>
      </div>
      <div class='modal-footer'>
        <button type='button' class='btn btn-primary' data-dismiss='modal' data-i18n='quests_modal_close'>Schließen</button>
      </div>
    </div>
  </div>
</div>
";

// Establish connection to database
$db = new DbConnector($config['db']);
$pdo = $db->getConnection();

// Query Database and Build Raid Billboard
try {
    $sql = "
SELECT 
  lat, 
  lon,
  quest_type,
  quest_timestamp, 
  quest_target,
  quest_conditions,
  quest_rewards,
  quest_template,
  quest_pokemon_id,
  quest_reward_type,
  quest_item_id,
  name,
  updated
FROM
  pokestop
WHERE
  quest_type IS NOT NULL
  AND name IS NOT NULL
  AND enabled = 1;
";

    $result = $pdo->query($sql);
    if ($result->rowCount() > 0) {
        echo $modal;
        echo "<div id='no-more-tables'>";
        echo "<table id='quest-table' class='table table-".$config['ui']['table']['style']." ".($config['ui']['table']['striped'] ? 'table-striped' : null)."' border='1'>";
        echo "<thead class='thead-".$config['ui']['table']['headerStyle']."'>";
        echo "<tr class='text-nowrap'>";
            echo "<th class='remove' data-i18n='quests_column_remove'>Schließen</th>";
            echo "<th class='reward' data-i18n='quests_column_reward'>Belohnung</th>";
            echo "<th class='quest' data-i18n='quests_column_quest'>Quest</th>";
            echo "<th class='condition' data-i18n='quests_column_condition'>Vorraussetzungen</th>";
            echo "<th class='city' data-i18n='quests_column_city'>Stadt</th>";
            echo "<th class='pokestop' data-i18n='quests_column_pokestop'>Stop</th>";
            //echo "<th class='updated' data-i18n='quests_column_updated'>Aktualisiert</th>";
        echo "</tr>";
        echo "</thead>";
        while ($row = $result->fetch()) {	
            $geofence = $geofenceSrvc->get_geofence($row['lat'], $row['lon']);
            if ($geofence == null && $config['ui']['pages']['quests']['ignoreUnknown'] !== false) {
                continue;
            }
            $city = ($geofence == null ? $config['ui']['unknownValue'] : $geofence->name);
            $map_link = sprintf($config['google']['maps'], $row["lat"], $row["lon"]);

            $quest_conditions_object = json_decode($row['quest_conditions']);
            $quest_rewards_object = json_decode($row['quest_rewards']);
            $quest_message = get_quest_message($row['quest_type'], $row['quest_target']);
	        $quest_reward = get_quest_reward($quest_rewards_object);
	        $quest_conditions_message = get_quest_conditions($quest_conditions_object);
	        $quest_icon = get_quest_icon($quest_rewards_object);
			
			//if(is_pokemon_reward($quest_rewards_object)){
				echo "<tr class='text-nowrap'>";
					echo "<td scope='row' class='text-center' data-title='Remove'><a title='Remove' data-toggle='tooltip' class='delete'><i class='fa fa-times'></i></a></td>";
					echo "<td data-title='Reward'><img src='$quest_icon' height=32 width=32 />&nbsp;" . $quest_reward . "</td>";
					echo "<td data-title='Quest'>" . $quest_message . "</td>";
					echo "<td data-title='Condition(s)'>" . (empty($quest_conditions_message) ? "&nbsp;" : $quest_conditions_message) . "</td>";
					echo "<td data-title='City'>" . $city . "</td>";
					echo "<td data-title='Gym'><a href='" . $map_link . "' target='_blank'>" . $row['name'] . "</a></td>";
					//echo "<td data-title='Updated'>" . date($config['core']['dateTimeFormat'], $row['updated']) . "</td>";
				echo "</tr>";
			//}
        }
        echo "</table>";
        echo "</div>";
		
        // Free result set
        unset($result);
    } else {
        echo "
		<div class='alert alert-primary' role='alert'>
			<i class='fa fa-info'>&nbsp;" . $config['ui']['noQuestsAvailableMessage'] . "</i>
		</div>";
    }
} catch (PDOException $e) {
    die("ERROR: Could not able to execute $sql. " . $e->getMessage());
}
// Close connection
unset($pdo);

function get_quest_message($type, $target) {
    switch ($type) {
        case 22://QuestType.AddFriend:
            $msg = "Füge %s Freund(e) hinzu";
            break;
        case 12://QuestType.AutoComplete:
            $msg = "Autocomplete";
            break;
        case 18://QuestType.BadgeRank:
            $msg = "Erhalte %s Medaille(n)";
            break;
        case 4://QuestType.CatchPokemon:
            $msg = "Fange %s Pokemon";
            break;
        case 21://QuestType.CompleteBattle:
            $msg = "Schließe %s Kämpfe ab";
            break;
        case 7://QuestType.CompleteGymBattle:
            $msg = "Schließe %s Arenenämpfe ab";
            break;
        case 9://QuestType.CompleteQuest:
            $msg = "Schließe %s Quest(s) ab";
            break;
        case 8://QuestType.CompleteRaidBattle:
            $msg = "Schließe %s Raid(s) ab";
            break;
        case 25://QuestType.EvolveIntoPokemon:
            $msg = "Entwickle %s in bestimmte Pokemon";
            break;
        case 15://QuestType.EvolvePokemon:
            $msg = "Entwickle %s Pokemon";
            break;
        case 11://QuestType.FavoritePokemon:
            $msg = "Favorisiere %s Pokemon";
            break;
        case 1://QuestType.FirstCatchOfTheDay:
            $msg = "Erster Fang des Tages";
            break;
        case 2://QuestType.FirstPokestopOfTheDay:
            $msg = "Erster Stop des Tages";
            break;
        case 17://QuestType.GetBuddyCandy:
            $msg = "Erhalte %s von deinem Kumpel";
            break;
        case 6://QuestType.HatchEgg:
            $msg = "Brüte %s Eie(r)";
            break;
        case 20://QuestType.JoinRaid:
            $msg = "Nimm an %s Raid(s) teil";
            break;
        case 16://QuestType.LandThrow:
            $msg = "Lande %s Würfe";
            break;
        case 3://QuestType.MultiPart:
            $msg = "Multi Part Quest";
            break;
        case 19://QuestType.PlayerLevel:
            $msg = "Werde Level %s"; ;
            break;
        case 24://QuestType.SendGift:
            $msg = "Verschicke %s Geschenk(e)";
            break;
        case 5://QuestType.SpinPokestop:
            $msg = "Drehe %s Pokestop(s)";
            break;
        case 23://QuestType.TradePokemon:
            $msg = "Tausche %s Pokemon";
            break;
        case 10://QuestType.TransferPokemon:
            $msg = "Verschicke %s Pokemon";
            break;
        case 14://QuestType.UpgradePokemon:
            $msg = "Levle %s Pokemon";
            break;
        case 13://QuestType.UseBerryInEncounter:
            $msg = "Nutze %s Beeren bei Pokemon";
            break;
        case 27://QuestType.completeCombat:
            $msg = "Kämpfe %s mal";
            break;
        case 28://QuestType.QUEST_TAKE_SNAPSHOT:
            $msg = "Mache %s Schnappschüsse";
            break;
        case 29://QuestType.BATTLE_TEAM_ROCKET:
            $msg = "Besiege %s Team Go Mitglied(er)";
            break;
        case 34://QuestType.BUDDY_EARN_AFFECTION_POINTS:
            $msg = "Verdiene %s Herz(en) mit deinem Kumpel";
            break;
        default: //QuestType.Unknown:
            $msg = "Unbekannt";
            break;
    }
    return sprintf($msg, $target);
}
function get_quest_conditions($conditions) {
    global $pokedex;
    $count = count($conditions);
    $quest_conditions = [];
    for ($i = 0; $i < $count; $i++) {
        $condition = $conditions[$i];
        switch ($condition->type) {
            case 16://BadgeType
                break;
            case 25://ThrowTypeInARow
                array_push($quest_conditions, sprintf("%s km Entfernung", number_format($condition->info->distance,0,' ','.')));
                break;
            case 15://CurveBall
                array_push($quest_conditions, "Curveball");
                break;
            case 4://DailyCaptureBonus
                array_push($quest_conditions, "Tägliches Pokemon");
                break;
            case 5://DailySpinBonus
                array_push($quest_conditions, "Täglicher Stop");
                break;
            case 22://NPCBattle
                array_push($quest_conditions, "Teamleiter-Kampf");
                break;
            case 23://PvPBattle
                array_push($quest_conditions, "PVP-Kampf");
                break;
            case 20://DaysInARow
                break;
            case 11://Item
                array_push($quest_conditions, "Benutze Items");
                break;
            case 19://NewFriend
                break;
            case 17://PlayerLevel
                array_push($quest_conditions, "Erreiche Level");
                break;
            case 2://PokemonCategory
                $pkmn = [];
                foreach ($condition->info->pokemon_ids as $pokemon_id) {
                    array_push($pkmn, $pokedex[$pokemon_id]);
                }
                array_push($quest_conditions, join(', ', $pkmn));
                break;
            case 1://PokemonType
                $types = [];
                foreach ($condition->info->pokemon_type_ids as $pokemon_type_id) {
                    array_push($types, get_pokemon_type($pokemon_type_id));
                }
                array_push($quest_conditions, "Typ: ");
                array_push($quest_conditions, join(', ', $types));
                break;
            case 28://PlayerLevel
                array_push($quest_conditions, "Schnappschuss vom Kumpel");
                break;
            case 27://PokemonType
                $chars = [];
                foreach ($condition->info->character_category_ids as $character_category_id) {
                    array_push($chars, get_grunt_character($character_category_id));
                }
                array_push($quest_conditions, "Mitglied: ");
                array_push($quest_conditions, join(', ', $chars));
                break;
            case 0://QuestContext
                break;
            case 0://RaidLevel
                array_push($quest_conditions, join(', ', $condition->info->raid_levels));
                break;
            case 0://SuperEffectiveCharge
                array_push($quest_conditions, "Sehr effektive Lade Attacke");
                break;
            case 0://ThrowType
                array_push($quest_conditions, get_throw_name($condition->info->throw_type_id));
                break;
            case 0://ThrowTypeInARow
                array_push($quest_conditions, sprintf("%s in a row", get_throw_name($condition->info->throw_type_id)));
                break;
            case 0://UniquePokestop
                array_push($quest_conditions, "Einzigartig");
                break;
            case 0://WeatherBoost
                array_push($quest_conditions, "Wetter-Boosted");
                break;
            case 0://WinBattleStatus
                array_push($quest_conditions, "Kampf gewinnen");
                break;
            case 0://WinGymBattleStatus
                array_push($quest_conditions, "Arenenkampf gewinnen");
                break;
            case 0://WinRaidStatus
                array_push($quest_conditions, "Raid gewinnen");
                break;
        }
    }
    return join(" ", $quest_conditions);
}
function get_quest_reward($rewards) {
    global $pokedex;
    $reward = $rewards[0];
    switch ($reward->type) {
        case 5: //AvatarClothing
            return "Avatar Clothing";
        case 4: //Candy
            return sprintf("%s Rare Candy", $reward->info->amount);
        case 1: //Experience
            return sprintf("%s XP", $reward->info->amount);
        case 2: //Item
            return sprintf("%s %s", $reward->info->amount, get_item($reward->info->item_id));
        case 7: //PokemonEncounter
            return $pokedex[$reward->info->pokemon_id];
        case 6: //Quest
            return "Quest";
        case 3: //Stardust
            return sprintf("%s Sternenstaub", $reward->info->amount);
        default:
            return "Unknown";
    }
}
function is_pokemon_reward($rewards) {
    $reward = $rewards[0];
    switch ($reward->type) {
        case 7: //PokemonEncounter
            return true;
        default:
            return false;
    }
}

function get_throw_name($throw_type_id) {
    switch ($throw_type_id) {
        case 13: //CatchCurveThrow
            return "Curveball-Wurf";
        case 12: //CatchExcellentThrow
            return "Fabelhafter Wurf";
        case 9: //CatchFirstThrow
            return "Erster Wurf";
        case 11: //CatchGreatThrow
            return "Großartiger Wurf";
        case 10: //CatchNiceThrow
            return "Guter Wurf";
        default:
            return $throw_type_id;
    }
}
function get_item($item_id) {
    switch ($item_id) {
        case 1://Poke_Ball
            return "x Pokeball";
        case 2://Great_Ball
            return "x Superball";
        case 3://Ultra_Ball
            return "x Hyperball";
        case 4://Master_Ball
            return "x Meisterball";
        case 5://Premier_Ball
            return "x Premierball";
        case 101://Potion
            return "x Tank";
        case 102://Super_Potion
            return "x Supertrank";
        case 103://Hyper_Potion
            return "x Hypertrank";
        case 104://Max_Potion
            return "x Top-Trank";
        case 201://Revive
            return "x Beleber";
        case 202://Max_Revive
            return "x Top-Beleber";
        case 301://Lucky_Egg
            return "x Glücksei";
        case 401://Incense_Ordinary
            return "x Rauch";
        case 402://Incense_Spicy
            return "Incense Spicy";
        case 403://Incense_Cool
            return "Incense Cool";
        case 404://Incense_Floral
            return "Incense Floral";
        case 501://Troy_Disk
            return "x Lockmodul";
        case 502://Troy_Disk
            return "x Lockmodul(Gletscher)";
        case 503://Troy_Disk
            return "x Lockmodul(Moos)";
        case 504://Troy_Disk
            return "x Lockmodul(Magnet)";
        case 602://X_Attack
            return "x X Attack";
        case 603://X_Defense
            return "x X Defense";
        case 604://X_Miracle
            return "x X Miracle";
        case 701://Razz_Berry
            return "x Himmihbeere";
        case 702://Bluk_Berry
            return "x Bluk Berry";
        case 703://Nanab_Berry
            return "x Nanabeere";
        case 704://Wepar_Berry
            return "x Wepar Berry";
        case 705://Pinap_Berry
            return "x Sananabeere";
        case 706://Golden_Razz_Berry
            return "x Goldene Himmihbeere";
        case 707://Golden_Nanab_Berry
            return "x Goldene Nanabeere";
        case 708://Golden_Pinap_Berry
            return "x Silberne Sananabeere";
        case 701://Special_Camera
            return "Special Camera";
        case 901://Incubator_Basic_Unlimited
            return "x Brutmaschine (unbegrenzt)";
        case 902://Incubator_Basic
            return "x Brutmaschine";
        case 903://Incubator_Super
            return "x Superbrutmaschine";
        case 1001://Pokemon_Storage_Upgrade
            return "x Pokemon Lager-Upgrade";
        case 1002://Item_Storage_Upgrade
            return "x Item Lager-Upgrade";
        case 1101://Sun_Stone
            return "x Sonnenstein";
        case 1102://Kings_Rock
            return "x Kingstein";
        case 1103://Metal_Coat
            return "x Metallmantel";
        case 1104://Dragon_Scale
            return "x Drachenklaue";
        case 1105://Upgrade
            return "x Upgrade";
        case 1106://Sinnoh-Stone
            return "Sinnoh-Stein";
        case 1107://Unova-Stone
            return "Unova-Stein";
        case 1201://Move_Reroll_Fast_Attack
            return "x Fast-TM";
        case 1202://Move_Reroll_Special_Attack
            return "x Lade-TM";
        case 1301://Rare_Candy
            return "x Sonderbonbon";
        case 1401://Free_Raid_Ticket
            return "x kostenloser Raidpass";
        case 1402://Paid_Raid_Ticket
            return "x kostenpflichtiger Raidpass";
        case 1403://Legendary_Raid_Ticket
            return "x Legendärer Raidpass";
        case 1404://Star_Piece
            return "x Sternenstück";
        case 1405://Friend_Gift_Box
            return "x Geschenkbox";
        default:
            return "Unbekannt";
    }
}
function get_quest_icon($rewards) {
    global $config;
    $icon_index = 0;
    $reward = $rewards[0];
    switch ($reward->type) {
        case 5://AvatarClothing
            break;
        case 4://Candy
            $icon_index = 1301;
            break;
        case 1://Experience
            $icon_index = -2;
            break;
        case 2://Item
            $icon_index = $reward->info->item_id;
            break;
        case 7://Pokemon
            return sprintf($config['urls']['images']['pokemon'], $reward->info->pokemon_id);
        case 6://Quest
            break;
        case 3://Stardust
            $icon_index = -1;
            break;
        default: //Unset/Unknown
            break;
    }
    return "./static/images/quests/$icon_index.png";
}
function get_pokemon_type($type) {
    switch ($type) {
        case 1:
            return "Normal";
        case 2:
            return "Kampf";
        case 3:
            return "Flug";
        case 4:
            return "Gift";
        case 5:
            return "Boden";
        case 6:
            return "Gestein";
        case 7:
            return "Käfer";
        case 8:
            return "Geist";
        case 9:
            return "Stahl";
        case 10:
            return "Feuer";
        case 11:
            return "Wasser";
        case 12:
            return "Pflanze";
        case 13:
            return "Elektro";
        case 14:
            return "Psycho";
        case 15:
            return "Eis";
        case 16:
            return "Drache";
        case 17:
            return "Unlicht";
        case 18:
            return "Fee";
        default:
            return "Keins";
    }
}

function get_grunt_character($char) {
    switch ($char) {
        case 1:
            return "Teamleiter";
        case 2:
            return "Rüpel";
        case 3:
            return "Arlo";
        case 4:
            return "Cliff";
        case 5:
            return "Sierra";
        case 6:
            return "Giovanni";
        default:
            return "Unbekannt";
    }
}
?>

<link rel="stylesheet" href="./static/css/footerfix.css"/>
<link rel="stylesheet" href="./static/css/themes.css"/>
<script type="text/javascript">
$(document).on("click", ".delete", function(){
  $(this).parents("tr").remove();
  $(".add-new").removeAttr("disabled");
});

var checkbox = $("#chkColumns input:checkbox"); 
var tbl = $("#quest-table");
var tblHead = $("#quest-table th");
checkbox.prop('checked', true); 
checkbox.click(function () {
  var colToHide = tblHead.filter("." + $(this).attr("name"));
  var index = $(colToHide).index();
  tbl.find('tr :nth-child(' + (index + 1) + ')').toggle();
});

if (get("quests-search-input") !== false) {
  $('#search-input').val(get("quests-search-input"));
}
if (get("quests-filter-city") !== false) {
  $('#filter-city').val(JSON.parse(get("quests-filter-city")));
}
if (get("quests-filter-pokestop") !== false) {
  $('#filter-pokestop').val(get("quests-filter-pokestop"));
}

filter_quests();

$('#reset-filters').on('click', function() {
  if (confirm($.i18n('quests_filters_reset_confirm'))) {
    $('#search-input').val('');
    $('#filter-city').val('');
    $('#filter-pokestop').val('');
    filter_quests();
  }
});
</script>