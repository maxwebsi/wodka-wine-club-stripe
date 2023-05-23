<?php

require 'stripe-php-7.70.0/init.php';

header('Content-Type: application/json');

$configStripe = parse_ini_file('config-stripe.ini');

// Make sure the configuration file is good.
if (!$configStripe) {
	http_response_code(500);
	echo json_encode([ 'error' => 'Internal server error.' ]);
	exit;
}

$stripe = new \Stripe\StripeClient($configStripe['stripe_secret_key']);
