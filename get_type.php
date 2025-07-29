<?php
require_once 'includes/db.php';

try {
    $stmt = $pdo->query("SELECT id, name FROM types ORDER BY name ASC");
    $types = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($types);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
