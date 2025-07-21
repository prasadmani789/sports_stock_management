<?php
$title = "Stock Management";
ob_start();
?>
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Current Stock</h6>
        <div>
            <a href="<?= BASE_URL ?>stock/purchase" class="btn btn-primary btn-sm">
                <i class="fas fa-cart-plus me-1"></i> Purchase Stock
            </a>
            <a href="<?= BASE_URL ?>stock/report" class="btn btn-info btn-sm">
                <i class="fas fa-chart-bar me-1"></i> View Report
            </a>
        </div>
    </div>
    <div class="card-body">
        <?php if (!empty($lowStockItems)): ?>
        <div class="alert alert-warning mb-4">
            <i class="fas fa-exclamation-triangle me-2"></i> 
            <strong>Warning:</strong> You have <?= count($lowStockItems) ?> item(s) with low stock.
        </div>
        <?php endif; ?>
        
        <div class="table-responsive">
            <table class="table table-bordered" id="stockTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Brand</th>
                        <th>MRP</th>
                        <th>In Stock</th>
                        <th>Last Purchase</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stock as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['item_name']) ?></td>
                        <td><?= htmlspecialchars($item['item_brand']) ?></td>
                        <td>₹<?= number_format($item['mrp'], 2) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td><?= $item['last_purchase_date'] ? date('d M Y', strtotime($item['last_purchase_date'])) : 'N/A' ?></td>
                        <td>
                            <?php if ($item['quantity'] < 5): ?>
                            <span class="badge bg-danger">Low Stock</span>
                            <?php else: ?>
                            <span class="badge bg-success">In Stock</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= BASE_URL ?>stock/purchase?item_id=<?= $item['item_id'] ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus"></i> Add
                            </a>
                            <a href="<?= BASE_URL ?>items/view/<?= $item['item_id'] ?>" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#stockTable').DataTable({
        responsive: true,
        columnDefs: [
            { orderable: false, targets: -1 }
        ]
    });
});
</script>

<?php
$content = ob_get_clean();
include 'layout.php';
?>