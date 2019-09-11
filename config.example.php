<?php

//DEFAULT FORK Configuration
// Min. Settings for a usage: db-infos, url configuration, title settings 

//Configuration options
$config = [
  "core" => [
    "timeZone" => "Europe/Berlin", //Time zone for PHP pages and MySql data
    "fromTimeZoneOffset" => "UTC", //Time zone offset to convert database time from e.g. UTC, -03:00, +01:00
    "dateTimeFormat" => "m-d-Y h:i:s A", //Date & Time format http://php.net/manual/en/function.date.php
    "showFooter" => true, //Show bottom footer
    "showCopyright" => true, //Show Nintendo/Niantic/Pokemon copyright footer
    "showErrors" => true, //Show PHP errors
    "showDebug" => false, //Shows debug output
    "startupLocation" => [0.0,0.0], //Default location to startup any map objects at
    "startupZoom" => 11, //Default zoom for any map objects
    "maxPokemon" => 649, //Maximum amount of pokemon for Pokemon stats page
    "lastNestMigration" => "2019-03-07T01:00:00Z" //UTC time of last nest migration
  ],
  "db" => [
    "host" => "127.0.0.1", //Database host name or IP address
    "port" => "3306", //Database port. (default: 3306)
    "user" => "", //Database username.
    "pass" => "", //Database user password.
    "dbname" => "", //Database name.
    "charset" => "utf8mb4" //Database character set. (default: utf8mb4)
  ],
  "discord" => [
    "enabled" => false, //Enable or disable Discord auth login
    "botToken" => "", //Discord bot token
    "botClientSecret" => "", //Discord bot client secret
    "botClientId" => 0, //Discord bot client ID
    "botRedirectUri" => "https://example.com/discord-callback.php", //Callback uri
    "inviteLink" => "", //Optional Discord invite link to preset the user if not in the guild
    "guildIds" => [], //Guild ID(s) the user should be in
    "roleIds" => [], //Role ID(s) the user should have
    "logUsers" => false //Log Discord users that have logged in
  ],
  "google" => [
    "analyticsId" => "", //Google Analytics Id. e.g. UA-XXXXX-Y
    "adSenseId" => "", //Google AdSense Id. e.g. ca-pub-XXXXXXXX
    "maps" => "https://maps.google.com/maps?q=%s,%s"
  ],
  "ui" => [
    "title" => "Tabname-Map", //Title for the Tabname
    "title-short" => "Titlename", //Title for the header
	"icon" => "static/logo-default.png", // Logo for the site
	"favicon" => "static/favicon-default.ico", // Favicon for the site
    "locale" => "de", //Set the language (e.g. `en` for English)
    "unknownValue" => "Unbekannt",
    "table" => [
      "style" => "dark", //light/dark
      "headerStyle" => "dark", //light/dark
      "striped" => true, //true/false
      "refreshRateS" => 60, //Refresh raids table data every x seconds.
      "forceRaidCards" => false //Forces new display style on Desktop and Mobile if true, otherwise tables on Desktop and new style on Mobile if false.
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
    ],
    "noRaidsAvailableMessage" => "<b>Raids nicht gefunden</b>, komm morgen wieder sobald die Raids gestartet sind.", //Notice message that's shown when no raids are available for the day.
    "pages" => [
      "dashboard" => [
        "enabled" => true, //Shows/hides dashboard page
		"shinyStatsToday" => false, // Needs your RDM to detect shinys and write them to your db!
		"shinyStatsAlltime" => false, // Needs your DB to have a shiny_stats table in the correct format(see above for the table creation) - you need to feed that table on your own.
									 // If you dont have a shiny_stat table and dont know how to handle that, dont activate ShinyStatsAlltime
		/*Shiny_stats table creation sql
			CREATE TABLE `shiny_stats` (
			`date` date NOT NULL,
			`pokemon_id` smallint(6) unsigned NOT NULL,
			`form` smallint(6) unsigned NOT NULL,
			`count` mediumint(6) unsigned NOT NULL,
			`count_shiny` smallint(6) unsigned NOT NULL,
			PRIMARY KEY (`date`,`pokemon_id`,`form`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
		*/
        "deviceStatus" => true, // Shows the Online status of all devices which are connected to the db(displays online if all devices are up, unstable + % of devices if some devices' last_seen isnt updated, offline if 0 devices havent a proper last_seen)
		"deviceStatusQuests" => false, // Checks the instance from devices and if the last_seen is good enough
		"QuestInstance" => "Quest", // This is a string that your questinstance(s) name must contain to be considered as "questing device"
		"deviceResponseLimit" => 600, // number of seconds, after x seconds the device is specified as "offline" for the status
		"discordRoles" => [] //Requires specific discord role, otherwise leave empty as []
      ],
      "pokemon" => [
        "enabled" => true, //Shows/hides pokemon page
        "discordRoles" => [] //Requires specific discord role, otherwise leave empty as []
      ],
      "raids" => [
        "enabled" => true, //Shows/hides raids page
        "discordRoles" => [], //Requires specific discord role, otherwise leave empty as []
        "ignoreUnknown" => false
      ],
      "gyms" => [
        "enabled" => true, //Shows/hides gyms page
        "discordRoles" => [], //Requires specific discord role, otherwise leave empty as []
        "ignoreUnknown" => false
      ],
      "quests" => [
        "enabled" => true, //Shows/hides gyms page
        "discordRoles" => [], //Requires specific discord role, otherwise leave empty as []
        "ignoreUnknown" => false
      ],
      "pokestops" => [
        "enabled" => false, //Shows/hides gyms page
        "discordRoles" => [] //Requires specific discord role, otherwise leave empty as []
      ],
      "nests" => [
        "enabled" => false, //Shows/hides gyms page
        "discordRoles" => [], //Requires specific discord role, otherwise leave empty as []
        "ignoreUnknown",
        "type" => "pmsf", //osm/pmsf
        "db" => [ //PMSF manualdb information
          "host" => "127.0.0.1", //PMSF database host name or IP address
          "port" => "3306", //PMSF database port. (default: 3306)
          "user" => "", //PMSF database username.
          "pass" => "", //PMSF database user password.
          "dbname" => "", //PMSF database name.
          "charset" => "utf8mb4" //PMSF database character set. (default: utf8mb4)
        ]
      ],
      "stats" => [
        "enabled" => true, //Shows/hides gyms page
        "discordRoles" => [] //Requires specific discord role, otherwise leave empty as []
      ]
    ],
    "navBarIconSize" => [36, 36] //NavBar image icon size e.g. [Width, Height] (36-48 is recommended)
  ],
  "urls" => [
    "map" => "https://myMapDomain.com/", //RealDeviceMap/PMSF/Other url
    "images" => [
      /* Pokemon images url 
        Supports PHP format strings: %s, %d, etc.
        e.g. http://map.example.com/static/img/pokemon/%s.png for RDM backend images
        e.g. http://map.example.com/static/img/pokemon/%03d.png for 3 digit file names
      */
      "pokemon" => "https://domain.com/static/monster-icons/%03d.png", 
	  // for a quick setup, just replace domain.com with your stats-domain(wont work for shiny stats images, shiny stats needs an icon set with %ID_%FORM (19_00) for example
      /* Raid egg images url
        Supports PHP format strings: %s, %d, etc.
        e.g. http://map.example.com/static/img/egg/%s.png for RDM backend images
      */
      "egg" => "https://domain.com/static/egg-icons/%s.png" // for a quick setup, just replace domain.com with your stats-domain
    ],
    "paypal" => "", //PayPal.me link e.g https://paypal.me/username
    "venmo" => "", //Venmo link e.g. https://venmo.com/username
    "patreon" => "" //Patreon link e.g. https://patreon.com/username
  ]
];

//Error reporting
ini_set("error_reporting", $config['core']['showErrors']);
if ($config['core']['showErrors']) {
  error_reporting(E_ALL|E_STRCT);
}
?>