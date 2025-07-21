<?php
$title = "Edit Item";
ob_start();
?>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Edit Sports Item</h6>
    </div>
    <div class="card-body">
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            
            <div class="mb-3">
                <label for="item_name" class="form-label">Item Name</label>
                <input type="text" class="form-control" id="item_name" name="item_name" 
                       value="<?= htmlspecialchars($item['item_name']) ?>" required>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="item_type" class="form-label">Item Type</label>
                    <select class="form-select" id="item_type" name="item_type" required>
                        <option value="">Select Type</option>
                        <option value="Cricket" <?= $item['item_type'] == 'Cricket' ? 'selected' : '' ?>>Cricket</option>
                        <option value="Football" <?= $item['item_type'] == 'Football' ? 'selected' : '' ?>>Football</option>
                        <option value="Tennis" <?= $item['item_type'] == 'Tennis' ? 'selected' : '' ?>>Tennis</option>
                        <option value="Badminton" <?= $item['item_type'] == 'Badminton' ? 'selected' : '' ?>>Badminton</option>
                        <option value="Basketball" <?= $item['item_type'] == 'Basketball' ? 'selected' : '' ?>>Basketball</option>
                        <option value="Fitness" <?= $item['item_type'] == 'Fitness' ? 'selected' : '' ?>>Fitness</option>
                        <option value="Other" <?= $item['item_type'] == 'Other' ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="item_brand" class="form-label">Brand</label>
                    <input type="text" class="form-control" id="item_brand" name="item_brand" 
                           value="<?= htmlspecialchars($item['item_brand']) ?>" required>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="central_gst" class="form-label">Central GST (%)</label>
                    <input type="number" step="0.01" class="form-control" id="central_gst" name="central_gst" 
                           value="<?= $item['central_gst'] ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="state_gst" class="form-label">State GST (%)</label>
                    <input type="number" step="0.01" class="form-control" id="state_gst" name="state_gst" 
                           value="<?= $item['state_gst'] ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="mrp" class="form-label">MRP (₹)</label>
                    <input type="number" step="0.01" class="form-control" id="mrp" name="mrp" 
                           value="<?= $item['mrp'] ?>" required>
                </div>
            </div>
            
            <div class="d-flex justify-content-end gap-2">
                <a href="<?= BASE_URL ?>items" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Item</button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>