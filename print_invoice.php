<?php
require_once '../config/db.php';

$id = $_GET['id'];
$sale = $pdo->query("SELECT * FROM sales WHERE id = $id")->fetch();
$items = $pdo->query("SELECT si.*, p.name FROM sale_items si JOIN products p ON si.product_id = p.id WHERE sale_id = $id")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Invoice #<?= $sale['id'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <h3>Sports Shop Invoice</h3>
    <p><strong>Customer:</strong> <?= $sale['customer_name'] ?><br>
    <strong>Date:</strong> <?= $sale['sale_date'] ?><br>
    <strong>Invoice ID:</strong> <?= $sale['id'] ?></p>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Product</th><th>Price</th><th>Qty</th><th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $i): ?>
                <tr>
                    <td><?= $i['name'] ?></td>
                    <td>₹<?= $i['price'] ?></td>
                    <td><?= $i['quantity'] ?></td>
                    <td>₹<?= $i['price'] * $i['quantity'] ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <th colspan="3" class="text-end">Total</th>
                <th>₹<?= $sale['total_amount'] ?></th>
            </tr>
        </tbody>
    </table>
</body>
</html>
