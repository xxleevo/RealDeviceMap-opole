<?php
require_once './vendor/autoload.php';
require_once './config.php';
require_once './includes/DiscordAuth.php';

if ($config['discord']['enabled']) {
echo "<script>console.log('Logout');</script>";
	destroyCookiesAndSessions();
	header('Location: google.de');
}
die;
