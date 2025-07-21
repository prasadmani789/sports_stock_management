<?php
class Quotation {
    private $db;

    public function __construct() {
        $this->db = (new Database())->connect();
    }

    public function beginTransaction() {
        $this->db->beginTransaction();
    }

    public function commitTransaction() {
        $this->db->commit();
    }

    public function rollbackTransaction() {
        $this->db->rollBack();
    }

    public function getAllQuotations() {
        $query = "SELECT q.*, c.customer_name 
                 FROM quotations q
                 LEFT JOIN customers c ON q.customer_id = c.customer_id
                 ORDER BY q.quotation_date DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getQuotationById($id) {
        $query = "SELECT q.*, c.customer_name, c.address, c.contact_number 
                 FROM quotations q
                 LEFT JOIN customers c ON q.customer_id = c.customer_id
                 WHERE q.quotation_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function create($data) {
        $query = "INSERT INTO quotations 
                 (customer_id, quotation_date, valid_until, total_amount, discount, tax_amount, status)
                 VALUES (:customer_id, :quotation_date, :valid_until, :total_amount, :discount, :tax_amount, 'pending')";
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':customer_id', $data['customer_id'], PDO::PARAM_INT);
        $stmt->bindParam(':quotation_date', $data['quotation_date']);
        $stmt->bindParam(':valid_until', $data['valid_until']);
        $stmt->bindParam(':total_amount', $data['total_amount']);
        $stmt->bindParam(':discount', $data['discount']);
        $stmt->bindParam(':tax_amount', $data['tax_amount']);
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function addQuotationItem($data) {
        $query = "INSERT INTO quotation_items 
                 (quotation_id, item_id, quantity, unit_price, discount)
                 VALUES (:quotation_id, :item_id, :quantity, :unit_price, :discount)";
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':quotation_id', $data['quotation_id'], PDO::PARAM_INT);
        $stmt->bindParam(':item_id', $data['item_id'], PDO::PARAM_INT);
        $stmt->bindParam(':quantity', $data['quantity'], PDO::PARAM_INT);
        $stmt->bindParam(':unit_price', $data['unit_price']);
        $stmt->bindParam(':discount', $data['discount']);
        
        return $stmt->execute();
    }

    public function getQuotationItems($quotationId) {
        $query = "SELECT qi.*, i.item_name, i.item_brand, i.mrp 
                 FROM quotation_items qi
                 JOIN items i ON qi.item_id = i.item_id
                 WHERE qi.quotation_id = :quotation_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':quotation_id', $quotationId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function updateStatus($quotationId, $status) {
        $query = "UPDATE quotations SET status = :status WHERE quotation_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $quotationId, PDO::PARAM_INT);
        $stmt->bindParam(':status', $status);
        return $stmt->execute();
    }

    public function convertToSale($quotationId) {
        $quotation = $this->getQuotationById($quotationId);
        $items = $this->getQuotationItems($quotationId);
        
        // Create sale record
        $query = "INSERT INTO sales 
                 (customer_id, sale_date, total_amount, discount, tax_amount, notes)
                 VALUES (:customer_id, NOW(), :total_amount, :discount, :tax_amount, 'Converted from quotation #{$quotationId}')";
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':customer_id', $quotation['customer_id'], PDO::PARAM_INT);
        $stmt->bindParam(':total_amount', $quotation['total_amount']);
        $stmt->bindParam(':discount', $quotation['discount']);
        $stmt->bindParam(':tax_amount', $quotation['tax_amount']);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to create sale record");
        }
        
        $saleId = $this->db->lastInsertId();
        
        // Add sale items
        foreach ($items as $item) {
            $query = "INSERT INTO sale_items 
                     (sale_id, item_id, quantity, unit_price, discount)
                     VALUES (:sale_id, :item_id, :quantity, :unit_price, :discount)";
            $stmt = $this->db->prepare($query);
            
            $stmt->bindParam(':sale_id', $saleId, PDO::PARAM_INT);
            $stmt->bindParam(':item_id', $item['item_id'], PDO::PARAM_INT);
            $stmt->bindParam(':quantity', $item['quantity'], PDO::PARAM_INT);
            $stmt->bindParam(':unit_price', $item['unit_price']);
            $stmt->bindParam(':discount', $item['discount']);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to add sale items");
            }
        }
        
        return $saleId;
    }
}