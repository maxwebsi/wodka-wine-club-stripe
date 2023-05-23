<?php

require_once 'shared.php';
require_once 'common.php';
$configStripe = parse_ini_file('config-stripe.ini');

$return = [
    'issue' => '',
    'message' => ''
];

//http://localhost/wine-club-selecttasting/sub/checkout-single-subscription/dev/thank-you?session_id=cs_test_a1HTPqNOvWoj4dhP2btTDB53wsC6hOCmgNH0b4Wv0G25TqaDJdg6wORFUo

// Fetch the Checkout Session to display the JSON result on the success page
if (isset($_REQUEST['session_id'])) {
    $id = $_REQUEST['session_id'];

    /** @var Stripe $stripe */
    $checkout_session = $stripe->checkout->sessions->retrieve($id);
    if ($checkout_session && $checkout_session->payment_status != "paid") {
        $return['issue'] = 'Ok';
        $return['message'] = '<h1>An error has occurred!</h1>
<p class="m-0">The session has expired, go back to the homepage:</p>
            <strong><a href="' . $configStripe["domain"] . ' class="mt-4 text-secondary">' . $configStripe['domain'] . '</a></strong>';

    } else {
        $shipping = $checkout_session->shipping->toArray();
        $sub = $stripe->subscriptions->retrieve($checkout_session->subscription);
        $productData = $sub->items->first()->plan->toArray();
        $idProduct = $productData['product'];
        $product = $stripe->products->retrieve($idProduct);
        $invoice = $stripe->invoices->retrieve($sub->latest_invoice);
        $paymentIntent = $stripe->paymentIntents->retrieve($invoice->payment_intent);
        $address = $paymentIntent->charges->first()->billing_details->toArray();

        // TODO riprendere il nome del prodotto ed inviarlo nel riepilogo dell'ordine
        // anche nella mail che inviamo al clinete rimettere i dati dell'ordine e quelli del cliente
        $subscriptionData = [
            'name' => $invoice->customer_name,
            'email' => $invoice->customer_email,
            'phone' => $invoice->customer_phone,
            'address' => $address['address'],
            'shipping' => $shipping,
            'product_name' => $product->name,
            'product_description' => $invoice->lines->first()->description,
            'product_price' => $invoice->total
        ];

        sendEmail("new order customer", null, null, null, $subscriptionData);
        sendEmail("new order admin", null, null, null, $subscriptionData);
        $return['issue'] = 'Ok';
        $return['message'] = '<h1 class="mb-4">Thanks for your purchase</h1>
            <p class="m-0">Your order has been received and will soon be taken care of.
                You can manage your account from this web page:</p>
            <strong><a href="' . $configStripe["domain"] . ' class="mt-4 text-secondary">' . $configStripe['domain'] . '</a></strong>';

    }
} else {
    $return['issue'] = 'Ok';
    $return['message'] = '<h1>An error has occurred!</h1>
<p class="m-0">The session has expired, go back to the homepage:</p>
            <strong><a href="' . $configStripe["domain"] . ' class="mt-4 text-secondary">' . $configStripe['domain'] . '</a></strong>';
}


echo json_encode($return);
