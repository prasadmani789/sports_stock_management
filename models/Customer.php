<?php
class Customer {
    private $db;

    public function __construct() {
        $this->db = (new Database())->connect();
    }

    public function getAllCustomers() {
        $query = "SELECT * FROM customers ORDER BY customer_name";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getCustomerById($id) {
        $query = "SELECT * FROM customers WHERE customer_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function create($data) {
        $query = "INSERT INTO customers (customer_name, address, contact_number, email) 
                  VALUES (:name, :address, :contact, :email)";
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':name', $data['customer_name']);
        $stmt->bindParam(':address', $data['address']);
        $stmt->bindParam(':contact', $data['contact_number']);
        $stmt->bindParam(':email', $data['email']);
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function update($id, $data) {
        $query = "UPDATE customers SET 
                  customer_name = :name, 
                  address = :address, 
                  contact_number = :contact, 
                  email = :email 
                  WHERE customer_id = :id";
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $data['customer_name']);
        $stmt->bindParam(':address', $data['address']);
        $stmt->bindParam(':contact', $data['contact_number']);
        $stmt->bindParam(':email', $data['email']);
        
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM customers WHERE customer_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function searchCustomers($searchTerm) {
        $query = "SELECT * FROM customers 
                 WHERE customer_name LIKE :search 
                 OR contact_number LIKE :search 
                 ORDER BY customer_name";
        $stmt = $this->db->prepare($query);
        $searchTerm = "%$searchTerm%";
        $stmt->bindParam(':search', $searchTerm);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}