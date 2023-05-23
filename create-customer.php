<?php
require_once 'shared.php';
require_once 'common.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$input = file_get_contents('php://input');
	$body = json_decode($input);
}

if (json_last_error() !== JSON_ERROR_NONE) {
	http_response_code(400);
	echo json_encode([ 'error' => 'Invalid request.' ]);
	exit;
}

$userData = sanitizeInputFields();

// Check if the customer exist on database
$config = parse_ini_file('config-db.ini');
$return = [
    'issue' => '',
    'message' => ''
];
if ($config) {
    try {
        $db = new PDO ("mysql:host=" . $config['hostname'] . ";dbname=" . $config['dbname'] . ";charset=" . $config['charset'], $config['user'], $config['pass']);
    } catch (PDOException $e) {
        $return['issue'] = "Error: " . $e->getMessage();
        echo json_encode($return);
        die();
    }

    $stmt = $db->prepare("SELECT * FROM mbtg_users WHERE status <> 'deleted' AND email = :email");
    $stmt->bindValue(':email', $userData['email'], PDO::PARAM_STR);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        $messageError = '<div class="alert alert-warning alert-dismissible fade show" role="alert">';
        $messageError .= 'There is already an account registered with this email address, don&apos;t you remember the password? ';
        $messageError .= '<a href="' . $configStripe["domain"] . 'reset?email=' . $userData['email'] . '">Click here</a> to reset it.';
        $messageError .= '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        $return['issue'] = false;
        $return['message'] = $messageError;
    } else {

        // Create customer to Stripe
        /** @var Stripe $stripe */
        $customer = $stripe->customers->create([
            'name' => $body->name . ' ' . $body->surname,
            'email' => $body->email,
            'phone' => $body->tel
        ]);

        if ($customer->getLastResponse()->code === 200) {

            // Create customer into database
            $stmt = null;
            $sql = "INSERT INTO mbtg_users (email, password, cod_stripe, status, created) VALUES (:email, :password, :codstripe, 'registered', NOW())";
            $stmt = $db->prepare($sql);

            $customerId = $customer->id;
            $stmt->bindParam(':email', $userData['email'], PDO::PARAM_STR);
            $stmt->bindParam(':password', $userData['hashedPassword'], PDO::PARAM_STR);
            $stmt->bindParam(':codstripe', $customerId, PDO::PARAM_STR);

            // Esegue la query e se ci sono errori ritorna false
            if (!$stmt->execute()) {
                $return['issue'] = false;
                $return['message'] = 'Errore inserimento query';
            } else {
                $mail = sendEmail('new customer', $userData['email'], $customer->name, $configStripe['domain']);

                $return['issue'] = 'OK';
                if (isset($userData['priceId'])) {
                    $return['subscription'] = 'OK';
                    $return['priceId'] = $userData['priceId'];
                    $return['customerId'] = $customerId;
                }
            }
        } else {
            $return['issue'] = false;
            $return['message'] = 'Errore creazione customer Stripe';
        }
    }

    // Chiude la connessione al DB
    $stmt = null;
    $db = null;

echo json_encode($return);
}


