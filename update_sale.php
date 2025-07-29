<?php
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (
            !isset($_POST['sale_id'], $_POST['customer_id'], $_POST['sale_date'], $_POST['item_id']) ||
            !is_array($_POST['item_id'])
        ) {
            throw new Exception("Invalid form data");
        }

        $sale_id = $_POST['sale_id'];
        $customer_id = $_POST['customer_id'];
        $sale_date = $_POST['sale_date'];
        $item_ids = $_POST['item_id'];
        $quantities = $_POST['quantity'];
        $mrps = $_POST['mrp_price'];
        $discounts = $_POST['discount'];
        $cgsts = $_POST['cgst'];
        $sgsts = $_POST['sgst'];
        $special_discounts = $_POST['special_discount'];
        $item_totals = $_POST['item_total'];

        $pdo->beginTransaction();

        // Step 1: Revert the old stock (add back previous quantities)
        $old_items = $pdo->prepare("SELECT item_id, quantity FROM sale_items WHERE sale_id = ?");
        $old_items->execute([$sale_id]);
        foreach ($old_items as $old) {
            $pdo->prepare("UPDATE items SET stock = stock + ? WHERE id = ?")
                ->execute([$old['quantity'], $old['item_id']]);
        }

        // Step 2: Delete old sale_items
        $pdo->prepare("DELETE FROM sale_items WHERE sale_id = ?")->execute([$sale_id]);

        // Step 3: Re-insert sale_items and update stock
        $stmt_item = $pdo->prepare("
            INSERT INTO sale_items 
            (sale_id, item_id, quantity, mrp, discount, cgst, sgst, special_discount, price)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $update_stock = $pdo->prepare("UPDATE items SET stock = stock - ? WHERE id = ?");

        $grand_total = 0;

        for ($i = 0; $i < count($item_ids); $i++) {
            $item_id = $item_ids[$i];
            $qty = $quantities[$i];
            $mrp_price = $mrps[$i];
            $disc = $discounts[$i];
            $cgst = $cgsts[$i];
            $sgst = $sgsts[$i];
            $special = $special_discounts[$i];
            $total = $item_totals[$i];
            $grand_total += $total;

            $stmt_item->execute([
                $sale_id,
                $item_id,
                $qty,
                $mrp_price,
                $disc,
                $cgst,
                $sgst,
                $special,
                $total
            ]);

            $update_stock->execute([$qty, $item_id]);
        }

        // Step 4: Update sales table
        $pdo->prepare("UPDATE sales SET customer_id = ?, sale_date = ?, total_amount = ? WHERE id = ?")
            ->execute([$customer_id, $sale_date, $grand_total, $sale_id]);

        $pdo->commit();

        header("Location: sales_list.php?msg=Sale+updated+successfully");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Error updating sale: " . $e->getMessage());
    }
} else {
    die("Invalid request method");
}
