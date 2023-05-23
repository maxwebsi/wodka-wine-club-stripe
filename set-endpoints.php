<?php
require_once 'shared.php';
require_once 'common.php';


$endPoints = $stripe->webhookEndpoints->all(['limit' => 3]);

echo '<pre>';
print_r($endPoints);
echo '</pre>';

$stripe->webhookEndpoints->update(
    'we_1IdFLZC33ocuaC7u1S3o1Lsm',
    ['url' => 'https://wineclub-selecttasting.com/stripe-webhooks.php']
);

$endPoints2 = $stripe->webhookEndpoints->all(['limit' => 3]);

echo '<pre>';
print_r($endPoints2);
echo '</pre>';
die();

