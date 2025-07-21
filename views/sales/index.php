<?php
$title = "Sales History";
ob_start();
?>
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Sales History</h6>
        <a href="<?= BASE_URL ?>sales/create" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> New Sale
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="salesTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sales as $sale): ?>
                    <tr>
                        <td>#INV-<?= $sale['sale_id'] ?></td>
                        <td><?= date('d M Y', strtotime($sale['sale_date'])) ?></td>
                        <td><?= htmlspecialchars($sale['customer_name'] ?? 'Walk-in Customer') ?></td>
                        <td><?= count($this->saleModel->getSaleItems($sale['sale_id'])) ?> items</td>
                        <td>₹<?= number_format($sale['total_amount'], 2) ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>sales/invoice/<?= $sale['sale_id'] ?>" class="btn btn-info btn-sm">
                                <i class="fas fa-file-invoice"></i>
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
    $('#salesTable').DataTable({
        responsive: true,
        order: [[1, 'desc']],
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