<?php
session_start();
require_once 'includes/db.php';
include 'includes/navbar.php';

// Fetch items using PDO
try {
    $stmt = $pdo->prepare("SELECT * FROM items");
    $stmt->execute();
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching items: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Items - Sports Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2 class="mb-4">Items List</h2>
    <a href="add_item.php" class="btn btn-success mb-3">+ Add New Item</a>
    
    <?php if (count($items) > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Brand</th>
                        <th>MRP</th>
                        <th>Discount (%)</th>
                        <th>CGST (%)</th>
                        <th>SGST (%)</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['id']) ?></td>
                            <td><?= htmlspecialchars($item['item_name']) ?></td>
                            <td><?= htmlspecialchars($item['category_id']) ?></td>
                            <td><?= htmlspecialchars($item['brand_id']) ?></td>
                            <td>₹<?= htmlspecialchars($item['mrp_price']) ?></td>
                            <td><?= htmlspecialchars($item['discount']) ?></td>
                            <td><?= htmlspecialchars($item['cgst']) ?></td>
                            <td><?= htmlspecialchars($item['sgst']) ?></td>
                            <td>
                                <?php if (!empty($item['image'])): ?>
                                    <img src="uploads/<?= htmlspecialchars($item['image']) ?>" width="50" height="50" alt="Item Image">
                                <?php else: ?>
                                    No Image
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="edit_item.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="delete_item.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this item?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>No items found.</p>
    <?php endif; ?>
</div>

</body>
</html>
