<?php
include './vendor/autoload.php';
require_once './config.php';
require_once './includes/DiscordAuth.php';

use RestCord\DiscordClient;

try {
  if (isset($_GET['code'])) {
    $auth = new DiscordAuth();
    $auth->handleAuthorizationResponse($_GET);
    $user = json_decode($auth->get("/api/v6/users/@me"));
    $guilds = json_decode($auth->get("/api/v6/users/@me/guilds"));
    $session_lifetime = 60;

    $in_trusted = in_trusted_guild($discord_guild_ids, $guilds);
    if (!$in_trusted) {
      die("You must join Discord server $discord_invite_link");
    }

    $valid = false;
    if (!empty($discord_bot_token)) {
      $discord = new DiscordClient(['token' => $discord_bot_token]); // Token is required
      $count = count($discord_guild_ids);
      for ($i = 0; $i < $count; $i++) {
        $member = $discord->guild->getGuildMember(['guild.id' => $discord_guild_ids[$i], 'user.id' => (int)$user->id]);
        if ($member === null)
          continue;

        $has_trusted_role = has_trusted_role($discord_role_ids, $member->roles);
        if (!$has_trusted_role)
          continue;

        if ($log_discord_users) { //Debug
          file_put_contents('./discord_users.txt', print_r($user, true), FILE_APPEND);
        }

        $_SESSION['user'] = $user->{'username'};

        setcookie("LoginCookie", session_id(), time()+$session_lifetime);
        $valid = true;
        break;
      }
	  
      if (!$has_trusted_role) {
        die("You do not have the required permissions to access this page.");
      }
    }
  }
  if ($valid) {
	header("Location: .");
  }
} catch (Exception $e) {
  file_put_contents('error.log', $e->getMessage(), FILE_APPEND);
  header("Location: ./discord-login.php");
}

function in_trusted_guild($trusted_guild_ids, $guilds) {
  if (count($trusted_guild_ids) == 0) {
    return true;
  }
  foreach ($guilds as $key=>$value) {
    if (in_array($value->id, $trusted_guild_ids)) {
  	  return true;
    }
  }
  return false;
}
function has_trusted_role($trusted_role_ids, $roles) {
  if (count($trusted_role_ids) == 0) {
    return true;
  }
  foreach ($roles as $key) {
    if (in_array($key, $trusted_role_ids)) {
      return true;
    }
  }
  return false;
}