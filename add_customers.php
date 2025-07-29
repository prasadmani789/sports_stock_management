<?php
include 'includes/db.php';
include 'includes/navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Customer - Sports Shop</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Add New Customer</h2>
    <a href="customers.php" class="btn btn-secondary">← Back to Customers</a>
  </div>

  <form action="save_customer.php" method="POST" class="card shadow p-4">
    <div class="mb-3">
      <label for="name" class="form-label">Customer Name <span class="text-danger">*</span></label>
      <input type="text" name="name" id="name" class="form-control" required>
    </div>

    <div class="mb-3">
      <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
      <textarea name="address" id="address" class="form-control" rows="3" required></textarea>
    </div>

    <div class="mb-3">
      <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
      <div class="input-group">
        <span class="input-group-text">+91</span>
        <input type="text" name="phone" id="phone" class="form-control" pattern="\d{10}" maxlength="10" required
               title="Enter 10 digit mobile number only (without +91)">
      </div>
    </div>

    <div class="mb-3">
      <label for="email" class="form-label">Email (Optional)</label>
      <input type="email" name="email" id="email" class="form-control" placeholder="example@domain.com">
    </div>

    <button type="submit" class="btn btn-primary">Add Customer</button>
  </form>
</div>

</body>
</html>
