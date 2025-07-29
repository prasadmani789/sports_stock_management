<?php
include 'includes/db.php';
include 'includes/navbar.php';

$sales = $pdo->query("
  SELECT s.id, s.sale_date, c.name AS customer_name, s.total_amount 
  FROM sales s 
  JOIN customers c ON s.customer_id = c.id 
  ORDER BY s.sale_date DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sales List</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .dropdown-toggle::after {
      margin-left: 0.5rem;
    }
  </style>
</head>
<body>
<div class="container mt-4">
  <h2 class="mb-4">Sales Report</h2>
  <div class="d-flex justify-content-between mb-3">
    <a href="sales.php" class="btn btn-primary">Add New Sale</a>
    <a href="index.php" class="btn btn-secondary">Back</a>
    <input type="text" id="searchInput" class="form-control w-25" placeholder="Search by customer or date">
  </div>

  <table class="table table-bordered table-hover" id="salesTable">
    <thead class="table-secondary">
      <tr>
        <th>#</th>
        <th>Customer</th>
        <th>Date</th>
        <th>Total Amount (₹)</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (count($sales) > 0): ?>
        <?php foreach ($sales as $i => $sale): ?>
          <tr>
            <td><?= $i + 1 ?></td>
            <td><?= htmlspecialchars($sale['customer_name']) ?></td>
            <td><?= date('d-m-Y', strtotime($sale['sale_date'])) ?></td>
            <td><?= number_format($sale['total_amount'], 2) ?></td>
            <td>
              <div class="dropdown">
                <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                  Actions
                </button>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="view_sale.php?id=<?= $sale['id'] ?>">View</a></li>
                  <li><a class="dropdown-item" href="edit_sale.php?id=<?= $sale['id'] ?>">Edit</a></li>
                  <li><a class="dropdown-item text-danger" href="#" onclick="confirmDelete(<?= $sale['id'] ?>)">Delete</a></li>
                </ul>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="5" class="text-center">No sales found.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Search filter
document.getElementById('searchInput').addEventListener('keyup', function () {
  const filter = this.value.toLowerCase();
  const rows = document.querySelectorAll('#salesTable tbody tr');

  rows.forEach(row => {
    const customer = row.cells[1].textContent.toLowerCase();
    const date = row.cells[2].textContent.toLowerCase();
    row.style.display = (customer.includes(filter) || date.includes(filter)) ? '' : 'none';
  });
});

// Delete confirmation
function confirmDelete(saleId) {
  if (confirm('Are you sure you want to delete this sale?')) {
    window.location.href = 'delete_sale.php?id=' + saleId;
  }
}
</script>
</body>
</html>
