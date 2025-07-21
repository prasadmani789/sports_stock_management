<?php
class Stock {
    private $db;

    public function __construct() {
        $this->db = (new Database())->connect();
    }

    public function getAllStockWithDetails() {
        $query = "SELECT s.*, i.item_name, i.item_brand, i.mrp 
                 FROM stock s
                 JOIN items i ON s.item_id = i.item_id
                 ORDER BY i.item_name";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getStockByItemId($itemId) {
        $query = "SELECT * FROM stock WHERE item_id = :item_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':item_id', $itemId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function increaseStock($itemId, $quantity) {
        $query = "UPDATE stock SET 
                 quantity = quantity + :quantity,
                 last_purchase_date = CURRENT_DATE
                 WHERE item_id = :item_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':item_id', $itemId, PDO::PARAM_INT);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function decreaseStock($itemId, $quantity) {
        $query = "UPDATE stock SET 
                 quantity = quantity - :quantity
                 WHERE item_id = :item_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':item_id', $itemId, PDO::PARAM_INT);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getLowStockItems($threshold = 5) {
        $query = "SELECT s.*, i.item_name, i.item_brand 
                 FROM stock s
                 JOIN items i ON s.item_id = i.item_id
                 WHERE s.quantity <= :threshold
                 ORDER BY s.quantity ASC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':threshold', $threshold, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getStockMovementHistory($limit = 100) {
        // Combine purchase and sales data
        $query = "(
                    SELECT 
                        'purchase' as type, 
                        p.purchase_date as date, 
                        i.item_name, 
                        p.quantity, 
                        p.purchase_price as unit_price, 
                        NULL as discount, 
                        p.supplier_details as details
                    FROM purchases p
                    JOIN items i ON p.item_id = i.item_id
                    ORDER BY p.purchase_date DESC
                    LIMIT :limit
                 ) UNION ALL (
                    SELECT 
                        'sale' as type, 
                        s.sale_date as date, 
                        i.item_name, 
                        si.quantity, 
                        si.unit_price, 
                        si.discount, 
                        CONCAT('Sale #', s.sale_id) as details
                    FROM sale_items si
                    JOIN sales s ON si.sale_id = s.sale_id
                    JOIN items i ON si.item_id = i.item_id
                    ORDER BY s.sale_date DESC
                    LIMIT :limit
                 )
                 ORDER BY date DESC
                 LIMIT :limit";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getRecentSales($days = 30) {
        $query = "SELECT i.item_id, i.item_name, SUM(si.quantity) as total_sold
                 FROM sale_items si
                 JOIN sales s ON si.sale_id = s.sale_id
                 JOIN items i ON si.item_id = i.item_id
                 WHERE s.sale_date >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
                 GROUP BY i.item_id
                 ORDER BY total_sold DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':days', $days, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}