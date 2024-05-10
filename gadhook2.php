<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './PHPMailer-master/src/Exception.php';
require './PHPMailer-master/src/PHPMailer.php';
require './PHPMailer-master/src/SMTP.php';

// Your secret key provided during webhook setup
$secret_key = '4Y4oGCReAnQLUIe7isg3NvlnDWOzMmd6awC8eTPNACkpKweJDM';

// Function to verify HMAC signature
function verify_signature($payload, $signature, $secret_key) {
    $calculated_signature = hash_hmac('sha256', $payload, $secret_key);
    return hash_equals($calculated_signature, $signature);
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the HMAC signature from the request headers
    $received_signature = $_SERVER['HTTP_X_GOOGLE_ADS_SIGNATURE'];

    // Get the raw POST data
    $post_data = $_POST['payload']; // Get the payload from the form
    
    // Verify HMAC signature
    if (!verify_signature($post_data, $received_signature, $secret_key)) {
        // Signature is not valid, reject the request
        http_response_code(403); // Forbidden
        echo "Invalid signature. Request rejected.";
        exit(); // Terminate script execution
    }

    // Decode the JSON data
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

    // Connect to the database
    $conn = new mysqli('82.165.214.221', 'usamtg', 'umCWaZq-hm7mxn:', 'leads');
    
    // Check connection
    if ($conn->connect_error) {
        http_response_code(500); // Internal Server Error
        die("Database connection failed: " . $conn->connect_error);
    }
    
    // Prepare the SQL statement using prepared statements to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO leads (name, email, phone) VALUES (?, ?, ?)");
    if (!$stmt) {
        http_response_code(500); // Internal Server Error
        echo "Error preparing SQL statement: " . $conn->error;
        $conn->close();
        exit();
    }
    
    // Bind parameters
    $bind_result = $stmt->bind_param("sss", $full_name, $email, $phone_number);
    if (!$bind_result) {
        http_response_code(500); // Internal Server Error
        echo "Error binding parameters: " . $stmt->error;
        $stmt->close();
        $conn->close();
        exit();
    }
    
    // Execute the statement
    $execute_result = $stmt->execute();
    if (!$execute_result) {
        http_response_code(500); // Internal Server Error
        echo "Error executing SQL statement: " . $stmt->error;
        $stmt->close();
        $conn->close();
        exit();
    }

    // Send email using PHPMailer
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp-relay.gmail.com';
        $mail->Port = 587; // or 465
        $mail->SMTPAuth = true; // Enable SMTP authentication
        $mail->SMTPSecure = 'tls'; // Enable TLS encryption

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
