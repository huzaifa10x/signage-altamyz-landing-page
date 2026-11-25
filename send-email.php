<?php
header("Content-Type: application/json");

require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$data = json_decode(file_get_contents("php://input"), true);

if ($data) {
    // Inputs
    $name       = trim($data['name'] ?? '');
    $from_email = filter_var(trim($data['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $phone      = trim($data['phone'] ?? '');
    $subject    = trim($data['subject'] ?? '');
    $message    = trim($data['message'] ?? '');
    
    // Dynamic CC Input (agar form se aaraha ho)
    $cc_email   = filter_var(trim($data['cc_email'] ?? ''), FILTER_VALIDATE_EMAIL);

    $mail = new PHPMailer(true);

    try {
        // --- Server Settings ---
        $mail->isSMTP();
        $mail->Host       = 'signage.altamyz.ae';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'enquires@signage.altamyz.ae';
        $mail->Password   = 'XAR(E,M+rYs=HU()'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // --- Recipients ---
        // SENDER: Server Email
        $mail->setFrom('enquires@signage.altamyz.ae', 'Signage Altamyz Landing Page ');
        
        // RECIPIENT: Main Target Email
        $mail->addAddress('sales@altamyz.ae'); 

        // OPTION 1: Fixed CC Email
        $mail->addCC('rahul@10xdigital.ae');

        // REPLY-TO: User Form Email
        if ($from_email) {
            $mail->addReplyTo($from_email, $name);
        }

        // --- Content ---
        $mail->isHTML(false);
        $mail->Subject = $subject ?: "New Contact Form Submission";
        $mail->Body    = "Name: $name\nEmail: $from_email\nPhone: $phone\n\nMessage:\n$message";

        $mail->send();
        echo json_encode(["status" => "success", "message" => "Email sent successfully"]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => "Mail sending failed: " . $mail->ErrorInfo
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid input data"]);
}