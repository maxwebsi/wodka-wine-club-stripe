<?php
$configStripe = parse_ini_file('config-stripe.ini');
$config = parse_ini_file('config-db.ini');
require_once 'common.php';

$email = null;
if (isset($_REQUEST['email']) && $_REQUEST['email']) {
    $email = filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL, array('flags' => FILTER_NULL_ON_FAILURE));
}


if (!$email || !$config) {
    header('Location: ' . $configStripe['domain']);
} else {
    try {
        $db = new PDO ("mysql:host=" . $config['hostname'] . ";dbname=" . $config['dbname'] . ";charset=" . $config['charset'], $config['user'], $config['pass']);
    } catch (PDOException $e) {
        $result['issue'] = "Error: " . $e->getMessage();
        echo json_encode($result);
        die();
    }

    $bytes = random_bytes(20);
    $resetCode =  bin2hex($bytes);

    $stmt = $db->prepare("UPDATE mbtg_users SET cod_reset = :reset, updated = now() where email = :email");
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->bindValue(':reset', $resetCode, PDO::PARAM_STR);

    $errorEmail = true;
    if ($stmt->execute()) {
        $sendOk = sendEmail('reset password', $email, '', $configStripe['domain'] . 'manage?code=' . $resetCode);
        if ($sendOk == "Email inviata con successo") {
            $errorEmail = false;
        }
    }
}
?>
    <!DOCTYPE html>
    <html lang="en" class="h-100">
    <head>
        <meta charset="UTF-8">
        <title>Reset Password | Select Tasting Wine Club</title>

        <link rel="icon" href="img/favicon.ico" type="image/x-icon"/>
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/style.css">

        <script src="https://js.stripe.com/v3/"></script>
        <script src="js/reset.js"></script>
    </head>
<body class="d-flex flex-column h-100">
<?php
require_once 'header.php';

if ($configStripe) {
    ?>
    <script type="text/javascript">
        var __domainUrl = "<?php echo ($_SERVER['HTTP_HOST'] == 'localhost' ? $configStripe['domain'] : '/') ?>";
    </script>
    <?php
}
?>
    <div id="loading-page">
        <img src="img/loader.gif">
    </div>
    <div class="container bg-light h-100 d-flex">
        <div class="row justify-content-center align-self-center w-100">
            <div class="col-md-8 col-xs-12 pt-4 pb-4">
                <div class="border border-secondary rounded p-2">
                    <div id="wrap-reg-form" class="collapse show">
                        <div class="mb-4">
                            <h1>Reset your password</h1>
                            <?php
                                if ($errorEmail == false) {
                                    ?>
                                    <p class="mt-4">We have sent you an email with instructions for recovering your password.</p>
                                    <?php
                                } else {
                                    ?>
                                    <p class="mt-4">An error occurred.</p>
                                    <?php
                                }
                            ?>
                            <p>If you have problems, you can contact us with these methods:</p>
                            <p>TEL: <a href="tel:+393488728265" rel="nofollow">+39 3488 728 265</a> Email: <a
                                        href="mailto:info@selecttasting.com" rel="nofollow">info@selecttasting.com</a>
                            </p>
                        </div>
                    </div>
                    <div id="message" class="mt-4"></div>
                </div>
            </div>
        </div>
    </div>
<?php
require_once 'footer.php';
?>
</body>
</html>