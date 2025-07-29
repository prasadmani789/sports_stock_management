<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
checkLogin();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2>Dashboard</h2>
    <p>This is the dashboard page.</p>
    <p><a href="items.php" class="btn btn-success">Manage Items</a></p>
    <p><a href="logout.php" class="btn btn-danger">Logout</a></p>
</div>
</body>
</html>
