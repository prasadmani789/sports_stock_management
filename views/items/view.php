<?php
$title = "Item Details";
ob_start();
?>
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Item Details</h6>
        <div>
            <a href="<?= BASE_URL ?>items/edit/<?= $item['item_id'] ?>" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="<?= BASE_URL ?>items" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-4">
                    <h4 class="font-weight-bold"><?= htmlspecialchars($item['item_name']) ?></h4>
                    <p class="text-muted mb-1">Item ID: <?= $item['item_id'] ?></p>
                </div>
                
                <div class="mb-3">
                    <h6 class="font-weight-bold">Details</h6>
                    <p>
                        <strong>Type:</strong> <?= htmlspecialchars($item['item_type']) ?><br>
                        <strong>Brand:</strong> <?= htmlspecialchars($item['item_brand']) ?><br>
                        <strong>MRP:</strong> ₹<?= number_format($item['mrp'], 2) ?><br>
                        <strong>GST:</strong> <?= $item['central_gst'] + $item['state_gst'] ?>%
                        (CGST: <?= $item['central_gst'] ?>%, SGST: <?= $item['state_gst'] ?>%)
                    </p>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="m-0 font-weight-bold">Stock Information</h6>
                    </div>
                    <div class="card-body">
                        <?php if ($stock): ?>
                            <div class="text-center mb-3">
                                <h2 class="font-weight-bold <?= $stock['quantity'] < 5 ? 'text-danger' : 'text-success' ?>">
                                    <?= $stock['quantity'] ?>
                                </h2>
                                <p class="mb-0">in stock</p>
                            </div>
                            
                            <div class="mb-3">
                                <h6 class="font-weight-bold">Last Purchase</h6>
                                <p>
                                    <?= $stock['last_purchase_date'] ? date('d M Y', strtotime($stock['last_purchase_date'])) : 'Not available' ?>
                                </p>
                            </div>
                            
                            <a href="<?= BASE_URL ?>stock/purchase" class="btn btn-primary btn-block">
                                <i class="fas fa-cart-plus me-1"></i> Purchase More
                            </a>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i> No stock record found for this item.
                            </div>
                            <a href="<?= BASE_URL ?>stock/purchase" class="btn btn-primary btn-block">
                                <i class="fas fa-cart-plus me-1"></i> Add Stock
                            </a>
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