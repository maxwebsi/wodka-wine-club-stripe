<?php
require_once "common.php";
require_once "shared.php";

$userData = sanitizeInputFields(false);

$config = parse_ini_file('config-db.ini');

if ($config) {

    $result = [
        'issue' => '',
        'message' => '',
        'customerId' => 0,
        'priceId' =>  $userData['priceId'],
        'subscription' => false
    ];

    try {
        $db = new PDO ("mysql:host=" . $config['hostname'] . ";dbname=" . $config['dbname'] . ";charset=" . $config['charset'], $config['user'], $config['pass']);
    } catch (PDOException $e) {
        $result['issue'] = "Error: " . $e->getMessage();
        echo json_encode($result);
        die();
    }

    $stmt = $db->prepare("SELECT * FROM mbtg_users WHERE status <> 'deleted' AND email = :email ORDER BY created DESC");
    $stmt->bindValue(':email', $userData['email'], PDO::PARAM_STR);
    $stmt->execute();

    $messageError = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        Wrong email or password. Don&apos;t you remember the password?&nbsp;
                        <a href="' . $configStripe["domain"] . 'reset?email=' . $userData['email'] . '">Click here</a> to reset it.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $user = $user[0];

        // Check credential
        if (password_verify($userData['hashedPassword'], $user['password'])) {
            $bytes = random_bytes(20);
            $idSession =  bin2hex($bytes);

            $stmtSession = $db->prepare('UPDATE mbtg_users SET id_session = :id_session WHERE id_user = :id_user');
            $stmtSession->bindValue(':id_session', $idSession, PDO::PARAM_STR);
            $stmtSession->bindValue(':id_user', $user['id_user'], PDO::PARAM_INT);

            if ($stmtSession->execute()) {

                if (isset($stripe)) {
                    $customer = $stripe->customers->retrieve($user['cod_stripe'])->toArray();
                    if ($customer && (!isset($customer['deleted']) || $customer['deleted'] != true)) {
                        $result['issue']  = 'OK';
                        $result['message'] = 'Login successfully completed';
                        $result['customerId'] = $user['cod_stripe'];

                        setcookie('tgsub', 'logged', 0);
                        setcookie('tgsub_st_user', $user['cod_stripe'], 0);
                        setcookie('tgsub_id_session', $idSession, 0);
                    } else {
                        $result['issue']  = false;
                        $result['message'] = $messageError;
                    }
                }
            } else {
                $result['issue']  = false;
                $result['message'] = 'Session error';
            }
        } else {
            $result['issue'] = false;
            $result['message'] = $messageError;
        }
    } else {
        $result['issue'] = false;
        $result['message'] = $messageError;
    }
    $stmt = null;
    $db = null;
    if ($result['customerId'] && $result['priceId']) {
        $result['subscription'] = 'OK';
    }
    echo json_encode($result);
}
