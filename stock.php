<?php
include 'includes/db.php';
include 'includes/navbar.php';

$categoryFilter = isset($_GET['category']) ? $_GET['category'] : '';

// Fetch categories
$categoriesStmt = $pdo->query("SELECT id, name FROM categories ORDER BY name");
$categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch items
$sql = "
    SELECT i.id, i.item_name, i.stock, c.name AS category_name
    FROM items i
    LEFT JOIN categories c ON i.category_id = c.id
";

$params = [];
if ($categoryFilter !== '') {
    $sql .= " WHERE (i.category_id = :cat OR i.category_id IS NULL)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['cat' => $categoryFilter]);
} else {
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
}

$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Stock Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .low-stock { background-color: #fff3cd !important; }
    </style>
</head>
<body>
<div class="container mt-5">
    <h3 class="mb-4">Stock Report</h3>

    <form method="get" class="row mb-3">
        <div class="col-md-4">
            <label class="form-label">Filter by Category:</label>
            <select name="category" class="form-select" onchange="this.form.submit()">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($categoryFilter == $cat['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>

    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>Item Name</th>
                <th>Category</th>
                <th>Purchased Qty</th>
                <th>Sold Qty</th>
                <th>Current Stock</th>
                <th>Status</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <?php
                    $itemId = $item['id'];
                    $purchasedQty = $item['stock'];

                    $soldStmt = $pdo->prepare("SELECT COALESCE(SUM(quantity), 0) as total_sold FROM sale_items WHERE item_id = ?");
                    $soldStmt->execute([$itemId]);
                    $soldQty = $soldStmt->fetchColumn();

                    $currentStock = $purchasedQty - $soldQty;
                    $status = ($currentStock < 5) ? "Low Stock" : "In Stock";
                    $rowClass = ($currentStock < 5) ? "low-stock" : "";
                    $modalId = "detailsModal" . $itemId;
                ?>
                <tr class="<?= $rowClass ?>">
                    <td><?= htmlspecialchars($item['item_name']) ?></td>
                    <td><?= htmlspecialchars($item['category_name'] ?? 'Uncategorized') ?></td>
                    <td><?= $purchasedQty ?></td>
                    <td><?= $soldQty ?></td>
                    <td><strong><?= $currentStock ?></strong></td>
                    <td><?= $status ?></td>
                    <td>
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#<?= $modalId ?>">
                            View
                        </button>
                    </td>
                </tr>

                <!-- Move modal outside of <tr> for safety -->
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modals placed at the end -->
<?php foreach ($items as $item): ?>
    <?php
        $itemId = $item['id'];
        $modalId = "detailsModal" . $itemId;
        $salesStmt = $pdo->prepare("
            SELECT si.quantity, si.price
            FROM sale_items si
            WHERE si.item_id = ?
            ORDER BY si.id DESC
        ");
        $salesStmt->execute([$itemId]);
        $sales = $salesStmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <div class="modal fade" id="<?= $modalId ?>" tabindex="-1" aria-labelledby="<?= $modalId ?>Label" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="<?= $modalId ?>Label">Details - <?= htmlspecialchars($item['item_name']) ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6>Sales:</h6>
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>Qty</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($sales): foreach ($sales as $s): ?>
                                <tr>
                                    <td><?= $s['quantity'] ?></td>
                                    <td>₹<?= number_format($s['price'], 2) ?></td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="2">No sales found</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <h6>Purchases:</h6>
                    <p>Items added directly in the <strong>items</strong> table are considered as purchases.</p>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
