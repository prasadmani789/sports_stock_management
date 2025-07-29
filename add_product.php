<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';
include 'includes/header.php';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $type = $_POST['type'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $image = '';

    // Handle image upload
    if ($_FILES['image']['name']) {
        $targetDir = "assets/images/";
        $image = time() . "_" . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $targetDir . $image);
    }

    $stmt = $pdo->prepare("INSERT INTO products (name, category, type, price, quantity, image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $category, $type, $price, $quantity, $image]);

    $msg = "Product added successfully!";
}
?>

<div class="container mt-4">
    <h3>Add Product</h3>

    <?php if ($msg): ?>
        <div class="alert alert-success"><?= $msg ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label>Product Name</label>
            <input type="text" name="name" class="form-control" required />
        </div>
        <div class="mb-3">
            <label>Category</label>
            <input type="text" name="category" class="form-control" />
        </div>
        <div class="mb-3">
            <label>Type</label>
            <input type="text" name="type" class="form-control" />
        </div>
        <div class="mb-3">
            <label>Price</label>
            <input type="number" name="price" step="0.01" class="form-control" />
        </div>
        <div class="mb-3">
            <label>Initial Quantity</label>
            <input type="number" name="quantity" class="form-control" />
        </div>
        <div class="mb-3">
            <label>Image (optional)</label>
            <input type="file" name="image" class="form-control" />
        </div>
        <button class="btn btn-success">Add Product</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
