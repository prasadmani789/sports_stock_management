<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';
include 'includes/header.php';

$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
?>

<h3>All Products</h3>
<a href="add_product.php" class="btn btn-primary mb-3">+ Add Product</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th><th>Name</th><th>Category</th><th>Type</th><th>Price</th><th>Qty</th><th>Image</th><th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($products as $p): ?>
        <tr>
            <td><?= $p['id'] ?></td>
            <td><?= $p['name'] ?></td>
            <td><?= $p['category'] ?></td>
            <td><?= $p['type'] ?></td>
            <td>₹<?= $p['price'] ?></td>
            <td><?= $p['quantity'] ?></td>
            <td>
                <?php if ($p['image']): ?>
                    <img src="../assets/images/<?= $p['image'] ?>" width="50">
                <?php endif; ?>
            </td>
            <td>
                <a href="edit_product.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                <a href="delete_product.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this product?')">Delete</a>
				<a href="export_products.php" class="btn btn-sm btn-success mb-3">
				<i class="bi bi-download"></i> Export to CSV </a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php include '../includes/footer.php'; ?>
