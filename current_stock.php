<?php
include '../includes/header.php';
require_once '../config/db.php';

$products = $pdo->query("SELECT * FROM products ORDER BY name")->fetchAll();
?>

<h3>Current Stock</h3>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Name</th><th>Category</th><th>Type</th><th>Price</th><th>Quantity</th><th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $p): ?>
            <tr>
                <td><?= $p['name'] ?></td>
                <td><?= $p['category'] ?></td>
                <td><?= $p['type'] ?></td>
                <td>₹<?= $p['price'] ?></td>
                <td><?= $p['quantity'] ?></td>
                <td>
                    <?php if ($p['quantity'] <= 5): ?>
                        <span class="badge bg-danger">Low Stock</span>
                    <?php else: ?>
                        <span class="badge bg-success">In Stock</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include '../includes/footer.php'; ?>
