<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './PHPMailer-master/src/Exception.php';
require './PHPMailer-master/src/PHPMailer.php';
require './PHPMailer-master/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    $name = isset($_POST['name']) ? sanitize_input($_POST['name']) : '';
    $phone = isset($_POST['phone']) ? sanitize_input($_POST['phone']) : '';
    $email = isset($_POST['email']) ? sanitize_input($_POST['email']) : '';
    $bestTime = isset($_POST['best-time']) ? sanitize_input($_POST['best-time']) : '';
    $bestWay = isset($_POST['best-way']) ? sanitize_input($_POST['best-way']) : '';
    $interestedService = isset($_POST['interested-service']) ? sanitize_input($_POST['interested-service']) : '';
    $stemCellPreparation = isset($_POST['stemCellPreparation']) ? 'Yes' : 'No';
    $textAppointments = isset($_POST['text-appointments']) ? 'Yes' : 'No';
    $textOffers = isset($_POST['text-offers']) ? 'Yes' : 'No';

    // Construct email message
    $message = "Name: $name\n";
    $message .= "Phone: $phone\n";
    $message .= "Email: $email\n";
    $message .= "Best Time to Contact: $bestTime\n";
    $message .= "Best Way to Contact: $bestWay\n";
    $message .= "Interested Service: $interestedService\n";
    $message .= "Stem Cell Preparation: $stemCellPreparation\n";
    $message .= "Receive Text Messages for Appointments: $textAppointments\n";
    $message .= "Receive Text Messages for Special Offers: $textOffers\n";



    // Send email using PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp-relay.gmail.com';
        $mail->Port = 587; // or 465
        $mail->SMTPAuth = false; // No authentication
        $mail->SMTPAutoTLS = true; // Enable TLS encryption

        // Recipients
        $mail->setFrom('dave@lookfeelandperformbetter.com', 'Dave');
        $mail->addAddress('usamtg@hotmail.com', 'Recipient Name');

        // Content
        $mail->isHTML(false);
        $mail->Subject = 'New Form Submission';
        $mail->Body = $message;
        $mail->SMTPDebug = 2;
        $mail->Debugoutput = 'html';

        // Attempt to send the email
        if ($mail->send()) {
            // Email sent successfully, redirect to thank you page
            header("Location: thankyou.html");
            exit();
        } else {
            // Email sending failed
            echo "Email sending failed: " . $mail->ErrorInfo;
        }
    } catch (Exception $e) {
        // PHPMailer exception occurred
        echo "Error sending email: " . $e->getMessage();
    }
} else {
    // Invalid request method, redirect to index page
    header("Location: index.php");
    exit();
}

function sanitize_input($input)
{
    // Using filter_var() for basic input sanitization
    $sanitized_input = filter_var($input, FILTER_SANITIZE_STRING);

    // Using htmlspecialchars() to convert special characters to HTML entities
    return htmlspecialchars($sanitized_input, ENT_QUOTES, 'UTF-8');
}
