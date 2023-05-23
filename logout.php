<?php
$configStripe = parse_ini_file('config-stripe.ini');

setcookie("tgsub", "", time() - 3600);
setcookie("tgsub_st_user", "", time() - 3600);
setcookie("tgsub_id_session", "", time() - 3600);

header('Location: ' . $configStripe['domain']);