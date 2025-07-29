<?php
include 'includes/db.php';
include 'includes/navbar.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid sale ID");
}
$sale_id = $_GET['id'];

// Fetch customers
$customers = $pdo->query("SELECT id, name FROM customers ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

// Fetch sale data
$sale = $pdo->prepare("SELECT * FROM sales WHERE id = ?");
$sale->execute([$sale_id]);
$saleData = $sale->fetch(PDO::FETCH_ASSOC);
if (!$saleData) die("Sale not found");

// Fetch sale items
$sale_items = $pdo->prepare("SELECT * FROM sale_items WHERE sale_id = ?");
$sale_items->execute([$sale_id]);
$items_in_sale = $sale_items->fetchAll(PDO::FETCH_ASSOC);

// Fetch all items
$all_items = $pdo->query("SELECT * FROM items ORDER BY item_name")->fetchAll(PDO::FETCH_ASSOC);
$item_data_json = json_encode($all_items);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Sale</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
  <h2>Edit Sale #<?= $sale_id ?></h2>

  <form action="update_sale.php" method="POST">
    <input type="hidden" name="sale_id" value="<?= $sale_id ?>">

    <div class="row mb-3">
      <div class="col-md-6">
        <label class="form-label">Customer</label>
        <select name="customer_id" class="form-select" required>
          <?php foreach ($customers as $cust): ?>
            <option value="<?= $cust['id'] ?>" <?= $cust['id'] == $saleData['customer_id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($cust['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Sale Date</label>
        <input type="date" name="sale_date" class="form-control" required value="<?= $saleData['sale_date'] ?>">
      </div>
    </div>

    <table class="table table-bordered">
      <thead class="table-secondary">
        <tr>
          <th>Item</th>
          <th>MRP</th>
          <th>Discount</th>
          <th>CGST</th>
          <th>SGST</th>
          <th>Special</th>
          <th>Qty</th>
          <th>Total</th>
          <th></th>
        </tr>
      </thead>
      <tbody id="itemRows">
        <?php foreach ($items_in_sale as $index => $row): ?>
          <tr>
            <td>
              <select name="item_id[]" class="form-select item-select">
                <?php foreach ($all_items as $item): ?>
                  <option value="<?= $item['id'] ?>" <?= $item['id'] == $row['item_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($item['item_name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </td>
            <td><input type="number" name="mrp_price[]" class="form-control mrp" value="<?= $row['mrp'] ?>" readonly></td>
            <td><input type="number" name="discount[]" class="form-control discount" value="<?= $row['discount'] ?>" readonly></td>
            <td><input type="number" name="cgst[]" class="form-control cgst" value="<?= $row['cgst'] ?>" readonly></td>
            <td><input type="number" name="sgst[]" class="form-control sgst" value="<?= $row['sgst'] ?>" readonly></td>
            <td><input type="number" name="special_discount[]" class="form-control special" value="<?= $row['special_discount'] ?>"></td>
            <td><input type="number" name="quantity[]" class="form-control qty" value="<?= $row['quantity'] ?>"></td>
            <td><input type="text" name="item_total[]" class="form-control total" value="<?= $row['price'] ?>" readonly></td>
            <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">×</button></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="text-end mb-3">
      <strong>Grand Total: ₹<span id="grandTotal"><?= $saleData['total_amount'] ?></span></strong>
    </div>

    <button type="submit" class="btn btn-primary">Update Sale</button>
    <a href="sales_list.php" class="btn btn-secondary">Back</a>
  </form>
</div>

<script>
const itemData = <?= $item_data_json ?>;

function removeRow(btn) {
  btn.closest('tr').remove();
  calculateGrandTotal();
}

document.addEventListener('change', function (e) {
  const row = e.target.closest('tr');
  if (e.target.classList.contains('item-select')) {
    const itemId = e.target.value;
    const item = itemData.find(i => i.id == itemId);
    if (item) {
      row.querySelector('.mrp').value = item.mrp_price;
      row.querySelector('.discount').value = item.discount;
      row.querySelector('.cgst').value = item.cgst;
      row.querySelector('.sgst').value = item.sgst;
      row.querySelector('.special').value = 0;
    }
    calculateRowTotal(row);
  }

  if (['qty', 'special'].some(cls => e.target.classList.contains(cls))) {
    calculateRowTotal(row);
  }
});

function calculateRowTotal(row) {
  const mrp = parseFloat(row.querySelector('.mrp').value) || 0;
  const discount = parseFloat(row.querySelector('.discount').value) || 0;
  const cgst = parseFloat(row.querySelector('.cgst').value) || 0;
  const sgst = parseFloat(row.querySelector('.sgst').value) || 0;
  const special = parseFloat(row.querySelector('.special').value) || 0;
  const qty = parseInt(row.querySelector('.qty').value) || 0;

  const discountAmt = mrp * (discount + special) / 100;
  const priceAfterDisc = mrp - discountAmt;
  const tax = priceAfterDisc * (cgst + sgst) / 100;
  const total = (priceAfterDisc + tax) * qty;

  row.querySelector('.total').value = total.toFixed(2);
  calculateGrandTotal();
}

function calculateGrandTotal() {
  let total = 0;
  document.querySelectorAll('.total').forEach(t => {
    total += parseFloat(t.value) || 0;
  });
  document.getElementById('grandTotal').textContent = total.toFixed(2);
}
</script>
</body>
</html>
