<?php
require 'vendor/autoload.php';
use Dompdf\Dompdf;

include 'config/db.php';
$sale_id = $_GET['id'];

$query = mysqli_query($conn, "SELECT * FROM sales WHERE id='$sale_id'");
$sale = mysqli_fetch_assoc($query);

$html = "
<h3>Invoice #{$sale['id']}</h3>
<p><strong>Customer:</strong> {$sale['customer_name']}</p>
<p><strong>Date:</strong> {$sale['sale_date']}</p>
<table border='1' cellpadding='5' cellspacing='0' width='100%'>
  <tr>
    <th>Product</th><th>Quantity</th><th>Price</th><th>Total</th>
  </tr>
  <tr>
    <td>{$sale['product_name']}</td>
    <td>{$sale['quantity']}</td>
    <td>{$sale['price']}</td>
    <td>{$sale['total']}</td>
  </tr>
</table>
<h4>Total: ₹ {$sale['total']}</h4>
";

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->render();
$dompdf->stream("invoice_{$sale['id']}.pdf");
