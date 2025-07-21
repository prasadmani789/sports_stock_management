<?php
$title = "Customer Details";
ob_start();
?>
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Customer Details</h6>
        <div>
            <a href="<?= BASE_URL ?>customers/edit/<?= $customer['customer_id'] ?>" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="<?= BASE_URL ?>customers" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-4">
                    <h5 class="font-weight-bold"><?= htmlspecialchars($customer['customer_name']) ?></h5>
                    <p class="text-muted mb-1">Customer ID: <?= $customer['customer_id'] ?></p>
                </div>
                
                <div class="mb-3">
                    <h6 class="font-weight-bold">Contact Information</h6>
                    <p>
                        <i class="fas fa-phone me-2"></i> <?= htmlspecialchars($customer['contact_number']) ?><br>
                        <i class="fas fa-envelope me-2"></i> <?= htmlspecialchars($customer['email']) ?>
                    </p>
                </div>
                
                <div class="mb-3">
                    <h6 class="font-weight-bold">Address</h6>
                    <p><?= nl2br(htmlspecialchars($customer['address'])) ?></p>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="m-0 font-weight-bold">Purchase History</h6>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($sales)): ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Invoice</th>
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($sales as $sale): ?>
                                        <tr>
                                            <td>#INV-<?= $sale['sale_id'] ?></td>
                                            <td><?= date('d M Y', strtotime($sale['sale_date'])) ?></td>
                                            <td>₹<?= number_format($sale['total_amount'], 2) ?></td>
                                            <td>
                                                <a href="<?= BASE_URL ?>sales/invoice/<?= $sale['sale_id'] ?>" class="btn btn-sm btn-info">
                                                    <i class="fas fa-file-invoice"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No purchase history found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>