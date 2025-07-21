<?php
$title = "Add New Item";
ob_start();
?>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Add New Sports Item</h6>
    </div>
    <div class="card-body">
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            
            <div class="mb-3">
                <label for="item_name" class="form-label">Item Name</label>
                <input type="text" class="form-control" id="item_name" name="item_name" required>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="item_type" class="form-label">Item Type</label>
                    <select class="form-select" id="item_type" name="item_type" required>
                        <option value="">Select Type</option>
                        <option value="Cricket">Cricket</option>
                        <option value="Football">Football</option>
                        <option value="Tennis">Tennis</option>
                        <option value="Badminton">Badminton</option>
                        <option value="Basketball">Basketball</option>
                        <option value="Fitness">Fitness</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="item_brand" class="form-label">Brand</label>
                    <input type="text" class="form-control" id="item_brand" name="item_brand" required>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="central_gst" class="form-label">Central GST (%)</label>
                    <input type="number" step="0.01" class="form-control" id="central_gst" name="central_gst" required>
                </div>
                <div class="col-md-4">
                    <label for="state_gst" class="form-label">State GST (%)</label>
                    <input type="number" step="0.01" class="form-control" id="state_gst" name="state_gst" required>
                </div>
                <div class="col-md-4">
                    <label for="mrp" class="form-label">MRP (₹)</label>
                    <input type="number" step="0.01" class="form-control" id="mrp" name="mrp" required>
                </div>
            </div>
            
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">Add Item</button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>