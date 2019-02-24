<?php
session_start();

include './vendor/autoload.php';
include './config.php';

date_default_timezone_set($config['core']['timeZone']);

if ($config['discord']['enabled'] && !isset($_SESSION['user'])) {
  header("Location: ./discord-login.php");
  die();
}

echo "<!doctype html>
<html lang='en'>
  <head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0, shrink-to-fit=no'>

    <link rel='shortcut icon' type='image/x-icon' href='./static/favicon.ico' />
    <link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css' integrity='sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS' crossorigin='anonymous'>
    <link rel='stylesheet' href='https://unpkg.com/leaflet@1.4.0/dist/leaflet.css' integrity='sha512-puBpdR0798OZvTTbP4A8Ix/l+A4dHDD0DGqYW6RQ+9jxkRFclaxxQb/SJAWZfWAkuyeQUytO7+7N4QKrDh+drA==' crossorigin=''/>
    <link rel='stylesheet' href='./static/css/font-awesome.min.css'>
    <link rel='stylesheet' href='./static/css/datepicker.css'>
    <link rel='stylesheet' href='./static/css/no-more-tables.css'>

    <script type='text/javascript' src='https://code.jquery.com/jquery-3.3.1.slim.min.js' integrity='sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo' crossorigin='anonymous'></script>
    <script type='text/javascript' src='https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js' integrity='sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut' crossorigin='anonymous'></script>
    <script type='text/javascript' src='https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js' integrity='sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k' crossorigin='anonymous'></script>
    <script type='text/javascript' src='https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js'></script>
    <script type='text/javascript' src='https://unpkg.com/leaflet@1.4.0/dist/leaflet.js' integrity='sha512-QVftwZFqvtRNi0ZyCtsznlKSWOStnDORoefr1enyq5mVL4tmKB3S/EnC3rRJcxCPavG10IcrVGSmPh6Qw5lwrg==' crossorigin=''></script>
    <script type='text/javascript' src='./static/js/filters.js'></script>
    <script type='text/javascript' src='./static/js/table.sorter.js'></script>

    <title>" . $config['ui']['title'] . "</title>
  </head>
  <body>";

include_once('./templates/header.html');

echo "<br/><p class='lead'>&nbsp;</p>";

if (isset($_SESSION['user'])) {
  echo "<h1>Welcome ".$_SESSION['user']."</h1>";
}

$request_method = $_SERVER["REQUEST_METHOD"];
switch($request_method) {
  case "GET":
    if(!empty($_GET["page"])) {
      $page = $_GET["page"];
      switch ($page) {
        case "pokemon":
          include('./pages/pokemon.php');
          break;
        case "raids":
          include('./pages/raids.php');
          break;
        case "gyms":
          include('./pages/gyms.php');
          break;
        case "quests":
          include('./pages/quests.php');
          break;
        case "pokestops":
          include('./pages/pokestops.php');
          break;
        case "stats":
          include('./pages/stats.php');
          break;          
      }
    } else {
      include('./pages/dashboard.php');
    }
    break;
  default:
    // Invalid Request Method
    header("HTTP/1.0 405 Method Not Allowed");
    break;
}

if (!empty($config['google']['analyticsId'])) {
  echo "
<script async src='https://www.google-analytics.com/analytics.js'></script>
<script>
  window.ga=window.ga||function(){(ga.q=ga.q||[]).push(arguments)};
  ga.l=+new Date;
  ga('create', '" . $config['google']['analyticsId'] . "', 'auto');
  ga('send', 'pageview');
</script>";
}

if (!empty($config['google']['adSenseId'])) {
  echo "
<script async src='//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js'></script>
<script>
  (adsbygoogle = window.adsbygoogle || []).push({
    google_ad_client: '" . $config['google']['adSenseId'] . "',
    enable_page_level_ads: true
  });
</script>";
}

if ($config['core']['showFooter']) {
  include_once('./templates/footer.html');
}

echo "</body>
</html>";
?>