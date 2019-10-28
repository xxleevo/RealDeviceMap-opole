<?php 
echo 
"<div style='margin-left:10px;'>" .
"<b>ERROR</b><br>
Diese Seite ist ausschließlich für Spender unseres Projektes.";
if (!empty($config['urls']['paypal']) && !empty($config['urls']['patreon'])) {
    echo "<br>Wenn du uns Supporten möchtest, dann findest du uns hier:" .
        "<br><a href='". $config['urls']['paypal'] ."'> Paypal </a>" .
        "<br><a href='". $config['urls']['patreon'] ."'> Patreon </a>";
} else if (!empty($config['urls']['patreon'])) {
    echo "<br>Wenn du uns Supporten möchtest, dann klicke <a href='". $config['urls']['patreon'] ."'> hier </a>";
}else if (!empty($config['urls']['paypal'])) {
    echo "<br>Wenn du uns Supporten möchtest, dann klicke <a href='". $config['urls']['paypal'] ."'> hier </a>";
}
echo "</div>";
?>
<link rel="stylesheet" href="./static/css/footerfix.css"/>