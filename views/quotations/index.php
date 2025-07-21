<?php
$title = "Quotations";
ob_start();
?>
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Quotations List</h6>
        <a href="<?= BASE_URL ?>quotations/create" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> New Quotation
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="quotationsTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Quotation #</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Valid Until</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($quotations as $quotation): ?>
                    <tr>
                        <td>#QT-<?= $quotation['quotation_id'] ?></td>
                        <td><?= date('d M Y', strtotime($quotation['quotation_date'])) ?></td>
                        <td><?= htmlspecialchars($quotation['customer_name'] ?? 'N/A') ?></td>
                        <td><?= date('d M Y', strtotime($quotation['valid_until'])) ?></td>
                        <td>₹<?= number_format($quotation['total_amount'], 2) ?></td>
                        <td>
                            <?php if ($quotation['status'] == 'pending'): ?>
                            <span class="badge bg-warning">Pending</span>
                            <?php elseif ($quotation['status'] == 'accepted'): ?>
                            <span class="badge bg-success">Accepted</span>
                            <?php else: ?>
                            <span class="badge bg-danger">Rejected</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= BASE_URL ?>quotations/view/<?= $quotation['quotation_id'] ?>" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i>
                            </a>
                            <?php if ($quotation['status'] == 'pending'): ?>
                            <a href="<?= BASE_URL ?>quotations/convert/<?= $quotation['quotation_id'] ?>" class="btn btn-success btn-sm">
                                <i class="fas fa-check"></i> Accept
                            </a>
                            <?php endif; ?>
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
    $('#quotationsTable').DataTable({
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