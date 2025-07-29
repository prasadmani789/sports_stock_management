<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';
include 'includes/header.php';

$sales = $pdo->query("SELECT * FROM sales ORDER BY id DESC")->fetchAll();
?>

<h3>Sales History</h3>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th><th>Customer</th><th>Date</th><th>Total</th><th>Invoice</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($sales as $s): ?>
            <tr>
                <td><?= $s['id'] ?></td>
                <td><?= $s['customer_name'] ?></td>
                <td><?= $s['sale_date'] ?></td>
                <td>₹<?= $s['total_amount'] ?></td>
                <td>
                    <a href="print_invoice.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-info" target="_blank">View</a>
					<a href="generate_invoice_pdf.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" target="_blank">
					<i class="bi bi-file-earmark-pdf"></i> PDF</a>

                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include 'includes/footer.php'; ?>
