<?php
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Aws\Ses\SesClient;


$config = include 'config.php';

$MyEmail = $config['MyEmail'];
$password = $config['password'];


// The SDK will automatically use credentials from environment variables, IAM roles, or AWS credentials file
$sesClient = new SesClient([
    'version' => 'latest',
    'region' => 'ap-southeast-2', // Change to your AWS region
]);


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $user_name = $_POST["name"]; // Retrieve user name
    $user_email = $_POST["email"]; // Retrieve user email
    $message = $_POST["message"];
    $sourceEmail = $MyEmail; // Verified email address

    // Validate the user-provided email address
    $userProvidedEmail = $user_email;

    if ($userProvidedEmail !== false && $userProvidedEmail !== '') {
        // Create a PHPMailer instance
        $mail = new PHPMailer(true);

        try {
            // Set SMTP configuration
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = $MyEmail; // Replace with your Gmail address
            $mail->Password   = $password; // Replace with your Gmail password
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            // Sender and recipient details
            $mail->setFrom($userProvidedEmail);
            $mail->addAddress($MyEmail);

            // Email content
            $mail->isHTML(false);
            $mail->Subject = 'New Contact Form Submission';
            $mail->Body    = $message;

            // Add Reply-To header
            $mail->addReplyTo($userProvidedEmail, $user_name);

            // Send email
            $mail->send();
            echo 'Email sent successfully!';
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo 'Invalid email address for Reply-To header.';
       
    }
}
?>