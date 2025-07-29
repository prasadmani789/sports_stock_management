<?php
require_once 'includes/db.php';


if (isset($_POST['type_name']) && !empty($_POST['type_name'])) {
    $name = trim($_POST['type_name']);
    $stmt = $pdo->prepare("INSERT INTO types (name) VALUES (?)");
    if ($stmt->execute([$name])) {
        echo "success";
    } else {
        echo "error";
    }
} else {
    echo "invalid";
}
?>
