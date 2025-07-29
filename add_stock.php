<?php
session_start();
require_once 'includes/db.php';           // Ensures $pdo is available
require_once 'includes/functions.php';
include 'includes/header.php';            // Bootstrap + session check

// Fetch all products
try {
    $products = $pdo->query("SELECT id, name FROM products WHERE is_deleted = 0")->fetchAll();
} catch (Exception $e) {
    die("Error fetching products: " . $e->getMessage());
}

$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'] ?? '';
    $quantity = $_POST['quantity'] ?? 0;

    if ($product_id && $quantity > 0) {
        try {
            // Insert stock entry
            $stmt = $pdo->prepare("INSERT INTO stock_entries (product_id, quantity) VALUES (?, ?)");
            $stmt->execute([$product_id, $quantity]);

            // Update product quantity
            $update = $pdo->prepare("UPDATE products SET quantity = quantity + ? WHERE id = ?");
            $update->execute([$quantity, $product_id]);

            $message = "✅ Stock added successfully!";
        } catch (PDOException $e) {
            $message = "❌ Error: " . $e->getMessage();
        }
    } else {
        $message = "❌ Please select a valid product and quantity.";
    }
}
?>

<div class="container mt-4">
    <h3>Add Stock</h3>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label for="product_id" class="form-label">Product</label>
            <select name="product_id" id="product_id" class="form-select" required>
                <option value="">-- Select Product --</option>
                <?php foreach ($products as $p): ?>
                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="quantity" class="form-label">Quantity</label>
            <input type="number" name="quantity" id="quantity" class="form-control" min="1" required>
        </div>

        <button type="submit" class="btn btn-primary">Add Stock</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>