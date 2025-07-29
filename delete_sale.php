<?php
include 'includes/db.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $sale_id = (int)$_GET['id'];

    try {
        $pdo->beginTransaction();

        // First delete from sale_items table
        $stmt_items = $pdo->prepare("DELETE FROM sale_items WHERE sale_id = ?");
        $stmt_items->execute([$sale_id]);

        // Then delete from sales table
        $stmt_sale = $pdo->prepare("DELETE FROM sales WHERE id = ?");
        $stmt_sale->execute([$sale_id]);

        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        // You may log this error
        die("Error deleting sale: " . $e->getMessage());
    }

    // Redirect after delete
    header("Location: sales_list.php");
    exit;
} else {
    echo "Invalid sale ID.";
}
