<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './PHPMailer-master/src/Exception.php';
require './PHPMailer-master/src/PHPMailer.php';
require './PHPMailer-master/src/SMTP.php';


// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get the raw POST data
    $post_data = $_POST['payload']; // Get the payload from the form

    $data = json_decode($post_data, true);
    
    // Extract lead information
    $lead_id = $data['lead_id'];
    $form_id = $data['form_id'];
    $campaign_id = $data['campaign_id'];
    $gcl_id = $data['gcl_id'];
    $adgroup_id = $data['adgroup_id'];
    $creative_id = $data['creative_id'];

    // Extract user column data
    $user_column_data = $data['user_column_data'];
    $full_name = '';
    $email = '';
    $phone_number = '';
    foreach ($user_column_data as $column) {
        if ($column['column_name'] === 'Full Name') {
            $full_name = $column['string_value'];
        } elseif ($column['column_name'] === 'User Phone') {
            $phone_number = $column['string_value'];
        } elseif ($column['column_name'] === 'User Email') {
            $email = $column['string_value'];
        }
    }

    
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
        $mail->Subject = 'New Lead Submission';
        $mail->Body = 'New lead submitted: ' . $full_name . ', ' . $email . ', ' . $phone_number;

        $mail->send();
    } catch (Exception $e) {
        http_response_code(500); // Internal Server Error
        echo "Error sending email: {$mail->ErrorInfo}";
        $stmt->close();
        $conn->close();
        exit();
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();

    // Send a response back to Google Ads
    http_response_code(200); // OK
    echo 'Success';
} else {
    // Not a POST request
    http_response_code(405); // Method Not Allowed
    echo 'Method Not Allowed';
}
?>
