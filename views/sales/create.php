<?php
$title = "Create New Sale";
ob_start();
?>
<div class="card shadow mb-4">
    <div class="card-header py-3 bg-primary text-white">
        <h6 class="m-0 font-weight-bold">New Sale</h6>
    </div>
    <div class="card-body">
        <form id="saleForm">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            
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
                    <label class="form-label">Sale Date</label>
                    <input type="datetime-local" class="form-control" name="sale_date" 
                           value="<?= date('Y-m-d\TH:i') ?>" required>
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
                                        data-gst="<?= $item['central_gst'] + $item['state_gst'] ?>"
                                        data-stock="<?= $item['quantity'] ?>">
                                    <?= htmlspecialchars($item['item_name']) ?> (<?= htmlspecialchars($item['item_brand']) ?>)
                                    - Stock: <?= $item['quantity'] ?>
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
                        <button type="button" class="btn btn-secondary" id="saveDraftBtn">
                            <i class="fas fa-save me-1"></i> Save Draft
                        </button>
                        <button type="submit" class="btn btn-success" id="completeSaleBtn">
                            <i class="fas fa-check-circle me-1"></i> Complete Sale
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal for new customer -->
<div class="modal fade" id="newCustomerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Add New Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="customerForm">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="customer_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contact Number</label>
                        <input type="text" class="form-control" name="contact_number" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" name="address" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveCustomerBtn">Save Customer</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap-5'
    });

    // Add new customer option
    $('#customerSelect').on('select2:open', function() {
        $('.select2-results').append(
            '<div class="select2-add-customer">' +
            '<button class="btn btn-sm btn-link text-primary" id="addCustomerBtn">' +
            '<i class="fas fa-plus me-1"></i>Add New Customer' +
            '</button>' +
            '</div>'
        );
    });

    // Show new customer modal
    $(document).on('click', '#addCustomerBtn', function() {
        $('#newCustomerModal').modal('show');
    });

    // Save new customer
    $('#saveCustomerBtn').click(function() {
        const formData = $('#customerForm').serialize();
        
        $.ajax({
            url: '<?= BASE_URL ?>api/customers',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    // Add new customer to select
                    const newOption = new Option(
                        response.data.customer_name + ' - ' + response.data.contact_number, 
                        response.data.customer_id, 
                        true, 
                        true
                    );
                    $('#customerSelect').append(newOption).trigger('change');
                    
                    // Close modal and reset form
                    $('#newCustomerModal').modal('hide');
                    $('#customerForm')[0].reset();
                    
                    Toastify({
                        text: "Customer added successfully!",
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#28a745",
                    }).showToast();
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON.errors;
                let errorMessages = [];
                
                for (const field in errors) {
                    errorMessages.push(errors[field]);
                }
                
                Toastify({
                    text: errorMessages.join('\n'),
                    duration: 5000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#dc3545",
                }).showToast();
            }
        });
    });

    // Add item to sale
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
        const stock = parseInt(selectedItem.data('stock'));
        
        if (qty > stock) {
            alert(`Only ${stock} items available in stock`);
            return;
        }
        
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
                        <input type="hidden" name="items[${itemId}][item_id]" value="${itemId}">
                    </td>
                    <td>
                        <input type="number" class="form-control form-control-sm item-qty" 
                               name="items[${itemId}][quantity]" value="${qty}" min="1" data-mrp="${mrp}">
                    </td>
                    <td>₹${mrp.toFixed(2)}</td>
                    <td>
                        <input type="number" class="form-control form-control-sm item-discount" 
                               name="items[${itemId}][discount]" value="${discount}" min="0" max="100">
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

    // Remove item from sale
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
            const amountBeforeTax = amountBeforeDiscount - discountAmount;
            const taxAmount = amountBeforeTax * (gst / 100);
            
            subtotal += amountBeforeDiscount;
            totalDiscount += discountAmount;
            totalTax += taxAmount;
        });
        
        $('#subtotal').text(`₹${subtotal.toFixed(2)}`);
        $('#totalDiscount').text(`₹${totalDiscount.toFixed(2)}`);
        $('#totalTax').text(`₹${totalTax.toFixed(2)}`);
        $('#totalAmount').text(`₹${(subtotal - totalDiscount + totalTax).toFixed(2)}`);
    }

    // Submit sale form
    $('#saleForm').submit(function(e) {
        e.preventDefault();
        
        if ($('#itemsTable tbody tr').length === 0) {
            alert('Please add at least one item to the sale');
            return;
        }
        
        const formData = $(this).serialize();
        
        // Disable button and show loading
        const submitBtn = $('#completeSaleBtn');
        submitBtn.prop('disabled', true);
        submitBtn.html('<span class="spinner-border spinner-border-sm" role="status"></span> Processing...');
        
        $.ajax({
            url: '<?= BASE_URL ?>api/sales',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    window.location.href = `<?= BASE_URL ?>sales/invoice/${response.data.sale_id}`;
                }
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false);
                submitBtn.html('<i class="fas fa-check-circle me-1"></i> Complete Sale');
                
                const errors = xhr.responseJSON.errors;
                let errorMessages = [];
                
                for (const field in errors) {
                    errorMessages.push(errors[field]);
                }
                
                Toastify({
                    text: errorMessages.join('\n'),
                    duration: 5000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#dc3545",
                }).showToast();
            }
        });
    });
});
</script>

<?php
$content = ob_get_clean();
include 'layout.php';
?>