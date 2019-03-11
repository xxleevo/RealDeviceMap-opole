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

/*
if (isset($_SERVER['HTTP_ORIGIN'])) {
    $address = 'https://' . $_SERVER['SERVER_NAME'];
    if (strpos($address, $_SERVER['HTTP_ORIGIN']) !== 0) {
        die("A");
    }
} else {
    die("B");
}

if (isset($_SERVER['HTTP_REFERER'])) {
    $address = 'https://' . $_SERVER['SERVER_NAME'];
    if (strpos($address, $_SERVER['HTTP_REFERER']) !== 0) {
        die("C");
    }
} else {
    die("D");
}
*/

if (!(isset($_SESSION['token']) && !empty($_SESSION['token']))) {
    die();
}
$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

$token = filter_var($data["token"], FILTER_SANITIZE_STRING);
if (!(isset($token) && !empty($token))) {
    die();
}
if ($_SESSION['token'] !== $token) {
    die();
}
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
    $sql = "SELECT * FROM " . $config["db"]["dbname"] . ".$table LIMIT $limit";
    $result = $pdo->query($sql);
    if ($result->rowCount() > 0) {
        $data = $result->fetchAll();
        echo json_encode($data);
    } else {
        if ($config["core"]["showDebug"]) {
            echo json_encode(["error" => 1, "message" => "Query returned zero results."]);
        }
    }
    unset($pdo);
    unset($db);
} else {
    $db = new DbConnector($config["db"]);
    $pdo = $db->getConnection();
    $type = filter_var($data["type"], FILTER_SANITIZE_STRING);
    switch ($type) {
        case "dashboard":
            $gymStats = get_gym_stats();
            $stopStats = get_pokestop_stats();
            $pokemonStats = get_pokemon_stats();
            $gymCount = get_table_count("gym");
            $raidCount = get_raid_stats();
            $spawnpointStats = get_spawnpoint_stats();
            $top10Pokemon = get_top_pokemon(10);
            $obj = [
                "pokemon" => $pokemonStats["total"],
                "active_pokemon" => $pokemonStats["active"],
                "iv_total" => $pokemonStats["iv_total"],
                "iv_active" => $pokemonStats["iv_active"],
                "gyms" => $gymCount,
                "raids" => $raidCount,
                "neutral" => $gymStats === 0 ? 0 : count($gymStats) < 4 ? 0 : $gymStats[0],
                "mystic" => $gymStats === 0 ? 0 : $gymStats[1],
                "valor" => $gymStats === 0 ? 0 : $gymStats[2],
                "instinct" => $gymStats === 0 ? 0 : $gymStats[3],
                "pokestops" => $stopStats === 0 ? 0 : $stopStats["total"],
                "lured" => $stopStats === 0 ? 0 : $stopStats["lured"],
                "quests" => $stopStats === 0 ? 0 : $stopStats["quests"],
                "tth_total" => $spawnpointStats === 0 ? 0 : $spawnpointStats["total"],
                "tth_found" => $spawnpointStats === 0 ? 0 : $spawnpointStats["found"],
                "tth_missing" => $spawnpointStats === 0 ? 0 : $spawnpointStats["missing"],
                "tth_percentage" => $spawnpointStats === 0 ? 0 : $spawnpointStats["percentage"],
                "top10_pokemon" => $top10Pokemon
            ];
            echo json_encode($obj);
            break;
        default:
            die();
    }
    unset($pdo);
    unset($db);
}
?>