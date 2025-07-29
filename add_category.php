<?php
require_once 'includes/db.php';


if (isset($_POST['category_name']) && !empty($_POST['category_name'])) {
    $name = trim($_POST['category_name']);
    $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
    if ($stmt->execute([$name])) {
        echo "success";
    } else {
        echo "error";
    }
} else {
    echo "invalid";
}
?>
