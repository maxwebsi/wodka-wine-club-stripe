<!DOCTYPE html>
<html lang="en" class="h-100">
<head>
    <meta charset="UTF-8">
    <title>Login to Select Tasting Wine Club</title>
    <link rel="icon" href="img/favicon.ico" type="image/x-icon"/>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">

    <script src="https://js.stripe.com/v3/"></script>
    <script src="js/script.js"></script>

    <!--Iubenda cookie consent-->
    <script type="text/javascript">var _iub = _iub || {}; _iub.cons_instructions = _iub.cons_instructions || []; _iub.cons_instructions.push(["init", {api_key: "hc970GKDU2ioSQ5UDhWtmTXJnguuzZng"}]);</script><script type="text/javascript" src="https://cdn.iubenda.com/cons/iubenda_cons.js" async></script>
</head>
<body class="d-flex flex-column h-100">
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


if (isset($_GET['p']) && $_GET['p']) {
    $priceId = filter_var($_GET['p'], FILTER_SANITIZE_STRING, FILTER_NULL_ON_FAILURE);
    $inputPriceId = '<input type="hidden" value="' . $priceId . '" id="price-id" name="price-id">';
} else {
    $inputPriceId = '<input type="hidden" value="price_1KcuOUFOIYFwUyjB370RBCEU" id="price-id" name="price-id">';
//    $inputPriceId = false;
}

if (isset($_COOKIE['tgsub_id_session'])) {
    $logged = true;
} else {
    $logged = false;
}
?>
<div id="loading-page">
    <img src="img/loader.gif">
</div>
<div id="container" class="container bg-light<?php echo($logged ? ' h-100' : '') ?>">
    <?php
    if ($inputPriceId !== false) {
        ?>
        <div class="wrap-secure-checkout bg-light d-flex align-items-center pt-4 pl-2">
            <img src="img/lock-grigio.svg" alt="Icon lock">
            <h3 class="ml-2 mb-0">Secure checkout</h3>
        </div>
        <?php
    }
    ?>
    <div id="sub-container" class="d-flex<?php echo($logged ? ' h-100' : '') ?>">
        <div class="row justify-content-center align-self-center w-100">
            <div class="col-md-8 col-xs-12 pt-4 pb-4">
                <div class="border border-secondary rounded p-2">
                    <div id="wrap-reg-form" class="collapse<?php echo($logged ? '' : ' show') ?>">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h1 class="m-0">Create Account</h1>
                            <p class="m-0">Already have an account?
                                <button id="show-login-form" class="btn btn-secondary">Log In</button>
                            </p>
                        </div>
                        <form id="form-registration">
                            <?php
                            if ($inputPriceId) {
                                echo $inputPriceId;
                            }
                            ?>
                            <div class="form-group">
                                <label for="new-name">Name</label>
                                <input type="text" class="form-control" id="new-name" required="required" data-cons-subject="first_name">
                            </div>
                            <div class="form-group">
                                <label for="new-surname">Surname</label>
                                <input type="text" class="form-control" id="new-surname" required="required" data-cons-subject="last_name">
                            </div>
                            <div class="form-group">
                                <label for="new-email">Email address</label>
                                <input type="email" class="form-control" id="new-email" required="required" data-cons-subject="email">
                            </div>
                            <div class="form-group">
                                <label for="new-password">Password</label>
                                <input type="password" class="form-control" id="new-password" required="required" data-cons-exclude>
                            </div>
                            <div class="form-group">
                                <label for="new-tel">Telephone</label>
                                <input type="text" class="form-control" id="new-tel" required="required" data-cons-subject="telephone">
                            </div>
                            <div class="form-group form-check">
                                <input type="checkbox" class="form-check-input" id="privacy" required="required" data-cons-preference="legal_documents">
                                <label class="form-check-label" for="exampleCheck1">I've read the <a href="https://www.iubenda.com/privacy-policy/42835687" target="_blank">Privacy Policy</a> and accept the <a href="https://www.iubenda.com/terms-and-conditions/42835687" target="_blank">Terms and Condition</a>
                                </label>
                            </div>
                            <div class="text-right">
                                <button type="submit" class="btn btn-primary">SIGN IN</button>
                            </div>
                        </form>
                    </div>

                    <div id="wrap-login-form" class="collapse">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h1 class="m-0">Login</h1>
                            <p class="m-0">New here?
                                <button id="show-registration-form" class="btn btn-secondary">Create an account</button>
                            </p>
                        </div>
                        <form id="form-login">
                            <?php
                            if ($inputPriceId) {
                                echo $inputPriceId;
                            }
                            ?>
                            <div class="form-group">
                                <label for="email">Email address</label>
                                <input type="email" class="form-control" id="email" required="required">

                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" required="required">
                            </div>
                            <div class="text-right">
                                <button type="submit" class="btn btn-primary">LOG IN</button>
                            </div>
                        </form>
                    </div>
                    <div id="message" class="mt-4"></div>

                    <div id="logged" class="collapse<?php echo($logged ? ' show' : '') ?>">
                        <h3 class="text-center mt-4 mb-4">Welcome on your customer portal at Select Tasting</h3>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="col text-center m-4">
                                <a class="btn btn-primary p-2"
                                   href="<?php echo $configStripe['domain'] . 'manage' ?>">Manage Password</a>
                            </div>
                            <div class="col text-center m-4">
                                <form method="POST"
                                      action="<?php echo $configStripe['domain'] ?>create-customer-portal-session.php">
                                    <button type="submit" class="btn btn-primary">Manage Billing</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            if ($inputPriceId) {
                ?>
                <div class="col-md-4 col-xs-12 pt-4 pb-4">
                    <div class="border border-secondary rounded p-2">
                        <h3 id="product-title"></h3>
                        <img src="" id="product-image" class="rounded d-none">
                        <p id="product-description" class="mt-2"></p>
                        <h3 class="text-center mt-4">Total: <span id="product-price"></span>&euro;</h3>
                        <div class="wrap-secure-checkout bg-light d-flex align-items-center pt-4 pl-2">
                            <img src="img/lock-grigio.svg" alt="Icon lock">
                            <p class="ml-2 mb-0">Secure checkout</p>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</div>
<?php
require_once 'footer.php';
?>
</body>
</html>