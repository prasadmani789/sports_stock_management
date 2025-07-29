<?php
require 'includes/db.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
require 'phpmailer/src/Exception.php';
require_once 'dompdf/vendor/autoload.php'; // Ensure this path is correct

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
use Dompdf\Dompdf;

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('<div style="color:red;">❌ Invalid sale ID.</div>');
}

$sale_id = (int)$_GET['id'];

// Fetch sale and customer details
$stmt = $pdo->prepare("
    SELECT s.*, c.name AS customer_name, c.email
    FROM sales s
    JOIN customers c ON s.customer_id = c.id
    WHERE s.id = ?
");
$stmt->execute([$sale_id]);
$sale = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$sale) {
    die('<div style="color:red;">❌ Sale not found.</div>');
}

// Fetch sale items
$stmt = $pdo->prepare("
    SELECT si.*, i.item_name
    FROM sale_items si
    JOIN items i ON si.item_id = i.id
    WHERE si.sale_id = ?
");
$stmt->execute([$sale_id]);
$sale_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Generate PDF from template
ob_start();
include 'templates/sale_invoice_template.php'; // Your invoice HTML template
$html = ob_get_clean();

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$pdfOutput = $dompdf->output();

// Ensure tmp folder exists
if (!is_dir('tmp')) {
    mkdir('tmp', 0777, true);
}

$pdfPath = "tmp/invoice_sale_$sale_id.pdf";
file_put_contents($pdfPath, $pdfOutput);

// Send Email with PHPMailer
$mail = new PHPMailer(true);

try {
    $mail->SMTPDebug = SMTP::DEBUG_OFF; // Change to DEBUG_SERVER for full logs
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'managerquotation@gmail.com';   // Your Gmail
    $mail->Password   = 'lxjhpohgagslmvpl';             // Your App Password (no spaces)
    $mail->SMTPSecure = SMTP::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('managerquotation@gmail.com', 'Sports Shop');
    $mail->addAddress($sale['email'], $sale['customer_name']);
    $mail->addAttachment($pdfPath, 'Invoice_' . $sale_id . '.pdf');

    $mail->isHTML(true);
    $mail->Subject = "Invoice for Sale #{$sale_id}";
    $mail->Body    = "
        Dear {$sale['customer_name']},<br><br>
        Thank you for your purchase. Please find the attached invoice.<br><br>
        Regards,<br>
        <strong>Sports Shop</strong>
    ";

    $mail->send();
    echo "<div style='color:green; font-weight:bold;'>✅ Email sent successfully to {$sale['email']}.</div>";
} catch (Exception $e) {
    echo "<div style='color:red; font-weight:bold;'>❌ Email failed: {$mail->ErrorInfo}</div>";
}

// Clean up temp file
if (file_exists($pdfPath)) {
    unlink($pdfPath);
}
?>
