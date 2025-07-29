<?php
require_once 'includes/db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT name FROM items WHERE id = ?");
    $stmt->execute([$id]);
    $item = $stmt->fetch();

    if ($item) {
        $delete = $pdo->prepare("DELETE FROM items WHERE id = ?");
        $delete->execute([$id]);

        header("Location: items.php?success=Item '{$item['name']}' deleted successfully.");
        exit;
    }
}

header("Location: items.php");
exit;
