<?php
require_once 'config/constants.php';
require_once 'config/Database.php';

// Simple router
$request = $_SERVER['REQUEST_URI'];
$base_path = str_replace('/index.php', '', $_SERVER['PHP_SELF']);
$request = str_replace($base_path, '', $request);

// Remove query string
$request = explode('?', $request)[0];

// API routes
if (strpos($request, '/api/') === 0) {
    header('Content-Type: application/json');
    
    $api_route = substr($request, 5);
    $method = $_SERVER['REQUEST_METHOD'];
    
    try {
        switch ($api_route) {
            case 'customers':
                require_once 'controllers/api/CustomerController.php';
                $controller = new CustomerController();
                
                if ($method === 'GET') {
                    echo json_encode($controller->index());
                } elseif ($method === 'POST') {
                    echo json_encode($controller->create());
                }
                break;
                
            case 'sales':
                require_once 'controllers/api/SaleController.php';
                $controller = new SaleController();
                
                if ($method === 'POST') {
                    echo json_encode($controller->create());
                }
                break;
                
            default:
                http_response_code(404);
                echo json_encode(['error' => 'Endpoint not found']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit();
}

// Web routes
switch ($request) {
    case '/':
        require 'views/dashboard.php';
        break;
        
    case '/items':
        require 'controllers/ItemController.php';
        (new ItemController())->index();
        break;
        
    case '/items/create':
        require 'controllers/ItemController.php';
        (new ItemController())->create();
        break;
        
    case (preg_match('/^\/items\/edit\/(\d+)$/', $request, $matches) ? true : false):
        require 'controllers/ItemController.php';
        (new ItemController())->edit($matches[1]);
        break;
        
    case (preg_match('/^\/items\/delete\/(\d+)$/', $request, $matches) ? true : false):
        require 'controllers/ItemController.php';
        (new ItemController())->delete($matches[1]);
        break;
        
    case '/sales/create':
        require 'controllers/SaleController.php';
        (new SaleController())->create();
        break;
        
    case (preg_match('/^\/sales\/invoice\/(\d+)$/', $request, $matches) ? true : false):
        require 'controllers/SaleController.php';
        (new SaleController())->invoice($matches[1]);
        break;
        
    case '/quotations/create':
        require 'controllers/QuotationController.php';
        (new QuotationController())->create();
        break;
        
    case '/stock':
        require 'controllers/StockController.php';
        (new StockController())->index();
        break;
        
    case '/stock/purchase':
        require 'controllers/StockController.php';
        (new StockController())->purchase();
        break;
        
    case '/customers':
        require 'controllers/CustomerController.php';
        (new CustomerController())->index();
        break;
        
    case '/customers/create':
        require 'controllers/CustomerController.php';
        (new CustomerController())->create();
        break;
        
    default:
        http_response_code(404);
        require 'views/errors/404.php';
        break;
}