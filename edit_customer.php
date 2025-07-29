<?php
include 'includes/db.php';
include 'includes/navbar.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
$stmt->execute([$id]);
$customer = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    $update = $pdo->prepare("UPDATE customers SET name=?, phone=?, email=?, address=? WHERE id=?");
    $update->execute([$name, $phone, $email, $address, $id]);
    header("Location: customers.php?updated=1");
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Customer</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h2>Edit Customer</h2>
  <form method="POST">
    <div class="mb-3">
      <label>Name</label>
      <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($customer['name']) ?>" required>
    </div>
    <div class="mb-3">
      <label>Phone</label>
      <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($customer['phone']) ?>" required pattern="^\+91[0-9]{10}$">
    </div>
    <div class="mb-3">
      <label>Email (optional)</label>
      <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($customer['email']) ?>">
    </div>
    <div class="mb-3">
      <label>Address</label>
      <textarea name="address" class="form-control"><?= htmlspecialchars($customer['address']) ?></textarea>
    </div>
    <button type="submit" class="btn btn-success">Update</button>
    <a href="customers.php" class="btn btn-secondary">Back</a>
  </form>
</div>
</body>
</html>
