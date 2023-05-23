<?php
require_once 'shared.php';
require_once 'common.php';
$payload = @file_get_contents('php://input');
$event = null;

$headers[] = 'MIME-Version: 1.0';
$headers[] = 'Content-type: text/html; charset=UTF-8';

// Additional headers
$headers[] = 'From: Select Tasting <alexandra@selecttasting.com>';

$headers[] = "Organization: Select Tasting";

$headers[] = "Reply-To: Select Tasting <alexandra@selecttasting.com>";
$headers[] = "Return-Path: Select Tasting <alexandra@selecttasting.com>";

try {
    $event = \Stripe\Event::constructFrom(
        json_decode($payload, true)
    );
} catch(\UnexpectedValueException $e) {

    // Mail it
    return mail('assistenza@tobugroup.com', 'web hook', 'errore classe', implode("\r\n", $headers));

    // Invalid payload
    http_response_code(400);
    exit();
}

// Handle the event
switch ($event->type) {
    case 'customer.updated':
        $message = '***';
        $message .= json_encode($event->data->object);
        $message .= '+++';

        $paymentIntent = $event->data->object;


        $customerData = [
            'name' => $paymentIntent->name,
            'email' => $paymentIntent->email,
            'phone' => $paymentIntent->phone,
            'address' => $paymentIntent->address,
            'shipping' => $paymentIntent->shipping
        ];

       return sendEmail("update customer", null, null, null, $customerData);


//        return mail('assistenza@tobugroup.com', 'web hook', $message, implode("\r\n", $headers));

//        echo 'pippo';
//        $paymentIntent = $event->data->object; // contains a \Stripe\PaymentIntent
        // Then define and call a method to handle the successful payment intent.
        // handlePaymentIntentSucceeded($paymentIntent);
        break;
    case 'payment_intent.succeeded':
        $paymentIntent = $event->data->object; // contains a \Stripe\PaymentIntent
        // Then define and call a method to handle the successful payment intent.
        // handlePaymentIntentSucceeded($paymentIntent);
        break;
    case 'payment_method.attached':
        $paymentMethod = $event->data->object; // contains a \Stripe\PaymentMethod
        // Then define and call a method to handle the successful attachment of a PaymentMethod.
        // handlePaymentMethodAttached($paymentMethod);
        break;
    // ... handle other event types
    default:
        echo 'Received unknown event type ' . $event->type;
}

http_response_code(200);
