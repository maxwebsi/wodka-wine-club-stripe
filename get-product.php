<?php
require_once 'shared.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$input = file_get_contents('php://input');
	$body = json_decode($input);
}

if (json_last_error() !== JSON_ERROR_NONE) {
	http_response_code(400);
	echo json_encode([ 'error' => 'Invalid request.' ]);
	exit;
}


//$product = \Stripe\Product::retrieve($body->priceId);

$price = $stripe->prices->retrieve(
    $body->priceId,
    []
);

if ($price) {
    $product = $stripe->products->retrieve(
        $price->product,
        []
    );
    $argProduct = $product->toArray();
    $argProduct['unit_price'] = $price->unit_amount;
    echo json_encode($argProduct);
} else {
    http_response_code(400);
    echo json_encode([ 'error' => 'Invalid price ID.' ]);
    exit;
}