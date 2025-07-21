<?php
$title = "Invoice #{$sale['sale_id']}";
ob_start();
?>
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Invoice #<?= $sale['sale_id'] ?></h6>
        <div>
            <button class="btn btn-primary btn-sm" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Print
            </button>
            <a href="<?= BASE_URL ?>sales" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>
    <div class="card-body">
        <div id="invoice-content">
            <div class="invoice-header mb-4 text-center">
                <h2 class="mb-1">Sports Shop</h2>
                <p class="mb-1">123 Sports Street, Sports City</p>
                <p class="mb-1">Phone: 9876543210 | Email: info@sportsshop.com</p>
                <p class="mb-1">GSTIN: 22AAAAA0000A1Z5</p>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-light py-2">
                            <h6 class="m-0 font-weight-bold">Bill To:</h6>
                        </div>
                        <div class="card-body p-3">
                            <p class="mb-1"><strong><?= htmlspecialchars($customer['customer_name'] ?? 'Walk-in Customer') ?></strong></p>
                            <?php if (!empty($customer['address'])): ?>
                            <p class="mb-1"><?= nl2br(htmlspecialchars($customer['address'])) ?></p>
                            <?php endif; ?>
                            <p class="mb-1">Phone: <?= htmlspecialchars($customer['contact_number'] ?? 'N/A') ?></p>
                            <?php if (!empty($customer['email'])): ?>
                            <p class="mb-1">Email: <?= htmlspecialchars($customer['email']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-light py-2">
                            <h6 class="m-0 font-weight-bold">Invoice Details:</h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between">
                                <p class="mb-1"><strong>Invoice #</strong></p>
                                <p class="mb-1">INV-<?= $sale['sale_id'] ?></p>
                            </div>
                            <div class="d-flex justify-content-between">
                                <p class="mb-1"><strong>Date</strong></p>
                                <p class="mb-1"><?= date('d M Y, h:i A', strtotime($sale['sale_date'])) ?></p>
                            </div>
                            <div class="d-flex justify-content-between">
                                <p class="mb-1"><strong>Payment Status</strong></p>
                                <p class="mb-1"><span class="badge bg-success">Paid</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="table-responsive mb-4">
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th width="5%">#</th>
                            <th width="45%">Item</th>
                            <th width="10%">Qty</th>
                            <th width="15%">Price</th>
                            <th width="10%">Disc.%</th>
                            <th width="15%">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($saleItems as $index => $item): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($item['item_name']) ?> (<?= htmlspecialchars($item['item_brand']) ?>)</td>
                            <td><?= $item['quantity'] ?></td>
                            <td>₹<?= number_format($item['unit_price'], 2) ?></td>
                            <td><?= $item['discount'] ?>%</td>
                            <td>₹<?= number_format($item['unit_price'] * $item['quantity'] * (1 - $item['discount'] / 100), 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="text-end"><strong>Subtotal:</strong></td>
                            <td>₹<?= number_format($sale['total_amount'] - $sale['tax_amount'] + $sale['discount'], 2) ?></td>
                        </tr>
                        <tr>
                            <td colspan="5" class="text-end"><strong>Discount:</strong></td>
                            <td>₹<?= number_format($sale['discount'], 2) ?></td>
                        </tr>
                        <tr>
                            <td colspan="5" class="text-end"><strong>GST:</strong></td>
                            <td>₹<?= number_format($sale['tax_amount'], 2) ?></td>
                        </tr>
                        <tr class="table-active">
                            <td colspan="5" class="text-end"><strong>Total Amount:</strong></td>
                            <td>₹<?= number_format($sale['total_amount'], 2) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-light py-2">
                            <h6 class="m-0 font-weight-bold">Payment Method</h6>
                        </div>
                        <div class="card-body p-3">
                            <p class="mb-0">Cash</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-light py-2">
                            <h6 class="m-0 font-weight-bold">Notes</h6>
                        </div>
                        <div class="card-body p-3">
                            <p class="mb-0"><?= nl2br(htmlspecialchars($sale['notes'] ?? 'Thank you for your business!')) ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-4 pt-4 border-top text-center">
                <p class="mb-1">Thank you for shopping with us!</p>
                <p class="mb-1"><strong>Sports Shop</strong></p>
                <p class="mb-0 text-muted">This is a computer generated invoice. No signature required.</p>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #invoice-content, #invoice-content * {
        visibility: visible;
    }
    #invoice-content {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    .card-header, .table-dark {
        background-color: #f8f9fa !important;
        color: #000 !important;
    }
    .table-active {
        background-color: rgba(0,0,0,.075) !important;
    }
    .badge {
        border: 1px solid #000;
    }
    .no-print {
        display: none !important;
    }
}
</style>

<?php
$content = ob_get_clean();
include 'layout.php';
?>