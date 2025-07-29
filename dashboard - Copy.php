<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
$admin = $_SESSION['admin'];
?>

<?php include('includes/header.php'); ?>

Welcome, <?= htmlspecialchars($admin) ?>!  
This is your shop dashboard.

<?php include('includes/footer.php'); ?>
