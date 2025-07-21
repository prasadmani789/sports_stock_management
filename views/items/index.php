<?php
$title = "Items Management";
ob_start();
?>
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Sports Items List</h6>
        <a href="<?= BASE_URL ?>items/create" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add Item
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="itemsTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Brand</th>
                        <th>GST</th>
                        <th>MRP</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= $item['item_id'] ?></td>
                        <td><?= htmlspecialchars($item['item_name']) ?></td>
                        <td><?= htmlspecialchars($item['item_type']) ?></td>
                        <td><?= htmlspecialchars($item['item_brand']) ?></td>
                        <td><?= $item['central_gst'] + $item['state_gst'] ?>%</td>
                        <td>₹<?= number_format($item['mrp'], 2) ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>items/edit/<?= $item['item_id'] ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn btn-danger btn-sm delete-item" data-id="<?= $item['item_id'] ?>">
                                <i class="fas fa-trash"></i>
                            </button>
                            <a href="<?= BASE_URL ?>items/view/<?= $item['item_id'] ?>" class="btn btn-info btn-sm">
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
    $('#itemsTable').DataTable({
        responsive: true,
        columnDefs: [
            { orderable: false, targets: -1 }
        ]
    });

    $('.delete-item').click(function() {
        if (confirm('Are you sure you want to delete this item?')) {
            window.location.href = '<?= BASE_URL ?>items/delete/' + $(this).data('id');
        }
    });
});
</script>

<?php
$content = ob_get_clean();
include 'layout.php';
?>