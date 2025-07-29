<?php
include 'includes/db.php';
include 'includes/navbar.php';

$reportType = isset($_GET['report_type']) ? $_GET['report_type'] : 'sales';
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : '';

$reportTitle = ucfirst($reportType) . " Report";

// Default values
$whereClause = '';
$params = [];
if ($startDate && $endDate) {
    $whereClause = " AND s.created_at BETWEEN :start AND :end";
    $params['start'] = $startDate . ' 00:00:00';
    $params['end'] = $endDate . ' 23:59:59';
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= $reportTitle ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h3 class="mb-3"><?= $reportTitle ?></h3>

    <form method="get" class="row g-3 mb-4">
        <div class="col-md-3">
            <label for="report_type" class="form-label">Select Report</label>
            <select name="report_type" id="report_type" class="form-select" onchange="this.form.submit()">
                <option value="sales" <?= ($reportType == 'sales') ? 'selected' : '' ?>>Sales Report</option>
                <option value="purchase" <?= ($reportType == 'purchase') ? 'selected' : '' ?>>Purchase Report</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="start_date" class="form-label">Start Date</label>
            <input type="date" id="start_date" name="start_date" class="form-control" value="<?= $startDate ?>">
        </div>
        <div class="col-md-3">
            <label for="end_date" class="form-label">End Date</label>
            <input type="date" id="end_date" name="end_date" class="form-control" value="<?= $endDate ?>">
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary">Filter</button>
        </div>
    </form>

    <?php if ($reportType == 'sales'): ?>
        <?php
        $sql = "
            SELECT si.*, i.item_name, s.created_at
            FROM sale_items si
            JOIN items i ON si.item_id = i.id
            JOIN sales s ON si.sale_id = s.id
            WHERE 1=1 $whereClause
            ORDER BY s.created_at DESC
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $totalRevenue = 0;
        $totalCost = 0;
        ?>
        <div class="mb-3">
            <strong>Total Records:</strong> <?= count($sales) ?>
        </div>
        <div class="mb-4">
            <?php foreach ($sales as $sale) {
                $totalRevenue += $sale['price'] * $sale['quantity'];
                $itemCost = ($sale['mrp'] * $sale['quantity']);
                $totalCost += $itemCost;
            } ?>
            <strong>Total Revenue:</strong> ₹<?= number_format($totalRevenue, 2) ?> <br>
            <strong>Total Cost (Based on MRP):</strong> ₹<?= number_format($totalCost, 2) ?> <br>
            <strong>Estimated Profit:</strong> ₹<?= number_format($totalRevenue - $totalCost, 2) ?>
        </div>

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Date</th>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sales as $sale): ?>
                    <tr>
                        <td><?= date('d-m-Y', strtotime($sale['created_at'])) ?></td>
                        <td><?= htmlspecialchars($sale['item_name']) ?></td>
                        <td><?= $sale['quantity'] ?></td>
                        <td>₹<?= number_format($sale['price'], 2) ?></td>
                        <td>₹<?= number_format($sale['price'] * $sale['quantity'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php else: ?>
        <?php
        // Purchase Report based on items table creation timestamp or similar logic (no separate purchase table)
        $sql = "SELECT i.*, c.name AS category_name FROM items i LEFT JOIN categories c ON i.category_id = c.id WHERE 1=1";
        if ($startDate && $endDate) {
            $sql .= " AND i.created_at BETWEEN :start AND :end";
        }
        $stmt = $pdo->prepare($sql);
        if ($startDate && $endDate) {
            $stmt->execute($params);
        } else {
            $stmt->execute();
        }
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <div class="mb-3">
            <strong>Total Records:</strong> <?= count($items) ?>
        </div>

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Stock</th>
                    <th>MRP</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['item_name']) ?></td>
                        <td><?= htmlspecialchars($item['category_name'] ?? 'Uncategorized') ?></td>
                        <td><?= $item['stock'] ?></td>
                        <td>₹<?= number_format($item['mrp_price'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <form method="post" action="export_report.php" target="_blank" class="mb-3 d-flex gap-2">
    <input type="hidden" name="report_type" value="<?= $reportType ?>">
    <input type="hidden" name="from" value="<?= htmlspecialchars($fromDate) ?>">
    <input type="hidden" name="to" value="<?= htmlspecialchars($toDate) ?>">
    <button type="submit" name="export_excel" class="btn btn-success btn-sm">Export to Excel</button>
    <button type="submit" name="export_pdf" class="btn btn-danger btn-sm">Export to PDF</button>
</form>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
