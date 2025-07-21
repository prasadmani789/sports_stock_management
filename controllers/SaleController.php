<?php
require_once '../models/Sale.php';
require_once '../models/Customer.php';
require_once '../models/Item.php';
require_once '../models/Stock.php';

class SaleController {
    private $saleModel;
    private $customerModel;
    private $itemModel;
    private $stockModel;

    public function __construct() {
        $this->saleModel = new Sale();
        $this->customerModel = new Customer();
        $this->itemModel = new Item();
        $this->stockModel = new Stock();
    }

    public function create() {
        $customers = $this->customerModel->getAllCustomers();
        $availableItems = $this->itemModel->getAvailableItemsWithStock();
        
        $title = "Create New Sale";
        include '../views/sales/create.php';
    }

    public function index() {
        $sales = $this->saleModel->getAllSales();
        $title = "Sales History";
        include '../views/sales/index.php';
    }

    public function invoice($saleId) {
        $sale = $this->saleModel->getSaleById($saleId);
        if (!$sale) {
            $_SESSION['error'] = "Sale not found";
            header('Location: /sales/create');
            exit();
        }

        $saleItems = $this->saleModel->getSaleItems($saleId);
        $customer = $this->customerModel->getCustomerById($sale['customer_id']);
        
        $title = "Invoice #{$saleId}";
        include '../views/sales/invoice.php';
    }

    // API Method for creating sale
    public function apiCreate() {
        header('Content-Type: application/json');
        
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Validate input
            if (empty($data['customer_id']) {
                throw new Exception('Customer is required');
            }
            
            if (empty($data['items']) || !is_array($data['items'])) {
                throw new Exception('At least one item is required');
            }

            // Start transaction
            $this->saleModel->beginTransaction();

            // Calculate totals
            $subtotal = 0;
            $totalDiscount = 0;
            $totalTax = 0;
            
            foreach ($data['items'] as $item) {
                $itemDetails = $this->itemModel->getItemById($item['item_id']);
                
                if (!$itemDetails) {
                    throw new Exception("Item not found: {$item['item_id']}");
                }
                
                $quantity = (int)$item['quantity'];
                $discount = (float)$item['discount'];
                $mrp = (float)$itemDetails['mrp'];
                $gst = (float)$itemDetails['central_gst'] + (float)$itemDetails['state_gst'];
                
                // Check stock availability
                $stock = $this->stockModel->getStockByItemId($item['item_id']);
                if ($stock['quantity'] < $quantity) {
                    throw new Exception("Insufficient stock for item: {$itemDetails['item_name']}");
                }
                
                $amountBeforeDiscount = $mrp * $quantity;
                $discountAmount = $amountBeforeDiscount * ($discount / 100);
                $amountAfterDiscount = $amountBeforeDiscount - $discountAmount;
                $taxAmount = $amountAfterDiscount * ($gst / 100);
                
                $subtotal += $amountBeforeDiscount;
                $totalDiscount += $discountAmount;
                $totalTax += $taxAmount;
            }
            
            $totalAmount = $subtotal - $totalDiscount + $totalTax;
            
            // Create sale record
            $saleId = $this->saleModel->create([
                'customer_id' => $data['customer_id'],
                'sale_date' => date('Y-m-d H:i:s'),
                'total_amount' => $totalAmount,
                'discount' => $totalDiscount,
                'tax_amount' => $totalTax,
                'notes' => $data['notes'] ?? ''
            ]);
            
            // Create sale items and update stock
            foreach ($data['items'] as $item) {
                $itemDetails = $this->itemModel->getItemById($item['item_id']);
                $mrp = (float)$itemDetails['mrp'];
                $discount = (float)$item['discount'];
                
                $this->saleModel->addSaleItem([
                    'sale_id' => $saleId,
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $mrp,
                    'discount' => $discount
                ]);
                
                // Update stock
                $this->stockModel->decreaseStock($item['item_id'], $item['quantity']);
            }
            
            // Commit transaction
            $this->saleModel->commitTransaction();
            
            return [
                'success' => true,
                'data' => ['sale_id' => $saleId],
                'message' => 'Sale completed successfully'
            ];
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->saleModel->rollbackTransaction();
            http_response_code(400);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}