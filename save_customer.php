<?php
include 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $email = !empty($_POST['email']) ? trim($_POST['email']) : null;

    if (!preg_match('/^\d{10}$/', $phone)) {
        die("Invalid phone number. It must be 10 digits.");
    }

    // Check for duplicate phone
    $check = $pdo->prepare("SELECT COUNT(*) FROM customers WHERE phone = ?");
    $check->execute([$phone]);
    if ($check->fetchColumn() > 0) {
        die("<script>alert('Phone number already exists!'); window.location='add_customer.php';</script>");
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO customers (name, address, phone, email, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$name, $address, $phone, $email]);

        header("Location: customers.php?success=1");
        exit();
    } catch (PDOException $e) {
        die("Error saving customer: " . $e->getMessage());
    }
}
