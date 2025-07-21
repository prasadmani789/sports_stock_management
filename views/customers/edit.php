<?php
$title = "Edit Customer";
ob_start();
?>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Edit Customer</h6>
    </div>
    <div class="card-body">
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="customer_name" class="form-label">Customer Name</label>
                    <input type="text" class="form-control" id="customer_name" name="customer_name" 
                           value="<?= htmlspecialchars($customer['customer_name']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="contact_number" class="form-label">Contact Number</label>
                    <input type="text" class="form-control" id="contact_number" name="contact_number" 
                           value="<?= htmlspecialchars($customer['contact_number']) ?>" required>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" 
                       value="<?= htmlspecialchars($customer['email']) ?>">
            </div>
            
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea class="form-control" id="address" name="address" rows="3"><?= htmlspecialchars($customer['address']) ?></textarea>
            </div>
            
            <div class="d-flex justify-content-end gap-2">
                <a href="<?= BASE_URL ?>customers" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Customer</button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>