<?php
// RealDeviceMap Database
$dbhost = "127.0.0.1"; // Dtabase host name or IP address
$dbPort = 3306;            // Database port. (default: 3306)
$dbuser = "user";          // Database username.
$dbpass = "password";      // Database user password.
$dbname = "rdmdb";         // Database name.

// Table Style
$table_style = "light"; // light/dark
$table_header_style = "dark"; // light/dark
$table_striped = true; // true/false

//Default unknown value
$unknown_value = "Unknown";

//Google Analytics ID
$google_analytics_id = ""; //e.g. UA-XXXXX-Y

//Google AdSense ID
$google_adsense_id = ""; //e.g. ca-pub-XXXXXXXX

//Table refresh interval in seconds
$table_refresh_s = 60;

//Discord OAuth login
$discord_login = false; //Enable or disable Discord auth login
$discord_bot_client_id = 0; //Discord bot client ID
$discord_bot_client_secret = ""; //Discord bot client secret
$discord_bot_redirect_uri = "https://website/discord-callback.php"; //Callback uri
$discord_invite_link = ""; //Optional Discord invite link to preset the user if not in the guild
$discord_guild_ids = []; //Guild ID the user should be in
$discord_role_ids = []; //Role IDs the user should have
$discord_bot_token = ""; //Discord bot token

$log_discord_users = false;

$paypal_link = "";
$venmo_link = "";
?>