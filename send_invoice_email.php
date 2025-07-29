<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
include 'config/db.php';

$sale_id = $_GET['id'];
$sale = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM sales WHERE id='$sale_id'"));

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.example.com';  // Replace with your SMTP
    $mail->SMTPAuth   = true;
    $mail->Username   = 'you@example.com';   // Replace
    $mail->Password   = 'yourpassword';      // Replace
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->setFrom('you@example.com', 'Sports Shop');
    $mail->addAddress('customer@example.com');  // Replace with customer's email

    $mail->isHTML(true);
    $mail->Subject = 'Invoice from Sports Shop';
    $mail->Body    = "Dear {$sale['customer_name']},<br><br>Attached is your invoice.<br><br>Thank you!";
    $mail->AltBody = 'Invoice from Sports Shop';

    // Generate PDF and attach
    $pdf = new Dompdf();
    $pdf->loadHtml("<h3>Invoice #{$sale['id']}</h3><p>Total: ₹{$sale['total']}</p>");
    $pdf->render();
    $attachment = $pdf->output();
    $mail->addStringAttachment($attachment, "Invoice_{$sale['id']}.pdf");

    $mail->send();
    echo "Invoice email sent.";
} catch (Exception $e) {
    echo "Message could not be sent. Error: {$mail->ErrorInfo}";
}
