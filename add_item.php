<?php
require_once 'includes/db.php';
require_once 'includes/navbar.php'; // Global navbar
session_start();

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_name = $_POST['item_name'];
    $category_id = $_POST['category_id'];
    $type_id = $_POST['type_id'];
    $brand_id = $_POST['brand_id'];
    $mrp_price = $_POST['mrp_price'];
    $discount = $_POST['discount'];
    $cgst = $_POST['cgst'];
    $sgst = $_POST['sgst'];

    // Handle image upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "uploads/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $image = $targetDir . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    }

    $stmt = $pdo->prepare("INSERT INTO items (item_name, category_id, type_id, brand_id, mrp_price, discount, cgst, sgst, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$item_name, $category_id, $type_id, $brand_id, $mrp_price, $discount, $cgst, $sgst, $image]);

    $message = $item_name;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Item - Sports Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #e3f2fd, #ffffff);
        }
        .card {
            border-radius: 1rem;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="bi bi-plus-circle-fill me-2"></i>Add New Item</h4>
            <div>
                <a href="add_item.php" class="btn btn-sm btn-light me-2"><i class="bi bi-arrow-clockwise"></i> Refresh</a>
                <a href="items.php" class="btn btn-sm btn-secondary"><i class="bi bi-box-arrow-left"></i> Back to Items</a>
            </div>
        </div>

        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Item Name</label>
                    <input type="text" name="item_name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select class="form-select" name="category_id" required>
                        <option value="">Select Category</option>
                        <?php
                        $stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
                        while ($row = $stmt->fetch()) {
                            echo "<option value='{$row['id']}'>{$row['name']}</option>";
                        }
                        ?>
                    </select>
                    <small class="text-muted">Manage from <a href="categories.php" target="_blank">Categories</a></small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Type</label>
                    <select class="form-select" name="type_id" required>
                        <option value="">Select Type</option>
                        <?php
                        $stmt = $pdo->query("SELECT * FROM types ORDER BY name ASC");
                        while ($row = $stmt->fetch()) {
                            echo "<option value='{$row['id']}'>{$row['name']}</option>";
                        }
                        ?>
                    </select>
                    <small class="text-muted">Manage from <a href="types.php" target="_blank">Types</a></small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Brand</label>
                    <select class="form-select" name="brand_id" required>
                        <option value="">Select Brand</option>
                        <?php
                        $stmt = $pdo->query("SELECT * FROM brands ORDER BY name ASC");
                        while ($row = $stmt->fetch()) {
                            echo "<option value='{$row['id']}'>{$row['name']}</option>";
                        }
                        ?>
                    </select>
                    <small class="text-muted">Manage from <a href="brands.php" target="_blank">Brands</a></small>
                </div>

                <div class="mb-3">
                    <label class="form-label">MRP Price</label>
                    <input type="number" step="0.01" name="mrp_price" class="form-control" required>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Discount (%)</label>
                        <input type="number" step="0.01" name="discount" class="form-control" value="0">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">CGST (%)</label>
                        <input type="number" step="0.01" name="cgst" class="form-control" value="0">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">SGST (%)</label>
                        <input type="number" step="0.01" name="sgst" class="form-control" value="0">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Item Image (optional)</label>
                    <input type="file" name="image" class="form-control">
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> Add Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if ($message): ?>
<!-- Toast Notification -->
<div class="position-fixed top-0 end-0 p-3" style="z-index: 1050">
    <div id="successToast" class="toast align-items-center text-bg-success border-0 show" role="alert">
        <div class="d-flex">
            <div class="toast-body">
                ✅ Item <strong><?= htmlspecialchars($message) ?></strong> added successfully!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
<script>
    setTimeout(() => {
        const toastEl = document.getElementById('successToast');
        if (toastEl) {
            const toast = new bootstrap.Toast(toastEl);
            toast.hide();
        }
    }, 4000);
</script>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
