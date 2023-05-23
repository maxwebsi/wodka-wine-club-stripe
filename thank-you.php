<!DOCTYPE html>
<html lang="en" class="h-100">
<head>
    <meta charset="UTF-8">
    <title>Thank you for your order to Select Tasting Wine Club</title>

    <link rel="icon" href="img/favicon.ico" type="image/x-icon"/>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">

    <script src="js/success.js"></script>
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

if (isset($_REQUEST['session_id']) && $_REQUEST['session_id']) {
    echo '<input type="hidden" id="session-id" value="' . $_REQUEST['session_id'] . '">';
}
?>
<div id="loading-page">
    <img src="img/loader.gif">
</div>
<div class="container bg-light d-flex h-100">
    <div class="row justify-content-center align-self-center w-100">
    <div class="col-md-8 col-xs-12 pt-4 pb-4">
        <div id="response" class="border border-secondary rounded p-2"></div>
    </div>
</div>
</div>
<?php
require_once 'footer.php';
?>
</body>
</html>
