<?php
require_once 'includes/db.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM items WHERE id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch();

$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
$brands = $pdo->query("SELECT * FROM brands")->fetchAll();
$types = $pdo->query("SELECT * FROM types")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $category_id = $_POST['category_id'];
    $type_id = $_POST['type_id'];
    $brand_id = $_POST['brand_id'];
    $mrp = $_POST['mrp'];
    $discount = $_POST['discount'];
    $cgst = $_POST['cgst'];
    $sgst = $_POST['sgst'];

    $update = $pdo->prepare("UPDATE items SET name=?, category_id=?, type_id=?, brand_id=?, mrp=?, discount=?, cgst=?, sgst=? WHERE id=?");
    $update->execute([$name, $category_id, $type_id, $brand_id, $mrp, $discount, $cgst, $sgst, $id]);

    header("Location: items.php?success=Item '$name' updated successfully.");
    exit;
}
?>

<!-- Basic form UI -->
<!DOCTYPE html>
<html>
<head>
    <title>Edit Item</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5">
    <h3>Edit Item</h3>
    <form method="POST">
        <input type="text" name="name" value="<?= $item['name'] ?>" class="form-control mb-2" required>

        <select name="category_id" class="form-control mb-2" required>
            <option value="">Select Category</option>
            <?php foreach ($categories as $c): ?>
                <option value="<?= $c['id'] ?>" <?= $item['category_id'] == $c['id'] ? 'selected' : '' ?>><?= $c['name'] ?></option>
            <?php endforeach; ?>
        </select>

        <select name="type_id" class="form-control mb-2" required>
            <option value="">Select Type</option>
            <?php foreach ($types as $t): ?>
                <option value="<?= $t['id'] ?>" <?= $item['type_id'] == $t['id'] ? 'selected' : '' ?>><?= $t['name'] ?></option>
            <?php endforeach; ?>
        </select>

        <select name="brand_id" class="form-control mb-2" required>
            <option value="">Select Brand</option>
            <?php foreach ($brands as $b): ?>
                <option value="<?= $b['id'] ?>" <?= $item['brand_id'] == $b['id'] ? 'selected' : '' ?>><?= $b['name'] ?></option>
            <?php endforeach; ?>
        </select>

        <input type="number" step="0.01" name="mrp" value="<?= $item['mrp'] ?>" class="form-control mb-2" placeholder="MRP" required>
        <input type="number" step="0.01" name="discount" value="<?= $item['discount'] ?>" class="form-control mb-2" placeholder="Discount (%)" required>
        <input type="number" step="0.01" name="cgst" value="<?= $item['cgst'] ?>" class="form-control mb-2" placeholder="CGST (%)" required>
        <input type="number" step="0.01" name="sgst" value="<?= $item['sgst'] ?>" class="form-control mb-2" placeholder="SGST (%)" required>

        <button class="btn btn-primary">Update Item</button>
    </form>
</body>
</html>
