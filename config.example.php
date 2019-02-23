<?php

//Configuration options
$config = [
  "core" => [
    "timeZone" => "America/Denver", //Time zone for PHP pages and MySql data
    "dateTimeFormat" => "m-d-Y h:i:s A", //Date & Time format http://php.net/manual/en/function.date.php
    "showFooter" => false, //Show bottom footer
    "showCopyright" => false, //Show Nintendo/Niantic/Pokemon copyright footer
    "showErrors" => true, //Show PHP errors
    "startupLocation" => [1,1] //Default location to startup any map objects at.
  ],
  "google" => [
    "analyticsId" => "", //Google Analytics Id. e.g. UA-XXXXX-Y
    "adSenseId" => "", //Google AdSense Id. e.g. ca-pub-XXXXXXXX
    "maps" => "https://maps.google.com/maps?q=%s,%s"
  ],
  "ui" => [
    "title" => "RDM-opole", //Website title
    "unknownValue" => "Unknown",
    "table" => [
      "style" => "light", //light/dark
      "headerStyle" => "dark", //light/dark
      "striped" => true, //true/false
      "refreshRateS" => 60 //Refresh raids table data every x seconds.
    ],
    "charts" => [
      "type" => "bar",
      "colors" => [
        "stroke" => "rgba(220,220,220,0.8)",
        "highlightFill" => "rgba(220,220,220,0.75)",
        "highlightStroke" => "rgba(220,220,220,1)",
        "border" => "rgba(200, 200, 200, 0.75)",
        "hoverBackground" => "rgba(200, 200, 200, 1)",
        "hoverBorder" => "rgba(200, 200, 200, 1)"
      ]
    ]
  ],
  "db" => [
    "host" => "127.0.0.1", //Database host name or IP address
    "port" => "3306", //Database port. (default: 3306)
    "user" => "root", //Database username.
    "pass" => "password", //Database user password.
    "dbname" => "rdmdb" //Database name.
  ],
  "urls" => [
    "images" => [
      "pokemon" => "http://example.com/images/pokemon/%s.png", //Pokemon images url
      "egg" => "http://example.com/images/egg/%s.png" //Egg images url
    ],
    "paypal" => "", //PayPal.me link e.g https://paypal.me/username
    "venmo" => "", //Venmo link e.g. https://venmo.com/username
    "patreon" => "" //Patreon link e.g. https://patreon.com/username
  ],
  "discord" => [
    "enabled" => false, //Enable or disable Discord auth login
    "botToken" => "", //Discord bot token
    "botClientId" => 0, //Discord bot client ID
    "botClientSecret" => "", //Discord bot client secret
    "botRedirectUri" => "https://example.com/discord-callback.php", //Callback uri
    "inviteLink" => "", //Optional Discord invite link to preset the user if not in the guild
    "guildIds" => [], //Guild ID(s) the user should be in
    "roleIds" => [], //Role ID(s) the user should have
    "logUsers" => false //Log Discord users that have logged in
  ]
];

//Error reporting
ini_set("error_reporting", $config['core']['showErrors']);
if ($config['core']['showErrors']) {
  error_reporting(E_ALL|E_STRCT);
}
?>