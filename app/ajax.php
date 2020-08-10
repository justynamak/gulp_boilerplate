<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

error_reporting(0);

require_once(__DIR__.'/phpmailer/PHPMailer.php');
require_once(__DIR__.'/phpmailer/Exception.php');
require_once(__DIR__.'/phpmailer/SMTP.php');
require_once(__DIR__.'/validate.php');

$response = [
    'error' => true,
    'errors' => []
];

$validate = new Validate();

$html = '';
$subject = '';

switch (filter_input(INPUT_POST, 'formName', FILTER_SANITIZE_STRING)) {

    default:
    case 'contactForm':
        require_once(__DIR__.'/contact.php');
        break;

}

if(!empty($response['errors'])){

   die(json_encode($response));
}

$mail = new PHPMailer(true);

try {
    
    //Server settings
    $mail->SMTPDebug = 0;
    $mail->isSMTP();
    $mail->Host       = 'mail23.mydevil.net';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'smtp@brandwisegroup.pl';
    $mail->Password   = 'mBzYbdIL90U3MP7iNVqo';
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 25;

    //Recipients
    $mail->setFrom('smtp@brandwisegroup.pl', 'Brandwise Mailer');
    // $mail->addAddress('office@shakeitupevent.com', 'Shake it up');
    $mail->addAddress('justyna.makuch@brandwise.pl', 'formularz');

   


    if (isset($_FILES) && count($_FILES)) {

        foreach ($_FILES as $file) {

            $mail->addAttachment($file['tmp_name'], $file['name']); 
        }
    }
       
    // Content
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->msgHTML($html);
    $mail->send();

    $response['error'] = false;
    $response['message'] = 'Pomyślnie wysłano wiadomość';
    
    die(json_encode($response));

} catch (Exception $e) {
    
    $response['errors'][] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";

    die(json_encode($response));
}