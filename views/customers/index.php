<?php
$title = "Customers Management";
ob_start();
?>
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Customers List</h6>
        <a href="<?= BASE_URL ?>customers/create" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add Customer
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="customersTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customers as $customer): ?>
                    <tr>
                        <td><?= htmlspecialchars($customer['customer_id']) ?></td>
                        <td><?= htmlspecialchars($customer['customer_name']) ?></td>
                        <td><?= htmlspecialchars($customer['contact_number']) ?></td>
                        <td><?= htmlspecialchars($customer['email']) ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>customers/view/<?= $customer['customer_id'] ?>" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?= BASE_URL ?>customers/edit/<?= $customer['customer_id'] ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn btn-danger btn-sm delete-customer" data-id="<?= $customer['customer_id'] ?>">
                                <i class="fas fa-trash"></i>
                            </button>
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
    $('#customersTable').DataTable({
        responsive: true,
        columnDefs: [
            { orderable: false, targets: -1 }
        ]
    });

    $('.delete-customer').click(function() {
        if (confirm('Are you sure you want to delete this customer?')) {
            window.location.href = '<?= BASE_URL ?>customers/delete/' + $(this).data('id');
        }
    });
});
</script>

<?php
$content = ob_get_clean();
include 'layout.php';
?>