<?php
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name'])) {
    $stmt = $pdo->prepare("INSERT INTO brands (name) VALUES (?)");
    $stmt->execute([trim($_POST['name'])]);
    header("Location: brands.php");
    exit;
}

if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM brands WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: brands.php");
    exit;
}

$brands = $pdo->query("SELECT * FROM brands ORDER BY name")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Brands</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">
    <h2>Manage Brands</h2>
    <form method="POST" class="mb-4">
        <div class="input-group">
            <input type="text" name="name" class="form-control" placeholder="Enter new brand" required>
            <button class="btn btn-primary" type="submit">Add</button>
        </div>
    </form>
    <table class="table table-bordered">
        <thead><tr><th>#</th><th>Name</th><th>Action</th></tr></thead>
        <tbody>
            <?php foreach ($brands as $b): ?>
            <tr>
                <td><?= $b['id'] ?></td>
                <td><?= htmlspecialchars($b['name']) ?></td>
                <td><a href="?delete=<?= $b['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this brand?')">Delete</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
