<?php
require_once 'includes/db.php';

try {
    $stmt = $pdo->query("SELECT id, name FROM brands ORDER BY name ASC");
    $brands = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($brands);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
