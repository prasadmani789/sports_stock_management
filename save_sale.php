<?php
include 'includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (
            !isset($_POST['customer_id'], $_POST['sale_date'], $_POST['item_id']) ||
            !is_array($_POST['item_id'])
        ) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request: missing items']);
            exit;
        }

        $customer_id = $_POST['customer_id'];
        $sale_date = $_POST['sale_date'];
        $item_ids = $_POST['item_id'];
        $quantities = $_POST['quantity'];
        $mrps = $_POST['mrp_price']; // Will insert into sale_items.mrp
        $discounts = $_POST['discount'];
        $cgsts = $_POST['cgst'];
        $sgsts = $_POST['sgst'];
        $special_discounts = $_POST['special_discount'];
        $item_totals = $_POST['item_total']; // this goes into `price`

        $pdo->beginTransaction();

        $grand_total = array_sum(array_map('floatval', $item_totals));

        // Insert into sales table
        $stmt = $pdo->prepare("INSERT INTO sales (customer_id, sale_date, total_amount) VALUES (?, ?, ?)");
        $stmt->execute([$customer_id, $sale_date, $grand_total]);
        $sale_id = $pdo->lastInsertId();

        // Insert into sale_items
        $stmt_item = $pdo->prepare("
            INSERT INTO sale_items 
                (sale_id, item_id, quantity, mrp, discount, cgst, sgst, special_discount, price)
            VALUES 
                (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        // Reduce stock
        $update_stock = $pdo->prepare("UPDATE items SET stock = stock - ? WHERE id = ?");

        for ($i = 0; $i < count($item_ids); $i++) {
            $item_id = $item_ids[$i];
            $qty = $quantities[$i];
            $mrp_price = $mrps[$i];
            $disc = $discounts[$i];
            $cgst = $cgsts[$i];
            $sgst = $sgsts[$i];
            $special = $special_discounts[$i];
            $total = $item_totals[$i];

            $stmt_item->execute([
                $sale_id,
                $item_id,
                $qty,
                $mrp_price, // stored in `mrp` column
                $disc,
                $cgst,
                $sgst,
                $special,
                $total
            ]);

            $update_stock->execute([$qty, $item_id]);
        }

        $pdo->commit();

        echo json_encode(['status' => 'success', 'message' => 'Sale saved successfully']);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Error saving sale: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
