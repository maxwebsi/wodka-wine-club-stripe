<?php
require_once 'shared.php';

if (isset($_COOKIE['tgsub_st_user'])) {
    $idUserStripe = $_COOKIE['tgsub_st_user'];
} else {
    $idUserStripe = null;
}

$session = $stripe->billingPortal->sessions->create([
    'customer' => $idUserStripe,
    'return_url' => $configStripe['domain'],
]);

//echo json_encode(['url' => $session->url]);

if ($session) {
    // Redirect to the customer portal.
    header("Location: " . $session->url);
    exit();
} else {

}
