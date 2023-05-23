<?php
require_once 'shared.php';

echo json_encode([
  'publishableKey' => $configStripe['stripe_publishable_key'],
  'basicPrice' => $configStripe['basic_price_id'],
  'proPrice' => $configStripe['pro_price_id']
]);
