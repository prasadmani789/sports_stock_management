<?php
$title = "Stock Report";
ob_start();
?>
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Stock Movement Report</h6>
        <div>
            <a href="<?= BASE_URL ?>stock" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back to Stock
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-light py-2">
                        <h6 class="m-0 font-weight-bold">Recent Purchases (Last 30 Days)</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Item</th>
                                        <th>Qty</th>
                                        <th>Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($purchases as $purchase): ?>
                                    <tr>
                                        <td><?= date('d M', strtotime($purchase['purchase_date'])) ?></td>
                                        <td><?= htmlspecialchars($purchase['item_name']) ?></td>
                                        <td><?= $purchase['quantity'] ?></td>
                                        <td>₹<?= number_format($purchase['purchase_price'], 2) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-light py-2">
                        <h6 class="m-0 font-weight-bold">Top Selling Items (Last 30 Days)</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Qty Sold</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($sales as $sale): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($sale['item_name']) ?></td>
                                        <td><?= $sale['total_sold'] ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header bg-light py-2">
                <h6 class="m-0 font-weight-bold">Stock Movement History</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="stockHistoryTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stockHistory as $history): ?>
                            <tr>
                                <td><?= date('d M Y', strtotime($history['date'])) ?></td>
                                <td>
                                    <?php if ($history['type'] == 'purchase'): ?>
                                    <span class="badge bg-primary">Purchase</span>
                                    <?php else: ?>
                                    <span class="badge bg-success">Sale</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($history['item_name']) ?></td>
                                <td><?= $history['quantity'] ?></td>
                                <td>₹<?= number_format($history['unit_price'], 2) ?></td>
                                <td><?= htmlspecialchars($history['details']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#stockHistoryTable').DataTable({
        responsive: true,
        order: [[0, 'desc']]
    });
});
</script>

<?php
$content = ob_get_clean();
include 'layout.php';
?>