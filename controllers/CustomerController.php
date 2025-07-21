<?php
require_once '../models/Customer.php';
require_once '../models/Sale.php';

class CustomerController {
    private $customerModel;
    private $saleModel;

    public function __construct() {
        $this->customerModel = new Customer();
        $this->saleModel = new Sale();
    }

    public function index() {
        $customers = $this->customerModel->getAllCustomers();
        $title = "Customers Management";
        include '../views/customers/index.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate CSRF token
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die("Invalid CSRF token");
            }

            $data = [
                'customer_name' => trim($_POST['customer_name']),
                'address' => trim($_POST['address']),
                'contact_number' => trim($_POST['contact_number']),
                'email' => filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL)
            ];

            if ($this->customerModel->create($data)) {
                $_SESSION['success'] = "Customer added successfully!";
                header('Location: /customers');
                exit();
            } else {
                $_SESSION['error'] = "Failed to add customer. Please try again.";
            }
        }
        $title = "Add New Customer";
        include '../views/customers/create.php';
    }

    public function edit($id) {
        $customer = $this->customerModel->getCustomerById($id);

        if (!$customer) {
            $_SESSION['error'] = "Customer not found";
            header('Location: /customers');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate CSRF token
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die("Invalid CSRF token");
            }

            $data = [
                'customer_name' => trim($_POST['customer_name']),
                'address' => trim($_POST['address']),
                'contact_number' => trim($_POST['contact_number']),
                'email' => filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL)
            ];

            if ($this->customerModel->update($id, $data)) {
                $_SESSION['success'] = "Customer updated successfully!";
                header('Location: /customers');
                exit();
            } else {
                $_SESSION['error'] = "Failed to update customer. Please try again.";
            }
        }

        $title = "Edit Customer";
        include '../views/customers/edit.php';
    }

    public function view($id) {
        $customer = $this->customerModel->getCustomerById($id);
        
        if (!$customer) {
            $_SESSION['error'] = "Customer not found";
            header('Location: /customers');
            exit();
        }

        $sales = $this->saleModel->getSalesByCustomer($id);
        $title = "Customer Details";
        include '../views/customers/view.php';
    }

    public function delete($id) {
        if ($this->customerModel->delete($id)) {
            $_SESSION['success'] = "Customer deleted successfully!";
        } else {
            $_SESSION['error'] = "Failed to delete customer. Please try again.";
        }
        header('Location: /customers');
        exit();
    }

    // API Methods
    public function apiIndex() {
        try {
            $customers = $this->customerModel->getAllCustomers();
            return ['success' => true, 'data' => $customers];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function apiCreate() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Validate input
            if (empty($data['customer_name']) || empty($data['contact_number'])) {
                throw new Exception('Customer name and contact number are required');
            }

            $customerId = $this->customerModel->create([
                'customer_name' => $data['customer_name'],
                'address' => $data['address'] ?? '',
                'contact_number' => $data['contact_number'],
                'email' => $data['email'] ?? ''
            ]);

            return [
                'success' => true,
                'data' => ['customer_id' => $customerId],
                'message' => 'Customer created successfully'
            ];
        } catch (Exception $e) {
            http_response_code(400);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}