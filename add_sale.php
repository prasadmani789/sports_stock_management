<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';
include 'includes/header.php';

// Fetch customers for dropdown
$customers = $pdo->query("SELECT id, name FROM customers ORDER BY name")->fetchAll();

// Fetch available products
$products = $pdo->query("SELECT * FROM products WHERE quantity > 0")->fetchAll();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = $_POST['customer_id'];
    $sale_date = $_POST['sale_date'];
    $products_sold = $_POST['products']; // array of product_id => qty

    $pdo->beginTransaction();

    try {
        // Step 1: Insert into sales
        $stmt = $pdo->prepare("INSERT INTO sales (customer_id, sale_date, total_amount) VALUES (?, ?, 0)");
        $stmt->execute([$customer_id, $sale_date]);
        $sale_id = $pdo->lastInsertId();

        $total = 0;

        // Step 2: Loop through sold items
        foreach ($products_sold as $product_id => $qty) {
            if ($qty <= 0) continue;

            $product = $pdo->query("SELECT price, quantity FROM products WHERE id = $product_id")->fetch();
            if (!$product || $product['quantity'] < $qty) continue;

            $price = $product['price'];
            $line_total = $price * $qty;
            $total += $line_total;

            // Insert into sale_items
            $stmt = $pdo->prepare("INSERT INTO sale_items (sale_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->execute([$sale_id, $product_id, $qty, $price]);

            // Reduce product stock
            $pdo->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?")->execute([$qty, $product_id]);
        }

        // Step 3: Update total in sales
        $pdo->prepare("UPDATE sales SET total_amount = ? WHERE id = ?")->execute([$total, $sale_id]);

        $pdo->commit();
        $message = "Sale recorded successfully!";
    } catch (Exception $e) {
        $pdo->rollBack();
        $message = "Error: " . $e->getMessage();
    }
}
?>

<h3>Record a Sale</h3>

<?php if ($message): ?>
    <div class="alert alert-info"><?= $message ?></div>
<?php endif; ?>

<form method="post">
    <div class="mb-3">
        <label>Customer</label>
        <select name="customer_id" class="form-select" required>
            <option value="">-- Select Customer --</option>
            <?php foreach ($customers as $cust): ?>
                <option value="<?= $cust['id'] ?>"><?= htmlspecialchars($cust['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3">
        <label>Sale Date</label>
        <input type="date" name="sale_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
    </div>

    <h5>Products</h5>
    <?php foreach ($products as $p): ?>
        <div class="mb-2">
            <label><?= htmlspecialchars($p['name']) ?> (₹<?= $p['price'] ?>) - Stock: <?= $p['quantity'] ?></label>
            <input type="number" name="products[<?= $p['id'] ?>]" class="form-control" placeholder="Qty" min="0">
        </div>
    <?php endforeach; ?>

    <button class="btn btn-primary">Submit Sale</button>
</form>

<?php include 'includes/footer.php'; ?>
