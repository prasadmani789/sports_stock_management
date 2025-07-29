<?php
include 'includes/db.php';
include 'includes/navbar.php';

// Handle pagination
$limit = 10;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Search filter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$searchSql = '';
$params = [];

if ($search !== '') {
    $searchSql = "WHERE name LIKE :search OR phone LIKE :search2 OR email LIKE :search3";
    $params[':search'] = "%$search%";
    $params[':search2'] = "%$search%";
    $params[':search3'] = "%$search%";
}

// Count total
$countQuery = $pdo->prepare("SELECT COUNT(*) FROM customers $searchSql");
$countQuery->execute($params);
$total = $countQuery->fetchColumn();
$pages = ceil($total / $limit);

// Fetch customers
$sql = "SELECT * FROM customers $searchSql ORDER BY created_at DESC LIMIT :start, :limit";
$stmt = $pdo->prepare($sql);

// Merge search and pagination parameters
$params[':start'] = $start;
$params[':limit'] = $limit;

$stmt->bindValue(':start', $start, PDO::PARAM_INT);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);

// Manually bind search fields if applicable
if ($search !== '') {
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    $stmt->bindValue(':search2', "%$search%", PDO::PARAM_STR);
    $stmt->bindValue(':search3', "%$search%", PDO::PARAM_STR);
}

$stmt->execute();
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Customers</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Customer List</h2>
    <a href="add_customers.php" class="btn btn-primary">+ Add Customer</a>
  </div>

  <!-- Search -->
  <form method="GET" class="input-group mb-3">
    <input type="text" name="search" class="form-control" placeholder="Search by name or phone" value="<?= htmlspecialchars($search) ?>">
    <button class="btn btn-outline-secondary">Search</button>
  </form>

  <!-- Table -->
  <div class="table-responsive">
    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Address</th>
          <th>Phone</th>
          <th>Email</th>
          <th>Created At</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($customers) > 0): ?>
          <?php foreach ($customers as $i => $row): ?>
            <tr>
              <td><?= $start + $i + 1 ?></td>
              <td><?= htmlspecialchars($row['name']) ?></td>
              <td><?= htmlspecialchars($row['address']) ?></td>
              <td>+91 <?= htmlspecialchars($row['phone']) ?></td>
              <td><?= htmlspecialchars($row['email']) ?></td>
              <td><?= date("d-m-Y", strtotime($row['created_at'])) ?></td>
              <td>
                <a href="edit_customer.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                <a href="delete_customer.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure to delete?');">Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="7" class="text-center">No customers found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <nav>
    <ul class="pagination justify-content-center">
      <?php for ($i = 1; $i <= $pages; $i++): ?>
        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
          <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>
    </ul>
  </nav>
</div>
</body>
</html>
