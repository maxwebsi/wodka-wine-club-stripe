<?php
require_once 'shared.php';
require_once 'common.php';

$userData = array(
    'address' =>
        array(
            'city' => 'Siena',
            'country' => 'IT',
            'line1' => 'Strada Massetana Romana, 254',
            'line2' => 'Provincia di Siena',
            'postal_code' => '53100',
            'state' => 'Toscana',
        ),
    'name' => 'Mario Rossi'
);


//if(!isset($_REQUEST['session_id'])) {
//    header('Location: ' . $configStripe['domain'] . 'login.php');
//    die();
//}

//http://localhost/wine-club-selecttasting/sub/checkout-single-subscription/dev/thank-you?session_id=cs_test_a1erh8T9CnWPL7SwJ8cLSwG4jB7KsjTq1Jmv3awG7wJW302aFCNPKgHJFl

// Fetch the Checkout Session to display the JSON result on the success page
$id = $_REQUEST['session_id'];

/** @var Stripe $stripe */
$checkout_session = $stripe->checkout->sessions->retrieve($id);

if ($checkout_session && $checkout_session->payment_status != "paid") {
    // error
} else {
    $shipping = $checkout_session->shipping->toArray();
    $sub = $stripe->subscriptions->retrieve($checkout_session->subscription);
    $invoice = $stripe->invoices->retrieve($sub->latest_invoice);
    // TODO riprendere il nome del prodotto ed inviarlo nel riepilogo dell'ordine
    // anche nella mail che inviamo al clinete rimettere i dati dell'ordine e quelli del cliente
    $customerData = [
        'name' => $invoice->customer_name,
        'email' => $invoice->customer_email,
        'phone' => $invoice->customer_phone,
        'address' => $invoice->customer_address,
        'shipping' => $invoice->customer_shipping

    ];

    $customerId = $checkout_session->customer;
    $customer = $stripe->customers->retrieve($customerId);
    if ($customer) {
        $email = $customer->email;
        $name = $customer->name;

        sendEmail("new order customer", $email, $name, $configStripe['domain']);
        sendEmail("new order admin", null, null, null, $customerData);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Thank you for your order to Select Tasting Wine Club</title>

    <link rel="icon" href="img/favicon.ico" type="image/x-icon"/>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">

    <script src="js/success.js"></script>
</head>
<body class="d-flex flex-column min-vh-100">
<?php
require_once 'header.php';

$configStripe = parse_ini_file('config-stripe.ini');

if ($configStripe) {
    ?>
    <script type="text/javascript">
        var __domainUrl = "<?php echo ($_SERVER['HTTP_HOST'] == 'localhost' ? $configStripe['domain'] : '/') ?>";
    </script>
    <?php
}

if (isset($_REQUEST['session_id']) && $_REQUEST['session_id']) {
    echo '<input type="hidden" id="session-id" value="' . $_REQUEST['session_id'] . '">';
}
?>
<div id="loading-page">
    <img src="img/loader.gif">
</div>
<div class="container bg-light">
    <div class="col-md-8 col-xs-12 pt-4 pb-4">
        <div class="border border-secondary rounded p-2">
            <h1 class="mb-4">Thanks for your purchase</h1>
            <p class="m-0">Your order has been received and will soon be taken care of.
                You can manage your account from this web page:</p>
            <strong><a href="<?php echo $configStripe['domain'] ?>login"
                       class="mt-4 text-secondary"><?php echo $configStripe['domain'] ?>login</a></strong>
        </div>
    </div>
</div>
<?php
require_once 'footer.php';
?>
</body>
</html>