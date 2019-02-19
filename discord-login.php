<?php
require_once './vendor/autoload.php';
require_once './config.php';
require_once './includes/DiscordAuth.php';

if ($discord_login) {
  $auth = new DiscordAuth();
  $auth->gotoDiscord();
} else {
  header("Location: .");
}