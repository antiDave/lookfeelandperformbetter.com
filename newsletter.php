<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './PHPMailer-master/src/Exception.php';
require './PHPMailer-master/src/PHPMailer.php';
require './PHPMailer-master/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $formType = isset($_POST['form_type']) ? sanitize_input($_POST['form_type']) : '';

    if ($formType === 'newsletter') {
        $email = isset($_POST['news-email']) ? sanitize_input($_POST['news-email']) : '';
        $message = "Newsletter subscriber \n";
    } elseif ($formType === 'offer') {
        $email = isset($_POST['offer-email']) ? sanitize_input($_POST['offer-email']) : '';
        $message = "Special Offer subscriber \n";
    } elseif ($formType === 'info') {
        $email = isset($_POST['news-email']) ? sanitize_input($_POST['news-email']) : '';
        $message = "More Info subscriber \n";
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid form type']);
        exit();
    }

    $message .= "Email: $email\n";

    // Send email using PHPMailer
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp-relay.gmail.com';
        $mail->Port = 587;
        $mail->SMTPAuth = false;
        $mail->SMTPAutoTLS = true;

        // Recipients
        $mail->setFrom('dave@lookfeelandperformbetter.com', 'Dave');
        $mail->addAddress('usamtg@hotmail.com', 'Recipient Name');

        // Content
        $mail->isHTML(false);
        $mail->Subject = 'New Form Submission';
        $mail->Body = $message;
        $mail->SMTPDebug = 2;
        $mail->Debugoutput = 'html';

        if (!$mail->send()) {
            echo json_encode(['success' => false, 'error' => $mail->ErrorInfo]);
            exit();
        } else {
            echo json_encode(['success' => true]);
            exit();
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $mail->ErrorInfo]);
        exit();
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit();
}

function sanitize_input($input)
{
    $sanitized_input = filter_var($input, FILTER_SANITIZE_STRING);
    return htmlspecialchars($sanitized_input, ENT_QUOTES, 'UTF-8');
}