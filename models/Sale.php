<?php
class Sale {
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

    public function getAllSales() {
        $query = "SELECT s.*, c.customer_name 
                 FROM sales s
                 LEFT JOIN customers c ON s.customer_id = c.customer_id
                 ORDER BY s.sale_date DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getSaleById($id) {
        $query = "SELECT s.*, c.customer_name, c.address, c.contact_number 
                 FROM sales s
                 LEFT JOIN customers c ON s.customer_id = c.customer_id
                 WHERE s.sale_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getSalesByCustomer($customerId) {
        $query = "SELECT s.* FROM sales s
                 WHERE s.customer_id = :customer_id
                 ORDER BY s.sale_date DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':customer_id', $customerId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function create($data) {
        $query = "INSERT INTO sales (customer_id, sale_date, total_amount, discount, tax_amount, notes)
                  VALUES (:customer_id, :sale_date, :total_amount, :discount, :tax_amount, :notes)";
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':customer_id', $data['customer_id'], PDO::PARAM_INT);
        $stmt->bindParam(':sale_date', $data['sale_date']);
        $stmt->bindParam(':total_amount', $data['total_amount']);
        $stmt->bindParam(':discount', $data['discount']);
        $stmt->bindParam(':tax_amount', $data['tax_amount']);
        $stmt->bindParam(':notes', $data['notes']);
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function addSaleItem($data) {
        $query = "INSERT INTO sale_items (sale_id, item_id, quantity, unit_price, discount)
                  VALUES (:sale_id, :item_id, :quantity, :unit_price, :discount)";
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':sale_id', $data['sale_id'], PDO::PARAM_INT);
        $stmt->bindParam(':item_id', $data['item_id'], PDO::PARAM_INT);
        $stmt->bindParam(':quantity', $data['quantity'], PDO::PARAM_INT);
        $stmt->bindParam(':unit_price', $data['unit_price']);
        $stmt->bindParam(':discount', $data['discount']);
        
        return $stmt->execute();
    }

    public function getSaleItems($saleId) {
        $query = "SELECT si.*, i.item_name, i.item_brand 
                 FROM sale_items si
                 JOIN items i ON si.item_id = i.item_id
                 WHERE si.sale_id = :sale_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':sale_id', $saleId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getRecentSales($days = 30) {
        $query = "SELECT s.sale_date, i.item_name, si.quantity, si.unit_price, si.discount
                 FROM sales s
                 JOIN sale_items si ON s.sale_id = si.sale_id
                 JOIN items i ON si.item_id = i.item_id
                 WHERE s.sale_date >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
                 ORDER BY s.sale_date DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':days', $days, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}