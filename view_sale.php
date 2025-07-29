<?php
include 'includes/db.php';
include 'includes/navbar.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  die('Invalid sale ID.');
}
$sale_id = (int) $_GET['id'];

$stmt = $pdo->prepare("
  SELECT s.*, c.name AS customer_name, c.email
  FROM sales s
  JOIN customers c ON s.customer_id = c.id
  WHERE s.id = ?
");
$stmt->execute([$sale_id]);
$sale = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$sale) {
  die('Sale not found.');
}

$stmt = $pdo->prepare("
  SELECT si.*, i.item_name
  FROM sale_items si
  JOIN items i ON si.item_id = i.id
  WHERE si.sale_id = ?
");
$stmt->execute([$sale_id]);
$sale_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Sale #<?= $sale_id ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    @media print {
      .no-print {
        display: none !important;
      }
    }
  </style>
</head>
<body>
<div class="container mt-4" id="printArea">
  <h2>Sale Details - ID #<?= $sale_id ?></h2>

  <div class="mb-4">
    <strong>Customer:</strong> <?= htmlspecialchars($sale['customer_name']) ?><br>
    <strong>Sale Date:</strong> <?= date('d-m-Y', strtotime($sale['sale_date'])) ?><br>
    <strong>Total Amount:</strong> ₹<?= number_format($sale['total_amount'], 2) ?><br>
    <strong>Email:</strong> <?= htmlspecialchars($sale['email'] ?? 'Not available') ?>
  </div>

  <table class="table table-bordered">
    <thead class="table-light">
      <tr>
        <th>Item Name</th>
        <th>MRP</th>
        <th>Discount (%)</th>
        <th>Special Discount (%)</th>
        <th>CGST (%)</th>
        <th>SGST (%)</th>
        <th>Quantity</th>
        <th>Final Price</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($sale_items as $item): 
        $disc = $item['discount'] + $item['special_discount'];
        $price_after_discount = $item['mrp'] - ($item['mrp'] * $disc / 100);
        $tax = ($price_after_discount * ($item['cgst'] + $item['sgst']) / 100);
        $final_price = $price_after_discount + $tax;
        $total = $final_price * $item['quantity'];
      ?>
        <tr>
          <td><?= htmlspecialchars($item['item_name']) ?></td>
          <td><?= number_format($item['mrp'], 2) ?></td>
          <td><?= $item['discount'] ?>%</td>
          <td><?= $item['special_discount'] ?>%</td>
          <td><?= $item['cgst'] ?>%</td>
          <td><?= $item['sgst'] ?>%</td>
          <td><?= $item['quantity'] ?></td>
          <td>₹<?= number_format($final_price, 2) ?></td>
          <td>₹<?= number_format($total, 2) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<div class="container mt-3 no-print">
  <a href="sales_list.php" class="btn btn-secondary">Back to Sales List</a>
  <button onclick="printPDF()" class="btn btn-outline-primary">Print / Save as PDF</button>
  <?php if (!empty($sale['email'])): ?>
    <button onclick="sendEmail(<?= $sale_id ?>)" class="btn btn-outline-success">Send Email</button>
  <?php endif; ?>
</div>

<script>
function printPDF() {
  window.print();
}

function sendEmail(saleId) {
  if (!confirm("Send sale invoice to customer's email?")) return;

  fetch('send_sale_email.php?id=' + saleId)
    .then(response => response.json())
    .then(data => {
      alert(data.message);
    })
    .catch(() => {
      alert('Failed to send email.');
    });
}
</script>
</body>
</html>
