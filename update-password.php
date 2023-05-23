<?php
$config = parse_ini_file('config-db.ini');
require_once 'common.php';
require_once 'shared.php';

$userData = sanitizeInputFields();

$return = [
    'issue' => '',
    'message' => ''
];
if ($config) {

    if ($userData['idUser'] && $userData['hashedPassword']) {
        try {
            $db = new PDO ("mysql:host=" . $config['hostname'] . ";dbname=" . $config['dbname'] . ";charset=" . $config['charset'], $config['user'], $config['pass']);
        } catch (PDOException $e) {
            $return['issue'] = "Error: " . $e->getMessage();
            echo json_encode($return);
            die();
        }

        $stmt = $db->prepare("UPDATE mbtg_users SET password = :password, cod_reset = null, updated = now() WHERE id_user = :iduser");
        $stmt->bindValue(':password', $userData['hashedPassword'], PDO::PARAM_STR);
        $stmt->bindValue(':iduser', $userData['idUser'], PDO::PARAM_STR);
        if($stmt->execute()) {
            $message = '<div class="alert alert-success alert-dismissible fade show" role="alert">';
            $message .= 'Password updated succesfully, go to login page for login: ';
            $message .= '<a href="' . $configStripe["domain"] . '">login</a></div>';
            $return['issue'] = 'Ok';
            $return['message'] = $message;
        } else {
            $messageError = '<div class="alert alert-warning alert-dismissible fade show" role="alert">';
            $messageError .= 'An error occurred, please try again';
            $messageError .= '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
            $return['issue'] = false;
            $return['message'] = $messageError;
        }
    } else {
        $return['issue'] = false;
        $return['message'] = "The password is not valid";
    }
}

echo json_encode($return);






