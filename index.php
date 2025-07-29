<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
checkLogin();

$page = 'dashboard';

// Low stock threshold
$low_stock_threshold = 5;

// Get summary data
$total_products = $pdo->query("SELECT COUNT(*) FROM products WHERE is_deleted = 0")->fetchColumn();
$total_sales = $pdo->query("SELECT COUNT(*) FROM sales")->fetchColumn();
$total_customers = $pdo->query("SELECT COUNT(*) FROM customers")->fetchColumn();
$low_stock_count = $pdo->query("SELECT COUNT(*) FROM products WHERE quantity <= $low_stock_threshold AND is_deleted = 0")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - Sports Shop</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="container mt-4">
  <h3 class="mb-4">🏪 Sports Shop Dashboard</h3>

  <div class="row g-4">
    <div class="col-md-3">
      <div class="card border-success shadow-sm">
        <div class="card-body text-success">
          <h5 class="card-title">Total Products</h5>
          <p class="card-text fs-4"><?= htmlspecialchars($total_products) ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card border-primary shadow-sm">
        <div class="card-body text-primary">
          <h5 class="card-title">Total Sales</h5>
          <p class="card-text fs-4"><?= htmlspecialchars($total_sales) ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card border-danger shadow-sm">
        <div class="card-body text-danger">
          <h5 class="card-title">Low Stock Items</h5>
          <p class="card-text fs-4"><?= htmlspecialchars($low_stock_count) ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card border-info shadow-sm">
        <div class="card-body text-info">
          <h5 class="card-title">Customers</h5>
          <p class="card-text fs-4"><?= htmlspecialchars($total_customers) ?></p>
        </div>
      </div>
    </div>
  </div>

  <!-- Low Stock Alert Table -->
  <?php if ($low_stock_count > 0): ?>
    <div class="alert alert-warning mt-4">
      <h5><i class="bi bi-exclamation-triangle-fill"></i> Low Stock Alert</h5>
      <div class="table-responsive">
        <table class="table table-bordered table-sm">
          <thead>
            <tr>
              <th>Product</th>
              <th>Quantity</th>
              <th>Category</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $stmt = $pdo->query("SELECT name, quantity, category FROM products WHERE quantity <= $low_stock_threshold AND is_deleted = 0");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
            ?>
              <tr>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td class="text-danger fw-bold"><?= htmlspecialchars($row['quantity']) ?></td>
                <td><?= htmlspecialchars($row['category']) ?></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php endif; ?>

  <!-- Quick Links -->
  <div class="mt-5">
    <h5>Quick Actions</h5>
    <div class="d-flex flex-wrap gap-3">
      <a href="add_item.php" class="btn btn-success"><i class="bi bi-plus-circle"></i> Add Product</a>
      <a href="add_stock.php" class="btn btn-warning"><i class="bi bi-box"></i> Add Stock</a>
      <a href="add_sale.php" class="btn btn-primary"><i class="bi bi-cart"></i> New Sale</a>
      <a href="view_sales.php" class="btn btn-info"><i class="bi bi-receipt"></i> View Sales</a>
      <a href="add_customers.php" class="btn btn-secondary"><i class="bi bi-people"></i> Add Customers</a>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
