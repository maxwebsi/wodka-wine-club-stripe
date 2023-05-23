<?php
$configStripe = parse_ini_file('config-stripe.ini');
$config = parse_ini_file('config-db.ini');
require_once 'common.php';

if (isset($_COOKIE['tgsub_id_session']) || isset($_REQUEST['code'])) {

    // if set code check if it's valid
    $resetCode = null;
    $sessionId = null;
    if (isset($_REQUEST['code'])) {
        $resetCode = filter_var($_REQUEST['code'], FILTER_SANITIZE_STRING, array('flags' => FILTER_NULL_ON_FAILURE));
    } elseif (isset($_COOKIE['tgsub_id_session'])) {
        $sessionId = $_COOKIE['tgsub_id_session'];
    }


    try {
        $db = new PDO ("mysql:host=" . $config['hostname'] . ";dbname=" . $config['dbname'] . ";charset=" . $config['charset'], $config['user'], $config['pass']);
    } catch (PDOException $e) {
        $result['issue'] = "Error: " . $e->getMessage();
        echo json_encode($result);
        die();
    }

    if ($resetCode) {
        $stmt = $db->prepare("SELECT tmp.* FROM (SELECT id_user, UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(updated) AS differenza, cod_reset, status FROM mbtg_users) AS tmp WHERE  tmp.status <> 'delete' AND tmp.differenza < 86400 AND tmp.cod_reset = :reset");
        $stmt->bindValue(':reset', $resetCode, PDO::PARAM_STR);
    } elseif ($sessionId) {
        $stmt = $db->prepare("SELECT id_user FROM mbtg_users WHERE status <> 'delete' AND id_session = :sessione");
        $stmt->bindValue(':sessione', $sessionId, PDO::PARAM_STR);
    }
    $error = false;
    if ($stmt->execute()) {
        if ($stmt->rowCount() == 1) {
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $userId = $users[0]['id_user'];
        } elseif ($stmt->rowCount() > 1) {
            $error = "The results is ambiguous";
        } else {
            $error = "The reset code is not valid or expired";
        }
    } else {
        $error = "An error occurred, please try again!";
    }
} else {
    header('Location: ' . $configStripe['domain']);
    die();
}
?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Reset Password | Select Tasting Wine Club</title>

        <link rel="icon" href="img/favicon.ico" type="image/x-icon"/>
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/style.css">

        <script src="https://js.stripe.com/v3/"></script>
        <script src="js/reset.js"></script>
    </head>
<body class="d-flex flex-column min-vh-100">
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
    <div class="container bg-light">
        <div class="row">
            <div class="col-md-8 col-xs-12 pt-4 pb-4">
                <div class="border border-secondary rounded p-2">
                    <div id="wrap-reg-form" class="collapse show">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h1 class="m-0">Reset your password</h1>
                        </div>
                        <?php
                        if ($error == false && isset($userId) && $userId > 0) {
                            ?>
                            <form id="form-reset">
                                <div class="form-group">
                                    <label for="new-password">Password</label>
                                    <input type="password" class="form-control" id="new-password">
                                </div>
                                <div class="form-group">
                                    <label for="new-password-2">Retype password</label>
                                    <input type="password" class="form-control" id="new-password-2">
                                </div>
                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary">SET NEW PASSWORD</button>
                                </div>
                                <input type="hidden" id="user-id" value="<?php echo $userId ?>">
                            </form>
                            <?php
                        } else {
                            echo '<p>' . $error . '</p>';
                            echo '<p class="m-0">Back to <a href="/" class="btn btn-primary">Login page</a></p>';
                        }
                        ?>
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