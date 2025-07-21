<?php
require_once '../models/Quotation.php';
require_once '../models/Customer.php';
require_once '../models/Item.php';

class QuotationController {
    private $quotationModel;
    private $customerModel;
    private $itemModel;

    public function __construct() {
        $this->quotationModel = new Quotation();
        $this->customerModel = new Customer();
        $this->itemModel = new Item();
    }

    public function create() {
        $customers = $this->customerModel->getAllCustomers();
        $availableItems = $this->itemModel->getAllItems();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate CSRF token
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die("Invalid CSRF token");
            }

            $customerId = (int)$_POST['customer_id'];
            $validUntil = $_POST['valid_until'];
            $items = json_decode($_POST['items_json'], true);
            
            try {
                // Calculate totals
                $subtotal = 0;
                $totalDiscount = 0;
                $totalTax = 0;
                
                foreach ($items as $item) {
                    $itemDetails = $this->itemModel->getItemById($item['item_id']);
                    $mrp = (float)$itemDetails['mrp'];
                    $discount = (float)$item['discount'];
                    $gst = (float)$itemDetails['central_gst'] + (float)$itemDetails['state_gst'];
                    
                    $amountBeforeDiscount = $mrp * $item['quantity'];
                    $discountAmount = $amountBeforeDiscount * ($discount / 100);
                    $amountAfterDiscount = $amountBeforeDiscount - $discountAmount;
                    $taxAmount = $amountAfterDiscount * ($gst / 100);
                    
                    $subtotal += $amountBeforeDiscount;
                    $totalDiscount += $discountAmount;
                    $totalTax += $taxAmount;
                }
                
                $totalAmount = $subtotal - $totalDiscount + $totalTax;
                
                // Create quotation
                $quotationId = $this->quotationModel->create([
                    'customer_id' => $customerId,
                    'quotation_date' => date('Y-m-d'),
                    'valid_until' => $validUntil,
                    'total_amount' => $totalAmount,
                    'discount' => $totalDiscount,
                    'tax_amount' => $totalTax
                ]);
                
                // Add quotation items
                foreach ($items as $item) {
                    $itemDetails = $this->itemModel->getItemById($item['item_id']);
                    $mrp = (float)$itemDetails['mrp'];
                    
                    $this->quotationModel->addQuotationItem([
                        'quotation_id' => $quotationId,
                        'item_id' => $item['item_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $mrp,
                        'discount' => $item['discount']
                    ]);
                }
                
                $_SESSION['success'] = "Quotation created successfully!";
                header("Location: /quotations/view/{$quotationId}");
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = "Error creating quotation: " . $e->getMessage();
            }
        }
        
        $title = "Create Quotation";
        include '../views/quotations/create.php';
    }

    public function index() {
        $quotations = $this->quotationModel->getAllQuotations();
        $title = "Quotations";
        include '../views/quotations/index.php';
    }

    public function view($quotationId) {
        $quotation = $this->quotationModel->getQuotationById($quotationId);
        
        if (!$quotation) {
            $_SESSION['error'] = "Quotation not found";
            header('Location: /quotations');
            exit();
        }
        
        $customer = $this->customerModel->getCustomerById($quotation['customer_id']);
        $items = $this->quotationModel->getQuotationItems($quotationId);
        
        $title = "Quotation #{$quotationId}";
        include '../views/quotations/view.php';
    }

    public function convertToSale($quotationId) {
        $quotation = $this->quotationModel->getQuotationById($quotationId);
        
        if (!$quotation) {
            $_SESSION['error'] = "Quotation not found";
            header('Location: /quotations');
            exit();
        }
        
        try {
            // Start transaction
            $this->quotationModel->beginTransaction();
            
            // Mark quotation as accepted
            $this->quotationModel->updateStatus($quotationId, 'accepted');
            
            // Create sale from quotation
            $saleId = $this->quotationModel->convertToSale($quotationId);
            
            // Commit transaction
            $this->quotationModel->commitTransaction();
            
            $_SESSION['success'] = "Quotation converted to sale successfully!";
            header("Location: /sales/invoice/{$saleId}");
            exit();
        } catch (Exception $e) {
            $this->quotationModel->rollbackTransaction();
            $_SESSION['error'] = "Error converting quotation: " . $e->getMessage();
            header("Location: /quotations/view/{$quotationId}");
            exit();
        }
    }
}