<?php
session_start();

include './config.php';
include './includes/DbConnector.php';
include './includes/utils.php';

define("DEFAULT_LIMIT", 999999);

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
$token = filter_input(INPUT_GET, "token", FILTER_SANITIZE_STRING);
if (!(isset($token) && !empty($token))) {
    die();
}
if ($_SESSION['token'] !== $token) {
    die();
}
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER["HTTP_X_REQUESTED_WITH"] == "XMLHttpRequest")) {
    die();
}

//TODO: Sanitize user input

if (!(isset($_GET['type']) && !empty($_GET['type']))) {
    if (!(isset($_GET['table']) && !empty($_GET['table']))) {
        die();
    }

    $table = filter_input(INPUT_GET, "table", FILTER_SANITIZE_STRING);
    $limit = filter_input(INPUT_GET, "limit", FILTER_SANITIZE_STRING);
    $limit = isset($limit) && !empty($limit) ? $limit : DEFAULT_LIMIT;
    $db = new DbConnector($config['db']);
    $pdo = $db->getConnection();
    $sql = "SELECT * FROM " . $config['db']['dbname'] . ".$table LIMIT $limit";
    $result = $pdo->query($sql);
    if ($result->rowCount() > 0) {
        $data = $result->fetchAll();
        echo json_encode($data);
    } else {
        if ($config['core']['showDebug']) {
            echo "Query returned zero results.";
        }
    }
    unset($pdo);
    unset($db);
} else {
    $db = new DbConnector($config['db']);
    $pdo = $db->getConnection();
    $type = $_GET['type'];
    switch ($type) {
        case "dashboard":
            $gymStats = get_gym_stats();
            $stopStats = get_pokestop_stats();
            $pokemonCount = get_table_count("pokemon");
            $activePokemonCount = get_table_count("pokemon WHERE expire_timestamp > UNIX_TIMESTAMP()");
			$ivScannedPokemonCount = get_table_count("pokemon WHERE expire_timestamp > UNIX_TIMESTAMP() AND iv is not null");
            $gymCount = get_table_count("gym");
            $raidCount = get_raid_stats();
			$legendaryRaidCount = get_table_count("gym WHERE raid_level = 5 AND raid_end_timestamp > UNIX_TIMESTAMP()");
            $obj = [
                "pokemon" => $pokemonCount,
                "active_pokemon" => $activePokemonCount,
				"iv_pokemon" => $ivScannedPokemonCount,
                "gyms" => $gymCount,
                "raids" => $raidCount,
                "legendaryRaids" => $legendaryRaidCount,
                "neutral" => $gymStats === 0 ? 0 : count($gymStats) < 4 ? 0 : $gymStats[0],
                "mystic" => $gymStats === 0 ? 0 : $gymStats[1],
                "valor" => $gymStats === 0 ? 0 : $gymStats[2],
                "instinct" => $gymStats === 0 ? 0 : $gymStats[3],
                "pokestops" => $stopStats === 0 ? 0 : $stopStats["total"],
                "lured" => $stopStats === 0 ? 0 : $stopStats["lured"],
                "quests" => $stopStats === 0 ? 0 : $stopStats["quests"]
            ];
            echo json_encode($obj);
            break;
    }
    unset($pdo);
    unset($db);
}
?>