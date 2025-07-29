<?php
require_once 'includes/db.php';

if (isset($_POST['brand_name']) && !empty($_POST['brand_name'])) {
    $name = trim($_POST['brand_name']);
    $stmt = $pdo->prepare("INSERT INTO brands (name) VALUES (?)");
    if ($stmt->execute([$name])) {
        echo "success";
    } else {
        echo "error";
    }
} else {
    echo "invalid";
}
?>
