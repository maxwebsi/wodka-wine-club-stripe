<?php
require_once 'shared.php';
require_once 'common.php';

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

    $listSub = $stripe->subscriptions->all(
        [
            'status' => 'active',
            'limit' => 100,
        ]
    );

//    print_r($listSub);

    $listNewCustomer = [];
    foreach ($listSub as $sub) {
        $customer = $stripe->customers->retrieve($sub->customer);
        if ($customer) {
            $stmt = $db->prepare("SELECT * FROM mbtg_users WHERE status <> 'deleted' AND cod_stripe = :codstripe OR email = :email");
            $stmt->bindValue(':codstripe', $sub->customer, PDO::PARAM_STR);
            $stmt->bindValue(':email', $customer->email, PDO::PARAM_STR);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                echo "utente presente " . $sub->customer . " " . $customer->email . "\r\n";
            } else {
                // Create customer into database
                $bytes = random_bytes(20);
                $resetCode =  bin2hex($bytes);
                $stmt = null;
                $sql = "INSERT INTO mbtg_users (email, password, cod_stripe, cod_reset, status, import, created, updated) VALUES (:email, :password, :codstripe, :codreset, 'registered', 1, NOW(), '2022-04-20 00:00:00')";
                $stmt = $db->prepare($sql);

                $password = password_hash("nfai458igigrbanco8rih", PASSWORD_BCRYPT);
                $stmt->bindParam(':email', $customer->email, PDO::PARAM_STR);
                $stmt->bindParam(':password', $password, PDO::PARAM_STR);
                $stmt->bindParam(':codstripe', $sub->customer, PDO::PARAM_STR);
                $stmt->bindParam(':codreset', $resetCode, PDO::PARAM_STR);

                // Esegue la query e se ci sono errori ritorna false
                if (!$stmt->execute()) {
                    echo 'errore inserimento utente ' . $sub->customer;
                } else {
                    array_push($listNewCustomer, array("id" => $sub->customer, "email" => $customer->email));
                }

//                if ($customer->email == "massimiliano.bacchini@gmail.com") {
                   $res = sendEmail('import customer', $customer->email, '', $configStripe['domain'] . 'manage?code=' . $resetCode);
                   if ($res != "Email inviata con successo") {
                        echo $res . " " . $customer->email . "\n\r";
                   }
//                }
            }
        } else {
            echo 'customer non trovato ' . $sub->customer . ' ';
        }
        $stmt = null;
    }

    // Chiude la connessione al DB
    $stmt = null;
    $db = null;
    echo "lista utenti creati\n\r";
    $iCus = 1;
    foreach ($listNewCustomer as $newCus) {
        echo $iCus . " " . $newCus['id'] . " " . $newCus['email'] . "\n\r";
        $iCus++;
    }
} else {
    echo 'errore';
}
