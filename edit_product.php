<?php
include '../includes/header.php';
require_once '../config/db.php';

$id = $_GET['id'];
$product = $pdo->query("SELECT * FROM products WHERE id = $id")->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $type = $_POST['type'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $image = $product['image'];

    if ($_FILES['image']['name']) {
        $targetDir = "../assets/images/";
        $image = time() . "_" . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $targetDir . $image);
    }

    $stmt = $pdo->prepare("UPDATE products SET name=?, category=?, type=?, price=?, quantity=?, image=? WHERE id=?");
    $stmt->execute([$name, $category, $type, $price, $quantity, $image, $id]);

    header("Location: view_products.php");
    exit();
}
?>

<h3>Edit Product</h3>

<form method="post" enctype="multipart/form-data">
    <div class="mb-3">
        <label>Product Name</label>
        <input type="text" name="name" class="form-control" value="<?= $product['name'] ?>" required />
    </div>
    <div class="mb-3">
        <label>Category</label>
        <input type="text" name="category" class="form-control" value="<?= $product['category'] ?>" />
    </div>
    <div class="mb-3">
        <label>Type</label>
        <input type="text" name="type" class="form-control" value="<?= $product['type'] ?>" />
    </div>
    <div class="mb-3">
        <label>Price</label>
        <input type="number" name="price" step="0.01" class="form-control" value="<?= $product['price'] ?>" />
    </div>
    <div class="mb-3">
        <label>Quantity</label>
        <input type="number" name="quantity" class="form-control" value="<?= $product['quantity'] ?>" />
    </div>
    <div class="mb-3">
        <label>Image</label><br>
        <?php if ($product['image']): ?>
            <img src="../assets/images/<?= $product['image'] ?>" width="70"><br><br>
        <?php endif; ?>
        <input type="file" name="image" class="form-control" />
    </div>
    <button class="btn btn-primary">Update Product</button>
</form>

<?php include '../includes/footer.php'; ?>
