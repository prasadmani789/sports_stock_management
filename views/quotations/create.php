<?php
$title = "Create Quotation";
ob_start();
?>
<div class="card shadow mb-4">
    <div class="card-header py-3 bg-primary text-white">
        <h6 class="m-0 font-weight-bold">New Quotation</h6>
    </div>
    <div class="card-body">
        <form id="quotationForm" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" name="items_json" id="itemsJson">
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Customer</label>
                    <select class="form-select select2" id="customerSelect" name="customer_id" required>
                        <option value="">Select Customer</option>
                        <?php foreach($customers as $customer): ?>
                        <option value="<?= $customer['customer_id'] ?>">
                            <?= htmlspecialchars($customer['customer_name']) ?> - <?= htmlspecialchars($customer['contact_number']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Valid Until</label>
                    <input type="date" class="form-control" name="valid_until" 
                           value="<?= date('Y-m-d', strtotime('+7 days')) ?>" required>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Add Items</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <select class="form-select select2" id="itemSelect">
                                <option value="">Select Item</option>
                                <?php foreach($availableItems as $item): ?>
                                <option value="<?= $item['item_id'] ?>" 
                                        data-mrp="<?= $item['mrp'] ?>"
                                        data-gst="<?= $item['central_gst'] + $item['state_gst'] ?>">
                                    <?= htmlspecialchars($item['item_name']) ?> (<?= htmlspecialchars($item['item_brand']) ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control" id="itemQty" placeholder="Qty" min="1" value="1">
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control" id="itemDiscount" placeholder="Discount %" min="0" max="100" value="0">
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-primary w-100" id="addItemBtn">
                                <i class="fas fa-plus me-1"></i> Add Item
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive mb-4">
                <table class="table table-bordered" id="itemsTable">
                    <thead class="table-dark">
                        <tr>
                            <th width="30%">Item</th>
                            <th width="10%">Qty</th>
                            <th width="15%">MRP</th>
                            <th width="10%">Disc.%</th>
                            <th width="15%">GST %</th>
                            <th width="15%">Amount</th>
                            <th width="5%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Items will be added here dynamically -->
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="5" class="text-end fw-bold">Subtotal:</td>
                            <td id="subtotal">₹0.00</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="5" class="text-end fw-bold">Discount:</td>
                            <td id="totalDiscount">₹0.00</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="5" class="text-end fw-bold">GST:</td>
                            <td id="totalTax">₹0.00</td>
                            <td></td>
                        </tr>
                        <tr class="table-active">
                            <td colspan="5" class="text-end fw-bold">Total Amount:</td>
                            <td id="totalAmount">₹0.00</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <label class="form-label">Additional Notes</label>
                    <textarea class="form-control" name="notes" rows="3"></textarea>
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-secondary" id="resetBtn">
                            <i class="fas fa-sync-alt me-1"></i> Reset
                        </button>
                        <button type="submit" class="btn btn-success" id="createQuotationBtn">
                            <i class="fas fa-file-invoice me-1"></i> Create Quotation
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap-5'
    });

    // Add item to quotation
    $('#addItemBtn').click(function() {
        const itemSelect = $('#itemSelect');
        const selectedItem = itemSelect.find('option:selected');
        const itemId = selectedItem.val();
        
        if (!itemId) {
            alert('Please select an item');
            return;
        }
        
        const itemName = selectedItem.text().split(' - ')[0];
        const qty = parseInt($('#itemQty').val()) || 1;
        const discount = parseFloat($('#itemDiscount').val()) || 0;
        const mrp = parseFloat(selectedItem.data('mrp'));
        const gst = parseFloat(selectedItem.data('gst'));
        
        const amountBeforeTax = mrp * qty * (1 - discount / 100);
        const taxAmount = amountBeforeTax * (gst / 100);
        const totalAmount = amountBeforeTax + taxAmount;
        
        // Add row to table
        const rowId = `item-${itemId}`;
        if ($(`#${rowId}`).length) {
            // Item already exists, update quantity
            const existingQty = parseInt($(`#${rowId} .item-qty`).val());
            $(`#${rowId} .item-qty`).val(existingQty + qty);
        } else {
            // Add new row
            $('#itemsTable tbody').append(`
                <tr id="${rowId}">
                    <td>${itemName}
                        <input type="hidden" name="item_id" value="${itemId}">
                    </td>
                    <td>
                        <input type="number" class="form-control form-control-sm item-qty" 
                               value="${qty}" min="1" data-mrp="${mrp}">
                    </td>
                    <td>₹${mrp.toFixed(2)}</td>
                    <td>
                        <input type="number" class="form-control form-control-sm item-discount" 
                               value="${discount}" min="0" max="100">
                    </td>
                    <td>${gst}%</td>
                    <td class="item-total">₹${totalAmount.toFixed(2)}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger remove-item">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `);
        }
        
        // Reset form
        itemSelect.val('').trigger('change');
        $('#itemQty').val(1);
        $('#itemDiscount').val(0);
        
        // Update totals
        updateTotals();
    });

    // Remove item from quotation
    $(document).on('click', '.remove-item', function() {
        $(this).closest('tr').remove();
        updateTotals();
    });

    // Update quantity or discount
    $(document).on('change', '.item-qty, .item-discount', function() {
        const row = $(this).closest('tr');
        const qty = parseInt(row.find('.item-qty').val()) || 0;
        const discount = parseFloat(row.find('.item-discount').val()) || 0;
        const mrp = parseFloat(row.find('.item-qty').data('mrp'));
        const gst = parseFloat(row.find('td:eq(4)').text().replace('%', ''));
        
        const amountBeforeTax = mrp * qty * (1 - discount / 100);
        const taxAmount = amountBeforeTax * (gst / 100);
        const totalAmount = amountBeforeTax + taxAmount;
        
        row.find('.item-total').text(`₹${totalAmount.toFixed(2)}`);
        updateTotals();
    });

    // Calculate totals
    function updateTotals() {
        let subtotal = 0;
        let totalDiscount = 0;
        let totalTax = 0;
        
        $('#itemsTable tbody tr').each(function() {
            const totalText = $(this).find('.item-total').text().replace('₹', '');
            const total = parseFloat(totalText);
            const qty = parseInt($(this).find('.item-qty').val()) || 0;
            const discount = parseFloat($(this).find('.item-discount').val()) || 0;
            const mrp = parseFloat($(this).find('.item-qty').data('mrp'));
            const gst = parseFloat($(this).find('td:eq(4)').text().replace('%', ''));
            
            const amountBeforeDiscount = mrp * qty;
            const discountAmount = amountBeforeDiscount * (discount / 100);
            const amountAfterDiscount = amountBeforeDiscount - discountAmount;
            const taxAmount = amountAfterDiscount * (gst / 100);
            
            subtotal += amountBeforeDiscount;
            totalDiscount += discountAmount;
            totalTax += taxAmount;
        });
        
        $('#subtotal').text(`₹${subtotal.toFixed(2)}`);
        $('#totalDiscount').text(`₹${totalDiscount.toFixed(2)}`);
        $('#totalTax').text(`₹${totalTax.toFixed(2)}`);
        $('#totalAmount').text(`₹${(subtotal - totalDiscount + totalTax).toFixed(2)}`);
    }

    // Reset form
    $('#resetBtn').click(function() {
        if (confirm('Are you sure you want to reset the form? All items will be removed.')) {
            $('#itemsTable tbody').empty();
            updateTotals();
            $('#customerSelect').val('').trigger('change');
            $('#itemSelect').val('').trigger('change');
            $('#itemQty').val(1);
            $('#itemDiscount').val(0);
            $('textarea[name="notes"]').val('');
        }
    });

    // Submit quotation form
    $('#quotationForm').submit(function(e) {
        e.preventDefault();
        
        if ($('#itemsTable tbody tr').length === 0) {
            alert('Please add at least one item to the quotation');
            return;
        }
        
        // Prepare items data
        const items = [];
        $('#itemsTable tbody tr').each(function() {
            const itemId = $(this).find('input[name="item_id"]').val();
            const qty = parseInt($(this).find('.item-qty').val()) || 0;
            const discount = parseFloat($(this).find('.item-discount').val()) || 0;
            
            items.push({
                item_id: itemId,
                quantity: qty,
                discount: discount
            });
        });
        
        // Set items JSON
        $('#itemsJson').val(JSON.stringify(items));
        
        // Submit form
        this.submit();
    });
});
</script>

<?php
$content = ob_get_clean();
include 'layout.php';
?>