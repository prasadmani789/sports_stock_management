<?php
include 'includes/db.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("DELETE FROM customers WHERE id = ?");
$stmt->execute([$id]);

header("Location: customers.php?deleted=1");
exit;
?>
