<?php
include 'includes/db.php';
include 'includes/navbar.php';

// Fetch customers and items
$customers = $pdo->query("SELECT id, name FROM customers ORDER BY name ASC")->fetchAll();
$items = $pdo->query("SELECT id, name, mrp, discount, cgst, sgst FROM items ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">
  <h2>Add Sale</h2>
  <form action="save_sale.php" method="POST">
    <div class="row mb-3">
      <div class="col-md-6">
        <label for="customer" class="form-label">Customer</label>
        <select name="customer_id" id="customer" class="form-select" required>
          <option value="">-- Select Customer --</option>
          <?php foreach ($customers as $cust): ?>
            <option value="<?= $cust['id'] ?>"><?= htmlspecialchars($cust['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-6">
        <label for="sale_date" class="form-label">Sale Date</label>
        <input type="date" name="sale_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
      </div>
    </div>

    <h5>Sale Items</h5>
    <table class="table table-bordered" id="itemsTable">
      <thead class="table-dark">
        <tr>
          <th>Item</th>
          <th>MRP</th>
          <th>Discount</th>
          <th>CGST%</th>
          <th>SGST%</th>
          <th>Special Discount</th>
          <th>Qty</th>
          <th>Total</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <!-- Rows will be added dynamically -->
      </tbody>
    </table>

    <div class="d-flex justify-content-between">
      <button type="button" class="btn btn-secondary" id="addRow">Add Item</button>
      <h5>Total Amount: <span id="grandTotal">0.00</span></h5>
    </div>

    <div class="mt-4">
      <button type="submit" class="btn btn-success">Save Sale</button>
      <a href="sales.php" class="btn btn-outline-secondary">Back</a>
    </div>
  </form>
</div>

<script>
const itemData = <?= json_encode($items) ?>;

function createRow() {
  const row = document.createElement('tr');
  const itemOptions = itemData.map(item => `<option value="${item.id}" data-item='${JSON.stringify(item)}'>${item.name}</option>`).join('');
  row.innerHTML = `
    <td><select name="item_id[]" class="form-select item-select" required><option value="">-- Select --</option>${itemOptions}</select></td>
    <td><input type="number" name="mrp[]" class="form-control mrp" readonly></td>
    <td><input type="number" name="discount[]" class="form-control discount" readonly></td>
    <td><input type="number" name="cgst[]" class="form-control cgst" readonly></td>
    <td><input type="number" name="sgst[]" class="form-control sgst" readonly></td>
    <td><input type="number" name="special_discount[]" class="form-control special_discount" value="0"></td>
    <td><input type="number" name="qty[]" class="form-control qty" value="1" min="1"></td>
    <td><input type="text" class="form-control total" readonly></td>
    <td><button type="button" class="btn btn-danger btn-sm removeRow">X</button></td>
  `;
  document.querySelector('#itemsTable tbody').appendChild(row);
  updateEvents();
}

function updateEvents() {
  document.querySelectorAll('.removeRow').forEach(btn => {
    btn.onclick = () => {
      btn.closest('tr').remove();
      calculateGrandTotal();
    }
  });

  document.querySelectorAll('.item-select').forEach(select => {
    select.onchange = function () {
      const item = JSON.parse(this.selectedOptions[0].dataset.item);
      const row = this.closest('tr');
      row.querySelector('.mrp').value = item.mrp;
      row.querySelector('.discount').value = item.discount;
      row.querySelector('.cgst').value = item.cgst;
      row.querySelector('.sgst').value = item.sgst;
      calculateRow(row);
    }
  });

  document.querySelectorAll('.qty, .special_discount').forEach(input => {
    input.oninput = function () {
      const row = this.closest('tr');
      calculateRow(row);
    }
  });
}

function calculateRow(row) {
  const mrp = parseFloat(row.querySelector('.mrp').value || 0);
  const baseDiscount = parseFloat(row.querySelector('.discount').value || 0);
  const specialDiscount = parseFloat(row.querySelector('.special_discount').value || 0);
  const cgst = parseFloat(row.querySelector('.cgst').value || 0);
  const sgst = parseFloat(row.querySelector('.sgst').value || 0);
  const qty = parseFloat(row.querySelector('.qty').value || 1);

  const discountedPrice = mrp - ((mrp * baseDiscount) / 100) - ((mrp * specialDiscount) / 100);
  const taxAmount = discountedPrice * (cgst + sgst) / 100;
  const total = (discountedPrice + taxAmount) * qty;

  row.querySelector('.total').value = total.toFixed(2);
  calculateGrandTotal();
}

function calculateGrandTotal() {
  let grandTotal = 0;
  document.querySelectorAll('.total').forEach(t => {
    grandTotal += parseFloat(t.value || 0);
  });
  document.getElementById('grandTotal').innerText = grandTotal.toFixed(2);
}

document.getElementById('addRow').addEventListener('click', createRow);

// Init
createRow();
</script>
