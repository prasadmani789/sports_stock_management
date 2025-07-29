<?php
include 'config/db.php';

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="products.csv"');

$output = fopen("php://output", "w");
fputcsv($output, ['ID', 'Product Name', 'Type', 'Price', 'Quantity']);

$result = mysqli_query($conn, "SELECT * FROM products");
while ($row = mysqli_fetch_assoc($result)) {
    fputcsv($output, [$row['id'], $row['name'], $row['type'], $row['price'], $row['quantity']]);
}

fclose($output);
exit;
