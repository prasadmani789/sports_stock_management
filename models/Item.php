<?php
class Item {
    private $db;

    public function __construct() {
        $this->db = (new Database())->connect();
    }

    public function getAllItems() {
        $query = "SELECT * FROM items ORDER BY item_name";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getItemById($id) {
        $query = "SELECT * FROM items WHERE item_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function create($data) {
        $query = "INSERT INTO items (item_name, item_type, item_brand, central_gst, state_gst, mrp) 
                  VALUES (:name, :type, :brand, :cgst, :sgst, :mrp)";
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':name', $data['item_name']);
        $stmt->bindParam(':type', $data['item_type']);
        $stmt->bindParam(':brand', $data['item_brand']);
        $stmt->bindParam(':cgst', $data['central_gst']);
        $stmt->bindParam(':sgst', $data['state_gst']);
        $stmt->bindParam(':mrp', $data['mrp']);
        
        return $stmt->execute();
    }

    public function update($id, $data) {
        $query = "UPDATE items SET 
                  item_name = :name, 
                  item_type = :type, 
                  item_brand = :brand, 
                  central_gst = :cgst, 
                  state_gst = :sgst, 
                  mrp = :mrp 
                  WHERE item_id = :id";
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $data['item_name']);
        $stmt->bindParam(':type', $data['item_type']);
        $stmt->bindParam(':brand', $data['item_brand']);
        $stmt->bindParam(':cgst', $data['central_gst']);
        $stmt->bindParam(':sgst', $data['state_gst']);
        $stmt->bindParam(':mrp', $data['mrp']);
        
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM items WHERE item_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}