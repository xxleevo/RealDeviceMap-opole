<?php
session_start();

include './config.php';
include './includes/DbConnector.php';
include './includes/utils.php';

define("DEFAULT_LIMIT", 999999);

$pos = !empty($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], getenv('HTTP_HOST'));
if ($pos === false) {
    http_response_code(401);
    die();
}

if (!(isset($_SESSION['token']) && !empty($_SESSION['token']))) {
    die();
}
$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);
if ($data === 0) {
    die();
}

$token = filter_var($data["token"], FILTER_SANITIZE_STRING);
if (!(isset($token) && !empty($token))) {
    die();
}
//TODO: Fix
//if ($_SESSION['token'] !== $token) {
//    die();
//}
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER["HTTP_X_REQUESTED_WITH"] == "XMLHttpRequest")) {
    die();
}

$allowedTables = [
    "pokemon",
    "gym",
    "pokestop",
    "spawnpoint",
    "pokemon_stats",
    "raid_stats",
    "quest_stats"
];

if (!(isset($data['type']) && !empty($data['type']))) {
    if (!(isset($data['table']) && !empty($data['table']) && in_array($data['table'], $allowedTables))) {
        die();
    }

    $table = filter_var($data["table"], FILTER_SANITIZE_STRING);
    $limit = filter_var(isset($data["limit"]) ? $data["limit"] : DEFAULT_LIMIT, FILTER_SANITIZE_STRING);
    $db = new DbConnector($config["db"]);
    $pdo = $db->getConnection();
    $sql = "SELECT * FROM $table LIMIT $limit";
    $result = $pdo->query($sql);
    if ($result->rowCount() > 0) {
        $data = $result->fetchAll();
        ini_set('memory_limit', '-1');
        echo json_encode($data);
    } else {
        if ($config["core"]["showDebug"]) {
            echo json_encode(["error" => 1, "message" => "Query returned zero results."]);
        }
    }
    unset($pdo);
    unset($db);
} else {
    $type = filter_var($data["type"], FILTER_SANITIZE_STRING);
    switch ($type) {
        case "dashboard":
            $stat = filter_var($data["stat"], FILTER_SANITIZE_STRING);
            switch ($stat) {
                case "pokemon":
                    $pokemonStats = get_pokemon_stats();
                    $obj = [
                        "pokemon" => $pokemonStats["total"],
                        "active_pokemon" => $pokemonStats["active"],
                        "iv_total" => $pokemonStats["iv_total"],
                        "iv_active" => $pokemonStats["iv_active"],
                        "iv_95_total" => $pokemonStats["iv_95_total"],
                        "iv_95_active" => $pokemonStats["iv_95_active"],
                        "iv_100_active" => $pokemonStats["iv_100_active"],
                        "iv_100_total" => $pokemonStats["iv_100_total"]
                    ];
                    echo json_encode($obj);
                    break;
                case "gyms":
                    $gymStats = get_gym_stats();
                    $gymCount = get_table_count("gym");
                    $raidCount = get_raid_stats();
					
					$gymWhiteActive = get_table_count("gym where updated > (UNIX_TIMESTAMP()-14400) AND team_id = 0");
					$gymBlueActive = get_table_count("gym where updated > (UNIX_TIMESTAMP()-14400) AND team_id = 1");
					$gymRedActive = get_table_count("gym where updated > (UNIX_TIMESTAMP()-14400) AND team_id = 2");
					$gymYellowActive = get_table_count("gym where updated > (UNIX_TIMESTAMP()-14400) AND team_id = 3");
					
					$totalActive = ($gymWhiteActive + $gymBlueActive + $gymRedActive + $gymYellowActive);
					$percentWhiteActive = (($gymWhiteActive/$totalActive)*100);
					$percentBlueActive = (($gymBlueActive/$totalActive)*100);
					$percentRedActive = (($gymRedActive/$totalActive)*100);
					$percentYellowActive = (($gymYellowActive/$totalActive)*100);
					
					$raidHatchedCountTotal = get_table_count("gym where raid_level >= 1 AND raid_level <= 5 AND raid_battle_timestamp < UNIX_TIMESTAMP() AND raid_end_timestamp > UNIX_TIMESTAMP()");
					$raidHatchedCountNormal = get_table_count("gym where raid_level >= 1 AND raid_level < 5 AND raid_battle_timestamp < UNIX_TIMESTAMP() AND raid_end_timestamp > UNIX_TIMESTAMP()");
					$raidHatchedCountLegendary = get_table_count("gym WHERE raid_level = 5 AND raid_battle_timestamp < UNIX_TIMESTAMP()  AND raid_end_timestamp > UNIX_TIMESTAMP()");
					
					$eggCountTotal = get_table_count("gym where raid_level >=1 AND raid_level <=5 AND raid_battle_timestamp > UNIX_TIMESTAMP()");
					$eggCountNormal = get_table_count("gym where raid_level >= 1 AND raid_level < 5 AND raid_battle_timestamp > UNIX_TIMESTAMP()");
					$eggCountLegendary = get_table_count("gym where raid_level = 5 AND raid_battle_timestamp > UNIX_TIMESTAMP()");
                    $obj = [
                        "gyms" => $gymCount,
                        "raids" => $raidCount,
                        "neutral" => $gymStats === 0 ? 0 : count($gymStats) < 4 ? 0 : $gymStats[0],
                        "mystic" => $gymStats === 0 ? 0 : $gymStats[1],
                        "valor" => $gymStats === 0 ? 0 : $gymStats[2],
                        "instinct" => $gymStats === 0 ? 0 : $gymStats[3],
						"neutralActive" => $gymWhiteActive,
						"neutralActivePercent" => $percentWhiteActive,
						"mysticActivePercent" => $percentBlueActive,
						"valorActivePercent" => $percentRedActive,
						"instinctActivePercent" => $percentYellowActive,
						"mysticActive" => $gymBlueActive,
						"valorActive" => $gymRedActive,
						"instinctActive" => $gymYellowActive,
						"hatchedRaids" => $raidHatchedCountTotal,
						"hatchedNormalRaids" => $raidHatchedCountNormal,
						"hatchedLegendaryRaids" => $raidHatchedCountLegendary,
						"eggs" => $eggCountTotal,
						"eggsNormal" => $eggCountNormal,
						"eggsLegendary" => $eggCountLegendary,
                    ];
                    echo json_encode($obj);
                    break;
                case "pokestops":
                    $stopStats = get_pokestop_stats();
                    $obj = [
                        "pokestops" => $stopStats === 0 ? 0 : $stopStats["total"],
                        "lured" => $stopStats === 0 ? 0 : $stopStats["lured"],
                        "quests" => $stopStats === 0 ? 0 : $stopStats["quests"],
                        "invasions" => $stopStats === 0 ? 0 : $stopStats["invasions"],
                    ];
                    echo json_encode($obj);
                    break;
                case "weather":
                    $weatherStats = get_weather_stats();
                    $obj = [
                        "total" => $weatherStats === 0 ? 0 : $weatherStats["total"],
                        "noBoost" => $weatherStats === 0 ? 0 : $weatherStats["noBoost"],
                        "clear" => $weatherStats === 0 ? 0 : $weatherStats["clear"],
                        "rain" => $weatherStats === 0 ? 0 : $weatherStats["rain"],
                        "partlyCloudy" => $weatherStats === 0 ? 0 : $weatherStats["partlyCloudy"],
                        "cloudy" => $weatherStats === 0 ? 0 : $weatherStats["cloudy"],
                        "windy" => $weatherStats === 0 ? 0 : $weatherStats["windy"],
                        "snow" => $weatherStats === 0 ? 0 : $weatherStats["snow"],
                        "fog" => $weatherStats === 0 ? 0 : $weatherStats["fog"],
                    ];
                    echo json_encode($obj);
                    break;
                case "tth":
                    $spawnpointStats = get_spawnpoint_stats();
                    $obj = [
                        "tth_total" => $spawnpointStats === 0 ? 0 : $spawnpointStats["total"],
                        "tth_found" => $spawnpointStats === 0 ? 0 : $spawnpointStats["found"],
                        "tth_missing" => $spawnpointStats === 0 ? 0 : $spawnpointStats["missing"],
                        //"tth_30min" => $spawnpointStats === 0 ? 0 : $spawnpointStats["min30"],
                        //"tth_60min" => $spawnpointStats === 0 ? 0 : $spawnpointStats["min60"]
                        //"tth_percentage" => $spawnpointStats === 0 ? 0 : $spawnpointStats["percentage"],
                    ];
                    echo json_encode($obj);
                    break;
                case "top":
                    $top10Pokemon = get_top_pokemon(10);
                    $top10PokemonIV = get_top_pokemon_iv(10);
                    $top10PokemonLifetime = get_top_pokemon_lifetime(10);
					$top10PokemonIV95 = get_top_pokemon_iv95(10);
					$top10PokemonIV100 = get_top_pokemon_iv100(10);
                    $obj = [
                        "top10_pokemon" => $top10Pokemon,
                        "top10_pokemon_iv" => $top10PokemonIV,
                        "top10_pokemon_lifetime" => $top10PokemonLifetime,
                        "top10_pokemon_iv95" => $top10PokemonIV95,
                        "top10_pokemon_iv100" => $top10PokemonIV100
                    ];
                    echo json_encode($obj);
                    break;
                case "shinyToday":
                    $shinyRates = get_shiny_rates();
                    $obj = [
                        "shiny_rates" => $shinyRates
                    ];
                    echo json_encode($obj);
                    break;
                case "shinyAlltime":
					$shinyRatesTotal = get_shiny_rates_total();
                    $obj = [
						"shiny_rates_total" => $shinyRatesTotal
                    ];
                    echo json_encode($obj);
                    break;
                case "new":
					$dateStops = [8,2019];
					if(isset($config['ui']['pages']['dashboard']['newPokestopsMinDate'])){
						$dateStops = $config['ui']['pages']['dashboard']['newPokestopsMinDate'];
					}
						$dateGyms = [8,2019];
					if(isset($config['ui']['pages']['dashboard']['newGymsMinDate'])){
						$dateGyms = $config['ui']['pages']['dashboard']['newGymsMinDate'];
					}
                    $newPokestops = get_new_pokestops($dateStops);
                    $newGyms = get_new_gyms($dateGyms);
                    $obj = [
                        "new_stops" => $newPokestops,
                        "new_gyms" => $newGyms
                    ];
                    echo json_encode($obj);
                    break;
            }
            break;
        case "nests":
            $coords = $data["data"]["coordinates"];
            $spawnpoints = getSpawnpointNestData($coords);
            $pokestops = getPokestopNestData($coords);
            $args = [
                "spawn_ids" => $spawnpoints, 
                "pokestop_ids" => $pokestops, 
                "nest_migration_timestamp" => $data["data"]["nest_migration_timestamp"], 
                "spawn_report_limit" => $data["data"]["spawn_report_limit"]
            ];
            try {
                getSpawnData($args);
            } catch (Exception $e) {
                echo json_encode(["error" => true, "message" => $e]);
            }
            break;
        case "stats":
            $stats = getSpawnDataReport($data["start"], $data["end"], $data["pokemon_id"]);
            echo json_encode($stats);
            break;
        default:
            die();
    }
}

function getSpawnDataReport($start, $end, $pokemon_id) {
    $sql = "
SELECT
  COUNT(id) AS total,
  SUM(iv = 100) AS iv100,
  SUM(iv = 0) AS iv0,
  SUM(iv > 0) AS with_iv,
  SUM(iv IS NULL) AS without_iv,
  SUM(iv > 90 AND iv < 100) AS iv90,
  SUM(iv >= 1 AND iv < 50) AS iv_1_49,
  SUM(iv >= 50 AND iv < 80) AS iv_50_79,
  SUM(iv >= 80 AND iv < 90) AS iv_80_89,
  SUM(iv >= 90 AND iv < 100) AS iv_90_99,
  SUM(iv >= 1 AND iv < 11) AS iv_1_10,
  SUM(iv >= 11 AND iv < 21) AS iv_11_20,
  SUM(iv >= 21 AND iv < 31) AS iv_21_30,
  SUM(iv >= 31 AND iv < 41) AS iv_31_40,
  SUM(iv >= 41 AND iv < 51) AS iv_41_50,
  SUM(iv >= 51 AND iv < 61) AS iv_51_60,
  SUM(iv >= 61 AND iv < 71) AS iv_61_70,
  SUM(iv >= 71 AND iv < 81) AS iv_71_80,
  SUM(iv >= 81 AND iv < 91) AS iv_81_90,
  SUM(iv >= 91 AND iv < 100) AS iv_91_99,
  SUM(gender = 1) AS male,
  SUM(gender = 2) AS female,
  SUM(gender = 3) AS genderless,
  SUM(level >= 1 AND level <= 9) AS level_1_9,
  SUM(level >= 10 AND level <= 19) AS level_10_19,
  SUM(level >= 20 AND level <= 29) AS level_20_29,
  SUM(level >= 30 AND level <= 35) AS level_30_35
FROM
  pokemon
WHERE
  pokemon_id = $pokemon_id
  AND first_seen_timestamp >= $start
  AND first_seen_timestamp <= $end
";
    return execute($sql, PDO::FETCH_OBJ);
}

function getSpawnData($args) {
    global $config;
    $binds = array();

    if (isset($args["spawn_ids"]) || isset($args["pokestop_ids"])) {
        if (isset($args["spawn_ids"]) && count($args["spawn_ids"]) > 0) {
            $spawns_in  = str_repeat('?,', count($args["spawn_ids"]) - 1) . '?';
            $binds = array_merge($binds, $args["spawn_ids"]);
        }
        if (isset($args["pokestop_ids"]) && count($args["pokestop_ids"]) > 0) {
            $stops_in  = str_repeat('?,', count($args["pokestop_ids"]) - 1) . '?';
            $binds = array_merge($binds, $args["pokestop_ids"]);
        }
      
        if ($stops_in && $spawns_in) {
            $points_string = "(pokestop_id IN (" . $stops_in . ") OR spawn_id IN (" . $spawns_in . "))";
        } else if ($stops_in) {
            $points_string = "pokestop_id IN (" . $stops_in . ")";
        } else if ($spawns_in) {
            $points_string = "spawn_id IN (" . $spawns_in . ")";
        } else {
            echo json_encode(array('spawns' => null, 'status'=>'Error: no points!'));
            return;
        }
        if (is_numeric($args["nest_migration_timestamp"]) && (int)$args["nest_migration_timestamp"] == $args["nest_migration_timestamp"]) {
            $ts = $args["nest_migration_timestamp"];
        } else {
            $ts = 0;
        }
        $binds[] = $ts;

        if (is_numeric($args["spawn_report_limit"]) && (int)$args["spawn_report_limit"] == $args["spawn_report_limit"] && (int)$args["spawn_report_limit"] != 0) {
            $limit = " LIMIT " . $args["spawn_report_limit"];
        } else {
            $limit = '';
        }    

        $sql_spawn = "
SELECT
  pokemon_id,
  COUNT(pokemon_id) AS count
FROM
  pokemon
WHERE " . $points_string . "
  AND first_seen_timestamp >= ?
GROUP BY
  pokemon_id
ORDER BY
  count DESC" . $limit;
        $db = new DbConnector($config['db']);
        $pdo = $db->getConnection();
        $stmt = $pdo->prepare($sql_spawn);
        try {
            $stmt->execute($binds);
        } catch (PDOException $e) {
            echo json_encode(["error" => true, "message" => $e]);
        } 

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        unset($pdo);
        unset($db);
        echo json_encode(array('spawns' => $result, 'sql' => $sql_spawn));
    } else {
        echo json_encode(["error" => true, "message" => "No data provided."]);
    }
}

function getSpawnpointNestData($coords) {
    $sql = "
SELECT
  id
FROM
  spawnpoint
WHERE
  ST_CONTAINS(ST_GEOMFROMTEXT('POLYGON(($coords))'), point(spawnpoint.lat, spawnpoint.lon))
";
    return execute($sql, PDO::FETCH_COLUMN);
}

function getPokestopNestData($coords) {
    $sql = "
SELECT
  id
FROM
  pokestop
WHERE
  ST_CONTAINS(ST_GEOMFROMTEXT('POLYGON(($coords))'), point(pokestop.lat, pokestop.lon))
";
    return execute($sql, PDO::FETCH_COLUMN);
}
?>