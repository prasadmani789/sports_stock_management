<?php
$title = "Purchase Stock";
ob_start();
?>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Purchase Stock</h6>
    </div>
    <div class="card-body">
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="item_id" class="form-label">Item</label>
                    <select class="form-select" id="item_id" name="item_id" required>
                        <option value="">Select Item</option>
                        <?php foreach ($items as $item): ?>
                        <option value="<?= $item['item_id'] ?>" <?= isset($_GET['item_id']) && $_GET['item_id'] == $item['item_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($item['item_name']) ?> (<?= htmlspecialchars($item['item_brand']) ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="quantity" class="form-label">Quantity</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="purchase_price" class="form-label">Purchase Price (per unit)</label>
                    <input type="number" step="0.01" class="form-control" id="purchase_price" name="purchase_price" required>
                </div>
                <div class="col-md-6">
                    <label for="purchase_date" class="form-label">Purchase Date</label>
                    <input type="date" class="form-control" id="purchase_date" name="purchase_date" value="<?= date('Y-m-d') ?>" required>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="supplier" class="form-label">Supplier Details</label>
                <textarea class="form-control" id="supplier" name="supplier_details" rows="3"></textarea>
            </div>
            
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">Record Purchase</button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>