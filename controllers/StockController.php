<?php
require_once '../models/Stock.php';
require_once '../models/Item.php';
require_once '../models/Purchase.php';

class StockController {
    private $stockModel;
    private $itemModel;
    private $purchaseModel;

    public function __construct() {
        $this->stockModel = new Stock();
        $this->itemModel = new Item();
        $this->purchaseModel = new Purchase();
    }

    public function index() {
        $stock = $this->stockModel->getAllStockWithDetails();
        $lowStockItems = $this->stockModel->getLowStockItems(5); // Items with quantity < 5
        
        $title = "Stock Management";
        include '../views/stock/index.php';
    }

    public function purchase() {
        $items = $this->itemModel->getAllItems();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate CSRF token
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die("Invalid CSRF token");
            }

            $itemId = (int)$_POST['item_id'];
            $quantity = (int)$_POST['quantity'];
            $purchasePrice = (float)$_POST['purchase_price'];
            $supplier = trim($_POST['supplier']);
            
            try {
                // Record purchase
                $this->purchaseModel->create([
                    'item_id' => $itemId,
                    'quantity' => $quantity,
                    'purchase_price' => $purchasePrice,
                    'purchase_date' => date('Y-m-d'),
                    'supplier_details' => $supplier
                ]);
                
                // Update stock
                $this->stockModel->increaseStock($itemId, $quantity);
                
                $_SESSION['success'] = "Purchase recorded and stock updated successfully!";
                header('Location: /stock');
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = "Error: " . $e->getMessage();
            }
        }
        
        $title = "Purchase Items";
        include '../views/stock/purchase.php';
    }

    public function report() {
        $stockHistory = $this->stockModel->getStockMovementHistory();
        $purchases = $this->purchaseModel->getRecentPurchases(30); // Last 30 days
        $sales = $this->stockModel->getRecentSales(30); // Last 30 days
        
        $title = "Stock Report";
        include '../views/stock/report.php';
    }
}