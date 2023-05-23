<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';


function sanitizeInputFields($pwCrypt = true)
{

    $filters = array(
        'email' => FILTER_VALIDATE_EMAIL,
        'password' => FILTER_SANITIZE_STRING,
        'priceId' => FILTER_SANITIZE_STRING,
        'name' => FILTER_SANITIZE_STRING,
        'surname' => FILTER_SANITIZE_STRING,
        'tel' => FILTER_SANITIZE_STRING,
        'idUser' => FILTER_SANITIZE_NUMBER_INT
    );
    $options = array(
        'email' => array(
            'flags' => FILTER_NULL_ON_FAILURE
        ),
        'password' => array(
            'flags' => FILTER_NULL_ON_FAILURE
        ),
        'priceId' => array(
            'flags' => FILTER_NULL_ON_FAILURE
        ),
        'name' => array(
            'flags' => FILTER_NULL_ON_FAILURE
        ),
        'surname' => array(
            'flags' => FILTER_NULL_ON_FAILURE
        ),
        'tel' => array(
            'flags' => FILTER_NULL_ON_FAILURE
        ),
        'idUser' => array(
            'flags' => FILTER_NULL_ON_FAILURE
        )
    );

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $inputs = json_decode(file_get_contents('php://input'));
    } else {
        // The request method is no POST
        die();
    }

    $filtered = array();
    foreach ($inputs as $key => $value) {
        $filtered[$key] = filter_var($value, $filters[$key], $options[$key]);
    }

    if ($pwCrypt) {
        $password = password_hash($filtered['password'], PASSWORD_BCRYPT);
    } else {
        $password = $filtered['password'];
    }

    $userData = [];

    if (isset($filtered['email'])) {
        $userData['email'] = $filtered['email'];
    }

    if (isset($filtered['password'])) {
        $userData['hashedPassword'] = $password;
    }

    if (isset($filtered['priceId']) && $filtered['priceId']) {
        $userData['priceId'] = $filtered['priceId'];
    } else {
        $userData['priceId'] = null;
    }

    if (isset($filtered['name']) && $filtered['name']) {
        $userData['name'] = $filtered['name'];
    } else {
        $userData['name'] = null;
    }

    if (isset($filtered['surname']) && $filtered['surname']) {
        $userData['surname'] = $filtered['surname'];
    } else {
        $userData['surname'] = null;
    }

    if (isset($filtered['tel']) && $filtered['tel']) {
        $userData['tel'] = $filtered['tel'];
    } else {
        $userData['tel'] = null;
    }

    if (isset($filtered['idUser']) && $filtered['idUser']) {
        $userData['idUser'] = $filtered['idUser'];
    } else {
        $userData['idUser'] = null;
    }

    return $userData;
}

/*
 * @params type [new customer, new order customer, reset password]
 *
 */

function sendEmail($type = null, $userEmail = null, $name = null, $url = null, $subscriptionData = null)
{

    switch ($type) {
        case "new customer":
            $subject = 'Thanks for your registration';
            $title = 'Thanks for your registration';
            $content = '<p>Thanks for registering on Selecttasting.com.</p><p>Your registration on Select Tasting customer portal is complete!</p>';
            $content .= '<p>Dear ' . $name . ',</p><p>Thanks for creating an account on Select Tasting.com. By accessing your account, you will be able to see your subscription plan and manage your shipping and billing information.</p>';
            $content .= '<p><a href="' . $url . '">Login</a> from this email or from the customer portal area on <a href="https://www.selecttasting.com/">www.selecttasting.com</a>.</p>';
            $signature = '<p>Kind regards,<br>Alexandra and the Select Tasting team</p>';
            break;
        case "new order customer":
            if (isset($subscriptionData['name'])) {
                $name = ' ' . $subscriptionData['name'];
            } else {
                $name = '';
            }
            $subject = 'Thanks for your subscription to our Club Brunello!';
            $title = 'Thanks for your subscription to our Club Brunello!';
            $content = '<p>Hi ' . $name . ',</p><p>Thank you for subscribing to Club Brunello by Select Tasting! We are excited to have you on board and we cannot wait to share great Italian wines with you!</p>';
            $content .= '<h3>Order details</h3>';
            $content .= '<p>' . $subscriptionData["product_description"] . '</p>';
            $content .= getOrderDetail($subscriptionData);
            $signature = '<p>Cheers from Montalcino,<br>Alexandra and the Select Tasting team</p>';
            $userEmail = $subscriptionData["email"];
            break;
        case "new order admin":
            $subject = 'New Order from Select Tasting';
            $title = 'You have received a new order';
            $content = '<p>You&apos;ve received a new order</p>';
            $content .= '<h4>Order details</h4>';
            $content .= '<p>' . $subscriptionData["product_description"] . '</p>';
            $content .= '<h4 style="margin-bottom: 0">Customer detail</h4>';
            $content .= '<table><tbody>';
            $content .= '<tr><td>Name: </td><td>' . $subscriptionData["name"] . '</td></tr>';
            $content .= '<tr><td>Email: </td><td>' . $subscriptionData["email"] . '</td></tr>';
            $content .= '<tr><td>Phone: </td><td>' . $subscriptionData["phone"] . '</td></tr>';
            $content .= '</tbody></table>';
            $content .= getOrderDetail($subscriptionData);
            $signature = null;
            $userEmail = 'info@selecttasting.com';
            break;
        case "update customer":
            $subject = 'Customer data update';
            $title = 'Customer data update';
            $content = '<h4 style="margin-bottom: 0">Customer detail</h4>';
            $content .= '<table><tbody>';
            $content .= '<tr><td>Name: </td><td>' . $subscriptionData["name"] . '</td></tr>';
            $content .= '<tr><td>Email: </td><td>' . $subscriptionData["email"] . '</td></tr>';
            $content .= '<tr><td>Phone: </td><td>' . $subscriptionData["phone"] . '</td></tr>';
            $content .= '</tbody></table>';
            $content .= getOrderDetail($subscriptionData);
            $signature = null;
            $userEmail = 'info@selecttasting.com';
            break;
        case 'reset password':
            $subject = 'Reset password - Select Tasting';
            $title = 'Reset your password';
            $content = '<p>This is the procedure for setting a new password in your Select Tasting account. If you have not requested a password reset, please do not consider this email.</p>';
            $content .= '<p>To reset your password click on the link below and update your profile. Note that the link is only valid for 24 hours.</p>';
            $content .= '<p>' . $url . '</p>';
            $signature = '<p>Kind regards,<br>the Select Tasting staff</p>';
            break;
        case 'import customer':
            $subject = 'New Customer Portal - Select Tasting';
            $title = 'New Customer Portal';
            $content = "<p>Dear Club Brunello member,<br>A new customer portal has been finally integrated into our system. You will be able to manage your payment method and all your shipping information in case you need to update any of them.</p>";
            $content .= "<p>Please complete your registration by generating your personal password following the link below.</p>";
            $content .= "<p>" . $url . "</p>";
            $content .= "<p>Thank you very much for your continuous support and we can't wait to share more great Brunello and Barolo with you very soon.</p>";
            $signature = "<p>Kind regards,<br>Aleksandra and the Select Tasting staff</p>";
            break;
    }

    $mail = new PHPMailer(true);

    $body = file_get_contents('email.html');
    $body = str_replace(array("%%__TITLE__%%", "%%__CONTENT__%%", "%%__SIGNATURE__%%"), array($title, $content, $signature), $body);

    try {
        //Server settings
//        $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'mail.wineclub-selecttasting.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'customer@wineclub-selecttasting.com';                     //SMTP username
        $mail->Password   = '[gz=QFPW13ux';                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;         //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
        $mail->Port       = 465;                                    //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

        //Recipients
        $mail->setFrom('customer@wineclub-selecttasting.com', 'Select Tasting');
        $mail->addAddress($userEmail);     //Add a recipient
        $mail->addReplyTo('info@selecttasting.com', 'Select Tasting');

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        $mail->send();
        return "Email inviata con successo";
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        $mail->getSMTPInstance()->reset();
    }

    //Clear all addresses and attachments for the next iteration
    $mail->clearAddresses();
    $mail->clearAttachments();
}

function getOrderDetail($subscriptionData)
{
    $content = '<h4 style="margin-bottom: 0">Billing address</h4>';
    $content .= '<table><tbody><tr><td>Address: </td><td>' . $subscriptionData["address"]["line1"];
    $content .= '<br>' . $subscriptionData["address"]["line2"] . '</td></tr>';
    $content .= '<tr><td>City: </td><td>' . $subscriptionData["address"]["city"] . '</td></tr>';
    $content .= '<tr><td>Postal code: </td><td>' . $subscriptionData["address"]["postal_code"] . '</td></tr>';
    $content .= '<tr><td>State: </td><td>' . $subscriptionData["address"]["state"] . '</td></tr>';
    $content .= '<tr><td>Country: </td><td>' . $subscriptionData["address"]["country"] . '</td></tr>';
    $content .= '</tbody></table>';
    $content .= '<h4 style="margin-bottom: 0">Shipping address</h4>';
    $content .= '<table><tbody>';
    $content .= '<tr><td>To: </td><td>' . $subscriptionData["shipping"]["name"] . '</td></tr>';
    $content .= '<tr><td>Address: </td><td>' . $subscriptionData["shipping"]["address"]["line1"];
    $content .= '<br>' . $subscriptionData["shipping"]["address"]["line2"] . '</td></tr>';
    $content .= '<tr><td>Postal code: </td><td>' . $subscriptionData["shipping"]["address"]["postal_code"] . '</td></tr>';
    $content .= '<tr><td>State: </td><td>' . $subscriptionData["shipping"]["address"]["state"] . '</td></tr>';
    $content .= '<tr><td>Country: </td><td>' . $subscriptionData["shipping"]["address"]["country"] . '</td></tr>';
    $content .= '</tbody></table>';

    return $content;
}