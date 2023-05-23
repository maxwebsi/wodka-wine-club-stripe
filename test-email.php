<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'mail.wineclub-selecttasting.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'customer@wineclub-selecttasting.com';                     //SMTP username
    $mail->Password   = '[gz=QFPW13ux';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;         //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
    $mail->Port       = 465;                                    //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

    //Recipients
    $mail->setFrom('customer@wineclub-selecttasting.com', 'Select Tasting');
    $mail->addAddress('assistenza@tobugroup.com', 'Assistenza Tobugroup');     //Add a recipient
    $mail->addReplyTo('massimiliano.bacchini@tobugroup.com', 'Massimiliano Bacchini TOBU');

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = 'Here is the subject';
    $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}

die();




        $subject = 'Oggetto della mail';
        $title = 'Titolo della mail';
        $content = '<p>Contenuto</p>';
        $signature = '<p>kind regards,<br>the Select Tasting staff</p>';


// Multiple recipients
$to = 'massimiliano.bacchini@gmail.com'; // note the comma

// Subject
$subject = 'Thank you for the registation';

$body = file_get_contents('email.html');

// Message
$message = '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>Select Tasting</title>
</head>
<body marginwidth="0" topmargin="0" marginheight="0" offset="0" style="background-color: #aeb3a2;">
<div id="wrapper">
    <table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
        <tbody>
            <tr>
                <td align="center" valign="top">
                    <table border="0" cellpadding="0" cellspacing="0" width="600" id="template_container"
                           style="background-color: #27525e;">
                        <tbody>
                            <tr>
                                <td align="center" valign="top">
                                    <!-- Header -->
                                    <table border="0" cellpadding="0" cellspacing="0" width="100%" id="template_header">
                                        <tr>
                                            <p style="margin-top:0;">
                                                <img src="http://localhost/wine-club-selecttasting/sub/checkout-single-subscription/dev/img/select-tasting-logo-white.png"
                                                     width="60" height="60" alt="Select Tasting"/>
                                            </p>
                                        </tr>
                                        <tr>
                                            <td id="header_wrapper" style="text-align: center; color: #fff;">
                                                <h1>' . $title . '</h1>
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- End Header -->
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td align="center" valign="top">
                    <!-- Footer -->
                    <table border="0" cellpadding="10" cellspacing="0" width="600" id="template_footer"
                           style="background-color: #fff;">
                        <tr>
                            <td align="center" valign="top">
                                <!-- Body -->
                                <table border="0" cellpadding="0" cellspacing="0" width="580" id="template_body">
                                    <tr>
                                        <td valign="top" id="body_content">
                                            <!-- Content -->
                                            <table border="0" cellpadding="20" cellspacing="0" width="100%">
                                                <tr>
                                                    <td valign="top">
                                                        <div id="body_content_inner"><p>' . $content . '</p></div>
                                                    </td>
                                                </tr>
                                            </table>
                                            <!-- End Content -->
                                        </td>
                                    </tr>
                                </table>
                                <!-- End Body -->
                            </td>
                        </tr>
                        <tr>
                            <td valign="top">
                                <table border="0" cellpadding="10" cellspacing="0" width="100%">
                                    <tr>
                                        <td colspan="2" valign="middle" id="credit">' . $signature . '</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <!-- End Footer -->
                </td>
            </tr>
        </tbody>
    </table>
</div>
</body>
</html>
';

// To send HTML mail, the Content-type header must be set
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'Content-type: text/html; charset=UTF-8';
//
//// Additional headers
$headers[] = 'From: Select Tasting <alexandra@selecttasting.com>';

$headers[] = "Organization: Select Tasting";

$headers[] = "Reply-To: Select Tasting <alexandra@selecttasting.com>";
$headers[] = "Return-Path: Select Tasting <alexandra@selecttasting.com>";
//
//$headers[] = "X-Priority: 3";
//$headers[] = "X-Mailer: PHP". phpversion();


// Mail it
$testEmail = mail($to, $subject, $message, implode("\r\n", $headers));

$prova = false;
