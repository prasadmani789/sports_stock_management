<?php
require_once '../models/Item.php';

class ItemController {
    private $itemModel;

    public function __construct() {
        $this->itemModel = new Item();
    }

    public function index() {
        $items = $this->itemModel->getAllItems();
        require_once '../views/items/index.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate CSRF token
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die("Invalid CSRF token");
            }

            $data = [
                'item_name' => trim($_POST['item_name']),
                'item_type' => trim($_POST['item_type']),
                'item_brand' => trim($_POST['item_brand']),
                'central_gst' => filter_var($_POST['central_gst'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
                'state_gst' => filter_var($_POST['state_gst'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
                'mrp' => filter_var($_POST['mrp'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION)
            ];

            if ($this->itemModel->create($data)) {
                $_SESSION['success'] = "Item added successfully!";
                header('Location: /items');
                exit();
            } else {
                $_SESSION['error'] = "Failed to add item. Please try again.";
            }
        }
        require_once '../views/items/create.php';
    }

    public function edit($id) {
        $item = $this->itemModel->getItemById($id);

        if (!$item) {
            $_SESSION['error'] = "Item not found";
            header('Location: /items');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate CSRF token
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die("Invalid CSRF token");
            }

            $data = [
                'item_name' => trim($_POST['item_name']),
                'item_type' => trim($_POST['item_type']),
                'item_brand' => trim($_POST['item_brand']),
                'central_gst' => filter_var($_POST['central_gst'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
                'state_gst' => filter_var($_POST['state_gst'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
                'mrp' => filter_var($_POST['mrp'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION)
            ];

            if ($this->itemModel->update($id, $data)) {
                $_SESSION['success'] = "Item updated successfully!";
                header('Location: /items');
                exit();
            } else {
                $_SESSION['error'] = "Failed to update item. Please try again.";
            }
        }

        require_once '../views/items/edit.php';
    }

    public function delete($id) {
        if ($this->itemModel->delete($id)) {
            $_SESSION['success'] = "Item deleted successfully!";
        } else {
            $_SESSION['error'] = "Failed to delete item. Please try again.";
        }
        header('Location: /items');
        exit();
    }
}