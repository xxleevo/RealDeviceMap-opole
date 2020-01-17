<?php
require_once './config.php';
require_once './includes/DbConnector.php';

function get_team($team_id) {
    switch ($team_id) {
        case "1": return "Mystic";
        case "2": return "Valor";
        case "3": return "Instinct";
        default:  return "Neutral";
    }
}
function get_gym_stats() {
    global $config;
    $db = new DbConnector($config['db']);
    $pdo = $db->getConnection();
    $sql = "
SELECT team_id AS team, COUNT(id) AS count
FROM gym
GROUP BY team
";
    $result = $pdo->query($sql);
    if ($result->rowcount() > 0) {
        $data = $result->fetchAll(PDO::FETCH_KEY_PAIR);
    }
    unset($pdo);
    unset($db);
 
    return $data;
}

function get_shiny_rates_grouped($limit) {
    global $config;
    $db = new DbConnector($config['db']);
    $pdo = $db->getConnection();
    $where = " ";
    $sql = "
SELECT * FROM (
    SELECT pokemon_id as pokeid, count
    FROM pokemon_shiny_stats
	WHERE date = CURDATE()
	ORDER BY 2 DESC
    LIMIT $limit
) AS A
";
    $result = $pdo->query($sql);
    $data = null;
    if ($result->rowCount() > 0) {
        $data = $result->fetchAll();
    }
    unset($pdo);
    unset($db);
    return $data;
}


function get_shiny_rates_mode_grouped($limit = 10) {
    global $config;
    $db = new DbConnector($config['db']);
    $pdo = $db->getConnection();
    $where = " ";
    $sql = "
SELECT * FROM (
    SELECT pokemon_id as pokeid, form as pokeform, shiny_count as count
    FROM shiny_stats
	WHERE date = CURDATE()
	ORDER BY 3 DESC
    LIMIT $limit
) AS A
";
    $result = $pdo->query($sql);
    $data = null;
    if ($result->rowCount() > 0) {
        $data = $result->fetchAll();
    }
    unset($pdo);
    unset($db);
    return $data;
}

function get_shiny_rates_shinypage($order) {
    global $config;
    $db = new DbConnector($config['db']);
    $pdo = $db->getConnection();
    $where = " WHERE shiny = 1 AND first_seen_timestamp >= UNIX_TIMESTAMP(CURDATE())";
    $sql = "
SELECT
  pokemon_id as pokeid,
  COUNT(pokemon_id) AS count,
  form as pokeform,
  (SELECT count(pokemon_id) FROM pokemon p where p.pokemon_id=pokeid AND p.form=pokeform AND shiny is not null AND first_seen_timestamp >= UNIX_TIMESTAMP(CURDATE())) as total,
  ((SELECT count(pokemon_id) FROM pokemon p where p.pokemon_id=pokeid AND p.form=pokeform AND shiny is not null AND first_seen_timestamp >= UNIX_TIMESTAMP(CURDATE()))/COUNT(pokemon_id)) as rate
FROM
  pokemon
$where
GROUP BY
  pokemon_id, form
";
    // apply order
    switch ($order) {
        case 0:
            $sql .= " ORDER BY pokemon_id ASC;";
            break;
        case 1:
            $sql .= " ORDER BY pokemon_id DESC;";
            break;
        case 2:
            $sql .= " ORDER BY rate ASC;
            ";
            break;
        case 3:
            $sql .= " ORDER BY rate DESC;";
            break;
    }
	
    $result = $pdo->query($sql);
    $data = null;
    if ($result->rowCount() > 0) {
        $data = $result->fetchAll();
    }
    unset($pdo);
    unset($db);
    return $data;
}

function get_shiny_rates_total_mode_custom($limit = 10) {
    global $config;
    $db = new DbConnector($config['db']);
    $pdo = $db->getConnection();
    $sql = "
SELECT
	pokemon_id AS pokeid,
	SUM(shiny_count) as count,
	SUM(count) as total,
	(SUM(count)/SUM(shiny_count)) as rate,
	form as pokeform
FROM shiny_stats
WHERE shiny_count > 0
GROUP BY pokemon_id, form
HAVING SUM(shiny_count) > 0
ORDER BY count DESC
LIMIT 10
";
    $result = $pdo->query($sql);
    $data = null;
    if ($result->rowCount() > 0) {
        $data = $result->fetchAll();
    }
    unset($pdo);
    unset($db);
    return $data;
}

function get_shiny_rates_total_custom_shinypage($order) {
    global $config;
    $db = new DbConnector($config['db']);
    $pdo = $db->getConnection();
    $sql = "
SELECT
	pokemon_id AS pokeid,
	SUM(shiny_count) as count,
	SUM(count) as total,
	(SUM(count)/SUM(shiny_count)) as rate,
	form as pokeform
FROM shiny_stats
WHERE shiny_count > 0
GROUP BY pokemon_id, form
HAVING SUM(shiny_count) > 0
";
    // apply order
    switch ($order) {
        case 0:
            $sql .= " ORDER BY pokemon_id ASC;";
            break;
        case 1:
            $sql .= " ORDER BY pokemon_id DESC;";
            break;
        case 2:
            $sql .= " ORDER BY rate ASC;
            ";
            break;
        case 3:
            $sql .= " ORDER BY rate DESC;";
            break;
    }
	
    $result = $pdo->query($sql);
    $data = null;
    if ($result->rowCount() > 0) {
        $data = $result->fetchAll();
    }
    unset($pdo);
    unset($db);
    return $data;
}

function get_shiny_rates_total_grouped($limit = 10) {
    global $config;
    $db = new DbConnector($config['db']);
    $pdo = $db->getConnection();
    $sql = "
SELECT stats.pokemon_id as pokeid, IFNULL(SUM(shiny_stats.count), 0) as count, SUM(stats.count) as total, SUM(stats.count) / IFNULL(SUM(shiny_stats.count), 0) as rate
FROM pokemon_iv_stats as stats
LEFT JOIN pokemon_shiny_stats as shiny_stats on stats.pokemon_id = shiny_stats.pokemon_id AND stats.date = shiny_stats.date
GROUP BY stats.pokemon_id
HAVING SUM(shiny_stats.count) > 0
ORDER BY count DESC
LIMIT $limit
";
    $result = $pdo->query($sql);
    $data = null;
    if ($result->rowCount() > 0) {
        $data = $result->fetchAll();
    }
    unset($pdo);
    unset($db);
    return $data;
}

function get_shiny_rates_total_shinypage($order) {
    global $config;
    $db = new DbConnector($config['db']);
    $pdo = $db->getConnection();
    $sql = "
SELECT stats.pokemon_id as pokeid, 
       IFNULL(SUM(shiny_stats.count), 0) as count, 
       SUM(stats.count) as total, 
       SUM(stats.count) / IFNULL(SUM(shiny_stats.count), 0) as rate
FROM pokemon_iv_stats as stats
LEFT JOIN pokemon_shiny_stats as shiny_stats on stats.pokemon_id = shiny_stats.pokemon_id AND stats.date = shiny_stats.date
GROUP BY stats.pokemon_id
HAVING SUM(shiny_stats.count) > 0
";
    // apply order
    switch ($order) {
        case 0:
            $sql .= " ORDER BY pokeid ASC;";
            break;
        case 1:
            $sql .= " ORDER BY pokeid DESC;";
            break;
        case 2:
            $sql .= " ORDER BY rate ASC;
            ";
            break;
        case 3:
            $sql .= " ORDER BY rate DESC;";
            break;
    }

    $result = $pdo->query($sql);
    $data = null;
    if ($result->rowCount() > 0) {
        $data = $result->fetchAll();
    }
    unset($pdo);
    unset($db);
    return $data;
}

function get_pokestop_stats_grouped() {
    global $config;
    $db = new DbConnector($config['db']);
    $pdo = $db->getConnection();
    $sql = "
SELECT * FROM (
    SELECT COUNT(*) as total_pokestops, SUM(lure_expire_timestamp > UNIX_TIMESTAMP()) as lured_pokestops, COUNT(quest_reward_type) as quest_pokestops, SUM(incident_expire_timestamp > UNIX_TIMESTAMP()) as invasion_pokestops
    FROM pokestop
) AS A
";
    $result = $pdo->query($sql);
    if ($result->rowCount() > 0) {
        $data = $result->fetchAll()[0];
    }
    unset($pdo);
    unset($db);
  
    return $data;
}
  
function get_raid_stats() {
    global $config;
    $db = new DbConnector($config['db']);
    $pdo = $db->getConnection();
    $sql = "
SELECT
  COUNT(id)
FROM
  gym
WHERE
  raid_pokemon_id IS NOT NULL
  AND name IS NOT NULL
  AND raid_end_timestamp >= UNIX_TIMESTAMP()
";
    $count = $pdo->query($sql)->fetchColumn();
    unset($pdo);
    unset($db);
      
    return $count;
}

function get_weather_stats_grouped() {
    global $config;
    $db = new DbConnector($config['db']);
    $pdo = $db->getConnection();
    $sql = "
SELECT * FROM (
    SELECT SUM(weather IS NOT NULL) as total_weatherboosted, SUM(weather = 0) as noBoost, SUM(weather = 1) as clear, SUM(weather = 2) as rain, SUM(weather = 3) as partlyCloudy, SUM(weather = 4) as cloudy, SUM(weather = 5) as windy, SUM(weather = 6) as snowy, SUM(weather = 7) as fog
    FROM pokemon
	WHERE expire_timestamp >= UNIX_TIMESTAMP()
) AS A
";
    $result = $pdo->query($sql);
    if ($result->rowCount() > 0) {
        $data = $result->fetchAll()[0];
    }
    unset($pdo);
    unset($db);
  
    return $data;
}
  
function get_table_count($table) {
    global $config;
    $db = new DbConnector($config['db']);
    $pdo = $db->getConnection();
    $sql = "SELECT count(id) FROM $table";
    $count = $pdo->query($sql)->fetchColumn();
    unset($pdo);
    unset($db);
    
    return $count;
}

function get_table_count_noId($table) {
    global $config;
    $db = new DbConnector($config['db']);
    $pdo = $db->getConnection();
    $sql = "SELECT count(*) FROM " . $config['db']['dbname'] . ".$table";
    $count = $pdo->query($sql)->fetchColumn();
    unset($pdo);
    unset($db);
    
    return $count;
}
function get_online_devices($timeout) {
    global $config;
    $db = new DbConnector($config['db']);
    $pdo = $db->getConnection();
    $sql = "SELECT count(*) FROM " . $config['db']['dbname'] . ".device WHERE last_seen > UNIX_TIMESTAMP()-$timeout";
    $count = $pdo->query($sql)->fetchColumn();
    unset($pdo);
    unset($db);
    
    return $count;
}
function get_online_quest_devices($questinstance,$timeout) {
    global $config;
    $db = new DbConnector($config['db']);
    $pdo = $db->getConnection();
    $sql = "SELECT count(*) FROM " . $config['db']['dbname'] . ".device WHERE instance_name LIKE '%" . $config['ui']['pages']['dashboard']['QuestInstance'] . "%' AND last_seen > UNIX_TIMESTAMP()-$timeout";
    $count = $pdo->query($sql)->fetchColumn();
    unset($pdo);
    unset($db);
    return $count;
}
function get_devices_count() {
    global $config;
    $db = new DbConnector($config['db']);
    $pdo = $db->getConnection();
    $sql = "SELECT count(*) FROM " . $config['db']['dbname'] . ".device";
    $count = $pdo->query($sql)->fetchColumn();
    unset($pdo);
    unset($db);
    
    return $count;
}
function get_quest_devices_count($questinstance) {
    global $config;
    $db = new DbConnector($config['db']);
    $pdo = $db->getConnection();
    $sql = "SELECT count(*) FROM " . $config['db']['dbname'] . ".device WHERE instance_name LIKE '%" . $config['ui']['pages']['dashboard']['QuestInstance'] . "%'";
    $count = $pdo->query($sql)->fetchColumn();
    unset($pdo);
    unset($db);
    
    return $count;
}
function get_pokestop_objects() {
    global $config;
    $db = new DbConnector($config['db']);
    $pdo = $db->getConnection();
    $sql = "SELECT id,lat,lon,name FROM pokestop";
    $result = $pdo->query($sql)->fetchAll();
    unset($pdo);
    unset($db);
  
    return $result;
}

function get_spawnpoint_stats_grouped() {
    global $config;
    $db = new DbConnector($config['db']);
    $pdo = $db->getConnection();
    $sql = "
SELECT * FROM (
    SELECT COUNT(*) as total_spawnpoints, SUM(despawn_sec IS NULL) as found_spawnpoints, SUM(despawn_sec IS NULL) as missing_spawnpoints
    FROM spawnpoint
) AS A
";
    $result = $pdo->query($sql);
    if ($result->rowCount() > 0) {
        $data = $result->fetchAll()[0];
    }
    unset($pdo);
    unset($db);

    return $data;
}

function get_gym_stats_grouped() {
    global $config;
    $db = new DbConnector($config['db']);
    $pdo = $db->getConnection();
    $sql = "
SELECT * FROM (
    SELECT COUNT(team_id) as active, SUM(team_id = 0) as white_active, SUM(team_id = 1) as blue_active, SUM(team_id = 2) as red_active, SUM(team_id = 2) as yellow_active, (SUM(team_id = 0)/count(team_id)) as white_perc, (SUM(team_id = 1)/count(team_id)*100) as blue_perc, (SUM(team_id = 2)/count(team_id)*100) as red_perc, (SUM(team_id = 3)/count(team_id)*100) as yellow_perc
    FROM gym
	WHERE updated > UNIX_TIMESTAMP()-14400
) AS A
JOIN (
    SELECT count(raid_level) as hatched_total, SUM(raid_level >= 1 AND raid_level < 5) as hatched_normal_total, SUM(raid_level = 5) as hatched_level5_total
    FROM gym
	WHERE raid_battle_timestamp < UNIX_TIMESTAMP() AND raid_end_timestamp > UNIX_TIMESTAMP()
) AS B
JOIN (
    SELECT count(raid_level) as eggs_total, SUM(raid_level >= 1 AND raid_level < 5) as eggs_normal, SUM(raid_level = 5) as eggs_level5
    FROM gym
	WHERE raid_battle_timestamp > UNIX_TIMESTAMP()
) AS C
JOIN (
    SELECT COUNT(id) as raids_total
    FROM gym
    WHERE raid_pokemon_id IS NOT NULL AND raid_end_timestamp > UNIX_TIMESTAMP()
) AS D
JOIN (
    SELECT COUNT(*) as gyms_total, SUM(team_id = 0) as white, SUM(team_id = 1) as blue, SUM(team_id = 2) as red, SUM(team_id = 3) as yellow
    FROM gym
) AS E
  ";
    $result = $pdo->query($sql);
    if ($result->rowCount() > 0) {
        $data = $result->fetchAll()[0];
    }
    unset($pdo);
    unset($db);

    return $data;
}

function get_pokemon_stats_total() {
    global $config;
    $db = new DbConnector($config['db']);
    $pdo = $db->getConnection();
    $sql = "
SELECT * FROM (
    SELECT SUM(count) AS total_today
    FROM pokemon_stats
	WHERE date = CURDATE()
) AS A 
JOIN (
    SELECT SUM(count) AS iv_total_today
    FROM pokemon_iv_stats
	WHERE date = CURDATE()
) AS B
JOIN (
    SELECT SUM(count) AS total_shiny_today
    FROM pokemon_shiny_stats
	WHERE date = CURDATE()
) AS C
JOIN (
    SELECT COUNT(id) AS active, COUNT(iv) AS iv_active, SUM(iv = 100) AS iv_100_active, SUM(iv >= 95 AND iv < 100) AS iv_95_active, SUM(iv = 0) AS iv_0_active, SUM(shiny = 1) AS shiny_active
    FROM pokemon
    WHERE expire_timestamp >= UNIX_TIMESTAMP()
) AS D
JOIN (
    SELECT count(iv) AS iv_total_today, SUM(iv = 100) AS iv_100_total_today, SUM(iv > 95) AS iv_95_total_today, SUM(iv = 0) AS iv_0_total_today
    FROM pokemon
    WHERE first_seen_timestamp >= CURDATE()
) AS E
  ";
    $result = $pdo->query($sql);
    if ($result->rowCount() > 0) {
        $data = $result->fetchAll()[0];
    }
    unset($pdo);
    unset($db);

    return $data;
}

function get_top_pokemon($limit = 10) {
#Select TODAY's Pokemon
    global $config;
    $db = new DbConnector($config['db']);
    $pdo = $db->getConnection();
    $sql = "
SELECT
  pokemon_id,
  form,
  COUNT(pokemon_id) AS count
FROM
  pokemon
WHERE
  first_seen_timestamp >= UNIX_TIMESTAMP(CURDATE())
GROUP BY
  pokemon_id,form
ORDER BY
  3 DESC
LIMIT
  $limit;
";
#  WHERE DATE(DATE_ADD(FROM_UNIXTIME(first_seen_timestamp), INTERVAL 2 HOUR) ) >= CURDATE()
    $result = $pdo->query($sql);
    if ($result->rowCount() > 0) {
        $data = $result->fetchAll();
    }
    unset($pdo);
    unset($db);

    return $data;
}

function get_top_pokemon_iv($limit = 10) {
    global $config;
    $db = new DbConnector($config['db']);
    $pdo = $db->getConnection();
    $sql = "
SELECT
  pokemon_id,
  form,
  COUNT(pokemon_id) AS count
FROM
  pokemon
WHERE 
  iv is not null AND first_seen_timestamp >= UNIX_TIMESTAMP(CURDATE())
GROUP BY
  pokemon_id,form
ORDER BY
  3 DESC
LIMIT
  $limit;
";
# (iv is not null) AND DATE(DATE_ADD(FROM_UNIXTIME(first_seen_timestamp), INTERVAL 2 HOUR) ) >= CURDATE()
    $result = $pdo->query($sql);
    if ($result->rowCount() > 0) {
        $data = $result->fetchAll();
    }
    unset($pdo);
    unset($db);

    return $data;
}

function get_top_pokemon_iv95($limit = 10) {
    global $config;
    $db = new DbConnector($config['db']);
    $pdo = $db->getConnection();
    $sql = "
SELECT
  pokemon_id,
  form,
  COUNT(pokemon_id) AS count
FROM
  pokemon
WHERE 
  iv > 95 AND first_seen_timestamp >= UNIX_TIMESTAMP(CURDATE())
GROUP BY
  pokemon_id,form
ORDER BY
  3 DESC
LIMIT
  $limit;
";
# (iv>95) AND DATE(DATE_ADD(FROM_UNIXTIME(first_seen_timestamp), INTERVAL 2 HOUR) ) >= CURDATE()
    $result = $pdo->query($sql);
    if ($result->rowCount() > 0) {
        $data = $result->fetchAll();
    }
    unset($pdo);
    unset($db);

    return $data;
}

function get_top_pokemon_iv100($limit = 10) {
    global $config;
    $db = new DbConnector($config['db']);
    $pdo = $db->getConnection();
    $sql = "
SELECT
  pokemon_id,
  form,
  COUNT(pokemon_id) AS count
FROM
  pokemon
WHERE 
  iv = 100 AND first_seen_timestamp >= UNIX_TIMESTAMP(CURDATE())
GROUP BY
  pokemon_id,form
ORDER BY
  3 DESC
LIMIT
  $limit;
";
# AND DATE(DATE_ADD(FROM_UNIXTIME(first_seen_timestamp), INTERVAL 2 HOUR) ) >= CURDATE()*/
    $result = $pdo->query($sql);
    if ($result->rowCount() > 0) {
        $data = $result->fetchAll();
    }
    unset($pdo);
    unset($db);

    return $data;
}

function get_top_pokemon_lifetime($limit = 10) {
    global $config;
    $db = new DbConnector($config['db']);
    $pdo = $db->getConnection();
    $sql = "
SELECT
 sum(count) AS count, 
 pokemon_id AS pokemon_id
 FROM pokemon_stats
 GROUP BY
 pokemon_id
 ORDER BY
 count DESC
 LIMIT
  $limit;
";
    $result = $pdo->query($sql);
    if ($result->rowCount() > 0) {
        $data = $result->fetchAll();
    }
    unset($pdo);
    unset($db);

    return $data;
}

function get_new_pokestops($minDate) {
	$minMonth = $minDate[0];
	$minYear = $minDate[1];
	
    global $config;
    $db = new DbConnector($config['db']);
    $pdo = $db->getConnection();
    $sql = "
SELECT
 count(id) AS new, 
 MONTH(from_unixtime(first_seen_timestamp)) AS month,
 YEAR(from_unixtime(first_seen_timestamp)) AS year
 FROM pokestop
 WHERE
 (MONTH(from_unixtime(first_seen_timestamp)) >= " . $minMonth . " AND YEAR(from_unixtime(first_seen_timestamp)) = " . $minYear . ") OR
 (YEAR(from_unixtime(first_seen_timestamp)) > " . $minYear . ") 
 GROUP BY
 month,year
 ORDER BY
 year,month ASC;
";
    $result = $pdo->query($sql);
    if ($result->rowCount() > 0) {
        $data = $result->fetchAll();
    }
    unset($pdo);
    unset($db);

    return $data;
}
function get_new_gyms($minDate) {
	$minMonth = $minDate[0];
	$minYear = $minDate[1];
	
    global $config;
    $db = new DbConnector($config['db']);
    $pdo = $db->getConnection();
    $sql = "
SELECT
 count(id) AS new, 
 MONTH(from_unixtime(first_seen_timestamp)) AS month,
 YEAR(from_unixtime(first_seen_timestamp)) AS year
 FROM gym
 WHERE
 (MONTH(from_unixtime(first_seen_timestamp)) >= " . $minMonth . " AND YEAR(from_unixtime(first_seen_timestamp)) = " . $minYear . ") OR
 (YEAR(from_unixtime(first_seen_timestamp)) > " . $minYear . ") 
 GROUP BY
 month,year
 ORDER BY
 year,month ASC;
";
    $result = $pdo->query($sql);
    if ($result->rowCount() > 0) {
        $data = $result->fetchAll();
    }
    unset($pdo);
    unset($db);

    return $data;
}

function get_raids() {
    global $config;

    $sql = "
SELECT 
  CONVERT_TZ(FROM_UNIXTIME(raid_battle_timestamp)," . "'" . $config['core']['fromTimeZoneOffset'] . "', '" . $config['core']['timeZone'] . "')
    AS starts, 
  CONVERT_TZ(FROM_UNIXTIME(raid_end_timestamp)," . "'" . $config['core']['fromTimeZoneOffset'] . "', '" . $config['core']['timeZone'] . "')
    AS ends, 
  id,
  raid_battle_timestamp,
  raid_end_timestamp,
  lat, 
  lon,
  raid_level,
  raid_pokemon_id, 
  raid_pokemon_move_1,
  raid_pokemon_move_2,
  name,
  team_id,
  ex_raid_eligible,
  updated,
  raid_pokemon_form
FROM
  gym
WHERE
  raid_pokemon_id IS NOT NULL
  AND name IS NOT NULL 
  AND raid_end_timestamp > UNIX_TIMESTAMP()
ORDER BY
  raid_end_timestamp;
";
    return execute($sql, PDO::FETCH_ASSOC);
}

function execute($sql, $mode = PDO::FETCH_ASSOC) {
  global $config;
  $db = new DbConnector($config['db']);
  $pdo = $db->getConnection();
  $result = $pdo->query($sql);
  $data = null;
  if ($result->rowCount() > 0) {
      $data = $result->fetchAll($mode);
  }
  unset($pdo);
  unset($db);

  return $data;
}

function get_raid_image($pokemonId, $raidLevel, $form) {
    global $config;
    if ($pokemonId > 0 && $form == '0') {
        return sprintf($config['urls']['images']['pokemon'], $pokemonId);
    } else if($pokemonId > 0 && $form > 0){
		return str_replace('00.png', $form,(sprintf($config['urls']['images']['pokemon'] . '.png', $pokemonId)));
	}
    return sprintf($config['urls']['images']['egg'], $raidLevel);
}

function is_mobile() {
    $useragent=$_SERVER['HTTP_USER_AGENT'];
    return preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4));  
}

function getMinutesLeft($endTimestamp) {
    $now = new DateTime("now");
    $ends = new DateTime();
    $ends->setTimestamp($endTimestamp);
    $diff = $now->diff($ends);
    $minutes = $diff->format("%I");
    return $minutes;
}

function hasDiscordRole($userRoles, $requiredRoles) {
    if (count($requiredRoles) == 0) {
        return true;
    }

    foreach ($userRoles as $role) {
        if (in_array($role, $requiredRoles)) {
            return true;
        }
    }
    return false;
}

//TODO: Better impl
function getRedirectPage() {
    global $config;
    if ($config['ui']['pages']['dashboard']['enabled'] &&
        !$config['ui']['pages']['pokemon']['enabled'] &&
        !$config['ui']['pages']['raids']['enabled'] &&
        !$config['ui']['pages']['gyms']['enabled'] &&
        !$config['ui']['pages']['quests']['enabled'] &&
        !$config['ui']['pages']['nests']['enabled'] &&
        !$config['ui']['pages']['stats']['enabled']&&
        !$config['ui']['pages']['graphs']['enabled']) {
        return 'dashboard';
    } else if (!$config['ui']['pages']['dashboard']['enabled'] &&
        $config['ui']['pages']['pokemon']['enabled'] &&
        !$config['ui']['pages']['raids']['enabled'] &&
        !$config['ui']['pages']['gyms']['enabled'] &&
        !$config['ui']['pages']['quests']['enabled'] &&
        !$config['ui']['pages']['nests']['enabled'] &&
        !$config['ui']['pages']['stats']['enabled']&&
        !$config['ui']['pages']['graphs']['enabled']) {
        return 'pokemon';
    } else if (!$config['ui']['pages']['dashboard']['enabled'] &&
        !$config['ui']['pages']['pokemon']['enabled'] &&
        $config['ui']['pages']['raids']['enabled'] &&
        !$config['ui']['pages']['gyms']['enabled'] &&
        !$config['ui']['pages']['quests']['enabled'] &&
        !$config['ui']['pages']['nests']['enabled'] &&
        !$config['ui']['pages']['stats']['enabled']&&
        !$config['ui']['pages']['graphs']['enabled']) {
        return 'raids';
    } else if (!$config['ui']['pages']['dashboard']['enabled'] &&
        !$config['ui']['pages']['pokemon']['enabled'] &&
        !$config['ui']['pages']['raids']['enabled'] &&
        $config['ui']['pages']['gyms']['enabled'] &&
        !$config['ui']['pages']['quests']['enabled'] &&
        !$config['ui']['pages']['nests']['enabled'] &&
        !$config['ui']['pages']['stats']['enabled']&&
        !$config['ui']['pages']['graphs']['enabled']) {
        return 'gyms';
    } else if (!$config['ui']['pages']['dashboard']['enabled'] &&
        !$config['ui']['pages']['pokemon']['enabled'] &&
        !$config['ui']['pages']['raids']['enabled'] &&
        !$config['ui']['pages']['gyms']['enabled'] &&
        $config['ui']['pages']['quests']['enabled'] &&
        !$config['ui']['pages']['nests']['enabled'] &&
        !$config['ui']['pages']['stats']['enabled']&&
        !$config['ui']['pages']['graphs']['enabled']) {
        return 'quests';
    } else if (!$config['ui']['pages']['dashboard']['enabled'] &&
        !$config['ui']['pages']['pokemon']['enabled'] &&
        !$config['ui']['pages']['raids']['enabled'] &&
        !$config['ui']['pages']['gyms']['enabled'] &&
        !$config['ui']['pages']['quests']['enabled'] &&
        $config['ui']['pages']['nests']['enabled'] &&
        !$config['ui']['pages']['stats']['enabled']&&
        !$config['ui']['pages']['graphs']['enabled']) {
        return 'nests';
    } else if (!$config['ui']['pages']['dashboard']['enabled'] &&
        !$config['ui']['pages']['pokemon']['enabled'] &&
        !$config['ui']['pages']['raids']['enabled'] &&
        !$config['ui']['pages']['gyms']['enabled'] &&
        !$config['ui']['pages']['quests']['enabled'] &&
        !$config['ui']['pages']['nests']['enabled'] &&
        !$config['ui']['pages']['stats']['enabled']&&
        !$config['ui']['pages']['graphs']['enabled']) {
        return 'stats';
    } else if (!$config['ui']['pages']['dashboard']['enabled'] &&
        !$config['ui']['pages']['pokemon']['enabled'] &&
        !$config['ui']['pages']['raids']['enabled'] &&
        !$config['ui']['pages']['gyms']['enabled'] &&
        !$config['ui']['pages']['quests']['enabled'] &&
        !$config['ui']['pages']['nests']['enabled'] &&
        !$config['ui']['pages']['stats']['enabled']&&
        !$config['ui']['pages']['graphs']['enabled']) {
        return 'graphs';
	} else {
        return '404';
    }
}

?>