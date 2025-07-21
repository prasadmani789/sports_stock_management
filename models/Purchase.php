<?php
class Purchase {
    private $db;

    public function __construct() {
        $this->db = (new Database())->connect();
    }

    public function create($data) {
        $query = "INSERT INTO purchases 
                 (item_id, quantity, purchase_price, purchase_date, supplier_details)
                 VALUES (:item_id, :quantity, :purchase_price, :purchase_date, :supplier_details)";
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':item_id', $data['item_id'], PDO::PARAM_INT);
        $stmt->bindParam(':quantity', $data['quantity'], PDO::PARAM_INT);
        $stmt->bindParam(':purchase_price', $data['purchase_price']);
        $stmt->bindParam(':purchase_date', $data['purchase_date']);
        $stmt->bindParam(':supplier_details', $data['supplier_details']);
        
        return $stmt->execute();
    }

    public function getRecentPurchases($days = 30) {
        $query = "SELECT p.*, i.item_name, i.item_brand 
                 FROM purchases p
                 JOIN items i ON p.item_id = i.item_id
                 WHERE p.purchase_date >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
                 ORDER BY p.purchase_date DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':days', $days, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getPurchasesByItem($itemId) {
        $query = "SELECT p.* FROM purchases p
                 WHERE p.item_id = :item_id
                 ORDER BY p.purchase_date DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':item_id', $itemId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}