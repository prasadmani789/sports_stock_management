<?php
include 'includes/db.php';
include 'includes/navbar.php';

// Fetch customers
$customers = $pdo->query("SELECT id, name FROM customers ORDER BY name")->fetchAll();

// Fetch items
$items = $pdo->query("SELECT id, item_name, mrp_price, discount, cgst, sgst FROM items ORDER BY item_name")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales - Sports Items Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table th, .table td { vertical-align: middle; }
    </style>
</head>
<body>

<div class="container mt-4">
    <h2 class="mb-4">New Sale</h2>
    <form action="save_sale.php" method="POST" id="salesForm">
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="customer_id" class="form-label">Select Customer</label>
                <select name="customer_id" id="customer_id" class="form-select" required>
                    <option value="">-- Select --</option>
                    <?php foreach ($customers as $cust): ?>
                        <option value="<?= $cust['id'] ?>"><?= htmlspecialchars($cust['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label for="sale_date" class="form-label">Sale Date</label>
                <input type="date" class="form-control" name="sale_date" value="<?= date('Y-m-d') ?>" required>
            </div>
        </div>

        <hr>

        <h5>Items</h5>
        <table class="table table-bordered" id="itemsTable">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>MRP</th>
                    <th>Discount %</th>
                    <th>CGST %</th>
                    <th>SGST %</th>
                    <th>Special Discount %</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th><button type="button" class="btn btn-sm btn-success" onclick="addRow()">+</button></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

        <div class="text-end">
            <strong>Grand Total: ₹<span id="grandTotal">0.00</span></strong>
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Save Sale</button>
        </div>
    </form>
</div>

<script>
    const itemsData = <?= json_encode($items) ?>;

    function addRow() {
        const tableBody = document.querySelector("#itemsTable tbody");

        const row = document.createElement("tr");

        const itemSelect = document.createElement("select");
        itemSelect.name = "item_id[]";
        itemSelect.classList.add("form-select", "item-select");
        itemSelect.innerHTML = `<option value="">-- Select --</option>`;
        itemsData.forEach(item => {
            itemSelect.innerHTML += `<option value="${item.id}">${item.item_name}</option>`;
        });

        row.innerHTML = `
            <td></td>
            <td><input type="number" name="mrp_price[]" class="form-control" readonly></td>
            <td><input type="number" name="discount[]" class="form-control" readonly></td>
            <td><input type="number" name="cgst[]" class="form-control" readonly></td>
            <td><input type="number" name="sgst[]" class="form-control" readonly></td>
            <td><input type="number" name="special_discount[]" class="form-control" value="0" min="0" max="100"></td>
            <td><input type="number" name="quantity[]" class="form-control" value="1" min="1"></td>
            <td><input type="number" name="item_total[]" class="form-control item-total" readonly></td>
            <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">X</button></td>
        `;
        row.children[0].appendChild(itemSelect);
        tableBody.appendChild(row);

        attachListeners(row);
    }

    function removeRow(button) {
        button.closest('tr').remove();
        updateGrandTotal();
    }

    function attachListeners(row) {
        const itemSelect = row.querySelector(".item-select");
        const mrpInput = row.querySelector("[name='mrp_price[]']");
        const discountInput = row.querySelector("[name='discount[]']");
        const cgstInput = row.querySelector("[name='cgst[]']");
        const sgstInput = row.querySelector("[name='sgst[]']");
        const specialInput = row.querySelector("[name='special_discount[]']");
        const qtyInput = row.querySelector("[name='quantity[]']");
        const totalInput = row.querySelector("[name='item_total[]']");

        itemSelect.addEventListener("change", () => {
            const selected = itemsData.find(i => i.id == itemSelect.value);
            if (selected) {
                mrpInput.value = selected.mrp_price;
                discountInput.value = selected.discount;
                cgstInput.value = selected.cgst;
                sgstInput.value = selected.sgst;
            } else {
                mrpInput.value = discountInput.value = cgstInput.value = sgstInput.value = "";
            }
            calculateItemTotal();
        });

        [specialInput, qtyInput].forEach(el => {
            el.addEventListener("input", calculateItemTotal);
        });

        function calculateItemTotal() {
            const mrp = parseFloat(mrpInput.value) || 0;
            const discount = parseFloat(discountInput.value) || 0;
            const special = parseFloat(specialInput.value) || 0;
            const qty = parseFloat(qtyInput.value) || 0;
            const cgst = parseFloat(cgstInput.value) || 0;
            const sgst = parseFloat(sgstInput.value) || 0;

            let priceAfterDiscount = mrp - (mrp * discount / 100);
            priceAfterDiscount -= (priceAfterDiscount * special / 100); // Special discount in %

            let taxAmount = priceAfterDiscount * (cgst + sgst) / 100;
            let finalPrice = (priceAfterDiscount + taxAmount) * qty;

            totalInput.value = finalPrice.toFixed(2);
            updateGrandTotal();
        }
    }

    function updateGrandTotal() {
        let grandTotal = 0;
        document.querySelectorAll(".item-total").forEach(input => {
            grandTotal += parseFloat(input.value) || 0;
        });
        document.getElementById("grandTotal").innerText = grandTotal.toFixed(2);
    }

    window.onload = addRow;
</script>
</body>
</html>
