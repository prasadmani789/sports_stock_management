<?php
include 'includes/db.php';
include 'includes/navbar.php';

// Fetch customers
$customers = $pdo->query("SELECT id, name FROM customers ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

// Fetch items
$items = $pdo->query("SELECT * FROM items ORDER BY item_name")->fetchAll(PDO::FETCH_ASSOC);
$item_data_json = json_encode($items);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sales - Sports Items Shop</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .table td, .table th {
      vertical-align: middle;
    }
    .alert-dismissible .btn-close {
      position: absolute;
      top: 0.75rem;
      right: 1rem;
    }
  </style>
</head>
<body>
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>New Sale</h2>
    <a href="sales_list.php" class="btn btn-outline-primary">View Sales Report</a>
  </div>

  <form id="salesForm">
    <div class="row mb-3">
      <div class="col-md-6">
        <label for="customer_id" class="form-label">Select Customer</label>
        <select name="customer_id" id="customer_id" class="form-select" required>
          <option value="">-- Select Customer --</option>
          <?php foreach ($customers as $cust): ?>
            <option value="<?= $cust['id'] ?>"><?= htmlspecialchars($cust['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-6">
        <label for="sale_date" class="form-label">Sale Date</label>
        <input type="date" name="sale_date" id="sale_date" class="form-control" required value="<?= date('Y-m-d') ?>">
      </div>
    </div>

    <table class="table table-bordered" id="itemsTable">
      <thead class="table-secondary">
        <tr>
          <th>Item</th>
          <th>MRP</th>
          <th>Discount (%)</th>
          <th>CGST (%)</th>
          <th>SGST (%)</th>
          <th>Special Discount (%)</th>
          <th>Quantity</th>
          <th>Total</th>
          <th><button type="button" class="btn btn-sm btn-success" onclick="addRow()">+</button></th>
        </tr>
      </thead>
      <tbody id="itemRows"></tbody>
    </table>

    <div class="text-end mb-3">
      <strong>Grand Total: ₹<span id="grandTotal">0.00</span></strong>
    </div>

    <button type="submit" class="btn btn-primary">Save Sale</button>
    <a href="index.php" class="btn btn-secondary">Back</a>
  </form>

  <div id="responseMsg" class="alert mt-3 d-none alert-dismissible fade show" role="alert">
    <span id="responseText"></span>
    <button type="button" class="btn-close" aria-label="Close" onclick="document.getElementById('responseMsg').classList.add('d-none')"></button>
  </div>
</div>

<script>
const itemData = <?= $item_data_json ?>;

function addRow() {
  const row = document.createElement('tr');
  row.innerHTML = `
    <td>
      <select name="item_id[]" class="form-select item-select" required>
        <option value="">-- Select --</option>
        ${itemData.map(item => `<option value="${item.id}">${item.item_name}</option>`).join('')}
      </select>
    </td>
    <td><input type="number" name="mrp_price[]" class="form-control mrp" readonly></td>
    <td><input type="number" name="discount[]" class="form-control discount" readonly></td>
    <td><input type="number" name="cgst[]" class="form-control cgst" readonly></td>
    <td><input type="number" name="sgst[]" class="form-control sgst" readonly></td>
    <td><input type="number" name="special_discount[]" class="form-control special" value="0"></td>
    <td><input type="number" name="quantity[]" class="form-control qty" value="1" min="1"></td>
    <td><input type="text" name="item_total[]" class="form-control total" readonly></td>
    <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">×</button></td>
  `;
  document.getElementById('itemRows').appendChild(row);
}

function removeRow(btn) {
  btn.closest('tr').remove();
  calculateGrandTotal();
}

document.addEventListener('change', function (e) {
  if (e.target.classList.contains('item-select')) {
    const selectedId = e.target.value;
    const item = itemData.find(i => i.id == selectedId);
    const row = e.target.closest('tr');
    if (item) {
      row.querySelector('.mrp').value = item.mrp_price;
      row.querySelector('.discount').value = item.discount;
      row.querySelector('.cgst').value = item.cgst;
      row.querySelector('.sgst').value = item.sgst;
      row.querySelector('.special').value = 0;
    }
    calculateRowTotal(row);
  }

  if (e.target.classList.contains('qty') || e.target.classList.contains('special')) {
    calculateRowTotal(e.target.closest('tr'));
  }
});

function calculateRowTotal(row) {
  const mrp = parseFloat(row.querySelector('.mrp').value) || 0;
  const disc = parseFloat(row.querySelector('.discount').value) || 0;
  const cgst = parseFloat(row.querySelector('.cgst').value) || 0;
  const sgst = parseFloat(row.querySelector('.sgst').value) || 0;
  const special = parseFloat(row.querySelector('.special').value) || 0;
  const qty = parseInt(row.querySelector('.qty').value) || 0;

  const discountAmount = mrp * (disc + special) / 100;
  const priceAfterDiscount = mrp - discountAmount;
  const taxAmount = priceAfterDiscount * (cgst + sgst) / 100;
  const total = (priceAfterDiscount + taxAmount) * qty;

  row.querySelector('.total').value = total.toFixed(2);
  calculateGrandTotal();
}

function calculateGrandTotal() {
  let grand = 0;
  document.querySelectorAll('.total').forEach(t => {
    grand += parseFloat(t.value) || 0;
  });
  document.getElementById('grandTotal').textContent = grand.toFixed(2);
}

// AJAX form submission
document.getElementById('salesForm').addEventListener('submit', function (e) {
  e.preventDefault();
  const form = e.target;
  const formData = new FormData(form);

  fetch('save_sale.php', {
    method: 'POST',
    body: formData
  })
    .then(res => res.json())
    .then(data => {
      const msgDiv = document.getElementById('responseMsg');
      const msgText = document.getElementById('responseText');
      msgDiv.classList.remove('d-none', 'alert-danger', 'alert-success');
      msgDiv.classList.add(data.status === 'success' ? 'alert-success' : 'alert-danger');
      msgText.textContent = data.message;

      if (data.status === 'success') {
        form.reset();
        document.getElementById('itemRows').innerHTML = '';
        document.getElementById('grandTotal').textContent = '0.00';
      }
    })
    .catch(() => {
      alert('Error submitting sale');
    });
});
</script>
</body>
</html>
