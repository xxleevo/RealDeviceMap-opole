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

        $in_trusted = in_trusted_guild($config['discord']['guildIds'], $guilds);
        if (!$in_trusted) {
            die("You must join Discord server " . $config['discord']['inviteLink']);
        }

        $valid = false;
        $roles = [];
        if (!empty($config['discord']['botToken'])) {
            $discord = new DiscordClient(['token' => $config['discord']['botToken']]); // Token is required
            $count = count($config['discord']['guildIds']);
            for ($i = 0; $i < $count; $i++) {
                $member = $discord->guild->getGuildMember(['guild.id' => $config['discord']['guildIds'][$i], 'user.id' => (int)$user->id]);
                if ($member === null)
                    continue;

                $has_trusted_role = has_trusted_role($config['discord']['roleIds'], $member->roles);
                if (!$has_trusted_role)
                    continue;

                if ($config['discord']['logUsers']) { //Debug
                    file_put_contents('./discord_users.txt', print_r($user, true), FILE_APPEND);
                }

                setcookie("LoginCookie", session_id(), time()+$session_lifetime);
                array_push($roles, $member->roles);
                $valid = true;
                break;
            }
	  
            if (!$has_trusted_role) {
                die("You do not have the required permissions to access this page.");
            }
        }
        if ($valid) {
            $_SESSION['user'] = ["username" => $user->username, "roles" => $roles];
	        header("Location: .");
        }
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